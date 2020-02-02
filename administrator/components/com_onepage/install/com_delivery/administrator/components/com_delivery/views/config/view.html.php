<?php
/**
 * @version		$Id: view.html.php 
 * @copyright	Copyright (C) 2005 - 2013 RuposTel.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');


//class JViewConfig extends OPCView
//class DeliveryViewConfig extends JViewLegacy
class configViewConfig extends OPCView
{
	/**
	 * Display the view
	 */
	 
	public function display($tpl = null)
	{
	
	
	    $model = $this->getModel();
		$this->shipment =& $model->getShipments(); 
		$this->model = $model; 
		$print = JRequest::getVar('tmpl', ''); 
		if (!empty($print))
		 {
		   $this->setLayout('print'); 
		 }
		parent::display($tpl);
		
	}

}
