<?php
/**
 * @version		$Id: view.html.php 
 * @copyright	Copyright (C) 2005 - 2013 RuposTel.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

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
		$data = JRequest::getVar('data', ''); 
		$nd = json_decode($data, true); 
		if (!empty($nd))
		$msg = $model->storeData($nd); 
		else 
		$msg = 'Error !'; 
		
		$vmcat = $nd['vmcat']; 
		
		$data = array(); 
		$data['msg'] = $msg; 
		$data['cat_id'] = $vmcat; 
		$data['entity'] = JRequest::getVar('entity', ''); 
		
		echo json_encode($data); 
		
		$app = JFactory::getApplication(); 
		$app->close(); 
		
		die(); 
		
		
	}

}
