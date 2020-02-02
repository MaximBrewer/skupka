<?php
/*
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 
class OPCLoggedShopper {

public static function getUserInfoBT(&$ref, &$OPCloader)
			{
			require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
			include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
			
			$custom_rendering_fields = OPCconfig::get('custom_rendering_fields', array()); 
			$render_as_hidden = OPCconfig::get('render_as_hidden', array()); 
			$hidden = array(); 
			$hidden_html = ''; 
			/*
			if (!class_exists('VirtuemartModelUser'))
			require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'models' .DIRECTORY_SEPARATOR. 'user.php');
		    */
			$virtuemart_userinfo_id = self::getBTID(); 
			/*
			if (!class_exists('VirtueMartModelState'))
			 require(JPATH_VM_ADMINISTRATOR.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'state.php'); 
			if (!class_exists('VirtueMartModelCountry'))
			require(JPATH_VM_ADMINISTRATOR.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'country.php'); 
		    */
			require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
			$countryModel = OPCmini::getModel('country'); //new VirtueMartModelCountry(); 
			$stateModel = OPCmini::getModel('state'); //new VirtueMartModelState();
					require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
			
			$umodel = OPCmini::getModel('user'); //new VirtuemartModelUser();
			
			$uid = JFactory::getUser()->id;
			if (is_callable($umodel, 'setId'))
			{
				$umodel->setId($uid); 
			}

							
			$userFields = $umodel->getUserInfoInUserFields('edit', 'BT', $virtuemart_userinfo_id);
		
		
				
				$db = JFactory::getDBO(); 
				$q = "select * from #__virtuemart_userinfos as uu, #__users as ju where uu.virtuemart_user_id = '".$uid."' and ju.id = uu.virtuemart_user_id and uu.address_type = 'BT' limit 0,1 "; 
				$db->setQuery($q); 
				$fields = $db->loadAssoc(); 
				
				
				
				//		echo $db->getErrorMsg();
			  if (!empty($virtuemart_userinfo_id) && (!empty($userFields[$virtuemart_userinfo_id])))
			   {
			    if (method_exists($umodel, 'getCurrentUser'))
				{
			    $user = $umodel->getCurrentUser();
				foreach ($user->userInfo as $address) {
				if ($address->address_type == 'BT') {
				
				// set the address from DB: 
				foreach ($address as $k=>$v)
				{
				if (!empty($ref->cart->BT[$k])) $a[$k] = $ref->cart->BT[$k]; 
				else $a[$k] = $v; 
				
				
				if ((!defined('VM_VERSION')) || (VM_VERSION < 3))
				{
					// RELOAD BT ADDRESS ALWAYES FOR VM2
					$a[$k] = $v; 
				}
				
				//$a = (array)$address;
				}
				if (!empty($per_order_rendering))
				foreach ($per_order_rendering as $v=>$po)
				{
				   $a[$po] = '';   
				}
				
					$ref->cart->BT = $a;
					
					continue; 
				}
				}
				}
			
			  }
			  
			 
			  
		

		
			   
		
				require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'userfields.php'); 
				OPCUserFields::populateCart($ref->cart, 'BT', true); 
   

	
				
				if (isset($ref->cart->BTaddress))
				{
					if (isset($ref->cart->BTaddress['fields']['fields']))
					{
						$ref->cart->BTaddress['fields'] = $ref->cart->BTaddress['fields']['fields']; 
					}
				$BTaddress =& $ref->cart->BTaddress['fields']; 
				}
				
				
				if (isset($userFields['fields']))
				$BTaddress = $userFields; 
			    else
				{
					if (isset($userFields[$virtuemart_userinfo_id]))
					$BTaddress = $userFields[$virtuemart_userinfo_id]; 
				}
				
			
				
				
				
				
				if (empty($BTaddress))
				{
					
					$empty = ''; 
					
				$userfieldmodel = OPCmini::getModel('userfields'); //new VirtuemartModelUser();
				$userFieldsBT = $userfieldmodel->getUserFieldsFor('cart','BT');
				$cBT = $ref->cart->BT; 
				$BTaddress = $userfieldmodel->getUserFieldsFilled(
					$userFieldsBT
					,$cBT
					,$empty
				);
				
				}
				

				
				
					if (!empty($per_order_rendering))
					{
				foreach ($per_order_rendering as $v=>$po)
				{
				   if (isset($ref->cart->BTaddress['fields'][$po]))
				    {
					   $fc = $ref->cart->BTaddress['fields'][$po]['formcode']; 
					   $x1 = stripos($fc, 'value="'); 
					    if ($x1 !== false)
						 {
						    $x2 = stripos($fc, '"', $x1+7); 
						
							$nf = substr($fc, 0, $x1+7).substr($fc, $x2); 
							//echo $fc."\n"; 
							//echo $nf; 
							$ref->cart->BTaddress['fields'][$po]['formcode'] = $nf; 
							$ref->cart->BTaddress['fields'][$po]['value'] = ''; 
							
						 }
					}
					}
					
					$BTaddress = $ref->cart->BTaddress['fields']; 
				}
	
				
				
				
				if (empty($BTaddress['fields']))
				{
					$arr = array(); 
					$arr['fields'] = $BTaddress; 
					$BTaddress = $arr; 
				}	
				
				if (!empty($uid))
					 {
						 //		 // we do not allow to change password here
						 $una = array('name', 'password', 'register_account', 'password2'); 
						 foreach ($una as $mkk)
						 {
							 unset($BTaddress['fields'][$mkk]); 
						 }
					 }

					 $bta_test = $BTaddress['fields']; 
					 
			
			OPCUserFields::getUserFields($BTaddress,$OPCloader ,$ref->cart);
			
			
			
			$BTaddress = $BTaddress['fields']; 
				
				
				
				// opc 2.0.115: 
				// $BTaddress = $userfields['fields']; 
				// end
				
				
				$useSSL = VmConfig::get('useSSL', 0);
				$edit_link = JRoute::_('index.php?option=com_virtuemart&view=user&task=editaddresscart&addrtype=BT&virtuemart_userinfo_id='.$virtuemart_userinfo_id.'&cid[]='.$uid, true, $useSSL);
				
				$ghtml = array(); 
				
				{
					//$OPCloader->getNamedFields($BTaddress, $fields, $ref->cart->BT); 
				
				
				
				
				
				foreach ($BTaddress as $k=>$val)
				 {
				   
				   // let update the value per type
				  if (isset($fields[$val['name']]))
				   $BTaddress[$k]['value'] = $fields[$val['name']]; //trim($BTaddress[$k]['value']); 
				  
				  if ($val['type'] === 'hidden') unset($BTaddress[$k]); 
				   
				   
				
				   //if (empty($BTaddress[$k]['value']) && (!empty($ref->cart->BT)) && (!empty($ref->cart->BT[$BTaddress[$k]['name']]))) $BTaddress[$k]['value'] = $ref->cart->BT[$BTaddress[$k]['name']]; 
				 
				   
				   if ($val['name'] == 'agreed') unset($BTaddress[$k]);
				   if ($val['name'] == 'username') unset($BTaddress[$k]);
				   if ($val['name'] == 'password') unset($BTaddress[$k]);
				    if (empty($custom_rendering_fields)) $custom_rendering_fields = array(); 
				    if (in_array($val['name'], $custom_rendering_fields))
				    {
					  unset($BTaddress[$k]); 
					  continue; 
					}
					
					if (in_array($val['name'], $render_as_hidden)) {
						$val['hidden'] = true; 
					}
			if (!empty($val['hidden']))
			{
				$hidden[] = $val; 
				$hidden_html .= $val['formcode']; 
				unset($BTaddress[$k]); 
				continue; 
			}
					
					
					
				   
				   $gf = array('city', 'virtuemart_state_id', 'virtuemart_country_id'); 
				   
				   if (in_array($val['name'], $gf))
				    {
					  $a = array();
					  if ($val['name'] == 'city')
					  {
					  
					    $a['name'] = 'city_field'; 
						$a['value'] = $fields[$val['name']]; 
						
					  }
					  else
					  if (($val['name'] == 'virtuemart_state_id'))
					  {
					    
						if (!empty($fields[$val['name']]))
						{
						$a['name'] = 'virtuemart_state_id'; 
						//$a['value'] = $fields[$val['name']];
						$sid = (int)$fields[$val['name']];; 
						$q = "select state_name from #__virtuemart_states where virtuemart_state_id = '".$sid."' limit 0,1"; 
						$db->setQuery($q); 
						$state_name = $db->loadResult(); 
						$a['value'] = OPCmini::slash($state_name); 
						}
						else
						{
								$a['name'] = 'virtuemart_state_id'; 
								$a['value'] = "";

						}
						// we will override the generated html in order to provide better autocomplete functions
						
					    
					  }
					  /*
					  else
					  if (false)
					  if ($val['name'] == 'virtuemart_country_id')
					  {
					  	if (!empty($fields[$val['name']]))
						{
						$a['name'] = 'virtuemart_country_id'; 
						//$a['value'] = $fields[$val['name']];
						$cid = (int)$fields[$val['name']];; 
						$q = "select country_name from #__virtuemart_countries where virtuemart_country_id = '".$cid."' limit 0,1"; 
						$db->setQuery($q); 
						$c_name = $db->loadResult(); 
						$a['value'] = OPCmini::slash($c_name, false); 
						}
						else
						{
								$a['name'] = 'virtuemart_country_id'; 
								$a['value'] = "";

						}

					   
					  }
					  */
					  if (!empty($a))
					  $ghtml[] = $a;
					}
				   
				 }
				 }
				 unset($BTaddress['vm_address_rescom']);
				 unset($BTaddress['vm_address_validated']);
				 $ref->cart->BTaddress['fields'] = $BTaddress; 
				 
				 
			  //check missing new fields
			  $hasmissing = $OPCloader->hasMissingFields($BTaddress, $ref->cart); 
			  
			   $BTaddress = $OPCloader->setCountryAndState($BTaddress); 
		      $htmlsingle_all = $OPCloader->getBTfields($ref, true, false); 
			  
			  
			  
			  $htmlsingle = '<div '; 
			  if (empty($hasmissing))
			  $htmlsingle .= ' style="display: none;" '; 
			  $htmlsingle .= ' id="opc_stedit_'.$virtuemart_userinfo_id.'">'.$htmlsingle_all.'</div>'; 
			  
			
			  
			  
  			  $BTaddress = $OPCloader->setCountryAndState($BTaddress); 
			
			
			
			
			  $edit_link = '#" onclick="return Onepage.op_showEditST('.$virtuemart_userinfo_id.')';
				$google_html = ''; 
				
				if (!empty($ghtml))
				foreach ($ghtml as $ii)
				{
				  
				  $google_html .= '<input type="hidden" name="google_'.$ii['name'].'" id="google_'.$ii['name'].'" value="'.$ii['value'].'" />'; 
				}
				
				
				$origBT = $BTaddress; 
			    $OPCloader->getNamedFields($BTaddress, $fields, $ref->cart->BT); 
			    $OPCloader->txtToVal($BTaddress); 
			
			
			
				$html = $OPCloader->fetch($OPCloader, 'customer_info.tpl', array('BTaddress' => $BTaddress, 'virtuemart_userinfo_id' => $virtuemart_userinfo_id, 'edit_link' => $edit_link)); 
				$BTaddress = $origBT; 
				
				$hidden_html = str_replace('"required"', '""', $hidden_html); 
				$hidden_html = '<div style="display:none;">'.$hidden_html.'</div>'; 
				$html .= $hidden_html; 
				
				
				if (empty($op_disable_shipto))
				{
				  $html .= '<input type="hidden" name="default_ship_to_info_id" value="'.$virtuemart_userinfo_id.'" checked="checked" />'; 
				}
				//$html .= '<input type="hidden" id="bt_virtuemart_userinfo_id" name="bt_virtuemart_userinfo_id" value="'.$virtuemart_userinfo_id.'" />'; 
				$html2 = $html.$google_html; 
				$html = '<div '; 
				if (!empty($hasmissing))
				$html .= ' style="display: none;" '; 
				$html .= ' id="opc_st_'.$virtuemart_userinfo_id.'">'.$html2.'</div>'.$htmlsingle.'<input type="hidden" id="opc_st_changed_'.$virtuemart_userinfo_id.'" name="opc_st_changed_'.$virtuemart_userinfo_id.'" value="'; 
				if (!empty($hasmissing))
				$html .= '1'; 
				else
				$html .= '0'; 
				$html .= '" />'; 
				$html = str_replace('password2', 'opc_password2', $html); 
				
				
				
				
				return $html; 
			}
			
