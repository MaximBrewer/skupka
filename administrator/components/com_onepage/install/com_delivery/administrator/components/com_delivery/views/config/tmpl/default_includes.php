<?php
/*
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/



	defined( '_JEXEC' ) or die( 'Restricted access' );
$document = JFactory::getDocument();
				$selectText = 'Vehicles';
				$vm2string = "editImage: 'edit image',select_all_text: '".JText::_('Select All')."',select_some_options_text: '".JText::_($selectText)."'" ;
				
if (!class_exists('VmConfig'))
	    require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');
	    VmConfig::loadConfig(); 
		if (method_exists('vmJsApi', 'js'))
		{
		$app = JFactory::getApplication(); 
		$jq = $app->get('jquery'); 
		if (empty($jq) && (!OPCJ3))
		{
		vmJsApi::js('jquery','//ajax.googleapis.com/ajax/libs/jquery/1.6.4','',TRUE);
		vmJsApi::js ('jquery-ui', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.16', '', TRUE);
		}
		if (OPCJ3)
		 {
		   JHtml::_('jquery.framework');
		   JHtml::_('jquery.ui');
		   JHtml::_('formbehavior.chosen', 'select');
		 }
		 else
		 {
		 //$document->addScript(JURI::base().'components/com_delivery/views/config/tmpl/js/chosen.jquery.js');
		vmJsApi::js('chosen.jquery.min');
		vmJsApi::css('chosen');
		 }
		$document->addScriptDeclaration ( '
//<![CDATA[
		var vm2string ={'.$vm2string.'} ;
		 
jQuery(document).ready( function(jQuery) {

			
			 
			
			jQuery(".vm-chzn-select").chosen({
			    inherit_select_classes: true,
				enable_select_all: true,
				select_all_text : vm2string.select_all_text,
				select_some_options_text:vm2string.select_some_options_text
				 });
			
			
			
		});
//]]>
				');
		
		
		}
		else
		{
		vmJsApi::jQuery(); 
		}
		$document->addScript(JURI::base().'components/com_virtuemart/assets/js/jquery.noConflict.js');
		
		
	$css = ' .chzn-container-multi .chzn-choices .search-field input {
	 height: 25px; 
	} 
	.chzn-choices, .vm-chzn-select {
	 width: 100px; 
	 max-width: 130px; 
	}
	iframe {
	  width: 95%; 
	  height: 300px;
	  border: 1px solid #ddd; 
	}
	
	'; 
	
	$document->addStyleDeclaration($css); 