<?php
/**
 * @version		$Id: default.php 21837 2011-07-12 18:12:35Z dextercowley $
 * @package		RuposTel OnePage Utils
 * @subpackage	com_onepage
 * @copyright	Copyright (C) 2005 - 2013 RuposTel.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
$document = JFactory::getDocument();
$document->setTitle(JText::_('COM_ONEPAGE_CATEGORY_PAIRING')); 
JHTMLOPC::script('opcbe.js', 'administrator/components/com_onepage/assets/js/', false);
$document->setTitle(JText::_('COM_ONEPAGE_CATEGORY_PAIRING')); 
jimport ('joomla.html.html.bootstrap');

if (!OPCJ3)
{
JHTMLOPC::stylesheet('bootstrap.min.css', 'components/com_onepage/themes/extra/bootstrap/', array());

$css = '
#vmMainPageOPC dl#pane {
 margin-bottom: 0; 
} '; 
$document = JFactory::getDocument(); 
$document->addStyleDeclaration($css); 

}


$css = '
#toolbar-box {
 display: none; 

 
}

div.container-fluid:empty {
 display: none; 
}
 '; 

$document->addStyleDeclaration($css); 


if (method_exists('vmJsApi', 'js'))
		{
		$app = JFactory::getApplication(); 
		$jq = $app->get('jquery'); 
		if (empty($jq) && (!OPCJ3))
		{
		//vmJsApi::js('jquery','//ajax.googleapis.com/ajax/libs/jquery/1.6.4','',TRUE);
		//vmJsApi::js ('jquery-ui', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.16', '', TRUE);
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'javascript.php');
		
		OPCJavascript::loadJquery();  		
		
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
		 jQuery( function($) {
			$(".vm-chzn-select").chosen({enable_select_all: false,select_all_text : vm2string.select_all_text,select_some_options_text:vm2string.select_some_options_text});
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
		