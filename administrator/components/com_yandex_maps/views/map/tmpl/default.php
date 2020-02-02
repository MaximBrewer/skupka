<?php
defined("_JEXEC") or die("Access deny");
JHtml::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/html');
JHtml::_('formbehavior.chosen', 'select');
if (method_exists('JHtmlBehavior', 'tabstate')) {
	JHtml::_('behavior.tabstate');
}
JHtml::stylesheet(JURI::root() . 'media/com_yandex_maps/js/rangeslider.css', array(), true);
JHtml::script(JURI::root() . 'media/com_yandex_maps/js/rangeslider.min.js');
JHtml::stylesheet(JURI::root() . 'media/com_yandex_maps/js/colpick/colpick.css', array(), true);
JHtml::script(JURI::root() . 'media/com_yandex_maps/js/colpick/colpick.js');
JHtml::stylesheet(JURI::root() . 'media/com_yandex_maps/js/chosenImage/chosenImage.css', array(), true);
JHtml::script(JURI::root() . 'media/com_yandex_maps/js/chosenImage/chosenImage.js');
JHtml::script(JURI::root() . 'media/com_yandex_maps/js/maps.js');
?>
<form action="index.php?option=com_yandex_maps&task=maps" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">
	<?php if (count($this->item->error)) {?>
		<div class="alert alert-error"><?php echo implode('<br>', array_values($this->item->error))?></div>
	<?php } ?>
	<div class="row-fluid">
			<div class="span5">
				<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'base'));?>
					<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'base', 'Карта'); ?> 
						<div class="control-group <?php echo (isset($this->item->error['title']) ? 'error' : '')?>">
							<label class="control-label" for="jform_title">Название</label>
							<div class="controls">
								<input type="text" name="jform[title]" value="<?php echo htmlspecialchars($this->item->title)?>" id="jform_title" placeholder="введите название карты">
							</div>
						</div>
						<div class="control-group <?php echo (isset($this->item->error['alias']) ? 'error' : '')?>">
							<label class="control-label" for="jform_alias" >Псевдоним</label>
							<div class="controls">
								<input type="text" name="jform[alias]" value="<?php echo htmlspecialchars($this->item->alias)?>" id="jform_alias" placeholder="введите псевдоним карты">
							</div>
						</div>
						<div class="control-group <?php echo (isset($this->item->error['active']) ? 'error' : '')?>">
							<label class="control-label" for="jform_active" >Карта включена</label>
							<div class="controls">
								<fieldset id="jform_params_use_autosave" class="radio btn-group" >
								<input type="radio" id="jform_active0" name="jform[active]" value="0" <?php echo (!$this->item->active ? 'checked="checked"' : '')?> />
								<label for="jform_active0" >Нет</label>
								<input type="radio" id="jform_active1" name="jform[active]" value="1" <?php echo ($this->item->active ?  'checked="checked"' : '')?>/>
								<label for="jform_active1" >Да</label>
							</fieldset>
						</div>
						</div>
						<div class="control-group <?php echo (isset($this->item->error['description']) ? 'error' : '')?>">
							<label class="control-label" for="jform_description" >Описание</label>
							<div class="controls">
								<?php echo JHTML::_('EditorCustom.write','jform[description]','jform_description',$this->item->description);?>
							</div>
						</div>
					<?php echo JHtml::_('bootstrap.endTab');?> 
					<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'behaviors', 'Поведения'); ?> 
						<?php include 'behaviors.php';?>
					<?php echo JHtml::_('bootstrap.endTab');?>
					<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'controls', 'Элементы управления'); ?> 
						<?php include 'controls.php';?>
					<?php echo JHtml::_('bootstrap.endTab');?>
					<?php echo JLayoutHelper::render('joomla.edit.params', $this); ?>
					<?php 
						//print_r($this->item->params);
					
					/* 
						echo JHtml::_('bootstrap.addTab', 'myTab', 'metadata', 'Публикация');
						echo JLayoutHelper::render('joomla.edit.publishingdata', $this);
						$fieldSets = $this->form->getFieldsets('metadata');
						foreach ($fieldSets as $name => $fieldSet) {
							foreach ($this->form->getFieldset($name) as $field) {
								echo $field->renderField();
							}
						};
						echo JHtml::_('bootstrap.endTab'); */
					?>
					<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'map_options', 'Настройки карты'); ?> 
						<div class="control-group <?php echo (isset($this->item->error['lat']) ? 'error' : '')?>">
							<label class="control-label" for="jform_lat" >Широта</label>
							<div class="controls">
								<input type="text" class="input-small" name="jform[lat]" value="<?php echo htmlspecialchars($this->item->lat)?>" id="jform_lat" placeholder="type latitude">
							</div>
						</div>
						<div class="control-group <?php echo (isset($this->item->error['lan']) ? 'error' : '')?>">
							<label class="control-label" for="jform_lan" >Долгота</label>
							<div class="controls">
								<input type="text" class="input-small" name="jform[lan]" value="<?php echo htmlspecialchars($this->item->lan)?>" id="jform_lan" placeholder="type longitude">
							</div>
						</div>
						<div class="control-group <?php echo (isset($this->item->error['width']) ? 'error' : '')?>">
							<label class="control-label" for="jform_width" >Ширина карты</label>
							<div class="controls">
								<input type="text" class="input-small" name="jform[width]" value="<?php echo htmlspecialchars($this->item->width)?>" id="jform_width" placeholder="type latitude">
							</div>
						</div>
						<div class="control-group <?php echo (isset($this->item->error['height']) ? 'error' : '')?>">
							<label class="control-label" for="jform_height" >Высота карты</label>
							<div class="controls">
								<input type="text" class="input-small" name="jform[height]" value="<?php echo htmlspecialchars($this->item->height)?>" id="jform_height" placeholder="type latitude">
							</div>
						</div>
						<div class="control-group <?php echo (isset($this->item->error['zoom']) ? 'error' : '')?>" >
							<label class="control-label" for="jform_zoom" >Масштаб карты</label>
							<div class="controls">
								<input type="number" class="input-small" name="jform[zoom]" value="<?php echo intval($this->item->zoom)?>" id="jform_zoom">
							</div>
						</div>
						<div class="control-group <?php echo (isset($this->item->error['minZoom']) ? 'error' : '')?>" >
							<label class="control-label" for="jform_minzoom" >Минимальный масштаб<br>карты</label>
							<div class="controls">
								<input type="number" class="input-small" name="jform[minZoom]" value="<?php echo intval($this->item->minZoom)?>" id="jform_minzoom">
							</div>
						</div>
						<div class="control-group <?php echo (isset($this->item->error['maxZoom']) ? 'error' : '')?>" >
							<label class="control-label" for="jform_maxzoom" >Максимальный масштаб<br>карты</label>
							<div class="controls">
								<input type="number" class="input-small" name="jform[maxZoom]" value="<?php echo intval($this->item->maxZoom)?>" id="jform_maxzoom">
							</div>
						</div>
						<div class="control-group <?php echo (isset($this->item->error['type']) ? 'error' : '')?>">
							<label class="control-label" for="jform_type" ><?php echo JText::_('Тип карты',true);?></label>
							<div class="controls">
								<select name="jform[type]" id="jform_type">
									<?php foreach($this->item->map_types as $key=>$name){?>
										<option <?php echo ($this->item->type==$key ? 'selected' : '')?> value="<?php echo $key?>"><?php echo JText::_($name)?></option>
									<?php } ?>
								</select>
							</div>
						</div>
					<?php echo JHtml::_('bootstrap.endTab');?> 
				<?php echo JHtml::_('bootstrap.endTabSet');?>
			</div>
			<div class="span7">
				<div id="xdsoft_toolbar">
					<?php 
						jhtml::_('xdwork.autocomplete');
					?>
					<table class="xdsoft_toolbar">
						<tr>
							<td id="xdsoft_button_s">
								<span id="button_placemark" class="xdsoft_button_add xdsoft_button_placemark"><span></span></span>
								<span id="button_polyline" class="xdsoft_button_add xdsoft_button_polyline"><span></span></span>
								<span id="button_polygon" class="xdsoft_button_add xdsoft_button_polygon"><span></span></span>
								<span id="button_circle" class="xdsoft_button_add xdsoft_button_circle"><span></span></span>
							</td>
							<td><input class="xdsoft_search_input xdsoft_map_search" type="text" placeholder="Найти место на карте"></td>
							<td><span class="xdsoft_button_add xdsoft_button_find"><span>Найти</span></span></td>
						</tr>
					</table>
					<div></div>
				</div>
				<?php 
                 $params_ = JComponentHelper::getParams('com_yandex_maps');
                 $api_key=$params_->get('api_key','');
				?>
				<script>
					window.api_key='<?php echo $api_key;?>';
					window.defaultSettings = {
						defaultCategoryId: '<?php echo !$this->item->getCategoriesCount() ? 0 : $this->item->categories[0]->id?>',
						iconLayout: '<?php echo $this->item->settings->get('object_show_title_with_image', 0) ? 'default#imageWithContent' : 'default#image'?>',
						iconColor: '<?php echo $this->item->settings->get('object_iconColor', '')?>',
						preset: '<?php echo $this->item->settings->get('object_preset', 'islands#blueIcon')?>',
						strokeWidth:  <?php echo $this->item->settings->get('object_strokeWidth', 3)?>,
						strokeColor: '<?php echo $this->item->settings->get('object_strokeColor', '#FE110F')?>',
						strokeOpacity: <?php echo $this->item->settings->get('object_strokeOpacity', 10)/10?>,
						fillOpacity: <?php echo $this->item->settings->get('object_fillOpacity', 8)/10?>,
						fillColor: '<?php echo $this->item->settings->get('object_iconColor', '#FC6057')?>',
						iconImageSize: [<?php echo $this->item->settings->get('object_iconImageSize', '32,32')?>]
					};
					window.ymOptions = <?php echo jhtml::_('map.getOptions', $this->item ?: new Yandex_MapsModelMaps(), clone(JComponentHelper::getParams('com_yandex_maps')));?>
                   
				</script>
				<div id="map" style="height: 650px;" data-base="<?php echo JURI::root()?>" data-lang="<?php echo $this->lang;?>"></div>
				<div id="objectStore"></div>
			</div>
	</div>
	<div id="dialogObjectCreate" style="display:none">
		<div id="xdsoft_object_form_parent">
			<table id="xdsoft_object_form">
				<tr>
					<td colspan="2"><input id="iconContent" placeholder="Название" type="text"></td>
					<td colspan="2">
						<select id="category_id">
							<?php foreach($this->item->categories as $category) { ?>
								<option value="<?php echo $category->id?>"><?php echo $category->title?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="4"><textarea name="" id="balloonContent" placeholder="Текст будет виден при нажатии на объект"></textarea></td>
				</tr>
				<tr>
					<td>
						<div><?php include "iconpicker.php"?></div>
						<div class="xdsoft_colors">
							<input title="Линия" id="strokeColor" type="text" value="FE110F" class="color"/>
						</div>
						<div class="xdsoft_colors">
							<input title="Заливка" id="fillColor" type="text" value="FE110F" class="color"/>
						</div>
					</td>
					<td colspan="3">
						<div class="xdsoft_ranges">
							<label for="strokeWidth">Толщина линии</label>
							<input id="strokeWidth" min="1" max="10" type="range"/>
						</div>
						<div class="xdsoft_ranges">
							<label for="strokeOpacity">Прозрачность линии</label>
							<input id="strokeOpacity" min="1" max="10" type="range"/>
						</div>
						<div class="xdsoft_ranges">
							<label for="fillOpacity">Прозрачность заливки</label>
							<input id="fillOpacity" min="1" max="10" type="range"/>
						</div>
					</td>
				</tr>
                <tr>
                    <td colspan="4">
                        <div>
                            <label for="coordinates"><a href="javascript:void(0)" onclick="jQuery('#coordinates').toggle();">Координаты</a></label>
                            <textarea style="display: none;" name="coordinates" id="coordinates" cols="30" rows="1">1</textarea>
                        </div>
                    </td>
                </tr>
				<tr>
					<td><a id="deleteObject" href="javascript:void(0)" class="btn-link">Удалить</a></td>
					<td></td>
					<td></td>
					<td><button id="updateObject" class="btn btn-ok">ОК</button></td>
				</tr>
			</table>
		</div>
	</div>
	<input type="hidden" id="loadobjects" name="loadobjects" value="<?php echo (int)$this->loadobjects?>">
	<input type="hidden" name="save" value="">
	<input type="hidden" name="option" value="com_yandex_maps">
	<input type="hidden" name="task" value="">
	<input type="hidden" name="jform[id]" value="<?php echo (int)$this->item->id;?>">
	<?php echo JHtml::_('form.token'); ?>
	<input type="hidden" name="id" value="<?php echo (int)$this->item->id;?>">
</form>