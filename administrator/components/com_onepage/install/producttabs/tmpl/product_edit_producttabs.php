<?php

/* license: commercial ! */

defined('_JEXEC') or 	die( 'Direct Access to ' . basename( __FILE__ ) . ' is not allowed.' ) ;
include(__DIR__.DIRECTORY_SEPARATOR.'product_edit_producttabs.includes.php'); 

 $isdisabled = false; 
 $first = reset($this->tabdata); 
 if ($first['tabname'] === 'disablethis') {
	 $isdisabled = true; 
 }
 
?>
<div class="uk-form">
<fieldset class="data-uk-margin">
<legend><?php echo JText::_('PLG_SYSTEM_PRODUCTTABS_TAB_TITLE'); ?></legend>
<?php $i = 1; 

if ((!empty($this->tabdata['is_derived'])) || ($isdisabled)) {

if (!empty($this->tabdata['is_derived'])) {
?>
<div class=" uk-form-row ">
   <button class="uk-button uk-button-danger uk-width-1-1" onclick="return copyParent(this);"><?php echo JText::_('PLG_SYSTEM_PRODUCTTABS_FORCEMODIFY').'...'; ?></button>
</div>
<?php
}
?>
<div style="display: none;" id="all_derived_tabs">
<?php

}


foreach ($this->tabdata as $i2=>$data) { 
if ($i2 === 'is_derived') continue; 
$i = $data['id']; 

$tabname = $data['tabname']; 
if (empty($tabname)) $tabname = JText::_('PLG_SYSTEM_PRODUCTTABS_TAB_NEW'); 

?>

<h2 class="uk-article-title"><span class="allflags">&nbsp;</span><?php echo $tabname; ?></h2>

<div class=" uk-form-row ">
<label class="uk-form-label" for="tab_<?php echo $i; ?>"><?php echo JText::_('PLG_SYSTEM_PRODUCTTABS_TAB_NAME'); ?></label>
<div class="uk-form-controls">
<input type="text" value="<?php echo $this->escape($data['tabname']); ?>" placeholder="<?php echo JText::_('PLG_SYSTEM_PRODUCTTABS_TAB_NAME'); ?>" name="tabname_<?php echo $i; ?>" id="tabname_<?php echo $i; ?>" style="width:100%;" >
</div>
</div>
<div class="uk-form-row">
<label  class="uk-form-label" for="tab_<?php echo $i; ?>"><?php echo JText::_('PLG_SYSTEM_PRODUCTTABS_TAB_DESC'); ?></label>
<div class="uk-form-controls">
<input type="text" placeholder="<?php echo JText::_('PLG_SYSTEM_PRODUCTTABS_TAB_DESC'); ?>" name="tabdesc_<?php echo $i; ?>"  id="tabdesc_<?php echo $i; ?>" style="width:100%;" value="<?php echo $this->escape($data['tabdesc']); ?>" >
</div>
</div>
<div class=" uk-form-row">
<label class="uk-form-label" for="tab_<?php echo $i; ?>"><?php echo JText::_('PLG_SYSTEM_PRODUCTTABS_TAB_CONTENT'); ?></label>
<div class="uk-form-controls">
<?php 
 echo $this->editor->display('tabcontent_'.$i,  $data['tabcontent'], '100%;', '550', '75', '20', array('pagebreak', 'readmore') ) ; ?>
</div>
</div>

<div class="uk-form-row">
<label  class="uk-form-label" for="tab_<?php echo $i; ?>"><?php echo JText::_('PLG_SYSTEM_PRODUCTTABS_TAB_ORDERING'); ?></label>
<div class="uk-form-controls">
<input type="number" placeholder="<?php echo JText::_('PLG_SYSTEM_PRODUCTTABS_TAB_ORDERING'); ?>" name="tabordering_<?php echo $i; ?>"  id="tabordering_<?php echo $i; ?>" style="width:100%;" value="<?php echo $this->escape($data['ordering']); ?>" >
</div>
</div>

<div class=" uk-form-row">
<button class="uk-button uk-button-danger uk-width-1-1" onclick="return removeTab(<?php echo $i; ?>);"><?php echo JText::_('PLG_SYSTEM_PRODUCTTABS_TAB_REMOVE').' '.$tabname; ?></button>
</div>

<?php } ?>

<div class=" uk-form-row">
<?php 

//if ($this->current_lang === $this->default_lang) 
{
?>
<button class="uk-button  uk-button-success uk-width-1-1" onclick="return addNewTab(this);"><?php echo JText::_('PLG_SYSTEM_PRODUCTTABS_TAB_ADDNEW'); ?></button>
<?php 
}
/*
else {
	?><p><?php echo JText::_('PLG_SYSTEM_PRODUCTTABS_CANNOTADDTAB').' '.$default_lang; ?></p><?php
}
*/
?>

<button class="uk-button  uk-button-success uk-width-1-1" onclick="return removeAll(this);"><?php echo JText::_('PLG_SYSTEM_PRODUCTTABS_REMOVE'); ?></button>
</div>

<?php
if ((!empty($this->tabdata['is_derived'])) || ($isdisabled)) {
?>

</div>

<?php
if ((!empty($this->tabdata['is_derived']))) { ?>

<button class="uk-button  uk-button-success uk-width-1-1" onclick="return disableThis(this);"><?php echo JText::_('PLG_SYSTEM_PRODUCTTABS_DISABLE'); ?></button>
<?php 
}
else {
	?>
	<button class="uk-button  uk-button-success uk-width-1-1" onclick="return removeAll(this);"><?php echo JText::_('PLG_SYSTEM_PRODUCTTABS_REMOVE'); ?></button>
	<?php
}	
}
?>
<input type="hidden" name="sys_store_tab_content" value="<?php
if (!empty($this->tabdata['is_derived'])) { echo '0'; } else { echo '1'; }  
?>" id="sys_store_tab_content" />
<input type="hidden" name="sys_add_new_tab" value="0" id="sys_add_new_tab" />
<input type="hidden" name="sys_remove_tab" value="0" id="sys_remove_tab" />
<input type="hidden" name="detected_default_lang" id="detected_default_lang" value="<?php echo $this->escape($this->default_lang); ?>" />
</fieldset>
</div>