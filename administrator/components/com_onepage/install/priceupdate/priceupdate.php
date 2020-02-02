<?php
/** 
 * @version		$Id: priceupdate.php$
 * @copyright	Copyright (C) 2005 - 2014 RuposTel.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
 

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');
if (!class_exists('calculationHelper')) {
  require_once(__DIR__.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'calculationh_override.php'); 
}

class plgSystemPriceupdate extends JPlugin
{
	public function __construct(&$subject, $config)
	{
		
		parent::__construct($subject, $config);
		
		
	}
	
	public function onAfterRoute() {
		$pid = JRequest::getInt('virtuemart_product_id', 0); 
		if (!empty($pid)) {
			$this->updateProductPrice($pid); 
		}
	}
	
	
	function plgVmOnCheckoutCheckStock( &$cart, &$product, &$quantity, &$errorMsg, &$adjustQ, $inCheckout )
    {
		
		$this->updateProductPrice($product->virtuemart_product_id); 
       
    }
	public function plgVmPrepareCartProduct(&$product, &$customfields, $selected, &$modificatorSum) {
		$this->updateProductPrice($product->virtuemart_product_id); 
	}
	
	
	public function updateProductPrice($virtuemart_product_id) {
		require_once(__DIR__.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR. 'oblksnurra.php' );
		
		
		
		if (!class_exists('VmConfig')) {
			if (!class_exists('VmConfig'))
			require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');
			VmConfig::loadConfig(); 
		}
		
		$productModel = VmModel::getModel('product'); 
		$product = $productModel->getProduct($virtuemart_product_id, true, true, 1); 
		return $this->plgVmUpdateProductPrice($product); 
	}
	
	public function plgVmUpdateProductPrice(&$product, $variant=0.0, $amount=0) {
		
		static $cache; 
		
		if (!JFactory::getApplication()->isSite()) return; 
		
		if (empty($product->selectedPrice)) $product->selectedPrice = 0; 
		if (empty($product->prices)) {
			if (isset($product->allPrices[0])) {
				$product->prices = $product->allPrices[0]; 
			}
			else {
			  return; 
			}
			
		}
		
		$product_price = $product->prices['product_price']; 
		$user_id = JFactory::getUser()->get('id'); 
		
		$testmode = true; 
		
		if ($testmode) {
			$price = 100; 			
		}
		else {
		if (empty($user_id)) return; 
		require_once(__DIR__.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR. 'oblksnurra.php' );
		$price = oblksnurra::getPris($product->virtuemart_product_id, $user_id , $product_price, $product->product_sku);

		}
		
		
		
		
		if ($price !== $product_price) {
			
			$product->allPrices[$product->selectedPrice]['product_price'] = $price; 
			$product->allPrices[$product->selectedPrice]['override'] = 0; 
			$product->allPrices[$product->selectedPrice]['product_override_price'] = 0; 
			$product->allPrices[$product->selectedPrice]['product_discount_id'] = 0; 
			$product->allPrices[$product->selectedPrice]['product_tax_id'] = $product->prices['product_tax_id']; 
			$product->allPrices[$product->selectedPrice]['product_currency'] = $product->prices['product_currency']; 
		}
		
		
	}
	public function plgVmInterpreteMathOp ($calculationHelper, $rule, $price,$revert){
		if (isset($calculationHelper->virtuemart_product_id)) {
			$this->updateProductPrice($calculationHelper->virtuemart_product_id); 
		}
	}
	
	public function plgVmInGatherEffectRulesBill(&$calculationHelper,&$rules){
		if (isset($calculationHelper->virtuemart_product_id)) {
			$this->updateProductPrice($calculationHelper->virtuemart_product_id); 
		}
		
	}
	
	public function plgVmInGatherEffectRulesProduct(&$calculationHelper,&$rules){
		if (isset($calculationHelper->virtuemart_product_id)) {
			$this->updateProductPrice($calculationHelper->virtuemart_product_id); 
		}
	}
	
	
}