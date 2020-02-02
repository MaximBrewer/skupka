<?php
///////////////////////////////////////////////////////////////////////////////
// Copyright 2010 InteraMind Advanced Analytics, http://www.interamind.com
// This file is part of InteraMind VM Emails Manager
//
///////////////////////////////////////////////////////////////////////////////

defined( '_JEXEC' ) or die( 'Restricted access' );
/**
* @copyright    Copyright (C) 2009 InteraMind Advanced Analytics. All rights reserved.

**/

?>
<table width="100%" align="center" border="0" cellspacing="0" cellpadding="10">
  <tr valign="top">
  <?php 
  if($storeAddressLogoStyle == 0 || $storeAddressLogoStyle == 2){
  ?>
  <td align="left" class="Stil1"><?php echo $vendor->vendor_store_name; ?></td>
  <?php 
  }?> 
  <?php 
  if($storeAddressLogoStyle == 1 || $storeAddressLogoStyle == 2){
  ?>
  <td align="right"><img border="0" src="<?php echo JUri::root() . $vendor->images[0]->file_url ?>"></td>
  <?php 
  }?> 
  </tr>   
</table>
