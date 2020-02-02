<?php if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );

/*
*
* @copyright Copyright (C) 2007 - 2010 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/


echo $intro_article; 
 
//joomore hacked;
global $jmshortcode;
if (!empty($jmshortcode) && (is_object($jmshortcode)))
$lang = $jmshortcode->shortcode;


if (!empty($newitemid))
$Itemid = $newitemid;

echo $op_basket; // will show either basket/basket_b2c.html.php or basket/basket_b2b.html 

?>
<div class="continue_and_coupon">
<div class="continue_left"><span>&nbsp;</span>
<?php
if (empty($no_continue_link) && (!empty($continue_link)) && ($continue_link != '//')) { 
$cl = true;  ?>
<div class="continue_shopping2"><a href="<?php echo $continue_link ?>" class="continue_link2"><?php echo OPCLang::_('COM_VIRTUEMART_CONTINUE_SHOPPING') ?></a></div>
<?php 
} 
?>
</div>
<div class="coupon_right" <?php 
if (empty($cl)) {
  echo ' style="float: right;" '; 
}
 ?> >
<?php 
echo $op_coupon; 
?>
</div>
</div>
<div class="min_order_text"><?php echo $min_reached_text; ?></div>
