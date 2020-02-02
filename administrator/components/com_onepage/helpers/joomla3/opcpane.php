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

class OPCPane 
{
  var $options; 
  var $type; 
  var $name; 
  static $instance; 
  public static function getInstance($type='TabSet', $options=array(), $name='myTabs')
  {
    JHtml::_('jquery.framework');
		   JHtml::_('jquery.ui');
		   jimport ('joomla.html.html.bootstrap');
	
	if (!empty($options))
	if (!empty($options['active']))
	$arr = array('active'=>$options['active']); 
	
    JHtml::_('jquery.framework');
	JHtml::_('jquery.ui');
    //$this->options = $arr; 
	$pane = new OPCPane(); 
	$pane->type = $type; 
	$pane->name = $name; 
	$pane->options = $options; 
        self::$instance = $pane; 
	return $pane; 
  }
  function startPane($type)
   {
     //return JHtml::_('tabs.start', 'tab_group_id', $this->options);
	 $this->name = $type; 
	 return JHtml::_('bootstrap.startTabSet', str_replace("'", "&apos;", $this->name), $this->options);
	 
   }
  function startPanel($name, $id)
   { 
     return JHtml::_('bootstrap.addTab', $this->name, $id, str_replace("'", "&apos;", $name), true);
     //return JHtml::_('tabs.panel', $name, $id);
   }
  function endPanel()
  {
    return JHtml::_('bootstrap.endTab');
    return ''; 
  }
  
  function endPane()
  {
   return JHtml::_('bootstrap.endTabSet');
   return JHtml::_('tabs.end');
  }
}
