<?php
/**
 * @version		OPC XML Export
 * @copyright	Copyright (C) 2005 - 2013 RuposTel.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');
@ini_set('memory_limit', '512M'); 
/**
 * View class for a list of banners.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_banners
 * @since		1.6
 */
jimport('joomla.application.component.view');
class JViewPairing extends OPCView
{
	/**
	 * Display the view
	 */
	 
	public function display($tpl = null)
	{
	    $model = $this->getModel();
		
		 //$config = JController::getModel('config', 'JModel'); 
		 require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'config.php'); 

		 $config = new JModelConfig(); 
		 $config->loadVmConfig(); 
		 
		 $this->data = $model->getData(); 
		 
		$this->model = $model; 
		$this->cats = $model->getVmCats(); 
		
		
		
		

		parent::display($tpl);
		
	}

}
