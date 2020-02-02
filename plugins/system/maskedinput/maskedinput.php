<?php
/**
 *  @package     Joomla.Plugin
 * @subpackage  System.maskedinput
 *
 * @copyright   Copyright © 2014 Beagler.ru. All rights reserved.
 * @license     GNU General Public License version 3 or later;
 */


defined('_JEXEC') or die('Restricted access');
jimport('joomla.plugin.plugin');

class plgSystemMaskedinput extends JPlugin {

    function onBeforeCompileHead() {
        if (JFactory::getApplication()->isAdmin()) {
            return;
        }
        $input = JFactory::getApplication()->input;
        $option = $input->getCmd('option', '');
        $mask = $this->params->get('mask');
        $controller = $input->getCmd('controller', '');
        $view = $input->getCmd('view', '');

        if ($option == 'com_virtuemart' && $view != 'category' && $view != 'productdetails') {
            $doc = JFactory::getDocument();
            $doc->addScript(JURI::base() . 'plugins/system/maskedinput/assets/jquery.maskedinput.min.js');
            $script = '
		jQuery(document).ready(function(){
			jQuery("input[name *= \'phone\']").mask("' . $mask . '");  
                        jQuery("input[name *= \'fax\']").mask("' . $mask . '"); 
                        jQuery("input[name *= \'tel\']").mask("' . $mask . '");  
		});
		
		';
            $doc->addScriptDeclaration($script);
        }
        if ($option == 'com_jshopping' && $controller != 'category' && $controller != 'product') {
            $doc = JFactory::getDocument();
            $doc->addScript(JURI::base() . 'plugins/system/maskedinput/assets/jquery.maskedinput.min.js');
            $script = '
		jQuery(document).ready(function(){
			jQuery("input[name *= \'phone\']").mask("' . $mask . '"); 
                        jQuery("input[name *= \'fax\']").mask("' . $mask . '"); 
                        jQuery("input[name *= \'tel\']").mask("' . $mask . '"); 
		});
		
		';
            $doc->addScriptDeclaration($script);
        }

        return;
    }

}
