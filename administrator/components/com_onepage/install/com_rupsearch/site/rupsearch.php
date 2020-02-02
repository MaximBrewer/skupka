<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_rupsearch
 * @copyright	Copyright (C) 2005 - 2013 RuposTel.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
 
defined('_JEXEC') or die;

		


$app = JFactory::getApplication(); 
if (!$app->isAdmin())
 {
 
$controller = JControllerLegacy::getInstance('Rupsearch');
$controller->execute(JRequest::getCmd('task', 'display'));
$controller->redirect();
}


/*
  $q = 'update #__menu set client_id = 10 where `link` like "index.php?option=com_rupsearch" and type="component"'; 
  $db = JFactory::getDBO(); 
  $db->setQuery($q); 
  $db->query(); 
  
 */
