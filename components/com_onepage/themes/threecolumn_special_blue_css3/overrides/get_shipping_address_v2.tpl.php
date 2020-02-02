<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 
/**
*
* @version $Id: get_shipping_address.tpl.php 1526 2008-09-15 19:21:43Z soeren_nb $
* @package VirtueMart
* @subpackage templates
* @copyright Copyright (C) 2007-2008 soeren - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
*
* http://virtuemart.net
*/

// this file renders one shipping address
// prior version 2.0.96 this file was not used
// the content of this file is moved from foreach of list_shipto_addresses.tpl.php
// 
// input parameters are 
	echo '<div class="sectiontableentry'.$i.'">';
	echo '<div class="op_radiowrapper">';
	
	$checked = '';

	//echo '<input type="radio" name="ship_to_info_id" id="' . $ST->virtuemart_userinfo_id . '" value="' . $ST->virtuemart_userinfo_id. '" '.$checked.' class="stradio"/>'."\n";
	
	echo '</div>'."\n";
	echo '<div class="op_labelwrapper">'."\n";
	echo '<label for="id'.$ST->virtuemart_userinfo_id.'">';
	// obsolete: $edit_link = 'index.php?option=com_virtuemart&view=user&task=editAddressSt&addrtype=ST&cid[]='.$uid.'&virtuemart_userinfo_id='.$ST->virtuemart_userinfo_id;
	//echo '<strong>' . $ST->address_type_name . "</strong> ";
	
	echo '<strong>' . $ST->address_type_name . "</strong> ";
		$edit_label = OPCLang::_('JACTION_EDIT'); 
	if ($edit_label == 'JACTION_EDIT') $edit_label = OPCLang::_('EDIT'); 

	
	echo '(<a href="'.$ST->edit_link.'">'.$edit_label.'</a>)'."\n";
	echo '<div>';
	
	
	foreach ($BTaddress as $item)
	{
	
	if (!empty($item['value']))
	{
	?>
         <div style="width: 100%; clear: both;">
           <div class="op_field_name" ><?php echo $item['title'] ?> </div>
           <div class="op_field_value">
           <?php
				echo $this->escape($item['value']);
           ?>
           </div>
        </div>
	<?php
	}
	}
	echo '</div>';
	echo '</label>
	</div>
	</div>'."\n";
	if($i == 1) $i++;
	elseif($i == 2) $i--;
