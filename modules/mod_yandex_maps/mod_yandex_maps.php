<?php
/**
 * @copyright	Copyright (c) 2014 XDSoft (https://xdan.ru) chupurnov@gmail.com. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

$doc = JFactory::getDocument();
require_once JPATH_ADMINISTRATOR . "/components/com_yandex_maps/helpers/html/configHelper.php";
require_once JPATH_ADMINISTRATOR."/components/com_yandex_maps/helpers/CModel.php";
JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_yandex_maps/models');
JHtml::addIncludePath(JPATH_ROOT.'/components/com_yandex_maps/helpers');
JHtml::_('xdwork.frontjs');
$mapid = $params->get('mapid', 1);
$map = JModelLegacy::getInstance('Maps', 'Yandex_MapsModel')->model($mapid);
configHelper::addParams($params, 2);

require JModuleHelper::getLayoutPath('mod_yandex_maps', $params->get('template', 'default'));