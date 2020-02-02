<?php
defined("_JEXEC") or die("Access deny");
JHtml::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/html');
$doc = JFactory::getDocument();
$doc->setBase(JURI::base());
?>
<div class="com_yandex_maps object">
	<h1><?php echo $this->object->title?></h1>
	<?php 
	if ($this->object->organization_object_id) {
		$organization = $this->object;
		echo jhtml::_('xdwork.organization', $organization);
	} ?>

	<?php if ($this->params->get('show_description_on_object_page', 1)) { ?>
	<div class="xdsoft_object_description">
        <?php echo jhtml::_('xdwork.description', $this->object->_data, $this->object->map);?>
    </div>
	<?php } ?>
	<div class="xdsoft_object_view">
		<?php if ($this->params->get('show_map_on_object_page', 1)) {
				echo JHtml::_('map.show',$this->object->map, $this->object);
			}
		?>
	</div>
</div>