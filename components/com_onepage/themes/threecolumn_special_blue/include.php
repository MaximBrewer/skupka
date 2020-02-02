<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
/*
* This file is part of stAn RuposTel one page checkout
* This is registered Virtuemart function to process one page checkout
* registration of this function is done automatically at first use of basket.php
* it uses all the fields from <form> and saves them into session and redirects to /html/checkout.onepage
* This function saves user information and the order to database and sends emails
*
* @copyright Copyright (C) 2007 - 2010 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free shoftware released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/
include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
$path = 'components/com_onepage/themes/'.$selected_template.'/'; 

JHTMLOPC::stylesheet('pbv.css', $path, array());
JHTMLOPC::stylesheet('tabcontent1.css', $path, array());
JHTMLOPC::script('tabcontent.js', $path, false);

 
$app = JFactory::getApplication(); 
$jtouch = $app->getUserStateFromRequest('jtpl', 'jtpl', -1, 'int');
if ($jtouch > 0)
 {
  JHTMLOPC::stylesheet('jtouch.css', $path, array() );
 }

if ((defined('OPC_DETECTED_DEVICE')) && (constant('OPC_DETECTED_DEVICE')!='DESKTOP'))
JHTMLOPC::stylesheet('mobile.css', $path, array() );

/*
JHTMLOPC::stylesheet('mobile.css', $path, array('media'=>'screen  and (-webkit-min-device-pixel-ratio : 1.5) ') );
JHTMLOPC::stylesheet('mobile.css', $path, array('media'=>'screen and (min-device-pixel-ratio : 1.5)') );
*/

//JHTMLOPC::script('checkbox.js', 'components/com_virtuemart/themes/default/templates/onepage/'.$selected_template, false);

JHTMLOPC::_('behavior.tooltip');
    $javascript = '
	if (typeof jQuery != \'undefined\')
	jQuery(document).ready(function(){
	 var userN = document.getElementById(\'username_login\'); 
	 if (userN != null && userN.value != \'\')
	  {
	    var labelU = document.getElementById(\'label_username_login\'); 
		if (labelU != null) labelU.innerHTML = \'\'; 
	  }
	 var userP = document.getElementById(\'passwd_login\'); 
	 if (userP != null && userP.value != \'\')
	  {
	    var labelP = document.getElementById(\'label_passwd_login\'); 
		if (labelP != null) labelP.innerHTML = \'\'; 
	  }
	
	});' ."\n\n";
 
	$document = JFactory::getDocument();
	$css = '
	<!--[if lte IE 7]>
		<link href="'.JURI::root().'components/com_onepage/themes/'.$selected_template.'/ie.css" rel="stylesheet" type="text/css">
	
	<![endif]-->
	'; 
	// $css .= ' @import url("'.JURI::root().'components/com_onepage/themes/'.$selected_template.'/ie.css");
	$document->addScriptDeclaration($javascript); 
	$document->addCustomTag($css);

	if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'slimbox'.DIRECTORY_SEPARATOR.'slimbox.js'))
	{
	 JHTMLOPC::script('slimbox.js', 'plugins/content/slimbox/', false);
	 JHTMLOPC::stylesheet('slimbox.css', 'plugins/content/slimbox/', array());
	}
