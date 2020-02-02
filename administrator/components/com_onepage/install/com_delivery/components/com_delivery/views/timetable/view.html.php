<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Login view class for Users.
 *
 * @package		Joomla.Site
 * @subpackage	com_users
 * @since		1.5
 */
 
class deliveryViewtimetable extends JViewLegacy
{
  public function display($tpl = null)
	{
	    require_once(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_delivery'.DS.'models'.DS.'config.php'); 
		$model = new configModelConfig(); 
		
	    //$model = $this->getModel('config');
		$this->shipment =& $model->getShipments(); 
		
		$this->model = $model; 
		
		
		 
		parent::display($tpl);
	}
}