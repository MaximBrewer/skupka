<?php

/**
 * include the unitejoomla library
 */
$currentDir = dirname(__FILE__) . "/";

if (!class_exists("UniteFunctions"))
    require_once $currentDir . 'functions.class.php';

if (!class_exists("UniteFunctionJoomla"))
    require_once $currentDir . 'functions_joomla.class.php';

if (!class_exists("UniteJoomlaAdminTable"))
    require_once $currentDir . 'admintable.class.php';

if (!class_exists("UniteImageView"))
    require_once $currentDir . 'image_view.class.php';

if (!class_exists("JMasterViewNivoRev"))
    require_once $currentDir . 'masterview.class.php';
?>