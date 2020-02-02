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
class JViewXmlexport extends OPCView
{
	/**
	 * Display the view
	 */
	 
	public function display($tpl = null)
	{ 
	    $model = $this->getModel();
		
		 //$config = JController::getModel('config', 'JModel'); 
		 require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'config.php'); 
		 require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		 
		 
		 
		 
		 $config = new JModelConfig(); 
		 $config->loadVmConfig(); 
		 $model->getGeneral($this); 

		$this->avai = $model->getAvai(); 
		$this->model =& $model; 
		$this->numprods = $model->getNumProducts(); 
		$this->trackingfiles = $model->getPhpExportThemes(); 
		$this->forms = $model->getJforms($this->trackingfiles); 
		$this->shoppergroups = $model->getShopperGroups(); 
		$this->langs = $model->getLanguages(); 
		$this->isEnabled = $model->isEnabled(); 
		$model->checkCompression($this); 
		
		$this->xml_export_customs = OPCconfig::getValue('xmlexport_config', 'xml_export_customs', 0, false); 
		$this->xml_export_test = OPCconfig::getValue('xmlexport_config', 'xml_export_test', 0, ''); 
		$this->xml_disable_categorycache = OPCconfig::getValue('xmlexport_config', 'xml_disable_categorycache', 0, false); 
		
		
		if (!empty($this->xml_export_customs))
		{
			$this->coreGroups = $model->getCoreGroups(); 
			$this->customFields = $model->buildCustomFields(); 
		}
		
		parent::display($tpl);
		
	}

}
