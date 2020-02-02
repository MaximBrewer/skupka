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


<div class="fields_hmlt">
<?php
echo $this->fields_html; 


?>
</div>
 <div class="op_formvars">
 <?php echo $this->op_formvars; ?>
  </div>
 <div style="float: left; clear: both;">
	<button id="confirmbtn_button" type="submit" class="submitbtn bandBoxRedStyle" autocomplete="off" <?php echo $this->op_onclick; ?>   ><?php echo $this->button_lbl; ?></button>
 </div>
 
 </form>
</div>
<?php

