<?php defined('_JEXEC') or die; ?>
  <div class="vmqv-wrap product-container">
   <div class="vmqv-image">
       <ul id="vmqv-slider">
            <?php
            $start_image = 0;
            for ($i = $start_image; $i < count($product->images); $i++) {
                $image = $product->images[$i]; 
                ?>
                <li>
                <?php
                if($img == 1){
                    echo $image->displayMediaThumb("",false,"", true, false, false); 
                } else {
                    echo $image->displayMediaFull("",false,"", false);
                }
                ?>
                </li>
            <?php
            }
            ?>
        </ul>
   </div>
   <div class="vmqv-detail">
       <?php if($price): ?>
           <?php echo shopFunctionsF::renderVmSubLayout('prices',array('product'=>$product,'currency'=>$currency)); ?>
           <div class="clearfix"></div>
       <?php endif; ?>
       
       <?php if($sdesc): ?>
           <div class="product-s-desc">
               <?php echo nl2br($product->product_s_desc); ?>
           </div>
           <div class="clearfix"></div>
       <?php endif; ?>  
       
       <?php if($cart): ?>
           <div class="product-cart">
               <?php echo shopFunctionsF::renderVmSubLayout('addtocart',array('product'=>$product)); ?>
           </div>
           <div class="clearfix"></div>
       <?php endif; ?>      
       
       <?php if($stock || $sku || $mf) echo '<div class="vmqv-detail-info">'; ?>
          
        <?php if($stock): ?>
           <div class="product-stock">
               <?php echo shopFunctionsF::renderVmSubLayout('stockhandle',array('product'=>$product)); ?>
           </div>
           <div class="clearfix"></div>
       <?php endif; ?>
       
       <?php if($sku && $product->product_sku): ?>
           <div class="product-sku"><?php echo JText::_('COM_VIRTUEMART_PRODUCT_SKU').' '.$product->product_sku; ?></div>
           <div class="clearfix"></div>
       <?php endif; ?>
       
       <?php if($mf && $product->mf_name): ?>
           <div class="product-mf"><?php echo JText::_('COM_VIRTUEMART_PRODUCT_DETAILS_MANUFACTURER_LBL').' '.$product->mf_name; ?></div>
           <div class="clearfix"></div>
       <?php endif; ?>
       
       <?php if($stock || $sku || $mf) echo '</div>'; ?>
        
   </div>
   <div class="clearfix"></div>
   <?php if($desc): ?>
       <div class="product-desc">
           <?php echo $product->product_desc; ?>
       </div>
   <?php endif; ?>
</div>
<script>
jQuery(document).ready(function($) {
    var slider = $("#vmqv-slider").lightSlider({
        item:1,
        slideMargin:0,
        loop:true
    }); 
    
    $('.iziModal-button-fullscreen').click(function(){
        setTimeout(function(){
            slider.refresh();
        }, 1);
    });
});
</script>