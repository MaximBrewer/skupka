<?php   
/* license: commercial ! */
defined('_JEXEC') or 	die( 'Direct Access to ' . basename( __FILE__ ) . ' is not allowed.' ) ;

/*
note, to override the quantities per wholesalers, insert this: 
$dispatcher = JEventDispatcher::getInstance(); 
$dispatcher->trigger('plgUpdateCustomPrice', array(&$product)); 

i tried about 20 techniques to override the stuff needed and the only reliable and future-proof that i found is via a template overrride here:
\templates\ja_brickstore\html\com_virtuemart\sublayouts\addtocartbar.php


*/
class plgSystemProductrestrictions extends JPlugin {

	public $last_error = ''; 
	function __construct(& $subject, $config) {
		
		 parent::__construct($subject, $config);
		 
		 
	}
	
	
	private function _init() {
	   
		 $action = 'vm.category'; 
		 $assetName = 'com_virtuemart.category'; 
		 $z = JFactory::getUser()->authorise($action, $assetName);
		 
		 return $z; 
		
	}
	 
	
	function onContentBeforeDisplay($context, &$article, &$params, $e) {
		
		if (!JFactory::getApplication()->isSite()) return; 
		
		if (strpos($context, 'com_virtuemart.')===0) {
			
			if ($context === 'com_virtuemart.productdetails') {
			 
			  $this->overrideProduct($article); 
			  
			}
			
		}
	}
	
	
	
	
	function getUserQuantity($cart, $pid) {
	   $db = JFactory::getDBO(); 
	   $user_id = JFactory::getUser()->get('id'); 
	   $email = JFactory::getUser()->get('email'); 
	   $r = 0; 
	   if ((isset($cart->BT)) && (!empty($cart->BT['email']))) $email2 = $cart->BT['email']; 
		  
		  $date = new JDate(date('Y').'-1-1 0:0:00');
		  if (method_exists($date, 'toMySQL'))
		  $m = $date->toMySQL(); 
	      else 
		  $m = $date->toSQL(); 
	   
	      //$q = 'select sum(product_quantity) from #__virtuemart_order_items as i, #__virtuemart_order_userinfos as u where i.virtuemart_product_id = '.(int)$pid." and i.virtuemart_order_id = u.virtuemart_order_id and i.created_on >= '".$db->escape($m)."' and ("; 
		  if (!empty($cart->BT['virtuemart_country_id'])) {
		  $q = "select sum(i.product_quantity) from #__virtuemart_order_items as i, #__virtuemart_order_userinfos as u,#__productrestrictions as r, #__virtuemart_product_categories as pc  where r.d2 > 0 and r.virtuemart_country_id = ".(int)$cart->BT['virtuemart_country_id']." and r.virtuemart_category_id = pc.virtuemart_category_id and pc.virtuemart_product_id = i.virtuemart_product_id and  i.virtuemart_order_id = u.virtuemart_order_id and i.created_on >= '".$db->escape($m)."' and ("; 
		  
		  $w = ''; 
		  $s = false; 
		  if (!empty($email)) {
		  $w .= " u.email = '".$db->escape($email)."' "; 
		  $s = true; 
		  }
	  
		  if (!empty($email2)) {
		  if (!empty($w)) $w .= ' or '; 
		  $w .= " u.email = '".$db->escape($email2)."' "; 
		  $s = true; 
		  }
	  
		  if (!empty($user_id)) {
		  if (!empty($w)) $w .= ' or '; 
		  $w .= " u.virtuemart_user_id = ".(int)$user_id; 
		  $s = true; 
		  }
		  
		  if (empty($s)) return 0; 
		  $q .= $w; 
	      $q .= ')'; 
	   
		  $db->setQuery($q); 
		  $r = $db->loadResult(); 
		  }
		  if (empty($r)) {
			  return 0; 
		  }
		  $r = (float)$r; 
		  return $r; 
	
	}
	
	function getProductRestrictions($pid, $cid) {
		$db = JFactory::getDBO(); 
		static $cache; 
		if (empty($cache)) $cache = array(); 
		if (!empty($cache[$pid.'_'.$cid])) return $cache[$pid.'_'.$cid]; 
		
		$catid = $this->isRegulated($pid); 
		
		
		if (empty($catid)) return array(); 
		
		  $q = 'select d1,d2 from #__productrestrictions where virtuemart_category_id = '.(int)$catid.' and virtuemart_country_id = '.(int)$cid.' limit 0,1'; 
		  //$q = 'select * from #__productrestrictions where 1'; //virtuemart_category_id = '.(int)$catid.' and virtuemart_country_id = '.(int)$cid.' limit 0,1'; 
		  $db->setQuery($q); 
		  $res = $db->loadAssoc(); 
		  
		  
		  
		  if (empty($res)) $res = array(); 
		  $cache[$pid.'_'.$cid] = $res; 
		  return $cache[$pid.'_'.$cid];
	}
	function hasAny($pid, $cid=0) {
		
		static $cache; 
		if (empty($cache)) $cache = array(); 
		if (!empty($cache[$pid.'_'.$cid])) return $cache[$pid.'_'.$cid]; 
		
		$cat = $this->isRegulated($pid); 
		if (empty($cat)) return false; 
		
		$db = JFactory::getDBO(); 
		$q = 'select d1,d2 from #__productrestrictions where virtuemart_category_id = '.(int)$cat; 
		if (!empty($cid)) 
			$q .= ' and virtuemart_country_id = '.(int)$cid; 
		
		$q .= ' and ((d1 > 0) or (d2 > 0)) limit 0,1'; 		
		$db->setQuery($q); 
		$r = $db->loadAssoc(); 
		
		
		
		if (empty($r)) $r = false; 
		else $r = true; 
		
		$cache[$pid.'_'.$cid] = $r; 
		
		return $r; 
	}
	
	function isRegulated($pid) {
		static $cache; 
		if (isset($cache[$pid])) return $cache[$pid]; 
		
		$db = JFactory::getDBO(); 
		$q = 'select r.virtuemart_category_id from #__virtuemart_product_categories as c, #__productrestrictions as r where c.virtuemart_product_id = '.(int)$pid.' and r.virtuemart_category_id = c.virtuemart_category_id '; 
		try {
		  $db->setQuery($q); 
		  $cat = $db->loadResult(); 
		}
		catch (Exception $e) {
			JFactory::getApplication()->enqueueMessage('Visit backend of productrestrictions plugin and click save to adjust database structure', 'error'); 
			//$this->onExtensionAfterSave(null, null); 
		}
		if (empty($cat)) {
			
			if ($this->params->get('debug', false)) {
				   JFactory::getApplication()->enqueueMessage('DEBUG productrestrictions: product ID '.(int)$pid.' is not regulated');
			   }
			
			$cache[$pid] = 0; 
			return 0; 
		}
		
		
		if ($this->params->get('debug', false)) {
				   JFactory::getApplication()->enqueueMessage('DEBUG productrestrictions: product ID '.(int)$pid.' IS regulated');
			   }
		
		$cache[$pid] = (int)$cat; 
		return (int)$cat; 
		
	}
	/*
	function plgVmOnAddToCartFilter(&$product, &$customfield, &$customProductData, &$customFiltered) {
			 $product->product_name = 'TEST'; 
			$product->step_order_level = 12;
			$product->max_order_level = $product->step_order_level * 10; 
			$product->min_order_level = $product->step_order_level; 
	}
	
	function plgVmgetPaymentCurrency($virtuemart_paymentmethod_id, &$paymentCurrency) {
		
	}
	
	*/
	
	function getTotalNumber(&$cart, &$product, &$quantity, &$isnew=true, &$changed=false) {
		 $isnew = true; 
		 $tq = 0; 
		 foreach ($cart->cartProductsData as $k=>$p) {
			   $pid = $mp = (int)$p['virtuemart_product_id']; 
			   $product->virtuemart_product_id = (int)$product->virtuemart_product_id; 
			   $catid = $this->isRegulated($pid); 
			   //don't count unregulated products
			   if (empty($catid)) continue; 
			   
			   if ($mp === $product->virtuemart_product_id) {
				   $isnew = false; 
				   //makes sure tha that the quantity is added to only one product:
				   if (!$changed) {
				     $q = (float)$quantity; 
					 $changed = true; 
				   }
				   else {
					   $q = (float)$p['quantity']; 
				   }
			   }
			   else {
			     $q = (float)$p['quantity']; 
			   }
			   if ($this->hasAny($mp, 0)) {
				 $tq = $tq + $q;    
			   }
		   }
		   return $tq; 
	}
	
