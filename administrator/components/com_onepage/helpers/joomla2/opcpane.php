<?php
/*
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/ 
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.html.pane' );
class OPCPane
{
   var $jpane;
   public static function getInstance($name, $options)
   {
    $jpane = JPane::getInstance($name, $options); 
	return $jpane; 

   }
  function startPane($type)
   {
     //return JHtml::_('tabs.start', 'tab_group_id', $this->options);
	 return $this->jpane->startPane($type); 
	 
	 
   }
  function startPanel($name, $id)
   { 
     return $this->jpane->startPanel($name, $id); 
     
   }
  function endPanel()
  {
    return $this->jpane->endPanel(); 
  }
  
  function endPane()
  {
   return $this->jpane->endPane(); 
  }
}
