<?php

/* license: commercial ! */

defined('_JEXEC') or 	die( 'Direct Access to ' . basename( __FILE__ ) . ' is not allowed.' ) ;
include(__DIR__.DIRECTORY_SEPARATOR.'edit_usertabs.includes.php'); 

$data = $this->tabdata; 

?>
<div class="uk-form">
<fieldset class="data-uk-margin">
<legend><?php echo JText::_('PLG_SYSTEM_USERTABS_TAB_TITLE'); ?></legend>
<?php $i = 1; 
$db = JFactory::getDBO(); 
$q = 'select u.`first_name`, u.`last_name`, j.`id` from #__users as j, #__virtuemart_userinfos as u where u.`virtuemart_user_id` = j.`id` and u.address_type = "BT" '; 
$db->setQuery($q); 
$list = $db->loadAssocList(); 

 ?><select name="usertabs_users[]" multiple="multiple" class="vm-chzn-select" data-placeholder="None">
 <option value="">-- select --</option><?php
  foreach ($list as $i=>$row) {
	
	 ?><option value="<?php echo $row['id']; ?>"   <?php 
	 
	 if (!empty($data))
	 foreach ($data as $id) {
		 $id = (int)$id['authorized_user_id']; 
		 $rid = (int)$row['id']; 
		 if ($id === $rid) echo ' selected="selected" '; 
		 
		 
	 }
	 
	 ?>><?php echo htmlentities($row['first_name'].' '.$row['last_name']); ?></option>
	 <?php
 }
 ?>
 </select>
 <?php
?>
<div class=" uk-form-row">
<button class="uk-button  uk-button-success uk-width-1-1" onclick="return addNewTab(this);"><?php echo JText::_('JSAVE'); ?></button>
</div>

<input type="hidden" name="sys_store_tab_content_user" value="1" />


</fieldset>
</div>