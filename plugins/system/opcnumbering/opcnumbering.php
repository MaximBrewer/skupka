<?php
/**
 * @version		opcnumbering.php 
 * @copyright	Copyright (C) 2005 - 2015 RuposTel.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

class plgSystemOpcnumbering extends JPlugin
{
    function __construct(& $subject, $config)
	{
		if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'compatibility.php')) return; 
		
		parent::__construct($subject, $config);
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'compatibility.php'); 
	}
	
	private function canRun()
	{
		if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'))
			{
				
				return false;
			}
		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
				require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'numbering.php'); 
	
	
	   //OPCNumbering::$debug = false; 
	   return true; 
	}
	
	public function plgVmOnUserInvoice($orderDetails,&$data)
	{
		if (!$this->canRun()) return; 
		
		$order_agenda_id = OPCconfig::get('order_numbering', false);
			
		
		//$data['invoice_number']
		$agenda_id = OPCconfig::get('invoice_numbering', false); 
		
		
		
		if (empty($agenda_id)) return; 
		
		
		$order_id = null; 
		
		if (is_object($orderDetails))
		{
		if (!empty($orderDetails->virtuemart_order_id ))
		$order_id = $orderDetails->virtuemart_order_id; 
	    
		}
		else
		{
			if (!empty($orderDetails['details']['BT']))
			if (is_object($orderDetails['details']['BT']))
			$order_id = $orderDetails['details']['BT']->virtuemart_order_id; 
			
			if (empty($order_id))
			{
				if (is_array($orderDetails))
				{
					if (!empty($orderDetails['virtuemart_order_id']))
					{
						$order_id = $orderDetails['virtuemart_order_id']; 
					}
					
					$number = $orderDetails['order_number']; 
				}
			}
			
			//virtuemart_order_id
		}
		
		
		 if ($agenda_id === $order_agenda_id)
		 {
			 if (substr($number, -2) === '-1') $number = substr($number, 0, -2); 
		 }
		 
		if ((!empty($order_id)) && (!empty($number)))
				{
					
					OPCNumbering::updateTypeid($agenda_id, 0, $order_id, $number);
					$updated = true; 
					
					
				}
		
		
		
		
		$numbering = OPCNumbering::requestNew($agenda_id, 1, $order_id);
		
		
		
		if (!empty($numbering))
		{
		$data['invoice_number'] = $numbering; 
		
		if (empty(self::$mydata)) self::$mydata = array(); 
		if (empty(self::$mydata[1])) self::$mydata[1] = array(); 
		self::$mydata[1][] = $numbering; 
		}
		
	}
	public static $mydata; 
	//plgVmConfirmedOrder
	public function plgVmConfirmedOrder($cart, $order)
	{
		
		if (!$this->canRun()) return; 
		
		$order_number = $order['details']['BT']->order_number; 
		$order_id = 0;
		$order_id = $order['details']['BT']->virtuemart_order_id; 
		$order_status = $order['details']['BT']->order_status; 
		$updated = false; 
		$agenda_id = OPCconfig::get('order_numbering', false);
		
		if (!empty(self::$mydata))
		{
			foreach (self::$mydata as $k=>$v)
			{
				// order: 
				if (empty($k))
				{
				foreach ($v as $number)
				{
				
				
				if ($order_number == $number)
				{
					
				}
				else
				{
					$db = JFactory::getDBO(); 
					$q = "select virtuemart_order_id from #__virtuemart_orders where order_number = '".$db->escape($number)."' order by created_on desc limit 0,1"; 
					$db->setQuery($q); 
					$order_id = $db->loadResult(); 
				}
				
				if (!empty($order_id))
				{
					OPCNumbering::updateTypeid($agenda_id, 0, $order_id, $number);
					$updated = true; 
					
					
				}
				}
				}
			}
		}
		
		if (empty($updated))
		{
			OPCNumbering::updateTypeidByNumber($agenda_id, 0, $order_id, $order_number);
		}
		
		$order_id = $order['details']['BT']->virtuemart_order_id; 
		
		require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'export_helper.php');
			if (class_exists('OnepageTemplateHelper'))
	{
	$ehelper = new OnepageTemplateHelper($order_id);
    
    $templates = $ehelper->getExportTemplates('ALL');
	
	
	
	//$x =  var_export($templates, true);
	//$x .= var_export($d, true); 
    //file_put_contents(JPATH_SITE.DIRECTORY_SEPARATOR.'log.txt', $x); 
	foreach ($templates as $t)
    {
      if (empty($t['tid_enabled'] )) continue;
      if (empty($t['tid_autocreate'])) continue;
	  //echo $t['tid_autocreatestatus'].' '.$d['order_status'].';';
      
	  
	  if (!in_array($order_status, $t['tid_autocreatestatus'])) continue; 
	  
      if ((!empty($t['tid_foreign'])) && (!empty($t['tid_foreigntemplate']))) 
	  $special = $ehelper->getNextAi($t['tid_foreigntemplate'], $order_id);
      else
      if ((!empty($t['tid_special'])) && (!empty($t['tid_ai']))) $special = $ehelper->getNextAi($t['tid'], $order_id);
	  if (!empty($t['tid_special']) && (empty($special))) continue;		      
	 	$tid = $t['tid'];
	 
	 $specials = array();
	 if (!empty($special))
	 $specials[0] = $special;
 
	
 
	 ob_start(); 
	 $ehelper->processTemplate($tid, $order_id, $specials, 'AUTOPROCESSING');
	 $x = ob_get_clean();
	 
	
	}
	
	
	 }
		
	}
	public function plgVmOnUserOrder(&$orderDetails)
	{
		
			
		
		//$_orderData->order_number
		if (!$this->canRun()) 
		{
			
			return; 
		}
		$agenda_id = OPCconfig::get('order_numbering', false); 
		
		
		
		if (empty($agenda_id)) 
		{
			
			return;
		}
		$order_id = null; 
		
		if (is_object($orderDetails))
		{
		if (!empty($orderDetails->virtuemart_order_id ))
		$order_id = $orderDetails->virtuemart_order_id; 
	    
		}
		else
		{
			if (!empty($orderDetails['details']['BT']))
			$order_id = $orderDetails['details']['BT']->virtuemart_order_id; 
			
		}
		
		
		
		$numbering = OPCNumbering::requestNew($agenda_id, 0, $order_id); 
		
	
		
		if (!empty($numbering))
		{
		
		$orderDetails->order_number = $numbering; 
		
				if (empty(self::$mydata)) self::$mydata = array(); 
		if (empty(self::$mydata[0])) self::$mydata[0] = array(); 
		self::$mydata[0][] = $numbering; 
	    return; 
		
		
		}
		
		
	}
	
}
