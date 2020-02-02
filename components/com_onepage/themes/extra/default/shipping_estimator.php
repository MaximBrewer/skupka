<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/*
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/




?>
<div class="estimator_wrap">
 <div class="left_wrap">
 <fieldset class="fieldset_estimator">
 <div class="title_wrap"><legend><h2><?php echo JText::_('COM_ONEPAGE_SHIPPING_QUOTE_ESTIMATE'); ?></h2></legend>
 </div>
 <div class="fields_wrap all_fields">
   <?php foreach ($eFields['fields'] as $field) { 
   
   ?>
   
   <div class="field_wrapper field_title">
   <div class="vr2">
   <div id="<?php echo addslashes($field['name']); ?>_div" class="formLabel ">
    <label for="<?php echo addslashes($field['name']); ?>">
    <?php echo JText::_($field['title']); ?>
	</label>
   </div>
  
   <div class="field_input formField" id="<?php echo addslashes($field['name']); ?>_input">
     <?php echo $field['formcode']; ?>
   </div>
   </div>
    </div>
   <?php } ?>
 </div>
 <div class="btn_wrap"><button class="estimator_button button buttonopc" onclick="return sEstimator.getRates(this);"><?php echo JText::_('COM_ONEPAGE_SHIPPING_GETQUOTE_BUTTON'); ?></button></div>
 </fieldset>
 </div>
 <div class="right_wrap">
 <fieldset class="fieldset_estimator">
  <div class="title_wrap"><legend><h2><?php echo JText::_('COM_ONEPAGE_SHIPPING_ESTIMATED_RATES'); ?></h2></legend>
 </div>
 <div id="shipping_rates_come_here">
  <p><?php echo JText::_('COM_ONEPAGE_SHIPPING_ESTIMATE_CLICK_REQUIRED'); ?></p>
 </div>
 </fieldset>
 </div>
 
</div>