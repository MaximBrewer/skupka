<?php
/**
 *
 * Show the product prices
 *
 * @package    VirtueMart
 * @subpackage
 * @author Max Milbers, Valerie Isaksen
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2014 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: default_showprices.php 8024 2014-06-12 15:08:59Z Milbo $
 */
// Check to ensure this file is included in Joomla!
defined ('_JEXEC') or die('Restricted access');
$product = $viewData['product'];
$currency = $viewData['currency'];
?>
<div class="product-price" id="productPrice<?php echo $product->virtuemart_product_id ?>">
	<?php
	if (!empty($product->prices['salesPrice'])) {
		//echo '<div class="vm-cart-price">' . vmText::_ ('COM_VIRTUEMART_CART_PRICE') . '</div>';
	}

	if ($product->prices['salesPrice']<=0 and VmConfig::get ('askprice', 1) and isset($product->images[0]) and !$product->images[0]->file_is_downloadable) {
		$askquestion_url = JRoute::_('index.php?option=com_virtuemart&view=productdetails&task=askquestion&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id . '&tmpl=component', FALSE);
		?>
		<a class="ask-a-question bold" href="<?php echo $askquestion_url ?>" rel="nofollow" ><?php echo vmText::_ ('COM_VIRTUEMART_PRODUCT_ASKPRICE') ?></a>
		<?php
	} else {
	//if ($showBasePrice) {
		echo $currency->createPriceDiv ('basePrice', 'COM_VIRTUEMART_PRODUCT_BASEPRICE', $product->prices);
		//if (round($product->prices['basePrice'],$currency->_priceConfig['basePriceVariant'][1]) != $product->prices['basePriceVariant']) {
			echo $currency->createPriceDiv ('basePriceVariant', 'COM_VIRTUEMART_PRODUCT_BASEPRICE_VARIANT', $product->prices);
		//}

	//}
	echo $currency->createPriceDiv ('variantModification', 'COM_VIRTUEMART_PRODUCT_VARIANT_MOD', $product->prices);
	if (round($product->prices['basePriceWithTax'],$currency->_priceConfig['salesPrice'][1]) != $product->prices['salesPrice']) {
		echo '<span class="price-crossed" >' . $currency->createPriceDiv ('basePriceWithTax', 'COM_VIRTUEMART_PRODUCT_BASEPRICE_WITHTAX', $product->prices) . "</span>";
	}
	if($product->prices[salesPrice] != $product->prices[priceWithoutTax]) {
echo $currency->createPriceDiv('priceWithoutTax', 'Обычная цена: ', $product->prices);
echo $currency->createPriceDiv('salesPrice', 'Цена со скидкой:<br>', $product->prices);
} else {
echo $currency->createPriceDiv('salesPrice', 'COM_VIRTUEMART_PRODUCT_SALESPRICE', $product->prices);
echo $currency->createPriceDiv('priceWithoutTax', '', '');
}
	
	}
	?>
</div>

