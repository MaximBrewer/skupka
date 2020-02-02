<?php
/*------------------------------------------------------------------------
# com_vdata - vData
# ------------------------------------------------------------------------
# author    Team WDMtech
# copyright Copyright (C) 2014 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support:  Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class  VdataControllerDisplay extends VdataController
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct()
	{
		parent::__construct();
		$this->model = $this->getModel('display');
		JFactory::getApplication()->input->set( 'view', 'display' );
		// Register Extra tasks
		$this->registerTask( 'add', 'edit' );
	}
	
	function display($cachable = false, $urlparams = false)
	{
		
		$user = JFactory::getUser();
		$canViewDisplay = $user->authorise('core.access.display', 'com_vdata');
		if(!$canViewDisplay){
			$msg = JText::_( 'ALERT_AUTHORIZATION_ERROR' );
			$this->setRedirect( 'index.php?option=com_vdata', $msg , 'error');
		}
		
		parent::display();
	}
}