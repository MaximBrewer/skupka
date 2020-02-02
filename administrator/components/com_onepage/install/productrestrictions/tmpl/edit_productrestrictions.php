<?php

/* license: commercial ! */

defined('_JEXEC') or 	die( 'Direct Access to ' . basename( __FILE__ ) . ' is not allowed.' ) ;
include(__DIR__.DIRECTORY_SEPARATOR.'edit_productrestrictions.includes.php'); 

?>
<div class="uk-form">


<fieldset class="data-uk-margin">
<p><?php echo JText::_('PLG_SYSTEM_PRODUCTRESTRICTIONS_CONFIG_DESC'); ?></p>
<?php 


?><label for="regulated_disabled"><input onclick="javascript: if (this.checked) { jQuery('#regulated_config').hide(); } else { jQuery('#regulated_config').show(); } " type="checkbox" value="<?php echo $this->reg_virtuemart_category_id; ?>" id="regulated_disabled" name="regulated_disabled" <?php  if (empty($this->totalregulated)) echo ' checked="checked" '; ?> /><?php echo JText::_('PLG_SYSTEM_PRODUCTRESTRICTIONS_NOTCONFIGURED'); ?></label>
<div id="regulated_config" <?php if (empty($this->totalregulated)) echo ' style="display: none;" '; ?> >



<fieldset class="data-uk-margin">
  <legend><?php echo JText::_('PLG_SYSTEM_PRODUCTRESTRICTIONS_RETAILER_CONFIG'); ?></legend>
  <p><?php echo JText::_('PLG_SYSTEM_PRODUCTRESTRICTIONS_RETAILER_CONFIG_DESC'); ?></p>
  
  <div class=" uk-form-row">
<label class="uk-form-label" for="sg_retailers"><?php echo JText::_('PLG_SYSTEM_PRODUCTRESTRICTIONS_SG'); ?></label>
<div class="uk-form-controls">
<select name="sg_retailers[]" id="sg_relatailer" multiple="multiple" >
<?php foreach ($this->opc_sg as $sg) { ?>
<option <?php $sgid = $sg['virtuemart_shoppergroup_id']; if (in_array($sgid, $this->generic_config['sg_retailers'])) { echo ' selected="selected" '; } ?> value="<?php echo $sg['virtuemart_shoppergroup_id']; ?>"><?php echo $sg['name']; ?></option>
<?php } ?>
</select>

 
</div>
</div>

<div class=" uk-form-row">
<label class="uk-form-label" for="tab_<?php echo $i; ?>"><?php echo JText::_('PLG_SYSTEM_PRODUCTRESTRICTIONS_RETAILERQ'); ?></label>
<div class="uk-form-controls">
<input type="text" placeholder="<?php echo JText::_('PLG_SYSTEM_PRODUCTRESTRICTIONS_RETAILERQ'); ?>" name="retailer_q" style="width:100%;" value="<?php if (!empty($this->generic_config['retailer_q'])) echo $this->escape($this->generic_config['retailer_q']); ?>" >
 
</div>
</div>


  
</fieldset>




<fieldset class="data-uk-margin">
  <legend><?php echo JText::_('PLG_SYSTEM_PRODUCTRESTRICTIONS_WHOLESALE_CONFIG'); ?></legend>
  <p><?php echo JText::_('PLG_SYSTEM_PRODUCTRESTRICTIONS_WHOLESALE_CONFIG_DESC'); ?></p>
  
  <div class=" uk-form-row">
<label class="uk-form-label" for="sg_wholesalers"><?php echo JText::_('PLG_SYSTEM_PRODUCTRESTRICTIONS_SG'); ?></label>
<div class="uk-form-controls">
<select name="sg_wholesalers[]" id="sg_wholesalers" multiple="multiple" >
<?php foreach ($this->opc_sg as $sg) { ?>
<option <?php $sgid = $sg['virtuemart_shoppergroup_id']; if (in_array($sgid, $this->generic_config['sg_wholesalers'])) { echo ' selected="selected" '; } ?> value="<?php echo $sg['virtuemart_shoppergroup_id']; ?>"><?php echo $sg['name']; ?></option>
<?php } ?>
</select>

 
</div>
</div>

