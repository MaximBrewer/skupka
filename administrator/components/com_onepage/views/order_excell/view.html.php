<?php
/*
*
* @copyright Copyright (C) 2007 - 2010 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/

	defined( '_JEXEC' ) or die( 'Restricted access' );
	jimport('joomla.application.component.view');
	if (!class_exists('OnepageTemplateHelper'))
	require ( JPATH_ROOT.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'export_helper.php');

	class JViewOrder_excell extends OPCView
	{
		function display($tpl = null)
		{	
		
			

			$model = $this->getModel();
			
			$ehelper = new OnepageTemplateHelper();
			
			$this->assignRef('ehelper', $ehelper);
			$this->assignRef('templates', $templates);
			$this->assignRef('model', $model);
			
			parent::display($tpl); 
		}
	}
