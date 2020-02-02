<?php
defined('_JEXEC') or 	die( 'Direct Access to ' . basename( __FILE__ ) . ' is not allowed.' ) ;

defined('DS') or define('DS', DIRECTORY_SEPARATOR);
if (!class_exists( 'VmConfig' )) require(JPATH_ROOT.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');

VmConfig::loadConfig();


if (!class_exists('vmCustomPlugin')) require(JPATH_VM_PLUGINS .DIRECTORY_SEPARATOR. 'vmcustomplugin.php');

class plgVmCustomVmarticle extends vmCustomPlugin {

	function __construct(& $subject, $config) {

		parent::__construct($subject, $config);

		$varsToPush = array(	
			'custom_vmarticle'=>array('','varchar(15000)'),
			
		);
		if (defined('VM_VERSION') && (VM_VERSION >= 3))
		$this->setConfigParameterable('customfield_params',$varsToPush);
		else
		$this->setConfigParameterable('custom_params',$varsToPush);
	}
	
	private function _getArticleSelector($name, $value, $required=false)
		{
		
		$id = $name; 
	
		if (empty($value) || (!is_numeric($value))) $value = null; 
		
		// Load the modal behavior script.
		JHtml::_('behavior.modal', 'a.opcmodal');

		// Build the script.
		$script = array();
		$html	= array();
		//if (stripos($id, '{')===false)
		{
		
		if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) {
			
		
		$html[] = '<script type="text/javascript">'; 
		$html[] = '//<![CDATA['; 
		
		$html[] = '	function jSelectArticle_'.$id.'(id, title, catid, object) {';
		$html[] = '		document.id("'.$id.'_id").value = id;';
		$html[] = '		document.id("'.$id.'_name").value = title;';
		$html[] = '		SqueezeBox.close();';
		$html[] = '	}';
		$html[] = '//]]>'; 
		$html[] = '</script>'; 

		}
		else
		{
		$html[] = '<script type="text/javascript">'; 
		$html[] = '//<![CDATA['; 
		$html[] = "
		function jSelectArticle(id, title, object) {
			document.getElementById(object + '_id').value = id;
			document.getElementById(object + '_name').value = title;
			document.getElementById('sbox-window').close();
		}";
		$html[] = '//]]>'; 
		$html[] = '</script>'; 
		
		}
		// Add the script to the document head.
		//JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));
		}

		// Setup variables for display.
		
		 if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) {
		$link	= 'index.php?option=com_content&amp;view=articles&amp;layout=modal&amp;tmpl=component&amp;function=jSelectArticle_'.$id;
		}
		else
		$link = 'index.php?option=com_content&amp;task=element&amp;tmpl=component&amp;object='.$id;
		$db	= JFactory::getDBO();
		$db->setQuery(
			'SELECT `title` ' .
			' FROM #__content' .
			' WHERE id = '.(int) $value
		);
		$title = $db->loadResult();

		if ($error = $db->getErrorMsg()) {
			JError::raiseWarning(500, $error);
		}
		
		if (empty($title)) {
		   if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) 
			$title = JText::_('COM_CONTENT_SELECT_AN_ARTICLE');
			else
			$title = JText::_('Select an Article');
		}
		
		$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
		
		// The current user display field.
		$html[] = '<div class="fltlft">';
		$html[] = '  <input type="text" id="'.$id.'_name" value="'.$title.'" disabled="disabled" size="35" />';
		$html[] = '</div>';

		// The user select button.
		
		$html[] = '<div class="button2-left">';
		$html[] = '  <div class="blank">';
		$html2 = ''; 
		$html2 .= '	<a class="opcmodal" id="modal_link_'.$id.'" title="';
		if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) 
		$html2 .= JText::_('COM_CONTENT_CHANGE_ARTICLE'); 
		else
		$html2 .= JText::_('Select an Article');
		$html2 .= '"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 800, y: 450}}">';
		if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) 
		$html2 .= JText::_('COM_CONTENT_CHANGE_ARTICLE_BUTTON');
		else
		$html2 .= JText::_('Select');
		$html2 .= '</a>';
		$html[] = $html2; 
		$html[] = '  </div>';
		$html[] = '</div>';

		// The active article id field.
		if (0 == (int)$value) {
			$value = '';
		} else {
			$value = (int)$value;
		}

		// class='required' for client side validation
		$class = '';
		if ($required) {
			$class = ' class="required modal-value"';
		}

		$html[] = '<input type="hidden" id="'.$id.'_id"'.$class.' name="'.$name.'" value="'.$value.'" />';

		$zhtml = implode("\n", $html);; 

		return $zhtml; 
	
		}
	private function getArticleSelector($f, $v)
	{
		JFactory::getLanguage()->load('com_content'); 
		if (file_exists(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'config.php'))
		{
			require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'config.php'); 
			$config = new JModelConfig(); 
			return $config->getArticleSelector($f, $v, false); 
		    
		}
		else
		{
			return $this->_getArticleSelector($f, $v); 
		}
		
	}

	// get product param for this plugin on edit
	function plgVmOnProductEdit($field, $product_id, &$row,&$retValue) {

		if ($field->custom_element != $this->_name) return '';
		/*
		$root = Juri::root();
		if (substr($root, -1) !== '/') $root .= '/'; 
		$root = str_replace('administrator/', '', $root); 
		JHtml::script($root.'plugins/vmcustom/vmarticle/assets/backend.js'); 
		JHtml::stylesheet($root.'plugins/vmcustom/vmarticle/assets/backend.css'); 
		JHTML::stylesheet($root.'components/com_onepage/themes/extra/bootstrap/bootstrap.min.css');
	
		*/		

		$this->_checkTable();
	    if (!isset($field->virtuemart_customfield_id)) $field->virtuemart_customfield_id = 0; 
		$field_id = $field->virtuemart_customfield_id; 
		
		if (!isset($field->virtuemart_product_id)) {
		 $product_id = JRequest::getVar('virtuemart_product_id', 0); 
		}
		else
		{
		  $product_id = $field->virtuemart_product_id; 
		}
		
		if (is_array($product_id)) $product_id = (int)reset($product_id); 
		
		if (defined('VM_VERSION') && (VM_VERSION >= 3)) {
		if (isset($field->customfield_value)) {
			if (is_numeric($field->customfield_value)) 
			{
				$article_id = (int)$field->customfield_value; 
			}
		}
		}
		else
		{
		if (isset($field->custom_value)) {
			if (is_numeric($field->custom_value)) 
			{
				$article_id = (int)$field->custom_value; 
			}
		}
			
		}
		if (empty($article_id)) 
		{
		$values = array(); //$this->_getValue($product_id, $field_id); 
		$row = $field->virtuemart_customfield_id; 
		
		
		$v = $this->_getValue($product_id, $field_id); 
		
		$article_id = $this->getFirstValue($v); 
		
		}
		
		
		
		
		// check if we are in json: 
		$isJson = JRequest::getVar('format', ''); 
		$js = ''; 
		if (defined('VM_VERSION') && (VM_VERSION >= 3))
		if ($isJson === 'json')
		{
			$js = '<script>Joomla.submitbutton(\'apply\');</script>'; 
		}
		
		
		if (empty($row)) {
		  $row = JRequest::getInt('row', 0); 
		}
		if (!empty($field_id)) $row = $field_id; 
		
		if (defined('VM_VERSION') && (VM_VERSION >= 3))
		$extra = '<input type="hidden" name="customfield_params[vmarticle]" value="vmarticle" />'; 
		else
		$extra = '<input type="hidden" name="plugin_param[vmarticle]" value="vmarticle" /><input type="hidden" name="field['.$row.'][custom_value]" value="vmarticle" /> <input type="hidden" name="save_customfields" value="1" />'; 
	
		
		
		$name = 'custom_vmarticle_'.$row; 
		$retValue .= $extra.$this->getArticleSelector($name, $article_id).$js; 
		return true;
		$my_values = $values[$product_id][$field_id]; 
		//$ret[$row['virtuemart_product_id']][$row['virtuemart_product_custom_id']][$row['attribute_name']]['attrib'] = $row['attribute_name']; 
	
		
		
		$field_id = $field->virtuemart_custom_id; 
		
		
		ob_start();

		$retValue .= $this->renderByLayout('backend',array('product_id'=>$product_id,'field'=>$field, 'currencies'=>$cs, 'values'=>$my_values) );
		return true; 
		
		
	}
	//$dispatcher->trigger ('plgVmOnProductEdit', array($field, $product_id, &$row, &$retValue));
	function plgVmOnStoreProduct($datas, $plugin_params)
	{
		static $run; 
		if (!empty($run)) return; 
		$run = true; 
	
		$custom_vmarticle = array(); //JRequest::getInt('custom_vmarticle', 0); 
	    foreach ($datas as $key=>$val)
		{
			if (stripos($key, 'custom_vmarticle_')!==false)
			{
				$k2 = str_replace('custom_vmarticle_', '', $key); 
				$k2 = (int)$k2; 
				$custom_vmarticle[$k2] = (int)$val; 
			}
		}
		
		
		if (!$this->_checkTable()) return; 
		
		$product_id = $datas['virtuemart_product_id']; 
	    if (is_array($product_id)) $product_id = (int)reset($product_id); 

		
		if (empty($product_id)) return; 
		
		$myids = array(); 
		
		foreach ($datas['field'] as $c_id => $field)
		{
			if (defined('VM_VERSION') && (VM_VERSION >= 3)) {
			if ($field['customfield_value'] === 'vmarticle')
			{
				

				$id = $field['virtuemart_customfield_id'];
				$myids[$id] = $id; 

			}
			}
			else
			{
				if ($field['custom_value'] === 'vmarticle')
			{
				

				$id = $field['virtuemart_customfield_id'];
				$myids[$id] = $id; 

			}
			}
		}
		

		
		$data = JRequest::get('post'); 

		foreach ($myids as $id)
		{
			 if (empty($id)) continue; 
		     $this->_clearCF($product_id, $id); 
			 if (empty($custom_vmarticle[$id])) continue; 
			 $this->_storeValue($product_id, $id, 0, $custom_vmarticle[$id], 0); 
			 $this->_updateVMValue($product_id, $id, 0, $custom_vmarticle[$id], 0); 
			
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
		$q = 'delete from #__virtuemart_custom_plg_vmarticle where virtuemart_product_id = '.(int)$product_id.' and virtuemart_product_custom_id = '.(int)$virtuemart_customfield_id; 
		$db->setQuery($q); 
		$db->query(); 
	}
	private function _getValue($product_id, $virtuemart_customfield_id=0, $cur_id=0)
	{
		if (empty($product_id)) return array(); 
		$db = JFactory::getDBO(); 
		$q = 'select * from `#__virtuemart_custom_plg_vmarticle` where virtuemart_product_id = '.(int)$product_id; 
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
	
	private function getFirstValue($cf)
	{
		if (empty($cf)) return 0; 
		
		foreach ($cf as $k=>$v)
		 foreach ($v as $k2=>$v2)
		  foreach ($v2 as $k3=>$v3) 
		    return $cf[$k][$k2][$k3]['attrib']; 
	}
	private function _updateVMValue($product_id, &$id, $price, $attrib, $cur_id)
	{
		if (defined('VM_VERSION') && (VM_VERSION >= 3)) {
	  if (!empty($id)) {
	  $db = JFactory::getDBO(); 
	  $q = 'select `virtuemart_product_id` from `#__virtuemart_product_customfields` where `virtuemart_customfield_id` = '.(int)$id.' limit 0,1';
	  
	  $db->setQuery($q); 
	  $res = $db->loadResult(); 
	  
	  if (!$this->_checkError($db)) return false; 
	  
	  if (!empty($res)) {
	     $q = 'update `#__virtuemart_product_customfields` set `customfield_value` = "'.(int)$attrib.'" where `virtuemart_customfield_id` = '.(int)$id; 
		 $db->setQuery($q); 
		 $db->query(); 
	  }
	  }
		}
		else
		{
			if (!empty($id)) {
	  $db = JFactory::getDBO(); 
	  $q = 'select `virtuemart_product_id` from `#__virtuemart_product_customfields` where `virtuemart_customfield_id` = '.(int)$id.' limit 0,1';
	  
	  $db->setQuery($q); 
	  $res = $db->loadResult(); 
	  
	  if (!$this->_checkError($db)) return false; 
	  
	  if (!empty($res)) {
	     $q = 'update `#__virtuemart_product_customfields` set `custom_value` = "'.(int)$attrib.'" where `virtuemart_customfield_id` = '.(int)$id; 
		 $db->setQuery($q); 
		 $db->query(); 
	  }
	  }
		}
	  
	  
	}
	private function _storeValue($product_id, $id, $price, $attrib, $cur_id)
	{
		
	  
		
	  $db = JFactory::getDBO(); 
	  $q = 'select `virtuemart_product_id` from #__virtuemart_custom_plg_vmarticle where virtuemart_product_custom_id = '.(int)$id.' and virtuemart_currency_id = '.(int)$cur_id.' and virtuemart_product_id = '.(int)$product_id.' limit 0,1';
	  
	  $db->setQuery($q); 
	  $res = $db->loadResult(); 
	  if (!$this->_checkError($db)) return false; 
	  
	
	
	$price = str_replace(',', '.', $price); 
	
	$price = floatval($price); 
	  /*
	  if (!empty($res))
	  {
		  $q = "update `#__virtuemart_custom_plg_vmarticle` set price = '".$db->escape($price)."', attribute_name = '".$db->escape($attrib)."' where virtuemart_product_custom_id = ".(int)$id." and virtuemart_currency_id = ".(int)$cur_id." and virtuemart_product_id = ".(int)$product_id." limit 1"; 
		  $db->setQuery($q); 
		  $db->query(); 
		  
	  }
	  else
	  */
	  {
		  
		  $q = "insert into `#__virtuemart_custom_plg_vmarticle` (`id`, `virtuemart_product_custom_id`, `virtuemart_product_id`, `attribute_name`, `price`, `virtuemart_currency_id`) values (NULL, '".(int)$id."', '".(int)$product_id."', '".$db->escape($attrib)."', '".$db->escape($price)."', '".(int)$cur_id."')"; 
		  $db->setQuery($q); 
		  $db->query(); 
		  
	  }
		if (!$this->_checkError($db)) return false; 
	  
	  
	}
	
	
	private function _checkTable()
	{
		/*
	 $db = JFactory::getDBO(); 
	 $q = 'drop table #__virtuemart_custom_plg_vmarticle'; 
	 $db->setQuery($q); 
	 $db->query(); 
	   */
	 if ($this->_tableExists('virtuemart_custom_plg_vmarticle')) return true; 
		$q = 'CREATE TABLE IF NOT EXISTS `#__virtuemart_custom_plg_vmarticle` (
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
		return 0; 
	}
	function plgVmOnDisplayProductFE($product, &$row, &$field) {
	  
	  
	  $product->customfields = array(&$field); 
	  if ($field->custom_element != $this->_name) return;
	  
	  $this->plgVmOnDisplayProductFEVM3($product, $field); 
	  $field->custom_title = null; 
	  return true; 
	}
	function plgVmOnDisplayProductFEVM3(&$product,&$group) {

		if ($group->custom_element != $this->_name) return '';
		
		
		
		$field_id = $group->virtuemart_customfield_id; 
		$product_id = $product->virtuemart_product_id; 
			
		$virtuemart_currency_id = 0;
		
		$article_id = 0; 
		
		foreach ($product->customfields as $f)
		{
			if (isset($f->custom_element) && ($f->custom_element === 'vmarticle'))
			{
				if (is_numeric($f->custom_value)) {
				  $article_id = (int)$f->custom_value; 
				}
			}
		}
		
		if (empty($article_id)) {
		$values = $this->_getValue($product_id, $field_id, 0); // $virtuemart_currency_id); 
		
		
		if (empty($values)) 
		{
			return true; 
		}
		
		
		
		
		$article_id = $this->getFirstValue($values); 
		}
		
		if (empty($article_id))
		{
			return; 
		}			
		
		$group->title = null; 
		$group->display .=  self::getArticle($article_id); 

		return true;
	}

	private static function getArticle($id, $repvals=array())
	{
		if (file_exists(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'loader.php'))
		{
			require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'loader.php'); 
			return OPCloader::getArticle($id, $repvals); 
		}
		
		$article = JTable::getInstance("content");
		
		$article->load($id);
		
		if (!class_exists('CurrencyDisplay'))
		require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'currencydisplay.php');
		$currencyDisplay = CurrencyDisplay::getInstance();

		
		if (class_exists('OPCParameter')) {
		 $parametar = new OPCParameter($article->attribs);
		}
		else {
		 $parametar = new JRegistry($article->attribs);
		}
		$x = $parametar->get('show_title', false); 
		$x2 = $parametar->get('title_show', false); 
		
		$intro = $article->get('introtext'); 
		$full = $article->get("fulltext"); 
		JPluginHelper::importPlugin('content'); 
		$dispatcher = JDispatcher::getInstance(); 
		$mainframe = JFactory::getApplication(); 
		$params = $mainframe->getParams('com_content'); 
		
		if ($x || $x2)
		{
			
			

			$title = '<div class="componentheading'.$params->get('pageclass_sfx').'">'.$article->get('title').'</div>';
			
		}
		else $title = ''; 
		if (empty($article->text))
		$article->text = $title.$intro.$full; 
		
		if (!empty($repvals))
		foreach ($repvals as $key=>$val)
		{
			if ((is_array($val)) || (is_object($val)))
			{
				foreach ($val as $k2=>$nval)
				{
					if (!is_string($nval)) continue; 
					
					if ((stripos($k2, 'price')!==false) && (is_numeric($nval)))
					{
						$nval = (float)$nval; 
						$nval2 = $currencyDisplay->priceDisplay ($nval);
						
						$article->text = str_replace('{'.$k2.'_text}', $nval2, $article->text); 
					}
					
					if (is_string($nval))
					$article->text = str_replace('{'.$k2.'}', $nval, $article->text); 
				}
			}
			else
			{
				if (!is_string($val)) continue; 
				$article->text = str_replace('{'.$key.'}', $val, $article->text); 
			}
		}
		
		$results = $dispatcher->trigger('onPrepareContent', array( &$article, &$params, 0)); 
		$results = $dispatcher->trigger('onContentPrepare', array( 'text', &$article, &$params, 0)); 
		
		return $article->get('text');
		
		
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
			$q = 'select attribute_name from #__virtuemart_custom_plg_vmarticle where id = '.(int)$id; 
			$db->setQuery($q); 
			$res = $db->loadResult(); 
			if (!$this->_checkError($db)) return ''; 
			$res = (string)$res; 
			return $res; 
		}
		$q = 'select m1.id, m1.attribute_name, m1.price from #__virtuemart_custom_plg_vmarticle as m1, #__virtuemart_custom_plg_vmarticle as m2 where m2.id = '.(int)$id.' and m1.virtuemart_product_id = m2.virtuemart_product_id and m1.virtuemart_product_custom_id = m2.virtuemart_product_custom_id and m1.attribute_name = m2.attribute_name'; 
		
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
	/**
	 * Trigger for VM3
	 * @author Max Milbers
	 * @param $product
	 * @param $productCustom
	 * @param $html
	 * @return bool|string
	 
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
	*/

	/**
	 *
	 * vendor order display BE
	 
	function plgVmDisplayInOrderBE(&$item, $productCustom, &$html) {
		if(!empty($productCustom)){
			$item->productCustom = $productCustom;
		}
		if (empty($item->productCustom->custom_element) or $item->productCustom->custom_element != $this->_name) return '';
		$this->plgVmOnViewCart($item,$productCustom,$html); //same render as cart
    }
	*/

	/**
	 *
	 * shopper order display FE
	 
	function plgVmDisplayInOrderFE(&$item, $productCustom, &$html) {
		if(!empty($productCustom)){
			$item->productCustom = $productCustom;
		}
		if (empty($item->productCustom->custom_element) or $item->productCustom->custom_element != $this->_name) return '';
		$this->plgVmOnViewCart($item,$productCustom,$html); //same render as cart
    }
		*/


	/**
	 * Trigger while storing an object using a plugin to create the plugin internal tables in case
	 *
	 * @author Max Milbers
	 */
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
function isMe($id) {
		$db = JFactory::getDBO(); 
		$q = 'select virtuemart_custom_id from #__virtuemart_customs where virtuemart_custom_id = '.$id. ' and custom_element = "vmarticle" limit 0,1'; 
		$db->setQuery($q); 
		$id = $db->loadResult(); 
		if (empty($id)) return false; 
		else return true; 
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

	function plgVmGetTablePluginParams($psType, $name, $id, &$xParams, &$varsToPush){
		return $this->getTablePluginParams($psType, $name, $id, $xParams, $varsToPush);
	}

	function plgVmSetOnTablePluginParamsCustom($name, $id, &$table,$xParams){
		return $this->setOnTablePluginParams($name, $id, $table,$xParams);
	}

	/**
	 * Custom triggers note by Max Milbers
	 */
	function plgVmOnDisplayEdit($virtuemart_custom_id,&$customPlugin){
		return $this->onDisplayEditBECustom($virtuemart_custom_id,$customPlugin);
	}
	
	private function _getDefaultPrice($product_id, $customfield_id)
	{
		$cur_id = $this->_getCurrency(); 
		$q = 'select `price` from #__virtuemart_custom_plg_vmarticle where virtuemart_product_custom_id = '.(int)$customfield_id.' and virtuemart_currency_id = '.(int)$cur_id.' and virtuemart_product_id = '.(int)$product_id.' limit 0,1';
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
	/*
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
	*/


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