<?php
/* license: commercial ! */
defined('_JEXEC') or 	die( 'Direct Access to ' . basename( __FILE__ ) . ' is not allowed.' ) ;
include(__DIR__.DIRECTORY_SEPARATOR.'productrestrictions_fe.includes.php'); 
JHtml::_('bootstrap.framework');

?>
<div class="vmMainPageOPCTabs container">
     <h3><?php echo JText::_('PLG_SYSTEM_PRODUCTRESTRICTIONS_LABEL'); ?></h3>
	 <p class="row col-md-12"><?php echo JText::_('PLG_SYSTEM_PRODUCTRESTRICTIONS_DESC'); ?></p>
	 <div class="row col-md-12">
	 <div class="span12 col-md-12">
	 <select name="country_list" onchange="javascript:changeCountryNotice(this)">
	   <?php 
	   
	   $s = false; 
	   foreach ($data as $k=>$r) {
	     echo '<option '; 
		 if (!empty($r['selected'])) { 
		 
		 
		 echo ' selected="selected" '; 
		 $s = true; 
		 
		 }
		 echo ' value="'.$r['virtuemart_country_id'].'">'.htmlentities($r['country_name']).'</option>'; 
	   }
	   ?>
	 </select>
	 </div>
	<?php foreach ($data as $k=>$r) { 
	  $vid = (int)$r['virtuemart_country_id']; 
	?>
	
	<div class="countrylistdata col-md-12" id="country_data_<?php echo $vid; ?>"  <?php if ((empty($r['selected']))) { if (!empty($s)) { echo ' style="display: none;" '; } $s = true; } ?> >
	  <div class=" col-md-12"><div class="span6 col-md-6"><?php echo JText::_('PLG_SYSTEM_PRODUCTRESTRICTIONS_TOTALINCART'); ?></div><div class="span6 col-md-6"><?php echo $r['d1']; ?></div></div>
	  <div class=" col-md-12"><div class="span6 col-md-6"><?php echo JText::_('PLG_SYSTEM_PRODUCTRESTRICTIONS_TOTALYEAR'); ?></div><div class="span6 col-md-6"><?php echo $r['d2']; ?></div></div>
	
	</div>  	
	
	<?php } ?> 
	</div>
	
	
	
	
</div>