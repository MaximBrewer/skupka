<?php
/**
 * @package Unite Revolution Slider for Joomla 1.7-2.5
 * @author UniteCMS.net
 * @copyright (C) 2012 Unite CMS, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
	
	// Check to ensure this file is included in Joomla!
	defined('_JEXEC') or die( 'Restricted access' );
	
	jimport('joomla.application.component.view');
	
	$isJoomla3 = UniteFunctions::isJoomla3();
	
	if($isJoomla3){
	class JMasterViewNivoRev extends JViewLegacy{
	};
	
	}
	else{
	class JMasterViewNivoRev extends JView{
	};
	
	}
	
?>