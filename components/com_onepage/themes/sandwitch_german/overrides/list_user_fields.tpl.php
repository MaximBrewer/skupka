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
*/

	/**
	 * This allows us to print the user fields on
	 * the various sections of the shop
	 *
	 * @param array $rowFields An array returned from ps_database::loadObjectlist
	 * @param array $skipFields A one-dimensional array holding the names of fields that should NOT be displayed
	 * @param ps_DB $db A ps_DB object holding ovalues for the fields
	 * @param boolean $startform If true, print the starting <form...> tag
	 * 
	 * content of this file is a modification of function listUserFields( $rowFields, $skipFields=array(), $db = null, $startForm = true ) 
	 * of Virtuemart 1.1.7
	 */
	 
	{
$lang = JFactory::getLanguage();
$extension = 'com_users';
$base_dir = JPATH_SITE;
$lang->load($extension, $base_dir); //, $language_tag, $reload);	

if (VM_REGISTRATION_TYPE != 'OPTIONAL_REGISTRATION' ) 
{
$css = '
div#vmMainPageOPC div#opc_password_input, div#vmMainPageOPC div#opc_password2_input, div#vmMainPageOPC div#password2_input, div#vmMainPageOPC div#username_input {
 left: 25% !important;
}
div#password2_div, div#opc_password_div, div#opc_password2_div, div#username_div {
 left: 0 !important;
}
'; 
$doc = JFactory::getDocument(); 
$doc->addStyleDeclaration( $css );
	
}