	function checkQuantity(&$cart, &$product, &$quantity, &$e='', $incheckout=false) {
	   
	   static $ea; 
	   if (empty($ea)) $ea = array(); 
	   $retVar = true; 
	   if (empty($e)) $e = ''; 
		
	   self::loadLang(); 
	   $cid = $this->_getCustomerCountry($cart); 
	  
	    $tqchecked = false; 
	    
		$isnew = true; 
		
		$ch = false; 
		$tq = $this->getTotalNumber($cart, $product, $quantity, $isnew, $ch); 
		/*
		   foreach ($cart->cartProductsData as $k=>$p) {
			   $mp = (int)$p['virtuemart_product_id']; 
			   $product->virtuemart_product_id = (int)$product->virtuemart_product_id; 
			   $catid = $this->isRegulated($pid); 
			   //don't count unregulated products
			   if (empty($catid)) continue; 
			   
			   if ($mp === $product->virtuemart_product_id) {
				   $isnew = false; 
				   if (!$ch) {
				     $q = (float)$quantity; 
					 $ch = true; 
				   }
				   else {
					   $q = (float)$p['quantity']; 
				   }
			   }
			   else {
			     $q = (float)$p['quantity']; 
			   }
			   if ($this->hasAny($mp, 0)) {
				 $tq = $tq + $q;    
			   }
		   }
		   */
		   
		   /*
		   if (class_exists('OPCloader')) {
			   
		   $x = debug_backtrace(); $r = array(); 
		   foreach ($x as $l) $r[] = $l['file'].' '.$l['line']; 
		  OPCloader::opcDebug($tq, 'tq'); 
		  OPCloader::opcDebug($r, 'tq'); 
		   }
		   */
		   
		   $maxnum = (float)$this->params->get('maxnum'); 
		   
		    $id = $product->virtuemart_product_id; 
		    if (!$this->hasAny($id, 0)) {
				//is not a regulated product: 
				$isnew = false; 
			}
		   
		   
		  // error_log(var_export(array('tq'=>$tq, 'maxnum'=>$maxnum), true)); 
		   //error_log(var_export($product, true)); 
		   
		   
		   if (($isnew) && ($tq >= $maxnum)) {
			
			   
			   if ($isnew) {
				   //$product->quantity = 0; 
				   /*
				   $quantity = 0; 
				   $product2 = new stdClass; 
				   $product2->product_name = $product->product_name; 
				   $product2->product_in_stock = 0; 
				   $product2->product_ordered = 0; 
				   $product2->quantity = 0; 
				   $product = $product2; 
				   return; 
				   */
				   $msg = JText::_('PLG_SYSTEM_PRODUCTRESTRICTIONS_MAXNUM_ERR').'<error id="PLG_SYSTEM_PRODUCTRESTRICTIONS_MAXNUM_ERR" style="display:none;"></error>';  
				   if (!isset($ea[$msg])) {
						$ea[$msg] = $msg; 
						if ($e != $msg)
						$e = $msg; 
						$retVar = false; 
					}
					else {
					//$e .= '<span></span>'; 
					$retVar = false; 
					}
					$tqchecked = true; 
				 
				   
					}
		   }
		     $msg = JText::_('PLG_SYSTEM_PRODUCTRESTRICTIONS_MAXNUM_ERR').'<error id="PLG_SYSTEM_PRODUCTRESTRICTIONS_MAXNUM_ERR" style="display:none;"></error>';    
			 if ($tq > $maxnum) {  
			   if (!isset($ea[$msg])) {
			     $ea[$msg] = $msg; 
				 if ($e != $msg)
				 $e = $msg; 
				 $retVar = false; 
				 
				 
				 
			   }
			   else {
				   //$e .= '<span></span>'; 
				   $retVar = false; 
			   }
			   $tqchecked = true; 
		   }
	  
	   $db = JFactory::getDBO(); 
	   $qp = (float)$quantity; 
	   //if (empty($qp)) return; 
	   
	   
	  
	   
	   
	   if (!empty($cid)) {
		   
		    $pid = (int)$product->virtuemart_product_id; 
		   
		   if (!isset($product->product_name)) {
	        $productModel = VmModel::getModel('product');
	        $productTemp = $productModel->getProduct($pid);
		    $product->product_name = $productTemp->product_name; 
	       }
		   /*
		   $tq = 0; 
		   foreach ($cart->cartProductsData as $k=>$p) {
			   $mp = $p['virtuemart_product_id']; 
			   $q = (float)$p['quantity']; 
			   if ($this->hasAny($mp, $cid)) {
				 $tq = $tq + $q;    
			   }
		   }
		  
		   
		   $maxnum = (float)$this->params->get('maxnum'); 
		   if ($tq > $maxnum) {
			   $e .= JText::_('PLG_SYSTEM_PRODUCTRESTRICTIONS_MAXNUM_ERR').'<br />'; 
		   }
		   */
		   
		  
		  /*
	      $q = 'select d1,d2 from #__productrestrictions where virtuemart_product_id = '.(int)$pid.' and virtuemart_country_id = '.(int)$cid.' limit 0,1'; 
		  $db->setQuery($q); 
		  $res = $db->loadAssoc(); 
		  */
		  
		  $res = $this->getProductRestrictions($pid, $cid); 
		  
		 
		  
		  // check cart quantity
		  if (!empty($res['d1'])) {
		     $q1 = (float)$res['d1']; 
			 // if cart quantity is larger, set this to max cart quantity
			 if ($qp > $q1) {
					$qp = $adj = $q1; 
					
					$msg = $product->product_name.': '.JText::_('PLG_SYSTEM_PRODUCTRESTRICTIONS_CARTQERR').' '.JText::_('PLG_SYSTEM_PRODUCTRESTRICTIONS_ADJUSTED').'<error id="PLG_SYSTEM_PRODUCTRESTRICTIONS_CARTQERR" style="display:none;"></error>'; 
					
					
					
					if (!isset($ea[$msg])) {
						$ea[$msg] = $msg; 
						if ($e != $msg)
						$e = $msg; 
						$retVar = false; 
					}
					else {
						//$e .= '<span></span>'; 
						$retVar = false; 
					}
					
			 }
			 
			 
			 if ($tq > $q1) { 
			   $msg = JText::_('PLG_SYSTEM_PRODUCTRESTRICTIONS_MAXNUM_ERR').'<error id="PLG_SYSTEM_PRODUCTRESTRICTIONS_MAXNUM_ERR" style="display:none;"></error>';  			 
			   
			    if (!empty($res['d1'])) {
			$msg = str_replace('{max}', $res['d1'], $msg); 
		  }
		  if (!empty($res['d2'])) {
		   $msg = str_replace('{maxyear}', $res['d2'], $msg); 
		  }
			   
			   //if (!isset($ea[$msg])) 
			   {
			     $ea[$msg] = $msg; 
				 $e = $msg; 
				 $retVar = false; 
				 
				 	 
		  
				 
				 return false; 
				 
				 
			   }
			   /*
			   else {
				   //$e .= '<span></span>'; 
				   $e .= '&nbsp;';
				   
				   $retVar = false; 
			   }
			   */
			   
			   
			   $tqchecked = true; 
			 
			 }
			 
		  }
		  
		  if (!empty($res['d2'])) {
		    $q2 = (float)$res['d2']; 
			$uq = $this->getUserQuantity($cart, $pid); 
			
			
			
			if (!empty($uq)) {
			   
			   // remaining quantity to be purchased: 
			   $remaining = $q2 - ($uq + $qp); 
			   
			   if ($remaining < 0) {
				 // if remaing allowed is smaller than zero, 
			     $qp = $adj = ($qp + $remaining); 
				 if ($qp < 0) {
					// the product which is in cart CANNOT BE PURCHASED !
				    $qp = $adj = 0; 
					if (isset($cart->cartProductsData)) {
					foreach ($cart->cartProductsData as $id=>$p) {
						
					   $cpid = (int)$p['virtuemart_product_id']; 
					   if ($pid == $cpid) {
					      // $cart->removeProductCart($id); 
					   }
					   
					}
					}
					if (isset($cart->products)) {
					foreach ($cart->products as $id=>$p) {
					   $cpid = (int)$p->virtuemart_product_id; 
					   if ($pid == $cpid) {
					       // $cart->removeProductCart($id); 
					   }
					   
					}
					}
					
					
					$msg = $product->product_name.': '.JText::_('PLG_SYSTEM_PRODUCTRESTRICTIONS_THISPRODUCTCANNOTBEPURCHASED').' '.JText::_('PLG_SYSTEM_PRODUCTRESTRICTIONS_ADJUSTED').'<error id="PLG_SYSTEM_PRODUCTRESTRICTIONS_THISPRODUCTCANNOTBEPURCHASED" style="display:none;"></error>';  	
					
					if (!isset($ea[$msg])) {
						$ea[$msg] = $msg; 
						if ($e != $msg)
						$e = $msg; 
						$retVar = false; 
					}
					else {
						//$e .= '<span></span>'; 
						$retVar = false; 
					}
					
				 }
			   }
			   else {
			   
			   if ($qp > $remaining) {
			      $qp = $adj = $remaining; 
				  $msg = $product->product_name.': '.JText::_('PLG_SYSTEM_PRODUCTRESTRICTIONS_OQERR').' '.JText::_('PLG_SYSTEM_PRODUCTRESTRICTIONS_ADJUSTED').'<error id="PLG_SYSTEM_PRODUCTRESTRICTIONS_ADJUSTED" style="display:none;"></error>';  	 
				  
				  $msg = str_replace('{max}', $res['d1'], $msg); 
				  $msg = str_replace('{maxyear}', $res['d2'], $msg); 
				  
				  if (!isset($ea[$msg])) {
					$ea[$msg] = $msg; 
					if ($e != $msg)
					$e = $msg; 
					$retVar = false; 
				}
				else {
				  // $e .= '<span></span>'; 
				  $retVar = false; 
				}
				  
			   }
			   
			   $total = JText::_('PLG_SYSTEM_PRODUCTRESTRICTIONS_ALLOWEDTOTAL').'<error id="PLG_SYSTEM_PRODUCTRESTRICTIONS_ALLOWEDTOTAL" style="display:none;"></error>'; 
			   
			   $r = $q2 - $uq; 
			   $r = (int)$r; 
			   $r = (string)$r; 
			   $total = str_replace('[total]', $r, $total); 
			  
			   if (!empty($e)) {
				   $msg = "\n<br />"; 
			       $msg .= $product->product_name.': '.$total; 
				   
				   if (!isset($ea[$msg])) {
					$ea[$msg] = $msg; 
					$e .= $msg; 
					$retVar = false; 
			   }
			   else {
				 //  $e .= '<span></span>'; 
				 $retVar = false; 
			   }
				   
			   }
			   
			   
			   
			   }
			   
			   
				   
				   
			   }				   
			}
			
			
			
		  }
		  
		  //adjust quantity: 
		  if (isset($adj)) {
		     $quantity = $adj; 
		  }
		  
		  
		  if (!empty($res['d1'])) {
			$e = str_replace('{max}', $res['d1'], $e); 
		  }
		  if (!empty($res['d2'])) {
		  $e = str_replace('{maxyear}', $res['d2'], $e); 
		  }
		 
		  return $retVar;
	   
	}
	
