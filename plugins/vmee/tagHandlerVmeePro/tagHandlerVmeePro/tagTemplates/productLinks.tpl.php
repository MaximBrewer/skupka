<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 
 
 
 * @copyright (C) 2014 Interamind LTD, http://www.interamind.com
 
**/
if(!empty($product_url_arr)){
		$prodidx = $prodCount = count($product_url_arr);
		foreach ($product_url_arr as $prod_id=>$product) {
		?>
			 <a href="<?php echo $product["href"]; ?>"><?php echo $product["link_text"]; ?></a>
		<?php 
			if($prodidx < $prodCount){
				echo '<br/>';
		}
		$prodidx--;
	}
}
?>