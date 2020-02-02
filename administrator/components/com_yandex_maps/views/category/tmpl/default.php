<?php
defined("_JEXEC") or die("Access deny");
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('formbehavior.chosen', 'select');
$params = JComponentHelper::getParams('com_yandex_maps');
$api_key=$params->get('api_key','');
JHtml::script(JURI::getInstance()->getScheme().'://api-maps.yandex.ru/2.1/?lang='.$this->lang.'&apikey='.$api_key);
JHtml::script(JURI::root() . 'media/com_yandex_maps/js/categories.js');
if (method_exists('JHtmlBehavior', 'tabstate')) {
	JHtml::_('behavior.tabstate');
}
?>
<form action="index.php?option=com_yandex_maps&task=categories" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">
	<?php if (count($this->item->error)) {?>
		<div class="alert alert-error"><?php echo implode('<br>', array_values($this->item->error))?></div>
	<?php }?>
	<div class="row-fluid">
			<div class="span5">
				<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'base'));?>
					<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'base', 'Категория'); ?> 
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
						<div class="control-group <?php echo (isset($this->item->error['map_id']) ? 'error' : '')?>">
							<label class="control-label" for="jform_map_id" >Карта:</label>
							<div class="controls">
								<select multiple name="jform[mapids][]" id="jform_map_id">
									<?php 
									$items = $this->maps->models();
									foreach ($items as $item) {?>
										<option
											data-lat="<?php echo $item->lat?>" 
											data-lan="<?php echo $item->lan?>" 
											data-zoom="<?php echo $item->zoom?>" 
											data-type="<?php echo $item->type?>" 
											<?php echo (in_array($item->id, $this->item->mapids) ? 'selected' : '')?> value="<?php echo $item->id?>"
										><?php echo $item->title?></option>
									<?php }?>
								</select>
							</div>
						</div>
                        <div class="control-group <?php echo (isset($this->item->error['category_id']) ? 'error' : '')?>">
							<label class="control-label" for="jform_category_id" >Родитель:</label>
							<div class="controls">
								<select name="jform[category_id]" id="jform_category_id">
									<option <?php echo (!$this->item->category_id ? 'selected' : '')?> value="0">--нет родителя--</option>
                                    <?php
                                    $options = array();
                                    $category_id = $this->item->category_id;
                                    $id = $this->item->id;
									CModel::getInstance( 'Categories', 'Yandex_MapsModel')->tree(function ($item, $deep) use (&$options, $category_id, $id){
                                        if ($item->id == $id) {
                                            return false;
                                        }
                                        $options[] = '<option 
                                            data-lat="'.$item->lat.'"
                                            data-lan="'.$item->lan.'"
                                            data-zoom="'.$item->zoom.'"
                                            '.($item->id == $category_id ? 'selected' : '').'
                                            value="'.$item->id.'"
										>'.
                                            str_repeat('-', $deep).$item->title.
                                        '</option>';
                                    });
                                    echo implode("\n", $options);
									?>
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
						<div class="control-group <?php echo (isset($this->item->error['description']) ? 'error' : '')?>">
							<label class="control-label" for="jform_description" >Описание</label>
							<div class="controls">
								<?php  echo JHTML::_('EditorCustom.write','jform[description]','jform_description',$this->item->description);?>
							</div>
						</div>
					<?php echo JHtml::_('bootstrap.endTab');?>
					<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'geo', 'Настройки локации'); ?> 
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
						<div class="control-group <?php echo (isset($this->item->error['zoom']) ? 'error' : '')?>" >
							<label class="control-label" for="jform_zoom" >Высота</label>
							<div class="controls">
								<input type="number" class="input-small" name="jform[zoom]" value="<?php echo htmlspecialchars($this->item->zoom)?>" id="jform_zoom" placeholder="type longitude">
							</div>
						</div>
					<?php echo JHtml::_('bootstrap.endTab');?>
					<?php
						echo JHtml::_('bootstrap.addTab', 'myTab', 'metadata', 'Публикация');

						$fieldSets = $this->form->getFieldsets('metadata');
						foreach ($fieldSets as $name => $fieldSet) {
							foreach ($this->form->getFieldset($name) as $field) {
								echo $field->renderField();
							}
						};

						echo JHtml::_('bootstrap.endTab');
					?>
					<?php
						echo JHtml::_('bootstrap.addTab', 'myTab', 'objects', 'Вид объектов');
						$fieldSets = $this->form->getFieldsets('params');

						foreach ($fieldSets as $name => $fieldSet) {
							foreach ($this->form->getFieldset($name) as $field) {
								echo $field->renderField();
							}
						};

						echo JHtml::_('bootstrap.endTab');
					?>
				<?php echo JHtml::_('bootstrap.endTabSet');?>
				<!--
				<div class="control-group <?php echo (isset($this->item->error['howtocenter']) ? 'error' : '')?>">
					<label class="control-label" for="jform_howtocenter" >Как центровать:</label>
					<div class="controls">
						<select name="jform[howtocenter]" id="jform_howtocenter">
							<option <?php echo ($this->item->howtocenter==0 ? 'selected' : '')?> value="0">Не центровать</option>
							<option <?php echo ($this->item->howtocenter==1 ? 'selected' : '')?> value="1">Просчитать область для всех меток</option>
							<option <?php echo ($this->item->howtocenter==2 ? 'selected' : '')?> value="2">От привязанной карты</option>
							<option <?php echo ($this->item->howtocenter==3 ? 'selected' : '')?> value="3">Из заданных (Широта,Долгота,Высота)</option>
						</select>
					</div>
				</div>
				-->
			</div>
			<div class="span7">
				<div id="map" style="height: 650px;"></div>
				<div id="status_stroke"></div>
			</div>
	</div>
	<input type="hidden" name="save" value="">
	<input type="hidden" name="option" value="com_yandex_maps">
	<input type="hidden" name="task" value="">
	<input type="hidden" name="jform[id]" value="<?php echo (int)$this->item->id?>">
	<?php echo JHtml::_('form.token'); ?>
	<input type="hidden" name="id" value="<?php echo (int)$this->item->id?>">
</form>