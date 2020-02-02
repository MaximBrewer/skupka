<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/
jimport( 'joomla.application.component.view' );

class vmeeProViewmaintenance extends JViewLegacy {

	/**
	 * @return vmeeProModelMaintenance
	 */
	private function getMaintenanceModel() {
		$model = $this->getModel();
		return $model;
	}
	
	function display($tpl = null)   {
		JToolBarHelper::title( VMEE_PRO_TITLE, 'interamind_logo' );
		JToolBarHelper::preferences( 'com_vmeeplus',650 );
		JToolBarHelper::custom("help", "help", "help", "Help", false);
		
		$model = $this->getMaintenanceModel();
		//vm emails manager templates
		if($model->isVMEmailsInstalled()){
			$emailsArr = array(	1=>'User registration confirmation',
								2=>'Order comfirmation',
								3=>'Order status changed',
								4=>'Download ID',
								5=>'Admin order confirmation');
			$this->assignRef('emails', $emailsArr);
		}	
		
        parent::display($tpl);
        vmeePlusHelper::addSubmenu('maintenance');
    }
}
