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

class OPCCommonHtml {

 public static function getStateHtmlSelectByStateAndCountry($cs, $country, $prefix='', $required=true, $ajax=true, &$retval=array())
 {
	     $html = self::getStateHtmlOptionsByStateAndCountry($cs, $country, $retval); 
	 
	     if ($required)
		 $ret = '<select class="inputbox multiple opcrequired" id="'.$prefix.'virtuemart_state_id" opcrequired="opcrequired" size="1"  name="'.$prefix.'virtuemart_state_id" >'.$html.'</select>'; 
		 else
	     $ret = '<select class="inputbox multiple" id="'.$prefix.'virtuemart_state_id"  size="1"  name="'.$prefix.'virtuemart_state_id" >'.$html.'</select>'; 
	 
	      return $ret; 
	 
 }

 public static function getOptionVals($field_name) {
	 
	 static $cache; 
	 if (isset($cache[$field_name])) return $cache[$field_name]; 
	 	$res = array(); 
	$db = JFactory::getDBO(); 
	//$q = 'select `v`.`fieldtitle` as field_title, `v`.`fieldvalue` as field_value from `#__virtuemart_userfield_values` as v, `#__virtuemart_userfields` as f where `f`.`virtuemart_userfield_id` = `v`.`virtuemart_userfield_id` and `f`.`name` = \''.$db->escape($business_selector).'\''; 
	
	$q = 'select  `v`.`fieldtitle` as fieldtitle, `v`.`fieldvalue`, f.name as name, f.type, f.value from `#__virtuemart_userfield_values` as v, `#__virtuemart_userfields` as f where `f`.`virtuemart_userfield_id` = `v`.`virtuemart_userfield_id` and `f`.`name` = \''.$db->escape($field_name).'\''; 
	try
	{
	 $db->setQuery($q); 
	 $res = $db->loadAssocList(); 

	}
	catch(Exception $e)
	{
	
		$res = array(); 
		
	}
	
	if (empty($res)) {
		$cache[$field_name] = array(); 
		return array(); 
	}
	
	$ret = array(); 
	foreach ($res as $k=>$row) {
		$ret[$row['fieldvalue']] = JText::_($row['fieldtitle']); 
	}
	$cache[$field_name] = $ret; 
	return $ret; 
	
	
 }

 public static function getStateHtmlOptionsByStateAndCountry($cs, $country, &$statesvalues=array())
 {
	  $states = array(); 	
	  $statesvalues = array(); 
	require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
	$stateModel = OPCmini::getModel('state'); //new VirtueMartModelState();
	//$html = '<div style="display: none;"><form>'; 
	static $cache; 
	if (isset($cache[$country])) $states = $cache[$country]; 
	else
	{
		if (empty($cache)) $cache = array(); 
		$cache[$country] = $states = $stateModel->getStates( $country, true, true );
	}
	
	if (!empty($states)) {
	 $ret = '<option value="none">'.OPCLang::_('COM_VIRTUEMART_LIST_EMPTY_OPTION').'</option>'; 
	}
	else {
	 $ret = '<option value=""> - </option>'; 
	}
	if (!empty($states))
	foreach ($states as $k=>$v)
	 {
	     
	    $ret .= '<option ';
		if ($v->virtuemart_state_id == $cs) $ret .= ' selected="selected" '; 
		$ret .= ' value="'.$v->virtuemart_state_id.'">'.$v->state_name.'</option>'; 
		
		$statesvalues[(int)$v->virtuemart_state_id] = $v->state_name; 
		
	 }
	 
	 
	 
	 return $ret; 
 }

 public static function getStateHtmlOptions(&$cart, $country, $type='BT', &$retval=array())
 {
    	
    //require_once(JPATH_VM_ADMINISTRATOR.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'state.php'); 

   
	
	
	$cs = '';  	
	if (!is_array($cart->$type)) $cs = ''; 
	else
    if ((!empty($cart->{$type}))) 
	if (is_array($cart->{$type})) 
	if (isset($cart->{$type}["virtuemart_state_id"])) 
	if (!empty($cart->{$type}["virtuemart_state_id"] ))
    $cs = $cart->{$type}['virtuemart_state_id']; 	
	
	$ret = self::getStateHtmlOptionsByStateAndCountry($cs, $country, $retval); 
	 
	 return $ret; 

 }
 
 public static function getCountriesOptionsVals(&$retval=array()) {
	 static $returnX; 
	 if (!empty($returnX)) {
		 $retval = $returnX; 
		 return $returnX; 
	 }
	 
	 $list = self::getCountries(); 
	 $reval = array(); 
	 foreach ($list as $k=>$v) {
		 $retval[(int)$v->virtuemart_country_id] = $v->country_name; 
	 }
	 $returnX = $retval; 
	 
 }
 
 public static function getCountries() {
	 static $countries; 
	 if (!empty($countries)) return $countries; 
	 
	 require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
	$countryModel = OPCmini::getModel('country'); //new VirtueMartModelCountry(); 
	$countries = $countryModel->getCountries(true, true, false); 
	return $countries; 
	
 }
 