<div class=" uk-form-row">
<label class="uk-form-label" for="tab_<?php echo $i; ?>"><?php echo JText::_('PLG_SYSTEM_PRODUCTRESTRICTIONS_WHOLESALEQ'); ?></label>
<div class="uk-form-controls">
<input type="text" placeholder="<?php echo JText::_('PLG_SYSTEM_PRODUCTRESTRICTIONS_WHOLESALEQ'); ?>" name="wholesaler_q" style="width:100%;" value="<?php  if (!empty($this->generic_config['wholesaler_q'])) echo $this->escape($this->generic_config['wholesaler_q']); ?>" >
 
</div>
</div>


  
</fieldset>

<fieldset class="data-uk-margin">
<div class=" uk-form-row">
<label class="uk-form-label" for="tab_<?php echo $i; ?>"><?php echo JText::_('PLG_SYSTEM_PRODUCTRESTRICTIONS_SG'); ?></label>
<div class="uk-form-controls">
<select name="sg_default[]" id="sg_default" multiple="multiple" >
<?php foreach ($this->opc_sg as $sg) { ?>
<option <?php $sgid = $sg['virtuemart_shoppergroup_id']; if (in_array($sgid, $this->generic_config['sg_default'])) { echo ' selected="selected" '; } ?> value="<?php echo $sg['virtuemart_shoppergroup_id']; ?>"><?php echo $sg['name']; ?></option>
<?php } ?>
</select>

 
</div>
</div>

<?php 


foreach ($this->allcountries as $i2=>$data) { 

$cn = JText::_($data['country_name']); 
$cid = (int)$data['virtuemart_country_id']; 
$cn3 = $data['country_3_code']; 

if (empty($data['d1'])) $data['d1'] = ''; 
if (empty($data['d2'])) $data['d2'] = ''; 

$i = $cid; 



 $tabname = $cn.'('.$cn3.')';
?>

<h2 class="uk-article-title"><?php echo $tabname; ?></h2>


<div class="uk-form-row">
<label  class="uk-form-label" for="tab_<?php echo $i; ?>"><?php echo JText::_('PLG_SYSTEM_PRODUCTRESTRICTIONS_MAXIMUMQUANTITYGENERAL'); ?></label>
<div class="uk-form-controls">
<input type="text" placeholder="<?php echo JText::_('PLG_SYSTEM_PRODUCTRESTRICTIONS_MAXIMUMQUANTITYGENERAL'); ?>" name="d1_<?php echo $i; ?>" style="width:100%;" value="<?php echo $this->escape($data['d1']); ?>" >
</div>
</div>
<div class=" uk-form-row">
<label class="uk-form-label" for="tab_<?php echo $i; ?>"><?php echo JText::_('PLG_SYSTEM_PRODUCTRESTRICTIONS_MAXIMUMQUANTITYPERUSER'); ?></label>
<div class="uk-form-controls">
<input type="text" placeholder="<?php echo JText::_('PLG_SYSTEM_PRODUCTRESTRICTIONS_MAXIMUMQUANTITYPERUSER'); ?>" name="d2_<?php echo $i; ?>" style="width:100%;" value="<?php echo $this->escape($data['d2']); ?>" >
 
</div>
</div>



<?php } ?>

</fieldset>

<input type="hidden" name="prodrestr_store_tab_content" value="1" />
<input type="hidden" name="prodrestr_add_new_tab" value="0" id="prodrestr_add_new_tab" />
<input type="hidden" name="prodrestr_remove_tab" value="0" id="prodrestr_remove_tab" />
</div>
</fieldset>
</div>