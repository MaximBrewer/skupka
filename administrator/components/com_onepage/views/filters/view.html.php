<?php
/**
 * @version		$Id: view.html.php RuposTel.com
 * @copyright	Copyright (C) 2005 - 2011 RuposTel.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class JViewFilters extends OPCView
{
	
	public function display($tpl = null)
	{
		
		
		
		require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'config.php'); 
		$config = new JModelConfig(); 
		$config->loadVmConfig(); 
		
		$pms = $config->getPaymentMethods();
		$this->pms = $pms; 
		$sids = $config->getShipmentMethods();
	    if (empty($sids)) $sids = array(); 
		$this->sids = $sids; 
		
		
		
	    $this->model = $this->getModel();
		$this->assignRef('model', $this->model); 
		parent::display($tpl);
		
	}

}