 public static function getStateList(&$ref)
 {
   
	  /*
    if (!class_exists('VirtueMartModelState'))
    require(JPATH_VM_ADMINISTRATOR.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'state.php'); 
    if (!class_exists('VirtueMartModelCountry'))
	require(JPATH_VM_ADMINISTRATOR.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'country.php'); 
      */
	
	   $db = JFactory::getDBO(); 
	   $q = 'select count(*) as count from #__virtuemart_states where 1'; 
	   $db->setQuery($q); 
	   $count = $db->loadResult(); 

	   require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
		OPCmini::setVMLANG(); 
	   
	
	$js_filename = 'opc_states_'.VMLANG.'_'.$count.'.js'; 
	$js_file = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'dynamic_scripts'.DIRECTORY_SEPARATOR.$js_filename; 
	$js_path = 'components/com_onepage/config/dynamic_scripts/'; 
	
	/*
	if (file_exists($js_file))
	{
	  JHTMLOPC::script($js_filename, $js_path); 
	}
	else
	*/
	{
	   
	
	
	
	
	
	$js = ' var OPCStates = { '."\n";  
	
	
	
	  
	
	$list = self::getCountries(); 
	
	$countries = array();
    $states = array(); 	
	$stateModel = OPCmini::getModel('state'); //new VirtueMartModelState();
	
	// if state is set in BT, let's make it default
	if (!empty($ref->cart->BT) && (!empty($ref->cart->BT['virtuemart_state_id'])))
	$cs = $ref->cart->BT['virtuemart_state_id']; 
	else $cs = '';  
	
	//$html = '<div style="display: none;">'; 
	
	$counts = count($list); 
	$ci =0; 
	
	foreach ($list as $c)
	{
	
	  if (empty($c->published)) continue; 
	  
	  $ci++; 
	  $states[$c->virtuemart_country_id] = $stateModel->getStates( $c->virtuemart_country_id, true, true );
	  unset($state); 
		//$html .= '<input type="hidden" name="opc_state_list" id="state_for_'.$c->virtuemart_country_id.'" value="" />'; 	  
	  if (!empty($states[$c->virtuemart_country_id])) 
	  {
	  
	  $js .= ' state_for_'.$c->virtuemart_country_id.': { '."\n"; 
	  
	  //$html .= '<select id="state_for_'.$c->virtuemart_country_id.'">'; 
	  //$html .= '<option value="">'.OPCLang::_('COM_VIRTUEMART_LIST_EMPTY_OPTION').'</option>'; 
	  
	  
	  $counts2 = count($states[$c->virtuemart_country_id]); 
	  $ci2 =0; 
	  
	  foreach ($states[$c->virtuemart_country_id] as $state)
	   {
	   
	     if (empty($state->published )) continue; 
	     $ci2++; 
	      //$js .= ' state_for_'.$c->virtuemart_country_id.'['.$state->virtuemart_state_id.']: "'.str_replace('"', '\"', $state->state_name).'",'."\n"; 
		  $js .= $state->virtuemart_state_id.': "'.str_replace('"', '\"', $state->state_name).'"'; 
		  if ($ci2 != $counts2) $js .= ', '; 
		  $js .= "\n"; 
		  
	     //$html .= '<option ';
		 //if ($state->virtuemart_state_id == $cs) $html .= ' selected="selected" '; 
		 //$html .= ' value="'.$state->virtuemart_state_id.'">'.$state->state_name.'</option>'; 
	   }
	   $js .= ' }'; 
	   if ($ci != $counts) $js .= ', ';
	   $js .= "\n"; 
	  //$html .= '</select>';
	  }
	  // debug

	  
	  
	}
	
	
	$js .= ' }; 
	
	
	'; 
	$html = '<div style="display: none;">'; 
	$html .= '<select id="no_states" name="no_states">'; 
	$html .= '<option value="">'.OPCLang::_('COM_VIRTUEMART_LIST_EMPTY_OPTION').'</option>'; 
	$html .= '</select>'; 
	$html .= '</div>'; 
	
	//$html .= '</div>'; 
	//alert(OPCStates.state_for_10[373]); 
	if (!empty($ref->cart->BT) && (!empty($ref->cart->BT['virtuemart_state_id'])))
	$cs = $ref->cart->BT['virtuemart_state_id']; 
	else $cs = '';  
	
	if (!empty($ref->cart->ST) && (!empty($ref->cart->ST['virtuemart_state_id'])))
	$css = $ref->cart->ST['virtuemart_state_id']; 
	else $css = '';  
	
	$html .= '<script type="text/javascript">
	var selected_bt_state = \''.$cs.'\';
	var selected_st_state = \''.$css.'\';
	
	</script>'; 
	
	//$html = '<script>'.$js.'</script>'; 
	
	
			jimport( 'joomla.filesystem.folder' );
			jimport( 'joomla.filesystem.file' );
			
			 if (JFile::write($js_file, $js) !== false)
			 {
			     JHTMLOPC::script($js_filename, $js_path); 
			 }
			 else
			 {
			   $html .= '
<script type="text/javascript">
//<![CDATA[		   
			   '.$js.'
//]]>		   
</script>
'; 
			 }
	
	return $html; 
	}
	return ''; 
 }
public static function getExtras(&$ref)
{
   $html = OPCCommonHtml::getStateList($ref); 
  //test ie8: 
  //$html = ''; 
  if (!empty(OPCloader::$extrahtml)) $html .= OPCloader::$extrahtml; 
  $html .= '<div id="opc_totals_hash">&nbsp;</div>'; 	
  $html2 = '<form action="#" name="hidden_form">
  <div style="display: none;">
   <input type="text" name="fool" value="1" required="required" class="required hasTip" title="fool::fool" />
   <select class="vm-chzn-select " name="hidden">
     <option value="1">test</option>
   </select>
   </div>
   <input type="hidden" name="opc_min_pov" id="opc_min_pov" value="" />
   <div style="display: none;">
   <a href="#" rel="{handler: \'iframe\', size: {x: 500, y: 400}}" class="opcmodal">a</a>
   <a href="#" rel="{handler: \'iframe\', size: {x: 500, y: 400}}" class="pfdmodal">a</a>
   </div>
   '.$html.'
   </form>
<script type="text/javascript">
/* <![CDATA[ */   

if (typeof Onepage != \'undefined\')
{
	Onepage.ga(\'Checkout Impression\', \'Checkout General\'); 
	'; 
	
	$session = JFactory::getSession(); 
	$seen = $session->get('opc_cart_seen', false);
	if (empty($seen))
	{
		$html2 .= ' Onepage.ga(\'Unique Checkout Session\', \'Checkout General\'); '; 
		$seen = $session->set('opc_cart_seen', true);
	}
	
	$error = $session->get('opc_last_error', ''); 
	if (!empty($error))
	{
		$error = strip_tags($error); 
		$error = str_replace("\r\n", " ", $error); 
		$error = str_replace("\r\r\n", " ", $error); 
		$error = str_replace("\n", " ", $error); 
		$error = str_replace("\r", " ", $error); 
		$error = htmlentities($error, ENT_XHTML, 'utf-8', false); 
		$error = addslashes($error); 
		if (!empty($error))
		{
		$html2 .= ' 
		try { 
		 Onepage.ga(\'Checkout Redirected: '.$error.'\', \'Checkout Error Redirect\'); 
		}
		catch(e) { 
		 Onepage.ga(\'Checkout Redirected with an Error \', \'Checkout Error Redirect\'); 
		}
		'; 
		}
		$session->clear('opc_last_error'); 
		
	}
	
	
	
$html2 .= '	
}

/* ]]> */
</script>
   
   
   
   '; 
  return $html2;
}

 public static function getFormVarsRegistration(&$ref, $task='opcregister')
 {
 	 
	 
   if (!isset(OPCloader::$inform_html)) OPCloader::$inform_html = array(); 
   $ih = implode('', OPCloader::$inform_html); 
   $html = '<input type="hidden" value="com_onepage" name="option" id="opc_option" />
		<input type="hidden" value="'.$task.'" name="task" id="opc_task" />
		<input type="hidden" value="opc" name="view" id="opc_view" />
		<input type="hidden" value="1" name="nosef" id="nosef" />
		<input type="hidden" name="saved_shipping_id" id="saved_shipping_id" value=""/>
		<input type="hidden" name="order_language" value="'.JFactory::getLanguage()->getTag().'" />
		<input type="hidden" value="opc" name="controller" id="opc_controller" />
		<input type="hidden" name="form_submitted" value="0" id="form_submitted" />
		<div style="display:none;" id="inform_html">&nbsp;'.$ih.'</div>';
		
	
  return $html;

 }
 public static function getFormVars(&$ref)
 {
     
	 
	 
   if (!isset(OPCloader::$inform_html)) OPCloader::$inform_html = array(); 
   $ih = implode('', OPCloader::$inform_html); 
   $html = '<input type="hidden" value="com_onepage" name="option" id="opc_option" />
		<input type="hidden" value="checkout" name="task" id="opc_task" />
		<input type="hidden" value="opc" name="view" id="opc_view" />
		<input type="hidden" value="1" name="nosef" id="nosef" />
		<input type="hidden" name="saved_shipping_id" id="saved_shipping_id" value=""/>
		<input type="hidden" name="order_language" value="'.JFactory::getLanguage()->getTag().'" />
		<input type="hidden" value="opc" name="controller" id="opc_controller" />
		<input type="hidden" name="form_submitted" value="0" id="form_submitted" />
		<div style="display:none;" id="inform_html">&nbsp;'.$ih.'</div>';
		
	
  return $html;
		
 }


}
