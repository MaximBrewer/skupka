<?php
defined("_JEXEC") or die("Access deny");
JHtml::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/html');
?>
<div class="com_yandex_maps map">
	<h1><?php echo $this->item->title;?></h1>
	<div class="map_description"><?php echo $this->item->description?></div>
	<div class="map_view row-fluid">
		<div class="span12">
			<?php echo JHtml::_('map.show', $this->item->map, $this->item); ?>
		</div>
	</div>
</div>