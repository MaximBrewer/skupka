<?php
defined('_JEXEC') or 	die( 'Direct Access to ' . basename( __FILE__ ) . ' is not allowed.' ) ;
/**
 *
 * @author SM planet - smplanet.net
 * @package VirtueMart
 * @subpackage custom
 * @copyright Copyright (C) 2012-2014 SM planet - smplanet.net. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 **/
if (!class_exists('plgVmCustomCatproduct')) require(JPATH_PLUGINS . DS . 'vmcustom' . DS . 'catproduct' . DS . 'catproduct.php');

class CatproductFunction extends plgVmCustomCatproduct {
	function __construct() {
		
	}
	// functions for backend
	function getArrayForSorting () {
		$sorting = array(
			array("value" => "default", "text" => "Default"),
			array("value" => "sortid", "text" => "Product ID"),
			array("value" => "sortsku", "text" => "Product SKU"),
			array("value" => "sortname", "text" => "Product Name"),
			array("value" => "sortweight", "text" => "Product Weight"),
			array("value" => "sortlength", "text" => "Product Length"),
			array("value" => "sortwidth", "text" => "Product Width"),
			array("value" => "sortheight", "text" => "Product Height"),
			array("value" => "sortprice", "text" => "Product Price")
			);
		return $sorting;
	}
	function getArrayForAttachedSelect () {
		$field_attached = array(
			array("value" => "id", "text" => "Product ID"),
			array("value" => "sku", "text" => "Product SKU"),
			array("value" => "category", "text" => "Category ID"),
			array("value" => "featured", "text" => "Featured products"),
			array("value" => "recent", "text" => "Recent products"),
			array("value" => "topten", "text" => "Top ten products"),
			array("value" => "random", "text" => "Random products"),
			array("value" => "related", "text" => "Related products")
		//	array("value" => "manufactor", "text" => "Manufactor ID")
			);
		return $field_attached;
	}
	function getFilesInFolder ($directory = '') {
		$layout_f = array ();
		if (empty($directory)) return $layout_f;

		$layouts = glob($directory . "*.php");
		
		$i = 0;
		foreach($layouts as $layout)
		{
			$layout = str_replace($directory, "", $layout);
			$layout_f[$i]["value"] = $layout;
			$layout_f[$i]["text"] = $layout;
			$i++;
		}
		return $layout_f;
	}
	function getHideaddtocart () {
		$hideaddtocart = array(
			array("value" => "css", "text" => "Hide with css (may hide button also on page without catproduct)"),
			array("value" => "js", "text" => "Hide with JavaScript (may not work on some templates)"),
			array("value" => "no", "text" => "Don't hide original addtocart button")
			);
		return $hideaddtocart;
	}
	function getHandlePlugins () {
		$handle_plugins = array(
			array("value" => "0", "text" => "Don't handle plug-ins (Additional custom-fields are not shown)"),
			array("value" => "1", "text" => "Custom-fields for each product (Custom-fields are shown in row, can be different for each product)"),
			//array("value" => "2", "text" => "Shows Custom-fields at the end of table (Comes only from parent product, same for all products, even attached one! they are added to all products even if they don't have this custom-field!)")
			);
		return $handle_plugins;
	}
	// functions for backend - END
	function checkShowPrices () {
		if (!class_exists ('CurrencyDisplay')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'currencydisplay.php');
		}
		$currency = CurrencyDisplay::getInstance ();
		
		$userModel = VmModel::getModel('user');
		$user = $userModel->getCurrentUser();
		$shopperModel = VmModel::getModel('shoppergroup');
		
