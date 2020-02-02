<?php
/**
 * @version		$Id: view.html.php 21705 2011-06-28 21:19:50Z dextercowley $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
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
class JViewEdittheme extends OPCView
{
	/**
	 * Display the view
	 */
	 
	public function display($tpl = null)
	{
	    set_time_limit(3600); 
		ini_set('max_execution_time', 3600);
	    $model = $this->getModel();
		$msg = $model->getPreview(); 
	    $cssfiles = $model->getCss(); 
		$getColors = array(); 
		$model->getColors($cssfiles, $getColors ); 
		$colors = $model->getPrefered(); 
		
		$this->assignRef('msgs', $msg); 
		$this->assignRef('templatecolors', $colors); 
	    $this->assignRef('cssfiles', $cssfiles); 
		$this->assignRef('colors', $getColors); 
		parent::display($tpl);
		
	}

}
