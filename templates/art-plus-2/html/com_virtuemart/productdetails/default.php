<link rel="stylesheet" href="../../../css/vmf.css" type="text/css" />

<?php
/**
 *
 * Show the product details page
 *
 * @package	VirtueMart
 * @subpackage
 * @author Max Milbers, Eugen Stranz, Max Galt
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2014 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: default.php 8842 2015-05-04 20:34:47Z Milbo $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/* Let's see if we found the product */
if (empty($this->product)) {
	echo vmText::_('COM_VIRTUEMART_PRODUCT_NOT_FOUND');
	echo '<br /><br />  ' . $this->continue_link_html;
	return;
}




echo shopFunctionsF::renderVmSubLayout('askrecomjs',array('product'=>$this->product));



if(vRequest::getInt('print',false)){ ?>
<body onLoad="javascript:print();">
<?php } ?>

<div class="spacer">
  <div class="productdetails-view productdetails">
    <div class="tovar_izpbrajenie">
      <?php
echo $this->loadTemplate('images');
?>
      <?php
	$count_images = count ($this->product->images);
	if ($count_images > 1) {
		echo $this->loadTemplate('images_additional');
	}

	// event onContentBeforeDisplay
	echo $this->product->event->beforeDisplayContent; ?>
    </div>
    <div class="tovar_right_bloc">
      <div class="tovar_nazvanie"><?php echo $this->product->product_name ?></div>
      <div class="product_sku">Артикул: <?php echo $this->product->product_sku ?></div>
      <div class="kratkoe_opisanie_v_tovare"><?php echo $this->product->product_s_desc ?></div>123
<div class="tovar_zakaz_v_klik"><?php $link_=JURI::base().JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.$product->virtuemart_product_id.'&virtuemart_category_id='.$product->virtuemart_category_id); ?>
                       
                      {popup url="index.php?option=com_rsform&formId=12&tmpl=component" }{/popup}
                     
        <a data-link="<?php echo $link_;?>" data-name="<?=$this->product->product_name ?>"  class="tovar_zakaz" href="index.php?option=com_rsform&formId=12&tmpl=component"></a></div>
      <div class="product-rating">
              <?php
JPluginHelper::importPlugin( 'content', 'vrvote' ); $dispatcher =& JDispatcher::getInstance(); $results = $dispatcher->trigger( 'vrvote', $this->product->virtuemart_product_id );
?>
              </div>
              <div class="tovar_cena">
  <?php
		
		echo shopFunctionsF::renderVmSubLayout('prices',array('product'=>$this->product,'currency'=>$this->currency));
		?> 
      </div>
      <div class="tovar_v_korziny">
  <?php
		echo shopFunctionsF::renderVmSubLayout('addtocart',array('product'=>$this->product));

		echo shopFunctionsF::renderVmSubLayout('stockhandle',array('product'=>$this->product));

		?>
      </div>
      <div class="socseti">
  <div class="share42init"></div>
<script type="text/javascript" src="share42/share42.js"></script>
      </div>
      <div class="tovar_sravnenie">
  <?php 
              $cl="";
              if(!empty($_SESSION['compare'])){            
              if (isset($_SESSION['compare'][$this->product->virtuemart_product_id])) {
                   $cl="in-comparison";
              }              
              }
           ?><span data-id="<?= $this->product->virtuemart_product_id ?>" style="position: relative;" class="btn-compare fa fa-random <?= $cl ?> compa<?=$this->product->virtuemart_product_id ?>" data-link="/index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=<?php echo $this->product->virtuemart_product_id; ?>&compare=1&tack=add"></span></div>
      <div class="tovar_v_nalicei">
  <?php /*?><td><?php
        // Availability
        $stockhandle = VmConfig::get('stockhandle', 'none');
        $product_available_date = substr($this->product->product_available_date,0,10);
        $current_date = date("Y-m-d");
        if (($this->product->product_in_stock - $this->product->product_ordered) < 1) {
 
        echo '<div class="nal1">Нет в наличии</div>';
 
        }
        else {
        ?>   <div class="availability">
                <?php echo '<div class="nal">В наличии</div>'.'('.$this->product->product_in_stock.')'; ?>
            </div>
        <?php
        }
        ?></td><?php */?>
      </div>
      <div class="tovar_proizvoditel">
  <?php
		// Manufacturer of the Product
		if (VmConfig::get('show_manufacturers', 1) && !empty($this->product->virtuemart_manufacturer_id)) {
		    echo $this->loadTemplate('manufacturer');
		}
		?>
      </div>
      <div class="tovar_nazad_v_kategoriu">
  <?php /*?><?php // Back To Category Button
	if ($this->product->virtuemart_category_id) {
		$catURL =  JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id='.$this->product->virtuemart_category_id, FALSE);
		$categoryName = vmText::_($this->product->category_name) ;
	} else {
		$catURL =  JRoute::_('index.php?option=com_virtuemart');
		$categoryName = vmText::_('COM_VIRTUEMART_SHOP_HOME') ;
	}
	?>
	
    	<a href="<?php echo $catURL ?>"  title="<?php echo $categoryName ?>"><?php echo vmText::sprintf('COM_VIRTUEMART_CATEGORY_BACK_TO',$categoryName) ?></a><?php */?>
      </div>
      <div class="tovar_navigacia_po_tovaram">
  <?php
    // Product Navigation
    if (VmConfig::get('product_navigation', 1)) {
	?>
        <div class="product-neighbours">
          <?php
	    if (!empty($this->product->neighbours ['previous'][0])) {
		$prev_link = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->product->neighbours ['previous'][0] ['virtuemart_product_id'] . '&virtuemart_category_id=' . $this->product->virtuemart_category_id, FALSE);
		echo JHtml::_('link', $prev_link, $this->product->neighbours ['previous'][0]
			['product_name'], array('rel'=>'prev', 'class' => 'previous-page','data-dynamic-update' => '1'));
	    }
	    if (!empty($this->product->neighbours ['next'][0])) {
		$next_link = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->product->neighbours ['next'][0] ['virtuemart_product_id'] . '&virtuemart_category_id=' . $this->product->virtuemart_category_id, FALSE);
		echo JHtml::_('link', $next_link, $this->product->neighbours ['next'][0] ['product_name'], array('rel'=>'next','class' => 'next-page','data-dynamic-update' => '1'));
	    }
	    ?>
          
          </div>
        <?php } // Product Navigation END
    ?>
      </div>
    </div>
    <div class="cl_bo"></div>
    <div class="tovar_vkladki"><?php
