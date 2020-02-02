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
class JViewUtils extends OPCView
{
	/**
	 * Display the view
	 */
	 
	public function display($tpl = null)
	{
		require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'config.php'); 
		 
		 $config = new JModelConfig(); 
		 $config->loadVmConfig(); 
		$this->opc =& $config; 
		 $this->cid = $config->getVendorCurrency(); 
		$this->currencies = $config->getCurrencies(); 
		//listShopperGroups
		$this->sgs = $config->listShopperGroups(); 
		
	    $model = $this->getModel();
		$this->menus = $model->getMenus(); 
		$this->sortedmenu = $model->getMenusSorted(); 
		$this->defaults = $model->getDefaults(); 
		$session = JFactory::getSession(); 
	    $res = $session->get('opcsearch', ''); 
		$this->model =& $model; 
		$this->results = $res;
		$this->cats = $model->getCats(); 
		
		parent::display($tpl);
		
	}
	public function printChildren($arr, $value, $title, $prefix='')
	{
	   $model = $this->getModel();
	   return $model->printChildren($arr, $value, $title, $prefix='->'); 

	}

}
