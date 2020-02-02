<?php
/*
*
* @copyright Copyright (C) 2007 - 2013 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
* stAn note: Always use default headers for your php files, so they cannot be executed outside joomla security 
*
*/

defined( '_JEXEC' ) or die( 'Restricted access' );


?>

   ga('<?php echo $tracker_name.'.'; ?>ec:addImpression', {
      'id': '<?php echo $this->escapeSingle($pid); ?>',
      'name': '<?php echo $this->escapeSingle($product->product_name); ?>',
      'category': '<?php echo $this->escapeSingle($product->category_name ); ?>',
	  'brand': '',
	  'variant': '<?php echo $this->escapeSingle($product->product_sku); ?>',
      'price': <?php echo number_format($product->product_final_price, 2, '.', ''); ?>,
	  'position': 1,
      
   });
