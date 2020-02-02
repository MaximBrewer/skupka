<?php
/**
 * @version		$Id: view.html.php 21705 2011-06-28 21:19:50Z RuposTel.com $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
JHTMLOPC::stylesheet('onepage.css', 'components/com_onepage/themes/'.$this->selected_template.'/', array());

$document = JFactory::getDocument();
				$selectText = JText::_('COM_ONEPAGE_TAXES_DONOT_DELETE_GIFTS_STATUSES');
				$vm2string = "editImage: 'edit image',select_all_text: '".JText::_('Select All')."',select_some_options_text: '".JText::_($selectText)."'" ;


		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'javascript.php');
		OPCJavascript::loadJquery();  
				
if (method_exists('vmJsApi', 'js'))
		{
		$app = JFactory::getApplication(); 
		$jq = $app->get('jquery', false); 
		$jq_ui = $app->get('jquery-ui', false); 
		if (empty($jq) && (!OPCJ3))
		{
		
		//DEPRECATED IN VM3: 
		//vmJsApi::js('jquery','//ajax.googleapis.com/ajax/libs/jquery/1.6.4','',TRUE);
		//vmJsApi::js ('jquery-ui', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.16', '', TRUE);
		
		$document->addScript('//code.jquery.com/jquery-latest.min.js'); 
		if (empty($jq_ui))
		{
		JHTMLOPC::script('jquery-ui.min.js', 'components/com_onepage/themes/extra/jquery-ui/', false);
		JHTMLOPC::stylesheet('jquery-ui.min.css', 'components/com_onepage/themes/extra/jquery-ui/', false);
		} 
		$document->addScript('//code.jquery.com/jquery-migrate-1.2.1.min.js'); 
		$app->set('jquery', true); 
		$app->set('jquery-migrate', true); 
		
		
		}
		if (OPCJ3)
		 {
		 
		   JHtml::_('jquery.framework');
		   JHtml::_('jquery.ui');
		   JHtml::_('formbehavior.chosen', 'select');
		 }
		 else
		 {
		vmJsApi::js('chosen.jquery.min');
		vmJsApi::css('chosen');
		 }
		$document->addScriptDeclaration ( '
//<![CDATA[
		var vm2string ={'.$vm2string.'} ;
		 jQuery( function(jQuery) {
			var czn = jQuery(".vm-chzn-select"); 
			if (czn.chosen != \'undefined\')
			czn.chosen({enable_select_all: true,select_all_text : vm2string.select_all_text,select_some_options_text:vm2string.select_some_options_text});
		});
//]]>
				');
		
		
		}
		else
		{
		vmJsApi::jQuery(); 
		}
		
$base = JURI::base(); 
$jbase = str_replace('/administrator', '', $base); 	
if (substr($jbase, -1) !== '/') $jbase .= '/'; 

if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'jquery.noConflict.js'))
$document->addScript($jbase.'components/com_virtuemart/assets/js/jquery.noConflict.js');
else
if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'jquery.noconflict.js'))
$document->addScript($jbase.'components/com_virtuemart/assets/js/jquery.noconflict.js');

$selected_template = $this->selected_template; 

if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.$selected_template.DIRECTORY_SEPARATOR.'include.php'))
 include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.$selected_template.DIRECTORY_SEPARATOR.'include.php'); 


		