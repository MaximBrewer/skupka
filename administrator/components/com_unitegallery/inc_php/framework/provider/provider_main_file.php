<?php

require_once $currentFolder . "/unitegallery_admin.php";
require_once $currentFolder . "/inc_php/framework/provider/provider_admin.class.php";

$productAdmin = new UniteProviderAdminUG();

//set global title
$title = JText::_('com_unitegallery');
JToolBarHelper::title($title, 'generic.png');

UniteFunctionJoomlaUG::disableMootools();


?>