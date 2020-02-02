<?php

if (!defined('_JEXEC'))
die('Direct Access to ' . basename(__FILE__) . ' is not allowed.');

/**
 * Calculation plugin for zip based tax rates
 *
 * @version $Id: 2.0.0
 * @package tax_per_zip for Virtuemart 2.0.20+
 * @subpackage Plugins - Zip Based US Tax
 * @author RuposTel.com
 * @copyright Copyright (C) RuposTel.com
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
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


class plgVmCalculationTax_per_zip extends vmCalculationPlugin {

	
    var $_calc_name = array(); 
	var $_calc_name_name = array();
	
	var $_xParamsP = ''; 
	var $_varsToPushParamP = ''; 
	
	function __construct(& $subject, $config) {
		// 		if(self::$_this) return self::$_this;
		

		$varsToPush = array();
		
		$varsToPush = array(
			'activated'          => array(0, 'int'),
			'google_url'		=> array('', 'varchar(1025)')
		);
		$this->varsToPush = $varsToPush; 
		$this->setConfigParameterable ('calc_params', $varsToPush);

		$this->_loggable = TRUE;
		$this->tableFields = array('id', 'virtuemart_order_id', 'tax_id');
		$this->_tableId = 'id';
		$this->_tablepkey = 'id';
		
		$this->taxTable =& $this->_tablename; 
		
		require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'helper.php'); 
		
		$this->helper = new zipTaxHelper($this); 
		
		$this->_xParamsP =& $this->_xParams; 
		$this->_varsToPushParamP =& $this->_varsToPushParam; 
		
		if (!defined('ZIP_TAX'))
		define('ZIP_TAX', 'zip_tax'); 
	    $this->helper = new zipTaxHelper($this); 
		parent::__construct($subject, $config);
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
			'activated' => 'tinyint(1) NOT NULL DEFAULT \'0\'',
			'google_url' => 'varchar(1025)',
		);
		return $SQLfields;
	}

	
	function plgVmOnStoreInstallPluginTable($jplugin_name,$name,$table=0) {
		
		//vmdebug('plgVmOnStoreInstallPluginTable',$jplugin_name,$name);
		if(!defined('VM_VERSION') or VM_VERSION < 3){
			return $this->onStoreInstallPluginTable($jplugin_name,$name);
		} else {
			$this->onStoreInstallPluginTable ($jplugin_name);
			$this->plgVmStorePluginInternalDataCalc($name);
		}

	}

	public function getVmPluginCreateTableSQLData() {
		return "CREATE TABLE IF NOT EXISTS `" . $this->_tablename . "_config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `virtuemart_calc_id` mediumint(1) unsigned DEFAULT NULL,
  `zip_start` mediumint(5) unsigned DEFAULT NULL,
  `zip_end` mediumint(5) unsigned DEFAULT NULL,
  `tax_rate` decimal(10,4) unsigned DEFAULT NULL,
  `created_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `modified_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) NOT NULL DEFAULT '0',
  `locked_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `locked_by` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `search` (`zip_start`,`zip_end`,`virtuemart_calc_id`),
  KEY `idx_virtuemart_calc_id` (`virtuemart_calc_id`),
  KEY `zip_start` (`zip_start`),
  KEY `virtuemart_calc_id` (`virtuemart_calc_id`),
  KEY `zip_end` (`zip_end`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Table for US tax based on Zip Code' AUTO_INCREMENT=1 ;"; 
 		

	}

	/**
	 * Gets the sql for creation of the table
	 * This table is used to store order information per plugin
	 * @author RuposTel.com
	 */
	public function getVmPluginCreateTableSQL() {
		
 		return "CREATE TABLE IF NOT EXISTS `" . $this->_tablename . "` (
 			    `id` int(11) unsigned NOT NULL AUTO_INCREMENT ,
 			    `virtuemart_calc_id` mediumint(1) UNSIGNED DEFAULT NULL,
				`zip_start` mediumint(5) UNSIGNED DEFAULT NULL,
				`zip_end` mediumint(5) UNSIGNED DEFAULT NULL,
				`tax_rate` decimal(10,4) UNSIGNED DEFAULT NULL,
 			    `created_on` datetime NOT NULL default '0000-00-00 00:00:00',
 			    `created_by` int(11) NOT NULL DEFAULT 0,
 			    `modified_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
 			    `modified_by` int(11) NOT NULL DEFAULT 0,
 			    `locked_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
 			    `locked_by` int(11) NOT NULL DEFAULT 0,
 			     PRIMARY KEY (`id`),
 			     KEY `idx_virtuemart_calc_id` (`virtuemart_calc_id`)
 			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Table for US tax based on Zip Code' AUTO_INCREMENT=1 ;";
			//ALTER TABLE  `rupostel_vm2onj2`.`rg6ma_virtuemart_calc_plg_tax_per_zip_config` ADD UNIQUE  `search` (  `zip_start` ,  `zip_end` ,  `virtuemart_calc_id` )
	}


	function plgVmAddMathOp(&$entryPoints){
	    
 		$entryPoints[] = array('calc_value_mathop' => ZIP_TAX, 'calc_value_mathop_name' => 'Zip Range Based Tax');
	}

	function plgVmOnDisplayEdit(&$calc,&$html){
		
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
		JHTML::script($root.'plugins/vmcalculation/tax_per_zip/helper.js');
		
		$q = 'select * from '.$this->_tablename.'_config where virtuemart_calc_id = '.$calc->virtuemart_calc_id.' order by zip_start asc'; 
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		
		/*
		if (!empty($calc->virtuemart_calc_id))
		{
		$html .= '<input type="hidden" name="cid[]" value="'.$calc->virtuemart_calc_id.'" />'; 
		$html .= '<input type="hidden" name="view" value="calc" />'; 
		$html .= '<input type="hidden" name="task" value="edit" />'; 
	    $html .= '<input type="hidden" name="option" value="com_virtuemart" />'; 
		}
		*/
	
	
		$html .= '<script type="text/javascript">
//<![CDATA[
     var line_iter = '.(count($res)).'; var myline = \'';

	//if (count($res)>300)
	{
	
	 $jsline = '<tr id="trid_{num}"><td>zip_start_{num}</td><td>zip_end_{num}</td><td>tax_rate_{num}</td><td>&nbsp;</td></tr>';	
	}
	/*
	else
	{		
	 
	  $jsline = '<tr id="trid_{num}"><td><input type="text" onchange="javascript: wasChanged({num})" id="zip_start{num}" name="zip_start{num}" value="zip_start_{num}" /></td><td><input type="text" name="zip_end{num}" onchange="javascript: wasChanged({num})" value="zip_end_{num}" /></td><td><input onchange="javascript: wasChanged({num})" type="text" name="tax_rate{num}" value="tax_rate_{num}" /></td><td><button href="#" onclick="javascript: return op_new_line(myline,\\\'tax_rates\\\');">Add more...</button></td></tr>';
	  $jsline = 'Manual editing is not allowed - use CSV linked URL'; 
	}
	*/

	 $jsline2 = str_replace('zip_start_{num}', '', $jsline);
     $jsline2 = str_replace('zip_end_{num}', '', $jsline2);
     $jsline2 = str_replace('tax_rate_{num}', '0', $jsline2);	 
	 $html .= $jsline2.'\'; 
//]]>
</script>';
        
		if (empty($calc->google_url)) $calc->google_url = ''; 
		$google_url = addslashes($calc->google_url); 
		
		$html .= '<fieldset><legend>'.JText::_('VMCALCULATION_TAX_PER_ZIP').'</legend>
		This extension was made for you by <a href="http://www.rupostel.com/">RuposTel.com</a>
		
		<table class="admintable" id="tax_ratesx">
	<tr><th colspan="3">Load from Google drive</th></tr>
	
	
	<tr><td>URL of publicly shared spreadsheet in g.docs (File -> Publish To Web -> CSV):</td><td><input type="url" name="google_url" value="'.$google_url.'" /></td><td><input type="button" onclick="loadGoogle()" value="Clear current data and load CSV from Google Drive" /><input type="hidden" value="0" name="load_google" id="load_google"></td></tr>
	<tr><td>Georgia data example: 
	
	<a href="https://docs.google.com/spreadsheets/d/15A3K5ZjD6Sxmo906gd1lkriqV4Ic-7BSMJQQwG6shyE/pub?output=csv">https://docs.google.com/spreadsheets/d/15A3K5ZjD6Sxmo906gd1lkriqV4Ic-7BSMJQQwG6shyE/pub?output=csv</a></td></tr>
	
		
	<tr><td>Florida data example:<a href="https://docs.google.com/spreadsheets/d/1nfXRgGOV6J7huz46sAOdItDlSEkiG9Xa7Np6BZhE61A/pub?output=csv">https://docs.google.com/spreadsheets/d/1nfXRgGOV6J7huz46sAOdItDlSEkiG9Xa7Np6BZhE61A/pub?output=csv</a></td></tr>
	<tr><td>California data example:<a href="https://docs.google.com/spreadsheets/d/1EHs1XIYolHInUNqQ1wJzY3Mc2g6dcPnB0_pTMArt-z0/pub?output=csv">https://docs.google.com/spreadsheets/d/1EHs1XIYolHInUNqQ1wJzY3Mc2g6dcPnB0_pTMArt-z0/pub?output=csv</a></td></tr>
	
	<tr><td><b>Make sure to update your tax rates regularly and you CSV import format is as one of the above</b></td></tr>
	
	<tr><td colspan="3">This function will erase all the data and replace them with the new data from Google Drive Spreadsheet. <br />Supported format: county/state tax/county surtax rate/county total/multiple zip code columns. </td></tr>
	</table>
		
	
	<table class="admintable" id="tax_rates">
	<tr><th>'
	.JText::_('VMCALCULATION_TAX_PER_ZIP_ZIP_START')
	.'</th><th>'
	.JText::_('VMCALCULATION_TAX_PER_ZIP_ZIP_END')
	.'</th><th colspan="2">'
	.JText::_('VMCALCULATION_TAX_PER_ZIP_TAX_RATE')
	.'</th></tr>';
	
    if (!empty($res))
	{
	foreach ($res as $k=>$row)
	 {
	   $rowhtml = str_replace('zip_start_{num}', $row['zip_start'], $jsline); 
	   $rowhtml = str_replace('zip_end_{num}', $row['zip_end'], $rowhtml);
	   $rowhtml = str_replace('tax_rate_{num}', $row['tax_rate'], $rowhtml);
	   $rowhtml = str_replace("\'", "'", $rowhtml);
	   $rowhtml = str_replace('{num}', $k, $rowhtml); 
	   if (count($res)<=300)
	   {
	   $rowhtml .= '<input type="hidden" name="waschanged'.$k.'" id="waschanged'.$k.'" value="NO;'.$row['id'].'" />'; 
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
		$html .= '<input type="hidden" name="last_iter" id="last_iter" value="'.count($row).'" />';
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

	
	
	public function plgVmInterpreteMathOp ($calculationHelper, $rule, $price,$revert){
		 
		 if (JFactory::getApplication()->isAdmin()) return $price; 
		 
		 if (empty($calculationHelper->inCart)) return; 
		
		$rule = (object)$rule;
		if(empty($rule->published)) return $price;
		$mathop = $rule->calc_value_mathop;
		$tax = 0.0;
		$id = $rule->virtuemart_calc_id; 
		
		$this->_getMethods($calculationHelper); 
		
		if (!class_exists('VirtueMartCart')) require(JPATH_SITE. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_virtuemart' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'cart.php');
		
		$found = false; 
		foreach ($this->methods as $method) { 
		
		if ($mathop==ZIP_TAX)
		{
			$calc_kind = $rule->calc_kind; 
			$id = (int)$rule->virtuemart_calc_id; 
			if ($id !== $method->virtuemart_calc_id) continue; 
			$calc =& $calculationHelper;
			
			
		
		
			if (!empty($calc->_cart))
			{
				/*
			$types = array('DBTaxRulesBill'=>'DBTaxBill', 'taxRulesBill'=>'TaxBill', 'DATaxRulesBill'=>'DATaxBill', 'salesPriceDBT'=>'salesPriceDBT', 'VatTax'=>'VatTax', 'Marge'=>'Marge', 'Tax'=>'Tax', 'DBTax'=>'DBTax', 'DATax'=>'DATax'); 
			
			foreach ($types as $t=>$kind)
			{
				if ($calc_kind === $kind)
				{
			
			if (isset($calc->_cart->cartData[$t][$id]))
			if (isset($calc->_cart->cartData[$t][$id]['calc_name']))
			{
				$calc->_cart->cartData[$t][$id]['calc_name'] .= '(test)'; 
				
			}
				}
			}
			
			
			*/
			}	

			
			if(isset($calculationHelper->_product)){
				$productId = $calculationHelper->_product->virtuemart_product_id;
			}
			
		  if (isset($calculationHelper->_cart))
		  {
			  $cart = $calculationHelper->_cart; 
		  }
		  else
		  {
			  
		  $cart = VirtueMartCart::getCart();
		  }
		  
		  if (method_exists($cart, 'getST'))
		  {
			  $address = $cart->getST(); 
		  }
		  else
		  {
		    $address = (($cart->ST === 0) ? $cart->BT : $cart->ST);
		  }
		  
		 
		  if (empty($address['zip'])) return; 
		  $zip = $address['zip']; 
		  $x = stripos($zip, '-'); 
		  if ($x !== false)
		  {
			  $zip = substr($zip, 0, $x); 
		  }
		  
		  $zip = preg_replace("/[^0-9 ]/", '', $zip); 
		  if (strlen($zip)==9)
		  {
			  $zip = substr($zip, 0, 5); 
		  }
		  else
		  if (strlen($zip)==8)
		  {
			  $zip = substr($zip, 0, 4); 
		  }
		  $zip = (int)$zip; 
		 
		 
		  
		  $tax_rate = $this->helper->getTaxRate($zip, $id);
		  
		  
		  
		 
		  if (empty($zip) || ($tax_rate === NULL))
		   {
		   
		     $tax_rate = $rule->calc_value; 
		   }
		  $tax_rate = (float)$tax_rate; 
		 
		   
		    $this->setCalcName($this->_calc_name[$id], $id, $tax_rate); 
		   
		   $found = true; 
		   break;
		}
		}
		
		if ($found) {
		if($revert){
			$tax = -$tax;
		}
		
		return $price + (float)$tax;
		}
		
		return false; 
		

		
	}

	function plgVmConfirmedOrder ($cart, $order) {

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
			
			if ($v['calc_value_mathop'] == ZIP_TAX)
			{
				if (!class_exists('VirtueMartCart')) require(JPATH_SITE. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_virtuemart' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'cart.php');
				
				 if (isset($calculationHelper->_cart))
				 {
					$cart = $calculationHelper->_cart; 
				  }
				  else
				  {
			  
					$cart = VirtueMartCart::getCart();
				   }
		  
		  if (method_exists($cart, 'getST'))
		  {
			  $address = $cart->getST(); 
		  }
		  else
		  {
		    $address = (($cart->ST === 0) ? $cart->BT : $cart->ST);
		  }
		  
		  $id = $v['virtuemart_calc_id']; 
		 
		  if (!empty($address['zip'])) {
		  $zip = preg_replace("/[^0-9 ]/", '', $address['zip']); 
		  $zip = (int)$zip; 
		  if (!empty($zip)) {
		  
		  $tax_rate = $this->helper->getTaxRate($zip, $id);
		  if (!empty($tax_rate))
		  {
			  $rules[$k]['calc_value'] = $tax_rate; 
			  $rules[$k]['calc_value_mathop'] = '+%'; 
			  
			  
		  }
		  }
		  }	
				$this->setCalcName($rules[$k]['calc_name'], $id, $tax_rate); 
			}
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
			
			if ($v['calc_value_mathop'] == ZIP_TAX)
			{
				if (!class_exists('VirtueMartCart')) require(JPATH_SITE. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_virtuemart' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'cart.php');
				
				 if (isset($calculationHelper->_cart))
				 {
					$cart = $calculationHelper->_cart; 
				  }
				  else
				  {
			  
					$cart = VirtueMartCart::getCart();
				   }
		  
		  if (method_exists($cart, 'getST'))
		  {
			  $address = $cart->getST(); 
		  }
		  else
		  {
			if (empty($cart->ST)) 
				if (isset($cart->BT))
				$address = $cart->BT;
		    
		  }
		  
		  
		  
		  if (empty($address)) return; 
		  
		  $id = $v['virtuemart_calc_id']; 
		 
		  if (!empty($address['zip'])) {
		  $zip = preg_replace("/[^0-9 ]/", '', $address['zip']); 
		  $zip = (int)$zip; 
		  if (!empty($zip)) {
		  
		  $tax_rate = $this->helper->getTaxRate($zip, $id);
		  if (!empty($tax_rate))
		  {
			  $rules[$k]['calc_value'] = $tax_rate; 
			  $rules[$k]['calc_value_mathop'] = '+%'; 
			  
			  
		  }
		  
		  
		   $found[$k] = $tax_rate; 
		   if ($tax_rate >= $last_tax_rate) {
		    $largest = $k; 
		    $last_tax_rate = $tax_rate; 
		    }
		  
		   $this->setCalcName($rules[$k]['calc_name'], $id, $tax_rate); 
		  
		  
		  }
		  }	
				
			}
		}
		}
		//If in cart, the tax is calculated per bill, so the rule per product must be removed
		/*
		if($calculationHelper->inCart){
			foreach($rules as $k=>$rule){
				if($rule['calc_value_mathop']=='zip_tax'){
					self::$_removedRules[$rules[$k]['virtuemart_calc_id']] = $rules[$k]; 
					unset($rules[$k]);
				}
			}
		}
		*/
		
		
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
		
		$this->helper->storeConfig($data); 
		
		if (isset($data['load_google']))
		{
			    $url = $data['google_url']; 
				$calc_id = (int)$data['virtuemart_calc_id']; 
				if (!empty($calc_id)) { 
				$this->helper->calc_id = $calc_id; 
				$this->helper->loadGoogle($url); 
			    }
				return;
		}
		
	    $too_many = JRequest::getVar('too_many'); 
		if (!empty($too_many)) return; 
		
		$last_iter = (int)$data['last_iter']; 
		$myid = (int)$data['my_calc_id']; 
		if (empty($myid)) return;
		$db = JFactory::getDBO(); 
		for($i=0; $i<=$last_iter; $i++)
		 {
		 
		    $zip_start = (int)($data['zip_start'.$i]); 
			
			
			$zip_end = (int)($data['zip_end'.$i]); 
			$tax_rate = (float)$data['tax_rate'.$i]; 
			if (!isset($data['waschanged'.$i]))
			 {
			 
			 if (($zip_start == '') || ($zip_end == '')) continue; 
			 
			   // new insert
			   $q = "insert into `".$this->_tablename.'_config` (id, virtuemart_calc_id, zip_start, zip_end, tax_rate) values (NULL, "'.$myid.'", "'.$db->escape($zip_start).'", "'.$zip_end.'", "'.$tax_rate.'")';
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
			    JFactory::getApplication()->enqueueMessage($e); 
			   }
			  
			 }
			 else
			 {
			    $a = explode(';', $data['waschanged'.$i]); 
				$id = (int)$a[1]; 
				 
				if ($a[0] == 'YES')
				{
			    if (($zip_start == '') || ($zip_end == ''))
				 {
				   $q = 'delete from '.$this->_tablename.'_config where id = '.$id.' limit 1'; 
				   $db->setQuery($q); 
				   $db->query(); 
				    if (!empty($e)) { echo $e; die(); }
					
				 }
				 else
				 {
				   $q = 'update '.$this->_tablename.'_config set zip_start = "'.$zip_start.'", zip_end="'.$zip_end.'", tax_rate = "'.$tax_rate.'" where id = '.$id.' limit 1'; 
				   $db->setQuery($q); 
				   $db->query(); 
				   
				    if (!empty($e)) { echo $e; die(); }
				   
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
		
		 $q = 'select * from `#__virtuemart_calcs` where `calc_value_mathop` LIKE "'.ZIP_TAX.'" 
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
	 
 function setCalcName(&$name, $id, $tax_rate, $country_2_code='')
	 {
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

