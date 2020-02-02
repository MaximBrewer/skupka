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

if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 

class OPCAddToCartAsLink {
  public static function addtocartaslink(&$ref)
 {
	 require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
	 OPCmini::storeReqState(array('new_quantity', 'new_virtuemart_product_id', 'quantity', 'virtuemart_product_id', 'cart_virtuemart_product_id', 'customProductData')); 
    $c = get_class($ref->cart); 

	
    if ($c != 'VirtuemartCart')
	{
	   $ref->cart = VirtuemartCart::getCart(); 
	   
	   
	}
	
	
   
	include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 

	$rememberhtml = ''; 
 
    $rp = JRequest::getVar('randomproduct', 0); 
	if (!empty($rp))
	 {
		if (OPCloader::checkOPCSecret())
		{
	      $opc_link_type = 1; 
	      $qer = 'select virtuemart_product_id from #__virtuemart_products where published=1 limit 1'; 
		  $db=JFactory::getDBO(); 
		  $db->setQuery($qer); 
		  $temp_id = $db->loadResult();
		  JRequest::setVar('add_id', $temp_id);
		  
		}
	 }
	if (empty($opc_link_type)) return OPCmini::loadReqState(array('new_quantity', 'new_virtuemart_product_id', 'quantity', 'virtuemart_product_id', 'cart_virtuemart_product_id', 'customProductData'));  
    $p_id = JRequest::getVar('add_id', '');
    if (empty($p_id)) return OPCmini::loadReqState(array('new_quantity', 'new_virtuemart_product_id', 'quantity', 'virtuemart_product_id', 'cart_virtuemart_product_id', 'customProductData')); ;

	$db = JFactory::getDBO(); 
	
   if (!isset($ref->cart->order_number)) $ref->cart->order_number = ''; 
if (!empty($p_id))
{

$qq = array(); 

if (is_array($p_id))
{

foreach ($p_id as $i=>$item)
{
	
if (!is_numeric($p_id[$i])) break;
	 
	 $qer = 'select `virtuemart_product_id` from `#__virtuemart_products` where `virtuemart_product_id` = '.(int)$item.' limit 0,1'; 
	 $db->setQuery($qer); 
	 $product_id = $db->loadResult(); 
	 if (empty($product_id)) continue; 

$q = JRequest::getVar('qadd_'.$p_id[$i], 1); 

if (!is_numeric($q)) break;

$rememberhtml .= '<input type="hidden" name="qadd_'.$p_id[$i].'" value="'.$q.'" />'; 
$rememberhtml .= '<input type="hidden" name="add_id['.$i.']" value="'.$p_id[$i].'" />'; 

$q = (float)$q;
$qq[$p_id[$i]] = $q;

}

}
else
{
// you can use /index.php?option=com_virtuemart&page=shop.cart&add_id=10&quadd=1;
// to add two products (ids: 10 and 11) of two quantity each (quadd_11=2 for product id 11 set quantity 2)
// OR /index.php?option=com_virtuemart&page=shop.cart&add_id[]=10&quadd_10=2&add_id[]=11&qadd_11=2

$q = JRequest::getVar('qadd_'.$p_id, 1); 
$rememberhtml .= '<input type="hidden" name="qadd_'.$p_id.'" value="'.$q.'" />'; 
$rememberhtml .= '<input type="hidden" name="add_id" value="'.$p_id.'" />'; 

$q = (float)$q;
$q2 = JRequest::getVar('qadd', 1);
//$rememberhtml .= '<input type="hidden" name="qadd" value="'.$q2.'" />'; 
if (!is_numeric($p_id)) return OPCmini::loadReqState(array('new_quantity', 'new_virtuemart_product_id', 'quantity', 'virtuemart_product_id', 'cart_virtuemart_product_id', 'customProductData')); ;

$qer = 'select `virtuemart_product_id` from `#__virtuemart_products` where `virtuemart_product_id` = '.(int)$p_id.' limit 0,1'; 
	 $db->setQuery($qer); 
	 $product_id = $db->loadResult(); 
	 if (empty($product_id)) return OPCmini::loadReqState(array('new_quantity', 'new_virtuemart_product_id', 'quantity', 'virtuemart_product_id', 'cart_virtuemart_product_id', 'customProductData')); ; 


$qq[$p_id] = $q;

$a = array(); 
$a[$p_id] = $p_id; 
$p_id = $a; 

}
   
   

}
else return OPCmini::loadReqState(array('new_quantity', 'new_virtuemart_product_id', 'quantity', 'virtuemart_product_id', 'cart_virtuemart_product_id', 'customProductData')); ;


    $post = JRequest::get('default');
	/*
	if (!class_exists('VirtueMartModelProduct'))
	 require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'models' .DIRECTORY_SEPARATOR. 'product.php');
	 */
	 
	$productClass = OPCmini::getModel('product'); //new VirtueMartModelProduct(); 
	
	//$virtuemart_product_ids = JRequest::getVar('virtuemart_product_id', array(), 'default', 'array'); //is sanitized then
	$newp = array(); 
$rr2 = array(); 


	$db = JFactory::getDBO(); 
	foreach ($p_id as $pid)
	 {
	   $pid = (int)$pid; 
	   $newp[$pid] = $pid; 
	   
	   	 $qer = 'select `virtuemart_product_id` from `#__virtuemart_products` where `virtuemart_product_id` = '.(int)$pid.' limit 0,1'; 
		 $db->setQuery($qer); 
		 $product_id = $db->loadResult(); 
		 if (empty($product_id)) continue; 
	   
       $product = $productClass->getProductSingle($pid, true, true, true); 
       if (empty($product)) continue; 
	   $rr = OPCAddToCartAsLink::getProductCustomsFieldCart($product); 
	   $rr2[] = $rr; 
	 }
	 
	 
	 
    if (($opc_link_type == 2) || ($opc_link_type == 1))
	{
	 if (!empty($ref->cart->products))
	  {
	    $p = $ref->cart->products;
		foreach ($p as $key=>$pr) 
		 {
		   $id = $pr->virtuemart_product_id; 
		   
		   // delete cart content
		   if ($opc_link_type == 1)
		   {
		  
		   if (isset($ref->cart->products[$key]))
		   $ref->cart->removeProductCart($key); 
		   else 
		   if (isset($ref->cart->product[$id]))
		   $ref->cart->removeProductCart($id); 
 continue; 
		   }
		   // do not increment quantity: 
		   if ($opc_link_type == 2)
		   if (in_array($id, $newp)) return OPCmini::loadReqState(array('new_quantity', 'new_virtuemart_product_id', 'quantity', 'virtuemart_product_id', 'cart_virtuemart_product_id', 'customProductData')); 
		   
		  
		 }
		 
	  }
    }	 
	
	
	 
	 
				{ # seyi_code
                       {
                                $session = JFactory::getSession();
                                $coupon_code = $session->get('link_coupon_code', '', 'awocoupon');
                                if(!empty($coupon_code)) {
                                        $ref->cart->couponCode = $coupon_code;
                                }
                        }
                }
				
	JRequest::setVar('virtuemart_product_id', $newp); //is sanitized then
	JRequest::setVar('quantity', $qq); //is sanitized then

	
	
	if (!empty($rr2))
	foreach ($rr2 as $rr1)
	 foreach ($rr1 as $post)
	 {
	    
	    $x = JRequest::getVar($post['name']); 
		if (empty($x))
		{
		 $test = array(); 
		 if (strpos($post['name'], ']')!==false)
		 {
		 $post['name'] = parse_str($post['name'].'='.$post['value'], $test); 
		
		 $firstkey = 0; 
		 if (!empty($test))
		  foreach ($test as $key=>$val)
		   {
		     $firstkey = $key; break; 
		   }
		     
		 $name = $firstkey; 
		 $value = $test[$name]; 
		 JRequest::setVar($name, $value); 
		 
		 }
		 else
	     JRequest::setVar($post['name'], $post['value']); 
		}
	 }
	if (!empty($opc_auto_coupon))
	{
	 if (empty($ref->cart->couponCode))
	 $ref->cart->couponCode = $opc_auto_coupon; 
	}
	if (defined('VM_VERSION') && (VM_VERSION >= 3))
	{
	 ob_start(); 
	 
	 $rr = $ref->cart->add($newp);	
	 //$ref->cart->prepareCartData(); 
	 
	 require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
	 $cart = OPCmini::getCart(); 
	 
	 $zz = ob_get_clean(); 
	 
	}
	else
	$ref->cart->add();
	
	
	
	JRequest::setVar('virtuemart_product_id', ''); 
	JRequest::setVar('add_id', ''); 
	JRequest::setVar('opc_adc', 1); 
	
	//$quantityPost = (int) $post['quantity'][$p_key];
	OPCmini::loadReqState(array('new_quantity', 'new_virtuemart_product_id', 'quantity', 'virtuemart_product_id', 'cart_virtuemart_product_id', 'customProductData')); 
	return $rememberhtml; 

  }
  
  
		/**
	 * Original function from customFields.php 
	 * We need to update custom attributes when using add to cart as link
	 *
	 * @author Patrick Kohl
	 * @param obj $product product object
	 * @return html code
	 */
	public static function getProductCustomsFieldCart ($product) {
		
		if (defined('VM_VERSION') && (VM_VERSION >= 3)) return array(); 
		$db = JFactory::getDBO(); 
		
		// group by virtuemart_custom_id
		$query = 'SELECT C.`virtuemart_custom_id`, `custom_title`, C.`custom_value`,`custom_field_desc` ,`custom_tip`,`field_type`,field.`virtuemart_customfield_id`,`is_hidden`
				FROM `#__virtuemart_customs` AS C
				LEFT JOIN `#__virtuemart_product_customfields` AS field ON C.`virtuemart_custom_id` = field.`virtuemart_custom_id`
				Where `virtuemart_product_id` =' . (int)$product->virtuemart_product_id . ' and `field_type` != "G" and `field_type` != "R" and `field_type` != "Z"';
		$query .= ' and is_cart_attribute = 1 group by virtuemart_custom_id';

		$db->setQuery ($query);
		$groups = $db->loadObjectList ();

		if (!class_exists ('VmHTML')) {
			require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'html.php');
		}
		$row = 0;
		if (!class_exists ('CurrencyDisplay')) {
			require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'currencydisplay.php');
		}
		$currency = CurrencyDisplay::getInstance ();

		if (!class_exists ('calculationHelper')) {
			require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'calculationh.php');
		}
		$calculator = calculationHelper::getInstance ();
		if (!class_exists ('vmCustomPlugin')) {
			require(JPATH_VM_PLUGINS .DIRECTORY_SEPARATOR. 'vmcustomplugin.php');
		}
		
		$reta = array(); 
		
		$free = OPCLang::_ ('COM_VIRTUEMART_CART_PRICE_FREE');
		// render select list
		if (!empty($groups))
		foreach ($groups as $group) {

			//				$query='SELECT  field.`virtuemart_customfield_id` as value ,concat(field.`custom_value`," :bu ", field.`custom_price`) AS text
			$query = 'SELECT field.`virtuemart_product_id`, `custom_params`,`custom_element`, field.`virtuemart_custom_id`,
							field.`virtuemart_customfield_id`,field.`custom_value`, field.`custom_price`, field.`custom_param`
					FROM `#__virtuemart_customs` AS C
					LEFT JOIN `#__virtuemart_product_customfields` AS field ON C.`virtuemart_custom_id` = field.`virtuemart_custom_id`
					Where `virtuemart_product_id` =' . (int)$product->virtuemart_product_id;
			$query .= ' and is_cart_attribute = 1 and C.`virtuemart_custom_id`=' . (int)$group->virtuemart_custom_id;

			// We want the field to be ordered as the user defined
			$query .= ' ORDER BY field.`ordering`';

			$db->setQuery ($query);
			$options = $db->loadObjectList ();
			//vmdebug('getProductCustomsFieldCart options',$options);
			$group->options = array();
			foreach ($options as $option) {
				$group->options[$option->virtuemart_customfield_id] = $option;
			}
			
			
			if ($group->field_type == 'V') {
				$default = current ($group->options);
				foreach ($group->options as $productCustom) {
					if ((float)$productCustom->custom_price) {
						$price = strip_tags ($currency->priceDisplay ($calculator->calculateCustomPriceWithTax ($productCustom->custom_price)));
					}
					else {
						$price = ($productCustom->custom_price === '') ? '' : $free;
					}
					$productCustom->text = $productCustom->custom_value . ' ' . $price;

				}
				$r = array(); 
				$r['name'] = 'customPrice[' . $row . '][' . $group->virtuemart_custom_id . ']'; 
				$r['value'] = $default->custom_value;
				$reta[] = $r; 
				
				//$group->display = VmHTML::select ('customPrice[' . $row . '][' . $group->virtuemart_custom_id . ']', $group->options, $default->custom_value, '', 'virtuemart_customfield_id', 'text', FALSE);
			}
			else {
				if ($group->field_type == 'G') {
					$group->display .= ''; // no direct display done by plugin;
				}
				else {
					if ($group->field_type == 'E') {
						$group->display = '';

						foreach ($group->options as $k=> $productCustom) {
							if ((float)$productCustom->custom_price) {
								$price = $currency->priceDisplay ($calculator->calculateCustomPriceWithTax ($productCustom->custom_price));
							}
							else {
								$price = ($productCustom->custom_price === '') ? '' : $free;
							}
							$productCustom->text = $productCustom->custom_value . ' ' . $price;
							$productCustom->virtuemart_customfield_id = $k;
							if (!class_exists ('vmCustomPlugin')) {
								require(JPATH_VM_PLUGINS .DIRECTORY_SEPARATOR. 'vmcustomplugin.php');
							}

							//legacy, it will be removed 2.2
							$productCustom->value = $productCustom->virtuemart_customfield_id;
							JPluginHelper::importPlugin ('vmcustom');
							$dispatcher = JDispatcher::getInstance ();
							$fieldsToShow = $dispatcher->trigger ('plgVmOnDisplayProductVariantFE', array($productCustom, &$row, &$group));

						
							$group->display .= '<input type="hidden" value="' . $productCustom->virtuemart_customfield_id . '" name="customPrice[' . $row . '][' . $productCustom->virtuemart_custom_id . ']" /> ';
							
							$r = array(); 
							$r['name'] = 'customPrice[' . $row . '][' . $productCustom->virtuemart_custom_id . ']';
							$r['value'] = $productCustom->virtuemart_customfield_id;
							$reta[] = $r; 
							
							
							
							if (!empty($currency->_priceConfig['variantModification'][0]) and $price !== '') {
								$group->display .= '<div class="price-plugin">' . OPCLang::_ ('COM_VIRTUEMART_CART_PRICE') . '<span class="price-plugin">' . $price . '</span></div>';
							}
							$row++;
						}
						$row--;
					}
					else {
						if ($group->field_type == 'U') {
							foreach ($group->options as $productCustom) {
								if ((float)$productCustom->custom_price) {
									$price = $currency->priceDisplay ($calculator->calculateCustomPriceWithTax ($productCustom->custom_price));
								}
								else {
									$price = ($productCustom->custom_price === '') ? '' : $free;
								}
								$productCustom->text = $productCustom->custom_value . ' ' . $price;

								$group->display .= '<input type="text" value="' . OPCLang::_ ($productCustom->custom_value) . '" name="customPrice[' . $row . '][' . $group->virtuemart_custom_id . '][' . $productCustom->value . ']" /> ';
								
								$r = array(); 
								
								$r['name'] = 'customPrice[' . $row . '][' . $group->virtuemart_custom_id . '][' . $productCustom->value . ']';
								$r['value'] = OPCLang::_ ($productCustom->custom_value);
								$reta[] = $r; 
								// only the first is used here
								//continue; 
								
								if (false)
								if (!empty($currency->_priceConfig['variantModification'][0]) and $price !== '') {
									$group->display .= '<div class="price-plugin">' . OPCLang::_ ('COM_VIRTUEMART_CART_PRICE') . '<span class="price-plugin">' . $price . '</span></div>';
								}
							}
						}
						else {
							if ($group->field_type == 'A') {
								$group->display = '';
								foreach ($group->options as $productCustom) {
								/*	if ((float)$productCustom->custom_price) {
										$price = $currency->priceDisplay ($calculator->calculateCustomPriceWithTax ($productCustom->custom_price));
									}
									else {
										$price = ($productCustom->custom_price === '') ? '' : $free;
									}*/
									$productCustom->field_type = $group->field_type;
									$productCustom->is_cart = 1;
								
								
								// only the first is used here
								continue; 


									
									$checked = '';
								}
							}
							else {

								$group->display = '';
								$checked = 'checked="checked"';
								foreach ($group->options as $productCustom) {
									//vmdebug('getProductCustomsFieldCart',$productCustom);
									if (false)
									if ((float)$productCustom->custom_price) {
										$price = $currency->priceDisplay ($calculator->calculateCustomPriceWithTax ($productCustom->custom_price));
									}
									else {
										$price = ($productCustom->custom_price === '') ? '' : $free;
									}
									$productCustom->field_type = $group->field_type;
									$productCustom->is_cart = 1;
								//	$group->display .= '<input id="' . $productCustom->virtuemart_custom_id . '" ' . $checked . ' type="radio" value="' .
								//		$productCustom->virtuemart_custom_id . '" name="customPrice[' . $row . '][' . $productCustom->virtuemart_customfield_id . ']" /><label
								//		for="' . $productCustom->virtuemart_custom_id . '">' . $this->displayProductCustomfieldFE ($productCustom, $row) . ' ' . $price . '</label>';
								//MarkerVarMods
									$r['name'] = 'customPrice[' . $row . '][' . $group->virtuemart_customfield_id . ']';
								   $r['value'] = $productCustom->virtuemart_custom_id;
								   $reta[] = $r; 
								   //only the first here
								   continue; 
									$checked = '';
								}
							}
						}
					}
				}
			}
			$row++;
		}

		return $reta;

	}
 public static function checkCheckboxProducts(&$cart, $q_o=array(), $new_id=array())
 {	
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
		OPCmini::storeReqState(array('new_quantity', 'new_virtuemart_product_id', 'quantity', 'virtuemart_product_id', 'cart_virtuemart_product_id', 'customProductData')); 
 
			if (empty($q_o)) {
	 		  $q_o = $quantity = JRequest::getVar('new_quantity', JRequest::getVar('quantity', array())); 
			}
			else {
				$quantity = $q_o; 
			}
			
			if (empty($new_id)) {
			  $new_id = JRequest::getVar('new_virtuemart_product_id', array()); 
			}
			
			
			
			$proceed = true; 
		    $found = false; 
		  if  (!empty($new_id))
		  {
			  $proceed = false; 
			  require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
			  $cart = OPCmini::getCart(); 
			  
			// we need to remove a product by product_id
			
			$q_saved = JRequest::getVar('quantity', null); 
			$i_saved = JRequest::getVar('cart_virtuemart_product_id', null); 
			$c_saved = JRequest::getVar('customProductData', null);
			
			//$quantity = JRequest::getVar('new_quantity', array()); 
			if (!is_array($new_id)) $new_id = array($new_id => $new_id); 
			
			if (defined('VM_VERSION') && (VM_VERSION >= 3)) {
			$products = $cart->cartProductsData; 
		}
		else {
		  $products = $cart->products;
		}
			
			
			
			
			foreach ($products as $cart_id=>$pZ)
			{
				$p = (object)$pZ; 
				$p->virtuemart_product_id = (int)$p->virtuemart_product_id; 
				
				if (in_array($p->virtuemart_product_id, $new_id))
				{
					
					
				
					
					
					if (empty($quantity[$p->virtuemart_product_id]))
					{
					 // we are going to remove the extra product
					 
					 JRequest::setVar('cart_virtuemart_product_id', $cart_id);
					 $cart->removeProductCart($cart_id); 
					 
					
					 
						require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
						$cart = OPCmini::getCart(); 
					    $proceed = false; 
						
						
							unset($quantity[$p->virtuemart_product_id]); 
							unset($new_id[$p->virtuemart_product_id]); 

					
					}
					else
					{
							// the extra product is already in the cart, do not update it...
							unset($quantity[$p->virtuemart_product_id]); 
							unset($new_id[$p->virtuemart_product_id]); 
							continue; 
					
					}	
				   		
					
				}
							}
		  }
		  
		   
		  
		  // product is not in cart, so let's not try to add it: 
		  if (!empty($quantity))
		  foreach ($quantity as $k=>$v)
		  {
			  if (empty($v)) {
				  unset($quantity[$k]); 
				  unset($new_id[$k]); 
			  }
		  }
		  
		 
		  
		  if ((!empty($quantity)) && (!empty($new_id)))
				{
			
			
			  
				
				 
				 JRequest::setVar('customProductData', array()); 
				 // array of products: 
				 JRequest::setVar('virtuemart_product_id', $new_id); 
				 //array of quantities: 
				 JRequest::setVar('quantity', $quantity);
				 JRequest::setVar('quantity', $quantity, 'post');
				 
				 
				
				  // add a new product per product_id: 
				  if (defined('VM_VERSION') && (VM_VERSION >= 3))
				{
				
					ob_start(); 
					
					$rr = $cart->add($new_id);	
					
					//JRequest::setVar('quantity', null);
					require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
					$cart = OPCmini::getCart(); 
					
					
					$zz = ob_get_clean(); 
	 
				}
				else
				{

					
				   $cart->add($new_id);
				}
			  
		  
				}

				
				require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
				$cart = OPCmini::getCart(); 
				
				
				return OPCmini::loadReqState(array('new_quantity', 'new_virtuemart_product_id', 'quantity', 'virtuemart_product_id', 'cart_virtuemart_product_id', 'customProductData')); 
			/*				
				
			if ((!isset($q_saved)) || (is_null($q_saved)))
			{
				JRequest::setVar('quantity', null); 
				unset($_POST['quantity']); unset($_GET['quantity']); unset($_REQUEST['quantity']); 
			}
			else
			{
			 JRequest::setVar('quantity', $q_saved); 
			}
			
			if ((!isset($i_saved)) || (is_null($i_saved)) )
			{
				unset($_POST['cart_virtuemart_product_id']); unset($_GET['cart_virtuemart_product_id']); unset($_REQUEST['cart_virtuemart_product_id']); 
			}
			else
			{
			 JRequest::setVar('cart_virtuemart_product_id', $i_saved); 
			}
			
			if ((!isset($c_saved)) || (is_null($c_saved)))
			{
				unset($_POST['customProductData']); unset($_GET['customProductData']); unset($_REQUEST['customProductData']); 
			}
			else
			{
			 JRequest::setVar('customProductData', $c_saved); 
			}
			*/
			
			//JRequest::setVar('customProductData', $c_saved); 
	
 }	 
  
}