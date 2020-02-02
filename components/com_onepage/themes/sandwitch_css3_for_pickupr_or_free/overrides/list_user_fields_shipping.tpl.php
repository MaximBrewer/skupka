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

$lang = JFactory::getLanguage();
$extension = 'com_users';
$base_dir = JPATH_SITE;
$lang->load($extension, $base_dir); //, $language_tag, $reload);	
	 
	{
		
		

		$missing = '';

		// collect all required fields
		$required_fields = Array(); 
		echo '<div style="width:100%;">';
				
// we will reorder the fields
		$nf = array();
		$nfo = array(); 
		$title = false; 
		/*
		foreach( $rowFields['fields'] as $f)
		{
		
		 // last_name, middle_name is skipped
		 // country is skipped
		 if ($f['name'] == 'shipto_company')  $nf[0] = $f; 	 
		 elseif ($f['name'] == 'shipto_title') {
		 $nf[1] = $f; $title = true; }
		 elseif ($f['name'] == 'shipto_first_name') $nf[2] = $f;
		 elseif ($f['name'] == 'shipto_address_1') $nf[3] = $f; 
		 elseif ($f['name'] == 'shipto_zip') $nf[4] = $f; 
		 elseif ($f['name'] == 'shipto_city') $nf[5] = $f; 
		 elseif ($f['name'] == 'shipto_phone_1') $nf[6] = $f;
		 elseif ($f['name'] == 'shipto_email') $nf[7] = $f; 
		 elseif ($f['name'] == 'shipto_email2') $nf[8] = $f; 
		 elseif ($f['name'] == 'shipto_opc_password') $nf[9] = $f; 
		 elseif ($f['name'] == 'shipto_password') $nf[10] = $f; 
		 elseif ($f['name'] == 'shipto_password2') $nf[11] = $f; 
		 elseif ($f['name'] == 'shipto_last_name') {;}
		 elseif ($f['name'] == 'shipto_middle_name') {;}
		 else $nfo[] = $f; 
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
			if ($field['type'] == 'delimeter') continue; 
			if (!empty($shipping_obligatory_fields))
			{
			$name = str_replace('shipto_', '', $field['name']); 
		   if (in_array($name, $shipping_obligatory_fields))
			{
			  $field['title'] .= ' *'; 
			}
			}
			if (empty($fied['readonly'])) $field['readonly'] = false;
		    $readonly = $field['readonly'] ? ' readonly="readonly"' : '';
	   		// Title handling.
	   		$key = $field['title'];
	   		if( $field['name'] == 'agreed') {
				continue;
	   			
			}
			 
			//if (((($field['name']!='shipto_first_name') || (empty($title) && ($field['name']=='shipto_first_name'))) && ($field['name']!='shipto_city')))
			{
			echo '<div class="field_wrapper '.$field['type'].'" ';		
			// special case 
			$fn = str_replace('shipto_', '', $field['name']); 
			if (!empty($business_fields) || (!empty($custom_rendering_fields)))
			if ((in_array($fn,$custom_rendering_fields)) || ((in_array('virtuemart_country_id', $custom_rendering_fields) && ($fn == 'virtuemart_state_id'))))
			{
			echo ' id="opc_business_'.$field['name'].'" style="display: none;';
			echo '" ';
			}
			echo '>'; 
			}
	   		// a delimiter marks the beginning of a new fieldset and
	   		// the end of a previous fieldset
			
	   		
	   		$title = false; 
	   		echo '<div id="'.$field['name'].'_div" class="formLabel ';
	   		if (stristr($missing,$field['name'])) {
	   			echo OPCLang::_('COM_ONEPAGE_MISSING');
	   		}
	   		echo '">';
			
			// added as placeholder css3 
			$arr = array('select', 'dropdown','multicheckbox', 'multiselect', 'radio','checkbox' );

			if (in_array($field['type'], $arr))
			{
	        echo '<label class="label_selects" style="clear: both; " for="'.$field['name'].'_field">';
			echo $field['title'];
			echo '</label>';
			}
			
	      
			
			// input ...
			$field['formcode'] = str_replace('type="', ' title="'.$field['title'].'" alt="'.$field['title'].'" placeholder="'.$field['title'].'" type="', $field['formcode']); 		
	        if( !empty( $field['required'] )) {
	        	
	        }
	      	echo ' </div>
	        <div class="formField '; 
			if (in_array($field['type'], $arr)) echo ' field_selects'; 
			echo '" id="'.$field['name'].'_input">'."\n";
	      	
	      	/**
	      	 * This is the most important part of this file
	      	 * Here we print the field & its contents!
	      	 */
			 
	   		switch( $field['name'] ) {
	   			case 'title':
	   				//$ps_html->list_user_title($db->sf('title', true, false), "id=\"title_field\"");
					echo $field['formcode'];
	   				break;
	   			
	   			case 'shipto_virtuemart_country_id':
	   				/*
					if( in_array('state', $allfields ) ) {
	   					$onchange = "onchange=\"changeStateList();\"";
	   				}
	   				else {
	   					$onchange = "";
	   				}
					*/
	   				//$ps_html->list_country("country", $db->sf('country', true), "id=\"country_field\" $onchange style=\"width: 215px;\"");
					if (in_array('virtuemart_country_id', $custom_rendering_fields)) echo '<input type="hidden" id="shipto_virtuemart_country_id" name="shipto_virtuemart_country_id" value="'.$default_shipping_country.'" />'; 
					else
					{
					$field['formcode'] = str_replace('name="', ' alt="'.$field['title'].'" placeholder="'.$field['title'].'" name="', $field['formcode']); 
					echo $field['formcode'];
					}
	   				break;
	   			
	   			case 'shipto_virtuemart_state_id':
//echo $ps_html->dynamic_state_lists( "country", "state", $db->sf('country', true), $db->sf('state', true, false) );
					if (in_array('virtuemart_country_id', $custom_rendering_fields) || (in_array('virtuemart_state_id', $custom_rendering_fields)))
					echo '<input type="hidden" id="shipto_virtuemart_state_id" name="shipto_virtuemart_state_id" value="0" />';
					else
					{
					$field['formcode'] = str_replace('name="', ' alt="'.$field['title'].'" placeholder="'.$field['title'].'" name="', $field['formcode']); 
					echo $field['formcode']; 
					}
					
					
					
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
			//if ((($field['name']!='shipto_title') && ($field['name'] != 'shipto_zip')) || (empty($title) && ($field['name'] == 'shipto_first_name')))
	   		echo '</div>';
	   }
		
	   
	 
	   	   echo '</div>';

	}


?>