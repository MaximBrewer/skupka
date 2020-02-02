<?php
defined("_JEXEC") or die("Access deny");
?>
<div class="com_yandex_maps">
	<a class="section" href="index.php?option=com_yandex_maps&task=maps">
		<img src="<?php echo JURI::root()?>/media/com_yandex_maps/images/com_yandex_maps-maps-icon-128x128.png"/>
		<span>Карты (<?php echo JModelLegacy::getInstance('Maps','Yandex_MapsModel')->count()?>)</span>
	</a>
	<a <?php echo JModelLegacy::getInstance('Maps','Yandex_MapsModel')->count() ? 'class="section"  href="index.php?option=com_yandex_maps&task=categories" ' : 'class="section disabled"'?>>
		<img src="<?php echo JURI::root()?>/media/com_yandex_maps/images/com_yandex_maps-categories-icon-128x128.png"/>
		<span>Категории (<?php echo JModelLegacy::getInstance('Categories','Yandex_MapsModel')->count()?>)</span>
	</a>
	<a <?php echo JModelLegacy::getInstance('Categories','Yandex_MapsModel')->count() ? 'class="section" href="index.php?option=com_yandex_maps&task=objects" ' : 'class="section disabled"'?>>
		<img src="<?php echo JURI::root()?>/media/com_yandex_maps/images/com_yandex_maps-objects-icon-128x128.png"/>
		<span>Объекты (<?php echo JModelLegacy::getInstance('Objects','Yandex_MapsModel')->count()?>)</span>
	</a>
	<a class="section" href="index.php?option=com_modules&filter_module=mod_yandex_maps">
		<img src="<?php echo JURI::root()?>/media/com_yandex_maps/images/com_yandex_maps-modules-icon-128x128.png"/>
		<span>Модули Яндекс карты</span>
	</a>
	<a class="section" href="index.php?option=com_plugins&view=plugins&filter_folder=system&filter_order=extension_id&filter_order_Dir=desc&filter_search=Яндекс">
		<img src="<?php echo JURI::root()?>/media/com_yandex_maps/images/com_yandex_maps-plugins-icon-128x128.png"/>
		<span>Плагин для вывода Яндекс карты</span>
	</a>
	<a class="section" href="index.php?option=com_config&view=component&component=com_yandex_maps">
		<img src="<?php echo JURI::root()?>/media/com_yandex_maps/images/com_yandex_maps-settings-icon-128x128.png"/>
		<span>Настройки</span>
	</a>
	<a <?php echo JModelLegacy::getInstance('Maps','Yandex_MapsModel')->count() ? 'class="section"  href="index.php?option=com_yandex_maps&task=export" ' : 'class="section disabled"'?>>
		<img src="<?php echo JURI::root()?>/media/com_yandex_maps/images/com_yandex_maps-export-icon-128x128.png"/>
		<span>Экспорт</span>
	</a>
	<a class="section"  href="index.php?option=com_yandex_maps&task=import">
		<img src="<?php echo JURI::root()?>/media/com_yandex_maps/images/com_yandex_maps-import-icon-128x128.png"/>
		<span>Импорт</span>
	</a>
	<div class="section" href="javascript:void()">
		<div class="row-fluid">
			<div class="span4">Автор:</div><div class="span4"><strong><a href="https://xdan.ru">xdan.ru</a></strong></div>
		</div>
		<div class="row-fluid">
			<div class="span4">Версия:</div><div class="span4"><strong><?php echo simplexml_load_string(file_get_contents(JPATH_COMPONENT_ADMINISTRATOR.'/manifest.xml'))->version?> MEDIUM</strong></div>
		</div>
		<div class="row-fluid">
			<div class="span4">Связь:</div><div class="span4"><a href="mailto:skoder@ya.ru">skoder@ya.ru</a></div>
		</div>
		<div class="row-fluid">
			<div class="span4">Док.</div><div class="span4"><a href="https://xdan.ru/dokumentatsiya-komponenta-yandeks-karty-dlya-joomla.html">Документация</a></div>
		</div>
		<div class="row-fluid">
			<div class="span4">Список </div><div class="span4"><a href="https://xdan.ru/komponent-yandeks-karty-dlya-joomla.html#changelog">изменений</a></div>
		</div>
		<div class="row-fluid">
			<div class="span4"><a href="index.php?option=com_installer&view=update">Посл. версия</a></div><div class="span5"><iframe style="border:0px;height:40px;" src="https://xdan.ru/update/yandex-maps.xml?last_version"></iframe></div>
		</div>
	</div>
    <a class="section" target="_blank" href="https://xdan.ru/docs/joomla/yandex-maps/">
		<img src="<?php echo JURI::root()?>/media/com_yandex_maps/images/com_yandex_maps-docs-icon-128x128.png"/>
		<span>Документация</span>
	</a>
</div>
<div class="xdelms">
	<div class="xdelm changelog">
		<div class="xdelm_wrapper">
			<h2>Список изменений</h2>
			<?php include "changelog.php";?>
		</div>
	</div>
	<!--FORLITE-->
</div>
<style>
.xdelms{
font-size:0;
}
.xdelms * {
-webkit-box-sizing: border-box;
-moz-box-sizing: border-box;
box-sizing: border-box;
}
.xdelm{
width: 50%;
margin-top: 50px;
padding-right: 10px;
vertical-align:top;
display:inline-block;
font-size:initial;
}
.xdelm:nth-child(2n){
padding-right: 0;
padding-left: 10px;	
}
.xdelm_wrapper{
border: 1px solid #ccc;
padding:10px 20px;
}
.xdelm_wrapper:after{
clear:both;
display:table;
content:"";
}
.nothas{
	text-decoration: line-through;
}
</style>
