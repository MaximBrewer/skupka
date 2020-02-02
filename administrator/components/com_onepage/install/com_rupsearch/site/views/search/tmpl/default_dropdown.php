<?php
/**
 * @package		RuposTel Ajax search pro
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2014 RuposTel.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;


$s = JText::_('SEARCH'); 
if ($s === 'SEARCH')
	$s = JText::_('JSEARCH_FILTER'); 
?>
<div class="vm_ajax_search_header2"><span class="s_kw"><?php echo $s.': '.$this->keyword; ?></span>
<?php if (!empty($this->next)) { ?><span class="has_next">
   <?php echo JText::_('COM_RUPSEARCH_HASNEXT'); ?>
  </span>
<?php } ?>
<a class="product_lnk_ajax" id="vm_ajax_search_link2<?php echo $this->myid; ?>" href="#" onclick="javascript: return hide_results_live('<?php echo $this->myid; ?>', false, true);"><?php echo JText::_('COM_VIRTUEMART_CLOSE'); ?>&nbsp;<i class="fa fa-times" ></i></a>
</div>
<?php 
$x = 1; 
$n = -1;



if (!empty($this->products))
foreach ($this->products as $n => $product) { 

$pa = new stdClass(); 
foreach ($product as $k=>$v)
{
	$pa->$k = $v; 
}
$product = $pa; 


		$html = ''; 
		 if (!empty($product->prices['salesPrice']))
		 {
		 $currency = CurrencyDisplay::getInstance();
		 $html = $currency->createPriceDiv ('salesPrice', 'COM_VIRTUEMART_PRODUCT_SALESPRICE', $product->prices);
		  }
		  $n++;
		  if ($x == 1) $x = 2; 
		  else $x = 1; 


?>
 <div id="vm_ajax_search_results2<?php echo $this->myid.'_'.$n; ?>" class="vm_ajax_search_row_<?php echo $x ?>" onmouseover="javascript:op_hoverme(this);"  onclick="javascript: aj_redirect('prod<?php echo $product->virtuemart_product_id.'_'.$this->myid; ?>');">
 
 <?php 
  
  
  $width = $this->module_params->get('image_width', 30); 
  $height = $this->module_params->get('image_height', 30); 
  
	//$width = 30; $height = 30;
	      $h2 = $height +1;
	      $pname = $product->product_name; 
		  $sku = $product->product_sku; 
			$url = 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.$product->virtuemart_product_id; 
			if (!empty($product->virtuemart_category_id))
			$url .= '&virtuemart_category_id='.$product->virtuemart_category_id; 
			
		    $href = JRoute::_($url); 
			$href = str_replace('modules/mod_vm_ajax_search/ajax/', '', $href);
			
			
			if (empty($product->product_thumb_image))
			{
			if (!empty($product->virtuemart_media_id))
				if (is_array($product->virtuemart_media_id))
				{
					$mediaid = reset($product->virtuemart_media_id); 
				}
				else
					if (is_numeric($product->virtuemart_media_id))
					{
						$mediaid = $product->virtuemart_media_id; 
					}
			else
			if (empty($product->product_thumb_image))
			{
			
			$dbj = JFactory::getDBO(); 
			
			$q = 'select virtuemart_media_id from #__virtuemart_product_medias where virtuemart_product_id = '.$product->virtuemart_product_id; 
			$dbj->setQuery($q); 
			$mediaid = $dbj->loadResult(); 
			
			}
			
			
			
			$product_thumb_image = rupResize_Image::getImageFile($mediaid);
			
			
			}
			else
			{
				$product_thumb_image = $product->product_thumb_image; 
			}
 
   ?>
	      <div class="image_wrap1" style="<?php if (empty($product_thumb_image)) echo 'display:none;'; ?>">
	      <div class="image_wrap2" style="width: <?php echo $width; ?>px; height: <?php echo $h2; ?>px; ">
		  <?php
		  if (!empty($product_thumb_image))
	      rupResize_Image::showImage($product_thumb_image, $width, $height); 
		  ?>&nbsp;
	      </div>
	      </div>



	      <div class="link_wrap1" style="<?php if (empty($product_thumb_image)) echo 'width:100%;'; ?>">
	      <?php $id = ' id="prod'.$product->virtuemart_product_id.'_'.$this->myid.'" '; ?>
	      <a class="product_lnk_ajax_text" style=" " href="<?php echo $href; ?>" <?php echo $id; ?> ><?php 
		  if (!empty($sku))
		  echo $sku.' - '; 
	  
	       echo $pname; ?></a>
		  
		  <?php 
		  if (!empty($html))
		  echo '<br />'.$html; 
		  ?>
	      </div>
		  
		  <input type="hidden" name="op_ajax_results" id="vm_ajax_search_results2_<?php echo $this->myid.'_value_'.$n; ?>" value="<?php echo $href; ?>" />
		  


	      </div>
 <?php
 
 } 
 if (empty($this->products))  {  ?>
 
 <div class="vm_ajax_search_row_1"><?php echo JText::_('NO RESULTS WERE FOUND'); ?></div>
 
 
 <?php } 
 
 