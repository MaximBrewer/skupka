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
jimport( 'joomla.application.component.model' );
jimport( 'joomla.filesystem.file' );
require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'compatibility.php'); 
require_once( JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'export_helper.php' );
class JModelOrder_excell extends OPCModel
{
	function __construct()
	{
		parent::__construct();
		
	}
	
	function getCountry($country_id) {
		$country_id = (int)$country_id; 
		
		static $cache; 
		if (isset($cache[$country_id])) return $cache[$country_id]; 
		
		 $db = JFactory::getDBO(); 
		   $q = "select * from #__virtuemart_countries where virtuemart_country_id = '".(int)$country_id."' limit 0,1"; 
		   $db->setQuery($q); 
		   $res = $db->loadAssoc(); 
		   $copy = array(); 
		   if (!empty($res)) {
		   foreach ($res as $key5=>$val5)
		    {
			   $copy[$key5] = $val5; 
			}
		   }
		   else {
		   $q = "select * from #__virtuemart_countries where 1=1 limit 0,1"; 
		    $db->setQuery($q); 
		    $res = $db->loadAssoc(); 			   
			if (!empty($res)) {
			$copy = array(); 
			foreach ($res as $key=>$vv) {
				$copy[$key] = ''; 
			}
			}
		   }
		   
		   unset($copy['published']); 
		   unset($copy['shared']); 
		   unset($copy['ordering']); 
			$cache[$country_id] = $copy; 
			return $copy; 
	}
	
	function getVMState($state_id) {
		$state_id = (int)$state_id; 
		
		static $cache; 
		if (isset($cache[$state_id])) return $cache[$state_id]; 
		
		 $db = JFactory::getDBO(); 
		   $q = "select * from #__virtuemart_states where virtuemart_state_id = '".(int)$state_id."' limit 0,1"; 
		   $db->setQuery($q); 
		   $res = $db->loadAssoc(); 
		   $copy = array(); 
		   if (!empty($res)) {
		   foreach ($res as $key5=>$val5)
		    {
			   $copy[$key5] = $val5; 
			}
		   }
		   else {
			$q = "select * from #__virtuemart_states where 1=1 limit 0,1"; 
		    $db->setQuery($q); 
		    $res = $db->loadAssoc(); 			   
			if (!empty($res)) {
			$copy = array(); 
			foreach ($res as $key=>$vv) {
				$copy[$key] = ''; 
			}
			}
		   }
		   
		   unset($copy['published']); 
		   unset($copy['shared']); 
		   unset($copy['ordering']); 
			$cache[$state_id] = $copy; 
			return $copy; 
	}
	
	
	function mergeArray(&$arr1, $arr2, $index1, $index2='') {
		$arr1 = (array)$arr1; 
		
		foreach ($arr1 as $k=>$v) {
			foreach ($arr2 as $k2=>$v2) {
				if (!empty($index2)) {
					
					
					
					if ($v[$index1] != $v2[$index2]) {
						
						continue; 
					}
				}
				else
				{
					if ($v[$index1] != $v2[$index1]) continue; 
				}
				foreach ($v2 as $k3=>$v3) {
					$arr1[$k][$k3] = $v3; 		
				}
				
			}
			
		}
	}
	function jsonToCols(&$rows, $json_col, $index, $prefix='json_') {
		
		
		$jr = array(); 
		foreach ($rows as $k=>$v) {
			if (!isset($v[$index])) continue; 
			if (!isset($v[$json_col])) continue; 
			$x = json_decode($v[$json_col], true);
			if (!empty($x)) {
				if (is_array($x) || (is_object($x))) {
				foreach ($x as $k4=>$v4) {
					if (!is_array($v4)) {
						$jr[$prefix.$k4] = 1;
					}
					else {
						foreach ($v4 as $k2=>$v2) {
							if (!is_array($v2)) {
								$jr[$prefix.$k4.'_'.$k2] = 1;
							}
							else
							{
								foreach ($v2 as $k3=>$v3) {
									if (!is_array($v3)) {
										$jr[$prefix.$k4.'_'.$k2.'_'.$k3] = 1;
									}
									else
									{
										// more then 3 dimensions... 
									}
								}
							}
						}					  
					}
				}
				}
			}
		}
		
		if (empty($jr)) return; 
		$ret = array(); 
		
		
		
		foreach ($rows as $k=>$vz) {
			
			
			$ret[$vz[$index]] = $rows[$k]; 
			foreach ($jr as $k2=>$vx) {
				$ret[$vz[$index]][$k2] = ''; 
			}
			
			
			$x = @json_decode($vz[$json_col], true);
			if (!empty($x)) {
				if (is_array($x) || (is_object($x))) {
				foreach ($x as $k4=>$v4) {
					if (!is_array($v4)) {
						$ret[$vz[$index]][$prefix.$k4] = $v4;
					}
					else {
						foreach ($v4 as $k2=>$v2) {
							if (!is_array($v2)) {
								$ret[$vz[$index]][$prefix.$k4.'_'.$k2] = $v2;
							}
							else
							{
								foreach ($v2 as $k3=>$v3) {
									if (!is_array($v3)) {
										$ret[$vz[$index]][$prefix.$k4.'_'.$k2.'_'.$k3] = $v3;
									}
									else
									{
										// more then 3 dimensions... 
									}
								}
							}
						}					  
					}
					
					
				}
				}
			}
			
		}
		$rows = $ret; 
		
		
		
	}
	
