<?php
/**
 
 
 
 * @copyright (C) 2014 Interamind LTD, http://www.interamind.com
 
**/

defined('_JEXEC') or die( 'Restricted access' );

if(file_exists(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_vmeeplus'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'autoloader.php')){
	require_once JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_vmeeplus'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'autoloader.php';
}

require_once JPATH_PLUGINS.DIRECTORY_SEPARATOR.'vmee'.DIRECTORY_SEPARATOR.'tagHandlerVmeePro'.DIRECTORY_SEPARATOR.'tagHandlerVmeePro' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'orderTagsHandler.php';
require_once JPATH_PLUGINS.DIRECTORY_SEPARATOR.'vmee'.DIRECTORY_SEPARATOR.'tagHandlerVmeePro'.DIRECTORY_SEPARATOR.'tagHandlerVmeePro' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'userTagsHandler.php';
jimport('joomla.plugin.plugin');

class plgVmeeTagHandlerVmeePro extends JPlugin {

	const ORIENTATION_ORDER = 1;
	const ORIENTATION_EXISTING_CUSTOMER = 2;
	const ORIENTATION_NEW_CUSTOMER = 4;
	const ORIENTATION_CART = 8;
	const ORIENTATION_WAITING_LIST = 16;
	
	function plgVmeeTagHandlerVmeePro(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$language = JFactory::getLanguage();
		$language->load('plg_vmee_tagHandlerVmeePro', JPATH_ADMINISTRATOR, 'en-GB', true);
		$language->load('plg_vmee_tagHandlerVmeePro', JPATH_ADMINISTRATOR, null, true);
	}
	
	function replaceTags(&$str, &$errors, &$resources){
		$str = $this->replaceArticleTags($str);
		$str = $this->replaceLanguageTags($str);
		
		$orderTagsHandler = new orderTagsHandler();
		$orderTagsHandler->replaceTags($str, $errors, $resources);
		
		//$useTagsHandler = new userTagsHandler();
		//$useTagsHandler->replaceTags($str, $errors, $resources);
		
		$str = $this->replaceCalcTags($str);
		
	}
	
	function getAvailableTagsDesc(){
		//this is only VMEE PRO plugin
		if(file_exists(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_vmemails".DIRECTORY_SEPARATOR."models".DIRECTORY_SEPARATOR."vmemails.php") )
			require_once ( JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_vmemails".DIRECTORY_SEPARATOR."models".DIRECTORY_SEPARATOR."vmemails.php");
	
		return array(
			vmemailsModelVmemails::$TYPE_REGISTRATION => null,
			vmemailsModelVmemails::$TYPE_ORDER_CONFIRM => null,
			vmemailsModelVmemails::$TYPE_ADMIN_ORDER_CONFIRM => null,
			vmemailsModelVmemails::$TYPE_ORDER_SATAUS_CHANGED => null
		); 
	}
	
	private function getTemplateVariables(){
		return $this->getAvailableTags(self::ORIENTATION_CART | self::ORIENTATION_EXISTING_CUSTOMER | self::ORIENTATION_NEW_CUSTOMER | self::ORIENTATION_ORDER | self::ORIENTATION_WAITING_LIST);
	}

	public function getAvailableTags($orientation){
		$availableTags = array();
		$onClick = 'javascript:this.focus();this.select();';
		$onFocus = 'javascript:this.focus();this.select();';
		
		$allOrientation = self::ORIENTATION_CART | self::ORIENTATION_EXISTING_CUSTOMER | self::ORIENTATION_NEW_CUSTOMER | self::ORIENTATION_ORDER ;
		$subjectTags = array();
		$description = '<table border="0" class="paramlist admintable">
	<tbody>';
		$description .= $this->checkOrientation('<tr>
			<td class="iparam_header">'.JText::_('SITENAME').'</td>
			<td class="iparam_td"><input class="iparam" value="[SITENAME]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
		</tr>', $allOrientation | self::ORIENTATION_WAITING_LIST , $orientation);
		$description .= $this->checkOrientation('<tr>
				<td class="iparam_header">'.JText::_('CUSTOMER_NAME').'</td>
				<td class="iparam_td"><input class="iparam" value="[CUSTOMER_NAME]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
				</tr>', self::ORIENTATION_NEW_CUSTOMER | self::ORIENTATION_EXISTING_CUSTOMER , $orientation);
		$description .= $this->checkOrientation('<tr>
			<td class="iparam_header">'.JText::_('CUSTOMER_USER_NAME').'</td>
			<td class="iparam_td"><input class="iparam" value="[CUSTOMER_USER_NAME]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
		</tr>', $allOrientation , $orientation);
		$description .= $this->checkOrientation('<tr>
			<td class="iparam_header">'.JText::_('CUSTOMER_FIRST_NAME').'</td>
			<td class="iparam_td"><input class="iparam" value="[CUSTOMER_FIRST_NAME]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
		</tr>', $allOrientation , $orientation);
		$description .= $this->checkOrientation('<tr>
			<td class="iparam_header">'.JText::_('CUSTOMER_LAST_NAME').'</td>
			<td class="iparam_td"><input class="iparam" value="[CUSTOMER_LAST_NAME]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
		</tr>', $allOrientation , $orientation);
		$description .= $this->checkOrientation('<tr>
			<td class="iparam_header">'.JText::_('ORDER_ID').'</td>
			<td class="iparam_td"><input class="iparam" value="[ORDER_ID]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
		</tr>', self::ORIENTATION_ORDER , $orientation);
		$description .= $this->checkOrientation('<tr>
				<td class="iparam_header">'.JText::_('VM_ORDER_ID').'</td>
				<td class="iparam_td"><input class="iparam" value="[VM_ORDER_ID]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
				</tr>', self::ORIENTATION_ORDER , $orientation);
		$description .= $this->checkOrientation('<tr>
			<td class="iparam_header">'.JText::_('ORDER_STATUS').'</td>
			<td class="iparam_td"><input class="iparam" value="[ORDER_STATUS]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
		</tr>', self::ORIENTATION_ORDER , $orientation);
		$description .= $this->checkOrientation('<tr>
				<td class="iparam_header">'.JText::_('INVOICE_NUMBER').'</td>
				<td class="iparam_td"><input class="iparam" value="[INVOICE_NUMBER]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
				</tr>', self::ORIENTATION_ORDER , $orientation);
		$description .= $this->checkOrientation('<tr>
			<td class="iparam_header">'.JText::_('VENDOR_NAME').'</td>
			<td class="iparam_td"><input class="iparam" value="[VENDOR_NAME]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
		</tr>', $allOrientation | self::ORIENTATION_WAITING_LIST , $orientation);
		$description .= $this->checkOrientation('<tr>
				<td class="iparam_header">'.JText::_('DAILY_TOTAL').'</td>
				<td class="iparam_td"><input class="iparam" value="[DAILY_TOTAL]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
				</tr>', $allOrientation , $orientation);

	$description .= '</tbody>
</table>';
		
		$title = JText::_( 'Template subject Variables' );
		$name = JText::_( 'Template subject Variables (drag & drop)' );
		$example = '';
		$subjectTags['title'] = $title;
		$subjectTags['name'] = $name;
		$subjectTags['description'] = $description;
		$subjectTags['example'] = $example;
		$subjectTags['order'] = 0;
		
		$availableTags[] = $subjectTags;
		
		$description = '<table border="0" class="paramlist admintable">
	<tbody>';
		$description .= $this->checkOrientation('<tr>
			<td class="iparam_header">'.JText::_('STORE_ADDRESS_FULL_HEADER').'</td>
			<td class="iparam_td"><input class="iparam" value="[STORE_ADDRESS_FULL_HEADER]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
		</tr>', $allOrientation | self::ORIENTATION_WAITING_LIST , $orientation);
		$description .= $this->checkOrientation('<tr>
			<td class="iparam_header">'.JText::_('ORDER_ITEMS_INFO').'</td>
			<td class="iparam_td"><input class="iparam" value="[ORDER_ITEMS_INFO]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
		</tr>', self::ORIENTATION_ORDER , $orientation);
		$description .= $this->checkOrientation('<tr>
				<td class="iparam_header">'.JText::_('ADMIN_ORDER_ITEMS_INFO').'</td>
				<td class="iparam_td"><input class="iparam" value="[ADMIN_ORDER_ITEMS_INFO]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
				</tr>', self::ORIENTATION_ORDER , $orientation);
		$description .= $this->checkOrientation('<tr>
				<td class="iparam_header">'.JText::_('DS_ORDER_ITEMS_INFO').'</td>
				<td class="iparam_td"><input class="iparam" value="[DS_ORDER_ITEMS_INFO|filter name|filter value]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
				</tr>', self::ORIENTATION_ORDER , $orientation);
		$description .= $this->checkOrientation('<tr>
			<td class="iparam_header">'.JText::_('SITENAME').'</td>
			<td class="iparam_td"><input class="iparam" value="[SITENAME]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
		</tr>', $allOrientation | self::ORIENTATION_WAITING_LIST , $orientation);
		$description .= $this->checkOrientation('<tr>
			<td class="iparam_header">'.JText::_('SITEURL').'</td>
			<td class="iparam_td"><input class="iparam" value="[SITEURL|Our site]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
		</tr>', $allOrientation | self::ORIENTATION_WAITING_LIST , $orientation);
		$description .= $this->checkOrientation('<tr>
				<td class="iparam_header">'.JText::_('CUSTOMER_NAME').'</td>
				<td class="iparam_td"><input class="iparam" value="[CUSTOMER_NAME]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
				</tr>', self::ORIENTATION_NEW_CUSTOMER | self::ORIENTATION_EXISTING_CUSTOMER , $orientation);
		$description .= $this->checkOrientation('<tr>
				<td class="iparam_header">'.JText::_('CUSTOMER_PASSWORD').'</td>
				<td class="iparam_td"><input class="iparam" value="[CUSTOMER_PASSWORD]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
				</tr>', self::ORIENTATION_NEW_CUSTOMER , $orientation);
		$description .= $this->checkOrientation('<tr>
				<td class="iparam_header">'.JText::_('ACTIVATION_LINK').'</td>
				<td class="iparam_td"><input class="iparam" value="[ACTIVATION_LINK|activation link]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
				</tr>', self::ORIENTATION_NEW_CUSTOMER | self::ORIENTATION_EXISTING_CUSTOMER , $orientation);
		$description .= $this->checkOrientation('<tr>
			<td class="iparam_header">'.JText::_('CUSTOMER_USER_NAME').'</td>
			<td class="iparam_td"><input class="iparam" value="[CUSTOMER_USER_NAME]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
		</tr>', self::ORIENTATION_NEW_CUSTOMER | self::ORIENTATION_EXISTING_CUSTOMER|self::ORIENTATION_ORDER , $orientation);
		$description .= $this->checkOrientation('<tr>
			<td class="iparam_header">'.JText::_('CUSTOMER_FIRST_NAME').'</td>
			<td class="iparam_td"><input class="iparam" value="[CUSTOMER_FIRST_NAME]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
		</tr>', $allOrientation , $orientation);
		$description .= $this->checkOrientation('<tr>
			<td class="iparam_header">'.JText::_('CUSTOMER_LAST_NAME').'</td>
			<td class="iparam_td"><input class="iparam" value="[CUSTOMER_LAST_NAME]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
		</tr>', $allOrientation , $orientation);
		$description .= $this->checkOrientation('<tr>
			<td class="iparam_header">'.JText::_('CUSTOMER_EMAIL').'</td>
			<td class="iparam_td"><input class="iparam" value="[CUSTOMER_EMAIL]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
		</tr>', $allOrientation , $orientation);
		$description .= $this->checkOrientation('<tr>
				<td class="iparam_header">'.JText::_('CUSTOMER_TOTAL').'</td>
				<td class="iparam_td"><input class="iparam" value="[CUSTOMER_TOTAL]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
				</tr>', self::ORIENTATION_EXISTING_CUSTOMER | self::ORIENTATION_ORDER , $orientation);
		$description .= $this->checkOrientation('<tr>
				<td class="iparam_header">'.JText::_('CUSTOMER_ORDERS_COUNT').'</td>
				<td class="iparam_td"><input class="iparam" value="[CUSTOMER_ORDERS_COUNT]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
				</tr>', self::ORIENTATION_EXISTING_CUSTOMER | self::ORIENTATION_ORDER , $orientation);
		$description .= $this->checkOrientation('<tr>
			<td class="iparam_header">'.JText::_('TODAY_DATE').'</td>
			<td class="iparam_td"><input class="iparam" value="[TODAY_DATE]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
		</tr>', $allOrientation | self::ORIENTATION_WAITING_LIST , $orientation);
		$description .= $this->checkOrientation('<tr>
			<td class="iparam_header">'.JText::_('TODAY_DATE_LOCALE').'</td>
			<td class="iparam_td"><input class="iparam" value="[TODAY_DATE_LOCALE|locale]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
		</tr>', $allOrientation | self::ORIENTATION_WAITING_LIST , $orientation);
		$description .= $this->checkOrientation('<tr>
			<td class="iparam_header">'.JText::_('ORDER_ID').'</td>
			<td class="iparam_td"><input class="iparam" value="[ORDER_ID]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
		</tr>', self::ORIENTATION_ORDER , $orientation);
		$description .= $this->checkOrientation('<tr>
				<td class="iparam_header">'.JText::_('VM_ORDER_ID').'</td>
				<td class="iparam_td"><input class="iparam" value="[VM_ORDER_ID]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
				</tr>', self::ORIENTATION_ORDER , $orientation);
		$description .= $this->checkOrientation('<tr>
			<td class="iparam_header">'.JText::_('ORDER_STATUS').'</td>
			<td class="iparam_td"><input class="iparam" value="[ORDER_STATUS]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
		</tr>', self::ORIENTATION_ORDER , $orientation);
		$description .= $this->checkOrientation('<tr>
				<td class="iparam_header">'.JText::_('ORDER_STATUS_COMMENT').'</td>
				<td class="iparam_td"><input class="iparam" value="[ORDER_STATUS_COMMENT]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
				</tr>', self::ORIENTATION_ORDER , $orientation);
		$description .= $this->checkOrientation('<tr>
				<td class="iparam_header">'.JText::_('INVOICE_NUMBER').'</td>
				<td class="iparam_td"><input class="iparam" value="[INVOICE_NUMBER]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
				</tr>', self::ORIENTATION_ORDER , $orientation);
		$description .= $this->checkOrientation('<tr>
				<td class="iparam_header">'.JText::_('INVOICE_LINK').'</td>
				<td class="iparam_td"><input class="iparam" value="[INVOICE_LINK|invoice link text]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
				</tr>', self::ORIENTATION_ORDER , $orientation);
		$description .= $this->checkOrientation('<tr>
			<td class="iparam_header">'.JText::_('BILL_TO_SHIP_TO').'</td>
			<td class="iparam_td"><input class="iparam" value="[BILL_TO_SHIP_TO]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
		</tr>', self::ORIENTATION_ORDER | self::ORIENTATION_EXISTING_CUSTOMER , $orientation);
		$description .= $this->checkOrientation('<tr>
			<td class="iparam_header">'.JText::_('COUPON_DISCOUNT').'</td>
			<td class="iparam_td"><input class="iparam" value="[COUPON_DISCOUNT]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
		</tr>', self::ORIENTATION_ORDER , $orientation);
		$description .= $this->checkOrientation('<tr>
			<td class="iparam_header">'.JText::_('ORDER_DATE').'</td>
			<td class="iparam_td"><input class="iparam" value="[ORDER_DATE]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
		</tr>', self::ORIENTATION_ORDER , $orientation);
		$description .= $this->checkOrientation('<tr>
			<td class="iparam_header">'.JText::_('ORDER_DATE_LOCALE').'</td>
			<td class="iparam_td"><input class="iparam" value="[ORDER_DATE_LOCALE|locale]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
		</tr>', self::ORIENTATION_ORDER , $orientation);
		$description .= $this->checkOrientation('<tr>
				<td class="iparam_header">'.JText::_('ORDER_DELIVERY_DATE').'</td>
				<td class="iparam_td"><input class="iparam" value="[ORDER_DELIVERY_DATE]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
				</tr>', self::ORIENTATION_ORDER , $orientation);
		$description .= $this->checkOrientation('<tr>
			<td class="iparam_header">'.JText::_('ORDER_LINK').'</td>
			<td class="iparam_td"><input class="iparam" value="[ORDER_LINK|order link]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
		</tr>', self::ORIENTATION_ORDER , $orientation);
		$description .= $this->checkOrientation('<tr>
				<td class="iparam_header">'.JText::_('ADMIN_ORDER_LINK').'</td>
				<td class="iparam_td"><input class="iparam" value="[ADMIN_ORDER_LINK|order link]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
				</tr>', self::ORIENTATION_ORDER , $orientation);
		$description .= $this->checkOrientation('<tr>
			<td class="iparam_header">'.JText::_('PAYMENT_INFO_DETAILS').'</td>
			<td class="iparam_td"><input class="iparam" value="[PAYMENT_INFO_DETAILS]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
		</tr>', self::ORIENTATION_ORDER , $orientation);
		$description .= $this->checkOrientation('<tr>
			<td class="iparam_header">'.JText::_('SHIPPING_INFO_DETAILS').'</td>
			<td class="iparam_td"><input class="iparam" value="[SHIPPING_INFO_DETAILS]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
		</tr>', self::ORIENTATION_ORDER , $orientation);
		$description .= $this->checkOrientation('<tr>
			<td class="iparam_header">'.JText::_('CUSTOMER_NOTE').'</td>
			<td class="iparam_td"><input class="iparam" value="[CUSTOMER_NOTE]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
		</tr>', self::ORIENTATION_ORDER , $orientation);
		$description .= $this->checkOrientation('<tr>
			<td class="iparam_header">'.JText::_('ORDER_SUB_TOTAL').'</td>
			<td class="iparam_td"><input class="iparam" value="[ORDER_SUB_TOTAL]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
		</tr>', self::ORIENTATION_ORDER , $orientation);
		$description .= $this->checkOrientation('<tr>
			<td class="iparam_header">'.JText::_('ORDER_SHIPPING').'</td>
			<td class="iparam_td"><input class="iparam" value="[ORDER_SHIPPING]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
		</tr>', self::ORIENTATION_ORDER , $orientation);
		$description .= $this->checkOrientation('<tr>
			<td class="iparam_header">'.JText::_('ORDER_TAX').'</td>
			<td class="iparam_td"><input class="iparam" value="[ORDER_TAX]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
		</tr>', self::ORIENTATION_ORDER , $orientation);
		$description .= $this->checkOrientation('<tr>
			<td class="iparam_header">'.JText::_('ORDER_TOTAL').'</td>
			<td class="iparam_td"><input class="iparam" value="[ORDER_TOTAL]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
		</tr>', self::ORIENTATION_ORDER , $orientation);
		$description .= $this->checkOrientation('<tr>
			<td class="iparam_header">'.JText::_('VENDOR_NAME').'</td>
			<td class="iparam_td"><input class="iparam" value="[VENDOR_NAME]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
		</tr>', $allOrientation | self::ORIENTATION_WAITING_LIST , $orientation);
		$description .= $this->checkOrientation('<tr>
			<td class="iparam_header">'.JText::_('CONTACT_EMAIL').'</td>
			<td class="iparam_td"><input class="iparam" value="[CONTACT_EMAIL]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
		</tr>', $allOrientation | self::ORIENTATION_WAITING_LIST , $orientation);
		$description .= $this->checkOrientation('<tr>
			<td class="iparam_header" style="border-top:1px solid grey;">'.JText::_('BILL_TO_FIELDS').'</td>
			<td class="iparam_td"><input class="iparam" value="[BT_COUNTRY]" onclick="' .$onClick. '" onfocus="'.$onFocus. '"  readonly="readonly"  readonly="readonly" /><br><input class="iparam" value="[BT_COMPANY]" onclick="' .$onClick. '" onfocus="'.$onFocus. '"  readonly="readonly"  readonly="readonly" /><br><input class="iparam" value="[BT_TITLE]" onclick="' .$onClick. '" onfocus="'.$onFocus. '"  readonly="readonly"  readonly="readonly" /><br><input class="iparam" value="[BT_FIRST_NAME]" onclick="' .$onClick. '" onfocus="'.$onFocus. '"  readonly="readonly"  readonly="readonly" /><br><input class="iparam" value="[BT_LAST_NAME]" onclick="' .$onClick. '" onfocus="'.$onFocus. '"  readonly="readonly"  readonly="readonly" /><br><input class="iparam" value="[BT_MIDDLE_NAME]" onclick="' .$onClick. '" onfocus="'.$onFocus. '"  readonly="readonly"  readonly="readonly" /><br><input class="iparam" value="[BT_ADDRESS_1]" onclick="' .$onClick. '" onfocus="'.$onFocus. '"  readonly="readonly" /><br><input class="iparam" value="[BT_ADDRESS_2]" onclick="' .$onClick. '" onfocus="'.$onFocus. '"  readonly="readonly" /><br><input class="iparam" value="[BT_CITY]" onclick="' .$onClick. '" onfocus="'.$onFocus. '"  readonly="readonly" /><br><input class="iparam" value="[BT_ZIP]" onclick="' .$onClick. '" onfocus="'.$onFocus. '"  readonly="readonly" /><br><input class="iparam" value="[BT_STATE]" onclick="' .$onClick. '" onfocus="'.$onFocus. '"  readonly="readonly" /><br><input class="iparam" value="[BT_PHONE_1]" onclick="' .$onClick. '" onfocus="'.$onFocus. '"  readonly="readonly" /><br><input class="iparam" value="[BT_PHONE_2]" onclick="' .$onClick. '" onfocus="'.$onFocus. '"  readonly="readonly" /><br><input class="iparam" value="[BT_FAX]" onclick="' .$onClick. '" onfocus="'.$onFocus. '"  readonly="readonly" /></td>
		</tr>', self::ORIENTATION_EXISTING_CUSTOMER | self::ORIENTATION_ORDER , $orientation);
		$description .= $this->checkOrientation('<tr>
			<td class="iparam_header" style="border-top:1px solid grey;">'.JText::_('SHIP_TO_FIELDS').'</td>
			<td class="iparam_td"><input class="iparam" value="[ST_COUNTRY]" onclick="' .$onClick. '" onfocus="'.$onFocus. '"  readonly="readonly" /><br><input class="iparam" value="[ST_COMPANY]" onclick="' .$onClick. '" onfocus="'.$onFocus. '"  readonly="readonly" /><br><input class="iparam" value="[ST_TITLE]" onclick="' .$onClick. '" onfocus="'.$onFocus. '"  readonly="readonly" /><br><input class="iparam" value="[ST_FIRST_NAME]" onclick="' .$onClick. '" onfocus="'.$onFocus. '"  readonly="readonly" /><br><input class="iparam" value="[ST_LAST_NAME]" onclick="' .$onClick. '" onfocus="'.$onFocus. '"  readonly="readonly" /><br><input class="iparam" value="[ST_MIDDLE_NAME]" onclick="' .$onClick. '" onfocus="'.$onFocus. '"  readonly="readonly" /><br><input class="iparam" value="[ST_ADDRESS_1]" onclick="' .$onClick. '" onfocus="'.$onFocus. '"  readonly="readonly" /><br><input class="iparam" value="[ST_ADDRESS_2]" onclick="' .$onClick. '" onfocus="'.$onFocus. '"  readonly="readonly" /><br><input class="iparam" value="[ST_CITY]" onclick="' .$onClick. '" onfocus="'.$onFocus. '"  readonly="readonly" /><br><input class="iparam" value="[ST_ZIP]" onclick="' .$onClick. '" onfocus="'.$onFocus. '"  readonly="readonly" /><br><input class="iparam" value="[ST_STATE]" onclick="' .$onClick. '" onfocus="'.$onFocus. '"  readonly="readonly" /><br><input class="iparam" value="[ST_PHONE_1]" onclick="' .$onClick. '" onfocus="'.$onFocus. '"  readonly="readonly" /><br><input class="iparam" value="[ST_PHONE_2]" onclick="' .$onClick. '" onfocus="'.$onFocus. '"  readonly="readonly" /><br><input class="iparam" value="[ST_FAX]" onclick="' .$onClick. '" onfocus="'.$onFocus. '"  readonly="readonly" /><br><input class="iparam" value="[ST_ADDRESS_TYPE_NAME]" onclick="' .$onClick. '" onfocus="'.$onFocus. '"  readonly="readonly" /></td>
		</tr>', self::ORIENTATION_EXISTING_CUSTOMER | self::ORIENTATION_ORDER , $orientation);
		$description .= $this->checkOrientation('<tr>
			<td class="iparam_header" style="border-top:1px solid grey;">'.JText::_('PRODUCTS_COUNT').'</td>
			<td class="iparam_td"><input class="iparam" value="[PRODUCTS_COUNT]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly" /></td>
		</tr>', self::ORIENTATION_ORDER , $orientation);
		$description .= $this->checkOrientation('<tr>
			<td class="iparam_header" style="border-top:1px solid grey;">'.JText::_('ITEMS_COUNT').'</td>
			<td class="iparam_td"><input class="iparam" value="[ITEMS_COUNT]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly" /></td>
		</tr>', self::ORIENTATION_ORDER , $orientation);
		$description .= $this->checkOrientation('<tr>
			<td class="iparam_header" style="border-top:1px solid grey;">'.JText::_('SHOPPER_GROUP').'</td>
			<td class="iparam_td"><input class="iparam" value="[SHOPPER_GROUP]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly" /></td>
		</tr>', $allOrientation^self::ORIENTATION_CART , $orientation);
		$description .= $this->checkOrientation('<tr>
			<td class="iparam_header" style="border-top:1px solid grey;">'.JText::_('MANUFACTURER_FIELDS').'</td>
			<td class="iparam_td"><input class="iparam" value="[MANUFACTURER_ID]" onclick="'.$onClick.'" onfocus="'.$onFocus . '"  readonly="readonly" /><br><input class="iparam" value="[MANUFACTURER_NAME]" onclick="'.$onClick.'" onfocus="'.$onFocus . '"  readonly="readonly" /><br><input class="iparam" value="[MANUFACTURER_EMAIL]" onclick="'.$onClick.'" onfocus="'.$onFocus . '"  readonly="readonly" /><br><input class="iparam" value="[MANUFACTURER_DESC]" onclick="'.$onClick.'" onfocus="'.$onFocus . '"  readonly="readonly" /><br><input class="iparam" value="[MANUFACTURER_CAT_ID]" onclick="'.$onClick.'" onfocus="'.$onFocus . '"  readonly="readonly" /><br><input class="iparam" value="[MANUFACTURER_URL]" onclick="'.$onClick.'" onfocus="'.$onFocus . '"  readonly="readonly" /></td>
		</tr>', self::ORIENTATION_ORDER , $orientation);
		$description .= $this->checkOrientation('<tr>
				<td class="iparam_header" style="border-top:1px solid grey;">'.JText::_('CART_INFO').'</td>
				<td class="iparam_td"><input class="iparam" value="[CART_INFO]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly" /></td>
				</tr>', self::ORIENTATION_CART , $orientation);
		$description .= $this->checkOrientation('<tr>
				<td class="iparam_header">'.JText::_('DAILY_TOTAL').'</td>
				<td class="iparam_td"><input class="iparam" value="[DAILY_TOTAL]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
				</tr>', $allOrientation , $orientation);
		$description .= $this->checkOrientation('<tr>
				<td class="iparam_header">'.JText::_('WAITING_LIST_PRODUCT_NAME').'</td>
				<td class="iparam_td"><input class="iparam" value="[WAITING_LIST_PRODUCT_NAME]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
				</tr>', self::ORIENTATION_WAITING_LIST , $orientation);
		$description .= $this->checkOrientation('<tr>
				<td class="iparam_header">'.JText::_('WAITING_LIST_PRODUCT_LINK').'</td>
				<td class="iparam_td"><input class="iparam" value="[WAITING_LIST_PRODUCT_LINK]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
				</tr>', self::ORIENTATION_WAITING_LIST , $orientation);
		$description .= $this->checkOrientation('<tr>
				<td class="iparam_header">'.JText::_('WAITING_LIST_PRODUCT_IMG').'</td>
				<td class="iparam_td"><input class="iparam" value="[WAITING_LIST_PRODUCT_IMG]" onclick="' . $onClick . '" onfocus="' . $onFocus . '"  readonly="readonly"  readonly="readonly" /></td>
				</tr>', self::ORIENTATION_WAITING_LIST , $orientation);
	$description .= '</tbody>
</table>';
		$title = JText::_( 'Template body variables' );
		$name = JText::_( 'Template body variables' );
		$example = '';
		$bodyTags['title'] = $title;
		$bodyTags['name'] = $name;
		$bodyTags['description'] = $description;
		$bodyTags['example'] = $example;
		$bodyTags['order'] = 1;
		
		$availableTags[] = $bodyTags;
		
		$description = '<table border="0" class="paramlist admintable">
	<tbody>';
		$description .= $this->checkOrientation('<tr>
				<td class="iparam_header"  style="border-top:1px solid grey;"><strong>'.JText::_('CALC_FIELDS').'</strong><br/><br/>
				<strong>'.JText::_('EXAMPLE').':</strong><br/> [CALC|0.15*[ORDER_TOTAL]]<br/>[CALC|0.1*[DAILY_TOTAL]]<br/>[CALC|(5+2)*(6-1)]</td>
				<td class="iparam_td">
				<p>[CALC|Phrase]</p>
				<p>'.JText::_('CALC_FIELDS_DESCRIPTION').'</p>
				</td>
				</tr>', $allOrientation, $orientation);
		$description .= $this->checkOrientation('<tr>
				<td class="iparam_header"  style="border-top:1px solid grey;"><strong>'.JText::_('PRODUCTS_LINKS_FIELDS').'</strong><br/><br/>
				<strong>'.JText::_('EXAMPLE').':</strong> [PRODUCTS_LINKS|mystore|reviews|21]<br/>[PRODUCTS_LINKS|||21]<br/>[PRODUCTS_LINKS]</td>
				<td class="iparam_td">
				<p>PRODUCTS_LINKS|sef_frefix|anchor_in_page|product_id]</p>
				<p>'.JText::_('PRODUCTS_LINKS_FIELDS_DESCRIPTION').'</p>
				</td>
				</tr>', self::ORIENTATION_ORDER |  self::ORIENTATION_CART, $orientation);
		$description .= $this->checkOrientation('<tr>
			<td class="iparam_header"  ><strong>'.JText::_('CUSTOM_ORDER_FIELDS').'</strong><br/><br/>
<strong>'.JText::_('EXAMPLE').':</strong> [CUSTOM_ORDERS:order_id]</td>
			<td class="iparam_td">[CUSTOM_ORDERS:replace_this_with_field_name]</td>
		</tr>', self::ORIENTATION_ORDER , $orientation);
		$description .= $this->checkOrientation('<tr>
			<td class="iparam_header"  style="border-top:1px solid grey;"><strong>'.JText::_('CUSTOM_USER_INFO_FIELDS').'</strong><br/><br/>
<strong>'.JText::_('EXAMPLE').':</strong> <p>[CUSTOM_USER_INFO:extra_field_1] - Billing fields</p><p>[CUSTOM_USER_INFO_ST:extra_field_1] - Shipping fields</p></td>
			<td class="iparam_td" >[CUSTOM_USER_INFO:replace_this_with_field_name]</td>
		</tr>', $allOrientation^self::ORIENTATION_CART , $orientation);
		$description .= $this->checkOrientation('<tr>
			<td class="iparam_header"  style="border-top:1px solid grey;"><strong>'.JText::_('LANGUAGE_FIELDS').'</strong><br/><br/>
<strong>'.JText::_('EXAMPLE').':</strong> [LANG:REG_COMPLETE_ACTIVATE]</td>
			<td class="iparam_td">
			<p>[LANG:value_from_vm_language_file]</p>
			<p>'.JText::_('LANGUAGE_FIELDS_DESCRIPTION').'<br>administrator/language/LANG_CODE/LANG_CODE.com_virtuemart.ini</p>
			</td>
		</tr>', $allOrientation | self::ORIENTATION_WAITING_LIST , $orientation);
		$description .= $this->checkOrientation('<tr>
			<td class="iparam_header"  style="border-top:1px solid grey;"><strong>'.JText::_('ARTICLE_ID_TAGS').'</strong><br/><br/>
<strong>'.JText::_('EXAMPLE').':</strong> [ARTICLE_ID:43]</td>
			<td class="iparam_td"><p>[ARTICLE_ID:article_id_#]</p>'.JText::_('ARTICLE_ID_TAGS_DESCRIPTION').'</td>
		</tr>', $allOrientation | self::ORIENTATION_WAITING_LIST , $orientation);
	$description .= '</tbody>
</table>';
		$title = JText::_( 'Advanced template Variables' );
		$name = JText::_( 'Advanced template Variables' );
		$example = '';
		$advancedTags['title'] = $title;
		$advancedTags['name'] = $name;
		$advancedTags['description'] = $description;
		$advancedTags['example'] = $example;
		$advancedTags['order'] = 2;
		
		$availableTags[] = $advancedTags;
		return $availableTags;
	}
	
	
	function replaceArticleTags($str){
		$tempDb	= JFactory::getDBO();
		preg_match_all('/\[ARTICLE_ID:[^\]]*\]/s', $str, $arr, PREG_PATTERN_ORDER);
		if(is_array($arr[0])){
			foreach ($arr[0] as $article_label){
				preg_match('/\[ARTICLE_ID:([^\]]*)\]/', $article_label, $inner_arr);
				$article_tag = trim($inner_arr[1]);
				$q = "SELECT `introtext`, `fulltext` FROM #__content WHERE id=".$article_tag;
				$tempDb->setQuery($q);
				$result = $tempDb->loadAssoc();
				if($result){
					$articleBody = $result['introtext'].$result['fulltext'];
					$replace = $articleBody;
					$str = str_replace($article_label, $articleBody, $str);
				}
			}
		}
		return $str;
		
		$q = "SELECT `introtext`, `fulltext` FROM #__content WHERE id=".$articleId;
		$tempDb->setQuery($q);
		$result = $tempDb->loadAssoc();
		$templateBody = $result['introtext'].$result['fulltext'];
	}
	
	function replaceLanguageTags($str){
		preg_match_all('/\[LANG:[^\]]*\]/s', $str, $arr, PREG_PATTERN_ORDER);
		if(is_array($arr[0])){
			foreach ($arr[0] as $custom_label){
				preg_match('/\[LANG:([^\]]*)\]/', $custom_label, $inner_arr);
				$lang_tag = trim($inner_arr[1]);
				$replace = JText::_($lang_tag) ? JText::_($lang_tag) : '[LANG:'.$lang_tag.']';
				
				$str = str_replace( $custom_label, $replace, $str);
			}
		}
		return $str;
	}
	
	private function checkOrientation($data, $allowedOrientatiom, $testedOrientation){
		$res = '';
		if($allowedOrientatiom & $testedOrientation){
			$res = $data;
		}
	
		return $res;
	}
	
	private function replaceCalcTags(&$str){
		$tagsArr = array();
		$pattern = '/\[CALC\s*?\|(.*?)\]/';
		$tagspos = emp_helper::preg_pos_all($pattern,$str,$tagsArr);
		$offset = 0;
		foreach ($tagsArr as $idx=>$tag){
			$length = strlen($tag);
			$tag = trim($tag,'[]');
			$tagParts = explode('|', $tag);
			$phrase = trim($tagParts[1]);
			$phrase = preg_replace('/[^0-9^\.^\*^\+^\\^\-^\(^\)]/', '', $phrase);
			$compute = 0;
			eval("\$compute = $phrase;");
			$str = substr_replace($str, $compute, $tagspos[$idx]+$offset,$length);
			$offset -= $length;
			$offset += strlen($compute);
		}
		return $str;
	}
}
?>