		if(count($user->shopper_groups)>0){
			$shopperModel->setID($user->shopper_groups[0]);
			$sprgrp = $shopperModel->getShopperGroup($user->shopper_groups[0]);
		} else {
			//This Fallback is not tested
			$sprgrp = $shopperModel->getDefault($user->JUser->guest);
		}
		if($sprgrp){
			if($sprgrp->custom_price_display){
				if(isset($sprgrp->show_prices) && $sprgrp->show_prices){
					return true;
				}
			} else {
				if(VmConfig::get('show_prices', 1)){
					return true;
				}
			}
		}
		return false;
	}
	
	function CP_addParent ($parent_product, $parametri, &$i, &$products) {
		if (isset($parametri["add_parent_to_table"]) && $parametri["add_parent_to_table"] == 1) {
			// get whole product
			$last_group = 'parent';
			if (isset($parametri["layout_field_parent"]) && $parametri["layout_field_parent"] != '') {
				$products[$last_group]['params']['layout'] = $parametri["layout_field_parent"];
				$products[$last_group]['params']['layout'] = $parametri["layout_field_parent"];
			} else $products[$last_group]['params']['layout'] = 'default.php';
			if (isset($parametri["def_qty_parent"]) && $parametri["def_qty_parent"] != '') {
				$products[$last_group]['params']['def_qty'] = $parametri["def_qty_parent"];
			} else $products[$last_group]['params']['def_qty'] = '1';
			if (isset($parametri["show_qty_parent"]) && $parametri["show_qty_parent"] != '') {
				$products[$last_group]['params']['show_qty'] = $parametri["show_qty_parent"];
			} else $products[$last_group]['params']['show_qty'] = '1';
			// check if show prices 
			/*if (CatproductFunction::checkShowPrices()) $products[$last_group]['params']['show_prices'] = 1;
			else $products[$last_group]['params']['show_prices'] = 0;*/
			
			$products[$last_group][$i] = $parent_product;
			//var_dump($parent_product['child']['product_name']);
			if (isset($parent_product['child']['product_name'])) {
				$products[$last_group]['params']['group_title'] = $parent_product['child']['product_name'];
			} else $products[$last_group]['params']['group_title'] = 'Parent';
			$products[$last_group]['params']['group_image'] = $parent_product['images'];
			$last_group = 'parent';
			$i++;
		}	
	}
	
	function CP_getChildren ($uncatChildren, $parametri, $product, $use_min_max, $use_box_quantity, &$groupid, &$i, &$products) {
		// get children
		if (count($uncatChildren) < 1) return;
		if (!isset($parametri["do_not_show_child"]) || $parametri["do_not_show_child"] <> "1") {
			$productModel = VmModel::getModel ('product');
			foreach ($uncatChildren as $child) {
				if($child <> $product->virtuemart_product_id) {
					// get whole product
					$product_temp = $this->getCatproductProduct($child, $product, $use_min_max, $use_box_quantity);
					
					// check if product has children
					$child_chilren = $productModel->getProductChildIds($child);
					if ($child_chilren) {
						$last_group = 'subchildren'.$groupid;
						$products['subchildren'.$groupid]['params']['group_title'] = $product_temp['child']['product_name'];
						$products['subchildren'.$groupid]['params']['group_image'] = $product_temp['images'];
						if (isset($parametri["layout_field_children"]) && $parametri["layout_field_children"] != '') {
							$products[$last_group]['params']['layout'] = $parametri["layout_field_children"];
						} else $products[$last_group]['params']['layout'] = 'default.php';
						if (isset($parametri["def_qty_children"]) && $parametri["def_qty_children"] != '') {
							$products[$last_group]['params']['def_qty'] = $parametri["def_qty_children"];
						} else $products[$last_group]['params']['def_qty'] = '0';
						if (isset($parametri["show_qty_children"]) && $parametri["show_qty_children"] != '') {
							$products[$last_group]['params']['show_qty'] = $parametri["show_qty_children"];
						} else $products[$last_group]['params']['show_qty'] = '1';
						// check if show prices 
						/*if (CatproductFunction::checkShowPrices()) $products[$last_group]['params']['show_prices'] = 1;
						else $products[$last_group]['params']['show_prices'] = 0;*/
						
						//$products['child1'][$i]['child']['catproduct_inline_row'] = "groupstart";
						//$i++;
						//var_dump($child_chilren);
						foreach ($child_chilren as $child_child) {
							$products['subchildren'.$groupid][$i] = $this->getCatproductProduct($child_child, $product, $use_min_max, $use_box_quantity);
							$last_group = 'subchildren'.$groupid;
							if ($products[$last_group][$i]) {
								$i++;
							} else {
								unset($products[$last_group][$i]);
							}
							
						}
						if(isset($parametri["sorting_field"]) && $parametri["sorting_field"] <> 'default') {
							$sortingfield = $parametri["sorting_field"];
							usort($products[$last_group], Array("CatproductFunction",$sortingfield));
						}	
						$groupid++;
						//$products[$i]['child']['catproduct_inline_row'] = "groupend";
					}
					else {
						//$products['children2'][$i]['child']['catproduct_inline_row'] = "groupstart";
						// $i++;
						$last_group = 'children';
						if (!isset($products[$last_group])) $products[$last_group] = array(); 
						$products[$last_group]['params']['group_title'] = $product->product_name; 
						if (isset($parametri["layout_field_children"]) && $parametri["layout_field_children"] != '') {
							$products[$last_group]['params']['layout'] = $parametri["layout_field_children"];
						} else $products[$last_group]['params']['layout'] = 'default.php';
						if (isset($parametri["def_qty_children"]) && $parametri["def_qty_children"] <> '') {
							$products[$last_group]['params']['def_qty'] = $parametri["def_qty_children"];
						} else $products[$last_group]['params']['def_qty'] = '0';
						if (isset($parametri["show_qty_children"]) && $parametri["show_qty_children"] != '') {
							$products[$last_group]['params']['show_qty'] = $parametri["show_qty_children"];
						} else $products[$last_group]['params']['show_qty'] = '1';
						// check if show prices 
						/*if (CatproductFunction::checkShowPrices()) $products[$last_group]['params']['show_prices'] = 1;
						else $products[$last_group]['params']['show_prices'] = 0;*/
						
						//array_push($products[$last_group],$product_temp);
						$products[$last_group][$i] = array();
						$products[$last_group][$i] = $product_temp;
						//$products[$last_group][$i][] = $product_temp['child']['customfieldsSorted']['addtocart'][0];
						//$products['customfields']
						//$products[$last_group][$i]['child']['customfieldsSorted'] =  $this->getCustomfields($products[$last_group][$i]['child']);
						//var_dump($products[$last_group][1][0]); //['child']['customfieldsSorted']);
						//var_dump($products[$last_group][1]['customfields']);
						if ($products[$last_group][$i]) {
							$i++;
						} else {
							unset($products[$last_group][$i]);
						}
					}
					//$products[$i]['child']['catproduct_inline_row'] = $parametri["title_for_attached_products"];
				}
			}
			//var_dump($products[$last_group]);
			//sort children if sording <> default
			if(isset($parametri["sorting_field"]) && $parametri["sorting_field"] <> 'default') {
				$sortingfield = $parametri["sorting_field"];
				usort($products[$last_group], Array("CatproductFunction",$sortingfield));
			}	
		}
	}
	
	function CP_getAttached ($parametri, $product, $use_min_max, $use_box_quantity, &$groupid, &$i, &$products, $original_product) {
		$productModel = VmModel::getModel ('product');
		if(isset($parametri["enable_attached_products_array"])) {
			$enable_attached_products_array = (array) $parametri["enable_attached_products_array"];
			$enable_title_for_attached_array = (isset($parametri["enable_title_for_attached_array"])) ? (array) $parametri["enable_title_for_attached_array"] : array();
			$title_for_attached_products_array = (isset($parametri["title_for_attached_products_array"])) ? (array) $parametri["title_for_attached_products_array"] : array();
			$id_sku_for_attached_products_array = (isset($parametri["id_sku_for_attached_products_array"])) ? (array) $parametri["id_sku_for_attached_products_array"] : array();
			$attached_products_array = (isset($parametri["attached_products_array"])) ? (array) $parametri["attached_products_array"] : array();
			$attached_products_layout_array = (isset($parametri["attached_products_layout_array"])) ? (array) $parametri["attached_products_layout_array"] : array();
			$attached_products_def_qty_array = (isset($parametri["attached_products_def_qty_array"])) ? (array) $parametri["attached_products_def_qty_array"] : array();
			$attached_products_show_qty_array = (isset($parametri["attached_products_show_qty_array"])) ? (array) $parametri["attached_products_show_qty_array"] : array();
			foreach ($enable_attached_products_array as $enable_attached) {
				if ($enable_attached == 1) {
					$id_sku = current($id_sku_for_attached_products_array);
					$title_for_attached = current($title_for_attached_products_array);
					$attached_products = current($attached_products_array);
					$enable_title = current($enable_title_for_attached_array);
					$attached_products_layout = current($attached_products_layout_array);
					$attached_products_def_qty = current($attached_products_def_qty_array);
					$attached_products_show_qty = current($attached_products_show_qty_array);
					
					$pripeti_artikli_k = array();
					$no_product = 0;
					$product_number = 50;
					/*$find_id = 0;
					
					if (isset($id_sku)) {
						if ($id_sku == 'sku') {
							$db =JFactory::getDBO();
							$find_id = 1;
							$ids=0;
						} else if ($id_sku == 'noprod'){
							$no_product = 1;
						}
					}*/
					
					$last_group = 'group'.$groupid;
					// get ids of attached
					$pripeti_artikli = $attached_products;
					if (!empty($pripeti_artikli) || $id_sku == "featured" || $id_sku == "recent" || $id_sku == "topten" || $id_sku == "random" || $id_sku == "related") {
						if (isset($attached_products_layout) && $attached_products_layout <> '') {
							$products[$last_group]['params']['layout'] = $attached_products_layout;
						} else {
							$products[$last_group]['params']['layout'] = 'multi-default.php';
						}
						if (isset($attached_products_def_qty) && $attached_products_def_qty <> '') {
							$products[$last_group]['params']['def_qty'] = $attached_products_def_qty;
						} else {
							$products[$last_group]['params']['def_qty'] = '1';
						}
						if (isset($attached_products_show_qty) && $attached_products_show_qty <> '') {
							$products[$last_group]['params']['show_qty'] = $attached_products_show_qty;
						} else {
							$products[$last_group]['params']['show_qty'] = '1';
						}
						// check if show prices 
						/*if (CatproductFunction::checkShowPrices()) $products[$last_group]['params']['show_prices'] = 1;
						else $products[$last_group]['params']['show_prices'] = 0;*/
						$pripeti_artikli = explode(",",$pripeti_artikli);
						// if custom title is enabled
						$products[$last_group]['params']['group_title'] = ' ';
						if ($enable_title == 1) {
							// get title
							//$products[$last_group][$i]['child']['catproduct_inline_row'] = $title_for_attached;
							$products[$last_group]['params']['group_title'] = JText::_($title_for_attached);
							$i++;
						}
						
						/*	
				
				$product_number = 10;*/
				//$products_ids = $productModel->sortSearchListQuery (TRUE, FALSE, 'topten', $product_number);
						if ($no_product <> 1) {
							switch ($id_sku) {
								case "category":
									if ($pripeti_artikli != '' && count($pripeti_artikli) > 0) {
										$categories = "";
										foreach ($pripeti_artikli as $category_id) {
											$categories .= ','.$category_id;
											$categories .= CatproductFunction::getChildCategories($category_id); 
										}
										// to array
										$categories = explode(',',$categories);
										// only unique
										$categories = array_unique($categories);
									}
									if ($categories != '' && count($categories) > 0) {
										foreach ($categories as $category) {
											if ($category <> '') {
												$products_ids = $productModel->sortSearchListQuery(true, $category, false, $product_number);
												foreach ($products_ids as $productid) {
													$product_ids .= ','.$productid;
												}
											}
										}
									}
									// ids to array
									$product_ids = explode(',',$product_ids);
									// remove duplicates
									$product_ids = array_unique($product_ids);
									// remove empty values
									$product_ids = array_filter($product_ids);
									if ($product_ids) {
										$pripeti_artikli_k = $product_ids;
									}
									break;
								case "featured":
									if (!empty($attached_products)) $product_number = $attached_products;
									$products_ids = $productModel->sortSearchListQuery (TRUE, FALSE, 'featured', $product_number);
									if ($products_ids) {
										$pripeti_artikli_k = $products_ids;
									}
									break;
								case "recent":
									if (!empty($attached_products)) $product_number = $attached_products;
									$products_ids = $productModel->sortSearchListQuery (TRUE, FALSE, 'recent', $product_number);
									if ($products_ids) {
										$pripeti_artikli_k = $products_ids;
									}
									break;
								case "topten":
									if (!empty($attached_products)) $product_number = $attached_products;
									$products_ids = $productModel->sortSearchListQuery (TRUE, FALSE, 'topten', $product_number);
									if ($products_ids) {
										$pripeti_artikli_k = $products_ids;
									}
									break;
								case "random":
									if (!empty($attached_products)) $product_number = $attached_products;
									$products_ids = $productModel->sortSearchListQuery (TRUE, FALSE, 'random', $product_number);
									if ($products_ids) {
										$pripeti_artikli_k = $products_ids;
									}
									break;
								case "related":
									$db =JFactory::getDBO();
									$ids=0;
									$q = 'SELECT pc.`customfield_value` as id FROM `#__virtuemart_product_customfields` as pc 
										LEFT JOIN `#__virtuemart_customs` as c
										ON pc.`virtuemart_custom_id` = c.`virtuemart_custom_id` 
										WHERE c.`field_type` = "R" 
										AND pc.`virtuemart_product_id` = '.$original_product->virtuemart_product_id;
										$db->setQuery ($q);
										$prodids = $db->loadObjectList();
										foreach ($prodids as $prodid) {
											$pripeti_artikli_k[$ids] = $prodid->id;
											$ids++;
										}
									break;
								case "manufactor":
									break;
								case "sku":
									$db =JFactory::getDBO();
									$ids=0;
									foreach ($pripeti_artikli as $pripet_artikel) {
										$q = 'SELECT `virtuemart_product_id` as id FROM `#__virtuemart_products` WHERE `published`=1
										AND `product_sku`= "' . $pripet_artikel . '"';
										$db->setQuery ($q);
										$prodids = $db->loadObjectList();
										foreach ($prodids as $prodid) {
											$pripeti_artikli_k[$ids] = $prodid->id;
											$ids++;
										}
									}
									break;
								case "id":
									$pripeti_artikli_k = $pripeti_artikli;
									break;
								case "noprod":
									$no_product = 1;
									break;
								default:
									continue;
							}
							/*if ($find_id == 1) {
								foreach ($pripeti_artikli as $pripet_artikel) {
									$q = 'SELECT `virtuemart_product_id` as id FROM `#__virtuemart_products` WHERE `published`=1
									AND `product_sku`= "' . $pripet_artikel . '"';
									$db->setQuery ($q);
									$prodids = $db->loadObjectList();
									foreach ($prodids as $prodid) {
										$pripeti_artikli_k[$ids] = $prodid->id;
										$ids++;
									}
								}
							}
							else {
								$pripeti_artikli_k = $pripeti_artikli;
							}*/
							// get attached products
							$no_attached = 0;
							foreach ($pripeti_artikli_k as $pripet_artikel) {
								// get whole product
								$products[$last_group][$i] = $this->getCatproductProduct($pripet_artikel, $product, $use_min_max, $use_box_quantity);
								if ($products[$last_group][$i]) {
									if ($no_attached == 0 && isset($products[$last_group][$i]['images'])) {
										$products[$last_group]['params']['group_image'] = $products[$last_group][$i]['images'];
										$no_attached = 1;
									}
									$i++;
								} else {
									unset($products[$last_group][$i]);
								}								
							}
						}
						else {
							foreach ($pripeti_artikli as $someadds) {
								$products[$last_group][$i]['child']['someadds'] = $someadds;
								$i++;
							}
						}
					}
				}
				$groupid++;
				next($id_sku_for_attached_products_array);
				next($title_for_attached_products_array);
				next($attached_products_array);
				next($enable_title_for_attached_array);
				next($attached_products_layout_array);
				next($attached_products_def_qty_array);
				next($attached_products_show_qty_array);
			}
		}
	}
	
	private static function sortid($a, $b) {
		if (!isset($a['child'])) return 1;
		if (!isset($b['child'])) return 1;
		$a = $a['child']['virtuemart_product_id'];
		$b = $b['child']['virtuemart_product_id'];
		if ($a == $b) return 0;
		return ($a < $b) ? -1 : 1;
   }
   private static function sortsku($a, $b) {
		if (!isset($a['child'])) return 1;
		if (!isset($b['child'])) return 1;
		$a = $a['child']['product_sku'];
		$b = $b['child']['product_sku'];
		if ($a == $b) return 0;
		return ($a < $b) ? -1 : 1;
   }
   private static function sortname($a, $b) {
		if (!isset($a['child'])) return 1;
		if (!isset($b['child'])) return 1;
		$a = $a['child']['product_name'];
		$b = $b['child']['product_name'];
		if ($a == $b) return 0;
		return ($a < $b) ? -1 : 1;
   }
   private static function sortweight($a, $b) {
		if (!isset($a['child'])) return 1;
		if (!isset($b['child'])) return 1;
		$a = $a['child']['product_weight'];
		$b = $b['child']['product_weight'];
		if ($a == $b) return 0;
		return ($a < $b) ? -1 : 1;
   }
   private static function sortlength($a, $b) {
		if (!isset($a['child'])) return 1;
		if (!isset($b['child'])) return 1;
		$a = $a['child']['product_length'];
		$b = $b['child']['product_length'];
		if ($a == $b) return 0;
		return ($a < $b) ? -1 : 1;
   }
   private static function sortwidth($a, $b) {
		if (!isset($a['child'])) return 1;
		if (!isset($b['child'])) return 1;
		$a = $a['child']['product_width'];
		$b = $b['child']['product_width'];
		if ($a == $b) return 0;
		return ($a < $b) ? -1 : 1;
   }
   private static function sortheight($a, $b) {
		if (!isset($a['child'])) return 1;
		if (!isset($b['child'])) return 1;
		$a = $a['child']['product_height'];
		$b = $b['child']['product_height'];
		if ($a == $b) return 0;
		return ($a < $b) ? -1 : 1;
   }
   private static function sortprice($a, $b) {
		if (!isset($a['child'])) return 1;
		if (!isset($b['child'])) return 1;
		$a = $a['prices']['salesPrice'];
		$b = $b['prices']['salesPrice'];
		if ($a == $b) return 0;
		return ($a < $b) ? -1 : 1;
   }
   
   
   function max_min_box_quantity ($product1,$use_min_max,$use_box_quantity) {
		$quantityp = Array ();
		$quantity_param = $product1['product_params'];
		if ($product1['product_params'] <> null) {
		$quantity_param = explode("|",$quantity_param);
		$min_order_level = explode("=",$quantity_param[0]);
		$max_order_level = explode("=",$quantity_param[1]);
		$product_box = explode("=",$quantity_param[2]);
		$product_box1 = explode("=",$quantity_param[3]);
		if ($use_min_max <> 0) {
			$quantityp["min"] = $min_order_level[1];
			$quantityp["max"] = $max_order_level[1];
		}
		else {
			$quantityp["min"] = 0;
			$quantityp["max"] = 0;
		}
		if ($use_box_quantity <> 0) {
			$quantityp["box"] = $product_box[1];
			if ($quantityp["box"] == "null" || $quantityp["box"] == '""' || $quantityp["box"] == "NULL") $quantityp["box"] = $product_box1[1];
			if ($quantityp["box"] == "null" || $quantityp["box"] == '""' || $quantityp["box"] == "NULL") $quantityp["box"] = 0;
		}
		else {
			$quantityp["box"] = 0;
		}
		} else {
			$quantityp["min"] = 0;
			$quantityp["max"] = 0;
			$quantityp["box"] = 0;
		}
		return $quantityp;
   }
   
   function getGlobalParameters ($parameters = '') {
		if ($parameters == '') return false;
		$global_params = Array();
		foreach(explode('|', $parameters) as $item){
			$item = explode('=',$item);
			$key = $item[0];
			unset($item[0]);
			$item = implode('=',$item);
			if(!empty($item)){
				$global_params[$key] = json_decode($item);
			}
		}
		return $global_params;
   }
   
   function getCurrencyCP() {
   		$currency_cp = 0;
		if (!class_exists ('CurrencyDisplay')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'currencydisplay.php');
		}
		$currency = CurrencyDisplay::getInstance ();

   		/*if(!defined('VM_VERSION') or VM_VERSION < 3){
			$currency_cp = $currency->getPositiveFormat();
		} else {*/
			$currency_id = $currency->getCurrencyForDisplay();
			$db = JFactory::getDBO ();
			$q = 'SELECT *  FROM `#__virtuemart_currencies` AS c
			WHERE c.virtuemart_currency_id = '.$currency_id;
			$db->setQuery ($q);
			$currency_cp = $db->loadObject ();
		//}

		return $currency_cp;
	}
	
	function getChildCategories ($category_id){
		
		$categories = CatproductFunction::getCategories1(true,$category_id);
		$categories1 = '';
		if (count($categories) > 0) {
			foreach ($categories as $category) {
				$categories1 .= $category->virtuemart_category_id.',';
				$categories1 .= CatproductFunction::getChildCategories($category->virtuemart_category_id);
			}
		}

		if (isset($categories1->virtuemart_category_id)) return $categories1->virtuemart_category_id;
		else if (count($categories1) > 0 && !isset($categories1->virtuemart_category_id)) return $categories1;
		else return '';
	}
	
	
	public function getCategories1($onlyPublished = true, $parentId = false, $keyword = "") {
		$category_model = VmModel::getModel('category');
		$vendorId = 1;

		$select = ' c.`virtuemart_category_id`, l.`category_description`, l.`category_name`, c.`ordering`, c.`published`, cx.`category_child_id`, cx.`category_parent_id`, c.`shared` ';

		$joinedTables = ' FROM `#__virtuemart_categories_'.VMLANG.'` l
				  JOIN `#__virtuemart_categories` AS c using (`virtuemart_category_id`)
				  LEFT JOIN `#__virtuemart_category_categories` AS cx
				  ON l.`virtuemart_category_id` = cx.`category_child_id` ';

		$where = array();

		if( $onlyPublished ) {
			$where[] = " c.`published` = 1 ";
		}
		if( $parentId !== false ){
			$where[] = ' cx.`category_parent_id` = '. (int)$parentId;
		}


		/*if(!class_exists('Permissions')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'permissions.php');
		if( !Permissions::getInstance()->check('admin') ){
			$where[] = ' (c.`virtuemart_vendor_id` = "'. (int)$vendorId. '" OR c.`shared` = "1") ';
		}*/

		$whereString = '';
		if (count($where) > 0){
			$whereString = ' WHERE '.implode(' AND ', $where) ;
		} else {
			$whereString = 'WHERE 1 ';
		}

		//$ordering = $this->_getOrdering();
		$ordering = " ORDER BY category_name ASC";
		$category_tree = $category_model->exeSortSearchListQuery(0,$select,$joinedTables,$whereString,'',$ordering, '', 100 );
		return $category_tree;

	}
	
	public function roundForDisplay($price, $currencyId=0,$quantity = 1.0,$inToShopCurrency = false,$nb= -1){
		if (!class_exists ('CurrencyDisplay')) require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'currencydisplay.php');
		$currency = CurrencyDisplay::getInstance(); 
		if(!defined('VM_VERSION') or VM_VERSION < 3){	
			$currencyId = $currency->getCurrencyForDisplay($currencyId);
			if($nb==-1){
				$nb = $currency->getNbrDecimals();
			}

			$price = (float)$price * (float)$quantity;

			$price = $currency->convertCurrencyTo($currencyId,$price,$inToShopCurrency);

			/*if($currency->_numeric_code===756 and VmConfig::get('rappenrundung',FALSE)=="1"){
				$price = round((float)$price * 2,1) * 0.5;
			} else {
				$price = round($price,$nb);
			}*/
			$price = round($price,$nb);
		} else {
			$price = $currency->roundForDisplay((float)$price * (float)$quantity);
		}
		return $price;
	}
	public function createPriceDiv($name,$description,$product_price,$priceOnly=false,$switchSequel=false,$quantity = 1.0,$forceNoLabel=false){
		if (!class_exists ('CurrencyDisplay')) require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'currencydisplay.php');
		$currency = CurrencyDisplay::getInstance(); 
		return $currency->createPriceDiv ($name,$description,$product_price,$priceOnly,$switchSequel,$quantity,$forceNoLabel);
	}
	public function getSymbol() {
		if (!class_exists ('CurrencyDisplay')) require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'currencydisplay.php');
		$currency = CurrencyDisplay::getInstance(); 
		return($currency->getSymbol());
	}
}