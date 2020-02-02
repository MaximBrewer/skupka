<?php

/**
 * @package Unite Nivo Slider Pro for Joomla 1.7-2.5
 * @version 1.0.0
 * @author UniteJoomla.com
 * @copyright (C) 2012- Unite Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

$currentDir = dirname(__FILE__) . "/";

require_once $currentDir . "helpers/globals_nivosliderpro.class.php";
require_once $currentDir . "helpers/helper_nivosliderpro.class.php";
require_once $currentDir . "helpers/nivosliderpro_output.class.php";
require_once $currentDir . "unitejoomla/includes.php";
$isJoomla3 = UniteFunctions::isJoomla3();

global $g_nivoSliderVersion;
$g_nivoSliderVersion = "3.1";

$currentDir = dirname(__FILE__) . "/";
if ($isJoomla3) {

//    require_once $currentDir . 'masterfield_joomla3.class.php';
    require_once $currentDir . "models/model_joomla3.php";

    class JMasterViewNivoBaseRev extends JViewLegacy {
        
    }

    ;

    class JControllerNivoBaseRev extends JControllerLegacy {
        
    }

    ;
} else {  //joomla 2.5
    require_once $currentDir . "models/model_joomla2.php";

    class JMasterViewNivoBaseRev extends JView {
        
    }

    ;

    class JControllerNivoBaseRev extends JController {
        
    }

    ;
}
//init the globals
GlobalsNivoPro::init();
UniteFunctionJoomla::$componentName = GlobalsNivoPro::COMPONENT_NAME;
?>