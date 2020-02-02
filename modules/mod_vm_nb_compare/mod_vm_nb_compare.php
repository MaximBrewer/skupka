<?php
/*------------------------------------
* Compare Products for Virtuemart
* Author    CMSMart Team
* Copyright Copyright (C) 2012 http://cmsmart.net. All Rights Reserved.
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* Websites: http://cmsmart.net
* Email: team@cmsmart.net
* Technical Support:  Forum - http://bloorum.com/forums
* Version 1.0.0
-----------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
$position = $params->get( 'compare_position');
$addtocart = $params->get( 'addtocart');
$rating = $params->get( 'rating');
$price = $params->get( 'price');
$description = $params->get( 'description');
$category = $params->get( 'category');
$manufacturer = $params->get( 'manufacturer');
$availability = $params->get( 'availability');
$product_sku = $params->get( 'product_sku');
$weight = $params->get( 'weight');
$length = $params->get( 'length');
$link = $params->get( 'link');

$mater = $params->get( 'compare_temp');



//Load
$document = JFactory::getDocument();

if ($mater!=="left") {
   $document->addScript('modules/mod_vm_nb_compare/assets/js/mod.js');
}
$document->addStyleSheet('modules/mod_vm_nb_compare/assets/font/css/font-awesome.css');
if($position != 'none'){
    // $document->addStyleSheet('modules/mod_vm_nb_compare/assets/css/style.css');
}else{
    $document->addStyleSheet('modules/mod_vm_nb_compare/assets/css/style2.css');
}
$js = <<<ENDJS
//<![CDATA[
var position_compare = "$position";
//]]>
ENDJS;
$document->addScriptDeclaration("$js");
$document->addStyleSheet('modules/mod_vm_nb_compare/assets/css/popup.css');
if ($mater=="left") {
    require(JModuleHelper::getLayoutPath('mod_vm_nb_compare','article'));
} else {
    require(JModuleHelper::getLayoutPath('mod_vm_nb_compare'));
}



