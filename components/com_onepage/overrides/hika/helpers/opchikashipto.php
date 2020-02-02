<?php
defined('_JEXEC') or die('Restricted access');

class OPCHikaShipto {
	public static function getShipToHtml() {
		
		$userFields = OPChikaaddress::getSTAddressFields(); 
		$unlg = OPChikauser::logged(); 
		$ref = OPChikaRef::getInstance(); 
		$cart =& $ref->cart; 
		$custom_rendering_fields = array(); 
		$default_shipping_country = OPCHikaAddress::getDefaultCountry(); 
		$only_one_shipping_address_hidden = OPCHikaConfig::get('only_one_shipping_address_hidden', false); 
		
		$render_as_hidden = OPCconfig::get('render_as_hidden', array()); 
		
		$hidden = array(); 
		$hidden_html = ''; 
		if (!empty($userFields['fields']))
		foreach ($userFields['fields'] as $key=>$val)
		{
			if (in_array($val['name'], $render_as_hidden)) {
			 $val['hidden'] = true; 
			}
			if (!empty($val['hidden']))
			{
				$hidden[] = $val; 
				$hidden_html .= $val['formcode']; 
				unset($userFields['fields'][$key]); 
			}
		}
		
		
		
		
		$vars = array(
		'rowFields' => $userFields, 
		'rowFields_st' => $userFields, 
		'cart' => $cart, 
		'opc_logged' => $unlg,
		);
		$html = OPChikarenderer::fetch('list_user_fields_shipping.tpl', $vars); 
		$hidden_html = str_replace('"required"', '""', $hidden_html); 
		$hidden_html = '<div style="display:none;">'.$hidden_html.'</div>'; 
		$html .= $hidden_html; 

		//$html = $this->addListeners($html);
		
		if (empty($custom_rendering_fields)) $custom_rendering_fields = array(); 
		if (in_array('address_country', $custom_rendering_fields)) $html .= '<input type="hidden" id="shipto_address_country_field" name="shipto_address_country" value="'.$default_shipping_country.'" />'; 
		if ((in_array('address_state', $custom_rendering_fields)))
		$html .= '<input type="hidden" id="shipto_address_state_field" name="shipto_address_state" value="0" />';   

		$html = str_replace('class="required"', 'class=" "', $html);

		$vars = array('op_shipto' => $html); 

		if (!empty($only_one_shipping_address_hidden) && (!empty($unlg)))
		{

			$html2 = '<input type="hidden" id="sachone" name="sa" value="adresaina" /><div id="ship_to_wrapper"><div id="idsa">'.$html.'</div></div>'; 
			
			
		}
		else
		{
			
			$html2 = OPChikarenderer::fetch('single_shipping_address.tpl', $vars); 

			if (empty($html2) && (!empty($unlg)))
			{
				
				$html2 = '<div id="ship_to_wrapper"><input type="checkbox" id="sachone" name="sa" value="adresaina" onkeypress="javascript: Hikaonepage.showSA(this, \'idsa\');" onclick="javascript: Hikaonepage.showSA(this, \'idsa\');" autocomplete="off" />';
				$html2 .= OPCLang::_('COM_VIRTUEMART_USER_FORM_ADD_SHIPTO_LBL');
				$html2 .= '<div id="idsa" style="display: none;">'.$html.'</div>'; 
				$html2 .= '</div>'; 
			}
		}





		// if theme does not exists, return legacy html
		if (empty($html2) || (!empty($no_wrapper))) 
		return $html; 



		return $html2;
		
	}
}