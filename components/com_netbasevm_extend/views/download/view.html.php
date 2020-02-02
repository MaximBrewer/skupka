<?php

if (!defined('_VALID_MOS') && !defined('_JEXEC'))
die('Direct Access to ' . basename(__FILE__) . ' is not allowed.');

/**
 * Package: com_digitolldownloads
 * Version: 1.0.5
 * Author: Stephen Roberts
 * Date: July 03 2012
 * Description: DigiToll Downloads is a full-featured download system for VirtueMart 2, giving you control over download management and delivery.
 * Copyright: Copyright (C) 2012 Stephen Roberts. All rights reserved.
 * Legal: GNU General Public License version 2 or later
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the view framework
jimport('joomla.application.component.view');


/**
 * Handle the orders view
 */
class DigiTollDownloadsViewDownload extends JViewLegacy {
	
	public function display($tpl = null)
	{
		$download_code = JRequest::getVar('download_code', '');
                $this->download_code=$download_code;
		
		parent::display($tpl);
	}
}