	function handleRetailers( &$cart, &$product, &$quantity, &$errorMsg, &$adjustQ, $inCheckout = false ) {
		
	  $tq = $this->getTotalNumber($cart, $product, $quantity, $isnew, $ch); 
	  $retailer_q = 0; 
	  foreach ($cart->cartProductsData as $k=>$p) {
			   $pid = (int)$p['virtuemart_product_id']; 
			   $catid = $this->isRegulated($pid); 
			   if (empty($catid)) continue; 
			   $data = $this->getData($catid, false, 0); 
			   if (!empty($data['retailer_q'])) {
			   $retailer_q = $data['retailer_q'];
			   break; 
			   }			   
			   
	  }
	  
	  
	  
	  $retailer_q = (int)$retailer_q;
	  if (!empty($retailer_q)) {
		  $mod = $tq % $retailer_q; 
		  
		  if (!empty($mod)) {
			  $x1 = $tq / $retailer_q; // 15 / 12 =  1.25, 35 / 12 = 2.9166
			  $x2 = floor($tq / $retailer_q); // = 1, 2
			  $dif = $x1-$x2; // 0.25 or 0.916
			  
			  $difMinus = $dif * $retailer_q;  //3, 11
			  //$difPlus = $tq - $difMinus + $retailer_q; //15-3+12 = 
			  $x3 = ceil($tq / $retailer_q); // = 2, 3
			  $difPlus = ($x3*$retailer_q) - $tq; // 0.25 or 0.916
			  //$difMinus = $dif * $retailer_q;  //3, 11
			  $text = JText::_('PLG_SYSTEM_PRODUCTRESTRICTIONS_RETAILER_ERROR'); 
			  $text = str_replace('{RETAILER_Q}', $retailer_q, $text); 
			  $text = str_replace('{REMOVE_RETAILER_Q}', $difMinus, $text); 
			  $text = str_replace('{ADD_RETAILER_Q}', $difPlus, $text); 
			   $text = str_replace('{TOTAL_RETAILER_Q}', $tq, $text); 
			  static $errorShown; 
			  if (empty($errorShown)) {
				  $errorShown = true; 
				  $errorMsg = $text; 
				  return false; 
			  }
			  else {
				  $errorMsg = $text; 
				  return false; 
			  }
		  }
	  }
	   
	   
	}
	
	
	public function plgUpdateCustomPrice(&$product) {
		if (!JFactory::getApplication()->isSite()) return; 
		$this->handleWholesalers( $product);
	}
	
	function handleWholesalers( &$product=null) {
		
			if (!empty($product)) {
			 $this->overrideProduct($product); 
			}
			
			
			//$ids = $productModel->sortSearchListQuery (TRUE, $this->categoryId);
			$categoryId = JRequest::getInt('virtuemart_category_id', 0); 
			$productModel = VmModel::getModel('product');
			
			if (!empty($categoryId)) {
				$perRow = 1; 
				$imgAmount = VmConfig::get('prodimg_browse',1);
				$ids = $productModel->sortSearchListQuery (TRUE, $categoryId);
				$vmPagination = $productModel->getPagination($perRow);
				$orderByList = $productModel->getOrderByList($categoryId);
				$products = $productModel->getProducts ($ids);
				//$productModel->addImages($this->products['products'], $imgAmount );
				
			}
			
			if (class_exists('VirtueMartModelProduct')) {
				foreach (VirtueMartModelProduct::$_products as $kx=>$px) {
					$this->overrideProduct(VirtueMartModelProduct::$_products[$kx]); 
				}
			}
			
			
			
	}
	
	//$retValues = $dispatcher->trigger('plgVmOnCheckoutCheckStock', array(  &$this, &$product, &$quantity, &$errorMsg, &$adjustQ));
	function plgVmOnCheckoutCheckStock( &$cart, &$product, &$quantity, &$errorMsg, &$adjustQ, $inCheckout = false )
    {	
		if (!JFactory::getApplication()->isSite()) return; 
		if (empty($cart->products)) return; 
		if ($this->checkDisabled()) {
			
			if ($this->params->get('debug', false)) {
				   JFactory::getApplication()->enqueueMessage('DEBUG productrestrictions: plugin disabled for current shopper group');
			   }
			
			return null; 
		}
		
		if ($this->params->get('debug', false)) {
				   JFactory::getApplication()->enqueueMessage('DEBUG productrestrictions: checking product '.$product->product_name);
			   }
		
	
		$pid = $product->virtuemart_product_id; 

		$catid = $this->isRegulated($pid); 
		if (empty($catid)) {
				   JFactory::getApplication()->enqueueMessage('DEBUG productrestrictions: product is not regulated');
			return; 
		}
		
		if ($this->isRetailer($pid)) {
			
			if ($this->params->get('debug', false)) {
				   JFactory::getApplication()->enqueueMessage('DEBUG productrestrictions: user is retailer');
			   }
			
			$ret = $this->handleRetailers($cart, $product, $quantity, $errorMsg, $adjustQ, $inCheckout);
			
			return $ret; 
		}
		
		
		if ($this->isWholesaler($pid)) {
			
			
			return $this->handleWholesalers($product);
		}
		
		if (!$this->isDefault($pid)) return;
		
		
			  if ($this->params->get('debug', false)) {
				   JFactory::getApplication()->enqueueMessage('DEBUG productrestrictions: user is default user - not a retailer or wholesalers');
			   }
		

		
		
		$doc = JFactory::getDocument(); 
		
		
		 $e = '';
		 $qx = $quantity; 
		 $ret = $this->checkQuantity($cart, $product, $qx, $e); 
		
		if (empty($e)) {
			
		
		}
		
		
		
		 if (!empty($e)) {
			 
		 	 
			 
		       //if (!empty($errorMsg)) $errorMsg .= '<br />'; 
			   if ($errorMsg !== $e) {
			    $errorMsg .= $e; 
			   }
			   $adjustQ = true; 
			   $quantity = $qx; 
			   
			   if (!empty($errorMsg))
			   $this->last_error = $errorMsg; 
			   
			   return false; 
		      }
			  
			  
		if ($ret===false) {
			
		
			   
			
			   $adjustQ = true; 
			   $quantity = $qx; 
			   return false; 
		}
		
		
		
		$this->removePreviousErrors(); 
			   
		
		return null;
		
    }
	
