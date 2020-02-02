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
		
		

		$missing = '';

		// collect all required fields
		$required_fields = Array(); 
		echo '<div style="width:100%;" class="list_user_fields">';
				
		
		$delimiter = 0;
		
		$copy = array(); 
		$z = 0; 
		foreach( $rowFields['fields'] as $field)
		{
			
			$copy[$z] = $field; 
			$z++; 
		}
		
		
		
		$z = -1; 
	   	foreach( $rowFields['fields'] as $field) {
			$z++; 
			if( $field['type'] == 'delimiter')
			{
				if ($this->hasDel())
				{
					echo $field['formcode']; 
					continue; 
				}
			}
			
		   if (empty($field['required']))
			{

			  $field['title'] .= ' (optional)'; 
			}
			$field['formcode'] = str_replace('type="', ' title="'.$field['title'].'" alt="'.$field['title'].'" placeholder="'.$field['title'].'" type="', $field['formcode']); 

			if (empty($field['type'])) continue;
			if (empty($fied['readonly'])) $field['readonly'] = false;
		    $readonly = $field['readonly'] ? ' readonly="readonly"' : '';
	   		// Title handling.
	   		$key = $field['title'];
	   		if( $field['name'] == 'agreed') {
				continue;
	   			
	   		}
	   		if (( $field['name'] == 'username' && VM_REGISTRATION_TYPE == 'OPTIONAL_REGISTRATION' ) || (( $field['name'] == 'opc_password' && VM_REGISTRATION_TYPE == 'OPTIONAL_REGISTRATION' && (!empty($op_usernameisemail)) ))) {
				
				
				?><div><div><div class="formLabel registrationSection">
						
						<label class="register_account" for="register_account">
						<span class="input_wrapper2">
						  <input type="checkbox" autocomplete="off" id="register_account" name="register_account" value="1" class="inputbox checkbox inline" onclick="return Onepage.showFields( this.checked, new Array(<?php
						if (empty($op_usernameisemail))
						echo '\'username\', \'password\', \'password2\''; 
						else echo '\'password\', \'password2\''; 
						echo ')';  ?>);" <?php 
					if (empty($op_create_account_unchecked)) echo ' checked="checked" '; ?> />
					  </span>
					
					<span class="reg_label">
					<?php echo OPCLang::_('COM_VIRTUEMART_ORDER_REGISTER'); ?>
					</span>
					
					
						</label>
					</div></div></div>
					
				<?php
			} elseif( $field['name'] == 'username' ) {
			    
				echo '<input type="hidden" id="register_account" name="register_account" value="1" />';
			}
	   		// a delimiter marks the beginning of a new fieldset and
	   		// the end of a previous fieldset
			
	   		
	   		
			$arr = array('select', 'dropdown','multicheckbox', 'multiselect', 'radio','checkbox' );
			if (in_array($field['type'], $arr)) {
	   		echo '<div id="'.$field['name'].'_div" class="formLabel ';
	   		if (stristr($missing,$field['name'])) {
	   			echo 'missing';
	   		}
	   		echo '"';
			if ($op_create_account_unchecked)
			 {
			   if (($field['name'] == 'opc_password') || ($field['name'] == 'password') || ($field['name'] == 'username') || ($field['name'] == 'password2')) echo ' style="display: none;" '; 
			 }
			 
			    if (!in_array($field['type'], $arr))
				echo '  '; 
			echo '>';
			}
	if (in_array($field['type'], $arr))
			{
			
	        echo '<label for="'.$field['name'].'_field">'.$field['title'].'</label>';
			$count = 0; 
	        $field['formcode'] = str_replace('class="', 'class="checkbox inline ', $field['formcode'], $count); 
			if (empty($count))
			{
				$field['formcode'] = str_replace('type="', 'class="checkbox " type="', $field['formcode'], $count); 
			}
			
			}
		$field['formcode'] = str_replace('type="', ' alt="'.$field['title'].'" placeholder="'.$field['title'].'" type="', $field['formcode']); 
	      	
			if (in_array($field['type'], $arr)) {
			echo '</div> '; 
			}

			$extra_class = ''; 
			$next = $z+1; 
			if (($field['name'] == 'first_name') && (isset($copy[$next])) && (isset($copy[$next]['name'])) && ($copy[$next]['name'] === 'last_name'))
			{
				$extra_class = ' two_col_field_left'; 
				$copy[$next]['extra_class'] = ' two_col_field_right'; 
			}
			if (($field['name'] == 'password') && (isset($copy[$next])) && (isset($copy[$next]['name'])) && ($copy[$next]['name'] === 'password2'))
			{
				$extra_class = ' two_col_field_left'; 
				$copy[$next]['extra_class'] = ' two_col_field_right'; 
			}
			if (($field['name'] == 'opc_password') && (isset($copy[$next])) && (isset($copy[$next]['name'])) && ($copy[$next]['name'] === 'password2'))
			{
				$extra_class = ' two_col_field_left'; 
				$copy[$next]['extra_class'] = ' two_col_field_right'; 
			}
			if (($field['name'] == 'opc_password') && (isset($copy[$next])) && (isset($copy[$next]['name'])) && ($copy[$next]['name'] === 'opc_password2'))
			{
				$extra_class = ' two_col_field_left'; 
				$copy[$next]['extra_class'] = ' two_col_field_left'; 
			}
			
			
	if (($field['name'] == 'shipto_first_name') && (isset($copy[$next])) && (isset($copy[$next]['name'])) && ($copy[$next]['name'] === 'shipto_last_name'))
			{
				$extra_class = ' two_col_field_left'; 
				$copy[$next]['extra_class'] = ' two_col_field_right'; 
			}
			if (($field['name'] == 'shipto_virtuemart_country_id') && (isset($copy[$next])) && (isset($copy[$next]['name'])) && ($copy[$next]['name'] === 'shipto_zip'))
			{
				$extra_class = ' two_col_field_left_7'; 
				$copy[$next]['extra_class'] = ' two_col_field_right_3'; 
			}
			
			if (($field['name'] == 'shipto_address_1') && (isset($copy[$next])) && (isset($copy[$next]['name'])) && ($copy[$next]['name'] === 'shipto_address_2'))
			{
				$extra_class = ' two_col_field_left_7'; 
				$copy[$next]['extra_class'] = ' two_col_field_right_3'; 
			}
			
			
			
			if (($field['name'] == 'virtuemart_country_id') && (isset($copy[$next])) && (isset($copy[$next]['name'])) && ($copy[$next]['name'] === 'zip'))
			{
				$extra_class = ' two_col_field_left_7'; 
				$copy[$next]['extra_class'] = ' two_col_field_right_3'; 
			}
			
			if (($field['name'] == 'address_1') && (isset($copy[$next])) && (isset($copy[$next]['name'])) && ($copy[$next]['name'] === 'address_2'))
			{
				$extra_class = ' two_col_field_left_7'; 
				$copy[$next]['extra_class'] = ' two_col_field_right_3'; 
			}
			
			$current = $z; 
			if (!empty($copy[$current]['extra_class']))
			{
				$extra_class = $copy[$current]['extra_class']; 
			}
			
			
		echo '
	      <div class="'.$extra_class.' formField" id="'.$field['name'].'_input"';
		  if ($op_create_account_unchecked)
			 {
			   if (($field['name'] == 'opc_password') || ($field['name'] == 'password') || ($field['name'] == 'username') || ($field['name'] == 'password2')) echo ' style="display: none;" '; 
			 }
		  
		  echo '>'."\n";
	      	
	      	/**
	      	 * This is the most important part of this file
	      	 * Here we print the field & its contents!
	      	 */
			 
	   		switch( $field['name'] ) {
				case 'opc_password': 
				case 'password': 
				case 'username': 
				case 'opc_password2': 
				case 'password2': 
				
				echo '<div><div>'.$field['formcode'].'</div></div>'; 
				break; 
				
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
					echo $field['formcode'];
	   				break;
	   			
	   			case 'virtuemart_state_id':
	   				//echo $ps_html->dynamic_state_lists( "country", "state", $db->sf('country', true), $db->sf('state', true, false) );
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
				case 'password2':
					
					echo $field['formcode']; 
		   			break;
					
	   			default:
					echo $field['formcode']; 
					break;
	   				
	   		}
	   		echo '</div>';
	   }
		
	   
	  
	   	   echo '</div>';

	}

