<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @since		1.5
 */

defined('_JEXEC') or die;

/**
 * Base controller class for Delivery.
 
 */
class DeliveryController extends JControllerLegacy
{
   /**
	 * Method to display a view.
	 *
	 * @param	boolean			If true, the view output will be cached
	 * @param	array			An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.
	 * @since	1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		// Get the document object.
		$document	= JFactory::getDocument();

		// Set the default view name and format from the Request.
		$vName	 = JRequest::getCmd('view', 'ajax');
		$vFormat = $document->getType();
		$lName	 = JRequest::getCmd('layout', 'default');
		
		// Push the model into the view (as default).
		$model = $this->getModel($vName);
		
		$view = $this->getView($vName, $vFormat);
		
		//$thiw->addModelPath(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_delivery'.DS.'models'); 
		jimport('joomla.application.component.model');
        //JModel::addIncludePath(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_delivery'.DS.'models');
		
	    $view->setModel($model, true);
	    $view->setLayout($lName);
		// Push document object into the view.
		$view->assignRef('document', $document);
		$view->display();
	}
}