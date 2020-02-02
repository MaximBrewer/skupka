<?php

/* license: commercial ! */

defined('_JEXEC') or 	die( 'Direct Access to ' . basename( __FILE__ ) . ' is not allowed.' ) ;
include(__DIR__.DIRECTORY_SEPARATOR.'product_edit_productrestrictions.includes.php'); 

?>
<div class="uk-form">
<fieldset class="data-uk-margin">

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



<input type="hidden" name="sys_store_tab_content" value="1" />
<input type="hidden" name="sys_add_new_tab" value="0" id="sys_add_new_tab" />
<input type="hidden" name="sys_remove_tab" value="0" id="sys_remove_tab" />

</fieldset>
</div>