<?php // no direct access
defined('_JEXEC') or die('Restricted access');

//dump ($cart,'mod cart');
// Ajax is displayed in vm_cart_products
// ALL THE DISPLAY IS Done by Ajax using "hiddencontainer" ?>

<!-- Virtuemart 2 Ajax Card -->
<div class="vmCartModule <?php echo $params->get('moduleclass_sfx'); ?>" id="vmCartModule">
	<a href="index.php?option=com_virtuemart&view=cart" rel="nofollow" >
	<div class="cart_top">
      
        <table width="100" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td><div class="korzina_1"> </div></td>
            <td><div class="total_products">
		<?php 
			echo  $data->totalProduct;
		?>
		</div></td>
            <td><div class="total">	
				<?php if($data->totalProduct > 0){
				echo $data->billTotal;
				} ?>
			<?php 
				if($data->totalProduct < 1){
					echo '<span class="cart_empty">'.vmText::_('MOD_VIRTUEMART_CART').'</span>';
			}
			 ?>
		</div></td>
          </tr>
  </table>
     

		
		
	</div>
	</a>
	<?php
if ($show_product_list) {
	?>
	<div class="wrap-cart-content">
	    <div class="cart_content">
	    
	    <div id="hiddencontainer" style=" display: none; ">
	        <div class="vmcontainer">
	            <div class="product_row">
	                <span class="quantity"></span>&nbsp;x&nbsp;<span class="product_name"></span>
	                <div class="subtotal_with_tax" style="float: right;"></div>
	            <div class="customProductData"></div>
	            </div>
	        </div>
	    </div>
	    <div class="vm_cart_products">
	        <div class="vmcontainer">
	        <?php
	            foreach ($data->products as $product){
	                ?><div class="product_row">
	                    <div class="block-left">
	                        <span class="quantity"><?php echo  $product['quantity'] ?></span>&nbsp;x&nbsp;<span class="product_name"><?php echo  $product['product_name'] ?></span>
	                        <?php if ( !empty($product['customProductData']) ) { ?>
	                        <div class="customProductData"><?php echo $product['customProductData'] ?></div>
	                        <?php } ?>
	                    </div>
	                        <div class="subtotal_with_tax block-right">
	                        <?php echo $product['subtotal_with_tax'] ?>
	                        </div>					
	            </div>
	        <?php }
	        ?>
	        </div>
	    </div>
	    
	    
	    <div class="total">
	        <?php if($data->totalProduct > 0){
	            echo $data->billTotal;
	        } ?>
	    </div>
	    
	    <div class="cart_info">
	    <?php 
	    if($data->totalProduct < 1){
	    echo vmText::_('MOD_VIRTUEMART_CART_EMPTY');
	    }
	     ?>
	    </div>
	    <div class="show_cart">
	    <?php if ($data->totalProduct) echo  $data->cart_show; ?>
	    </div>
	    <div style="clear:both;"></div>
	    <div class="payments_signin_button" ></div>
	    <noscript>
	    <?php echo vmText::_('MOD_VIRTUEMART_CART_AJAX_CART_PLZ_JAVASCRIPT') ?>
	    </noscript>
	    </div>
	</div>
<?php } ?>
</div>
<script>
jQuery(document).ready(function(){
    jQuery('#vmCartModule').hover(
        function(){
            jQuery('.wrap-cart-content').stop().addClass('open'); 
        },
        function(){
            jQuery('.wrap-cart-content').stop().removeClass('open');  
        } 
    )
});
</script>
