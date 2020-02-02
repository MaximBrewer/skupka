<?php if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );

/*
*
* @copyright Copyright (C) 2007 - 2010 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
* THIS FILE RENDERS THE SELECT DROP DOWN FOR LOGGED IN USERS
*
* TO FORMAT INDIVIDUAL ADDRESS USE get_shipping_address_v2.tpl.php
* TO FORMAT EDITING OF THE ADDRESS USE list_user_fields_shipping.tpl.php WHICH IS USED FOR UNLOGGED AS WELL
*
* This file is loaded from \components\com_onepage\helpers\loader.php function getUserInfoST
*/

$html3 = '<div class="before_select"></div><div class="middle_select" style=""><div class="after_select" style="" >&nbsp;</div><select data-role="none" data-enhance="false" class="" name="ship_to_info_id" id="id'.$virtuemart_userinfo_id.'" onchange="return Onepage.changeST(this);" >'; 
				$html3 .= '<option value="'.$virtuemart_userinfo_id.'">'.OPCLang::_('COM_VIRTUEMART_USER_FORM_ST_SAME_AS_BT').'</option>';
				foreach ($STaddressList as $stlist)
				{
				  $html3 .= '<option value="'.$stlist->virtuemart_userinfo_id.'">';
				  if (!empty($stlist->address_type_name)) $html3 .= $stlist->address_type_name;
				  
				   
				     /*
				     if (isset($stlist->first_name))
				     $html3 .= $stlist->first_name.' ';
					 if (isset($stlist->last_name))
					 $html3 .= $stlist->last_name.' '; 
					 */
					 if (isset($stlist->address_1))
					 $html3 .= ','.$stlist->address_1; 
					 
					 if (isset($stlist->city))
					 $html3 .= ','.$stlist->city; 

				   
				  $html3 .= '</option>'; 
				}
				$html3 .= '<option value="new">'.OPCLang::_('COM_VIRTUEMART_USER_FORM_ADD_SHIPTO_LBL').'</option>';
				$html3 .= '</select></div>'; 

echo $html3; 				