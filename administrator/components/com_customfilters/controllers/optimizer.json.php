<?php
/**
 *
 * The optimizer controller file
 *
 * @package 	customfilters
 * @author		Sakis Terz
 * @link		http://breakdesigns.net
 * @copyright	Copyright (c) 2012-2018 breakdesigns.net. All rights reserved.
 * @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *				customfilters is free software. This version may have been modified
 *				pursuant to the GNU General Public License, and as distributed
 *				it includes or is derivative of works licensed under the GNU
 *				General Public License or other free or open source software
 *				licenses.
 * @version $Id: controller.php 2014-03-06 19:09:00Z sakis $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the controller framework
jimport('joomla.application.component.controller');

/**
 * main controller class
 * @package		customfilters
 * @author		Sakis Terz
 * @since		1.0
 */
class CustomfiltersControllerOptimizer extends JControllerLegacy{



	/**
	 * Proxy for getModel.
	 *
	 * @param	string	$name	The name of the model.
	 * @param	string	$prefix	The prefix for the PHP class name.
	 *
	 * @return	JModel
	 * @since	1.0
	 */
	public function getModel($name = 'Optimizer', $prefix = 'CustomfiltersModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}
	
	/**
	 * Triggers the optimization functions in the model
	 * @since 1.9.5
	 * @return JSon object containing the log 
	 */
	public function optimize(){
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$model=$this->getModel();
		$log=$model->optimize();
		echo json_encode($log);
	}
	

}
