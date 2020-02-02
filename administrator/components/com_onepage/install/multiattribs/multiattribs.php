<?php
defined('_JEXEC') or 	die( 'Direct Access to ' . basename( __FILE__ ) . ' is not allowed.' ) ;

defined('DS') or define('DS', DIRECTORY_SEPARATOR);
if (!class_exists( 'VmConfig' )) require(JPATH_ROOT.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');

VmConfig::loadConfig();


if (!class_exists('vmCustomPlugin')) require(JPATH_VM_PLUGINS .DIRECTORY_SEPARATOR. 'vmcustomplugin.php');

class plgVmCustomMultiattribs extends vmCustomPlugin {

	function __construct(& $subject, $config) {

		parent::__construct($subject, $config);

		$varsToPush = array(	
			'custom_multiattribs'=>array('','varchar(15000)'),
			
		);

		$this->setConfigParameterable('customfield_params',$varsToPush);

	}
	
	
	function getCurrencies()
	{
		$currencyModel = VmModel::getModel('currency');
		$currencies = $currencyModel->getVendorAcceptedCurrrenciesList(1);
		
		$db = JFactory::getDBO(); 
		foreach ($currencies as $a=>$c)
	{

		$q = 'select * from #__virtuemart_currencies where virtuemart_currency_id = '.(int)$c->virtuemart_currency_id.' limit 0,1'; 
		$db->setQuery($q); 
		$res = $db->loadAssoc(); 

		foreach ($res as $k=>$v)
		{
			$currencies[$a]->$k = $v; 
		}
		}
		return $currencies; 

	}

	// get product param for this plugin on edit
	function plgVmOnProductEdit($field, $product_id, &$row,&$retValue) {

		if ($field->custom_element != $this->_name) return '';
		$root = Juri::root();
		if (substr($root, -1) !== '/') $root .= '/'; 
		$root = str_replace('administrator/', '', $root); 
		JHtml::script($root.'plugins/vmcustom/multiattribs/assets/backend.js'); 
		JHtml::stylesheet($root.'plugins/vmcustom/multiattribs/assets/backend.css'); 
		JHTML::stylesheet($root.'components/com_onepage/themes/extra/bootstrap/bootstrap.min.css');
	
		
	   $cs = $this->getCurrencies(); 
		
		if (!isset($field->virtuemart_customfield_id)) 
		{
			$field_id = 0; 
		}
		else
		{
		$field_id = $field->virtuemart_customfield_id; 
		}
		if (isset($field->virtuemart_product_id))
		$product_id = $field->virtuemart_product_id; 
		$values = $this->_getValue($product_id, $field_id); 
		
		$field_id = (int)$field_id; 
		$row = (int)$row; 
		
		$isJson = JRequest::getVar('format', ''); 
		$js = ''; 
		if (defined('VM_VERSION') && (VM_VERSION >= 3))
		if ($isJson === 'json')
		{
			$js = '<script>Joomla.submitbutton(\'apply\');</script>'; 
		}

		
		
		
		if ((!empty($field_id)) && (!empty($values)))
		{
		  
 		  $my_values = $values[$product_id][$field_id]; 
		}
		else
		{
			$my_values = array(); 
		}
		//$ret[$row['virtuemart_product_id']][$row['virtuemart_product_custom_id']][$row['attribute_name']]['attrib'] = $row['attribute_name']; 
	
		
		
		//$field_id = $field->virtuemart_custom_id; 
		
		
		

		$retValue .= $this->renderByLayout('backend',array('product_id'=>$product_id,'field'=>$field, 'currencies'=>$cs, 'values'=>$my_values, 'row'=>$row) );
		
		if ($field_id === 0) {
			$retValue .= $js; 
		
		
		}

		
		return true; 
		
		//VmConfig::$echoDebug = true;
		//vmdebug('plgVmOnProductEdit',$field);
		$html ='
			<fieldset>
				<legend>'. vmText::_('VMCUSTOM_TEXTINPUT') .'</legend>
				<table class="admintable">
					'.VmHTML::row('input','VMCUSTOM_TEXTINPUT_SIZE','customfield_params['.$row.'][custom_size]',$field->custom_size);
		$options = array(0=>'VMCUSTOM_TEXTINPUT_PRICE_BY_INPUT',1=>'VMCUSTOM_TEXTINPUT_PRICE_BY_LETTER');
		$html .= VmHTML::row('select','VMCUSTOM_TEXTINPUT_PRICE_BY_LETTER_OR_INPUT','customfield_params['.$row.'][custom_price_by_letter]',$options,$field->custom_price_by_letter,'','value','text',false);

		//$html .= ($field->custom_price_by_letter==1)?vmText::_('VMCUSTOM_TEXTINPUT_PRICE_BY_LETTER'):vmText::_('VMCUSTOM_TEXTINPUT_PRICE_BY_INPUT');
		$html .='</td>
		</tr>
				</table>
			</fieldset>';
		$retValue .= $html;
		$row++;
		return true ;
	}
	//$dispatcher->trigger ('plgVmOnProductEdit', array($field, $product_id, &$row, &$retValue));
	function plgVmOnStoreProduct($datas, $plugin_params)
	{
		if (!$this->_checkTable()) return; 
		
		$product_id = (int)$datas['virtuemart_product_id']; 
		
		if (empty($product_id)) return; 
		
		$myids = array(); 
		
		foreach ($datas['field'] as $c_id => $field)
		{
			
			if ($field['customfield_value'] === 'multiattribs')
			{
				

				$id = (int)$field['virtuemart_customfield_id'];
				if (empty($id)) continue; 
				$myids[$id] = $id; 

			}
		}
		
		$data = JRequest::get('post'); //('customfield_params', array()); 

		foreach ($myids as $id)
		{
			if (empty($id)) continue; 
		    $this->_clearCF($product_id, $id); 
			
			if (isset($data['customfield_params'][$id]['custom_multiattribs']))
			{
				
				
				$c2 = $data['customfield_params'][$id]['custom_multiattribs'];
				
				
				foreach ($c2 as $c)
				{
				$cids = array(); 
				foreach ($c as $k=>$v)
				{
					if (stripos($k, 'currency_price_')!==false)
					{
						$cid = str_replace('currency_price_', '', $k); 
						$cids[] = $cid; 
					}
					
					
				}
				
				
				
				
				
				
				
					 
					$attrib = $c['attrib']; 
					if (empty($attrib)) continue; 
					
					foreach ($cids as $k2=>$n)
					{
					  $price = $c['currency_price_'.$n]; 
					  $cur_id = $n; 
					  
					  
					  $this->_storeValue($product_id, $id, $price, $attrib, $cur_id); 
					
					}
					
					
					
				
				}
				
				
			}
		 
		}
		
		
		
		
	}
	
	private function _checkError(&$db)
	{

	  $e = $db->getErrorMsg(); if (!empty($e)) {
	JFactory::getApplication()->enqueueMessage($e); 
     return false; 
    }
		return true; 	
	}
	
	private function _clearCF($product_id, $virtuemart_customfield_id)
	{
		$db = JFactory::getDBO(); 
		$q = 'delete from #__virtuemart_custom_plg_multiattribs where virtuemart_product_id = '.(int)$product_id.' and virtuemart_product_custom_id = '.(int)$virtuemart_customfield_id; 
		$db->setQuery($q); 
		$db->query(); 
	}
	private function _getValue($product_id, $virtuemart_customfield_id=0, $cur_id=0)
	{
		$db = JFactory::getDBO(); 
		$q = 'select * from `#__virtuemart_custom_plg_multiattribs` where virtuemart_product_id = '.(int)$product_id; 
		if (!empty($cur_id))
		{
			$q .= ' and virtuemart_currency_id = '.(int)$cur_id; 
		}
		if (!empty($virtuemart_customfield_id))
		{
			$q .= ' and virtuemart_product_custom_id = '.(int)$virtuemart_customfield_id; 
		}
		
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		if (empty($res)) return array(); 
		
		$res = (array)$res; 
		
		
		$ret = array(); 
		
		foreach ($res as $n=>$row)
		{
		   @$ret[$row['virtuemart_product_id']][$row['virtuemart_product_custom_id']][$row['attribute_name']]['attrib'] = $row['attribute_name']; 
		   
		   if (!empty($cur_id))
		   {
			   @$ret[$row['virtuemart_product_id']][$row['virtuemart_product_custom_id']][$row['attribute_name']][$row['id']] = $row['price']; 
			   
			    @$ret[$row['virtuemart_product_id']][$row['virtuemart_product_custom_id']][$row['attribute_name']]['id'] = $row['id']; 
		   }
		   else
		   {
		   @$ret[$row['virtuemart_product_id']][$row['virtuemart_product_custom_id']][$row['attribute_name']][$row['virtuemart_currency_id']] = $row['price']; 
		   }
		   
		}
		
		return $ret; 

		
		
	}
	
	private function _storeValue($product_id, $id, $price, $attrib, $cur_id)
	{
		
		
		
	  $db = JFactory::getDBO(); 
	  $q = 'select `virtuemart_product_id` from #__virtuemart_custom_plg_multiattribs where virtuemart_product_custom_id = '.(int)$id.' and virtuemart_currency_id = '.(int)$cur_id.' and virtuemart_product_id = '.(int)$product_id.' limit 0,1';
	  
	  $db->setQuery($q); 
	  $res = $db->loadResult(); 
	  if (!$this->_checkError($db)) return false; 
	  
	
	
	$price = str_replace(',', '.', $price); 
	
	$price = floatval($price); 
	  /*
	  if (!empty($res))
	  {
		  $q = "update `#__virtuemart_custom_plg_multiattribs` set price = '".$db->escape($price)."', attribute_name = '".$db->escape($attrib)."' where virtuemart_product_custom_id = ".(int)$id." and virtuemart_currency_id = ".(int)$cur_id." and virtuemart_product_id = ".(int)$product_id." limit 1"; 
		  $db->setQuery($q); 
		  $db->query(); 
		  
	  }
	  else
	  */
	  {
		  
		  $q = "insert into `#__virtuemart_custom_plg_multiattribs` (`id`, `virtuemart_product_custom_id`, `virtuemart_product_id`, `attribute_name`, `price`, `virtuemart_currency_id`) values (NULL, '".(int)$id."', '".(int)$product_id."', '".$db->escape($attrib)."', '".$db->escape($price)."', '".(int)$cur_id."')"; 
		  $db->setQuery($q); 
		  $db->query(); 
		  
	  }
		if (!$this->_checkError($db)) return false; 
	  
	  
	}
	
	
	private function _checkTable()
	{
		/*
	 $db = JFactory::getDBO(); 
	 $q = 'drop table #__virtuemart_custom_plg_multiattribs'; 
	 $db->setQuery($q); 
	 $db->query(); 
	   */
	 if ($this->_tableExists('virtuemart_custom_plg_multiattribs')) return true; 
		$q = 'CREATE TABLE IF NOT EXISTS `#__virtuemart_custom_plg_multiattribs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `virtuemart_product_custom_id` int(11) NOT NULL,
  `virtuemart_product_id` int(11) NOT NULL,
  `attribute_name` varchar(255) NOT NULL,
  `price` decimal(15,6) NOT NULL,
  `virtuemart_currency_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `virtuemart_product_id` (`virtuemart_product_id`),
  KEY `virtuemart_product_id_2` (`virtuemart_product_id`,`virtuemart_currency_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;'; 

    $db = JFactory::getDBO(); 
	$db->setQuery($q); 
	$db->query(); 
	if (!$this->_checkError($db)) return false; 
	return true; 
	}
	
	
  private function _tableExists($table)
  {
   
   
   $db = JFactory::getDBO();
   $prefix = $db->getPrefix();
   $table = str_replace('#__', '', $table); 
   $table = str_replace($prefix, '', $table); 
   $table = $db->getPrefix().$table; 
   
   
   
   
   
   $q = 'select * from '.$table.' where 1 limit 0,1';
   
   $q = "SHOW TABLES LIKE '".$table."'";
	   $db->setQuery($q);
	   $r = $db->loadResult();
	   
	   
	   
	   if (!empty($r)) 
	    {
	
		return true;
		}
	
   return false;
  }
	
	private function _includes() 
	{
		
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
if (!class_exists( 'VmConfig' )) require(JPATH_ROOT.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');


VmConfig::loadConfig();
VmConfig::loadJLang('mod_virtuemart_product', true);

if (!class_exists('VmView'))
	require(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'vmview.php');

if (!class_exists('shopFunctionsF'))
require(JPATH_VM_SITE .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'shopfunctionsf.php');


if (!class_exists('CurrencyDisplay'))
	if (!class_exists('CurrencyDisplay')) require(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' .  DIRECTORY_SEPARATOR. 'helpers' . DIRECTORY_SEPARATOR . 'currencydisplay.php');


if(!class_exists('calculationHelper')) require(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' .  DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'calculationh.php');

if (!class_exists('VirtuemartCart'))
	require(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'cart.php');




	}
	
	private function _getCurrency()
	{
		
		$this->_includes(); 
		
		$calculator = calculationHelper::getInstance();
		$cart = VirtuemartCart::getCart(); 
		
		if (empty($cart->pricesCurrency))
		$cart->pricesCurrency = $calculator->_currencyDisplay->getCurrencyForDisplay();

				
		$mainframe = JFactory::getApplication();
		$virtuemart_currency_id = $mainframe->getUserStateFromRequest( "virtuemart_currency_id", 'virtuemart_currency_id',vRequest::getInt('virtuemart_currency_id',$cart->pricesCurrency) );
		
		$virtuemart_currency_id = (int)$virtuemart_currency_id; 
		
		return $virtuemart_currency_id; 
	}
	
	function plgVmOnDisplayProductFEVM3(&$product,&$group) {

		if ($group->custom_element != $this->_name) return '';
		
		$field_id = $group->virtuemart_customfield_id; 
		$product_id = $product->virtuemart_product_id; 
			
		$virtuemart_currency_id = $this->_getCurrency(); 
		
		$values = $this->_getValue($product_id, $field_id, $virtuemart_currency_id); 
		
		if (empty($values)) 
		{
			return true; 
		}
		
		$my_values = $values[$product_id][$field_id]; 

		$root = Juri::root();
		if (substr($root, -1) !== '/') $root .= '/'; 
		$root = str_replace('administrator/', '', $root); 
		JHtml::script($root.'plugins/vmcustom/multiattribs/assets/fe.js'); 
		
		
		$group->display .=  $this->renderByLayout('default',array('product'=>$product,'group'=>$group, 'values'=>$my_values) );

		return true;
	}


	/**
	 * Function for vm3
	 * @see components/com_virtuemart/helpers/vmCustomPlugin::plgVmOnViewCart()
	 * @author Patrick Kohl
	 */
	function plgVmOnViewCart($product,$row,&$html) {
		
		
		if (empty($product->productCustom->custom_element) or $product->productCustom->custom_element != $this->_name) return '';
		
		
		
		if (!$plgParam = $this->GetPluginInCart($product)) return '' ;

		
		foreach($plgParam as $k => $item){

		
			if(!empty($item['comment']) ){
				if($product->productCustom->virtuemart_customfield_id==$k){
					$html .='<span>'.vmText::_($product->productCustom->custom_title).' '.$item['comment'].'</span>';
				}
			}
		}
		return true;
	}
	
	
	
	
    private function _getTitleAndPrices($id, $onlytitle=false, $currency_id=0)
	{
		
		$db = JFactory::getDBO(); 
		
		if ($onlytitle)
		{
			$q = 'select attribute_name from #__virtuemart_custom_plg_multiattribs where id = '.(int)$id; 
			$db->setQuery($q); 
			$res = $db->loadResult(); 
			if (!$this->_checkError($db)) return ''; 
			$res = (string)$res; 
			return $res; 
		}
		$q = 'select m1.id, m1.attribute_name, m1.price from #__virtuemart_custom_plg_multiattribs as m1, #__virtuemart_custom_plg_multiattribs as m2 where m2.id = '.(int)$id.' and m1.virtuemart_product_id = m2.virtuemart_product_id and m1.virtuemart_product_custom_id = m2.virtuemart_product_custom_id and m1.attribute_name = m2.attribute_name'; 
		
		if (!empty($currency_id))
		{
			$q .= ' and m1.virtuemart_currency_id = '.(int)$currency_id.' limit 0,1'; 
		}
		
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		if (!$this->_checkError($db)) return array(); 
		$ret = array(); 
		foreach ($res as $k=>$v)
		{
			$ret[$v['id']] = $v; 
		}
		return $ret; 
		
	}

	function plgVmOnViewCartVM3(&$product, &$productCustom, &$html) {
		
		
		if (empty($productCustom->custom_element) or $productCustom->custom_element != $this->_name) return false;

		
		
		if(empty($product->customProductData[$productCustom->virtuemart_custom_id][$productCustom->virtuemart_customfield_id])) return false;
		
		
		foreach( $product->customProductData[$productCustom->virtuemart_custom_id] as $k =>$item ) {
			if($productCustom->virtuemart_customfield_id == $k) {
				if(isset($item['comment'])){
					
					$id = $item['comment']; 
					$price = $this->_getPrice($id); 
					$title = $this->_getTitleAndPrices($id, true); 
				    $plugin_title = $productCustom->custom_title; 
					
					$calculator = calculationHelper::getInstance();
					$currencyDisplay = $calculator->_currencyDisplay; 
					
					$html .= $this->renderByLayout('carttitle',array('product'=>$product,'productCustom'=>$productCustom, 'title'=>$title, 'plugin_title'=>$plugin_title, 'price'=>$price, 'currencyDisplay'=>$currencyDisplay) );
					
					
				}
			}
		}
		return true;
	}
	
	

	function plgVmOnViewCartModuleVM3( &$product, &$productCustom, &$html) {
		return $this->plgVmOnViewCartVM3($product,$productCustom,$html);
	}

	function plgVmDisplayInOrderBEVM3( &$product, &$productCustom, &$html) {
		$this->plgVmOnViewCartVM3($product,$productCustom,$html);
	}

	function plgVmDisplayInOrderFEVM3( &$product, &$productCustom, &$html) {
		$this->plgVmOnViewCartVM3($product,$productCustom,$html);
	}


	/**
	 *
	 * vendor order display BE
	 */
	function plgVmDisplayInOrderBE(&$item, $productCustom, &$html) {
		if(!empty($productCustom)){
			$item->productCustom = $productCustom;
		}
		if (empty($item->productCustom->custom_element) or $item->productCustom->custom_element != $this->_name) return '';
		$this->plgVmOnViewCart($item,$productCustom,$html); //same render as cart
    }


	/**
	 *
	 * shopper order display FE
	 */
	function plgVmDisplayInOrderFE(&$item, $productCustom, &$html) {
		if(!empty($productCustom)){
			$item->productCustom = $productCustom;
		}
		if (empty($item->productCustom->custom_element) or $item->productCustom->custom_element != $this->_name) return '';
		$this->plgVmOnViewCart($item,$productCustom,$html); //same render as cart
    }



	public function plgVmOnStoreInstallPluginTable($psType,$data,$table) {

		if($psType!=$this->_psType) return false;
		if(empty($table->custom_element) or $table->custom_element!=$this->_name ){
			return false;
		}
		if(empty($table->is_input)){
			vmInfo('COM_VIRTUEMART_CUSTOM_IS_CART_INPUT_SET');
			$table->is_input = 1;
			$table->store();
		}
		//Should the textinput use an own internal variable or store it in the params?
		//Here is no getVmPluginCreateTableSQL defined
 		//return $this->onStoreInstallPluginTable($psType);
	}

	/**
	 * Declares the Parameters of a plugin
	 * @param $data
	 * @return bool
	 */
	function plgVmDeclarePluginParamsCustomVM3(&$data){
    
	$dt2 = (array)$data; 
	if (isset($dt2['virtuemart_product_id']))
	{
		//set defaults: 
	$get = JRequest::getVar('customProductData', array()); 
	$product_id = $dt2['virtuemart_product_id']; 
	$virtuemart_custom_id = $dt2['virtuemart_custom_id']; 
	if (!$this->isMe($virtuemart_custom_id)) return; 
	$virtuemart_customfield_id = $dt2['virtuemart_customfield_id']; 
	$arr2 = array($virtuemart_custom_id => $virtuemart_customfield_id); 
	$arr = array($product_id=> $arr2); 
	foreach ($arr as $k=>$v)
	{
	if (!isset($get[$k])) $get[$k] = $v; 
	}
	
	//JRequest::setVar('customProductData', $get); 
	$_REQUEST['customProductData'] = $get; 
	/*test end*/		
	}
	
		return $this->declarePluginParams('custom', $data);
	}
	
	function isMe($id) {
		$db = JFactory::getDBO(); 
		$q = 'select virtuemart_custom_id from #__virtuemart_customs where virtuemart_custom_id = '.$id. ' and custom_element = "multiattribs" limit 0,1'; 
		$db->setQuery($q); 
		$id = $db->loadResult(); 
		if (empty($id)) return false; 
		else return true; 
	}

	function plgVmGetTablePluginParams($psType, $name, $id, &$xParams, &$varsToPush){
		return $this->getTablePluginParams($psType, $name, $id, $xParams, $varsToPush);
	}

	function plgVmSetOnTablePluginParamsCustom($name, $id, &$table,$xParams){
		return $this->setOnTablePluginParams($name, $id, $table,$xParams);
	}

	
	function plgVmOnDisplayEdit($virtuemart_custom_id,&$customPlugin){
		return $this->onDisplayEditBECustom($virtuemart_custom_id,$customPlugin);
	}
	
	private function _getDefaultPrice($product_id, $customfield_id)
	{
		$cur_id = $this->_getCurrency(); 
		$q = 'select `price` from #__virtuemart_custom_plg_multiattribs where virtuemart_product_custom_id = '.(int)$customfield_id.' and virtuemart_currency_id = '.(int)$cur_id.' and virtuemart_product_id = '.(int)$product_id.' limit 0,1';
		$db = JFactory::getDBO(); 
		$db->setQuery($q); 
	    $res = $db->loadResult(); 
		if (empty($res)) return 0; 
		$ret = floatval($res); 
		return $ret; 
	}
	
	private function _getPrice($id)
	{
			$cur = $this->_getCurrency(); 
			$prices = $this->_getTitleAndPrices($id, false, $cur); 
			$price = 0; 
			foreach ($prices as $k=>$v)
			{
				$price = $v['price']; 
				break; 
			}
			return $price; 
	}
	
	public function plgVmPrepareCartProduct(&$product, &$customfield,$selected,&$modificatorSum){

		if ($customfield->custom_element !==$this->_name) return ;

		//$product->product_name .= 'Ice Saw';
		//vmdebug('plgVmPrepareCartProduct we can modify the product here');
		$selected_v = $selected; 
		if ($selected_v === 1)
		{
			//we have a default attribute to get selected
			$price = $this->_getDefaultPrice($product->virtuemart_product_id, $customfield->virtuemart_customfield_id); 
			$modificatorSum += $price; 
			return true; 
		}
		
		if (!empty($selected['comment'])) {
			$id = $selected['comment']; 
		
			$price = $this->_getPrice($id); 
			$modificatorSum += $price; 
		} else {
			$modificatorSum += 0.0;
		}

		return true;
	}


	public function plgVmDisplayInOrderCustom(&$html,$item, $param,$productCustom, $row ,$view='FE'){
		$this->plgVmDisplayInOrderCustom($html,$item, $param,$productCustom, $row ,$view);
	}

	public function plgVmCreateOrderLinesCustom(&$html,$item,$productCustom, $row ){
// 		$this->createOrderLinesCustom($html,$item,$productCustom, $row );
	}
	function plgVmOnSelfCallFE($type,$name,&$render) {
		$render->html = '';
	}
	
	public function onAfterRender()
	{
		
	}

}



// No closing tag