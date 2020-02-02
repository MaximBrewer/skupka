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
	 
	
		
		
		
		$missing = '';

		// collect all required fields
		$required_fields = Array(); 
		?>
		<div class="com_virtuemart view-user">
		<table class="adminForm user-details all_fields"><tbody>
				<?php
		
		$delimiter = 0;
	   	foreach( $rowFields['fields'] as $field) {
			
			if (empty($field['type'])) continue;
			if ($field['type'] === 'hidden')
			{
				echo $field['formcode']; 
				continue; 
			}
			if (empty($fied['readonly'])) $field['readonly'] = false;
		    $readonly = $field['readonly'] ? ' readonly="readonly"' : '';
	   		// Title handling.
	   		$key = $field['title'];
	   		if( $field['name'] == 'agreed') {
				continue;
	   			
	   		}
			
			if( $field['type'] == 'delimiter')
			{
				if ($this->hasDel())
				{
					echo $field['formcode']; 
					continue; 
				}
			}
			
			
			
			
	   		if (( $field['name'] == 'username' && VM_REGISTRATION_TYPE == 'OPTIONAL_REGISTRATION' ) || (( $field['name'] == 'opc_password' && VM_REGISTRATION_TYPE == 'OPTIONAL_REGISTRATION' && (!empty($op_usernameisemail)) ))) {
				echo '<tr title="'.htmlentities(JText::_('COM_VIRTUEMART_ORDER_REGISTER')).'"><td class="key"><div class="field_wrapper"><div class="wr2"><div class="formLabel inputregister_account">
						<input type="checkbox" autocomplete="off" id="register_account" name="register_account" value="1" class="inputbox" onclick="return Onepage.showFields( this.checked, new Array('; 
						if (empty($op_usernameisemail))
						echo '\'username\', \'password\', \'password2\''; 
						else echo '\'password\', \'password2\''; 
					echo ') );" '; 
					if (empty($op_create_account_unchecked)) echo ' checked="checked" '; 
					echo '/>
					</div>
					</td>
					<td>
					<div class="formField labelregister_account">
						<label for="register_account">'.JText::_('COM_VIRTUEMART_ORDER_REGISTER').'</label>
					</div>
					</div>
					</div>
					</td>
					</tr>
					';
			} elseif( $field['name'] == 'username' ) {
			    
				echo '<input type="hidden" id="register_account" name="register_account" value="1" />';
			}
			
			$hidden_style = ''; 
			if ($op_create_account_unchecked)
			 {
			   
			   if (($field['name'] == 'opc_password') || ($field['name'] == 'password') || ($field['name'] == 'username') || ($field['name'] == 'password2')) 
				   $hidden_style = ' style="display: none;" '; 
			  
			 }
			
	   		// a delimiter marks the beginning of a new fieldset and
	   		// the end of a previous fieldset
			?><tr class="field_wrapper" title="<?php echo htmlentities($field['title']); ?>">
			<td class="key">
	   		
	   		
	   		 <?php 
			 $classes = ' formLabel '; 
	   		if (stristr($missing,$field['name'])) {
	   			$classes .=  'missing';
	   		}
			?>
	   		<?php
			
			  ?>
			
	        <label id="<?php echo $field['name']; ?>_div" for="<?php echo $field['name']; ?>_field"><?php echo $field['title']; ?> 
	        <?php
			if( !empty( $field['required'] )) {
	        	?><span> *</span><?php
	        }
			?>
			</label>
	      	</td>
	        <td class="formField" id="<?php echo $field['name']; ?>_input">
		  
	      	<?php
	      	/**
	      	 * This is the most important part of this file
	      	 * Here we print the field & its contents!
	      	 */
			 
	   		switch( $field['name'] ) {
	   			default:
					echo $field['formcode']; 
					break;
	   				
	   		}
	   	?>
			</td>
			</tr>
			<?php
	   }
		
	  
	   	   ?></tbody></table></div><?php



