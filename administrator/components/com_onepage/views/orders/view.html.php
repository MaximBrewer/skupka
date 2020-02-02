<?php
/*
*
* @copyright Copyright (C) 2007 - 2010 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/

	defined( '_JEXEC' ) or die( 'Restricted access' );
	jimport('joomla.application.component.view');
	class JViewOrders extends OPCView
	{
		function display($tpl = null)
		{	
			// load language: 
			
			require_once ( JPATH_ROOT.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'export_helper.php');
			
			$model = $this->getModel();
			$model->loadVirtuemart(); 
			require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'config.php'); 
			$config = new JModelConfig(); 
			$config->loadVmConfig(); 
			require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'listfactory.php'); 
			$this->statuses = $config->getOrderStatuses();
			// Get data from the model
			$pagination = $this->get('Pagination');
            $items = $this->get('Data');   
            $total = $this->get('Total');   
            
			$ehelper = new OnepageTemplateHelper();
			$templates = $ehelper->getExportTemplates('ALL');

			$templates = $model->getTemplates();
			//$order_data = $model->getOrderData();
		    
		    //$ehelper = new OnepageTemplateHelper($order_id);
			
			$this->assignRef('ehelper', $ehelper);
			$this->assignRef('templates', $templates);
			$this->assignRef('model', $model); 
            // push data into the template
            $this->assignRef('items', $items);     
            $this->assignRef('total', $total);     
            $this->assignRef('pagination', $pagination);

			
			parent::display($tpl); 
		}
	
	
	public function getUrl()
	 {
		 require_once ( JPATH_ROOT.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'export_helper.php');
		 $ehelper = new OnepageTemplateHelper();
		 return $ehelper->getUrl(); 
	 }
	
	
	
	
function getRefOrders($order_id, $tree=false)
	{
	 if (defined('PARTNERS_INSTALLED') && (PARTNERS_INSTALLED=='0')) return "";
	 
	
	 
	 

	 $db = JFactory::getDBO();
	 $q = "SHOW TABLES LIKE '".$db->getPrefix()."partners_orders'";
	 $db->setQuery($q);
	 $r = $db->loadResult();
	 if (empty($r)) 
	 {
	 define('PARTNERS_INSTALLED', '0');
	 return "";
	 }
	   
	   
	 $q = "SELECT * FROM #__partners_orders where order_id = ".$order_id." order by id desc LIMIT 0,100";
	 $db->setQuery($q);
	 $res = $db->loadAssocList();
	 $msg = $db->getErrorMsg();
	 if (!defined('PARTNERS_INSTALLED'))
	 {
	 if (!empty($msg)) 
	 {
	  define('PARTNERS_INSTALLED', '0');
	  return "";
	 }
	 else 
	  define('PARTNERS_INSTALLED', '1');
	 }
	 
	 $orders = array();
	 
	 foreach($res as $val)
	 {
	  
	  $order_id = $val['order_id'];
	  $orders[$order_id] = array();
	  
	  $q = "select first_name, last_name from #__virtuemart_order_userinfos where virtuemart_order_id = '".(int)$order_id."' and address_type = 'BT' limit 0,1 ";
	  $db->setQuery($q);
	  $row = $db->loadAssoc();
	  $orders[$order_id]['first_name'] = $row['first_name'];
	  $orders[$order_id]['last_name'] = $row['last_name'];
	  $orders[$order_id]['order_total'] = $val['order_total'];
	  $q = "select * from #__partners_ref where order_ref_id = '".$val['id']."' order by start asc";
	  $db->setQuery($q); 
	  $data = $db->loadAssocList();
	  foreach ($data as $k)
	  {
	   $arr = array();
	   
	   $arr['title'] = $k['title'];
	   $arr['url'] = $k['url'];
	   $arr['ref'] = $k['ref'];
	   if (!$tree)
	   {
	    if (empty($arr['ref'])) return '';
	    else
	    {
	     $ref = urldecode(urldecode($arr['ref']));
	     $p1 = strpos($ref, '//');
	     $p2 = strpos($ref, '/', $p1+3);
	     if ($p1 !== false && $p2 !== false)
	     return substr($ref, $p1+2, $p2-$p1-2);
	     else return $arr['ref'];
	    }
	   }
	   
	   
	   $start = $k['start'];
	   $end = $k['end'];
	   if (!empty($start) && (!empty($end)))
	   {
	    $time = $end-$start;
	    $time = number_format($time, 0).' sec ';
	    $arr['time'] = $time;
	   }
	   else $arr['time'] = 'Unknown';
	   if (empty($orders[$order_id]['ref']))
	   {
	    $orders[$order_id]['ref'] = array();
	    $orders[$order_id]['ref'][] = $arr;
	   }
	   else
	     $orders[$order_id]['ref'][] = $arr;
	  }
	 }
	 if (!$tree) return "";
	 return $orders;
	}
	
	
	function printWrapper($field, $start = false)
	{
	 if ($start == true)
	 {
	  
	 }
	 else
	 {
	 $html = '<div id="buttons_'.$field.'" style="display: none; ">'
	       .'<input type="button" id="update_'.$field.'" value="Update" size="10" class="ipt" onclick="javascript:op_update(this);" />'
       			 .'<input type="button" id="cancel_'.$field.'" value="Cancel" size="10" class="ipt" onclick="javascript:op_cancel(this);" />'
	          			.'</div>';
	 return $html;
	 }
	}
	}
	
