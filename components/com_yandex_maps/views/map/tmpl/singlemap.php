<?php
defined("_JEXEC") or die("Access deny");
if (empty($map) && $this->map) {
	$map = $this->map;
}
if (empty($params) && $this->params) {
	$params = $this->params;
}
JHtml::addIncludePath(JPATH_ROOT.'/administrator/components/com_yandex_maps/helpers/html');
echo JHtml::_('map.show',$map);
