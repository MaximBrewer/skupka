<?php
/**
 * @version		$Id: contact.php 21555 2011-06-17 14:39:03Z chdemko $
 * @package		Joomla.Site
 * @subpackage	Contact
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class JControllerPairing extends JControllerBase
{
 function getViewName() 
	{ 
		return 'pairing';		
	} 

   function getModelName() 
	{		
		return 'pairing';
	}
}