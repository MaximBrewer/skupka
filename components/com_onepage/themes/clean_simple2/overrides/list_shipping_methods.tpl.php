<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 

$counter_payment = 1;
foreach ($shipping as $html)
{
  echo '<div class="pay_box payment'.$counter_payment.'" style="clear: both;"><br />'; 
  echo $html;
  echo '</div>'; 
  $counter_payment++;
}