<?php
defined('_JEXEC') or die('Restricted access');

$url = JRoute::_ ('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' .$product->virtuemart_category_id);
$img = JURI::base(true).'plugins/system/vmquickview/assets/img/eye.svg';
?>

<div class="vmquickview-button" data-product-id="<?php echo $product->virtuemart_product_id; ?>" data-url="<?php echo $url; ?>" data-name="<?php echo $product->product_name; ?>" data-izimodal-open="#vmquickview"><img src="<?php echo $img; ?>" alt=""><?php echo JText::_('PLG_VM_SYSTEM_VMQUICKVIEW_BUTTON') ?></div>