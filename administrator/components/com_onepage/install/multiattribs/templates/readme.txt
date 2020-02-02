This modification of the add-to-cart in VM will insert an encoded continue shopping URl for OPC and set the product URLs to the URL where they were added to the cart

see this portion inside the form: 


<input type="hidden" name="product_addtocart_url" value="<?php echo base64_encode(JURI::current()); ?>" />