	private function removePreviousErrors() {
		
		//this requires OPC !!!
		if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'removemsgs.php')) {
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'removemsgs.php');
		$msgs = array(); 
		$msgs[] = 'PLG_SYSTEM_PRODUCTRESTRICTIONS_MAXNUM_ERR';  
		$msgs[] =  'PLG_SYSTEM_PRODUCTRESTRICTIONS_CARTQERR';
		$msgs[] =  'PLG_SYSTEM_PRODUCTRESTRICTIONS_ADJUSTED';
		$msgs[] =  'PLG_SYSTEM_PRODUCTRESTRICTIONS_OQERR';
		
		OPCremoveMsgs::filterMsgs($msgs);  
		
		
		
		}
	}
	
	private function createTable() {
		$db = JFactory::getDBO(); 
	  $q = "CREATE TABLE IF NOT EXISTS `#__productrestrictions` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
  `virtuemart_category_id` int(11) NOT NULL,
  `virtuemart_country_id` int(11) NOT NULL,
  `d1` int(11),
  `d2` int(11),
  `d3` int(11),
  `d4` int(11),
  `data` text,
  `data2` longtext,
  `data3` longtext,
  `extra1` varchar(255),
  `extra2` varchar(255),
  PRIMARY KEY (`id`),
  KEY `virtuemart_category_id` (`virtuemart_category_id`),
  KEY `country_id` (`virtuemart_country_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;"; 
      $db->setQuery($q); 
      $db->query(); 

	}
	
	static $_done; 
	
	public function _getGeoCountry()
		{
			if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_geolocator'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'helper.php')) {
			include_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_geolocator'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'helper.php');
			if (class_exists('geoHelper')) 
			{
			$c = geoHelper::getCountry2Code();
			
			if ($c === 'UK') $c = 'GB'; 
			if ($c === 'EL') $c = 'GR'; 
			
			
			$arr = array('A1', 'A2', 'O1'); 
			if (in_array($c, $arr))  {
				
				
				return ''; 
			}
			
			return $c; 
			
			}
			
			
			}
			
			
			
			return ''; 

		}
	
	public static function loadLang() {
         JFactory::getLanguage()->load('plg_system_productrestrictions', dirname(__FILE__).DIRECTORY_SEPARATOR); 
		JFactory::getLanguage()->load('plg_system_productrestrictions', JPATH_ADMINISTRATOR); 	
	
	if (!class_exists('VmConfig'))
	{
		require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		VmConfig::loadConfig(); 
	}

	   
	   if (!class_exists('VirtueMartCart')) require(JPATH_SITE. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_virtuemart' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'cart.php');
	   
	   if (class_exists('vmLanguage')) {
		   if (method_exists('vmLanguage', 'loadJLang')) {
				vmLanguage::loadJLang('com_virtuemart', true);
		   }
	   }
	   
	}
	
	
	
	// BT vs Logged vs GeoIP
    function _getCustomerCountry($cart=null) {
	   
	   
		self::loadLang(); 
	
	   
	   if (empty($cart)) {
	     $cart = VirtueMartCart::getCart(); 
	   }
	   if ((isset($cart->BT)) && (!empty($cart->BT['virtuemart_country_id']))) return (int)$cart->BT['virtuemart_country_id']; 
	   
	   $userId = JFactory::getUser()->get('id'); 
	   $userId = (int)$userId; 
	   if (!empty($userId)) {
	   
	   $db = JFactory::getDBO(); 
	   $q = 'select * from `#__virtuemart_userinfos` where `virtuemart_user_id` = '.(int)$userId.' and address_type = "BT" limit 0,1'; 
	   
	   $db->setQuery($q); 
	   $res = $db->loadAssocList(); 
	   
	   
	  
	   if (!empty($res)) {
		 $cart->BT = array(); 
	     foreach ($res as $k=>$v) {
		   $cart->BT[$k] = $v; 
		 }
	   }
	   
	  return (int)$cart->BT['virtuemart_country_id']; 
	   
	   
	   }
	   
	   $c = $this->_getGeoCountry(); 
	   if (!empty($c)) {
		   
		   
	     $cid = $this->getCountryId($c); 
		 return $cid; 
	   }
	   
	   return; 
	   
	   
	 }
	 
	 public function getCountryId($country_2_code)
	{
		$db = JFactory::getDBO(); 
		$country_2_code = trim($country_2_code); 
		$country_2_code = strtoupper($country_2_code); 
		$q = "select `virtuemart_country_id` from `#__virtuemart_countries` where `country_2_code` = '".$db->escape($country_2_code)."' limit 0,1"; 
		$db->setQuery($q); 
		$cid = $db->loadResult();
		
		$cid = (int)$cid; 
		return $cid; 
	}
	 
	
	// accepts either $product object or the ID itself
	public function plgGetproductrestrictions($virtuemart_product_id, &$html) {
		   if (!JFactory::getApplication()->isSite()) return; 
		   if ($this->checkDisabled()) return; 
		   
		   if (is_object($virtuemart_product_id) && (isset($virtuemart_product_id->virtuemart_product_id))) $virtuemart_product_id = $virtuemart_product_id->virtuemart_product_id; 
	       
		   $cid = $this->_getCustomerCountry(); 
		   $cat_id = $this->isRegulated($virtuemart_product_id); 
		   
		   if (empty($cat_id)) return; 
		   $data = $this->getData($cat_id, false, $cid); 
		   
		   
		   
		   if (empty($data)) return; 
		   
		   
		   foreach ($data as $k=>$v) {
			   // clear unused: 
			  if ((empty($v['d1']) && (empty($v['d2'])))) unset($data[$k]); 
			}
			
	       self::loadLang(); 
	    
	       
			
			$layout = 'productrestrictions_fe'; 
			
			$root = Juri::root(); 
		    if (substr($root, -1) !== '/') $root .= '/'; 
			
			
			
			$path = self::getIncludePath($layout); 
			ob_start(); 
			if (!empty($path)) include($path); 
			$htmlZX = ob_get_clean(); 
			
			
			
			
			ob_start(); 
			$jsf = self::getIncludePath($layout.'.includes'); 
			if (!empty($jsf)) include($jsf); 
			$js = ob_get_clean(); 
			
			$html .= $htmlZX.$js; 
			
			if (empty(self::$_done)) self::$_done = array(); 
			self::$_done[$virtuemart_product_id] = $virtuemart_product_id; 
			
		   
	   
	}
	function listShopperGroups()
		{
		  $db = JFactory::getDBO(); 
		  $q = 'select * from #__virtuemart_shoppergroups where published = 1'; 
		  $db->setQuery($q); 
		  $res = $db->loadAssocList(); 
		  if (empty($res)) return array(); 
		  
		  foreach ($res as $k=>$row) {
			  $res[$k]['name'] = JText::_($row['shopper_group_name']); 
			  
		  }
		  return $res;
		  
		}
	public function checkDisabled() {
	  static $result; 
	  if (isset($result)) return $result; 
	  
	  $user_id = (int)JFactory::getUser()->get('id'); 
	  if (!empty($user_id)) {
		 $disabled_for_sg = $this->params->get('disabled_for_sg', array()); 
		 if (empty($disabled_for_sg)) { $result = false; return false; }
	     //virtuemart_vmuser_shoppergroups
		 $db = JFactory::getDBO(); 
		 $q = 'select `virtuemart_shoppergroup_id` from `#__virtuemart_vmuser_shoppergroups` where `virtuemart_user_id` = '.$user_id; 
		 $db->setQuery($q); 
		 $res = $db->loadAssocList(); 
		 foreach ($res as $row) {
			 $sg = (int)$row['virtuemart_shoppergroup_id']; 
			 if (in_array($sg, $disabled_for_sg)) {
			   { $result = true; 
			   
			   if ($this->params->get('debug', false)) {
				   JFactory::getApplication()->enqueueMessage('DEBUG productrestrictions: shopper group ID '.$sg.' in disabled shopper groups '.var_export($disabled_for_sg, true), 'notice');
			   }
			   
			   
			   
			   return true; 
			   }
			 }
		 }
	  }
	   $result = false; return false; 
	  
	}
	
	public function onContentPrepare($context, &$article, &$params, $page = 0)
	{
		if (!JFactory::getApplication()->isSite()) return; 
		if ($this->checkDisabled()) return; 
		$class = get_class($article); 
		
		
		if (($class == 'TableProducts') || (($class == 'stdClass') && (isset($article->virtuemart_product_id)))) {
		   //self::toObject($article); 
		   if (isset($article->virtuemart_product_id)) {
		    $virtuemart_product_id = (int)$article->virtuemart_product_id; 
			
			if (!empty(self::$_done[$virtuemart_product_id])) return; 
			
			$html = ''; 
			$this->plgGetproductrestrictions($virtuemart_product_id, $html); 
			
			$rtype = $this->params->get('rederingtype', false); 
			
			// default {tabs} or append
			if (empty($rtype)) {
			if (stripos($article->text, '{productrestrictions}')!==false) {
			  $article->text = str_replace('{productrestrictions}', $html, $article->text); 
			}
			else
			{
		      $article->text .= $html; 
			}
			}
			else {
				$rtype = (int)$rtype; 
				switch ($rtype) {
				  case 2: 
				    $article->text = $html.$article->text; 
					break; 
				  case 3: 
				    return; 
				  case 4: 
				     $article->text = str_replace('{productrestrictions}', $html, $article->text); 
					 break; 
				  default: 
				   return; 
				}
				
			}
			
			
			
			
		   
		}
	  }
	}
	
	function checkCompat() {
		
		
		if (!self::loadVM()) return false; 
	if (file_exists(JPATH_VM_ADMINISTRATOR.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'adminui.php')) {
	$x = file_get_contents(JPATH_VM_ADMINISTRATOR.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'adminui.php'); 
	
	
	if (strpos($x, 'plgVmBuildTabs')===false) {
		
		$newCode = '
		$dispatcher = JDispatcher::getInstance();
		$returnValues = $dispatcher->trigger(\'plgVmBuildTabs\', array(&$view, &$load_template));
		'; 
		$search = 'foreach ( $load_template as $tab_content => $tab_title ) {'; 
	    $count = 0;
		$x = str_replace($search, $newCode.$search, $x, $count); 
		if ($count > 0) {
			jimport( 'joomla.filesystem.file' );
		if (JFile::copy(JPATH_VM_ADMINISTRATOR.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'adminui.php', JPATH_VM_ADMINISTRATOR.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'adminui.opc_bck.php')!==false) {
		 JFile::write(JPATH_VM_ADMINISTRATOR.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'adminui.php', $x); 
		 JFactory::getApplication()->enqueueMessage(JText::_('COM_ONEPAGE_ADDED_SUPPORT_FOR_TABS')); 
			}
		}
		}
	}
	
	$x = VmConfig::get('enable_content_plugin', false); 
	if (empty($x)) {
	  self::loadLang(); 
	  JFactory::getLanguage()->load('com_virtuemart', JPATH_ADMINISTRATOR); 
	  JFactory::getApplication()->enqueueMessage(JText::_('PLG_SYSTEM_PRODUCT_TABS_ERROR').': <b>'.JText::_('COM_VIRTUEMART_ADMIN_CFG_ENABLE_CONTENT_PLUGIN').'</b>');  
	}
	
	}
	
	function updateTable() {
		if (self::tableExists('#__productrestrictions')) {
			$cols = self::getColumns('#__productrestrictions'); 
			 if (isset($cols['virtuemart_product_id'])) {
				 $db = JFactory::getDBO(); 
				 $q = 'drop table #__productrestrictions'; 
				 $db->setQuery($q); 
				 $db->query(); 
			 }
		}
	}
	
	static function tableExists($table)
	{
   
   
   $db = JFactory::getDBO();
   $prefix = $db->getPrefix();
   $table = str_replace('#__', '', $table); 
   $table = str_replace($prefix, '', $table); 
   $table = $db->getPrefix().$table; 
   
   // stAn, it's much faster to do a positive select then to do a show tables like...
    
   
   $q = "SHOW TABLES LIKE '".$table."'";
	   $db->setQuery($q);
	   $r = $db->loadResult();
	   if (!empty($r)) return true; 
	   
	return false;
	}
	 public static function getColumns($table) {
   if (!self::tableExists($table)) return array(); 
   $db = JFactory::getDBO();
   $prefix = $db->getPrefix();
   $table = str_replace('#__', '', $table); 
   $table = str_replace($prefix, '', $table); 
   $table = $db->getPrefix().$table; 
	 
   
   // here we load a first row of a table to get columns
   $db = JFactory::getDBO(); 
   $q = 'SHOW COLUMNS FROM '.$table; 
   $db->setQuery($q); 
   $res = $db->loadAssocList(); 
  
   $new = array(); 
   if (!empty($res)) {
    foreach ($res as $k=>$v)
	{
		
		$new[$v['Field']] = $v['Field']; 
	}
	
	return $new; 
   }
   
   return array(); 
   
   
 }
	
	
	function onExtensionAfterSave($tes2, $test) {
	  $this->updateTable(); 
	  $this->createTable(); 
	  $this->checkCompat(); 
	}
	private static function getIncludePath($layout) {
	   $paths = self::getIncludePaths($layout);
	   if (empty($paths)) return ''; 
	   return $paths[0]; 
	}
	private static function getIncludePaths($layout='') {
	   $ret = array(); 
	   $tp = JPATH_SITE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.JFactory::getApplication()->getTemplate().DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'plg_system_productrestrictions'.DIRECTORY_SEPARATOR; 
	  
	   if (file_exists($tp)) {
	      if (!empty($layout)) {
		     if (file_exists($tp.$layout.'.php')) $ret = array(0 => $tp.DIRECTORY_SEPARATOR.$layout.'.php'); 
		  }
		  else
		  {
			  $ret[] = $tp; 
		  }
	   }
	   if (empty($layout)) {
	     $ret[] = __DIR__.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR;
	   }
	   else
	   {
		   if (file_exists(__DIR__.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR.$layout.'.php')) {
		      $ret[] = __DIR__.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR.$layout.'.php'; 
		   }
	   }
	   
	   return $ret; 
	}
	
	private function loadVM() {
		/* Require the config */
		if (!file_exists(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'shopfunctionsf.php')) return false; 
	
	
		if (!class_exists( 'VmConfig' )) require(JPATH_ROOT.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');
		VmConfig::loadConfig();
		if(!class_exists('VmImage')) require(JPATH_ROOT.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'image.php'); 
		if(!class_exists('shopFunctionsF'))require(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'shopfunctionsf.php'); 
		
		if (!class_exists('VirtueMartCart')) require(JPATH_SITE. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_virtuemart' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'cart.php');
		self::loadLang(); 
		return true; 
	}
	/*
	public function plgVmOnDisplayProductFEVM3(&$product, &$group) {
		echo 'plgVmOnDisplayProductFEVM3'; var_dump($product->virtuemart_product_id); 
		return;
		die('plgVmOnDisplayProductFEVM3'); 
	}
	public function plgVmOnViewCartVM3(&$product, &$productCustom, &$html) {
		echo 'plgVmOnViewCartVM3'; var_dump($product->virtuemart_product_id); 
		return;
		die('plgVmOnViewCartVM3'); 
	}
	public function plgVmPrepareCartProduct(&$product, &$customField, $selected, &$mod = 0) {
		die('plgVmPrepareCartProduct'); 
		
	}
	*/
	private function overrideProduct(&$product=null) {
		self::loadLang(); 
		$user_id = JFactory::getUser()->get('id'); 
		
		/*
		$x = debug_backtrace(); $r = array(); 
		foreach ($x as $l) $r[] = $l['file'].' '.$l['line']; 
		var_dump($r); die(); 
		*/
		
		if (is_null($product)) { 
		$option = JRequest::getVar('option', ''); 
		$view = JRequest::getVar('view', ''); 
		$virtuemart_product_id = JRequest::getVar('virtuemart_product_id', ''); 
		
		
		if (($option === 'com_virtuemart') && ($view === 'productdetails') && (!empty($virtuemart_product_id))) {
			self::loadLang(); 
			$productModel = VmModel::getModel('product');
	        $product = $productModel->getProduct($virtuemart_product_id);
		}
		}
		
		
		if (empty($product)) {
			die('empty prod'); 
			return; 
		}
		$pid = $product->virtuemart_product_id; 
		$cat_id = $this->isRegulated($pid); 
		
		
		if (empty($cat_id)) return; 
		if (!empty($user_id)) {
		$cid = $this->_getCustomerCountry(); 
		$data = $this->getData($cat_id, false, $cid); 
		if (isset($data[0])) {
		$general_config = $data[0]; 
		
		
		
			$sgs = $this->getSG(); 
			
			
			foreach ($sgs as $sgid) {
				if (in_array($sgid, $general_config['sg_wholesalers'])) {
					if (!empty($general_config['wholesaler_q'])) {
						
						
						
					 $product->step_order_level = (int)$general_config['wholesaler_q'];
					 $product->max_order_level = $product->step_order_level * 10; 
					 $product->min_order_level = $product->step_order_level; 
					 if ($this->params->get('debug', false)) {
						JFactory::getApplication()->enqueueMessage('DEBUG  wholesale detected for product: '.$product->product_name);
					  }
					}
				}
			}
		
		
		}
		
		static $cartAdjusted; 
		if (empty($cartAdjusted)) {
			
			$cartAdjusted = true; 
			$cart = VirtueMartCart::getCart(); 
			if (!empty($cart->products))
			foreach ($cart->products as $ind=> &$productCart) {			
				$cat_id = $this->isRegulated($productCart->virtuemart_product_id); 
				if (!empty($cat_id)) {
					$data = $this->getData($cat_id, false, $cid); 
					if (isset($data[0])) {
						$general_config = $data[0]; 
						if (!empty($user_id)) {
						$sgs = $this->getSG(); 
						foreach ($sgs as $sgid) {
							if (in_array($sgid, $general_config['sg_wholesalers'])) {
							if (!empty($general_config['wholesaler_q'])) {
							$productCart->step_order_level = (int)$general_config['wholesaler_q'];
							$productCart->max_order_level = $productCart->step_order_level * 10; 
							$productCart->min_order_level = $productCart->step_order_level; 
							
							if ($this->params->get('debug', false)) {
								JFactory::getApplication()->enqueueMessage('DEBUG  wholesale detected for product: '.$productCart->product_name);
							}
							
							}
							}
						}
				   
				}
			}
		}
			}
		
		
		}
		}
		
		 
		
		
	}
	
	private function getSG($user_id=0) {
		if (empty($user_id)) {
		  $user_id = JFactory::getUser()->get('id'); 
		}
		if (!empty($user_id)) {
			$db = JFactory::getDBO(); 
			$q = 'select virtuemart_shoppergroup_id from #__virtuemart_vmuser_shoppergroups where virtuemart_user_id = '.(int)$user_id; 
			$db->setQuery($q); 
			$res = $db->loadAssocList(); 
			if (!empty($res)) {
			$ret = array(); 
			foreach ($res as $row) {
				$id = (int)$row['virtuemart_shoppergroup_id']; 
				$ret[$id] = $id; 
			}
			return $ret; 
			}
			
		}
		
			$db = JFactory::getDBO(); 
			$q = 'select virtuemart_shoppergroup_id from #__virtuemart_shoppergroups where `default` > 0'; 
			$db->setQuery($q); 
			$res = $db->loadAssocList(); 
			if (!empty($res)) {
			$ret = array(); 
			foreach ($res as $row) {
				$id = (int)$row['virtuemart_shoppergroup_id']; 
				$ret[$id] = $id; 
			}
			return $ret; 
			}
		return array(); 
		
	}
	
	public function plgVmBuildTabs(&$view, &$tabs)
	{
		
		if (!$this->_init()) return; 
		
		JFactory::getLanguage()->load('plg_system_productrestrictions', dirname(__FILE__).DIRECTORY_SEPARATOR); 
		JFactory::getLanguage()->load('plg_system_productrestrictions', JPATH_ADMINISTRATOR); 
		$class = get_class($view); 
		switch ($class)
		{
			case 'VirtuemartViewCategory': 
			
			  $virtuemart_category_id = $vmid = JRequest::getVar('virtuemart_category_id', JRequest::getVar('cid')); 
			  
			   
			  // unknown category ID: 
			  if (empty($virtuemart_category_id)) return; 
			  if (is_array($virtuemart_category_id)) $virtuemart_category_id = reset($virtuemart_category_id); 
			  $virtuemart_category_id = (int)$virtuemart_category_id; 

			/*
			 $db = JFactory::getDBO(); 
			 $q = 'select * from #__virtuemart_countries where published = 1'; 
			 $db->setQuery($q); 
			 $clist = $db->loadAssocList(); 
			 if (empty($clist)) return; 
			 */
			 
			 $clist = $this->getData($virtuemart_category_id); 

			
			 $t = 0; 
			 foreach ($clist as $data) {
				 
if (empty($data['d1'])) $data['d1'] = 0; 
else $data['d1'] = (float)$data['d1']; 
$t += $data['d1'];
if (empty($data['d2'])) $data['d2'] = 0; 
else $data['d2'] = (float)$data['d2']; 
$t += $data['d2'];
			 }
			 $totalregulated = $t; 
			 
			
				// $data = $this->getData($virtuemart_product_id); 
				 $paths = self::getIncludePaths(); 
				 
					  $tabs['productrestrictions'] = JTExt::_('PLG_SYSTEM_PRODUCTRESTRICTIONS'); 
				foreach ($paths as $p) {
				  $view->addTemplatePath( $p );
				}
				
				if (method_exists('vmLanguage', 'loadJLang')) {
				vmLanguage::loadJLang('com_virtuemart_config');
			    vmLanguage::loadJLang('com_virtuemart_shoppers',true);
				}
				
					  //$view->addTemplatePath( __DIR__.DIRECTORY_SEPARATOR.'tabs'.DIRECTORY_SEPARATOR.'product'.DIRECTORY_SEPARATOR );
					
					$sg = $this->listShopperGroups(); 
					$view->assignRef('opc_sg', $sg); 
					
					$retailer_q = ''; //to FINISH !
					$view->assignRef('retailer_q', $retailer_q); 
					
					
					$wholesaler_q = ''; //to FINISH !
					$view->assignRef('wholesaler_q', $wholesaler_q); 
					$generic_config = $clist[0]; 
					unset($clist[0]); 
					$view->assignRef('allcountries', $clist); 
					$view->assignRef('generic_config', $generic_config); 
					$view->assignRef('totalregulated', $t); 
					$view->assignRef('reg_virtuemart_category_id', $virtuemart_category_id); 
				//	$view->assignRef('tabdata', $data); 
				    //$view->assignRef('opc_forms', $forms); 
					
				 
			  
			  
			  
			  break; 
			  
		}
		
			  
	}
	
	public function getGeneralConfig($virtuemart_category_id) {
		 $db = JFactory::getDBO(); 
	   $q = 'select `data` from #__productrestrictions as r where r.virtuemart_category_id = '.(int)$virtuemart_category_id.' and r.virtuemart_country_id = 0 limit 1'; 
	   $db->setQuery($q); 
	   $json = $db->loadResult(); 
	   if (empty($json)) {
		   $json_data = array(); 
		   
				 $json_data['sg_wholesalers'] = array(); 
				 $json_data['sg_retailers'] = array(); 
				 $json_data['sg_default'] = array(); 
				 $json_data['retailer_q'] = (int)0;
				 $json_data['wholesaler_q'] = (int)0;
	   }
	   else {
	   $json_data = @json_decode($json, true); 
	   
	   if (!empty($json_data)) {
		   foreach ($json_data as $k=>$row) {
			   $json_data[$k] = $row; 
		   }
	   }
	   }
	   
	   return $json_data; 
	   
	}
	public function getData($virtuemart_category_id, $fill=true, $cid=-1) {
	   
		
		static $cache; 
		if (isset($cache[$virtuemart_category_id.'_'.$fill.'_'.$cid])) {
			return $cache[$virtuemart_category_id.'_'.$fill.'_'.$cid]; 
		}
		
		
		if ($cid === 0) {
			$general_config = $this->getGeneralConfig($virtuemart_category_id); 
			$cache[$virtuemart_category_id.'_'.$fill.'_'.$cid] = $general_config; 
			return $general_config;
		}
		
	   $db = JFactory::getDBO(); 
	   $q = 'select * from #__productrestrictions as r, #__virtuemart_countries as c where r.virtuemart_category_id = '.(int)$virtuemart_category_id.' and c.virtuemart_country_id = r.virtuemart_country_id and c.published = 1'; 
	   $db->setQuery($q); 
	   $resd = $db->loadAssocList(); 
	   
	  
	   
	   
	   if (empty($resd)) $resd = array(); 
	   
	   if ($fill) {
	   
	      $db = JFactory::getDBO(); 
		   $q = 'select * from #__virtuemart_countries where published = 1'; 
		   $db->setQuery($q); 
		   $clist = $db->loadAssocList(); 
	   }
	   else {
		   $cc = array(); 
	       foreach ($resd as $c) {
			   $c['virtuemart_country_id'] = (int)$c['virtuemart_country_id']; 
			   $cc[$c['virtuemart_country_id']] = $c['virtuemart_country_id']; 
		   }
		   if (!empty($cc)) {
		   $q = 'select * from #__virtuemart_countries where virtuemart_country_id in ('.implode(',', $cc).')'; 
		   $db->setQuery($q); 
		   $clist = $db->loadAssocList(); 
		   }
	   }
	   
	   
	   
	   
	   if (empty($clist)) {
		   $cache[$virtuemart_category_id.'_'.$fill.'_'.$cid] = null; 
		   return; 
	   }
	   
	   static $cS; 
	   
	   if (!empty($resd)) {
		   $nr = array(); 
	      foreach ($resd as $k=>$r) {
			  $nr[$r['virtuemart_country_id']] = $resd[$k]; 
			  
			  if ((empty($cid)) && (empty($cS))) {
			     $nr[$r['virtuemart_country_id']]['selected'] = 'selected'; 
				 $cS = true; 
			  }
			  else 
				if ((!empty($cid)) && ($cid > 0)) {
				   if ($r['virtuemart_country_id'] == $cid) {
				      $nr[$r['virtuemart_country_id']]['selected'] = 'selected'; 
				   }
				}
			  
		  }
		  
		  $resd = $nr; 
		  
		  
	   }
	      $resc = array(); 
		  foreach ($clist as $k=>$row) {
		   
		   $cid = (int)$row['virtuemart_country_id'];
		   $resc[$cid] = array(); 
		   $resc[$cid]['id'] = 0; 
		   $resc[$cid]['virtuemart_category_id'] = (int)$virtuemart_category_id; 
		   $resc[$cid]['virtuemart_country_id'] = (int)$row['virtuemart_country_id']; 
		   
		   $resc[$cid]['country_name'] = $row['country_name']; 
		   $resc[$cid]['country_3_code'] = $row['country_3_code']; 
		   
		   $resc[$cid]['d1'] = ''; 
		   $resc[$cid]['d2'] = ''; 
		   
		   if (empty($resd[$cid])) {
			   $resd[$cid] = $resc[$cid]; 
		   }
		   else {
		      $resd[$cid]['country_name'] = $row['country_name']; 
			  $resd[$cid]['country_3_code'] = $row['country_3_code']; 
		   }
		  }
	   
	   
	     $general_config = $this->getGeneralConfig($virtuemart_category_id); 
	     $resd[0] = $general_config; 
	   
	   $cache[$virtuemart_category_id.'_'.$fill.'_'.$cid] = $resd; 
	   return $resd; 
	   
	}
	
	private function isWholesaler($pid) {
		
		static $cache; 
		if (empty($cache)) $cache = array(); 
		if (isset($cache[$pid])) return $cache[$pid]; 
		
		
		$user_id = (int)JFactory::getUser()->get('id'); 
		if (empty($user_id)) {
			
			if ($this->params->get('debug', false)) {
				   JFactory::getApplication()->enqueueMessage('DEBUG productrestrictions: anonymous user');
			   }
			$cache[$pid] = false; 
			return false; 
		}
		$sgs = $this->getSG($user_id); 
		if (empty($sgs)) {
			
			  if ($this->params->get('debug', false)) {
				   JFactory::getApplication()->enqueueMessage('DEBUG productrestrictions: no shopper groups associated with the user');
			   }
			$cache[$pid] = false; 
			return false; 
		}
		
		$cat_id = $this->isRegulated($pid); 
		//var_dump($cat_id); die(); 
		if (empty($cat_id)) {
			
			if ($this->params->get('debug', false)) {
				   JFactory::getApplication()->enqueueMessage('DEBUG productrestrictions: product ID '.(int)$pid.' is not regulated');
			   }
			
			$cache[$pid] = false; 
			return false; 
		}
		
		$cid = $this->_getCustomerCountry(); 
		$data = $this->getData($cat_id, false, $cid); 
		
		if (isset($data[0])) {
		$general_config = $data[0]; 
		
			
			foreach ($sgs as $sgid) {
				if (in_array($sgid, $general_config['sg_wholesalers'])) {
					
				   if ($this->params->get('debug', false)) {
						JFactory::getApplication()->enqueueMessage('DEBUG productrestrictions: shopper group ID '.$sgid.' IS wholesale'); 
					}
					$cache[$pid] = true; 
					return true; 
					
				}
				else {
				    if ($this->params->get('debug', false)) {
						JFactory::getApplication()->enqueueMessage('DEBUG productrestrictions: shopper group ID '.$sgid.' is not wholesale'); 
					}
					
				}
			}
		
		
		}
		else {
			
			   if ($this->params->get('debug', false)) {
				   JFactory::getApplication()->enqueueMessage('DEBUG productrestrictions: no generic config exists for regulated category ID '.(int)$cat_id);
			   }

		}
		$cache[$pid] = false; 
		return false; 
		
	}
	
	private function isRetailer($pid) {
		
		static $cache; 
		if (empty($cache)) $cache = array(); 
		if (isset($cache[$pid])) return $cache[$pid]; 
		
		$user_id = (int)JFactory::getUser()->get('id'); 
		if (empty($user_id)) {
			$cache[$pid] = false; 
			return false; 
		}
		$sgs = $this->getSG($user_id); 
		if (empty($sgs)) {
			$cache[$pid] = false; 
			return false; 
		}
		
		$cat_id = $this->isRegulated($pid); 
		if (empty($cat_id)) {
			$cache[$pid] = false; 
			return false; 
		}
		
		$cid = $this->_getCustomerCountry(); 
		$data = $this->getData($cat_id, false, $cid); 
		
		if (isset($data[0])) {
		$general_config = $data[0]; 
		
			
			foreach ($sgs as $sgid) {
				if (in_array($sgid, $general_config['sg_retailers'])) {
					$cache[$pid] = true; 
					return true; 
					
				}
			}
		
		
		}
		$cache[$pid] = false; 
		return false; 
		
	}
	
	private function isDefault($pid) {
		
		static $cache; 
		if (empty($cache)) $cache = array(); 
		if (isset($cache[$pid])) return true; 
		
		
		$cat_id = $this->isRegulated($pid); 
		if (empty($cat_id)) {
			$cache[$pid] = false; 
			return false; 
		}
		
		$user_id = (int)JFactory::getUser()->get('id'); 
		if (empty($user_id)) {
			$cache[$pid] = true; 
			return true; 
		}
		
		$sgs = $this->getSG($user_id); 
		if (empty($sgs)) {
			$cache[$pid] = true; 
			return true; 
		}
		
		$cid = $this->_getCustomerCountry(); 
		$data = $this->getData($cat_id, false, $cid); 
		
		if (isset($data[0])) {
		$general_config = $data[0]; 
		
			
			foreach ($sgs as $sgid) {
				if (in_array($sgid, $general_config['sg_default'])) {
					$cache[$pid] = true; 
					return true; 
					
				}
			}
		
		
		}
		$cache[$pid] = false; 
		return false; 
		
	}
	
	
	
	public function onAfterRoute() {
		if (!JFactory::getApplication()->isSite()) return; 
		$this->onAfterRoute2(); 
		
		

		if (!self::loadVM()) return; 
		$this->handleWholesalers(); 
		
		
		
		if (!$this->_init()) {
		    
		   return; 
		}
		
		
		
		$x = JRequest::getVar('prodrestr_store_tab_content', false); 
		if (!empty($x)) {
			$cid = JRequest::getVar('virtuemart_category_id', JRequest::getVar('cid')); 
			if (is_array($cid)) $cid = reset($cid); 
			$post = JRequest::get('post'); 
			$remove = JRequest::getInt('prodrestr_remove_tab', false); 
			
			$remove = JRequest::getVar('regulated_disabled', -1); 
			
			
			$q = 'delete from #__productrestrictions where virtuemart_category_id = '.(int)$cid; 
			$db = JFactory::getDBO(); 
			$db->setQuery($q); 
			$db->query(); 
			
			if ($remove ==  $cid) {
				
				
				
				return; 
			}
			
		
			foreach ($post as $k=>$v) {
				
			   if (strpos($k, 'd1_')===0) {
				  
			     $id = str_replace('d1_', '', $k); 
				 $input = JFactory::getApplication()->input; 
				 $d1 = $input->get('d1_'.$id, ''); 
				 $d2 = $input->get('d2_'.$id, ''); 
				 
				
				 
				 if (empty($id)) continue; 
				 $this->insertUpdate($cid, $id, $d1, $d2, ''); 
				 
				 
			   }
			   
			}
			
			
			 $sg_retailers = JRequest::getVar('sg_retailers', array()); 
			
				 foreach ($sg_retailers as $k=>$r) {
					 $r = (int)$r; 
					 $sg_retailers[$k] = (int)$r; 
					 if (empty($r)) unset($sg_retailers[$k]); 
				 }
				 $sg_wholesalers = JRequest::getVar('sg_wholesalers', array()); 
				 
				  foreach ($sg_wholesalers as $k=>$r) {
					 $r = (int)$r; 
					 $sg_wholesalers[$k] = (int)$r; 
					 
					 if (empty($r)) unset($sg_wholesalers[$k]); 
				 }
				 
				 
				 
				 $sg_default = JRequest::getVar('sg_default', array()); 
				   foreach ($sg_default as $k=>$r) {
					 $r = (int)$r; 
					 $sg_default[$k] = (int)$r; 
					 if (empty($r)) unset($sg_default[$k]); 
				 }
				 
				 
				 $retailer_q = JRequest::getInt('retailer_q', 0); 
				 $wholesaler_q = JRequest::getInt('wholesaler_q', 0); 
				 
				 
				 $data = array(); 
				 $data['sg_wholesalers'] = $sg_wholesalers; 
				 $data['sg_retailers'] = $sg_retailers; 
				 $data['sg_default'] = $sg_default; 
				 $data['retailer_q'] = (int)$retailer_q; 
				 $data['wholesaler_q'] = (int)$wholesaler_q; 
				
				 $json_extra = json_encode($data); 
			     $this->insertUpdate($cid, 0, 0, 0, $json_extra); 
			
				
			
			
		}
	}
	
	private function insertUpdate($category_id, $vid, $d1, $d2, $json_extra) {
		
		
		
	   if (empty($d1)) $d1 = 'NULL'; 
	   if (empty($d2)) $d2 = 'NULL'; 
	   if (empty($category_id)) return; 
	   if ((empty($vid)) && (empty($json_extra))) return; 
	   
	   $db = JFactory::getDBO(); 
	   $q = 'select `id` from #__productrestrictions where virtuemart_country_id = '.(int)$vid.' and virtuemart_category_id = '.(int)$product_id.' limit 0,1'; 
	   $db->setQuery($q); 
	   $res = $db->loadAssoc(); 
	   
	   if (empty($res)) {
		 
		 if (empty($d1) && (empty($d2))) return; 
		   
	     $q = "insert into #__productrestrictions (`id`, `virtuemart_category_id`, `virtuemart_country_id`, `d1`, `d2`, `extra1`, `extra2`, `data`) ";
		 $q .= " values (NULL, ".(int)$category_id.", '".(int)$vid."', '".$db->escape($d1)."', '".$db->escape($d2)."', '', '', '".$db->escape($json_extra)."')"; 
		 $db->setQuery($q); 
		 $db->query(); 
		 
		
	   }
	   else
	   {
		   $id = (int)$res['id']; 
		   if (empty($d1) && (empty($d2))) {
		     $q = 'delete from #__product_restrictions where `id` = '.(int)$id; 
		   }
		   else {
			 if (empty($d1) && (empty($d2))) return; 
		    $q = "update #__productrestrictions set `d1` = '".$db->escape($d1)."', `d2` = '".$db->escape($d2)."' where `id` = ".(int)$id." and `virtuemart_category_id` = ".(int)$category_id." and `data` = '".$db->escape($json_extra)."'"; 
		   }
		  
		   $db->setQuery($q); 
		   $db->query(); 
	   }
	}
	
	/*helper functions*/
	private static function toObject(&$product, $recursion=0) {
    
	
	if (is_object($product)) {
	 $copy = new stdClass(); 
	 $attribs = get_object_vars($product); 
	 $isO = true; 
	}
	elseif (is_array($product)) {
		  $copy = array(); 
		  $isO = false; 
		  $attribs = array_keys($product); 
		  $copy2 = array(); 
		  foreach ($attribs as $zza=>$kka) {
		       if (strpos($kka, "\0")===0) continue;
			   $copy2[$kka] = $product[$kka]; 
		  }
		  $attribs = $copy2; 
		}
		
	
    foreach ($attribs as $k=> $v) {
		if (strpos($k, "\0")===0) continue;
		if ($isO) {
	      $copy->{$k} = $v; 	
		}
		else
		{
			$copy[$k] = $v; 
		}
		
		//if ($recursion < 5)
		if ((is_object($v)) && (!($v instanceof stdClass))) {
		   $recursion++; 
		   if ($isO) {
		     OPCmini::toObject($copy->{$k}, $recursion); 
		   }
		   else
		   {
			   OPCmini::toObject($copy[$k], $recursion); 
		   }
		}
		else
		{
			if (is_array($v)) {
			   $recursion++; 
			   if ($isO) {
		        OPCmini::toObject($copy->{$k}, $recursion); 
			   }
			   else
			   {
				   OPCmini::toObject($copy[$k], $recursion); 
			   }
			}
		}
		/*
		if (is_array($v)) {
		
		  $keys = array_keys($v); 
	  
		  foreach ($keys as $kk2=>$z2) {
		     if (strpos($z2, "\0")===0) continue;
			 $copy->{$k}[$z2] = $v[$z2]; 
			 if ((is_object($v[$z2])) && (!($v[$z2] instanceof stdClass))) {
				$recursion++; 
			    OPCmini::toObject($copy->{$k}[$z2]); 
			 }
			 else
			 if (is_array($v[$z2])) {
			    $recursion++; 
			    OPCmini::toObject($copy->{$k}[$z2]); 
			 }
			 
		  }
		}
		*/
		
		
	}
	$recursion--;
	$product = $copy; 
 }
 
 public function onAfterRoute2() {
	 return;
	 $option = JRequest::getVar('option', ''); 
	 $view = JRequest::getVar('view', ''); 
	 $format = JRequest::getVar('format', ''); 
	 $task = JRequest::getVar('task', ''); 
	 
	 
	 if (($option === 'com_virtuemart') && ($view === 'cart') && ($format === 'json') && ($task = 'addJS')) {
	  if (!defined('JPATH_COMPONENT')) {
		  define('JPATH_COMPONENT', JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'); 
	  }
	  if (!self::loadVM()) return false; 
	  if (VmConfig::get('use_as_catalog', 0)) return; 
	  
	  $virtuemart_product_ids = vRequest::getInt('virtuemart_product_id');
	  if (empty($virtuemart_product_ids)) return; 
	  $errorMsg = 0;
	  $cart = VirtueMartCart::getCart(); 
	  $products = $cart->add($virtuemart_product_ids, $errorMsg );
	  if (empty($products)) {
	  $json = new stdClass(); 
	  
	  $json->msg .= '<p>' . $this->last_error . '</p>';
	  $json->stat = '2';
	  //$doc = JFactory::getApplication()->getDocument(); 
	  $html = json_encode($json); 
	  echo $html; 
	  //$doc->setBuffer($html); 
	  jExit(); 
	  }
	  else {
		  return $this->addJS($products, $errorMsg); 
	  }
	 }
 }
	
 
     /**
	 * Add the product to the cart, with JS
	 * Z:\Winscp\scp05350\_root@92.240.237.203\srv\www\rupostel.com\web\vm2\purity\components\com_virtuemart\controllers\cart.php
	 * doesn't provide means to set/change error message and that is why we must use whole function to use the API
	 * @access public
	 */
	public function addJS($products, $errorMsg) {
		if(VmConfig::showDebug()) {
			VmConfig::$echoDebug = 1;
			ob_start();
		}
		$json = new stdClass();
		$cart = VirtueMartCart::getCart();
		if ($cart) {
			require_once(JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS.'controllers'.DS.'cart.php'); 
			$VirtueMartControllerCart = new VirtueMartControllerCart(); 
			
			$view = $VirtueMartControllerCart->getView ('cart', 'json');
			$virtuemart_category_id = shopFunctionsF::getLastVisitedCategoryId();

			$virtuemart_product_ids = vRequest::getInt('virtuemart_product_id');

			$view = $VirtueMartControllerCart->getView ('cart', 'json');
			$errorMsg = 0;

			//$products = $cart->add($virtuemart_product_ids, $errorMsg );


			$view->setLayout('padded');
			$json->stat = '1';

			if(!$products or count($products) == 0){
				$product_name = vRequest::get('pname');
				if(is_array($virtuemart_product_ids)){
					$pId = $virtuemart_product_ids[0];
				} else {
					$pId = $virtuemart_product_ids;
				}
				if($product_name && $pId) {
					$view->product_name = $product_name;
					$view->virtuemart_product_id = $pId;
				} else {
					$json->stat = '2';
				}
				$view->setLayout('perror');
			}

			$view->assignRef('products',$products);
			$view->assignRef('errorMsg',$errorMsg);

			if(!VmConfig::showDebug()) {
				ob_start();
			}
			$view->display ();
			$json->msg = ob_get_clean();
			if(VmConfig::showDebug()) {
				VmConfig::$echoDebug = 0;
			}
		} else {
			$json->msg = '<a href="' . JRoute::_('index.php?option=com_virtuemart', FALSE) . '" >' . vmText::_('COM_VIRTUEMART_CONTINUE_SHOPPING') . '</a>';
			$json->msg .= '<p>' . vmText::_('COM_VIRTUEMART_MINICART_ERROR') . '</p>';
			$json->stat = '0';
		}
		echo json_encode($json);
		jExit();
	}
 
 

}
/*
abstract class JTableObserverProducts extends JTableObserver {
	
}
*/