<?php
defined("_JEXEC") or die("Access deny");
JHtml::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/html');
?>
<form action="index.php?option=com_yandex_maps" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">
	<?php 
	if (!class_exists('ZipArchive')) { ?>
	<div class="alert alert-block"><h4>Внимание!</h4><a href="http://php.net/manual/ru/class.ziparchive.php">ZipArchive</a> не установлен. Поэтому экспорт будет сделан в текстовый файл. Это может занять больше ресурсов.</div>
	<?php } 
	JHtml::_('btngroup._', 'maps','Карты', true ,'export','control');
	JHtml::_('btngroup._', 'categories','Категории', true ,'export','control');
	JHtml::_('btngroup._', 'category_to_map','Привязка категорий к картам', true ,'export','control');
	JHtml::_('btngroup._', 'objects','Объекты', true ,'export','control');
	JHtml::_('btngroup._', 'object_to_category','Привязка объектов к категориям', true ,'export','control');
	JHtml::_('btngroup._', 'organizations','Данные организаций', false ,'export','control');
	JHtml::_('btngroup._', 'settings','Настройки компонента', false ,'export','control');
	?>
	<script>
		jQuery('.control-group.control input').on('change', function() {
			var cnt = 0;
			jQuery('.control-group.control input').each(function () {
				(this.value==1) && cnt++;
			});
			document.adminForm.boxchecked.value = cnt;
		});
	</script>
	<input type="hidden" name="option" value="com_yandex_maps">
	<input type="hidden" name="task" value="">
	<input type="hidden" name="boxchecked" value="3">
</form>
