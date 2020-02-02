<?php
/**
 * @package Unite Gallery
 * @author UniteCMS.net / Valiano
 * @copyright (C) 2012 Unite CMS, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('_JEXEC') or die('Restricted access');

$headerTitle = __("General Settings", UNITEGALLERY_TEXTDOMAIN);

$operations = new UGOperations();

$objSettings = $operations->getGeneralSettingsObject();

$objOutput = new UniteSettingsProductUG();
$objOutput->init($objSettings);
$objOutput->setShowSaps(false);

require HelperUG::getPathTemplate("settings");

