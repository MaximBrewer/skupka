<?php
defined("_JEXEC") or die("Access deny");
JHtml::_('formbehavior.chosen', 'select');
JHtml::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/html');
?>
<form action="index.php?option=com_yandex_maps&task=categories"  enctype="multipart/form-data"  method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">
	<?php if (!class_exists('ZipArchive')) { ?>
	<div class="alert alert-block"><h4>Внимание!</h4><a href="http://php.net/manual/ru/class.ziparchive.php">ZipArchive</a> не установлен. Поэтому импорт может быть сделан только из текстового файла.</div>
	<?php } ?>
	<legend>Файл с данными</legend>
	<div class="control-group">
		<label class="control-label" for="jform_import_file">Файл с данными</label>
		<div class="controls">
			<input type="file" id="jform_import_file" name="jform[import][file]">
		</div>
	</div>
	<legend>Импортировать только</legend>
	<?php
		JHtml::_('btngroup._', 'maps','Карты', true ,'import','control');
		JHtml::_('btngroup._', 'categories','Категории', true ,'import','control');
		JHtml::_('btngroup._', 'category_to_map','Привязка категорий к картам', true ,'import','control');
		JHtml::_('btngroup._', 'objects','Объекты', true ,'import','control');
		JHtml::_('btngroup._', 'object_to_category','Привязка объектов к категориям', true ,'import','control');
		JHtml::_('btngroup._', 'organizations','Данные организаций', false ,'import','control');
		JHtml::_('btngroup._', 'settings','Настройки компонента', false ,'import','control');
		?>
	<legend>Режим вставки</legend>
	<div class="control-group">
		<label class="control-label" for="jform_import_mode">Режим вставки</label>
		<div class="controls">
			<select id="jform_import_mode" name="jform[import][mode]">
				<option value="-1">Отчистить перед вставкой</option>
				<option value="0">Заменять id на null</option>
				<option value="1">Вставлять как есть</option>
				<option selected value="2">Вставлять только не существующие</option>
			</select>
		</div>
	</div>
	<input type="hidden" name="option" value="com_yandex_maps">
	<input type="hidden" name="task" value="">
	<input type="hidden" name="boxchecked" value="3">
	<input type="hidden" name="jform[import][save]" value="3">
</form>
