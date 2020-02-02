<?php
/* OPC fix for awo coupons display */

defined('_JEXEC') or die( 'Restricted access' );
$cart = VirtuemartCart::getCart(); 
echo $cart->couponCode; 