public static function getUserInfoST(&$ref, &$OPCloader)
{
  			
			   include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
			   
			   if (empty($ref->cart))
			    {
				  $ref->cart = VirtueMartCart::getCart();
				}
			   //$ref->cart->ST = 0; 
			   /*
			   if (method_exists($ref->cart, 'prepareAddressDataInCart'))
			   $ref->cart->prepareAddressDataInCart('ST', 1);
			   
			   if (method_exists($ref->cart, 'prepareAddressFieldsInCart'))
			   $ref->cart->prepareAddressFieldsInCart();
			   */
			   require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'userfields.php');  
			   OPCUserFields::populateCart($ref->cart, 'ST', false);
			   
			   
			   
			   if (!empty($ref->cart->ST))
			   {
			   
			    $STaddress = $ref->cart->STaddress['fields']; 
				
				foreach ($STaddress as $k=>$val)
				 {
				   
				   $kk = str_replace('shipto_', '', $STaddress[$k]['name']); 
				   if (empty($STaddress[$k]['value']) && (!empty($ref->cart->ST)) && (!empty($ref->cart->ST[$kk]))) $STaddress[$k]['value'] = $ref->cart->ST[$kk]; 				
				   $STaddress[$k]['value'] = trim($STaddress[$k]['value']); 
				   if ($val['name'] == 'agreed') unset($STaddress[$k]);
				   
				 }
				 $STnamed = $STaddress; 
				 $STnamed = $OPCloader->setCountryAndState($STnamed); 
				 $ref->cart->STaddress['fields'] = $STaddress; 
				 
				 
				}
				else $STaddress = array(); 
				//$bt_user_info = $ref->cart->BTaddress->user_infoid; 
			
				/*
				if (!class_exists('VirtuemartModelUser'))
				require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'models' .DIRECTORY_SEPARATOR. 'user.php');
			    */
				require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
				$umodel = OPCmini::getModel('user'); //new VirtuemartModelUser();
				
				$virtuemart_userinfo_id = 0; 
				$currentUser = JFactory::getUser();
				$uid = $currentUser->get('id');
				
			
				
				//$userDetails = $umodel->getUser();
				if (is_callable($umodel, 'setId'))
			{
				$umodel->setId($uid); 
			}
				$virtuemart_userinfo_id = $umodel->getBTuserinfo_id();
				
				$userFields = $umodel->getUserInfoInUserFields('default', 'BT', $virtuemart_userinfo_id);
				
				
				/*
				if (empty($userFields[$virtuemart_userinfo_id]))
				$virtuemart_userinfo_id = $umodel->getBTuserinfo_id();
				else $virtuemart_userinfo_id = $userFields[$virtuemart_userinfo_id]; 
				*/
				
				
				//$id = $umodel->getId(); 
				
				if (empty($virtuemart_userinfo_id)) return false; 
				
				//$STaddressList = $umodel->getUserAddressList($uid , 'ST');
				$STaddressList = self::getUserAddressList($uid , 'ST');
				
				
				$STaddressListOrig = $STaddressList; 
				$addressCount = count($STaddressListOrig); 
				
				
				
				if ($addressCount > 10) $addressCountAjax = true; 
				else $addressCountAjax = false; 
				
				// getUserAddressList uses references/pointers for it's objects, therefore we need to create a copy manually:
				/*
				if (false)
				{
				$STaddressListOrig = array(); 
				if (!empty($STaddressList))
				foreach ($STaddressList as $k => $v)
				{
				 foreach ($v as $n=>$r)
				  {
				    $STaddressListOrig[$k]->$n = $r;       
				  }
				}
				}
				*/
				if (isset($ref->cart->STaddress['fields']))
				$BTaddress = $ref->cart->STaddress['fields']; 
				else
				$BTaddress = $ref->cart->BTaddress['fields']; 
				
				
				if (!empty($ref->cart->savedST))
				  {
				    
					foreach ($STaddressList as $key2=>$adr2)
					foreach ($ref->cart->savedST as $key=>$val)
					foreach ($adr2 as $keya => $vala)
					  {
					    if ($keya==$key)
						if ($val == $vala)
					     {
						   if (!isset($bm[$key2])) $bm[$key2] =0; 
						   $bm[$key2]++; 
						 }						 
					  }
				  if (isset($ref->cart->selected_shipto)) {
					  $selected_id = $ref->cart->selected_shipto; 
				  }
				  else {
				  $largest = 0; 
				  $largest_key = 0; 
				  if (!empty($bm))
				  foreach ($bm as $key=>$bc)
				   {
				      if ($bc >= $largest)  
					  {
					    $largest = $bc; 
						$largest_key = $key; 
					  }
				   }
				   if (!empty($largest))
				     {
					   
					   $selected_id = $STaddressList[$largest_key]->virtuemart_userinfo_id;
					   
					 }
				  }
				   }
				
				$x = VmVersion::$RELEASE;	
				$useSSL = VmConfig::get('useSSL', 0);
				
				
				foreach ($STaddressList as $ke => $address)
				 {
				
				  $STaddressList[$ke] = $OPCloader->setCountryAndState($STaddressList[$ke]); 
				
				  
				   if (empty($address->address_type_name))
				    {
					  $address->address_type_name = OPCLang::_('COM_VIRTUEMART_USER_FORM_ADDRESS_LABEL'); 
					  //$address->address_type_name = OPCLang::_('JACTION_EDIT'); 
					}
					
					$link = self::getEditLink($uid, $address->virtuemart_userinfo_id); 
				 
				 
				     $STaddressList[$ke]->edit_link = 	$link; 
				 


				   }

				 
				 
				 
					$new_address_link = '#" onclick="return Onepage.op_showEditST();';
				//version_compare(
				//vm204: index.php?option=com_virtuemart&view=user&task=editaddresscart&new=1&addrtype=ST&cid[]=51
	// don't use ST 
				
				
				
				
				if (empty($only_one_shipping_address))
				{
				$arr = array ('virtuemart_userinfo_id' => $virtuemart_userinfo_id, 
						'STaddressList'=>$STaddressList, ); 
				$html3 = $OPCloader->fetch($OPCloader, 'list_select_address.tpl', $arr); 
				$bm = array(); 
				
				if (!empty($html3))
				{
				   if ($addressCountAjax)
				    {
					   $html3 = str_replace('Onepage.changeST(', 'Onepage.changeSTajax(', $html3); 
					}
				}
				
				if (empty($html3))
				 {
				 
				 
				   //theme file not found, please create or copy /overrides/list_select_address.tpl.php to your theme directory
				   if (!$addressCountAjax)
				   {
				   $html3 = '<select class="opc_st_select" name="ship_to_info_id" id="id'.$virtuemart_userinfo_id.'" onchange="return Onepage.changeST(this);" >';
				   }
				   else
				   {
				   
				   if (defined('OPC_DETECTED_DEVICE') && (OPC_DETECTED_DEVICE != 'DESKTOP'))
				$nochosen = true; 
				else $nochosen = false; 
				   
				   
				   
				   if ($nochosen)
				   {
				   $html3 = '<select class="opc_st_select" name="ship_to_info_id" id="id'.$virtuemart_userinfo_id.'" onchange="return Onepage.changeSTajax(this);" >';
				   }
				   else
				   {
				   $html3 = '<select class="opc-chzn-select opc_st_select" name="ship_to_info_id" id="id'.$virtuemart_userinfo_id.'" onchange="return Onepage.changeSTajax(this);" >';
				   }
				   
				   }
				   if (empty($only_one_shipping_address_hidden))
				   $html3 .= '<option value="'.$virtuemart_userinfo_id.'">'.OPCLang::_(OPCLang::_('COM_ONEPAGE_USER_FORM_ST_SAME_AS_BT')).'</option>';
				foreach ($STaddressList as $stlist)
				{
				  $html3 .= '<option value="'.$stlist->virtuemart_userinfo_id.'">';
				  if (!empty($stlist->address_type_name)) 
				     $html3 .= $stlist->address_type_name;

				  if (isset($stlist->address_1))
					 $html3 .= ','.$stlist->address_1; 
					 
					 if (isset($stlist->city))
					 $html3 .= ','.$stlist->city; 
					 
				  $html3 .= '</option>'; 
				}
				$html3 .= '<option value="new">'.OPCLang::_(OPCLang::_('COM_ONEPAGE_USER_FORM_ADD_SHIPTO_LBL')).'</option>';
				$html3 .= '</select>'; 
				   
				   
				 }
				 if (!empty($selected_id))
				 {
				   $html3 = str_replace('value="'.$selected_id.'"', 'value="'.$selected_id.'" selected="selected" ', $html3); 
				 }
				$html3 .= '<input type="hidden" name="sa" id="sachone" value="" />'; 
				}
				else
				{
				  // load single_shipping_address.tpl.php
				  if (!empty($STaddressList))
				  {
				  $adr1 = reset($STaddressListOrig); 
				
  
				  foreach ($adr1 as $k=>$v)
				  {
				    $ada[$k] = $v; 
				    $ada['shipto_'.$k] = $v; 
				  }
				  $ref->cart->ST = $ada; 
				  }
				  else $ref->cart->ST = 0; 
				  
				if (!empty($ref->cart->ST['virtuemart_country_id']))
				$dc = $ref->cart->ST['virtuemart_country_id']; 
				else
				$dc = OPCloader::getDefaultCountry($ref->cart, true); 				
				
				  
				  $htmlsingle = $OPCloader->getSTfields($ref, true, false, $dc); 
				  if (!empty($adr1))  {
				    $htmlsingle .= '<input type="hidden" name="shipto_logged" value="'.$adr1->virtuemart_userinfo_id.'" />'; 
					//always update ST when in single mode:
					$htmlsingle .= '<input type="hidden" name="opc_st_changed_'.$adr1->virtuemart_userinfo_id.'" id="opc_st_changed_'.$adr1->virtuemart_userinfo_id.'" value="1" />'; 
					//$htmlsingle .= '<input type="hidden" name="ship_to_info_id" id="ship_to_info_id" value="'.$adr1->virtuemart_userinfo_id.'" />'; 
				  }
				  else  {
					  $htmlsingle .= '<input type="hidden" name="shipto_logged" value="new" />'; 
					  $htmlsingle .= '<input type="hidden" name="ship_to_info_id" id="ship_to_info_id" value="new" />'; 
				  }
				  // a default BT address
				  $htmlbt = '<input type="hidden" name="ship_to_info_id_bt" id="ship_to_info_id_bt" value="'.$virtuemart_userinfo_id.'"  class="stradio"/>'; 
				  $htmlsingle.= $htmlbt; 
				  $ref->cart->ST = 0; 
				  return $htmlsingle; 
				  // end of load single shipping address for a logged in user
				}
				$i = 2;
				
				
				
				$BTaddressNamed = $BTaddress; 
				$BTaddressNamed = $OPCloader->setCountryAndState($BTaddressNamed); 
				
				if (!empty($STaddressList) && (empty($htmlsingle)))
				if (!$addressCountAjax)
				{
				foreach ($STaddressListOrig as $ind=>$adr1x)
				{
				
				
				$userFieldsS = $umodel->getUserInfoInUserFields('default', 'BT', $adr1x->virtuemart_userinfo_id);
				$STaddress = $userFieldsS[$adr1x->virtuemart_userinfo_id]['fields']; 
				//$STaddress = $userFieldsS; 
				$BTaddressNamed = $STaddressNamed = $OPCloader->setCountryAndState($STaddress); 
				
				$html2 = self::renderNamed($BTaddressNamed, $adr1x, $ref->cart, $OPCloader, $virtuemart_userinfo_id); 
				
				
				
				}
				
				
				}
				else
				{
				  // we have a problem, the too many addresses will cause a memory leak, therefore we load them over ajax
				  
				}
				
				
				
				// add a new address: 
				if (empty($htmlsingle))
				{
				$ref->cart->ST = 0; 
				$dc = OPCloader::getDefaultCountry($ref->cart, true); 
				
				$html22 = $OPCloader->getSTfields($ref, true, true, $dc); 
				$html22 .= '<input type="hidden" name="shipto_logged" value="new" />'; 
				//$html2 .= '<div id="hidden_st_" style="display: none;">'.$html22.'</div>'; 

				$html22 = str_replace('id="', 'id="REPLACEnewREPLACE', $html22); 
				$html22 = str_replace('name="', 'name="REPLACEnewREPLACE', $html22); 
				
				
				$html22 = '<div id="hidden_st_new" style="display: none;">'.$html22.'<div id="opc_st_new">&nbsp;</div><input type="hidden" name="opc_st_changed_new" id="opc_st_changed_new" value="1" /></div>'; 
				
				
				
				if (!isset(OPCloader::$extrahtml)) OPCloader::$extrahtml = ''; 
				OPCloader::$extrahtml .= $html22; 
				$html22 = ''; 
				
				if (!isset($html2)) $html2 = ''; 
				}
				else $html2 = ''; 
				
				
				
				$ref->cart->ST = 0; 
				$STnamed = $STaddress; 
				$STnamed = $OPCloader->setCountryAndState($STnamed); 
				 
				$vars = array(
				 'STaddress' => $STnamed, 
				 'bt_user_info_id' => $virtuemart_userinfo_id, 
				 'BTaddress' => $BTaddress,
				 'STaddressList' => $STaddressList,
				 'uid'=>$uid,
				 'cart'=>$ref->cart,
				 'new_address_link' => $new_address_link, 
				
				);
				
				// a default BT address
				$htmlbt = '<input type="hidden" name="ship_to_info_id_bt" id="ship_to_info_id_bt" value="'.$virtuemart_userinfo_id.'"  class="stradio"/>'; 
				$htmlbt = '<div id="hidden_st_'.$virtuemart_userinfo_id.'" style="display: none;">'.$htmlbt.'</div>'; 
				$htmlbt .= '<input type="hidden" id="bt_virtuemart_userinfo_id" name="bt_virtuemart_userinfo_id" value="'.$virtuemart_userinfo_id.'" />'; 
				//$ref->cart->STaddress = $STaddress; 
				//$ref->cart->BTaddress = $BTaddress; 
				
				if (empty($html3) && (empty($htmlsingle)))
				{
				$html =  $OPCloader->fetch($OPCloader, 'list_shipto_addresses.tpl', $vars); 
				}
				else $html = ''; 
				

				
				//if (!empty($html) && (!empty($html2)))
				//opc321:if ((!empty($html2)))
				$html = $html3.'<div id="edit_address_list_st_section">'.$html.'</div>'.$htmlbt; //opc321.$html2; 
				
			
				
			
				foreach ($STaddressList as $ST)
				 {
				   $html = str_replace('for="'.$ST->virtuemart_userinfo_id.'"', ' for="id'.$ST->virtuemart_userinfo_id.'" ', $html); 
				   $html = str_replace('id="'.$ST->virtuemart_userinfo_id.'"', ' id="id'.$ST->virtuemart_userinfo_id.'" onclick="javascript:Onepage.op_runSS(this);" ', $html); 
				 }
				   $html = str_replace('for="'.$virtuemart_userinfo_id.'"', ' for="id'.$virtuemart_userinfo_id.'" ', $html); 
				   $html = str_replace('id="'.$virtuemart_userinfo_id.'"', ' id="id'.$virtuemart_userinfo_id.'" onclick="javascript:Onepage.op_runSS(this);" ', $html); 
				
				if (!empty($selected_id))
				{
				  $jsst = '
//<![CDATA[				  
if (typeof jQuery != \'undefined\')
jQuery(document).ready(function($) {
				  var elst = document.getElementById(\'id'.$virtuemart_userinfo_id.'\'); 
				  if (typeof Onepage != \'undefined\')
				  if (elst != null)
				   {
				   '; 
				   if ($addressCountAjax)
				    {
					$jsst .= '
				  Onepage.changeSTajax(elst);
				    '; 
					}
					else
					{
				   $jsst .= '
				  Onepage.changeST(elst);
				    '; 
					}
					$jsst .= '
				   }
				  });
//]]>				  
				  '; 
				  
				  $doc = JFactory::getDocument(); 
				  $doc->addScriptDeclaration($jsst); 
				}
				
				if (defined('OPC_DETECTED_DEVICE') && (OPC_DETECTED_DEVICE != 'DESKTOP'))
				$nochosen = true; 
				else $nochosen = false; 
				
				
				if (!$nochosen)
				if ($addressCountAjax)
				{
				   		if (OPCJ3)
		 {
		   JHtml::_('jquery.framework');
		   JHtml::_('jquery.ui');
		   JHtml::_('formbehavior.chosen', 'select');
		 }
		 else
		 {
		   vmJsApi::js('chosen.jquery.min');
		vmJsApi::css('chosen');
		 }
	     $document = JFactory::getDocument(); 
		 $document->addScriptDeclaration ( '
//<![CDATA[
		 if (typeof jQuery != \'undefined\')
		 jQuery( function() {
			var d = jQuery(".opc-chzn-select"); 
			if (typeof d.chosen != \'undefined\')
			d.chosen({
			    enable_select_all: false,
				});
		});
//]]>
				');
		 

				}
				 
				
				return $html; 

}
public static function copyBTintoSTUserInfos(&$cart) {
	$user_id = JFactory::getUser()->get('id'); 
	//address ST won't be created for empty user_id !
	if (empty($user_id)) return; 
	$jnow = JFactory::getDate();
		if (method_exists($jnow, 'toMySQL'))
		$now = $jnow->toMySQL();
		else $now = $jnow->toSQL(); 
	
	if ((empty($cart->ST)) || ($cart->STsameAsBT)) {
		$db = JFactory::getDBO(); 
		$q = 'select * from `#__virtuemart_userinfos` where `virtuemart_user_id` = '.(int)$user_id.' and `address_type` = "BT"'; 
		$db->setQuery($q); 
		$bt = $db->loadAssoc(); 
		
		
		if (!empty($bt)) {
		$st = array(); 
		$only_one_shipping_address = OPCconfig::get('only_one_shipping_address'); 
		
		if (!empty($only_one_shipping_address)) {
		   //update existing if just one is enabled: 
		   
		   $q = 'select * from `#__virtuemart_userinfos` where `virtuemart_user_id` = '.(int)$user_id.' and address_type = "ST"'; 	
		   $db->setQuery($q); 
		   $st = $db->loadAssoc(); 
		   if (empty($st)) $st = array(); 
		}
		
		foreach ($bt as $k=>$v) {
			if ($k === 'virtuemart_userinfo_id') {
				if (isset($st['virtuemart_userinfo_id'])) {
					//rewrite current ST: 
					$v = $st['virtuemart_userinfo_id']; 
				}
				else {
				   $v = 'NULL'; 	
				}
			}
			if ($k === 'address_type') $v = 'ST'; 
			if ($k === 'address_type_name') {
				$v = OPCLang::_(OPCLang::_('COM_ONEPAGE_FE_ST'));
			}
			$st[$k] = $v; 
		}
		
		$st['modified_on'] = $now;
	    $st['modified_by'] = $user_id;
		
		OPCmini::insertArray('#__virtuemart_userinfos', $st); 
		if (!empty($st['virtuemart_userinfo_id'])) {
			$cart->selected_shipto = $st['virtuemart_userinfo_id']; 
		}
		
		}
		
		
	}
	
}
public static function copyBTintoST(&$order) {
			$db = JFactory::getDBO(); 
	
	
		
	
		if (!empty($order)) {
		       if (empty($order['details']['ST']) ||
			    ($order['details']['BT']->virtuemart_order_userinfo_id === $order['details']['ST']->virtuemart_order_userinfo_id)) {
				
				
				  $q = 'select * from `#__virtuemart_order_userinfos` where `virtuemart_order_userinfo_id` = '.(int)$order['details']['BT']->virtuemart_order_userinfo_id; 
				  $db->setQuery($q); 
				  $insert = $db->loadAssoc();
				  if (!empty($insert)) {
				  $insert['address_type'] = 'ST'; 
				  $insert['virtuemart_order_userinfo_id'] = 'NULL'; 
				  $insert['address_type_name'] = OPCLang::_(OPCLang::_('COM_ONEPAGE_FE_ST'));
				  
				   $table = '#__virtuemart_order_userinfos';
				   $ui = OPCmini::getColumns($table); 
				   OPCmini::insertArray($table, $insert, $ui); 
					return;
				   
				  }
				}
				
		}
		else {
			
			$user_id = JFactory::getUser()->get('id');  
			
			 //check if user got ST address: 
	   if (!empty($user_id)) {
	   $q = 'select * from #__virtuemart_userinfos where virtuemart_user_id = '.(int)$user_id.' and address_type = "ST" limit 0,1'; 
	   $db->setQuery($q); 
	   $res = $db->loadAssoc(); 
	   if (empty($res)) {
	     // user got no record about the ST address in his userinfos table, so let's create this from stored BT... 
	     $q = 'select * from #__virtuemart_userinfos where virtuemart_user_id = '.(int)$user_id.' and address_type = "BT" limit 0,1'; 
		 $db->setQuery($q); 
		 $bt = $db->loadAssoc(); 
		 if (!empty($bt)) {
		 $bt['virtuemart_userinfo_id'] = 'NULL'; 
		 $bt['address_type'] = 'ST'; 
		 $bt['address_type_name'] = OPCLang::_(OPCLang::_('COM_ONEPAGE_FE_ST'));
		 $table = '#__virtuemart_userinfos';

		 OPCmini::insertArray($table, $bt); 
		 }
	   }
	   }
			
		}
			
				
				// make sure we got address_type_name in userinfos, otherwise it won't be shown!
				/*
				$o_user_id = (int)$order['details']['BT']->virtuemart_user_id; 
				if (!empty($o_user_id)) {
				   $q = "update `#__virtuemart_userinfos` set `address_type_name` = '".$db->escape(OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_SHIPPING_LBL'))."' where `virtuemart_user_id` = ".(int)$o_user_id.' and `address_type_name` = "" and `address_type` = "ST"'; 
				   $db->setQuery($q); 
				   $db->query(); 
				   
				}
				*/
				
				
}


 public static function renderNamed($BTaddressNamed, $adr1, &$cart, &$OPCloader, $virtuemart_userinfo_id, $returnHtml=false)
  {
	  
	 
	  
				$uid = JFactory::getUser()->get('id'); 
				//if ($ind >= 10) continue; 
				{
				// will load all the shipping addresses
				$ada = array(); 
				foreach ($adr1 as $k=>$v)
				 {
				   $ada[$k] = $v; 
				   $ada['shipto_'.$k] = $v; 
				 }
				 
				//302 commented: 
				$cart->ST = $ada; 
				}

				$adr1->edit_link = '#" onclick="return Onepage.op_showEditST('.$adr1->virtuemart_userinfo_id.')';
				
				$i = 2;
				
				$adr1 = $OPCloader->setCountryAndState($adr1); 
				$OPCloader->cart = $cart; 
				
				foreach ($adr1 as $k=>$v)
				{
					if ((isset($BTaddressNamed[$k])) && (empty($BTaddressNamed[$k]['value'])))
					{
						$BTaddressNamed[$k]['value'] = $v; 
					}
				}
				
				foreach ($BTaddressNamed as $k=>$v) {
					if (empty($v['title'])) unset($BTaddressNamed[$k]); 
				}
				
				$arr = array(
				 'ST' => $adr1, 
				 'bt_user_info_id' => $virtuemart_userinfo_id, 
				 'BTaddress' => $BTaddressNamed,
				 'uid'=>$uid,
				 'cart'=>$cart,
				 'i'=>$i,
				 ); 
				
				$html2_1 = $OPCloader->fetch($OPCloader, 'get_shipping_address_v2.tpl', $arr); 
				
				
				
				
				if (empty($html2_1))
				{
				  // theme file not found, please create or copy /overrides/get_shipping_address_v2.tpl.php
				  /// ************** start of customer info / shipping address
				    
					foreach ($BTaddressNamed as $key=>$val)
					 {
					   if (!empty($adr1->$key))
					    $BTaddressNamed[$key]['value'] = $adr1->$key; 
					   else 
					    unset($BTaddressNamed[$key]); 
					 }
					
					
			
			
				  	$vars = array ('BTaddress' => $BTaddressNamed, 
									'edit_link' => $adr1->edit_link ); 
					
					$html2_1 = $OPCloader->fetch($OPCloader, 'customer_info.tpl', $vars); 
					
					
					$edit_label = OPCLang::_('JACTION_EDIT'); 
					if ($edit_label == 'JACTION_EDIT') $edit_label = OPCLang::_('EDIT'); 
					$html2_1 = str_replace(OPCLang::_('COM_VIRTUEMART_USER_FORM_EDIT_BILLTO_LBL'), $edit_label, $html2_1); 

					/// ************** end of customer info
				}
				
				
				if (!empty($cart->ST['virtuemart_country_id']))
				$dc = $cart->ST['virtuemart_country_id']; 
				else
				$dc = OPCloader::getDefaultCountry($cart, true); 				

				$hasmissing = $OPCloader->hasMissingFieldsST($cart->ST); 
				
				
				$html2_id = '<div '; 
				if (empty($hasmissing))
				$html2_id .= ' style="display: none;" '; 
				$html2_id .= ' id="opc_stedit_'.$adr1->virtuemart_userinfo_id.'">'; 
				$html2_id .= ' <input type="hidden" name="st_complete_list" value="'.$adr1->virtuemart_userinfo_id.'" />';
				$gf = $OPCloader->getSTfields($OPCloader, true, true, $dc); 
				

				$html2_id .= $gf; 
				$html2_id .= '</div>';  
				
				$html2_id = str_replace('id="', 'id="REPLACE'.$adr1->virtuemart_userinfo_id.'REPLACE', $html2_id); 
				$html2_id = str_replace('name="', 'name="REPLACE'.$adr1->virtuemart_userinfo_id.'REPLACE', $html2_id); 
				
				$html2 = '<input type="hidden" id="opc_st_changed_'.$adr1->virtuemart_userinfo_id.'" name="opc_st_changed_'.$adr1->virtuemart_userinfo_id.'" value="'; 
				if (!empty($hasmissing)) $html2 .= '1'; 
				else $html2 .= '0'; 
				$html2 .= '" />';
				$html2 .= '<div '; 
				if (!empty($hasmissing))
				$html2 .= ' style="display: none;" '; 
			
				
			
			
				$html2 .= ' id="opc_st_'.$adr1->virtuemart_userinfo_id.'">'.$html2_1.'</div>'.$html2_id; 
				
				if($i == 1) $i++;
				elseif($i == 2) $i--;
				
				
				
				if (!empty($STaddressList))
				{
				
				$html2 .= '<input type="hidden" name="shipto_logged" value="'.$adr1->virtuemart_userinfo_id.'" />'; 
				}
				else
				{
				  $html2 .= '<input type="hidden" name="shipto_logged" value="new" />'; 
				}
				
				$html2 = '<div id="hidden_st_'.$adr1->virtuemart_userinfo_id.'" style="display: none;">'.$html2.'</div>'; 
				
				if ($returnHtml) return $html2; 
				
				if (!isset(OPCloader::$extrahtml)) OPCloader::$extrahtml = ''; 
				OPCloader::$extrahtml .= $html2; 
				
				
				return $html2; 
  }
  public static function getEditLink($uid, $stid)
  {
     $useSSL = VmConfig::get('useSSL', 0);
	 $x = VmVersion::$RELEASE;	
     if (version_compare($x, '2.0.3', '<')) 
	  {
	  return JRoute::_('index.php?option=com_virtuemart&view=user&task=editAddressSt&addrtype=ST&cid[]='.$uid.'&virtuemart_userinfo_id='.$stid, true, $useSSL); 
	    
	  }
	  return JRoute::_('index.php?option=com_virtuemart&view=user&task=editaddresscart&addrtype=ST&cid[]='.$uid.'&virtuemart_userinfo_id='.$stid, true, $useSSL); 
	  
	  
	  
	  
  }
  
 public static function getSTHtml(&$cart)
 {
	 
      $html = ''; 
      $stId = JRequest::getVar('ship_to_info_id', 0); 
	   $stId = (int)$stId; 
	   if (!empty($stId))
	    {
		   $user_id = JFactory::getUser()->get('id'); 
		   if (!empty($user_id))
		    {
			  $db = JFactory::getDBO(); 
			  $q = 'select * from #__virtuemart_userinfos where virtuemart_userinfo_id = '.$db->escape($stId).' limit 0,1'; 
			  $db->setQuery($q); 
			  $adr1 = $db->loadObject(); 
			  if (!empty($adr1))
			  if ($adr1->virtuemart_user_id == $user_id)
			   {
			   /*
			   $new_adr1 = new stdClass(); 
			   foreach ($adr1 as $key=>$val)
			    {
				  $new_adr1->$key = $val; 
				}
				*/
			   
			   
			   require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
				$umodel = OPCmini::getModel('user'); //new VirtuemartModelUser();
				
				$virtuemart_userinfo_id = 0; 
				$currentUser = JFactory::getUser();
				$uid = $currentUser->get('id');
				
			
				if (is_callable($umodel, 'setId'))
			{
				$umodel->setId($uid); 
			}
				
				//$userDetails = $umodel->getUser();
				$virtuemart_userinfo_id = $umodel->getBTuserinfo_id();
				
				$userFields = $umodel->getUserInfoInUserFields('default', 'BT', $virtuemart_userinfo_id);
				$userFields = $umodel->getUserInfoInUserFields('default', 'ST', $stId);
				
			   
			   
			     /*
				
				 */
				 /*
				 if (method_exists($cart, 'prepareAddressDataInCart'))
			     $cart->prepareAddressDataInCart('ST', 1);
			     if (method_exists($cart, 'prepareAddressFieldsInCart'))
				 $cart->prepareAddressFieldsInCart();
			     */
				 
			   require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'userfields.php');  
			   OPCUserFields::populateCart($cart, 'ST', false);
			   
			   
			    if (isset($cart->STaddress['fields']))
				 $BTaddress = $cart->STaddress['fields']; 
				 else
				 $BTaddress = $cart->BTaddress['fields']; 
				 
				 if (isset($userFields[$stId])) {
					 $BTaddress = $userFields[$stId]['fields']; 
				 }
				 
				 
				 $new_address_link = '#" onclick="return Onepage.op_showEditST();';
				 require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'loader.php'); 
				 $OPCloader = new OPCloader(); 
				 
				 $BTaddressNamed = $OPCloader->setCountryAndState($BTaddress); 
				 $html = self::renderNamed($BTaddressNamed, $adr1, $cart, $OPCloader, $virtuemart_userinfo_id, true); 
				 
				 
				  
			   }
			}
		}
	return $html; 
 }

	public static function getUserInfos($user_id, $address_type='BT') {
	   	$db = JFactory::getDBO(); 
		$q = "select * from `#__virtuemart_userinfos` as uu, `#__users` as ju where uu.`virtuemart_user_id` = '".(int)$user_id."' and ju.`id` = uu.`virtuemart_user_id` and uu.`address_type` = '".$db->escape($address_type)."' order by uu.`modified_on` desc limit 0,1 "; 
		
		try { 
		$db->setQuery($q); 
		$fields = $db->loadAssoc(); 
		}
		catch (Exception $e) {
			$q = "select * from `#__virtuemart_userinfos` as uu where uu.`virtuemart_user_id` = '".(int)$user_id."' and uu.`address_type` = '".$db->escape($address_type)."' order by uu.`modified_on` desc limit 0,1 "; 
			$db->setQuery($q); 
		    $fields = $db->loadAssoc(); 
			
			if (empty($fields) && ($address_type !== 'BT')) return array(); 
			if (empty($fields)) $fields = array(); 
			$fields = (array)$fields; 
			
			$q = 'select * from #__users where id = '.(int)$user_id; 
			$db->setQuery($q); 
		    $fields2 = $db->loadAssoc(); 
			if (!empty($fields2)) {
				foreach ($fields2 as $k=>$v) {
					$fields[$k] = $v; 
				}
			}
			
		}
		if (empty($fields)) return array(); 
		$fields = (array)$fields; 
		
		$arr = array('created_on', 'modified_on', 'modified_by', 'created_by', 'locked_on', 'locked_by'); 
		foreach ($arr as $v) { unset($fields[$v]); }
		
		return $fields; 
	}
	
	public static function getUserAddressList($uid, $type='ST') {
		 $db = JFactory::getDBO(); 
		 $user_id = (int)$uid; 
		 
		 if (empty($user_id)) return array(); 
		 
		$q = "select uu.* from `#__virtuemart_userinfos` as uu, `#__users` as ju where uu.`virtuemart_user_id` = ".(int)$user_id." and ju.`id` = uu.`virtuemart_user_id` and uu.address_type = '".$db->escape($type)."' "; 
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		$ret = array(); 
		foreach ($res as $k=>$v) {
			$virtuemart_userinfo_id = (int)$v['virtuemart_userinfo_id']; 
			$ret[$virtuemart_userinfo_id] = (object)$v; 
		}
		
		return $ret; 
	}
	
	public static function getSetAllBt(&$cart) {
		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'userfields.php'); 
		
		 $user_id = JFactory::getUser()->get('id'); 
   if (!class_exists('VirtuemartModelUser'))
	    require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'models' .DIRECTORY_SEPARATOR. 'user.php');
	
   $userModel = new VirtuemartModelUser();
   if (method_exists($userModel, 'setId')) {
	   $userModel->setId($user_id);
		}
		
		$bt = OPCLoggedShopper::getUserInfos($user_id, 'BT'); 
	   $cart->BT = $bt; 
	   $cart->BTaddress = array(); 
	    $virtuemart_userinfo_id = OPCUserFields::getUserinfoid($user_id, 'BT'); 
	   $userFields = $userModel->getUserInfoInUserFields('edit', 'BT', $virtuemart_userinfo_id,false);
	   $userFields = $userFields[$virtuemart_userinfo_id];
	   $cart->BTaddress = $userFields; 
	   return $userFields; 
	}
	
	private static function getSTLogged($ship_to_info_id=0) {
		$user_id = JFactory::getUser()->get('id'); 
		
		static $_cache; 
		if (isset($_cache[$user_id.'_'.$ship_to_info_id])) {
			return $_cache[$user_id.'_'.$ship_to_info_id]; 
		}
		$res = array(); 
		if ((!empty($ship_to_info_id)) && ($ship_to_info_id !== 'new')) {
			
			
			$user_id = (int)$user_id; 
			$db = JFactory::getDBO(); 
			$q = "select * from `#__virtuemart_userinfos` as uu, `#__users` as ju where uu.`virtuemart_user_id` = ".(int)$user_id." and ju.`id` = uu.`virtuemart_user_id` and `uu`.`virtuemart_userinfo_id` = ".(int)$ship_to_info_id." limit 0,1 "; 
			$db->setQuery($q); 
			$res = $db->loadAssoc(); 
			
			
			
			if (empty($res)) $res = array(); 
			
		}
		$res = (array)$res; 
		$_cache[$user_id.'_'.$ship_to_info_id] = $res; 
		return $_cache[$user_id.'_'.$ship_to_info_id]; 
		
	}
	public static function checkSec(&$userinfo_id) {
		$db = JFactory::getDBO(); 
		$user = JFactory::getUser(); 
	    $user_id = $user->get('id'); 
		if (!is_numeric($user_id)) return false; 
		$user_id = (int)$user_id; 
		if ($userinfo_id === 'new') return true; 
		$userinfo_id = (int)$userinfo_id; 
		if (empty($userinfo_id)) return true; 
		$q = 'select `virtuemart_user_id` from `#__virtuemart_userinfos` where `virtuemart_userinfo_id` = '.(int)$userinfo_id; 
		$db->setQuery($q); 
		$res = $db->loadResult(); 
		if (empty($res)) return false; 
		$res = (int)$res; 
		if ($res === $user_id) return true; 
		
		return false; 
	}
	public static function getBTID() {
		
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
			
			$umodel = OPCmini::getModel('user'); //new VirtuemartModelUser();
			
			$uid = JFactory::getUser()->id;
			if (is_callable($umodel, 'setId'))
			{
				$umodel->setId($uid); 
			}
		    //$userDetails = $umodel->getUser();
			$virtuemart_userinfo_id = $umodel->getBTuserinfo_id();
			return $virtuemart_userinfo_id; 
	}
	public static function setAddress(&$cart, $ajax=false, $force_only_st=false, $no_post=false, &$post, $userFields, $userFieldsst) {
		// this part for registered users, let's retrieve the selected address
			$user = JFactory::getUser(); 
			$user_id = $user->id; 
			if (!self::checkSec($post['ship_to_info_id'])) return; 
			$db = JFactory::getDBO(); 
			// ship_to_info_id  (currently selected shipping address when more then one is shown)
			// opc_st_changed_  (if to update the address)
			// ship_to_info_id_bt (BT address)
			// 
			
			$bt = JRequest::getVar('ship_to_info_id_bt', ''); 
			if (empty($bt)) $bt = self::getBTID(); 
			$bt = (int)$bt; 
			
			if (!self::checkSec($bt)) return; 
			$bt_changed = JRequest::getVar('opc_st_changed_'.$bt, false); 
			
			
			
			
			$test = $saved_ship_to = $post['ship_to_info_id']; 
			$sa = JRequest::getVar('sa', false); 
			
			// change ship_to_info_id when no shipping address is selected
			/*
			if (!empty($bt))
			if ((!$ajax))
			{
				
				JRequest::setVar('ship_to_info_id', $bt); 
				$_POST['ship_to_info_id'] = $_GET['ship_to_info_id'] = $_REQUEST['ship_to_info_id'] = $bt; 
				$post['ship_to_info_id'] = $bt; 
				
			}
			*/
			
			$db = JFactory::getDBO(); 
			if (($post['ship_to_info_id'] !== 'new') && (!empty($post['ship_to_info_id'])))
			{
				
				$res = self::getSTLogged($post['ship_to_info_id']); 
				
				
			}
			if ($post['ship_to_info_id'] === 'new') 
			{
				// for a new ST we have ship_to_info_id
				$res = array(); 
				$res['user_id'] = $res['virtuemart_user_id'] = JFactory::getUser()->get('id'); 
				$res['address_type'] = 'ST';
			}
			
			
			if ($post['ship_to_info_id'] !== 'new') {
				if (isset($_POST['ship_to_info_id']))
				JRequest::setVar('shipto', (int)$_POST['ship_to_info_id']);
				$cart->selected_shipto = (int)$db->escape($post['ship_to_info_id']); 
			}
			
			// stAn 250
			
			
			$user_id = $res['virtuemart_user_id']; 
			$email = self::getEmail($user_id); 
			
			if (empty($post['email']))
			if (empty($cart->BT['email']))
			if (!empty($email)) $post['email'] = $email; 
			
			if (empty($post['email']))
			if (!empty($cart->BT['email']))
			$post['email'] = $cart->BT['email']; 
			
			
			if (empty($post['username']))
			if (!empty($cart->BT['username']))
			$post['username'] = $cart->BT['username']; 
			
			if (empty($post['username']))
			if (empty($cart->BT['username']))
			if (!empty($user_id))
			{
				if (!empty($user->username))
				$post['username'] = $cart->BT['username'] =  $user->username; 
				
				
				
				if (is_array($cart->ST))
				$cart->ST['username'] = $user->username; 
			}

			
			
			$prefix = ''; 
			if (!isset($post[$prefix.'first_name']) && (!isset($post[$prefix.'last_name'])) && (!empty($post[$prefix.'name'])))
			{
				$a = explode($post[$prefix.'name']); 
				$c = count($a);  
				if ($c>1)
				{
					$post[$prefix.'first_name'] = $a[0]; 
					$cz = $c - 1; 
					$post[$prefix.'last_name'] = $a[$cz]; 
					
				}
			}
			
			$opc_debug = OPCconfig::get('opc_debug', false); 
			
			foreach ($userFields['fields'] as $key=>$uf22)   
			{
				$key_name = $uf22['name']; 
				
				
				
				if ($uf22['type'] == 'delimiter') continue; 
				// don't save passowrds
				if (stripos($uf22['name'], 'password')) $post[$uf22['name']] = ''; 
				
				// POST['variable'] and POST['shipto_variable'] are prefered from database information
				
				
				
				if ((!empty($post[$uf22['name']]) || ((($res['address_type'] == 'ST') && (!empty($post['shipto_'.$uf22['name']]))))) && (empty($force_only_st)))
				{
					
					
					
					// if the selected address is ST, let's first checkout POST shipto_variable
					// then POST['variable']
					// and then let's insert it from the DB
					// will not override BT address when ST is open a the user is logged in
					if (($res['address_type'] == 'ST') && (!empty($post['shipto_'.$uf22['name']]))) {
						$address[$uf22['name']] = $post['shipto_'.$uf22['name']]; 
					}
					else {
						
						if (!empty($bt_changed)) {
							
							$address[$uf22['name']] =  $post[$uf22['name']];
							
						}
						else
						if (isset($res[$uf22['name']]))
						$address[$uf22['name']] = $res[$uf22['name']]; 
						
						/*
					if (!empty($opc_debug))  {
						$address[$uf22['name']] = ''; //'OPCDEBUG'.__LINE__.':'.$post[$uf22['name']]; 
					}
					else {
					$address[$uf22['name']] = ''; //$post[$uf22['name']]; 
					}
					*/
					}
					
					
				}
				else
				{
					// since version 2.0.100 we will update the BT address:
					if (!empty($bt_changed))
					{
						if (isset($post[$uf22['name']]))
						{
							$address[$uf22['name']] =  $post[$uf22['name']];
						}
						else
						if (isset($res[$uf22['name']]))
						$address[$uf22['name']] = $res[$uf22['name']]; 

					}
					else {
						
						
						
						if (!empty($res[$uf22['name']])) {
							$address[$uf22['name']] = $res[$uf22['name']]; 
							
						}
						else {
							$address[$uf22['name']] = ''; 
						}
					}
				}
				
				if (!isset($address[$uf22['name']])) $address[$uf22['name']] = ''; 
				
			}
			
			
			
			// the selected is BT
			if ($res['address_type'] == 'BT') 
			{
				
				
				
				
				$user = JFactory::getUser(); 
				$ix=0; 
				foreach ($address as $v) { 
					
					if ((!empty($v)) && ((empty($cart->BT[$k])) || ($cart->BT[$k] != $v)))  {
						$ix++; 
					}
				}
				
				if (!empty($user->email))
				{
					$address['email'] = $user->email; 
				}
				if (!empty($user->name))
				{
					$address['name'] = $user->name; 
				}
				$cart->STsameAsBT = 1; 
				
				
				
				
				
				
				if ($ix > 1)  {
					/*
					foreach ($address as $kA=>$vA ){
					if (!is_array($cart->BT)) $cart->BT = array(); 
					$cart->BT[$kA] = $vA;
					}
					*/
					self::addressToBt($cart, $address); 
				}
				//$cart->BT = $address; 
				
				
				
				if (!$ajax)
				if ($sa == 'adresaina')
				{
					
					$cart->STsameAsBT = 0; 
					// lets set the ship to address
					
					{
						
						JRequest::setVar('ship_to_info_id', $saved_ship_to); 
						$_POST['ship_to_info_id'] = $saved_ship_to; 
						$_REQUEST['ship_to_info_id'] = $saved_ship_to; 
						$post['ship_to_info_id'] = $saved_ship_to; 
						
						JRequest::setVar('shipto', $saved_ship_to);
					}


					
					
					if ($post['ship_to_info_id'] != $saved_ship_to)
					{
						/*
						$db = JFactory::getDBO(); 
						
						$q = "select * from #__virtuemart_userinfos as uu, #__users as ju where uu.virtuemart_user_id = '".$user_id."' and ju.id = uu.virtuemart_user_id and uu.virtuemart_userinfo_id = '".$db->escape($post['ship_to_info_id'])."' limit 0,1 "; 
						$db->setQuery($q); 
						
						$res = $db->loadAssoc(); 
						$err = $db->getErrorMsg(); 
						*/
						if ($post['ship_to_info_id'] !== 'new')
						$res2X = self::getSTLogged($post['ship_to_info_id'] ); 
						
						if (!empty($res2X)) $res = $res2X; 
						
						
					}
					//$user_id = $res['virtuemart_user_id']; 
					if (isset($_POST['ship_to_info_id']))
					if ($post['ship_to_info_id'] !== 'new')
					JRequest::setVar('shipto', $_POST['ship_to_info_id']);
					$cart->selected_shipto = (int)$db->escape($post['ship_to_info_id']); 
					
					$email = self::getEmail($user_id); 
					if (!empty($email)) 
					{
						$post['email'] = $email; 
						
						if (empty($post['shipto_email']))
						$post['shipto_email'] = $email; 
					}
					
					
					$st_changed = JRequest::getVar('opc_st_changed_'.$saved_ship_to, false); 
					//if ($st_changed)
					{
						$data['shipto_virtuemart_userinfo_id'] = $saved_ship_to;
						$cart->selected_shipto = (int)$saved_ship_to;
						JRequest::setVar('shipto', $saved_ship_to); 
					}
					if ($st_changed)
					foreach ($userFieldsst['fields'] as $key=>$uf22)   
					{
						// don't save passowrds
						if (stripos($uf22['name'], 'password')) $post[$uf22['name']] = ''; 
						
						// POST['variable'] and POST['shipto_variable'] are prefered from database information
						if ((!empty($post[$uf22['name']]) || ((($res['address_type'] == 'ST') && (!empty($post['shipto_'.$uf22['name']]))))) && (empty($force_only_st)))
						{
							// if the selected address is ST, let's first checkout POST shipto_variable
							// then POST['variable']
							// and then let's insert it from the DB
							// will not override BT address when ST is open a the user is logged in
							$u2 = str_replace( 'shipto_', '', $uf22['name']); 
							
							if (($res['address_type'] == 'ST') && (!empty($post['shipto_'.$uf22['name']])))
							{
								$address[$uf22['name']] = $post['shipto_'.$uf22['name']]; 
								$address[$u2] = $post['shipto_'.$uf22['name']]; 
							}
							else
							{
								if (!empty($opc_debug))  {
									$address[$u2] = 'OPCDEBUG'.__LINE__.':'.$post[$uf22['name']]; 
									$address[$uf22['name']] = 'OPCDEBUG'.__LINE__.':'.$post[$uf22['name']]; 
								}
								else {
									$address[$u2] = $post[$uf22['name']]; 
									$address[$uf22['name']] = $post[$uf22['name']]; 
								}
							}
							
							
							
						}
						else
						{
							$u2 = str_replace( 'shipto_', '', $uf22['name']); 
							// since version 2.0.100 we will update the BT address:
							
							if (!empty($st_changed))
							{
								if (isset($post[$uf22['name']]))
								{
									if (!empty($opc_debug))  {
										$address[$uf22['name']] =  'OPCDEBUG'.__LINE__.':'.$post[$uf22['name']];
									}
									else {
										$address[$uf22['name']] =  $post[$uf22['name']];
									}
								}
								else
								{
									if (isset($res[$uf22['name']]))  {
										if (!empty($opc_debug))  {
											$address[$uf22['name']] = 'OPCDEBUG'.__LINE__.':'.$res[$uf22['name']]; 
										}
										else {
											$address[$uf22['name']] = $res[$uf22['name']]; 
										}
									}
									else
									if (isset($res[$u2]))  {
										if (!empty($opc_debug))  {
											$address[$uf22['name']] = 'OPCDEBUG'.__LINE__.':'.$res[$u2]; 
										}
										else {
											$address[$uf22['name']] = $res[$u2]; 
										}
									}
								}
							}
							else
							if (!empty($res[$uf22['name']]))
							$address[$uf22['name']] = $res[$uf22['name']]; 
							else $address[$uf22['name']] = ''; 
						}
					}
					$ix=0; 
					
					foreach ($address as $v) { if (!empty($v)) $ix++; }
					if ($ix > 1)  {
						/*
						foreach ($address as $kA=>$vA ){
							if (!is_array($cart->ST)) $cart->ST = array(); 
							if (isset($address['shipto_'.$ka])) $vA = $address['shipto_'.$ka]; 
							$cart->ST[$kA] = $vA;
						}
						*/
						self::addressToSt($cart, $address); 
					}
					
					
				}
				else
				$cart->ST = 0; 
				
				
				
				if (empty($force_only_st))
				return;
			}
			else 
			{
				// if we updated the logged in ST address, don't set it here
				if (empty($force_only_st)) {
					$ix=0; 
					/*
					foreach ($address as $v) { if (!empty($v)) $ix++; }
					if ($ix > 1)  {
						foreach ($address as $kA=>$vA ){
						if (!is_array($cart->ST)) $cart->ST = array(); 
						if (isset($address['shipto_'.$ka])) $vA = $address['shipto_'.$ka]; 
						$cart->ST[$kA] = $vA;
						}
						
						
					}
					*/
					self::addressToSt($cart, $address); 
					
				}
				$cart->STsameAsBT = 0; 
			}
			
			
			
			// the selected address is not BT
			// we need to get a proper BT
			// and set up found address as ST
			if ((!$cart->STsameAsBT) && (empty($force_only_st)))
			{
				
				
				
				$q = "select * from #__virtuemart_userinfos where virtuemart_user_id = '".$db->escape($res['virtuemart_user_id'])."' and address_type = 'BT' limit 0,1"; 
				$db->setQuery($q); 
				$btres = $db->loadAssoc();
				

				/*
				if (method_exists($cart, 'prepareAddressDataInCart'))
				$cart->prepareAddressDataInCart('BT', 1);
				
				if (method_exists($cart, 'prepareAddressFieldsInCart'))
				$cart->prepareAddressFieldsInCart();
				*/
				require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'userfields.php');  
				OPCUserFields::populateCart($cart, 'BT', true);
				
				
				$fieldtype = 'BTaddress';
				$userFieldsbt = $cart->BTaddress;
				foreach ($userFieldsbt['fields'] as $key=>$uf)   
				{
					// POST['variable'] is prefered form userinfos.variable in db
					$index = str_replace('shipto_', '', $uf['name']); 
					if (!empty($post[$index]))
					{
						$address[$index] = $post[$index]; 
					}
					else
					{
						if (isset($btres[$index]))
						$address[$index] = $btres[$index]; 
					}
				}
				
				$user = JFactory::getUser(); 
				$user_id = $user->get('id'); 
				if (!empty($user_id)) {
					if (empty($address['name'])) $address['name'] = $user->get('name', ''); 
					if (empty($address['email'])) $address['email'] = $user->get('email', ''); 
				}
				// spain 195
				// us 223
				$ix=0; 
				foreach ($address as $v) { if (!empty($v)) $ix++; }
				if ($ix > 1)  {
					/*
					foreach ($address as $kA=>$vA ){
					if (!is_array($cart->BT)) $cart->BT = array(); 
					$cart->BT[$kA] = $vA;
					}					//$cart->BT = $address; 
					*/
					self::addressToBt($cart, $address); 
				}
				//$cart->BT = $address; 
				
				return;
			}
	}
	
	
	function getEmail($id=null)
	{
		$user = JFactory::getUser();
		$email = $user->email; 
		if (empty($email) && (!empty($user->id)))
		{
			$q = 'select email from #__users where id = '.$user->id.' limit 0,1'; 
			$db = JFactory::getDBO(); 
			$db->setQuery($q); 
			$email = $db->loadResult(); 
			
		}
		return $email; 
		
	}
	
	public static function addressToSt(&$cart, $address) {
		
		VirtueMartControllerOpc::addressToSt($cart, $address);
	}
	
	public static function addressToBt(&$cart, $address) {
		VirtueMartControllerOpc::addressToBt($cart, $address);
	}
	
	

 
}			