$session = JFactory::getSession(); 
		$saved_f = $session->get('opc_fields', array(), 'opc'); 
		if (empty($saved_f)) $saved_fields = array(); 
		else
		$saved_fields = @json_decode($saved_f, true); 
	
	//if (empty($registration_html)) $no_login_in_template = true; 
 if (!empty($saved_fields['opc_is_business'])) $opc_is_business = true; 
 else $opc_is_business = false; 		


		$missing = '';

		// collect all required fields
		$required_fields = Array(); 
		echo '<div style="width:100%;">';
		
		// we will reorder the fields
		$nf = array();
		$nfo = array(); 
		$found = array(); 
		/*
		foreach( $rowFields['fields'] as $f)
		{
		
		 // last_name, middle_name is skipped
		 // country is skipped
		 if ($f['name'] == 'company')  $nf[0] = $f; 	 
		 elseif ($f['name'] == 'title') $nf[1] = $f; 
		 elseif ($f['name'] == 'first_name') $nf[2] = $f;
		 elseif ($f['name'] == 'address_1') $nf[3] = $f; 
		 elseif ($f['name'] == 'zip') 
		 {
		 $nf[4] = $f; 
		 
		 }
		 elseif ($f['name'] == 'city') $nf[5] = $f; 
		 elseif ($f['name'] == 'phone_1') $nf[6] = $f;
		 elseif ($f['name'] == 'email') $nf[7] = $f; 
		 elseif ($f['name'] == 'email2') $nf[8] = $f; 
		 elseif ($f['name'] == 'opc_password') $nf[9] = $f; 
		 elseif ($f['name'] == 'password') $nf[10] = $f; 
		 elseif ($f['name'] == 'password2') $nf[11] = $f; 
		 elseif ($f['name'] == 'last_name') {;}
		 elseif ($f['name'] == 'middle_name') {;}
		 else $nfo[] = $f; 
		 
		 $found[$f['name']] = $f['name']; 
		}
		
		ksort($nf); 
		foreach ($nfo as $d)
		 {
		   $nf[] = $d;
		 }
		$rowFields['fields'] = $nf; 
		*/
		
		$delimiter = 0;
	
		
		
	   	foreach( $rowFields['fields'] as $field) {
			if (empty($field['type'])) continue;
			if (empty($fied['readonly'])) $field['readonly'] = false;
			
			
		    $readonly = $field['readonly'] ? ' readonly="readonly"' : '';
	   		// Title handling.
	   		$key = $field['title'];
	   		if( $field['name'] == 'agreed') {
				continue;
	   			
	   		}
			
			if (!empty($field['required']))
			{
			  $field['title'] .= ' *'; 
			}
			
			//if ((($field['name']!='first_name') && (isset($found['title']))) && (($field['name']!='city') && (isset($found['zip']))))		
			//if (!(($field['name']=='city') && ($found['city'])))
			{
			echo '<div class="field_wrapper '.$field['type'].'"';		
			// special case 
			
			if (!empty($business_fields) || (!empty($custom_rendering_fields)))
			if ((in_array($field['name'], $business_fields) && (empty($is_logged))) || (in_array($field['name'],$custom_rendering_fields)) || ((in_array('virtuemart_country_id', $custom_rendering_fields) && ($field['name'] == 'virtuemart_state_id')))) 
			{	
			echo ' id="opc_business_'.$field['name'].'" '; 
			if (empty($opc_is_business)) echo ' style="display: none;" ';
			}
			else
			if (( $field['name'] == 'username' && VM_REGISTRATION_TYPE == 'OPTIONAL_REGISTRATION' ) || ((( $field['name'] == 'opc_password' || ($field['name'] == 'password')) && VM_REGISTRATION_TYPE == 'OPTIONAL_REGISTRATION' && (!empty($op_usernameisemail)) )) )
			if (in_array('register_account', $business_fields))
			{
				
			echo ' id="opc_business_register_account" ';
			if (empty($opc_is_business)) echo ' style="display: none;" ';
			}
			
			//if (in_array('virtuemart_country_id', $custom_rendering_fields) || (in_array('virtuemart_state_id', $custom_rendering_fields)))
			
			//echo '" ';
			echo '>'; 
			}
			
			$core = array('username', 'opc_password', 'opc_password2'); 
			
			if ((in_array($field['name'], $core))
			&& (VM_REGISTRATION_TYPE == 'OPTIONAL_REGISTRATION')
			&& (!defined('REGBUTTON')))
			{
			    define('REGBUTTON', 1); 
				echo '<div class="formLabel" id="register_label">
						
						<button name="regbutton" style="position: static;" onkeypress="javascript: return showSAreg(this);"  class="';
						if (empty($op_create_account_unchecked))
						echo 'button_checkbox_ed'; 
						else
						echo 'button_checkbox_uned'; 
						echo '" onclick="javascript: return showSAreg(this);" autocomplete="off" ><div style="float: left;">&nbsp;</div><span id="register_span" class="register_span">'.OPCLang::_('COM_VIRTUEMART_ORDER_REGISTER').'</span></button>
					</div>
				<div class="formField">
				  
					  <div style="display: none;">
						<input type="checkbox" autocomplete="off" id="register_account" name="register_account" value="1" class="inputbox" onchange="showFields( this.checked, new Array('; 
						if (empty($op_usernameisemail))
						echo '\'username\', \'password\', \'password2\''; 
						else echo '\'password\', \'password2\''; 
					echo ') );" '; 
					if (empty($op_create_account_unchecked)) echo ' checked="checked" '; 
					echo '/>
					  </div>
					</div>
					
					';
			} 
			    if ((VM_REGISTRATION_TYPE != 'OPTIONAL_REGISTRATION')
				&& (VM_REGISTRATION_TYPE != 'NO_REGISTRATION')
				&& (!defined('REGBUTTON'))
				&& (in_array($field['name'], $core)))
				{
				define('REGBUTTON', 1); 
				echo '<input type="hidden" id="register_account" name="register_account" value="1" />';
				
				}
				
				
				
			
	   		// a delimiter marks the beginning of a new fieldset and
	   		// the end of a previous fieldset
			/*
	   		if( $field['type'] == 'delimiter') {
	   			if( $delimiter > 0) {
	   				echo "</fieldset>\n";
	   			}
	   			if( VM_REGISTRATION_TYPE == 'SILENT_REGISTRATION' && $field['title'] == OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_CUST_INFO_LBL') && $page == 'checkout.index' ) {
	   				continue;
	   			}
	   			echo '<fieldset>
				     <legend class="sectiontableheader">'.$field['title'].'</legend>
';
	   			$delimiter++;
	   			continue;
	   		}
	   		*/
	   		echo '<div id="'.$field['name'].'_div" class="formLabel ';
	   		if (stristr($missing,$field['name'])) {
	   			echo OPCLang::_('COM_ONEPAGE_MISSING');
	   		}
			echo '"'; 
			if ((VM_REGISTRATION_TYPE == 'OPTIONAL_REGISTRATION') && (!empty($op_create_account_unchecked)) && (in_array($field['name'], array('username', 'password', 'opc_password', 'password2')))) echo ' style="display: none;" '; 
	   		echo '>';
	        echo '<label for="'.$field['name'].'_field">';
			/*
			if ($field['name'] == 'title') 
			{
			$name = OPCLang::_('COM_USERS_PROFILE_NAME_LABEL'); 
			$name = str_replace(':', '', $name); 
			echo $name; 
			}
			else
			if ($field['name'] == 'first_name') echo ''; 
			else
			*/
			echo $field['title'];
			echo '</label>';
	        if( !empty( $field['required'] )) {
	        	
	        }
	      	echo ' </div>
	      <div class="formField" id="'.$field['name'].'_input"'; 
		  if ((VM_REGISTRATION_TYPE == 'OPTIONAL_REGISTRATION') && (!empty($op_create_account_unchecked)) && (in_array($field['name'], array('username', 'password', 'opc_password', 'password2')))) echo ' style="display: none;" '; 
		  echo '>'."\n";
	      	
	      	/**
	      	 * This is the most important part of this file
	      	 * Here we print the field & its contents!
	      	 */
			 
	   		switch( $field['name'] ) {
	   			case 'title':
	   				//$ps_html->list_user_title($db->sf('title', true, false), "id=\"title_field\"");
					echo $field['formcode'];
	   				break;
	   			
	   			case 'virtuemart_country_id':
	   				/*
					if( in_array('state', $allfields ) ) {
	   					$onchange = "onchange=\"changeStateList();\"";
	   				}
	   				else {
	   					$onchange = "";
	   				}
					*/
	   				//$ps_html->list_country("country", $db->sf('country', true), "id=\"country_field\" $onchange style=\"width: 215px;\"");
					
					//$field['formcode'] = str_replace('id="virtuemart_country_id"', ' onchange="javascript: Onepage.op_validateCountryOp2(\'false\', \'false\', this);" id="virtuemart_country_id" ', $field['formcode']); 
					
					
					if (in_array('virtuemart_country_id', $custom_rendering_fields)) echo '<input type="hidden" id="virtuemart_country_id" name="virtuemart_country_id" value="'.$default_shipping_country.'" />'; 
					else
					echo $field['formcode'];
	   				break;
	   			
	   			case 'virtuemart_state_id':
	   				//echo $ps_html->dynamic_state_lists( "country", "state", $db->sf('country', true), $db->sf('state', true, false) );
					if (in_array('virtuemart_country_id', $custom_rendering_fields) || (in_array('virtuemart_state_id', $custom_rendering_fields)))
					echo '<input type="hidden" id="virtuemart_state_id" name="virtuemart_state_id" value="0" />';
					else
					echo $field['formcode']; 
				    //echo "<noscript>\n";
				    //$ps_html->list_states("state", $db->sf('state', true, false), "", "id=\"state_field\"");
				    //echo "</noscript>\n";
	   				break;
				case 'agreed':
					//echo '<input type="checkbox" id="agreed_field" name="agreed" value="1" class="inputbox" />';
					echo $field['formcode'];
					break;
				case 'password':
					echo $field['formcode']; 
		   			break;

				case 'password2':
					
					echo $field['formcode']; 
		   			break;
				case 'socialNumber':
					echo $field['formcode']; 
					break;
	   			default:
					echo $field['formcode']; 
					break;
	   				
	   		}
			
	   		echo '</div>';
			//if ((($field['name']!='title') && (isset($found['title']))) && 
			//if (!(($field['name'] == 'zip') && (isset($found['city']))))
			echo '</div>'; 
	   }
		if( $delimiter > 0) {
		
		if( !empty( $required_fields ))  {
			echo '<div style="padding:5px;text-align:center;"><strong>(* = '.OPCLang::_('CMN_REQUIRED').')</strong></div>';
		  	 
		}
			echo "</fieldset>\n";
		}
	   
	   	   echo '</div>';

	}


?>
