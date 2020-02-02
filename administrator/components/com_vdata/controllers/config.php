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

class  VdataControllerConfig extends VdataController
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct()
	{
		parent::__construct();
		$this->model = $this->getModel('config');
		JFactory::getApplication()->input->set( 'view', 'config' );
	}
	
	function display($cachable = false, $urlparams = false){
		
		$user = JFactory::getUser();
		$canViewConfig = $user->authorise('core.access.config', 'com_vdata');
		
		if(!$canViewConfig){
			$msg = JText::_( 'ALERT_AUTHORIZATION_ERROR' );
			$this->setRedirect( 'index.php?option=com_vdata', $msg , 'error');
		}
			
		parent::display();
		
	}
	
	/**
	 * save a record (and redirect to main page)
	 * @return void
	 */
	function save()
	{
		
		if($this->model->store()) {
			$msg = JText::_( 'CONFIG_SAVED' );
			$this->setRedirect( 'index.php?option=com_vdata&view=config', $msg );
		} else {
			JFactory::getApplication()->enqueueMessage($this->model->getError());
			$this->setRedirect( 'index.php?option=com_vdata&view=config');
		}
	}
	
	/* function plugintaskajax()
	{
		
		$plugin = explode('.', JFactory::getApplication()->input->get('plugin', ''));
		
		if(count($plugin)==2)	{
		
			JPluginHelper::importPlugin('vdata', $plugin[0]);
			$dispatcher = JDispatcher::getInstance();
			
			try{
				$dispatcher->trigger($plugin[1]);
			}catch(Exception $e){
				jexit('{"result":"error", "error":"'.$e->getMessage().'"}');
			}
		
		}
		
	} */
	function close()
	{
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( 'index.php?option=com_vdata&view=vdata', $msg );
	}
}