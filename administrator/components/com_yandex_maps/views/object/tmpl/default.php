<?php
defined("_JEXEC") or die("Access deny");
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('formbehavior.chosen', 'select');
if (method_exists('JHtmlBehavior', 'tabstate')) {
	JHtml::_('behavior.tabstate');
}

JHtml::_('xdwork.yapi');

JHtml::stylesheet(JURI::root() . 'media/com_yandex_maps/js/rangeslider.css',[], true);
JHtml::script(JURI::root() . 'media/com_yandex_maps/js/rangeslider.min.js');
JHtml::script(JURI::root() . 'media/com_yandex_maps/js/object.js');
JHtml::stylesheet(JURI::root() . 'media/com_yandex_maps/js/colpick/colpick.css',[], true);
JHtml::script(JURI::root() . 'media/com_yandex_maps/js/colpick/colpick.js');
JHtml::stylesheet(JURI::root() . 'media/com_yandex_maps/js/chosenImage/chosenImage.css',[], true);
JHtml::script(JURI::root() . 'media/com_yandex_maps/js/chosenImage/chosenImage.js');

$params = JComponentHelper::getParams('com_yandex_maps');

$organization_mode = JFactory::getApplication()->input->get('mode') == 'organization';
?>
<form action="index.php?option=com_yandex_maps&task=objects.<?php echo JFactory::getApplication()->input->getCmd('task')?>&id=<?php echo (int)$this->item->id?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal" enctype="multipart/form-data">
	<?php if (count($this->item->error)) {?>
		<div class="alert alert-error"><?php echo implode('<br>', array_values($this->item->error))?></div>
	<?php }?>
	<div class="row-fluid">
			<div class="span5">
				<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'base'));?>
					<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'base', 'Объект'); ?> 
						<div class="control-group <?php echo (isset($this->item->error['title']) ? 'error' : '')?>">
							<label class="control-label" for="jform_title">Название:</label>
							<div class="controls">
								<input type="text" name="jform[title]" value="<?php echo htmlspecialchars($this->item->title)?>" id="jform_title" placeholder="введите название">
							</div>
						</div>
						<div class="control-group <?php echo (isset($this->item->error['alias']) ? 'error' : '')?>">
							<label class="control-label" for="jform_alias" >Псевдоним:</label>
							<div class="controls">
								<input type="text" name="jform[alias]" value="<?php echo htmlspecialchars($this->item->alias)?>" id="jform_alias" placeholder="введите псевдоним">
							</div>
						</div>
						<div class="control-group <?php echo (isset($this->item->error['description']) ? 'error' : '')?>">
							<label class="control-label" for="jform_description" >Описание</label>
							<div class="controls">
								<?php echo JHTML::_('EditorCustom.write','jform[description]','jform_description',$this->item->description);?>
							</div>
						</div>
						<div class="control-group <?php echo (isset($this->item->error['category_id']) ? 'error' : '')?>">
							<label class="control-label" for="jform_category_id" >Категория:</label>
							<div class="controls">
								<select name="jform[categoryids][]" id="jform_category_id" multiple>
								<?php 
									$categories = CModel::getInstance( 'Categories', 'Yandex_MapsModel')->models();
									foreach($categories as $item) {?>
									<option  
										data-lat="<?php echo $item->lat?>" 
										data-lan="<?php echo $item->lan?>" 
										data-zoom="<?php echo $item->zoom?>" 
										data-type="<?php echo $item->type?>" 
										<?php echo (in_array($item->id, $this->item->categoryids) ? 'selected' : '')?>  value="<?php echo $item->id?>"
									><?php echo $item->title?></option>
								<?php }?>
								</select>
							</div>
						</div>
						<div class="control-group <?php echo (isset($this->item->error['active']) ? 'error' : '')?>">
							<label class="control-label" for="jform_active" >Включена:</label>
							<div class="controls">
								<fieldset id="jform_params_use_autosave" class="radio btn-group" >
									<input type="radio" id="jform_active0" name="jform[active]" value="0" <?php echo (!$this->item->active ? 'checked="checked"' : '')?> />
									<label for="jform_active0" >Нет</label>
									<input type="radio" id="jform_active1" name="jform[active]" value="1" <?php echo ($this->item->active ?  'checked="checked"' : '')?>/>
									<label for="jform_active1" >Да</label>
								</fieldset>
							</div>
						</div>
					<?php echo JHtml::_('bootstrap.endTab');?> 
					<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'view', 'Вид объекта'); ?> 
						<div class="control-group <?php echo (isset($this->item->error['type']) ? 'error' : '')?>">
							<label class="control-label" for="jform_type" ><?php echo JText::_('Тип объекта');?></label>
							<div class="controls">
								<select name="jform[type]" id="jform_type">
									<?php foreach($this->item->types as $key=>$name){?>
										<option <?php echo ($this->item->type==$key ? 'selected' : '')?> value="<?php echo $key?>"><?php echo JText::_($name)?></option>
									<?php }?>
								</select>
							</div>
						</div>
						<div id="option_and_properties">
							<?php if ($this->item->settings->get('use_title_how_icon_content', 1)!=1):?>
							<div class="control-group placemark">
								<label class="control-label" for="iconContent">Текст метки:</label>
								<div class="controls">
									<textarea class="span12 properties" id="iconContent"><?php echo $this->item->settings->get('object_iconContent', '');?></textarea>
								</div>
							</div>
							<?php endif; ?>
							<?php JHtml::_('btngroup._', 'visible','Видимый', 1,'options','options', 'visible', 'options'); ?>
							
							<?php if ($this->item->settings->get('use_description_how_balloon_content', 1)!=1):?>
							<div class="control-group placemark polygon polyline circle">
								<label class="control-label" for="balloonContent">Содержание <br>высплывающего<br>окна:</label>
								<div class="controls">
									<?php echo JHTML::_('EditorCustom.write','balloonContent','balloonContent','');?>
								</div>
							</div>
							<?php endif; ?>
							
							<div class="control-group placemark">
								<label class="control-label" for="strokeOpacity">Стиль метки:</label>
								<div class="controls">
									<select id="preset" class="options">
									<?php
										$images = include JPATH_COMPONENT_ADMINISTRATOR.'/helpers/images.php';
										foreach ($images as $name=>$image) {?>
											<option <?php echo ($this->item->settings->get('object_preset', 'islands#icon') == $name ? 'selected' : '');?> data-img-src="<?php echo JURI::root().'media/com_yandex_maps/images/placemark/'.$image?>"><?php echo $name?></option> 
										<?php }?>
									</select>
								</div>
							</div>
							<div class="control-group placemark">
								<label class="control-label" for="iconColor">Цвет иконки:</label>
								<div class="controls">
									<input type="text" value="<?php echo $this->item->settings->get('object_iconColor', '')?>" class="span3 options color" id="iconColor"/>
								</div>
							</div>
							<div class="control-group placemark">
								<label class="control-label" for="strokeOpacity">Собственная иконка:</label>
								<div class="controls">
									<?php JHTML::_('media._','iconImageHref','iconImageHref','images','','options', $this->item->settings->get('object_iconImageHref', ''));?>
								</div>
							</div>
							<div class="control-group placemark">
								<label class="control-label" for="strokeOpacity">Размер иконки:</label>
								<div class="controls">
									<?php JHTML::_('xy._', 'iconImageSize', 'iconImageSize', array('Ширина','Высота'),'images','options', $this->item->settings->get('object_iconImageSize', ''));?>
								</div>
							</div>
							<div class="control-group placemark">
								<label class="control-label" for="strokeOpacity">Смещение иконки:</label>
								<div class="controls">
									<?php JHTML::_('xy._', 'iconImageOffset', 'iconImageOffset', array('По X','По Y'),'images','options', $this->item->settings->get('object_iconImageOffset', ''));?>
								</div>
							</div>
							<div class="control-group polygon polyline circle">
								<label class="control-label" for="strokeOpacity">Прозрачность линий:</label>
								<div class="controls">
									<input type="range" min="0" max="10" value="<?php echo $this->item->settings->get('object_strokeOpacity', '10')?>" class="span12 options range" id="strokeOpacity"/>
								</div>
							</div>
							<div class="control-group polygon polyline circle">
								<label class="control-label" for="strokeWidth">Толщина линий:</label>
								<div class="controls">
									<input type="number" min="1"  value="<?php echo $this->item->settings->get('object_strokeWidth', '1')?>" class="span4 options" id="strokeWidth"/>
								</div>
							</div>
							<div class="control-group polygon polyline circle">
								<label class="control-label" for="strokeColor">Цвет линий:</label>
								<div class="controls">
									<input type="text" value="<?php echo $this->item->settings->get('object_strokeColor', '')?>" class="span3 options color" id="strokeColor"/>
								</div>
							</div>
							<div class="control-group polygon circle">
								<label class="control-label" for="fillColor">Цвет заливки:</label>
								<div class="controls">
									<input type="text" value="<?php echo $this->item->settings->get('object_fillColor', '')?>" class="span3 options color" id="fillColor"/>
								</div>
							</div>
							<div class="control-group polygon circle">
								<label class="control-label" for="fillOpacity">Прозрачность заливки:</label>
								<div class="controls">
									<input type="range" min="0" max="10" step="1" value="<?php echo $this->item->settings->get('object_fillOpacity', '10')?>" value="3" class="span12 options range" id="fillOpacity"/>
								</div>
							</div>
						</div>
					<?php echo JHtml::_('bootstrap.endTab');?>

					<?php if ($this->item->organization_object_id or $organization_mode) {
						echo JHtml::_('bootstrap.addTab', 'myTab', 'organization', 'Организация');
						include 'organization.php';
						echo JHtml::_('bootstrap.endTab');
					} ?>

					<?php
						JFactory::getLanguage()->load('com_content', JPATH_ADMINISTRATOR, 'ru-RU', true);
					?>

					<?php
						echo JHtml::_('bootstrap.addTab', 'myTab', 'metadata', 'Публикация');
						echo JLayoutHelper::render('joomla.edit.publishingdata', $this);

						$fieldSets = $this->form->getFieldsets('metadata');

						foreach ($fieldSets as $name => $fieldSet) {
							foreach ($this->form->getFieldset($name) as $field) {
								echo $field->renderField();
							}
						};

						$fieldSets = $this->form->getFieldsets('params');

						foreach ($fieldSets as $name => $fieldSet) {
							foreach ($this->form->getFieldset($name) as $field) {
								echo $field->renderField();
							}
						};

						echo JHtml::_('bootstrap.endTab');
					?>
				<?php echo JHtml::_('bootstrap.endTabSet');?>
			</div>
			<div class="span7">
				<div id="map" style="height: 650px;" data-base="<?php echo JURI::root()?>" data-lang="<?php echo $this->lang;?>"></div>
				<p  class="muted">*для создания объекта, дважды кликните по любому месту карты</p>
				<div id="status_stroke"></div>
			</div>
	</div>
	<input type="hidden" id="object_show_title_with_image" value="<?php echo (int)$params->get('object_show_title_with_image', 0)?>">
	<input type="hidden" name="jform[lat]" id="jform_lat" value="<?php echo htmlspecialchars(''.$this->item->lat)?>">
	<input type="hidden" name="jform[lan]" id="jform_lan" value="<?php echo htmlspecialchars(''.$this->item->lan)?>">
	<input type="hidden" name="jform[zoom]" id="jform_zoom" value="<?php echo htmlspecialchars(''.$this->item->zoom)?>">
	<input type="hidden" name="jform[coordinates]" id="jform_coordinates" value="<?php echo htmlspecialchars(''.$this->item->coordinates)?>">
	<input type="hidden" name="jform[options]" id="jform_options" value="<?php echo htmlspecialchars(''.$this->item->options)?>">
	<input type="hidden" name="jform[properties]" id="jform_properties" value="<?php echo htmlspecialchars(''.$this->item->properties)?>">

	<input type="hidden" name="save" value="">
	<input type="hidden" name="option" value="com_yandex_maps">
	<input type="hidden" name="task" value="">
	<input type="hidden" name="jform[id]" value="<?php echo (int)$this->item->id?>">
	<?php echo JHtml::_('form.token'); ?>
	<input type="hidden" name="id" value="<?php echo (int)$this->item->id?>">
</form>