	// vracia pole so vsetkymi beznymi hodnotami objednavky
	function getOrderData($where='')
	{
		$db = JFactory::getDBO();
		$q = 'select * from #__virtuemart_orders as o ';
		$all = JRequest::getVar('export_all_history', false); 
		if (!empty($all)) {
			$q .= ' left join (select * from #__virtuemart_order_histories order by virtuemart_order_history_id desc ) as h on o.virtuemart_order_id = h.virtuemart_order_id '; 
		}
		$q .= ' left join (select * from #__virtuemart_order_userinfos) as ui on (ui.virtuemart_order_id = o.virtuemart_order_id and ui.address_type="BT") ';
		$q .= $where;
		$q .= ' group by o.virtuemart_order_id order by o.created_on desc limit 99999 ';
		$db->setQuery($q);
		$res = $db->loadAssocList();
		if (!empty($res)) {
			$orders = array(); 
			foreach ($res as $k=>$v) {
				
				$v['virtuemart_order_id'] = (int)$v['virtuemart_order_id'];  
				$orders[$v['virtuemart_order_id']] = $v['virtuemart_order_id']; 
				
			}
			if (!empty($orders)) {
				$q = 'select * from #__onepage_moss as o where order_id IN ('.implode(',', $orders).') group by o.order_id order by timestamp desc ';
				$db->setQuery($q); 
				$res3 = $db->loadAssocList(); 
				if (!empty($res3)) {
					$this->jsonToCols($res3, 'vat_data', 'order_id'); 
					//$this->jsonToCols($res3, 'payment_data', 'order_id'); 
					
					$this->mergeArray($res, $res3, 'virtuemart_order_id', 'order_id'); 	 
				}
			}
		}
		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
		OPCmini::setVMLANG(); 
		
		
		$type = 'shipment'; 
		$q = 'select virtuemart_'.$type.'method_id, '.$type.'_name from `#__virtuemart_'.$type.'methods_'.VMLANG.'` where 1=1 limit 0,1000'; 
		$db->setQuery($q); 
		$shipmentsdata = $db->loadAssocList(); 
		$ships = array(); 
		foreach ($shipmentsdata as $row) {
			$ships[(int)$row['virtuemart_'.$type.'method_id']]  = $row[$type.'_name'];
		}
		
		$type = 'payment'; 
		$q = 'select virtuemart_'.$type.'method_id, '.$type.'_name from `#__virtuemart_'.$type.'methods_'.VMLANG.'` where 1=1 limit 0,1000'; 
		$db->setQuery($q); 
		$paydata = $db->loadAssocList(); 
		$pays = array(); 
		foreach ($paydata as $row) {
			$pays[(int)$row['virtuemart_'.$type.'method_id']]  = $row[$type.'_name'];
		}
		
		foreach ($res as $k=>$row) {
			
			$virtuemart_shipmentmethod_id = (int)$row['virtuemart_shipmentmethod_id']; 
			if (isset($ships[$virtuemart_shipmentmethod_id])) {
				$res[$k]['shipment_name'] = $ships[$virtuemart_shipmentmethod_id]; 
			}
			else {
				$res[$k]['shipment_name'] = ''; 
			}
			
			$virtuemart_paymentmethod_id = (int)$row['virtuemart_paymentmethod_id']; 
			if (isset($ships[$virtuemart_paymentmethod_id])) {
				$res[$k]['payment_name'] = $ships[$virtuemart_paymentmethod_id]; 
			}
			else {
				$res[$k]['payment_name'] = ''; 
			}
			
			if (isset($row['virtuemart_country_id'])) {
			  $country_id = (int)$row['virtuemart_country_id']; 
			  $country_data = $this->getCountry($country_id); 
			 
			  foreach ($country_data as $kkv => $vvv) {
				  $res[$k][$kkv] = $vvv; 
			  }				  
			}
			if (isset($row['virtuemart_state_id'])) {
			  $state_id = (int)$row['virtuemart_state_id']; 
			  $state_data = $this->getVMState($state_id); 
			  foreach ($state_data as $kks => $vvs) {
				  $res[$k][$kks] = $vvs; 
			  }				  
			}
			
		}
		
		if (empty($res)) 
		{
			
			$e = $db->getErrorMsg();
			JFactory::getApplication()->redirect('index.php?option=com_onepage&view=orders', 'No data '.$e); 
			
		}
		
		
		
		return (array)$res;
		
	}
	function getColNames($res, $table, $prefix)
	{
		$names = array(); 
		foreach ($res as $k=>$n) {
			$names[] = '`'.$table.'`.'.'`'.$k.'` as '.$prefix.$k; 
		}
		return (array)$names; 
	}
	// vracia pole so vsetkymi beznymi hodnotami objednavky
	function getOrderDataWithItems($where='')
	{
		$db = JFactory::getDBO();
		
		$h = 'select * from #__virtuemart_order_histories where 1 limit 1'; 
		$db->setQuery($h); 
		$res = $db->loadAssoc(); 
		$h1 = $this->getColNames($res, '#__virtuemart_order_histories', 'order_histories_'); 
		
		$h = 'select * from #__virtuemart_order_userinfos where 1 limit 1'; 
		$db->setQuery($h); 
		$res = $db->loadAssoc(); 
		$h2 = $this->getColNames($res, '#__virtuemart_order_userinfos', 'order_userinfos_' ); 
		$hst = $this->getColNames($res, 'ui3', 'order_userinfos_st_' ); 
		
		$h = 'select * from #__virtuemart_orders where 1 limit 1'; 
		$db->setQuery($h); 
		$res = $db->loadAssoc(); 
		$h3 = $this->getColNames($res, '#__virtuemart_orders', 'orders_' ); 
		
		$all = JRequest::getVar('export_all_history', false); 
		if (!empty($all)) {
			$hq = ', '.implode(',',$h1); 
			$hq2 = ', #__virtuemart_order_histories'; 
		}
		else $hq = $hq1 = ''; 
		
		$q = 'select o.*'.$hq.', '.implode(',', $h2).', '.implode(',', $h3).' from #__virtuemart_order_items as o'.$hq1.', #__virtuemart_order_userinfos, #__virtuemart_orders '; 
		
		if (empty($where)) $where = ' where 1=1 '; 
		$q .= $where;
		$q .= ' and ((#__virtuemart_order_userinfos.virtuemart_order_id = o.virtuemart_order_id) and (#__virtuemart_order_userinfos.address_type = "BT")) and (#__virtuemart_orders.virtuemart_order_id = o.virtuemart_order_id) '; 
		if (!empty($all)) {
			$q .= ' and (#__virtuemart_order_histories.virtuemart_order_id = o.virtuemart_order_id) '; 
		}
		

		$q .= ' order by o.created_on desc '; 
		$q .= ' limit 99999 ';
		
		
		//die(); 
		try {
			$db->setQuery($q);
			$res = $db->loadAssocList();
			
			
			
		}
		catch (Exception $e) {
			JFactory::getApplication()->redirect('index.php?option=com_onepage&view=orders', 'Error loading data '); 
			die(); 
		}
		
		if (!class_exists('VmConfig'))	  
		{
			require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
			VmConfig::loadConfig(); 
		}
		
		
		if (empty($res)) 
		{
			$e = $db->getErrorMsg();
			JFactory::getApplication()->redirect('index.php?option=com_onepage&view=orders', 'No data '.$e); 
		}
		
		
		
		$atr = array(); 
		$orders = array(); 
		foreach ($res as $k=>$v) {
			if ($v['product_attribute'] == '[]') $res[$k]['product_attribute'] = ''; 
			$v['virtuemart_order_id'] = (int)$v['virtuemart_order_id'];  
			$orders[$v['virtuemart_order_id']] = $v['virtuemart_order_id']; 
			
			
			
			
			
			
			
			$res[$k]['product_attribute'] = self::adjustProductAttribute($v['product_attribute']); 
			
			
			
			if (isset($v['order_userinfos_virtuemart_country_id'])) {
			  $country_id = (int)$v['order_userinfos_virtuemart_country_id']; 
			  $country_data = $this->getCountry($country_id); 
			 
			  foreach ($country_data as $kkv => $vvv) {
				  $res[$k][$kkv] = $vvv; 
			  }				  
			}
			if (isset($v['order_userinfos_virtuemart_state_id'])) {
			  $state_id = (int)$v['order_userinfos_virtuemart_state_id']; 
			  $state_data = $this->getVMState($state_id); 
			  foreach ($state_data as $kks => $vvs) {
				  $res[$k][$kks] = $vvs; 
			  }				  
			}
		
		}
		
		    
			$this->jsonToCols($res, 'product_attribute', 'virtuemart_order_item_id', 'atr_'); 
			
			
			
			$q = 'select * from #__virtuemart_order_histories as o where virtuemart_order_id IN ('.implode(',', $orders).') group by o.virtuemart_order_id order by created_on desc ';
			$db->setQuery($q); 
			$res2 = $db->loadAssocList(); 
			if (!empty($res2)) {
				
				
				$this->mergeArray($res, $res2, 'virtuemart_order_id'); 
				
			}
			
			//$this->arrayMerge($res, $res2, 'virtuemart_order_id'); 
			
			$q = 'select * from #__onepage_moss as o where order_id IN ('.implode(',', $orders).') group by o.order_id order by timestamp desc ';
			$db->setQuery($q); 
			$res3 = $db->loadAssocList(); 
			
			
			if (!empty($res3)) {
				$res3 = (array)$res3;
				$this->jsonToCols($res3, 'vat_data', 'order_id'); 
				//$this->jsonToCols($res3, 'payment_data', 'order_id'); 
				
				$this->mergeArray($res, $res3, 'virtuemart_order_id', 'order_id'); 
			}
			
			
			/*
	foreach ($res as $k2=>$v2) {
	foreach ($res2 as $k=>$v) {
			if ($v2['virtuemart_order_id'] != $v['virtuemart_order_id']) continue; 
			foreach ($v as $k3=>$v3) {
			$res[$k2][$k3] = $v3; 
			}
	}
	
	$t = @json_decode($v2['product_attribute'], true); 
	foreach ($atr as $k=>$v) {
		$res[$k2][$k] = ''; 
		
		
		if (!empty($t))
		{
			
			
		foreach ($t as $k=>$v) {
			
			
			if (!is_array($v)) {
			$res[$k2]['atr_'.$k] =  $v;
			}
			else {
				foreach ($v as $k5=>$v5) {
					$res[$k2]['atr_'.$k.'_'.$k5] =  $v5;
				}
			}
		}
		}
	}
	}
	
	
	
	*/
			

			return (array)$res;
			
		}
	public static function adjustProductAttribute($product_attribute, $returnNamedOnly=false) {
		
		$only_named = array(); 
		$db = JFactory::getDBO(); 
		
		if (defined('VM_VERSION') && (VM_VERSION >= 3))  {
				
				$t = @json_decode($product_attribute, true); 
				if (!empty($t)) {
					
					
				
		
		
		foreach ($t as $kX=>$vX) {
						
						
						$q = 'select custom_title from #__virtuemart_customs where virtuemart_custom_id = '.$kX; 
						$db->setQuery($q); 
						$custom_name = $db->loadResult(); 
						
						
						
						
						if (is_array($vX)) {
							foreach ($vX as $kX4=>$vX4) {
								
								if (!is_numeric($kX4)) continue; 
								//$q = 'select custom_title from #__virtuemart_customs where virtuemart_custom_id = '.$kX4; 
								
								$q = 'select customfield_value from #__virtuemart_product_customfields where virtuemart_customfield_id = '.$kX4; 
								
								$db->setQuery($q); 
								$custom_vl = $db->loadResult(); 
								
								if (is_numeric($vX4)) {
									$q = 'select customfield_value from #__virtuemart_product_customfields where virtuemart_customfield_id = '.$vX4; 
									$db->setQuery($q); 
									$custom_vl = $db->loadResult(); 
								}
								else {
									
									if (!empty($vX4))
									$custom_vl = $vX4;   
								}
								
							}
						}
						else {
							
							if (is_numeric($vX)) {
								$q = 'select customfield_value from #__virtuemart_product_customfields where virtuemart_customfield_id = '.$vX; 
								$db->setQuery($q); 
								$custom_vl = $db->loadResult(); 
							}
							else {
								$custom_vl = $vX; 
							}
						}
						$custom_name = JText::_($custom_name); 
						if (is_string($custom_vl)) { 
						   $t[$custom_name] = JText::_($custom_vl); 
						   $only_named[$custom_name] = JText::_($custom_vl); 
						}
						else {
						   $t[$custom_name] = $custom_vl; 
						   $only_named[$custom_name] = $custom_vl; 
						}
						
						
					}
					if (!empty($returnNamedOnly)) return json_encode($only_named); 
					return json_encode($t); 
					
					}
					
			} else {
				
				$js = @json_decode($product_attribute, true); 
				$db = JFactory::getDBO(); 
				if (!empty($js))
				foreach ($js as $custom_field_id => $val) {
					
					if (!defined('VM_VERSION') || (VM_VERSION < 3))
					{
						$custom_field_col = 'custom_value'; 
					}
					
					$q = 'select `'.$custom_field_col.'` from #__virtuemart_product_customfields where virtuemart_customfield_id = '.(int)$custom_field_id; 
					$db->setQuery($q); 
					$custom_vl = $db->loadResult(); 
					
					
					$q = 'select * from #__virtuemart_product_customfields where virtuemart_customfield_id = '.(int)$custom_field_id; 
					$db->setQuery($q); 
					$test = $db->loadAssoc(); 
					
					$virtuemart_custom_id = $test['virtuemart_custom_id']; 
					$q = 'select custom_title from #__virtuemart_customs where virtuemart_custom_id = '.$virtuemart_custom_id; 
					$db->setQuery($q); 
					$custom_name = $db->loadResult(); 
					
					//OR
					/*
					$q = 'select custom_title from #__virtuemart_customs where virtuemart_custom_id = '.$custom_field_id; 
					$db->setQuery($q); 
					$custom_name = $db->loadResult(); 
					*/
					
					
					
					
					if (empty($custom_vl)) {
					$val = trim($val); 
					$e = explode('/span>', $val); 
					if (isset($e[1])) {
						$ee = $e[1].'/span>'; 
						$ee = strip_tags($ee); 
						$custom_vl = trim($ee); 
						
						
					}
					}
					
					if (empty($custom_vl)) $custom_vl = $val; 
					$custom_name = JText::_($custom_name); 
					//$js[$custom_name] = JText::_($custom_vl); 
					//$only_named[$custom_name] = JText::_($custom_vl); 
					
					if (is_string($custom_vl)) { 
						   $js[$custom_name] = JText::_($custom_vl); 
						   $only_named[$custom_name] = JText::_($custom_vl); 
						}
						else {
						   $js[$custom_name] = $custom_vl; 
						   $only_named[$custom_name] = $custom_vl; 
						}
						
					
				}
				return json_encode($js); 
			}
			
			return $product_attribute; 
					
	}
		
		
		function getList()
		{
			$db = JFactory::getDBO();

			$list  = "SELECT * FROM #__users AS u LEFT JOIN jos_vm_user_info AS ui ON u.id=ui.user_id"
			." WHERE "
			." ui.perms = 'shopper' "
			." ORDER BY user_id ASC ";

			$db->setQuery($list);
			//echo $list;
			$arr = $db->loadAssocList();
			$users = array();
			foreach ($arr as $row)
			{
				$user = array();
				foreach ($row as $key=>$val)
				{
					$user[$key] = $val;
				} 
				$users[] = $user;

			}
			//var_dump($users);
			//die();
			//$i = 0;
			return (array)$users;


		}

		
	}