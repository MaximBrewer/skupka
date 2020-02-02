<?php
/**
 * @version		$Id: view.html.php 21705 2011-06-28 21:19:50Z RuposTel.com $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
$this->loadTemplate('header'); 

?><div id="vmMainPageOPC">
<h1><?php echo JText::_('COM_VIRTUEMART_USER_FORM_EDIT_BILLTO_LBL'); ?></h1>
<form action="<?php echo $this->action_url; ?>" method="post" name="adminForm" class="form-ivalidate" autocomplete="off">
<?php echo $this->registration_html; ?>


<?php
echo $this->fields_html; 


?>

 
 <?php echo $this->op_formvars; ?>
  <fieldset><legend><?php echo JText::_('COM_VIRTUEMART_SHOPPER_FORM_GROUP'); ?></legend>
	   <select name="virtuemart_shoppergroup_id[]" multiple="multiple" class="vm-chzn-select" style="min-width: 200px; min-height: 150px;">
	      


		  <?php foreach ($this->groups as $g)
		  {
		    echo '<option '; 
			echo $g['selected']; 
			echo ' value="'.$g['virtuemart_shoppergroup_id'].'">'.JText::_($g['shopper_group_name']).'</option>'; 
		  }
?> </select>
 </fieldset>
 <div style="float: left; clear: both;">
	<button id="confirmbtn_button" type="submit" class="submitbtn bandBoxRedStyle" autocomplete="off" <?php echo $this->op_onclick ?>   ><?php echo $this->button_lbl; ?></button>
 </div>
 
 </form>
</div>
 