$comments = JPATH_SITE . '/components/com_jcomments/jcomments.php';
  if (file_exists($comments)) {
    require_once($comments);
    $options = array();
    $options['object_id'] = $this->product->virtuemart_product_id;
    $options['object_group'] = 'com_virtuemart';
    $options['published'] = 1;
    $count = JCommentsModel::getCommentsCount($options);
  }
?>
          <!-- Nav tabs -->
          <ul class="nav nav-tabs" role="tablist">
            <li class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Описание</a></li>
            <li><a href="#reviews" aria-controls="reviews" role="tab" data-toggle="tab">Отзывы (<?php echo $count; ?>)</a></li>
      </ul>
          
          <!-- Tab panes -->
          <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade in active" id="home">
              <?php echo $this->product->product_desc; ?>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="reviews">
              <?php // onContentAfterDisplay event
        echo $this->product->event->afterDisplayContent; 
 
        $comments = JPATH_ROOT . '/components/com_jcomments/jcomments.php';
            if (file_exists($comments)) {
                require_once($comments);
                echo JComments::showComments($this->product->virtuemart_product_id, 'com_virtuemart', $this->product->product_name);
            }
        ?>
            </div>
    </div></div>
<?php // afterDisplayTitle Event
    echo $this->product->event->afterDisplayTitle ?>
    
    
    <?php
    // PDF - Print - Email Icon
    if (VmConfig::get('show_emailfriend') || VmConfig::get('show_printicon') || VmConfig::get('pdf_icon')) {
	?>
    <div class="icons">
      <?php

	    $link = 'index.php?tmpl=component&option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->product->virtuemart_product_id;

		echo $this->linkIcon($link . '&format=pdf', 'COM_VIRTUEMART_PDF', 'pdf_button', 'pdf_icon', false);
	    //echo $this->linkIcon($link . '&print=1', 'COM_VIRTUEMART_PRINT', 'printButton', 'show_printicon');
		echo $this->linkIcon($link . '&print=1', 'COM_VIRTUEMART_PRINT', 'printButton', 'show_printicon',false,true,false,'class="printModal"');
		$MailLink = 'index.php?option=com_virtuemart&view=productdetails&task=recommend&virtuemart_product_id=' . $this->product->virtuemart_product_id . '&virtuemart_category_id=' . $this->product->virtuemart_category_id . '&tmpl=component';
	    echo $this->linkIcon($MailLink, 'COM_VIRTUEMART_EMAIL', 'emailButton', 'show_emailfriend', false,true,false,'class="recommened-to-friend"');
	    ?>
      <div class="clear"></div>
      </div>
    <?php } // PDF - Print - Email Icon END
    ?>
    
    
    <?php
    

	echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'ontop'));
    ?>
    
    <div class="vm-product-container">
      
      <div class="vm-product-details-container">
        <div class="spacer-buy-area">
          </div>
      </div>
      <div class="clear"></div>
      
      
      </div>
    
    
    
    
    <?php
    

	echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'normal'));

    // Product Packaging
    $product_packaging = '';
    if ($this->product->product_box) {
	?>
    <div class="product-box">
      <?php
	        echo vmText::_('COM_VIRTUEMART_PRODUCT_UNITS_IN_BOX') .$this->product->product_box;
	    ?>
      </div>
    <?php } // Product Packaging END ?>
    
    <?php 
	echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'onbot'));

    echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'related_products','class'=> 'product-related-products','customTitle' => true ));

	echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'related_categories','class'=> 'product-related-categories'));

	?>
    
  <?php // onContentAfterDisplay event


$j = 'jQuery(document).ready(function($) {
	Virtuemart.product(jQuery("form.product"));

	$("form.js-recalculate").each(function(){
		if ($(this).find(".product-fields").length && !$(this).find(".no-vm-bind").length) {
			var id= $(this).find(\'input[name="virtuemart_product_id[]"]\').val();
			Virtuemart.setproducttype($(this),id);

		}
	});
});';
//vmJsApi::addJScript('recalcReady',$j);

/** GALT
	 * Notice for Template Developers!
	 * Templates must set a Virtuemart.container variable as it takes part in
	 * dynamic content update.
	 * This variable points to a topmost element that holds other content.
	 */
$j = "Virtuemart.container = jQuery('.productdetails-view');
Virtuemart.containerSelector = '.productdetails-view';";

vmJsApi::addJScript('ajaxContent',$j);

echo vmJsApi::writeJS();
?> </div>
</div>
<?php /*?><?php
echo $this->product->event->afterDisplayContent; 
$comments = JPATH_ROOT . '/components/com_jcomments/jcomments.php';
if (file_exists($comments)) {
require_once($comments);
echo JComments::showComments($this->product->virtuemart_product_id, 'com_virtuemart', $this->product->product_name);
}
?><?php */?>


