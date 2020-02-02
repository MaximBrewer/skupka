<?php

if (!defined('_JEXEC'))
die('Direct Access to ' . basename(__FILE__) . ' is not allowed.');

/**
 * Calculation plugin for MOSS and EU VAT
 *
 * @version $Id: 2.0.0
 * @package moss_calc for Virtuemart 3+
 * @subpackage Plugins
 * @author RuposTel.com
 * @copyright Copyright (C) RuposTel.com
 * @license commercial
 *
 *
 */

if (!class_exists('VmConfig'))
{
	require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	VmConfig::loadConfig(); 
}

if (!class_exists('vmPlugin'))
{
if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'vmplugin.php'))
{
if (!JFactory::getApplication()->isAdmin())
require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'vmplugin.php'); 
}
}

if (!class_exists('vmPSplugin'))
{
	
	require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmpsplugin.php'); 
}

if (!class_exists('vmCalculationPlugin')) require(JPATH_VM_PLUGINS.DIRECTORY_SEPARATOR.'vmcalculationplugin.php');


class plgVmCalculationMoss_calc extends vmCalculationPlugin {

	
    var $_calc_name = array(); 
	var $_calc_name_name = array();
	
	var $_xParamsP = ''; 
	var $_varsToPushParamP = ''; 
	var $methods = array(); 
	
	function __construct(& $subject, $config) {
		
		

		$varsToPush = array();
		
		$varsToPush = array(
			
			'activated'          => array(0, 'int'),
			'google_url'		=> array('', 'varchar(1025)'),
			'moss_mode'		    => array(0, 'int'), 
			'tcode_home' => array(0, 'int'),
			'tcode_eu_vat' => array(0, 'int'),
			'tcode_eu_private' => array(0, 'int'),
			'tcode_zero_eu' => array(0, 'int'),
			'tcode_zero_us' => array(0, 'int')
		);
		
		$this->varsToPush = $varsToPush; 
		
		$this->setConfigParameterable ('calc_params', $varsToPush);

		$this->_loggable = TRUE;
		$this->tableFields = array('id', 'virtuemart_order_id', 'tax_id');
		$this->_tableId = 'id';
		$this->_tablepkey = 'id';
		
		$this->taxTable =& $this->_tablename; 
		
		require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'helper.php'); 
		
		$this->helper = new mossTaxHelper($this); 
	   
		$this->_xParamsP =& $this->_xParams; 
		
		$this->_varsToPushParamP =& $this->_varsToPushParam; 
		
		if (!defined('MOSS_TAX'))
		define('MOSS_TAX', 'moss'); 
	
