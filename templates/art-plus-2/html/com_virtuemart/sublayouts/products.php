
<link rel="stylesheet" href="../../../css/vmf.css" type="text/css" />


<?php




defined('_JEXEC') or die('Restricted access');

$products_per_row = $viewData['products_per_row'];

$currency = $viewData['currency'];

$showRating = $viewData['showRating'];

$verticalseparator = " vertical-separator";

echo shopFunctionsF::renderVmSubLayout('askrecomjs');



$ItemidStr = '';

$Itemid = shopFunctionsF::getLastVisitedItemId();

if(!empty($Itemid)){

	$ItemidStr = '&Itemid='.$Itemid;

}

$ar_compare = array();
if(!empty($_SESSION['compare'])){
foreach($_SESSION['compare'] as $key=>$product_id){
     array_push($ar_compare,$product_id);
  }
}


foreach ($viewData['products'] as $type => $products ) {



	$rowsHeight = shopFunctionsF::calculateProductRowsHeights($products,$currency,$products_per_row);



	if(!empty($type) and count($products)>0){

		$productTitle = vmText::_('COM_VIRTUEMART_'.strtoupper($type).'_PRODUCT'); ?>


  <div class="<?php echo $type ?>-view">
    
    <h4><?php echo $productTitle ?></h4>
    
    <?php // Start the Output

    }



	// Calculating Products Per Row

	$cellwidth = ' width'.floor ( 100 / $products_per_row );



	$BrowseTotalProducts = count($products);



	$col = 1;

	$nb = 1;

	$row = 1;

    



	foreach ( $products as $product ) {



		// Show the horizontal seperator

		if ($col == 1 && $nb > $products_per_row) { ?>
    
    <?php }



		// this is an indicator wether a row needs to be opened or not

		if ($col == 1) { ?>
    
    
    <div class="row grid-view">
      
      
      
      
      
      <?php }



		// Show the vertical seperator

		if ($nb == $products_per_row or $nb % $products_per_row == 0) {

			$show_vertical_separator = ' ';

		} else {

			$show_vertical_separator = $verticalseparator;

		}



    // Show Products ?>
      
      <div class="product vm-col ">
        
        <div class="spacer">
          
         
                
        
              
         
            
         
          <div class="wmvo_otstyp">
            <div class="wmvo_vnewnaa_granica">
             <div class="vm-product-media-container">
              <div class="wmvo_izobrajenie">
                <div class="wmvo_izobrajenie2">
  <div class="main-image"><a title="<?php echo $product->product_name ?>" href="<?php echo $product->link.$ItemidStr; ?>">
    
    <?php

						echo $product->images[0]->displayMediaThumb('class="browseProductImage main-image"', false);

						?>
    
  </a></div>
                </div>
              </div>
               </div>
              <div class="vm-product-descr-container">
<div class="wmvo_nazvanie_kategoria"><div class="wmvo_nazvanie_kategoria_2"><?php echo JHtml::link ($product->link.$ItemidStr, $product->product_name); ?></div></div>
              <div class="product_sku_cat"><?php echo 'Артикул: ', $product->product_sku;?></div>
              <div class="product-rating">
                    <?php
                    JPluginHelper::importPlugin( 'content', 'vrvote' ); $dispatcher =& JDispatcher::getInstance(); $results = $dispatcher->trigger( 'vrvote', $product->virtuemart_product_id );

                    ?></div>
              <div class="wmvo_kratkoe_opisanie"> <?php if(!empty($rowsHeight[$row]['product_s_desc'])){ ?>

                    <?php // Product Short Description

						if (!empty($product->product_s_desc)) {

							echo shopFunctionsF::limitStringByWord ($product->product_s_desc, 9999, ' ...') ?><?php } ?><?php  } ?>
                    </div></div>
                    <div class="vm-product-detail-container">
                    <div class="vm-product-rating-container">
                    <?php echo shopFunctionsF::renderVmSubLayout('rating',array('showRating'=>$showRating, 'product'=>$product));
				if ( VmConfig::get ('display_stock', 1)) { ?>
                    <span><?php echo $product->stock->stock_tip ?></span>
                    <?php if (($product->product_in_stock - $product->product_ordered) > 1) {
                             echo '('.$product->product_in_stock.')';
                           }
                    ?>
                    <?php }
				        echo shopFunctionsF::renderVmSubLayout('stockhandle',array('product'=>$product));
				    ?>
                    </div>
                    <div class="wmvo_cena">
                      <?php //echo $rowsHeight[$row]['price'] ?>
                      
                      <?php

				      echo shopFunctionsF::renderVmSubLayout('prices',array('product'=>$product,'currency'=>$currency)); ?>
                    </div>
                    <div class="wmvo_kypit">
  <?php //echo $rowsHeight[$row]['customs'] ?>
                      
                      
                      <?php

//-				echo shopFunctionsF::renderVmSubLayout('addtocart',array('product'=>$product,)); // 

				echo shopFunctionsF::renderVmSubLayout('addtocart',array('product'=>$product,'rowHeights'=>$rowsHeight[$row], 'position' => array('ontop', 'addtocart'))); // 

			?>
                    </div>
                 <div class="wmvo_sravnit">
                      <?php                
                   $cl="";
                   if(in_array($product->virtuemart_product_id,$ar_compare)){
                       $cl="in-comparison";
                   }
                    ?> 
                      <span data-id="<?= $product->virtuemart_product_id ?>"  class="btn-compare fa fa-random <?= $cl ?> compa<?= $product->virtuemart_product_id ?>" data-link="/index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=<?php echo $product->virtuemart_product_id; ?>&compare=1&tack=add"></span></div>
                  <div class="wmvo_bistraj_pokypka"> <?php $link_=JURI::base().JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.$product->virtuemart_product_id.'&virtuemart_category_id='.$product->virtuemart_category_id); ?>
                       
                      {popup url="index.php?option=com_rsform&formId=12&tmpl=component" }{/popup}
                      <a data-link="<?php echo $link_;?>" data-name="<?=$product->product_name ?>"  class="tovar_zakaz" href="index.php?option=com_rsform&formId=12&tmpl=component"></a></div>
            <div class="wmvo_podrobnee">
  <?php 

				$link = empty($product->link)? $product->canonical:$product->link;

				echo JHtml::link($link.$ItemidStr,vmText::_ ( 'Подробнее...' ), array ('title' => $product->product_name ) );

				?>
            </div>
   </div>
            </div>
           
          </div>
          
          
        </div>
        
      </div>
      
      
      
      <?php

    $nb ++;



      // Do we need to close the current row now?

      if ($col == $products_per_row || $nb>$BrowseTotalProducts) { ?>
      
      <div class="clear"></div>
      
      </div>
    
    
    <?php

      	$col = 1;

		$row++;

    } else {

      $col ++;

    }

  }



      if(!empty($type)and count($products)>0){

        // Do we need a final closing row tag?

        //if ($col != 1) {

      ?>
    
    <div class="clear"></div>
    
  </div>


<?php

    // }

    }

  }

