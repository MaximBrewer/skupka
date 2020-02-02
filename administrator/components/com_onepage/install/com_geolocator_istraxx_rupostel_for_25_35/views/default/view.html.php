<?php
/*
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* GeoLocator is free software released under GNU/GPL  This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
* This php file was created by www.rupostel.com team
*/

	defined( '_JEXEC' ) or die( 'Restricted access' );
	jimport('joomla.application.component.view');
	class defaultViewDefault extends JViewLegacy
	{
		function display($tpl = null)
		{	
		 
			global $option, $mainframe;
			
			
			$model = $this->getModel('default');
			
			
			parent::display($tpl); 
		}
	}