		parent::__construct($subject, $config);
		$this->helper->params = $this->params; 
		$this->helper->ref =& $this; 
		
		
		
		
if (!class_exists('calculationHelper'))
{
	require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'calculationh.php'); 
	
}
		
		
		if (!class_exists('VirtueMartCart')) require(JPATH_SITE. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_virtuemart' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'cart.php');
		
	}
	public function plgVmonSelectedCalculatePricePayment_removed( $cart, &$cartPrices, &$paymentName  )
	{
		$calc =& calculationHelper::getInstance(); 
		if (isset($calc->_cart->cartData['VatTax']))
		{
			if (count($calc->_cart->cartData['VatTax'])===1)
			{
				$first = reset($calc->_cart->cartData['VatTax']); 
				if ((count($first)===1) && (isset($first['DBTax'])) && (empty($first['DBTax'])))
				{
					$calc->_cart->cartData['VatTax'] = array(); 
					
				}
			}
		}
	}
	
	function getTableSQLFields() {
		$SQLfields = array(
			'id' => ' int(1) UNSIGNED NOT NULL AUTO_INCREMENT',
			'virtuemart_calc_id' => 'int(1) UNSIGNED NOT NULL DEFAULT \'0\'',
			'activated' => 'int(1) NOT NULL DEFAULT \'0\'',
			'google_url' => 'varchar(1025)',
			'tcode_home' => 'int(1) NOT NULL DEFAULT \'1\'',
			'tcode_eu_vat' => 'int(1) NOT NULL DEFAULT \'4\'',
			'tcode_eu_private' => 'int(1) NOT NULL DEFAULT \'1\'',
			'tcode_zero_eu' => 'int(1) NOT NULL DEFAULT \'2\'',
			'tcode_zero_us' => 'int(1) NOT NULL DEFAULT \'2\'',
			'moss_mode'		    => 'int(1) NOT NULL DEFAULT \'0\'',
		);
		return $SQLfields;
	}

	function checkTcodeCol() {
	    $res = mossTaxHelper::getColumns('virtuemart_calc_plg_moss_calc_config'); 
		
		$sql = $this->getVmPluginCreateTableSQLData(); 
		$db = JFactory::getDBO(); 
		$db->setQuery($sql); 
		$db->query(); 
		
		if (!isset($res['tcode'])) {
			
			$q = 'ALTER TABLE `#__virtuemart_calc_plg_moss_calc_config` ADD `tcode` INT NULL DEFAULT NULL AFTER `tax_rate`';
			$db = JFactory::getDBO(); 
			$db->setQuery($q); 
			$db->query(); 
			
		}

	}
	function plgVmOnStoreInstallPluginTable($jplugin_name,$name,$table=0) {
		
		//vmdebug('plgVmOnStoreInstallPluginTable',$jplugin_name,$name);
		if(!defined('VM_VERSION') or VM_VERSION < 3){
			$ret = $this->onStoreInstallPluginTable($jplugin_name,$name);
		} else {
			$ret = $this->onStoreInstallPluginTable ($jplugin_name);
			$this->plgVmStorePluginInternalDataCalc($name);
		}
		
		$this->checkTcodeCol(); 
		$this->createOrderItemsTable(); 
		
		
		return $ret; 
	}
	public function createOrderItemsTable() {
		$q = "CREATE TABLE IF NOT EXISTS `" . $this->_tablename . "_orderitems` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `virtuemart_order_id` int(1) UNSIGNED NOT NULL DEFAULT '0',
  `virtuemart_order_item_id` int(1) UNSIGNED NOT NULL DEFAULT '0',
  `tcode` int(1) UNSIGNED NOT NULL DEFAULT '99',
  `tax_rate` decimal(10,6) NOT NULL DEFAULT '0.000000',
  `vat_delivery` varchar(2) CHARACTER SET ascii NOT NULL DEFAULT '',
  `product_subtotal_net` decimal(25,15) NOT NULL DEFAULT '0.000000000000000',
  `product_subtotal_tax` decimal(25,15) NOT NULL DEFAULT '0.000000000000000',
  `product_subtotal_gross` decimal(25,15) NOT NULL DEFAULT '0.000000000000000',
  `product_item_net` decimal(25,15) NOT NULL DEFAULT '0.000000000000000',
  `product_item_tax` decimal(25,15) NOT NULL DEFAULT '0.000000000000000',
  `product_item_gross` decimal(25,15) NOT NULL DEFAULT '0.000000000000000',
  `product_quantity` decimal(25,15) NOT NULL DEFAULT '0.000000000000000',
  PRIMARY KEY (`id`),
  KEY `order_id` (`virtuemart_order_id`),
  KEY `order_item_id` (`virtuemart_order_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;"; 
$db = JFactory::getDBO(); 
$db->setQuery($q); 
$db->query(); 



	}
	public function getVmPluginCreateTableSQLData() {
		return "CREATE TABLE IF NOT EXISTS `" . $this->_tablename . "_config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `virtuemart_calc_id` mediumint(1) unsigned DEFAULT NULL,
  `virtuemart_country_id` int(10) unsigned DEFAULT NULL,
  `tax_rate` decimal(10,4) unsigned DEFAULT NULL,
  `tcode` int(10) unsigned DEFAULT NULL,
  `created_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `modified_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) NOT NULL DEFAULT '0',
  `locked_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `locked_by` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),  
  KEY `idx_virtuemart_calc_id` (`virtuemart_calc_id`),
  KEY `virtuemart_country_id` (`virtuemart_country_id`)
  
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Table for MOSS rates' AUTO_INCREMENT=1 ;"; 
 		

	}

	/**
	 * Gets the sql for creation of the table
	 * This table is used to store order information per plugin
	 * @author RuposTel.com
	 */
	public function getVmPluginCreateTableSQL() {
		
 		return "CREATE TABLE IF NOT EXISTS `" . $this->_tablename . "` (
 			    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `virtuemart_calc_id` mediumint(1) unsigned DEFAULT NULL,
  `virtuemart_country_id` int(10) unsigned DEFAULT NULL,
  `tax_rate` decimal(10,4) unsigned DEFAULT NULL,
  `tcode` int(10) unsigned DEFAULT NULL,
  `created_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `modified_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) NOT NULL DEFAULT '0',
  `locked_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `locked_by` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),  
  KEY `idx_virtuemart_calc_id` (`virtuemart_calc_id`),
  KEY `virtuemart_country_id` (`virtuemart_country_id`)
 			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Table for US tax based on Zip Code' AUTO_INCREMENT=1 ;";
			//ALTER TABLE  `rupostel_vm2onj2`.`rg6ma_virtuemart_calc_plg_moss_calc_config` ADD UNIQUE  `search` (  `zip_start` ,  `zip_end` ,  `virtuemart_calc_id` )
	}


	function plgVmAddMathOp(&$entryPoints){
	    
 		$entryPoints[] = array('calc_value_mathop' => MOSS_TAX, 'calc_value_mathop_name' => 'MOSS Rates and EU VAT');
	}
	
	function _isMossMode($id=0)
	{
	   $calculationH = calculationHelper::getInstance(); 
	   $this->_getMethods($calculationH);
	   $id = (int)$id; 
	   
	   
	   
	   foreach ($this->methods as $idM=>$method)
	   {
		   if ($id === $idM) {
			   
			   if (!empty($method->moss_mode))
			   {
				   return true; 
			   }
			   
		   }
	   }
	   return false; 
	}
	
	function plgVmOnDisplayEdit(&$calc,&$html){
		
	    if ($calc->calc_value_mathop !== MOSS_TAX)
		{
			return;
		}
		
		
		
		$isMoss = $this->_isMossMode($calc->virtuemart_calc_id); 
		
		//$this->params->get('moss_mode', true); 
		if ($isMoss) {
		 $checked = ' selected="selected" ';  
		}
		else 
		{
			$checked = ''; 
		}
		
		$moss_html = '<div style="width: 100%;"><label for="moss_mode">'.JText::_('VMCALCULATION_MOSS_CALC_CHOOSETAXMODE').'<br />
			<select name="moss_mode" id="moss_mode" >
		    <option value="0">'.JText::_('VMCALCULATION_MOSS_CALC_CHOOSETAXMODE_EUVAT').'</option>
			<option value="1" '.$checked.'>'.JText::_('VMCALCULATION_MOSS_CALC_CHOOSETAXMODE_MOSS').'</option>
			</select></label><div style=width: 100%;">'.JText::_('VMCALCULATION_MOSS_CALC_CHOOSETAXMODE_DESC').'</div></div>'; 
		
		$html .= $moss_html; 
		
		$html .= '<fieldset><legend>'.JText::_('VMCALCULATION_MOSS_CALC_TAXCODE').'<legend>'; 
		ob_start(); 
		?>
		<table>
		<tr>
		<td>
		<label for="tcode_home"><?php echo JText::_('VMCALCULATION_MOSS_CALC_TAXCODE_HOME'); ?></label>
		</td>
		<td>
		<?php 
		  if (!empty($calc->tcode_home)) $tcode_home = (int)$calc->tcode_home; 
		  else $tcode_home = $calc->tcode_home = 1; 
		  echo $this->getTCodeDropDown(' name="tcode_home" id="tcode_home" style="min-width: 200px;" ', $tcode_home); 
		?></td>
		</tr>
		<tr>
		<td>
		<label for="tcode_eu_vat"><?php echo JText::_('VMCALCULATION_MOSS_CALC_TAXCODE_EUVAT'); ?></label>
		</td>
		<td>
		<?php 
		  if (!empty($calc->tcode_eu_vat)) $tcode_eu_vat = (int)$calc->tcode_eu_vat; 
		  else {
			  if ($this->_isMossMode($calc->virtuemart_calc_id)) {
				  $tcode_eu_vat = $calc->tcode_eu_vat = 22; 
			  }
			  else {
			    $tcode_eu_vat = $calc->tcode_eu_vat = 4; 
			  }
		  }
		  echo $this->getTCodeDropDown(' name="tcode_eu_vat" id="tcode_eu_vat" style="min-width: 200px;" ', $tcode_eu_vat); 
		?></td>
		</tr>
		
		 <?php if (!$this->_isMossMode($calc->virtuemart_calc_id)) { ?>
		 <tr>
		 <td>
		<label for="tcode_eu_private"><?php echo JText::_('VMCALCULATION_MOSS_CALC_TAXCODE_EUPRIVATE'); ?></label>
		</td>
		<td>
		<?php 
		  if (!empty($calc->tcode_eu_private)) $tcode_eu_private = (int)$calc->tcode_eu_private; 
		  else $tcode_eu_private = $calc->tcode_eu_private = 1; 
		  echo $this->getTCodeDropDown(' name="tcode_eu_private" id="tcode_eu_private" style="min-width: 200px;" ', $tcode_eu_private); 
		?></td>
		</tr>
		 <?php } ?>
		<tr>
		<td>
			<label for="tcode_zero_us"><?php echo JText::_('VMCALCULATION_MOSS_CALC_TAXCODE_US'); ?></label>
			</td>
			<td>
		<?php 
		  if (!empty($calc->tcode_zero_us)) $tcode_zero_us = (int)$calc->tcode_zero_us; 
		  else $tcode_zero_us = $calc->tcode_zero_us = 0; 
		  echo $this->getTCodeDropDown(' name="tcode_zero_us" id="tcode_zero_us" style="min-width: 200px;" ', $tcode_zero_us); 
		?></td>
		</tr>
		</table>
		<?php 
		$html .= ob_get_clean(); 
		$html .= '</fieldset>'; 
		
		if (empty($isMoss)) {
			return true; 
		}
		
		$g = JRequest::getVar('load_google', false); 
		if (!empty($g))
		if (class_exists('VirtuemartControllerCalc'))
			{
				
				$VirtuemartControllerCalc = new VirtuemartControllerCalc(); 
				$VirtuemartControllerCalc->save(); 
				
				$cid = JRequest::getVar('cid'); 
				if (is_array($cid)) $cid = reset($cid); 
				$cid = (int)$cid; 
				JFactory::getApplication()->redirect('index.php?option=com_virtuemart&view=calc&task=edit&cid[]='.$cid); 
				return; 
			
			}
		
		// check table: 
		$sql = $this->getVmPluginCreateTableSQLData(); 
		$db = JFactory::getDBO(); 
		$db->setQuery($sql); 
		$db->query(); 
		$e = $db->getErrorMsg(); 
		if (!empty($e))
		$html.= 'Error creating tables: '.$e.'<br />'; 
		
		$root = Juri::root(); 
		$root = str_replace('/administrator/', '/', $root); 
		JHTML::script($root.'plugins/vmcalculation/moss_calc/helper.js');
		
		$q = 'select * from `'.$this->_tablename.'_config` where `virtuemart_calc_id` = '.$calc->virtuemart_calc_id.' order by `virtuemart_country_id` asc'; 
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		
	    $html .= '<input type="hidden" name="virtuemart_calc_id2" value="'.(int)$calc->virtuemart_calc_id.'" />'; 
		
		
		
		$html .= '<script type="text/javascript">
//<![CDATA[
     var line_iter = '.(count($res)).'; var myline = \'';

	
	{		
	 $select = $this->getTCodeDropDown(' name="tcodes_{num}" onchange="javascript: wasChanged({num})" style="min-width: 150px; " ', '', 'rel{num}_{i}="rel{num}_{i}"'); 
	 $jsline = '<tr id="trid_{num}"><td><input type="text" onchange="javascript: wasChanged({num})" id="country{num}" name="country{num}" value="country_{num}" /></td><td><input onchange="javascript: wasChanged({num})" type="text" name="tax_rate{num}" value="tax_rate_{num}" /></td><td>'.$select.'</td><td><button href="#" onclick="javascript: return op_new_line(myline,\\\'tax_rates\\\');">'.JText::_('VMCALCULATION_MOSS_CALC_MORE').'...</button></td></tr>';
	
	}

	 $jsline2 = str_replace('country_{num}', '', $jsline);
     
     $jsline2 = str_replace('tax_rate_{num}', '0', $jsline2);	 
	 $html .= $jsline2.'\'; 
//]]>
</script>';
        
		if (empty($calc->google_url)) $calc->google_url = ''; 
		$google_url = addslashes($calc->google_url); 
		
		if (empty($google_url)) $google_url = 'https://docs.google.com/spreadsheets/d/1TS149FnIU5YLZGbhwnLQAKpGvhrAoPqcttY3hz4qapM/pubhtml?gid=1718579556&single=true'; 
		
		
		
		
		$html .= '<fieldset><legend>'.JText::_('VMCALCULATION_MOSS_CALC').'</legend>
		This extension was made for you by <a href="http://www.rupostel.com/">RuposTel.com</a>
		
		<table class="admintable" id="tax_ratesx">
	<tr><th colspan="3">'.JText::_('VMCALCULATION_MOSS_CALC_LOAD_GOOGLE').'</th></tr>
	
	
	<tr><td>'.JText::_('VMCALCULATION_MOSS_CALC_LOAD_GOOGLE_URL').'</td><td><input type="url" name="google_url" value="'.$google_url.'" /></td><td><input type="button" onclick="loadGoogle()" value="'.addslashes(JText::_('VMCALCULATION_MOSS_CALC_LOAD_GOOGLE_URL_BTN')).'" /><input type="hidden" value="0" name="load_google" id="load_google"></td></tr>
	
	
	<tr><td colspan="3">'.JText::_('VMCALCULATION_MOSS_CALC_LOAD_GOOGLE_URL_DESC').' <br />'.JText::_('VMCALCULATION_MOSS_CALC_LOAD_GOOGLE_URL_FORMATS_DESC').' <br />
	<a href="https://docs.google.com/spreadsheets/d/1TS149FnIU5YLZGbhwnLQAKpGvhrAoPqcttY3hz4qapM/pubhtml?gid=1718579556&single=true">'.JText::_('VMCALCULATION_MOSS_CALC_LOAD_GOOGLE_URL_FORMATS_DESC_LINK').'</a>
	
	<h3>'.JText::_('VMCALCULATION_MOSS_CALC_HOTOUSE').'<h3>
	<p>'.JText::_('VMCALCULATION_MOSS_CALC_HOTOUSE_MODE1').' </p>
	<p>'.JText::_('VMCALCULATION_MOSS_CALC_HOTOUSE_MODE2').' </p>
	<p>'.JText::_('VMCALCULATION_MOSS_CALC_HOTOUSE_MODE3').' </p>
	<p>'.JText::_('VMCALCULATION_MOSS_CALC_HOTOUSE_MODE4').' </p>
	
	<p></p>
	
	</td></tr>
	</table>
		
	
	<table class="admintable" id="tax_rates">
	<tr><th>'
	.JText::_('VMCALCULATION_MOSS_CALC_COUNTRY_CODE')
	.'</th><th colspan="2">'
	.JText::_('VMCALCULATION_MOSS_CALC_TAX_RATE')
	.'</th></tr>';
	
	
	if (!class_exists('ShopFunctions'))
		   require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'shopfunctions.php');
		   
		   
		   
		   
	
    if (!empty($res))
	{
	foreach ($res as $k=>$row)
	 {
		$id = $row['virtuemart_country_id']; 
		 $country_2_code = mossTaxHelper::getCountryByID($id, 'country_2_code'); 
		
		 //rel{num}_i="rel{num}_i"
	   if (isset($row['tcode'])) $tcode = $row['tcode']; 
	   else $tcode = $this->getDefaultTcode($country_2_code, $calc->virtuemart_calc_id); 
	   
	   $rowhtml = str_replace('country_{num}', $country_2_code, $jsline); 
	   $rowhtml = str_replace('rel{num}_'.$tcode.'="rel{num}_'.$tcode.'"', ' selected="selected" ', $rowhtml); 
	   $rowhtml = str_replace('rel{num}_', 'del', $rowhtml); 
	   $rowhtml = str_replace('tax_rate_{num}', $row['tax_rate'], $rowhtml);
	   $rowhtml = str_replace("\'", "'", $rowhtml);
	   $rowhtml = str_replace('{num}', $k, $rowhtml); 
	   if (count($res)<=300)
	   {
	   //update always everything: 
	   $rowhtml .= '<input type="hidden" name="waschanged'.$k.'" id="waschanged'.$k.'" value="YES;'.$row['id'].'" />'; 
	   }
	  
	   $html .= $rowhtml; 
	 }
	 
	 
	
	 }
	 
	  {
	     
	     $rowhtml = str_replace("\'", "'", $jsline2);
		  $rowhtml = str_replace('{num}', count($res), $rowhtml); 
		 $html .= $rowhtml; 
	  }
	
		if (empty($row)) $row = array(); 

		$html .= '</table>';
		$html .= '<input type="hidden" name="last_iter" id="last_iter" value="'.count($res).'" />';
		 if (count($res)>300)
	 {
		 $html .= '<input type="hidden" name="too_many" value="1" />'; 
	 }
		$html .= '<input type="hidden" name="my_calc_id" id="my_calc_id" value="'.(int)$calc->virtuemart_calc_id.'" />';
		/*
		if ($calc->activated) {
			
		}
		*/
		$html .= '</fieldset>';
		return TRUE;
	}
	private function getUSTcode($country_2_code, $virtuemart_calc_id) {
		if (!empty($virtuemart_calc_id)) {
		$method = $this->isMe($virtuemart_calc_id); 
		if (!empty($method)) {
		if ($this->isHomeCountry($country_2_code)) { 
		   return $method->tcode_zero_us; 
		   
		}
		
		}
	  }
	}
	/*
	
CREATE TABLE IF NOT EXISTS `g52p3_virtuemart_calc_plg_moss_calc_orderitems` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `virtuemart_order_id` int(1) UNSIGNED NOT NULL DEFAULT '0',
  `virtuemart_order_item_id` int(1) UNSIGNED NOT NULL DEFAULT '0',
  `tcode` int(1) UNSIGNED NOT NULL DEFAULT '99',
  `tax_rate` decimal(6,6) NOT NULL DEFAULT '0.000000',
  `product_subtotal_net` decimal(15,15) NOT NULL DEFAULT '0.000000000000000',
  `product_subtotal_tax` decimal(15,15) NOT NULL DEFAULT '0.000000000000000',
  `product_subtotal_gross` decimal(15,15) NOT NULL DEFAULT '0.000000000000000',
  `product_item_net` decimal(15,15) NOT NULL DEFAULT '0.000000000000000',
  `product_item_tax` decimal(15,15) NOT NULL DEFAULT '0.000000000000000',
  `product_item_gross` decimal(15,15) NOT NULL DEFAULT '0.000000000000000',
  `product_quantity` decimal(15,15) NOT NULL DEFAULT '0.000000000000000',
  PRIMARY KEY (`id`),
  KEY `order_id` (`virtuemart_order_id`),
  KEY `order_item_id` (`virtuemart_order_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
*/
	public function plgVmOnCheckoutStorePricesOPC($order, $prices, $taxes) {
	   $data = array(); 
	   $ind = 0; 
	   //debug_zval_dump($taxes); die(); 
	   foreach ($order['items'] as $k=>$item) {
	    $data['id'] = 'NULL'; 
	    $data['virtuemart_order_id'] = (int)$order['details']['BT']->virtuemart_order_id; 
	    $data['virtuemart_order_item_id'] = (int)$item->virtuemart_order_item_id; 
		
		if (!defined('VM_VERSION') || (VM_VERSION < 3)) {
			$ind = $item->virtuemart_product_id; 
		}
		$rate = null; 
		$tcode = 99; 
		$vat_delivery = ''; 
		if (isset($prices[$ind]['VatTax'])) {
		$taxesl = $prices[$ind]['VatTax']; 
		foreach ($taxesl as $calc_id => $d) {
		  if ($this->isMe($calc_id)) {
		  if (!isset($taxes['VatTax'][$calc_id])) continue; 
		  
		  $rate = (float)$d[1]; 
		  if (isset($taxes['VatTax'][$calc_id]['calc_tcode']))
		  $tcode = $taxes['VatTax'][$calc_id]['calc_tcode']; 
		  if (isset($taxes['VatTax'][$calc_id]['vat_delivery']))
		  $vat_delivery = $taxes['VatTax'][$calc_id]['vat_delivery']; 
		  
		  
		  break; 
		  }
		}
		}
		else {
			
		}
		 $data['virtuemart_order_item_id'] = (int)$item->virtuemart_order_item_id; 
		 if (empty($rate)) {
			
		 }
		 $data['tax_rate'] = (float)$rate; 
		 
		 if (!isset($prices[$ind])) {
			 
			 continue; 
		 }
		 
		 $salesPrice = floatval($prices[$ind]['salesPrice']); 
		 $salesPriceWithoutTax = $salesPrice / ((100 + $rate) / 100); 
		 $tax = $salesPrice - $salesPriceWithoutTax; 
		 
		 
		 $q = floatval($item->product_quantity); 
		 $data['product_quantity'] = $q; 
		 
		 $data['product_subtotal_net'] = $salesPriceWithoutTax * $q; 
		 $data['product_subtotal_tax'] = $tax * $q; 
		 $data['product_subtotal_gross'] = $salesPrice * $q; 
		 $data['tcode'] = (int)$tcode; 
		 $data['vat_delivery'] = $vat_delivery; 
		 
		 $data['product_item_net'] = $salesPriceWithoutTax; 
		 $data['product_item_tax'] = $tax; 
		 $data['product_item_gross'] = $salesPrice; 
		 
		 $q = mossTaxHelper::insertArray($this->_tablename.'_orderitems', $data); 
		
		 
	     $ind++; 
	   }
	  
	   
	   
	   
	}
	
	private function getEUVATTcode($country_2_code, $virtuemart_calc_id) {
		if (!empty($virtuemart_calc_id)) {
		$method = $this->isMe($virtuemart_calc_id); 
		if (!empty($method)) {
		if ($this->isHomeCountry($country_2_code)) { 
		   return (int)$method->tcode_home; 
		   
		}
		
		}
		if ($this->_inEU($country_2_code)) {
		  return (int)$method->tcode_eu_vat; 
		}
		else {
			return (int)$method->tcode_zero_us ;
		}
		
		
		}
	}
	private function getDefaultTcode($country_2_code, $virtuemart_calc_id=0) {
		
		if (!empty($virtuemart_calc_id)) {
		$method = $this->isMe($virtuemart_calc_id); 
		if (!empty($method)) {
		if ($this->isHomeCountry($country_2_code)) { 
		   return (int)$method->tcode_home; 
		   
		}
		}
		
		if (!$this->_isMossMode($virtuemart_calc_id)) {
			
			return (int)$method->tcode_home; 
		}
		
		}
		
		$country_2_code = strtoupper($country_2_code); 
		switch ($country_2_code) {
			case 'BE': return 31; 
			case 'BG': return 32; 
			case 'CZ': return 33; 
			case 'CS': return 33; 
			case 'DK': return 34; 
			case 'DE': return 35; 
			case 'EE': return 36; 
			case 'EL': return 37; 
			case 'GR': return 37; 
			case 'ES': return 38; 
			case 'FR': return 39; 
			case 'HR': return 40; 
			
			case 'IE': return 41; 
			case 'IT': return 42; 
			case 'CY': return 43; 
			case 'LV': return 44; 
			case 'LT': return 45; 
			case 'LU': return 46; 
			
			case 'HU': return 47; 
			case 'MT': return 48; 
			case 'NL': return 49; 
			case 'AT': return 50; 
			case 'PL': return 51; 
			case 'PT': return 52; 
			
			case 'RO': return 53; 
			case 'SI': return 54; 
			case 'SK': return 55; 
			case 'FI': return 56; 
			case 'SE': return 57; 
			
			case 'UK': return 1; 
			case 'GB': return 1; 
			default: 
			if ($this->_inEU($country_2_code)) {
			   return 1; 
			}
			else return 0; 
		}
		
	}
	
	private function getTCodeDropDown($header='name="tcodes"', $default='', $inject='') {
		$html = '<select '.$header.'>'; 
		for ($i=0;  $i<=99; $i++) {
			$injectS = str_replace('{i}', $i, $inject); 
			$html .= '<option value="'.$i.'" '; 
			if ($default === $i) $html .= ' selected="selected" '; 
			$html .= $injectS.' >'.$this->params->get('T'.$i, 'T'.$i).'</option>'; 
		}
		$html .= '</select>'; 
		return $html; 
	}
	
	public function plgVmInterpreteMathOp ($calculationHelper, $rule, $price,$revert){

		if (JFactory::getApplication()->isAdmin()) return $price; 
		
		
		
		if (empty($calculationHelper->inCart)) return; 


		
		$rule = (object)$rule;
		if(empty($rule->published)) return $price;
		$mathop = $rule->calc_value_mathop;
		$tax = 0.0;
		$id = (int)$rule->virtuemart_calc_id; 
		
		$this->_getMethods($calculationHelper); 
		

		$found = false; 
		foreach ($this->methods as $method) { 
		
		if ($id !== $method->virtuemart_calc_id) continue; 
		
		
		
		if (!class_exists('VirtueMartCart')) require(JPATH_SITE. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_virtuemart' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'cart.php');
	
		if ($mathop === MOSS_TAX)
		{
			
			
			$calc_kind = $rule->calc_kind; 
			$id = (int)$rule->virtuemart_calc_id; 
			//$calc = calculationHelper::getInstance();
			
			$this->helper->calc = $rule; 

			
		  if (isset($calculationHelper->_cart))
		  {
			  $cart = $calculationHelper->_cart; 
		  }
		  else
		  {
		   $cart = VirtueMartCart::getCart();
		  }
		  
		 
		  $country_2_code = ''; 
		  
		  
		  
		  $tax_rate = (float)$rule->calc_value; 
		  
		  $tcode = $this->getDefaultTcode($country_2_code, $id); 
		  $this->_checkZeroTax($tax_rate, $cart, $id, $rule->calc_value,$country_2_code, $tcode); 
		  

		  
		  
		  if (empty($country_2_code) || ($tax_rate === NULL))
		   {
		     $tcode = $this->getDefaultTcode($country_2_code, $id); 
		     $tax_rate = $rule->calc_value; 
		   }
		  $tax_rate = (float)$tax_rate; 

		  
		 

		  $this->setCalcName($this->_calc_name[$id], $id, $tax_rate, $country_2_code, $tcode); 
		  
		   
		   $tax = ($tax_rate / 100) * $price; 
		   // only one tax rule can be applied in moss mode
		   $found = true; 
		   break; 
		}
		}
		
		if ($found) {

		
		if($revert){
			$price = $price - $tax;
			return $price; 
		}
		

		
		$ret = $price + (float)$tax;
		
		
		return $ret; 
		}
		
		return false; 
		

		
	}
	
	function _validateVat($customer_country_id, $vat_id, $company_name='', $cart)
	{
		if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'opc.php')) {
		
		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'opc.php'); 
		
		if (empty($customer_country_id)) $customer_country_id = $this->_getCountry($cart, 'BT'); 
		
		
		// speacial case: 
		// if BT is Home, ignore the ST address: 
		$countryBT = $this->_getCountry($cart, 'BT'); 
		$country_2_code = mossTaxHelper::getCountryByID($countryBT, 'country_2_code'); 
		
		if ($this->isHomeCountry($country_2_code)) { 
			   return false; 
			}
			
		$ret2 = VirtueMartControllerOpc::checkOPCVat($vat_id, $customer_country_id, $ret, $company_name);
	    
		if ($ret === VAT_STATUS_COUNTRY_HOME) {
			$debug = $this->params->get('debug', false); 
			 if (!empty($debug))
					 {
						 $this->eMsg('', __LINE__.' VAT Calc: VAT ID used is not valid in EU but it is a home VAT number and thus it is ignored.  '.$country_2_code.' vat ID: '.$vat_id); 
					 }
			
			return false; 
		}
		
		// vat number is valid: 
		if ($ret === VAT_STATUS_VALID) return true; 
		if ($ret === VAT_STATUS_COUNTRY_NOT_IN_EU) return true; 
		if ($ret === VAT_STATUS_COUNTRY_NOT_IN_EU2) return true; 
		
		$opc_euvat_contrymatch = OPCconfig::get('opc_euvat_contrymatch', false); 
		
		$redoA = array(VAT_STATUS_COUNTRY_ERROR); 
		
	
		if (empty($countryBT)) return false; 
	
		if ((empty($opc_euvat_contrymatch) && (in_array($ret, $redoA))) && ($countryBT !== $customer_country_id))
		{
			$checkvat = VirtueMartControllerOpc::checkOPCVat($vat_id, $countryBT, $ret);
			
			if ($ret === VAT_STATUS_VALID) return true; 
			
		}
		
		
		// system error, validation not available: 
		if ($ret === VAT_STATUS_SOAP_ERROR) return true; 
		
		
		}
		
		return false; 
	}
	
	function isHomeCountry(&$country_2_code) {
	   $country_2_code = strtoupper($country_2_code); 
	   // by default if country is empty and home is in EU, returns true
	   // if home is not in EU and country is empty, returns false
	   if (empty($country_2_code)) {
	     $home = $this->_getHomeCountry(); 
		 if ($this->_inEU($home)) {
			 $country_2_code = $home; 
			 return true; 
		 }
		 return false; 
	   }; 
	    $home = $this->params->get('home', ''); 
		$home = strtoupper($home); 
		$homes = array(); 
		if (!empty($home))
		{
			 
			if (stripos($home, ',')!==false)
			{
				
				
				$a = explode(',', $home); 
				foreach ($a as $h)
				{
					$h = trim($h); 
					$homes[] = strtoupper($h); 
					
				}
				
			
			}
			else
			{
				$home = trim($home); 		
				$homes[] = $home;
			}
			if (in_array($country_2_code, $homes)) return true; 
		}
		return false; 
	}
	// returns primary country
	function _getHomeCountry()
	{
		$home = $this->params->get('home', ''); 
		if (!empty($home))
		{
			if (stripos($home, ',')!==false)
			{
				$a = explode(',', $home); 
				$mc = trim($a[0]); 
				
				$mc = strtoupper($mc); 
				
				if ($mc === 'UK') $mc = 'GB'; 
				if ($mc === 'EL') $mc = 'GR'; 
				
				
				return strtoupper($mc); 
			}
		}
		$home = trim($home); 
		return strtoupper($home);
	}
	// if country is empty, it's compared against home country
	function _inEU($country_2_code)
	{
		 if (empty($country_2_code)) {
		 $home = $this->_getHomeCountry(); 
		 if (!empty($home)) { 
		  if ($this->_inEU($home)) return true; 
		  return false; 
		 }
		}
		
		if (isset($this->params)) {
		$eu = $this->params->get('EU', 'AT,BE,BG,CY,CZ,DE,DK,EE,ES,FI,FR,GB,GR,HU,IE,IT,LT,LU,LV,MT,NL,PL,PT,RO,SE,SI,SK,HR,UK,EL'); 
		}
		else
	    {
			$eu = 'AT,BE,BG,CY,CZ,DE,DK,EE,ES,FI,FR,GB,GR,HU,IE,IT,LT,LU,LV,MT,NL,PL,PT,RO,SE,SI,SK,HR,UK,EL'; 
		}
		$a = explode(',', $eu); 
		$eua = array(); 
		foreach ($a as $k=>$v)
		{
			$v = trim($v); 
			if (empty($v)) continue; 
			$eua[] = strtoupper($v); 
		}
		
		$country_2_code = strtoupper($country_2_code); 
		if (in_array($country_2_code, $eua)) return true; 
		return false; 
		
	}
	
	function _checkZeroTax(&$tax_rate, $cart=null, $id, $default_rate, &$new_2_code='', &$tcode)
	{
		
		if (is_null($cart))
		{
			 $cart = VirtueMartCart::getCart();
			 
		}
		
		if (!empty($cart->BT) && (count($cart->BT) === 1) && (isset($cart->BT[0]))) {
			  $cart->BT = array(); 
			}
			
			if (!empty($cart->ST) && (count($cart->ST) === 1) && (isset($cart->ST[0]))) {
			  $cart->ST = array(); 
			}
		
		$debug = $this->params->get('debug', false); 
		
		if (!empty($debug)) {
		  static $once; 
		  if (empty($once)) {
		  $ret = $this->helper->checkOthers(); 
		  if (!empty($ret)) {
		     $this->eMsg($id, __LINE__.' VAT Calc: Plugin detected more than one calculation plugin. Double check your calculations since Virtuemart may not support multiple calculation pluings at once. Detected plugins: '.implode(',', $ret)); 
		  }
		  }
		  $once  = true; 
		}
		
		$home = $this->_getHomeCountry(); 
		$virtuemart_country_id = $home_virtuemart_country_id = $this->helper->getCountryId($home);
		$tax_rate = $this->helper->getTaxRate($virtuemart_country_id, $id, $default_rate);
		if ((empty($cart->BT) && (JFactory::getApplication()->isAdmin()))) {
		  
		  
		  
		  
		 
					if (!empty($debug))
					 {
						 $this->eMsg($id, __LINE__.' VAT Calc: For backend we always return home rate - '.$home.' tax rate: '.$tax_rate); 
					 }
					$new_2_code = $home; 
					return false; 
		  
		}
		
			 $country_2_code = $this->_getCountry($cart); 
		    $customer_country_id = $this->_getCountry($cart, 'virtuemart_country_id'); 
			
			
			
			if (!empty($cart->BT)) { 
			 $addressBT = $cart->BT; 
			 if (!$this->params->get('ignore_st', false)) { 
			 if ((!empty($cart->ST)) && ($cart->STsameAsBT !== 1)) 
				 $addressST = $cart->ST; 
			 else $addressST = $addressBT; 
			 }
			 else
			 {
				 $addressST = $addressBT; 
			


			}
			
			$countryBT = $addressBT['virtuemart_country_id']; 
			$country_2_code_bt = mossTaxHelper::getCountryByID($countryBT, 'country_2_code'); 
			
			}
			else
			{
				$countryBT = $customer_country_id;
				$country_2_code_bt = $country_2_code; 
				$addressBT = $addressST = array(); 
				$addressBT['virtuemart_country_id'] = $addressST['virtuemart_country_id'] = $countryBT;
			}
		
		
		
		
		$private_person_field = $this->params->get('private_person_field', 'private_person'); 
		if (empty($private_person_field)) $private_person_field = 'private_person'; 
		$company_field = $this->params->get('company_field', 'company'); 
		if (empty($company_field)) $company_field = 'company'; 
		$eu_vat_field = $this->params->get('eu_vat_field', 'eu_vat'); 
		if (empty($eu_vat_field)) $eu_vat_field = 'eu_vat'; 
		
		$zero_rate_euvat = $this->params->get('zero_rate_euvat', true); 
		$zero_rate_company = $this->params->get('zero_rate_company', true); 
		$zero_rate_private = $this->params->get('zero_rate_private', true); 
		
		$country_2_code = $this->_getCountry($cart); 
		$customer_country_id = $this->_getCountry($cart, 'virtuemart_country_id'); 
		
		
		$new_2_code = $country_2_code; 
		
		
		$private_person_field_value = $this->params->get('private_person_field_value', 1); 
		
		
		// private person is not in EU, let's charge home VAT rate:
		if (!$this->_inEU($country_2_code))
		{
		
		$tcode = $this->getUSTcode($country_2_code, $id); 
		// private persons outside EU: 
		if (empty($zero_rate_private))
		{
			   // private persons outside EU pay tax: 
			   // private person checkbox: 
			    
				if ((!empty($addressBT[$private_person_field])) && ($addressBT[$private_person_field] == $private_person_field_value))
				{		
					$tax_rate = $this->helper->getTaxRate($virtuemart_country_id, $id, $default_rate);
					if (!empty($debug))
					 {
						 $this->eMsg($id, __LINE__.' VAT Calc: Private person outside EU was detected with private person field in BT, and thus returning home rate for non-EU private persons - '.$home.' tax rate: '.$tax_rate); 
					 }
					$new_2_code = $home; 
					$tcode = $this->getDefaultTcode($new_2_code, $id); 
					return false; 
				}
				
			    if (isset($addressST[$private_person_field])) 
				if ((!empty($addressST[$private_person_field])) && ($addressST[$private_person_field] == $private_person_field_value))
				{
			          $tax_rate = $this->helper->getTaxRate($virtuemart_country_id, $id, $default_rate);
					  
					 if (!empty($debug))
					 {
						 $this->eMsg($id, __LINE__.' VAT Calc: Private person outside EU was detected with private person field in ST, and thus returning home rate for non-EU private persons - '.$home.' tax rate: '.$tax_rate); 
					 }
					  $new_2_code = $home; 
					  $tcode = $this->getDefaultTcode($new_2_code, $id); 
					  return false; 
				}
				
				// company and euvat fields are empty:
				if ((!empty($company_field)) && (!empty($eu_vat_field)))
				if (empty($addressBT[$company_field]) && (empty($addressBT[$eu_vat_field])))
				{
				   	$tax_rate = $this->helper->getTaxRate($virtuemart_country_id, $id, $default_rate);
					
					if (!empty($debug))
					 {
						 $this->eMsg($id, __LINE__.' VAT Calc: Private person outside EU was detected, empty company and empty eu vat fields, and thus returning home rate for non-EU private persons - '.$home.' tax rate: '.$tax_rate); 
					 }
					$new_2_code = $home; 
					$tcode = $this->getDefaultTcode($new_2_code, $id); 
					return false; 
				}
				/* ST address is ignored for company name and euvat check
				if (empty($addressST[$company_field]) && (empty($addressST[$eu_vat_field])))
				{
				   	$tax_rate = $this->helper->getTaxRate($virtuemart_country_id, $id, $default_rate);
					return false; 
				}
				*/
				// end, company and euvat fields are empty
				
				
				
				// for other cases of customers outside EU return zero rate: 
				
					if (!empty($debug))
					 {
						 $this->eMsg($id, __LINE__.' VAT Calc: Private person outside EU was not detected and thus returning zero rate for non-EU companies - '.$country_2_code); 
					 }
				$tcode = $this->getUSTcode($country_2_code, $id); 
				
				$tax_rate = 0; 
				return true; 
			
		}
		else
		{
			// if BT is in home, then ignore ST: 
			
			if ($this->isHomeCountry($country_2_code_bt)) { 
				$tax_rate = $this->helper->getTaxRate($countryBT, $id, $default_rate);
					
					if (!empty($debug))
					 {
						 $this->eMsg($id, __LINE__.' VAT Calc: BT is in home country and thus he always pays tax - '.$country_2_code_bt.' tax rate: '.$tax_rate); 
					 }
					 $tcode = $this->getDefaultTcode($country_2_code_bt, $id); 
					 return true; 
			
			
			}
			
			$tax_rate =0; 
			if (!empty($country_2_code)) { 
					if (!empty($debug))
					 {
						 $this->eMsg($id, __LINE__.' VAT Calc: All customers outside EU get zero tax rate - '.$country_2_code.' tax rate: '.$tax_rate); 
					 }

			$tcode = $this->getUSTcode($country_2_code, $id); 
			
			// everybody outside EU should not pay tax: 
			
			return true; 
			}
		}
		}
		else
		{
			
			if (empty($country_2_code)) {
			  if ($this->isHomeCountry($country_2_code)) { 
				$new_2_code = $country_2_code; 
			}
			}
			// person is inside EU, let's check if he is private or company: 
			
			// private person start
			if ((!empty($addressBT[$private_person_field])) && ($addressBT[$private_person_field] == $private_person_field_value))
				{		
		$tax_rate = $this->helper->getTaxRate($customer_country_id, $id, $default_rate);
					
					if (!empty($debug))
					 {
						 $this->eMsg($id, __LINE__.' VAT Calc: Private person field detected in BT address, customer is inside EU - '.$country_2_code.' tax rate: '.$tax_rate); 
					 }
					 
					$tcode = $this->getDefaultTcode($country_2_code, $id); 
					
					return false; 
				}
				
			    if (isset($addressST[$private_person_field])) 
				if ((!empty($addressST[$private_person_field])) && ($addressST[$private_person_field] == $private_person_field_value))
				{
			      $tax_rate = $this->helper->getTaxRate($customer_country_id, $id, $default_rate);
				  if (!empty($debug))
					 {
						 $this->eMsg($id, __LINE__.' VAT Calc: Private person field detected, customer is inside EU - '.$country_2_code.' tax rate: '.$tax_rate); 
					 }
				  
				      $tcode = $this->getDefaultTcode($country_2_code, $id); 
					  return false; 
				}
				
				// company and euvat fields are empty:
				if ((!empty($company_field)) && (!empty($eu_vat_field)))
				if (empty($addressBT[$company_field]) && (empty($addressBT[$eu_vat_field])))
				{
					

				   	$tax_rate = $this->helper->getTaxRate($customer_country_id, $id, $default_rate);
					if (!empty($debug)) {
					if (is_null($tax_rate)) { 
						 $this->eMsg($id, __LINE__.' VAT Calc: MOSS mode is enabled, but country was not found within MOSS plugin - '.$country_2_code.' tax rate: '.$tax_rate); 

					}
					}
					if (!empty($debug))
					 {
						 $this->eMsg($id, __LINE__.' VAT Calc: Customer is inside EU and has no COMPANY or VAT ID filled in BT. He is considered a private person from - '.$country_2_code.' tax rate: '.$tax_rate); 
					 }

					$tcode = $this->getDefaultTcode($country_2_code, $id); 
					return false; 
				}
				/* ST address is ignored for EU VAT and Company name here: 
				if (empty($addressST[$company_field]) && (empty($addressST[$eu_vat_field])))
				{
				   	$tax_rate = $this->helper->getTaxRate($customer_country_id, $id, $default_rate);
					return false; 
				}
				*/
				// end, company and euvat fields are empty
				
				// private person end
				
				//company start
				
				if (!empty($eu_vat_field) && ((!empty($addressBT[$eu_vat_field]))))
				{
				 
				 if ($this->isHomeCountry($country_2_code)) {
					 
					 $tax_rate = $this->helper->getTaxRate($customer_country_id, $id,$default_rate);
					 if (!empty($debug))
					 {
						 $this->eMsg($id, __LINE__.' VAT Calc: Company is based in home country - '.$country_2_code.' tax rate: '.$tax_rate); 
					 }
					 $tcode = $this->getDefaultTcode($country_2_code, $id); 
					 
				    
				    return false; 
				 }
					if (!empty($company_field) && (!empty($eu_vat_field)))
					if ($this->_validateVat($customer_country_id, $addressBT[$eu_vat_field], $addressBT[$company_field], $cart))
				    {
					 $tax_rate = 0; 
					 if (!empty($debug))
					 {
						 $this->eMsg($id, __LINE__.' VAT Calc: Tax rate for EU VAT validated customers '.$country_2_code.' tax rate: '.$tax_rate); 
					 }
						
					 $tcode = $this->getEUVATTcode($country_2_code, $id); 
					 return true; 
					}
					else
					{
					 
					 if (!empty($debug))
					 {
						 $this->eMsg($id, __LINE__.' VAT Calc: VAT ID validation failed...  Country: '.$country_2_code.' VAT ID: '.$addressBT[$eu_vat_field].' Company name: '.$addressBT[$company_field]); 
					 }
					 $tcode = $this->getDefaultTcode($country_2_code, $id);
					}
					
				}
				
				
				// company end
				
		}
		
		// defaults: 
		
		if ($this->isHomeCountry($country_2_code)) { 
		 $new_2_code = $country_2_code; 
		 // if country is empty, it is set to home: 
		 $country_id = $this->helper->getCountryId($country_2_code);
		 $tax_rate = $this->helper->getTaxRate($country_id, $id,$default_rate);
		 
		 $tcode = $this->getDefaultTcode($country_2_code, $id);
		 //TCODE: IF HOME IS OUTSIDE EU, THERE IS NO LOGIC NOW TO HANDLE THIS!
		 if (is_null($tax_rate)) {
		  // home country is outside EU:
		   $tax_rate = 0; 
				if (!empty($debug))
					 {
						 $this->eMsg($id, __LINE__.' VAT Calc: Home country is outside EU, no rate selected for empty or home country'.' tax rate: '.$tax_rate); 
					 }

		  
		 
		  return true; 
		 }
		 if (!empty($debug))
					 {
						 $this->eMsg($id, __LINE__.' VAT Calc: Home country rate selected '.$country_2_code.' tax rate: '.$tax_rate); 
					 }
		 
		 return false; 
		}
		
		
		// company from Swiss (BT) ships to Spain (ES) where home is IT
		if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'opc.php')) {
		
		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'opc.php'); 
		if ((!$this->_inEU($country_2_code_bt)) && ($this->_inEU($country_2_code)))
		{
			if (!$this->isHomeCountry($country_2_code)) { 
				 if (!empty($eu_vat_field))
			     if (!empty($addressBT[$eu_vat_field]))
				 {
					 //if they got EU registered VAT number: 
					 $vat_id = $addressBT[$eu_vat_field]; 
					 if (!empty($company_field)) {
					  $company_name = $addressBT[$company_field]; 
					 }
					 else {
						 $company_name = ''; 
					 }
					 $ret2 = VirtueMartControllerOpc::checkOPCVat($vat_id, $customer_country_id, $ret, $company_name);
					 if ($ret === VAT_STATUS_VALID)
					 {
						 $tax_rate = 0; 
						 $this->eMsg($id, __LINE__.' VAT Calc: Customer BT country is outside EU '.$country_2_code_bt.' and he has a valid VAT number not to pay tax in EU - '.$country_2_code.' tax rate: '.$tax_rate); 
						 $tcode = $this->getEUVATTcode($country_2_code, $id);
						 return true; 
					 }
					 else
					 {
						 // he ships to EU but he does not have VAT ID  
						 
					 }
				 }
			}
		}
		}
		
		$tax_rate = $this->helper->getTaxRate($customer_country_id, $id,$default_rate);
				
					 {
						if (!empty($tax_rate)) {
						   $isMoss = $this->_isMossMode($id); 
						   if (empty($isMoss)) {
						   $home = $this->_getHomeCountry(); 
						   if (!empty($debug)) {
						     $this->eMsg($id, __LINE__.' VAT Calc: Home country rate is selected - '.$home.' tax rate: '.$tax_rate); 
						   }
						   $tcode = $this->getDefaultTcode($home, $id);
						 }
						 else
						 {
							 if (!empty($debug)) {
								$this->eMsg($id, __LINE__.' VAT Calc: Customer country rate is selected - '.$country_2_code.' tax rate: '.$tax_rate); 
							 }
						   $tcode = $this->getDefaultTcode($country_2_code, $id);
						 }
						}
					 }

		
		return false; 
		
	}
	
	private function _backtrace()
	{
		$x = debug_backtrace(); 
		foreach ($x as $l) {
			if (isset($l['file']))
			$this->eMsg('', $l['file'].' '.$l['line']); 
		}
	}
	function eMsg($id='', $msg)
	{
		$extra = ''; 
		if (!empty($id)) { 
		{ 
		 $extra = $id; 
		 $msg = $extra.':'.$msg; 
		}
		}
		static $done; 
		if (!empty($done[$msg])) return; 
		JFactory::getApplication()->enqueueMessage($msg); 
		if (empty($done)) $done = array(); 
		$done[$msg] = $msg; 
	}
	
	function plgVmConfirmedOrder ($cart, $order) {

		}
		
		public function _getGeoCountry()
		{
			if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_geolocator'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'helper.php')) {
			include_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_geolocator'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'helper.php');
			if (class_exists('geoHelper')) 
			{
			$c = geoHelper::getCountry2Code();
			
			if ($c === 'UK') $c = 'GB'; 
			if ($c === 'EL') $c = 'GR'; 
			
			
			$arr = array('A1', 'A2', 'O1'); 
			if (in_array($c, $arr))  {
				$debug = $this->params->get('debug', false); 
			   if (!empty($debug))
					 {
						 $this->eMsg($id, __LINE__.' VAT Calc: GeoLocator detected a proxy and returned HOME country as default.  '); 
					 }
				
				return ''; 
			}
			
			return $c; 
			
			}
			
			
			}
			
			$debug = $this->params->get('debug', false); 
			   if (!empty($debug))
					 {
						 $this->eMsg('', __LINE__.' VAT Calc: GeoLocator is enabled, BUT NOT INSTALLED. Please install it from OPC Extensions tab. '); 
					 }
			
			return ''; 

		}
	public function _getCountry(&$cart, $what='country_2_code')
	{
		$country =  0; 
		
		if (JFactory::getApplication()->isAdmin()) {
		  $home = $this->_getHomeCountry(); 
		  if ($what === 'country_2_code') return $home; 
		  $virtuemart_country_id = $this->helper->getCountryId($home);
		  return $virtuemart_country_id;
		}
		  
		
         if ((!$this->params->get('ignore_st', false)) && ($what !== 'BT')) { 
			
		
		 if (method_exists($cart, 'getST'))
		  {
			  $address = $cart->getST(); 
			 
		  }
		  else
		  {
		    $address = (($cart->ST === 0) ? $cart->BT : $cart->ST);
		  }
		 }
		 else
		 {
			 $address = $cart->BT; 
		 }
		  $geo = $this->params->get('use_geolocator', false); 
		 
		 
		  if (empty($address)) {
		      $userId = JFactory::getUser()->get('id'); 
			  if (!empty($userId)) {
				$address =  $this->_getCustomerData($cart); 

			  }
		  
		  }
		  
		  if (empty($address)) 
		  {
			  
			  
			  
			  
			  if (!empty($geo))
			  {
				  $gc = $this->_getGeoCountry(); 
				  if (!empty($gc)) {
					  
					  $address = array(); 
					 
					  $country = $address['virtuemart_country_id'] = $this->helper->getCountryId($gc); 
				  }
			  }
			  if (empty($address)) { 
			  if ($what == 'country_2_code') return ''; 
			  else return 0; 
			  }
		  }
		  
		  if (!empty($address['virtuemart_country_id']))
		  $country = $address['virtuemart_country_id']; 
		  else
		  {
			   if (!empty($geo))
			  {
				  $gc = $this->_getGeoCountry(); 
				   if (!empty($gc)) {
				  $country = $address['virtuemart_country_id'] = $this->helper->getCountryId($gc); 
				   }
			  }
		  }
		if ($what == 'country_2_code') {
		
		 if (!class_exists('ShopFunctions'))
		   require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'shopfunctions.php');
		   
		   if (!empty($country))
		   {
		    $country_2_code = mossTaxHelper::getCountryByID($country, 'country_2_code'); 
		   }
		   else
		   {
			   return '';
		   }
		   
		   return  $country_2_code;
		}
		
		return $country;
		  
	}
	
	public function _getCountryName($country_id)
	{
		
	}
	
	private function isMe($id) {
		$calculationH = new stdClass(); 
		$calculationH->_cart = new stdClass(); 
		$calculationH->_cart->vendorId = 1; 
		$this->_getMethods($calculationH);
		$id = (int)$id; 
		if (!empty($this->methods))
		foreach ($this->methods as $method) { 
		   if ($id === $method->virtuemart_calc_id) return $method; 
		}
		return false; 
		
	}
	
	public function plgVmInGatherEffectRulesBill(&$calculationHelper,&$rules){
		
		 if (JFactory::getApplication()->isAdmin()) return;
		
		 if (empty($calculationHelper->inCart)) return; 
		
		 $this->_getMethods($calculationHelper); 
		 foreach ($this->methods as $method) { 
		
		
		foreach($rules as $k=>$v)
		{
			
			$id = (int)$v['virtuemart_calc_id']; 
		
		
			if ($id != $method->virtuemart_calc_id) continue; 
			
			//if ($v['calc_value_mathop'] == MOSS_TAX)
			{
				
				
				 if (isset($calculationHelper->_cart))
				 {
					$cart = $calculationHelper->_cart; 
				  }
				  else
				  {
			  
					$cart = VirtueMartCart::getCart();
				   }
		  
		
		  
		  $id = $v['virtuemart_calc_id']; 
		 
		 
		  
		   $country2code = $this->_getCountry($cart); 
		   
		  
		  
		  
		  
		  
		  
		  
		  $tax_rate = (float)$v['calc_value']; 
		  $tcode = $this->getDefaultTcode($country2code, $id); 
		  $this->_checkZeroTax($tax_rate, $cart, $id, $v['calc_value'], $country2code, $tcode); 
		  
		  
		  if (!is_null($tax_rate))
		  {
			  $rules[$k]['calc_value'] = $tax_rate; 
			  $rules[$k]['calc_value_mathop'] = '+%'; 
			  
			  
		  }
		  
		  }	

		   if (empty($this->_calc_tcode)) {
			   $this->_calc_tcode = array(); 
		   }
		   
		   if (!isset($this->_calc_tcode[$id])) {
		     $this->_calc_tcode[$id] = $tcode; 
		   }
		   if (empty($this->_vat_delivery)) {
			   $this->_vat_delivery = array(); 
		   }
		   
		   if (!isset($this->_vat_delivery[$id])) {
		     $this->_vat_delivery[$id] = $country2code; 
		   }
		   
		   $rules[$k]['calc_tcode'] =& $this->_calc_tcode[$id];
		   $rules[$k]['vat_delivery'] =& $this->_vat_delivery[$id];
		   
		   
		   
		   $this->setCalcName($rules[$k]['calc_name'], $id, $tax_rate, $country2code, $tcode); 
		  
				
				



























			}
		
		 }
		 /*
		if (!empty(self::$_removedRules))
		{
			$list = array(); 
			foreach ($rules as $r)
			{
				$list[$r['virtuemart_calc_id']] = (int)$r['virtuemart_calc_id']; 
			}
			foreach (self::$_removedRules as $r2)
			{
				$id = (int)$r2['virtuemart_calc_id']; 
				if (!in_array($id, self::$_removedRules))
				{
					$rules[] = $r2; 
				}
			}
		}
		*/
		
	}


	/**
	 * We can only calculate it for the productdetails view
	 * @param $calculationHelper
	 * @param $rules
	 */
	 
	private static $_removedRules; 
	public function plgVmInGatherEffectRulesProduct(&$calculationHelper,&$rules){
		
		//if (JFactory::getApplication()->isAdmin()) return;
		$this->_getMethods($calculationHelper); 
		
		$found = array(); 
		$largest = 0; 
		$last_tax_rate = 0; 
		foreach($rules as $k=>$v)
		{
		foreach ($this->methods as $method) { 
		
		
	   
		
			
				$id = (int)$v['virtuemart_calc_id']; 
		
		
		
			if ($id !== $method->virtuemart_calc_id) {
				
				
				continue; 
				
			}
			
		
		
		
		   
			
		
			
				
				
				 if (isset($calculationHelper->_cart))
				 {
					$cart = $calculationHelper->_cart; 
				  }
				  else
				  {
			  
					$cart = VirtueMartCart::getCart();
				   }
		  
		  
		  
		  $id = (int)$v['virtuemart_calc_id']; 
		 
		 
		 
		  $country_2_code = $c2 = $this->_getCountry($cart); 
		  
		  
		
		  
		  
		 

		  
		  
		  $tax_rate = (float)$v['calc_value']; 
		  $tcode = $this->getDefaultTcode($country_2_code, $id); 
		  $this->_checkZeroTax($tax_rate, $cart, $id, $v['calc_value'], $country_2_code, $tcode); 
		  
		      $found[$k] = $tax_rate; 
			  
			  //we should not apply 2 vat rules on a single item and thus just one calculation with higher value is selected:
		  if ($tax_rate >= $last_tax_rate) {
		    $largest = $k; 
		    $last_tax_rate = $tax_rate; 
		  }
		  
			  $rules[$k]['calc_value'] = $tax_rate; 
			  $rules[$k]['calc_value_mathop'] = '+%'; 
			  
			  
			   if (empty($this->_calc_tcode)) {
			   $this->_calc_tcode = array(); 
		   }
		   if (!isset($this->_calc_tcode[$id])) {
		     $this->_calc_tcode[$id] = $tcode; 
		   }
		   
		    if (empty($this->_vat_delivery)) {
			   $this->_vat_delivery = array(); 
		   }
		   
		   if (!isset($this->_vat_delivery[$id])) {
		     $this->_vat_delivery[$id] = $country_2_code; 
		   }
		   
		   
		   
		   $rules[$k]['calc_tcode'] =& $this->_calc_tcode[$id];
		  $rules[$k]['vat_delivery'] =& $this->_vat_delivery[$id];
		  
		  
		   $this->setCalcName($rules[$k]['calc_name'], $id, $tax_rate, $country_2_code, $tcode); 
		 
				
			}
		}
		
		if (count($found)>1) {
		  foreach ($found as $k=>$v)
		  {
			  if ($k != $largest) {
			    unset($rules[$k]); 
			  }
		  }
		}
		
		
		
		
		
		
	}
    


  	
    
   
	public function plgVmStorePluginInternalDataCalc(&$data){
		
		if (!isset($data['virtuemart_calc_id'])) return;
		$id = $data['virtuemart_calc_id']; 
		if (!$this->isMe($id)) return; 
		
		
		
		
		
		
		$this->helper->storeConfig($data); 
		$this->checkTcodeCol(); 
		$this->createOrderItemsTable(); 
		
		if (empty($data['moss_mode'])) return; 
		if (isset($data['load_google']) && (!empty($data['load_google'])))
		{
			    $url = $data['google_url']; 
				$calc_id = (int)$data['virtuemart_calc_id2']; 
				
				
				if (!empty($calc_id)) { 
				$this->helper->calc_id = $calc_id; 
				
				
				$this->helper->loadGoogle($url); 
			    }
				return;
		}
		

		
		
		if (!isset($data['my_calc_id'])) return; 
		$last_iter = (int)$data['last_iter']; 
		$myid = (int)$data['my_calc_id']; 
		if (empty($myid)) return;
		$db = JFactory::getDBO(); 
		for($i=0; $i<=$last_iter; $i++)
		 {
		 
		     $country = strtoupper($data['country'.$i]); 
			
			 if (!empty($country)) { 
			 $virtuemart_country_id = $this->helper->getCountryId($country); 
			}
			else
			{
				$country = 0; 
			}
			
			
			$tax_rate = (float)$data['tax_rate'.$i]; 
			if (!isset($data['waschanged'.$i]))
			 {
			 
				if (empty($country)) continue; 
			
			 
				$q = "select * from `".$this->_tablename.'_config` where virtuemart_calc_id = '.(int)$myid.' and `virtuemart_country_id` = '.(int)$virtuemart_country_id; 
				$db->setQuery($q); 
				$res = $db->loadAssocList(); 
				if (count($res)<=1) {
					$q = "delete from from `".$this->_tablename.'_config` where virtuemart_calc_id = '.(int)$myid.' and `virtuemart_country_id` = '.(int)$virtuemart_country_id; 
					$db->setQuery($q); 
					$db->query(); 
				}
			   // new insert
			   if (!isset($data['tcodes_'.$i])) {
				   $tcode = $this->getDefaultTcode($country); 
			   }
			   else {
			     $tcode = (int)$data['tcodes_'.$i]; 
			   }
			   
			   			   
			   
			   $q = "insert into `".$this->_tablename.'_config` (id, virtuemart_calc_id, virtuemart_country_id, tax_rate, tcode) values (NULL, "'.(int)$myid.'", "'.$db->escape($virtuemart_country_id).'", "'.$db->escape($tax_rate).'", '.(int)$tcode.')';
			   try
			   {
			   $db->setQuery($q); 
			   $db->query(); 
			   }
			   catch (Exception $e)
			   {
				   
			   }
			   $e = $db->getErrorMsg(); 
			   if (!empty($e)) { 
			    $this->eMsg($id, $e); 
			   }
			  
			 }
			 else
			 {
			    $a = explode(';', $data['waschanged'.$i]); 
				$id = (int)$a[1]; 
				 
				 
				 
				if ($a[0] == 'YES')
				{
			    if ((empty($country)))
				 {
				   $q = 'delete from '.$this->_tablename.'_config where id = '.$id.' limit 1'; 
				   $db->setQuery($q); 
				   $db->query(); 
				    
					
					
				 }
				 else
				 {
					 if (!isset($data['tcodes_'.$i])) {
						$tcode = $this->getDefaultTcode($country); 
					}
					else {
						$tcode = (int)$data['tcodes_'.$i]; 
					}
			   
			   
			   
				   $q = 'update `'.$this->_tablename.'_config` set `virtuemart_country_id` = "'.$virtuemart_country_id.'", tax_rate = "'.$db->escape($tax_rate).'", tcode = '.(int)$tcode.' where id = '.(int)$id.' limit 1'; 
				   $db->setQuery($q); 
				   $db->query(); 
				   
				    
				   
				 }
				}
				 
			 }
			
		 }
		
		//$table = $this->getTable('calcs');
		if (!class_exists ('TableCalcs')) {
			require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'tables' .DIRECTORY_SEPARATOR. 'calcs.php');
		}
		
		
		

		
		
		
		/*

		$db = JFactory::getDBO ();
		$table = new TableCalcs($db);
		$table->setUniqueName('calc_name');
		$table->setObligatoryKeys('calc_kind');
		$table->setLoggable();
		$table->setParameterable ($this->_xParams, $this->_varsToPushParam);
		$table->bindChecknStore($data);
		*/
	}

	public function plgVmGetPluginInternalDataCalc(&$calcData){
		
		//$this->_backtrace(); 
		
		$calcData->setParameterable ($this->_xParams, $this->_varsToPushParam);

		if (!class_exists ('VmTable')) {
			require(JPATH_VM_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'vmtable.php');
		}
		VmTable::bindParameterable ($calcData, $this->_xParams, $this->_varsToPushParam);
		
		
		
		
		return true;

	}

	public function plgVmDeleteCalculationRow($id){
		$this->removePluginInternalData($id);
	}

	public function plgVmOnUpdateOrderPayment($data,$old_order_status){


	}
	function _getMethods(&$calculationH)
	 {
		 if (!empty($this->methods)) return; 
		 
		 if (isset($calculationH->_cart->vendorId))
		 $id = $calculationH->_cart->vendorId; 
	     
		if (empty($id)) $id = 1; 
		
		 
		 
		 
		 $jnow = JFactory::getDate();
		 $_db = JFactory::getDBO();
		 $_now = $jnow->toSQL();
		 $_nullDate = $_db->getNullDate();
		
		 $q = 'select * from `#__virtuemart_calcs` where `calc_value_mathop` LIKE "'.MOSS_TAX.'" 
		         AND `published`="1"
                AND (`virtuemart_vendor_id`="' . $id . '" OR `shared`="1" )
				AND ( publish_up = "' . $_db->escape($_nullDate) . '" OR publish_up <= "' . $_db->escape($_now) . '" )
				AND ( publish_down = "' . $_db->escape($_nullDate) . '" OR publish_down >= "' . $_db->escape($_now) . '" )

		 
		 '; 
		 $ids = $this->helper->getAssocList($q); 
		
		
		
		if(empty($calculationH->_calcModel)){
			$calculationH->_calcModel = VmModel::getModel('calc');
		}
		$this->methods = array(); 
		foreach ($ids as $k=>$rowA) { 
		   $calcRuleID = (int)$rowA['virtuemart_calc_id']; 

	     
		    $row = (object)$rowA; 
		 
		    $row->virtuemart_calc_id = (int)$row->virtuemart_calc_id; 
			$row->virtuemart_vendor_id = (int)$row->virtuemart_vendor_id; 
			$row->calc_jplugin_id = (int)$row->calc_jplugin_id; 
			$row->calc_value = (float)$row->calc_value; 
			$row->calc_currency = (int)$row->calc_currency; 
			$row->published = (int)$row->published; 
			$row->calc_currency = (int)$row->calc_currency; 
			
			
			vmTable::bindParameterable($row, 'calc_params', $this->varsToPush); 
			
			$this->methods[$calcRuleID] = (object)$row; 
		 
		 
		}
		
		
		
		
	 }
	 function _getCustomerData($cart) {
	   
	   $userId = JFactory::getUser()->get('id'); 
	   $userId = (int)$userId; 
	   if (empty($userId)) return array(); 
	   
	   $q = 'select * from `#__virtuemart_userinfos` where `virtuemart_user_id` = '.$userId.' and address_type = "BT" limit 0,1'; 
	   $res = $this->helper->getAssocList($q); 
	   
	  
	   if (!empty($res)) {
		 $cart->BT = array(); 
	     foreach ($res as $k=>$v) {
		   $cart->BT[$k] = $v; 
		 }
	   }
	   
	   return $cart->BT; 
	   
	 }
	 
	 function setCalcName(&$name, $id, $tax_rate, $country_2_code='', &$tcode=0)
	 {
		 
		 if (!empty($tax_rate)) {
		     $isMoss = $this->_isMossMode($id); 
			 if (empty($isMoss)) {
			 $home = $this->_getHomeCountry(); 
	         // if we are not in moss mode, we always set the country to home: 
			 
		     $country_2_code = $home;
			}
		  }
		 
		 if (!defined('VM_VERSION')) return; 
		 if (empty($this->_calc_name))
				{
					$this->_calc_name = array(); 
				}
		 
		 if (empty($this->_calc_name_orig))
				{
					$this->_calc_name_orig = array(); 
				}
		 
		  if (!isset($this->_calc_name_orig[$id])) {
		   $this->_calc_name_orig[$id] = $name; 
		  }
		  
		   if (empty($this->_calc_tcode)) {
			   $this->_calc_tcode = array(); 
		   }
		   
		   $this->_calc_tcode[$id] = $tcode; 
		  
		if (empty($this->_vat_delivery)) {
			   $this->_vat_delivery = array(); 
		   }
		   
		 
		  $this->_vat_delivery[$id] = $country_2_code; 
		   		  
		  
		  if (empty($tax_rate)) {
		    $name = $this->_calc_name_orig[$id]; 
			return;
		  }
		  
		  if (!empty($this->_calc_name_orig[$id]))
			 {
				 static $cache; 
				 
				 if (empty($cache)) $cache = array(); 
				 
				 $key = $id.'_'.$tax_rate.'_'.$country_2_code; 
				 
				 if (isset($cache[$key])) { 
				  $name = $cache[$key]; 
				  return; 
				 }
				 
			  $display = $this->params->get('display_form', ''); 
			  if (empty($display)) return; 
				
			  $display_tax_country = $this->params->get('display_tax_country', false); 
			 
				$r = round($tax_rate); 
				$trate = number_format($r, 0, '', ''); 
				$display = str_replace('NN', $trate, $display); 
					

				$zx = explode('D', $display); 
				$cc = count($zx); 
				$cc--; 
				$Dstr = ''; 
				for ($i=1; $i<=$cc; $i++)
				{
					$Dstr .= 'D'; 
				}
					 
				$rd = abs($r - $tax_rate); 
				$df = number_format($rd, $cc, '.', '');
				$zx = explode('.', $df); 
				$dec = $zx[1]; 
				$display = str_replace($Dstr, $dec, $display); 
				
				
				
				
				 if (empty($display_tax_country)) { 
			    $display = str_replace('CCC', '', $display); 
				$display = str_replace('CC', '', $display); 
				$display = str_replace(',CC', '', $display); 
				$display = str_replace(',CCC', '', $display); 
				$display = str_replace(', CCC', '', $display); 
				$display = str_replace(', CC', '', $display); 
			  }
			  else {
				if (stripos($display, 'CCC')) {
					$db = JFactory::getDBO(); 
				 $q = "select country_3_code from #__virtuemart_countries where country_2_code = '".$db->escape($country_2_code)."' limit 0,1"; 
				 $country_3_code = $this->helper->loadResult($q); 
				 
				 $display = str_replace('CCC', $country_3_code, $display); 
				 
				}
				else
				{
					$display = str_replace('CC', $country_2_code, $display); 
				}
			  }
				
				
				$this->_calc_name[$id] = $this->_calc_name_orig[$id].$display; 
				
				$cache[$key] =& $this->_calc_name[$id]; 
				
				/*
			   $tr = number_format($tax_rate, 2, '.', ''); 
			   
			   
			   
			   if ((!empty($country_2_code))  && (!empty($display_tax_country))) {
			   $this->_calc_name[$id] = $this->_calc_name_orig[$id].' (%'.$tr.', '.$country_2_code.')'; 
			   }
			   else
			   {
				   $this->_calc_name[$id] = $this->_calc_name_orig[$id].' (%'.$tr.')'; 
			   }
			   */
			   
			    $name =& $this->_calc_name[$id];
				
			 }
			 
			 
			 
			
			 
			 
			 
	 }
	 

}

// No closing tag
/* deprecated settings: 

<field name="moss_mode" type="list"  default="0" label="Choose Tax Mode (MOSS vs EU VAT)" description="Choose EU VAT mode if you charge ONLY your home vat rates for tax eligible persons. Choose MOSS if you are registered for VAT in multiple countries and you charge the rates of customer country." >
				 			<option
				value="0">EU VAT (Only Home Country Rates)</option>
			<option
				value="1">MOSS (multiple EU VAT registratons)</option>

				</field>

*/

