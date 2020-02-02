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
 
class deliveryViewajax extends JViewLegacy
{
  public function display($tpl = null)
	{
	 require_once(JPATH_SITE.DS.'plugins'.DS.'vmshipment'.DS.'pickup_or_free'.DS.'helper.php'); 
	 $cd = JRequest::getVar('cd'); 
	 
	 $data = pfHelper::checkDate($cd); 
	 
	 $x = @ob_get_clean(); $x = @ob_get_clean(); $x = @ob_get_clean(); $x = @ob_get_clean(); $x = @ob_get_clean(); $x = @ob_get_clean(); 
	 @header('Content-Type: application/json');
	 if (empty($data)) $data = ''; 
	 echo json_encode($data); 
	 JFactory::getApplication()->close(); 
	 die(); 
	 parent::display($tpl);
	}
}