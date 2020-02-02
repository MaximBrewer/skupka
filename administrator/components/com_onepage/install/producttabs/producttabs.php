<?php     
/* license: commercial ! */
defined('_JEXEC') or 	die( 'Direct Access to ' . basename( __FILE__ ) . ' is not allowed.' ) ;


class plgSystemProducttabs extends JPlugin {

	public static $inloop; 
	function __construct(& $subject, $config) {
		
		 parent::__construct($subject, $config);
		 
		 JFactory::getLanguage()->load('plg_system_producttabs', __DIR__); 
		 JFactory::getLanguage()->load('plg_system_producttabs', JPATH_ADMINISTRATOR); 
		 
		 $sections = $this->params->get('customordering'); 
		 $datas = self::parseCommas($sections, false); 
		 
		 if (in_array('PLG_SYSTEM_PRODUCTTABS_ORDERING_REVIEWS', $datas)) {
			 $this->params->set('displayreviews', true); 
		 }
		 if (in_array('PLG_SYSTEM_PRODUCTTABS_ORDERING_NOTIFY', $datas)) {
			 $this->params->set('displaynotify', true); 
		 }
		 if (in_array('PLG_SYSTEM_PRODUCTTABS_ORDERING_RECOMMEND', $datas)) {
			 $this->params->set('displayrecommend', true); 
		 }
		 if (in_array('PLG_SYSTEM_PRODUCTTABS_ORDERING_RELATED', $datas)) {
			 $this->params->set('displayrelated', true); 
		 }
		 
		 if (in_array('PLG_SYSTEM_PRODUCTTABS_ORDERING_SOLD', $datas)) {
			 $this->params->set('displaymostsold', true); 
		 }
		 
		 
		 if (in_array('PLG_SYSTEM_PRODUCTTABS_ORDERING_DETAILS', $datas)) {
			 $this->params->set('displaydetails', true); 
		 }
		 
		 if (in_array('PLG_SYSTEM_PRODUCTTABS_ORDERING_CUSTOMFIELDS', $datas)) {
			 $this->params->set('displaycustoms', true); 
		 }
		//PLG_SYSTEM_PRODUCTTABS_ORDERING_CUSTOMFIELDS
		 
		 
	}
	private function getProductCustoms($virtuemart_product_id) {
		
		if (!self::loadVM()) return; 
		$productModel = VmModel::getModel('product');
	    $product = $productModel->getProduct($virtuemart_product_id);
		
		$customfieldsModel = VmModel::getModel ('Customfields');

			if (!empty($product->customfields)) {

				if (!class_exists ('vmCustomPlugin')) {
					require(JPATH_VM_PLUGINS . DS . 'vmcustomplugin.php');
				}
				$customfieldsModel -> displayProductCustomfieldFE ($product, $product->customfields);
			
			
			$isCustomVariant = false;
			if (!empty($product->customfields)) {
				foreach ($product->customfields as $k => $custom) {
					if($custom->field_type == 'C' and $custom->virtuemart_product_id != $virtuemart_product_id){
						$isCustomVariant = $custom;
					}
					if (!empty($custom->layout_pos)) {
						$product->customfieldsSorted[$custom->layout_pos][] = $custom;
					} else {
						$product->customfieldsSorted['normal'][] = $custom;
					}
					unset($product->customfields);
				}

			}
			}
		
		if (!empty($product)) {
		$html = shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$product,'position'=>'OPC'));
		return $html; 
		}
		return ''; 
	}
	
	private function getProductDescription($virtuemart_product_id) {
		if (!self::loadVM()) return; 
		$productModel = VmModel::getModel('product');
	    $product = $productModel->getProduct($virtuemart_product_id);
		
		if (!empty($product)) {
			
		$desc = $product->product_desc; 
		$desc = str_replace('nav nav-tabs', 'removed_tab_classes', $desc); 
		$desc = str_replace('data-toggle="tab"', 'removed_data_classes', $desc); 
		
		return $desc; 
		}
		return ''; 
	}
	
	
  private static function tableExists($table)
  {
   static $cache; 
   
   
   $db = JFactory::getDBO();
   $prefix = $db->getPrefix();
   $table = str_replace('#__', '', $table); 
   $table = str_replace($prefix, '', $table); 
   $table = $db->getPrefix().$table; 
   
   
   if (empty($cache)) $cache = array(); 
   
   if (isset($cache[$table])) return $cache[$table]; 
   
  
  	
   
   $q = "SHOW TABLES LIKE '".$table."'";
	   $db->setQuery($q);
	   $r = $db->loadResult();
	   
	   if (empty($cache)) $cache = array(); 
	   
	   if (!empty($r)) 
	    {
		$cache[$table] = true; 
		return true;
		}
		$cache[$table] = false; 
   return false;
  }
	
	//opc mini: 
	private static function parseCommas($str, $toint=true)
    {
	  if (empty($str)) return array(); 
	  $e = explode(',', $str); 
	  
	  $ea = array(); 
	  if (count($e)>0) {
	    foreach ($e as $c) {
		  $c = trim($c); 
		  if ($c === '0') {
		   $ea[0] = 0; 
		   continue; 
		  }
		  if ($toint) {
		    $c = (int)$c; 
		  }
		  if (empty($c)) continue; 
		  $ea[$c] = $c; 
		}
	  }
	  else
	  {
		  $c = trim( $str ); 
		  if ($c === '0') {
		   $ea[0] = 0; 
		  }
		  if ($toint) {
			$c = (int)$c;
		  }		  
		  if (!empty($c)) $ea[$c] = $c; 
	  }
	  return $ea; 
 }
	
	private function _init() {
	   
		 $action = 'vm.product'; 
		 $assetName = 'com_virtuemart.product'; 
		 $z = JFactory::getUser()->authorise($action, $assetName);
		 return $z; 
		
	}
	
	
	private function createTable() {
		$db = JFactory::getDBO(); 
	  $q = "
CREATE TABLE IF NOT EXISTS `#__producttabs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `virtuemart_product_id` int(11) NOT NULL,
  `tabname` text NOT NULL,
  `tabdesc` longtext NOT NULL,
  `tabcontent` longtext NOT NULL,
  `extra1` varchar(10) NOT NULL,
  `extra2` int(11) NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `params` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`virtuemart_product_id`),
  KEY `extra1` (`extra1`),
  KEY `extra2` (`extra2`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;"; 
      $db->setQuery($q); 
      $db->query(); 

	  try { 
	   $q = 'ALTER TABLE `#__producttabs` CHANGE `extra2` `extra2` INT(11) NOT NULL';
	   $db->setQuery($q); 
	   $db->query(); 
	  }
	  catch (Exception $e) {
		  
	  }
		 
	  try { 
	  $q = 'ALTER TABLE `#__producttabs` CHANGE `extra1` `extra1` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL'; 
	  $db->setQuery($q); 
	  $db->query(); 
	  }
	  catch (Exception $e) {
		  
	  }
	  
	 $q = 'select * from #__producttabs where 1=1 limit 1'; 
	 $db->setQuery($q); 
	 $res = $db->loadAssoc(); 
	 if (!empty($res)) {
		 if (!isset($res['ordering'])) {
			 $q = 'ALTER TABLE `#__producttabs` ADD `ordering` INT NOT NULL DEFAULT \'0\' AFTER `extra2`'; 
			 try { 
			  $db->setQuery($q); 
			  $db->query(); 
			 }
			 catch (Exception $e) {
				
			 }
		 }
		 if (!isset($res['params'])) {
			  $q = 'ALTER TABLE `#__producttabs` ADD `params` TEXT NOT NULL DEFAULT \'\' AFTER `ordering`'; 
			  try { 
			   $db->setQuery($q); 
			   $db->query(); 
			  }
			  catch (Exception $e) {
				 
			  }
			 
		 }
	 }
	 else {
		 static $done; 
		 if (empty($done)) {

		  $q = 'drop table #__producttabs'; 
		  $db->setQuery($q); 
		  $db->query(); 
		  $done = true; 
		  $this->createTable(); 
		 }
	 }
	 
	 $lang = $this->getDefaultLang(); 
	 $q = 'update #__producttabs set `extra1` = \''.$db->escape($lang).'\' where `extra1` = \'\''; 
	 $db->setQuery($q); 
	 $db->query(); 
	  
	  
	}
	
	public function getRecommendHtml($virtuemart_product_id) {
		static $recursionRev; 
		if (!empty($recursionRev)) return; 
		$recursionRev = true; 
		$vars = array('virtuemart_product_id'=>$virtuemart_product_id, 
		'task'=>'recommend'); 
		$task = JRequest::getVar('task'); 
		JRequest::setVar('task', 'recommmend'); 
		$html = self::_getVMView('recommend', $vars, 'productdetails', 'form', 'html'); 
		
		JRequest::setVar('task', $task); 
		$recursionRev = false; 
		return $html;

	}
	public function getReviewHtml($virtuemart_product_id) {
		
		static $recursionRev; 
		if (!empty($recursionRev)) return; 
		$recursionRev = true; 
		$vars = array('virtuemart_product_id'=>$virtuemart_product_id); 
		$html = self::_getVMView('productdetails', $vars, 'productdetails', 'default_reviews', 'html'); 
		
		$recursionRev = false; 
		return $html;
	}
	
	
	
	public function getNotifyHtml($virtuemart_product_id) {
		static $recursion; 
		if (!empty($recursion)) return; 
		$recursion = true; 
		$vars = array('virtuemart_product_id'=>$virtuemart_product_id); 
		$html = self::_getVMView('productdetails', $vars, 'productdetails', 'notify', 'html'); 
		
		
		$recursion = false; 
		return $html;
	}
	
	//getRelatedHtml
	public function getRelatedHtml($virtuemart_product_id) {
		static $recursion; 
		if (!empty($recursion)) return; 
		$recursion = true; 
		$productModel = VmModel::getModel('product');
	    $product = $productModel->getProduct($virtuemart_product_id);
		$html = shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$product,'position'=>'related_products','class'=> 'product-related-products','customTitle' => true ));
		if (empty($html)) {
			$html = shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$product,'position'=>'opc_related_products','class'=> 'product-related-products','customTitle' => true ));
		}
		$recursion = false; 
		return $html;
	}
	
	public function getMostSold($virtuemart_product_id) {
		if (!self::loadVM()) return; 
$q2 = "select p.virtuemart_product_id, i2.product_quantity from #__virtuemart_order_items as i ";
$q2 .= "left outer join #__virtuemart_order_items as i2 on (i.virtuemart_order_id = i2.virtuemart_order_id) ";
$q2 .= "left outer join #__virtuemart_products as p on (i.virtuemart_product_id = p.virtuemart_product_id) ";
$q2 .= " where i2.virtuemart_product_id = ".(int)$virtuemart_product_id." and i.virtuemart_product_id <> i2.virtuemart_product_id and p.published=1 and p.virtuemart_product_id <> ".(int)$virtuemart_product_id." group by p.virtuemart_product_id order by i2.product_quantity";
$q2 .= " limit 0,9 ";
$db = JFactory::getDBO(); 
$db->setQuery($q2); 
$ids = $db->loadAssocList(); 
	
	
	
		$html = ''; 
		if (!empty($ids))  {
			
			$customfield = new stdClass(); 
			$q = 'select custom_params from #__virtuemart_customs where field_type="R" limit 0,1'; 
			$db->setQuery($q); 
			$params = $db->loadResult(); 
			if (!empty($params)) {
			$params = explode('|', $params); 
			foreach ($params as $row) {
				$item = explode('=', $row);
				$key = $item[0]; 
				if (!empty($key)) {
				$val = @json_decode($item[1]); 
				if (!empty($val)) {
					$customfield->{$key} = $val; 
				}
				}
			}
			}
			else {
				//wPrice="1"|wImage="1"|wDescr="1"|
				$customfield->wPrice = 1; 
				$customfield->wImage = 1; 
				$customfield->wDescr = 1; 
			}
		$html .= '<div class="product-related-products">'; 
		foreach ($ids as $row) {
					$id = (int)$row['virtuemart_product_id']; 
					$pModel = VmModel::getModel('product');
					$product = $pModel->getProduct((int)$id,TRUE,true,TRUE,1);

					if(!$product) continue;

					$thumb = '';
					 $width = VmConfig::get('img_width',90);
					 $height = VmConfig::get('img_height',90);

					
						if (!empty($product->virtuemart_media_id[0])) {
							$thumb = VirtueMartModelCustomfields::displayCustomMedia ($product->virtuemart_media_id[0],'product',$width,$height).' ';
						} else {
							$thumb = VirtueMartModelCustomfields::displayCustomMedia (0,'product',$width,$height).' ';
						}
					
					
					$html .= '<div class="product-field product-field-type-R">'; 
					$html .= shopFunctionsF::renderVmSubLayout('related',array('customfield'=>$customfield,'related'=>$product, 'thumb'=>$thumb));
					
					$html .= '</div>'; 

		}
		$html .= '</div>';
		}
		return $html;
	}
	// original code from shopfunctionsF::renderMail
	private static function _getVMView($viewName, $vars=array(),$controllerName = NULL, $layout='default', $format='html')
	{
		
		
		if (!self::loadVM()) return; 
		$originallayout = JRequest::getVar( 'layout' );
		if(!class_exists('VirtueMartControllerVirtuemart')) require(JPATH_VM_SITE.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'virtuemart.php');
		// 		$format = (VmConfig::get('order_html_email',1)) ? 'html' : 'raw';
		
		// calling this resets the layout
		$controller = new VirtueMartControllerVirtuemart();
		JRequest::setVar( 'layout', $layout );
		

		//Todo, do we need that? refering to http://forum.virtuemart.net/index.php?topic=96318.msg317277#msg317277
		
		if (method_exists($controller, 'addViewPath')) { 
			$controller->addViewPath(JPATH_VM_SITE.DIRECTORY_SEPARATOR.'views');
		}
		else
		if (method_exists($controller, 'addIncludePath')) {
			$controller->addIncludePath(JPATH_VM_SITE.DIRECTORY_SEPARATOR.'views');
		}
		
		
		$view = $controller->getView($viewName, $format);
		
		
		$view->assignRef('layout', $layout); 
		$view->assignRef('format', $format); 
		
		$view->setLayout($layout); 
		
		if (!$controllerName) $controllerName = $viewName;
		$controllerClassName = 'VirtueMartController'.ucfirst ($controllerName) ;
		if (!class_exists($controllerClassName)) require(JPATH_VM_SITE.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.$controllerName.'.php');

		//Todo, do we need that? refering to http://forum.virtuemart.net/index.php?topic=96318.msg317277#msg317277
		if (method_exists($view, 'addTemplatePath')) {
			$view->addTemplatePath(JPATH_VM_SITE.'/views/'.$viewName.'/tmpl'); 
		} else { 
			if (method_exists($view, 'addIncludePath')) 
			{
				$view->addIncludePath( JPATH_VM_SITE.'/views/'.$viewName.'/tmpl' );
			}
		}
		
		$app = JFactory::getApplication(); 
		$template = $app->getTemplate(); 
		
		$tp = JPATH_SITE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.$viewName; 
		
		if (method_exists($view, 'addTemplatePath')) {
			$view->addTemplatePath( JPATH_VM_SITE.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.$viewName.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR );
		}
		else
		{
			if (method_exists($view, 'addIncludePath')) {
				$view->addIncludePath( JPATH_VM_SITE.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.$viewName.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR );
			}
		}
		
		
		if (file_exists($tp))
		{
			
			
			if (method_exists($view, 'addTemplatePath')) { 
				$view->addTemplatePath(JPATH_SITE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.$viewName.DIRECTORY_SEPARATOR); 
				
				
				
			}
			else
			{
				if (method_exists($view, 'addIncludePath')) {
					$view->addIncludePath( JPATH_SITE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.$viewName.DIRECTORY_SEPARATOR );
				
					
				}
			}
		}

		$vmtemplate = VmConfig::get('vmtemplate','default');
		if($vmtemplate=='default'){
			if(JVM_VERSION >= 2){
				$q = 'SELECT `template` FROM `#__template_styles` WHERE `client_id`="0" AND `home`="1"';
			} else {
				$q = 'SELECT `template` FROM `#__templates_menu` WHERE `client_id`="0" AND `menuid`="0"';
			}
			$db = JFactory::getDbo();
			$db->setQuery($q);
			$template = $db->loadResult();
		} else {
			$template = $vmtemplate;
		}

		if($template){
			if (method_exists($view, 'addTemplatePath')) {
				$view->addTemplatePath(JPATH_SITE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.$viewName);
			}
			else
			{
				if (method_exists($view, 'addIncludePath')) {
					$view->addIncludePath(JPATH_SITE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.$viewName);
				}
			}
		} else {
			if(isset($db)){
				$err = $db->getErrorMsg() ;
			} else {
				$err = 'The selected vmtemplate is not existing';
			}
			
		}

		
		
	
		
		foreach ($vars as $key => $val) {
			$view->$key = $val;
		}
		ob_start(); 
		$html = $view->display();
		$html2 = ob_get_clean(); 
		
		JRequest::setVar( 'layout', $originallayout );
		return $html.$html2; 
		
		
		
	}
	
	public function getCurrentLang() {
		if (!self::loadVM()) return; 
		$lang = JFactory::getLanguage()->getTag(); 
		$default_lang = $this->getDefaultLang(); 
		
		
		
		
		//backend override:
		 $lg = JRequest::getVar('vmlang', $lang); 
		 if (empty($lg)) $lg = $lang; 
		 
		 $langs = VmConfig::get('active_languages',array());
         
		 if (!in_array($lg, $langs) ) {
				$lg = $default_lang; 
				return $default_lang; 
		 }
		
		$lg2 = $lg;
		
		
		if ($lg2 === $default_lang) $lg = $default_lang; 
		//if ($lang === $default_lang) return $default_lang; 
		return $lg; 
	}
	public function getDefaultLang() {
		$cache; 
		if (!empty($cache)) return $cache; 
		
		$q = 'select `params` from #__extensions where `type` = "component" and `element` = "com_languages"'; 
		$db = JFactory::getDBO(); 
		$db->setQuery($q); 
		$json = $db->loadResult(); 
		$data = json_decode($json, true); 
		if (isset($data['site'])) {
			$retlang = $data['site']; 
			$cache = $retlang; 
			if (!empty($retlang)) return $retlang; 
		}
		
		if (!self::loadVM()) return; 
		if (!isset(VmConfig::$jDefLang)) return JFactory::getLanguage()->getTag();
		
		$cache = VmConfig::$jDefLang;
		return VmConfig::$jDefLang;
	}
	
	static $_done; 
	// accepts either $product object or the ID itself
	public function plgGetProductTabs($virtuemart_product_id, &$html) {
		
		
		  static $recursionX; 
		  if (!empty($recursionX)) return ''; 
		  $recursionX = true; 
		  
		   if (is_object($virtuemart_product_id) && (isset($virtuemart_product_id->virtuemart_product_id))) $virtuemart_product_id = $virtuemart_product_id->virtuemart_product_id; 
		   $original_product_id = $virtuemart_product_id;
		   
		   
			$user_id = JFactory::getUser()->get('id'); 
		   
		   $lang = JFactory::getLanguage()->getTag(); 
		   
	       $data = $this->getData($virtuemart_product_id, $lang); 
			
			$first = reset($data); 
		
			if ($first['tabname'] === 'disablethis') return ''; 
			//displayrelated
		   
		   $displayrelated = $this->params->get('displayrelated'); 
		   if (!empty($displayrelated)) {
			  
			   $related = array(); 
			   $related['tabdesc'] = JText::_('COM_VIRTUEMART_RELATED_PRODUCTS_HEADING'); 
			   $related['tabcontent'] = $this->getRelatedHtml($virtuemart_product_id); 
			   if (empty($related['tabcontent'])) {
				   $related['tabcontent'] = $this->getRelatedHtml($original_product_id); 
			   }
			    
			   $related['tabname'] = JText::_('COM_VIRTUEMART_RELATED_PRODUCTS'); 
			   $related['id'] = 'related'; 
				if (!empty($related['tabcontent'])) {
			     $data['PLG_SYSTEM_PRODUCTTABS_ORDERING_RELATED'] = $related; 
			    }
			   
		   }
		   
		    $displaysold = $this->params->get('displaymostsold'); 
			
		   if (!empty($displaysold)) {
			   JFactory::getLanguage()->load('plg_system_producttabs', __DIR__); 
			   JFactory::getLanguage()->load('plg_system_producttabs', JPATH_ADMINISTRATOR); 
			 
			   $sold = array(); 
			   $sold['tabdesc'] = JText::_('PLG_SYSTEM_PRODUCTTABS_SOLD_DESC'); 
			   $sold['tabcontent'] = $this->getMostSold($virtuemart_product_id); 
			   if (empty($sold['tabcontent'])) {
				   $sold['tabcontent'] = $this->getMostSold($original_product_id); 
			   }
			   $sold['tabname'] = JText::_('PLG_SYSTEM_PRODUCTTABS_SOLD'); 
			   $sold['id'] = 'sold'; 
			   if (!empty($sold['tabcontent'])) {
			   $data['PLG_SYSTEM_PRODUCTTABS_ORDERING_SOLD'] = $sold; 
			   }
			   
		   }
		   
		   
		   $displaydetails = $this->params->get('displaydetails'); 
			
		   if (!empty($displaydetails)) {
			   JFactory::getLanguage()->load('plg_system_producttabs', __DIR__); 
			   JFactory::getLanguage()->load('plg_system_producttabs', JPATH_ADMINISTRATOR); 
			 
			   $desctab = array(); 
			   $desctab['tabdesc'] = JText::_('COM_VIRTUEMART_PRODUCT_DESC'); 
			   $desctab['tabcontent'] = $this->getProductDescription($virtuemart_product_id); 
			   
			   if (empty($desctab['tabcontent'])) {
				   $desctab['tabcontent'] = $this->getProductDescription($original_product_id); 
			   }
			   
			   $desctab['tabname'] = JText::_('COM_VIRTUEMART_PRODUCT_DESC_TITLE'); 
			   $desctab['id'] = 'productdescription'; 
			   if (!empty($desctab['tabcontent'])) {
			   $data['PLG_SYSTEM_PRODUCTTABS_ORDERING_DETAILS'] = $desctab; 
			   }
			   
		   }
		   
		   $displaycustoms = $this->params->get('displaycustoms'); 
		   if (!empty($displaycustoms)) {
			   JFactory::getLanguage()->load('plg_system_producttabs', __DIR__); 
			   JFactory::getLanguage()->load('plg_system_producttabs', JPATH_ADMINISTRATOR); 
			 
			   $customs = array(); 
			   $customs['tabdesc'] = JText::_('PLG_SYSTEM_PRODUCTTABS_CUSTOMDESC'); 
			   $customs['tabcontent'] = $this->getProductCustoms($virtuemart_product_id); 
			   
			   if (empty($customs['tabcontent'])) {
				   $customs['tabcontent'] = $this->getProductCustoms($original_product_id); 
			   }
			   
			   $customs['tabname'] = JText::_('PLG_SYSTEM_PRODUCTTABS_CUSTOMS'); 
			   $customs['id'] = 'customfieldsid'; 
			   if (!empty($customs['tabcontent'])) {
			   $data['PLG_SYSTEM_PRODUCTTABS_ORDERING_CUSTOMFIELDS'] = $customs; 
			   }
			   
		   }
		   
		   
		   $displayreviews = $this->params->get('displayreviews'); 
		   $showReviewFor = VmConfig::get('showReviewFor', ''); 
		   if ($showReviewFor === 'none') $displayreviews = false; 
		   if ($showReviewFor === 'registered') {
			  
			   if ($user_id < 1) $displayreviews = false; 
		   }
		   if (!empty($displayreviews)) {
			   
			   $reviews = array(); 
			   $reviews['tabdesc'] = ''; 
			   $reviews['tabcontent'] = $this->getReviewHtml($virtuemart_product_id); 
			   
			    if (empty($reviews['tabcontent'])) {
				   $reviews['tabcontent'] = $this->getReviewHtml($original_product_id); 
			   }
			   
			   $reviews['tabname'] = JText::_('COM_VIRTUEMART_REVIEWS'); 
			   $reviews['id'] = 'reviews'; 
			   if (!empty($reviews['tabcontent'])) {
			     $data['PLG_SYSTEM_PRODUCTTABS_ORDERING_REVIEWS'] = $reviews; 
			   }
		   }
		   $displaynotify = $this->params->get('displaynotify'); 
		   if (!empty($displaynotify)) {
			   
			   $notify = array(); 
			   $notify['tabdesc'] = ''; 
			   $notify['tabcontent'] = $this->getNotifyHtml($virtuemart_product_id); 
			   
			    if (empty($notify['tabcontent'])) {
				   $notify['tabcontent'] = $this->getNotifyHtml($original_product_id); 
			   }
			   
			   $notify['tabname'] = JText::_('COM_VIRTUEMART_CART_NOTIFY'); 
			   $notify['id'] = 'notify'; 
			   if (!empty($notify['tabcontent'])) {
			     $data['PLG_SYSTEM_PRODUCTTABS_ORDERING_NOTIFY'] = $notify; 
			   }
			   
		   }
		   
		     $displayrecommend = $this->params->get('displayrecommend'); 
			 $show_emailfriend = VmConfig::get('show_emailfriend'); 
			 if (empty($show_emailfriend)) $displayrecommend = false; 
			 $recommend_unauth = VmConfig::get('recommend_unauth', false); 
		   $user = JFactory::getUser(); 
		   if (($user->guest) && (empty($recommend_unauth))) $displayrecommend = false; 
		   
		   
		   
		   if (!empty($displayrecommend)) {
			   
			   $displayrecommend = array(); 
			   $displayrecommend['tabdesc'] = ''; 
			   $displayrecommend['tabcontent'] = $this->getRecommendHtml($virtuemart_product_id); 
			   
			     if (empty($displayrecommend['tabcontent'])) {
				   $displayrecommend['tabcontent'] = $this->getRecommendHtml($original_product_id); 
			   }
			   
			   $displayrecommend['tabname'] = JText::_('COM_VIRTUEMART_PRODUCT_RECOMMEND'); 
			   $displayrecommend['id'] = 'displayrecommend'; 
			   if (!empty($displayrecommend['tabcontent'])) {
			     $data['PLG_SYSTEM_PRODUCTTABS_ORDERING_RECOMMEND'] = $displayrecommend;
			   }			   
			   
		   }
		   
		  
		   if (empty($data)) return; 
		   
		   foreach ($data as $k=>$v) {
			  if (empty($v['tabname'])) unset($data[$k]); 
			  if (empty($v['id'])) unset($data[$k]); 
			}
		   if (empty($data)) return; 
		   
		   
			
	       $data = $this->reorderData($data); 
	   
		   $first = reset($data);
		   $first_key = key($data);
		   $data[$first_key]['active'] = true; 
	   
	   
	       $framework = $this->params->get('framework'); 
			if (empty($framework)) {
			   $layout = 'producttabs_fe'; 
			}
			else
			{
				$layout = 'producttabs_fe_'.$framework; 
			}
			
			$root = Juri::root(); 
		    if (substr($root, -1) !== '/') $root .= '/'; 
			
			$path = self::getIncludePath($layout); 
			ob_start(); 
			if (!empty($path)) include($path); 
			$htmlZX = ob_get_clean(); 
			
			ob_start(); 
			$jsf = self::getIncludePath($layout.'.includes'); 
			if (!empty($jsf)) include($jsf); 
			$js = ob_get_clean(); 
			
			$html .= $htmlZX.$js; 
			
			/*
			if (!class_exists('OPCloadmodule'))
			{
			require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'loadmodule.php'); 
			}
			*/
			self::$inloop = true; 
			//OPCloadModule::onContentPrepare('text', $html); 
			JPluginHelper::importPlugin('content'); 
			$dispatcher = JDispatcher::getInstance(); 
			//$article = JTable::getInstance("content");
			$article = new JRegistry(''); 
			$article->text = $html; 
			$params = new JRegistry(''); 
			$results = $dispatcher->trigger('onContentPrepare', array( 'text', &$article, &$params, 0)); 
			
			if (!empty($article->text)) {
				$html = $article->text; 
			}
			self::$inloop = false; 
			
			if (empty(self::$_done)) self::$_done = array(); 
			self::$_done[$virtuemart_product_id] = $virtuemart_product_id; 
			
		    $recursionX = false; 
	   
	}
	
	public function onContentPrepare($context, &$article, &$params, $page = 0)
	{
		if (!JFactory::getApplication()->isSite()) return; 
		//recursive protection: 
		if (!empty(self::$inloop)) return; 
		
		$class = get_class($article); 
		
		
		if (($class == 'TableProducts') || (($class == 'stdClass') && (isset($article->virtuemart_product_id)))) {
		   //self::toObject($article); 
		   if (isset($article->virtuemart_product_id)) {
		    $virtuemart_product_id = (int)$article->virtuemart_product_id; 
			
			if (!empty(self::$_done[$virtuemart_product_id])) return; 
			
			$html = ''; 
			
			
			$rtype = $this->params->get('rederingtype', false); 
			
			// default {tabs} or append
			if (empty($rtype)) {
			if (stripos($article->text, '{tabs}')!==false) {
			  $this->plgGetProductTabs($virtuemart_product_id, $html); 
			  $article->text = str_replace('{tabs}', $html, $article->text); 
			}
			else
			{
		      $article->text .= $html; 
			}
			}
			else {
				$rtype = (int)$rtype; 
				switch ($rtype) {
				  case 2: 
				    $this->plgGetProductTabs($virtuemart_product_id, $html); 
				    $article->text = $html.$article->text; 
					break; 
				  case 3: 
				    return; 
				  case 4: 
				     $this->plgGetProductTabs($virtuemart_product_id, $html); 
				     $article->text = str_replace('{tabs}', $html, $article->text); 
					 break; 
				  default: 
				   return; 
				}
				
			}
			
			
			
			
		   
		}
	  }
	}
	
	function checkCompat() {
		if (!self::loadVM()) return; 
	if (file_exists(JPATH_VM_ADMINISTRATOR.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'adminui.php')) {
	$x = file_get_contents(JPATH_VM_ADMINISTRATOR.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'adminui.php'); 
	
	
	if (strpos($x, 'plgVmBuildTabs')===false) {
		
		$newCode = '
		$dispatcher = JDispatcher::getInstance();
		$returnValues = $dispatcher->trigger(\'plgVmBuildTabs\', array(&$view, &$load_template));
		'; 
		$search = 'foreach ( $load_template as $tab_content => $tab_title ) {'; 
	    $count = 0;
		$x = str_replace($search, $newCode.$search, $x, $count); 
		if ($count > 0) {
			jimport( 'joomla.filesystem.file' );
		if (JFile::copy(JPATH_VM_ADMINISTRATOR.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'adminui.php', JPATH_VM_ADMINISTRATOR.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'adminui.opc_bck.php')!==false) {
		 JFile::write(JPATH_VM_ADMINISTRATOR.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'adminui.php', $x); 
		 JFactory::getApplication()->enqueueMessage(JText::_('COM_ONEPAGE_ADDED_SUPPORT_FOR_TABS')); 
			}
		}
		}
	}
	
	$x = VmConfig::get('enable_content_plugin', false); 
	if (empty($x)) {
	  JFactory::getLanguage()->load('com_virtuemart', JPATH_ADMINISTRATOR); 
	  JFactory::getApplication()->enqueueMessage(JText::_('PLG_SYSTEM_PRODUCTTABS_ERROR').': <b>'.JText::_('COM_VIRTUEMART_ADMIN_CFG_ENABLE_CONTENT_PLUGIN').'</b>');  
	}
	
	}
	
	function onExtensionAfterSave($tes2, $test) {
	  $this->createTable(); 
	  $this->checkCompat(); 
	}
	private static function getIncludePath($layout) {
	   $paths = self::getIncludePaths($layout);
	   if (empty($paths)) return ''; 
	   return $paths[0]; 
	}
	private static function getIncludePaths($layout='') {
	   $ret = array(); 
	   $tp = JPATH_SITE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.JFactory::getApplication()->getTemplate().DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'plg_system_producttabs'.DIRECTORY_SEPARATOR; 
	  
	   if (file_exists($tp)) {
	      if (!empty($layout)) {
		     if (file_exists($tp.$layout.'.php')) $ret = array(0 => $tp.DIRECTORY_SEPARATOR.$layout.'.php'); 
		  }
		  else
		  {
			  $ret[] = $tp; 
		  }
	   }
	   if (empty($layout)) {
	     $ret[] = __DIR__.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR;
	   }
	   else
	   {
		   if (file_exists(__DIR__.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR.$layout.'.php')) {
		      $ret[] = __DIR__.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR.$layout.'.php'; 
		   }
	   }
	   
	   return $ret; 
	}
	
	private static function loadVM() {
		
		static $run; 
		if (!empty($run)) return; 
		$run = true; 
		
		/* Require the config */
		
		if (!file_exists(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'shopfunctionsf.php')) return false; 
	
		if (!class_exists( 'VmConfig' )) require(JPATH_ROOT.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');
		VmConfig::loadConfig();
		if(!class_exists('VmImage')) require(JPATH_ROOT.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'image.php'); 
		if(!class_exists('shopFunctionsF'))require(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'shopfunctionsf.php'); 
		
		if (class_exists('vmLanguage')) {
			if (method_exists('vmLanguage', 'loadJLang')) {
				vmLanguage::loadJLang('com_virtuemart', true);
			}
		}
		return true; 
		
	}
	
	public function plgVmBuildTabs(&$view, &$tabs)
	{
		
		if (!$this->_init()) return; 
		if (!self::tableExists('#__producttabs')) {
			 $this->createTable(); 
			 $this->checkCompat(); 
		}
		JFactory::getLanguage()->load('plg_system_producttabs', dirname(__FILE__).DIRECTORY_SEPARATOR); 
		JFactory::getLanguage()->load('plg_system_producttabs', JPATH_ADMINISTRATOR); 
		$class = get_class($view); 
		switch ($class)
		{
			case 'VirtuemartViewProduct': 
			
			  $virtuemart_product_id = $vmid = JRequest::getVar('virtuemart_product_id'); 
			  
			   
			  // unknown category ID: 
			  if (empty($virtuemart_product_id)) return; 
			  if (is_array($virtuemart_product_id)) $virtuemart_product_id = reset($virtuemart_product_id); 
			  $virtuemart_product_id = (int)$virtuemart_product_id; 


			 
			
			     $old_product_id = $virtuemart_product_id; 
				 $lg = $this->getCurrentLang(); 
				 
				 //$data = $this->getData($virtuemart_product_id, $lg);
				 $data = array(); 
				 $lang = $lg; 
				 $default_lang = $this->getDefaultLang(); 
				 $this->dataFromLangToLang($old_product_id, $data, $default_lang, $lang); 
				 
				 
				 $first = reset($data); 
				 if ($first['tabname'] === 'disablethis') {
					  
				 }
				 
				 if ((!empty($data)) && (!empty($data[0]['id']))) {
				 if ($old_product_id !== $virtuemart_product_id) {
					 $data['is_derived'] = true; 
					 
				 }
				 else {
					 $data['is_derived'] = false; 
				 }
				 }
				 $paths = self::getIncludePaths(); 
				 
					  $tabs['producttabs'] = JTExt::_('PLG_SYSTEM_PRODUCTTABS'); 
				foreach ($paths as $p) {
				  $view->addTemplatePath( $p );
				}
					  //$view->addTemplatePath( __DIR__.DIRECTORY_SEPARATOR.'tabs'.DIRECTORY_SEPARATOR.'product'.DIRECTORY_SEPARATOR );
					
					$current_lang = $this->getCurrentLang(); 
					$view->assignRef('current_lang', $current_lang); 
					$view->assignRef('default_lang', $default_lang); 
					$view->assignRef('tabdata', $data); 
				    //$view->assignRef('opc_forms', $forms); 
					
				 
			  
			  
			  
			  break; 
			  
		}
		
			  
	}
	
	public function getData(&$virtuemart_product_id, $lang='') {
	   
	   if (empty($lang)) $lang = $this->getDefaultLang(); 
	   
	   $db = JFactory::getDBO(); 
	   $q = 'select * from #__producttabs where virtuemart_product_id = '.(int)$virtuemart_product_id.' and (extra1 = \''.$db->escape($lang).'\') order by ordering asc'; 
	   try { 
	   $db->setQuery($q); 
	   $res = $db->loadAssocList(); 
	   }
	   catch (Exception $e) {
		   $res = null; 
	   }
	   
	   //echo $q."<br />\n"; 
	   
	   
	   /*
	    $first = reset($res); 
		if ($first['tabname'] === 'disablethis') {
					  return $this->emptyTab($virtuemart_product_id, $res); 
				 }
	   */
	   
	   if (empty($res)) {
		  
		  if (empty($isbe)) 
		  $q = 'select product_parent_id from #__virtuemart_products where virtuemart_product_id = '.(int)$virtuemart_product_id; 
		  $db->setQuery($q); 
		  $parent_id = (int)$db->loadResult(); 
		  if (!empty($parent_id)) {
			  $virtuemart_product_id = $parent_id; 
			  return $this->getData($virtuemart_product_id, $lang); 
		  }	 
		  
		  return $this->emptyTab($virtuemart_product_id); 
		  
	     
	   }
	   
	   $res = (array)$res; 
	   //$res[0]['active'] = true; 
	   
	   return $res; 
	   
	}
	
	public function emptyTab($virtuemart_product_id, $res=array()) {
	
		  $res[0] = array(); 
		  $res[0]['id'] = 0; 
		  $res[0]['virtuemart_product_id'] = (int)$virtuemart_product_id; 
		  if (!isset($res[0]['tabname']))
		  $res[0]['tabname'] = ''; 
		  $res[0]['tabdesc'] = ''; 
		  $res[0]['tabcontent'] = ''; 
		  $res[0]['extra1'] = ''; 
		  $res[0]['extra2'] = ''; 
		  $res[0]['ordering'] = ''; 
		  $res[0]['params'] = ''; 
		  
		  
		  return $res; 
	}
	
	public function onAfterRoute() {
	     
		$this->vmLangIntersector(); 
		
		if (!$this->_init()) {
		    
		   return; 
		}
		if (!self::loadVM()) return; 
		$x = JRequest::getVar('sys_store_tab_content', false); 
		if ($x === false) return; 
		
	
		
		$cid = JRequest::getVar('virtuemart_product_id'); 
		if (is_array($cid)) $cid = reset($cid); 
		$cid = (int)$cid; 
		if (empty($cid)) return;
		
		$lg = $this->getCurrentLang(); 
		
		
		
		if ((!empty($x)) && ($x === '1')) {
			
			if (!self::tableExists('#__producttabs')) {
			 $this->createTable(); 
			 $this->checkCompat(); 
			}
			
			//legacy: 
			$db = JFactory::getDBO(); 
			$lang = $this->getDefaultLang(); 
			$q = 'update #__producttabs set `extra1` = \''.$db->escape($lang).'\' where `extra1` = \'\''; 
			$db->setQuery($q); 
			$db->query(); 
			
			
			
			$post = JRequest::get('post'); 
			$remove = JRequest::getInt('sys_remove_tab', false); 
		
			foreach ($post as $k=>$v) {
				
			   if (strpos($k, 'tabname_')===0) {
				  
			     $id = str_replace('tabname_', '', $k); 
				 $input = JFactory::getApplication()->input; 
				 $name = JRequest::getVar('tabname_'.$id, ''); 
				 $desc = $input->get('tabdesc_'.$id, '', 'RAW'); 
				 $ordering = (int)$input->get('tabordering_'.$id, '', 'RAW'); 
				 $cont = $input->get('tabcontent_'.$id, '', 'RAW'); 
				 
				 
				 
				 
				 
				
				 if (empty($id) && empty($name)) continue; 
				 $this->insertUpdate($cid, $id, $name, $desc, $cont, $lg, $ordering); 
				 
				  
				 
			   }
			   
			}
			
			if (!empty($post['sys_add_new_tab'])) { 
			   $id = 0; 
			   $this->insertUpdate($cid, $id, '', '', ''); 
			}
			
			if (!empty($remove)) {
			
			  $this->removeTab($cid, $remove); 
			}
			
			$this->fixPrev(); 
		}
		else
		if ((!empty($x)) && ($x === 'copyparent')) {
			
			$post = JRequest::get('post'); 
			foreach ($post as $k=>$v) {
				
			   if (strpos($k, 'tabname_')===0) {
				  
			      $id = str_replace('tabname_', '', $k); 
				  $id_orig = $id; 
				 $input = JFactory::getApplication()->input; 
				 $name = JRequest::getVar('tabname_'.$id, ''); 
				 $desc = $input->get('tabdesc_'.$id, '', 'RAW'); 
				 $ordering = (int)$input->get('tabordering_'.$id, '', 'RAW'); 
				 $cont = $input->get('tabcontent_'.$id, '', 'RAW'); 
				 
				 $default_lang = $this->getDefaultLang(); 
				 if (empty($id) && empty($name)) continue; 
				 //insert new, since this is a COPY !
				 $id = 0; 
				 $this->insertUpdate($cid, $id, $name, $desc, $cont, $default_lang, $ordering); 
				 
				 if ((!empty($id)) && ($remove === $id_orig)) {
					 $toremove = $id; 
				 }
				
				 
			   }
			   
			}
			
			
			if (!empty($post['sys_add_new_tab'])) { 
			   $id = 0;
			   $this->insertUpdate($cid, $id, '', '', ''); 
			}
			
			if (!empty($toremove)) {
			
			  $this->removeTab($cid, $remove); 
			}
			
		}
		else
		if ((!empty($x)) && ($x === 'disablethis')) { 
	       
			   $this->removeTab($cid); 
			   $id = 0; 
			   $this->insertUpdate($cid, $id, 'disablethis', '', ''); 
		   
		}
		else
		if ((!empty($x)) && ($x === 'removeall')) { 
	       
			   $this->removeTab($cid); 
			
		}
		
		
		
		
		
		
	}
	
	private function reorderData($data) {
		 $sections = $this->params->get('customordering'); 
		 if (empty($sections)) return $data; 
		 $datas = self::parseCommas($sections, false); 
		
		
		$newdata = array(); 
		$art = array('PLG_SYSTEM_PRODUCTTABS_ORDERING_TAB1','PLG_SYSTEM_PRODUCTTABS_ORDERING_TAB2','PLG_SYSTEM_PRODUCTTABS_ORDERING_TAB3','PLG_SYSTEM_PRODUCTTABS_ORDERING_TAB4','PLG_SYSTEM_PRODUCTTABS_ORDERING_TAB5','PLG_SYSTEM_PRODUCTTABS_ORDERING_TAB6','PLG_SYSTEM_PRODUCTTABS_ORDERING_TAB7','PLG_SYSTEM_PRODUCTTABS_ORDERING_TAB_OTHER');
		foreach ($datas as $type) {
			foreach ($data as $k=>$tab) {
				if ($k === $type) {
					$newdata[$k] = $tab; 
					unset($data[$k]); 
					break; 
				}
				else 
				if (in_array($type, $art)) {
					if (is_numeric($k)) {
						$newdata[] = $tab; 
						unset($data[$k]); 
						break; 
					}
				}
			}
			
		}
		$data = $newdata; 
		
		return $data; 
	}
	
	private function vmLangIntersector() {
		$option = JRequest::getVar('option', ''); 
	 $view = JRequest::getVar('view', ''); 
	 $task = JRequest::getVar('task', ''); 
	 $format = JRequest::getVar('format', ''); 
	 $editView = JRequest::getVar('editView', ''); 
	 $lg = JRequest::getVar('lg', ''); 
	 $id = JRequest::getVar('id', 0); 
	 
	 
	 if (($option === 'com_virtuemart') && ($view === 'translate') && ($task === 'paste') && ($format === 'json') && ($editView === 'product') && (!empty($lg))) {
		 
		    if (!self::loadVM()) return; 

		if (is_array($id) && (count($id)==1)) $id = (int)reset($id); 
		if (empty($id)) return; 
		
		$lang = $lg; 
		if (!isset(VmConfig::$jDefLang)) return; 
		$langs = VmConfig::get('active_languages',array(VmConfig::$jDefLang)) ;

		if (!in_array($lang, $langs) ) {
			return; 
		}
		if (!method_exists('vmLanguage', 'setLanguageByTag')) return; 
		vmLanguage::setLanguageByTag($lang);
		
		if (empty(VmConfig::$vmlang)) return; 
		
		$dblang = VmConfig::$vmlang;
		$tableName = '#__virtuemart_products_'.$dblang;
 
		$m = VmModel::getModel('coupon');
		$table = $m->getTable('products');
		if (empty($table)) {
			return; 
		    
		}
		static $done; 
		$done = array(); 
		
		if (!is_array($id)) {
			$product_id = $id; 
			$json = $this->getLangData($product_id, $lang, 'product', VmConfig::$vmlang, $json);
			
			$data = array(); 
			$fromlang = ''; //JFactory::getLanguage()->getTag(); 
			
			$this->dataFromLangToLang($product_id, $data, $fromlang, $lang); 
			
			$tr = array('tabname', 'tabdesc', 'tabcontent'); 
			if (!empty($data)) {
				foreach ($data as $k=>$row) {
					if ($row['tabname'] === 'disablethis') break; 
					$tab_id = $row['id']; 
					foreach ($row as $ke=>$val) {
						if (!in_array($ke, $tr)) continue; 
						$json['fields'][$ke.'_'.$tab_id] = $val; 
					}
				}
			}
		}
		else {
			$json['multiple'] = array();

			$firstid = (int)reset($id); 
			foreach ($id as $myid) {
				
				$tomerge =  array();
				$ret = array(); 
				
				$product_id = $id; 
				$tomerge = $this->getLangData($myid, $lang, 'product', VmConfig::$vmlang,$ret);
				
				if (empty($tomerge)) continue; 
				$data = array(); 
				$fromlang = JFactory::getLanguage()->getTag(); 
				if (!empty($firstid)) {
				$fromlang = ''; 
				$this->dataFromLangToLang($firstid, $data, $fromlang, $lang); 
				//$data = $this->getData($myid, $lang); 
				$firstid = 0; 
			$tr = array('tabname', 'tabdesc', 'tabcontent'); 
			if (!empty($data)) {
				
				foreach ($data as $k=>$row) {
					if ($row['tabname'] === 'disablethis') break; 
					$tab_id = $row['id']; 
					foreach ($row as $ke=>$val) {
						
						if (!in_array($ke, $tr)) continue; 
						
						$kk = $ke.'_'.$tab_id;
					    if (!empty($done[$kk])) continue; 
						$tomerge['fields'][$kk] = $val; 
						$done[$kk] = true; 
						
					}
				}
			
				
			}
				}
				
				
				$tomerge['requested_id'] = $myid;
				$json['lang'] =  VmConfig::$vmlang;
				$json['multiple'][] = $tomerge;
			}
			
			
		}

			echo vmJsApi::safe_json_encode($json);
			jExit();
			return;
		
		 
		 
	 }
	}
	
	
	/* this functio comes from translate.php since it doesn't allow any sort of override */
	
	private function getLangData($id, $lang, $viewKey, $dblang, $json) {

		$tables = array ('category' =>'categories','product' =>'products','manufacturer' =>'manufacturers','manufacturercategories' =>'manufacturercategories','vendor' =>'vendors', 'paymentmethod' =>'paymentmethods', 'shipmentmethod' =>'shipmentmethods');
		$tableName = '#__virtuemart_'.$tables[$viewKey].'_'.$dblang;
 
		$m = VmModel::getModel('coupon');
		$table = $m->getTable($tables[$viewKey]);
		if (empty($table)) {
		   return false; 
		}
		//Todo create method to load lang fields only
		$table->load($id);
		$vs = $table->loadFieldValues();
		$lf = $table->getTranslatableFields();

		$json['fields'] = array();
		foreach($lf as $v){
			if(isset($vs[$v])){
				$json['fields'][$v] = $vs[$v];
			}
		}

		//if ($json['fields'] = $db->loadAssoc()) {
		if ($table->getLoaded()) {
			$json['structure'] = 'filled' ;
			$json['msg'] = vmText::_('COM_VIRTUEMART_SELECTED_LANG').':'.$lang;

		} else {
			$db =JFactory::getDBO();

			$json['structure'] = 'empty' ;
			$db->setQuery('SHOW COLUMNS FROM '.$tableName);
			$tableDescribe = $db->loadAssocList();
			array_shift($tableDescribe);
			$fields=array();
			foreach ($tableDescribe as $key =>$val) $fields[$val['Field']] = $val['Field'] ;
			$json['fields'] = $fields;
			$json['msg'] = vmText::sprintf('COM_VIRTUEMART_LANG_IS_EMPTY',$lang ,vmText::_('COM_VIRTUEMART_'.strtoupper( $viewKey)) ) ;
		}
		return $json; 
	}
	private function fixPrev() {
		return;
		$db = JFactory::getDBO(); 
		$q = 'delete from #__producttabs where extra1 NOT LIKE "" and (extra2 = "" or extra2 = "0")'; 
		$db->setQuery($q); 
		$db->query(); 
		
		
		
	}
	private function removeTab($product_id, $id=0) {
		$db = JFactory::getDBO(); 
	  $q = 'select extra2 from #__producttabs where id = '.(int)$id; 
	  $db->setQuery($q); 
	  $fallbackid = $db->loadResult(); 
	  
	  
	  $product_id = (int)$product_id; 
	  $id = (int)$id; 
	  if ((!empty($product_id)) ) {
	  $db = JFactory::getDBO(); 
	  $q = 'delete from #__producttabs where '; 
	  if (!empty($id)) {
	    $q .= '`id` = '.(int)$id.' and '; 
	  }
	  $q .= ' virtuemart_product_id = '.(int)$product_id; 
	  if (!empty($id)) {
	   $q .= ' or extra2 = '.(int)$id; 
	  }
	  if (!empty($fallbackid)) {
		  $q .= ' or `id` = '.(int)$fallbackid;
		  $q .= ' or `extra2` = '.(int)$fallbackid;
	  }
	  
	  $db->setQuery($q); 
	  $db->query(); 
	  }
	}
	
	//returnes true if lang had changed... 
	private function checkChangedLang(&$id, $lang='', &$fallbackid, $product_id) {
	
		if (empty($id)) return true; 
		
		$default_lang = $this->getDefaultLang(); 
		if ($lang === $default_lang) return false; 
		
		$db = JFactory::getDBO(); 
		$q = 'select `extra1`, `extra2` from #__producttabs where `id` = '.(int)$id; 
		$db->setQuery($q); 
		$resA = $db->loadAssoc(); 
		
		
		
		//if lang is empty, this is DEFAULT !
		if ((is_null($resA)) || ($resA===false)) return true; //not found !
		
		$IDlang = $resA['extra1']; 
		
		if ($IDlang === $default_lang) {
			$fallbackid = $id; //self 
		}
		else {
		  if (!empty($resA['extra2'])) {
			  $fallbackid = (int)$resA['extra2'];
		  } 
		}
		
		
		
		
		if (empty($fallbackid)) {
			//create empty tab for fallback ID: 
			JFactory::getApplication()->enqueueMessage(JText::_('PLG_SYSTEM_PRODUCTTABS_FALLBACKERROR')); 
			return true; 
		}
		
		/*
		if ($IDlang == $default_lang) {
			if ($lang !== $default_lang) {
				$q = 'select id from #__producttabs where extra1 = \''.$db->escape($lang).'\' and extra2 = \''.(int)$fallbackid.'\''; 
			}
		}
		*/
		
		//get Updated ID: 
		$q = 'select `id` from #__producttabs where virtuemart_product_id = \''.(int)$product_id.'\' and extra2 = \''.(int)$fallbackid.'\' '; 
		$q .= ' and extra1 = \''.$db->escape($lang).'\' '; 
		$q .= ' limit 1'; 
		$db->setQuery($q); 
		$idX = $db->loadResult(); 
		
	
		
		if (!empty($idX)) {
			$id = $idX; 
			return false; 
		}
		
		if ($lang === $IDlang) return false; //lang matches the ID
		if ($lang !== $IDlang) return true; //add a new ID
		return false; //update existing... 
		
	}
	
	private function dataFromLangToLang($product_id, &$data, $fromlang, $tolang) {
		
		//load current lang: 
		$data = $this->getData($product_id, $tolang); 
		
		
		//var_dump($data); die('zxxx'.__LINE__); 
		
		
		$default_lang = $this->getDefaultLang(); 
		if (empty($fromlang)) $fromlang = $default_lang; 
		if ($fromlang === $tolang) return; 
		
		
		$lg2 = $fromlang;
		
		
		if ($lg2 === $default_lang) $fromlang = $default_lang; 
		$tr = array('tabname', 'tabdesc', 'tabcontent'); 
		//load fallback lang:
		$data2 = $this->getData($product_id, $fromlang); 
		
		foreach ($data2 as $k=>$row) {
			
			foreach ($data as $k2=>$r2) {
				
				
				if ($row['id'] === $r2['extra2']) {
					
					
					foreach ($tr as $key) {
					
						$data2[$k][$key] = $r2[$key]; 
					}
				}
			}
		}
		$data = $data2; 
		
		
		
		
	}
	private function insertUpdate($product_id, &$id, $name, $desc, $cont, $lang='', $ordering=0) {
		
		
		
	   $fallbackid = 0; 
	   
	   $db = JFactory::getDBO(); 
	   $default_lang = $this->getDefaultLang(); 
	   $orig_id = $id; 
	   if (empty($lang)) $lang = $default_lang; 
	   
	   if (!empty($id))
	   if ($this->checkChangedLang($id, $lang, $fallbackid, $product_id)) $id = 0; 
	   
	   
	   if (empty($id)) {
		  
	   }
	   
	   
	  
	   
	   
	   if ($lang !== $default_lang) {
		   //check if we got master lang ready: 
		   if (empty($fallbackid)) {
			   /*
				$q = 'select `id` from #__producttabs where `virtuemart_product_id` = '.(int)$product_id.' and `extra1` = \''.$db->escape($default_lang).'\''; 
				$db->setQuery($q); 
				$master_id = $db->loadResult(); 
				*/
				 $q = 'select `id`, `tabname`, `tabdesc`, `tabcontent` from #__producttabs where `virtuemart_product_id` = '.(int)$product_id.' and `extra1` = \''.$db->escape($default_lang).'\' and `extra2` = \'\''; 
		   }
		   else {
			    $q = 'select * from #__producttabs where `id` = '.(int)$fallbackid; 
		   }
				$db->setQuery($q); 
				$data = $db->loadAssoc(); 
				
				if (empty($data['id'])) {
					 $q = "insert into #__producttabs (`id`, `virtuemart_product_id`, `tabname`, `tabdesc`, `tabcontent`, `extra1`, `extra2`, `ordering`) values (NULL, ".(int)$product_id.", '".$db->escape($name)."', '".$db->escape($desc)."', '".$db->escape($cont)."', '".$db->escape($default_lang)."', '0', ".(int)$ordering.")"; 
					$db->setQuery($q); 
					$db->query();
					
					 $last_id = $db->insertid();
					 if (!empty($last_id)) $fallbackid = $last_id; 
					
				}
				else {
					if (empty($data['tabname']) && (empty($data['tabdesc'])) && (empty($data['tabcontent']))) {
				 $q = "update #__producttabs set `tabname` = '".$db->escape($name)."', `tabdesc` = '".$db->escape($desc)."', `tabcontent` = '".$db->escape($cont)."', `ordering`=".(int)$ordering." where `id` = ".(int)$data['id']; 
				 
				 $db->setQuery($q); 
					$db->query();
				 
					}
				}
			
			
	   }
	   
	   
	   
	   
	   if (empty($orig_id)) {
		   
		 
	     $q = "insert into #__producttabs (`id`, `virtuemart_product_id`, `tabname`, `tabdesc`, `tabcontent`, `extra1`, `extra2`, `ordering`) values (NULL, ".(int)$product_id.", '".$db->escape($name)."', '".$db->escape($desc)."', '".$db->escape($cont)."', '".$db->escape($lang)."', '".(int)$fallbackid."', ".(int)$ordering.")"; 
		 $db->setQuery($q); 
		 $db->query(); 
		 
		 $last_id = $db->insertid();
		 if (!empty($last_id)) $id = $last_id; 
		
	   }
	   else
	   {
		   $q = "update #__producttabs set `tabname` = '".$db->escape($name)."', `tabdesc` = '".$db->escape($desc)."', `tabcontent` = '".$db->escape($cont)."', `ordering`=".(int)$ordering." where `id` = ".(int)$id." and `virtuemart_product_id` = ".(int)$product_id." "; 
		  
		   $db->setQuery($q); 
		   $db->query(); 
	   }
	   
	   $langs = VmConfig::get('active_languages',array($default_lang));
	   /*
	    
	   
	   if (empty($master_id)) {
		   
		    //insert also into default language: 
		    $q = "insert into #__producttabs (`id`, `virtuemart_product_id`, `tabname`, `tabdesc`, `tabcontent`, `extra1`, `extra2`, `ordering`) values (NULL, ".(int)$product_id.", '".$db->escape($name)."', '".$db->escape($desc)."', '".$db->escape($cont)."', '".$db->escape($default_lang)."', '0', ".(int)$ordering.")"; 
		    $db->setQuery($q); 
		    $db->query(); 
	   }
	   */
	   
	   if ($lang === $default_lang) {
		   $fallbackid = $id; 
	   }
	   
	   foreach ($langs as $langZ) {
		  if ($langZ == $lang) continue; 
		  if ($langZ == $default_lang) continue; 
		  
		    $q = 'select `id`, `tabname`, `tabdesc`, `tabcontent` from #__producttabs where `virtuemart_product_id` = '.(int)$product_id.' and `extra1` = \''.$db->escape($langZ).'\' and `extra2` = \''.(int)$fallbackid.'\''; 
			$db->setQuery($q); 
			
			$data = $db->loadAssoc(); 
			
			$has_id = false; 
			if (isset($data['id'])) $has_id = true; 
			
			//copy to other langs as well automatically: 
			if (empty($has_id)) {
				 $q = "insert into #__producttabs (`id`, `virtuemart_product_id`, `tabname`, `tabdesc`, `tabcontent`, `extra1`, `extra2`, `ordering`) values (NULL, ".(int)$product_id.", '".$db->escape($name)."', '".$db->escape($desc)."', '".$db->escape($cont)."', '".$db->escape($langZ)."', '".(int)$fallbackid."', ".(int)$ordering.")"; 
				 $db->setQuery($q); 
				 $db->query(); 
			}
			else {
				
				if (empty($data['tabname']) && (empty($data['tabdesc'])) && (empty($data['tabcontent']))) {
				 $q = "update #__producttabs set `tabname` = '".$db->escape($name)."', `tabdesc` = '".$db->escape($desc)."', `tabcontent` = '".$db->escape($cont)."', `ordering`=".(int)$ordering." where `id` = ".(int)$data['id']; 
				}
		  
				$db->setQuery($q); 
				$db->query(); 
				
			}
			
		   
	   }
	   
	   
	   //adjust ordering for current item: 
	   if (!empty($id)) {
	   $q = 'update #__producttabs set `ordering` = \''.(int)$ordering.'\' where `id` = '.(int)$id;
	   if (!empty($fallbackid)) {
		   //for the fallbackitem:
	   $q .= ' or `id` = '.$fallbackid; 
		   //for the other languages:
	   $q .= ' or `extra2` = '.$fallbackid; 
	   $q .= ' or `extra2` = '.$id; 
	   }
   
	   $db->setQuery($q); 
	   $db->query(); 
	   }
	   
	}
	
	/*helper functions*/
	private static function toObject(&$product, $recursion=0) {
    
	
	if (is_object($product)) {
	 $copy = new stdClass(); 
	 $attribs = get_object_vars($product); 
	 $isO = true; 
	}
	elseif (is_array($product)) {
		  $copy = array(); 
		  $isO = false; 
		  $attribs = array_keys($product); 
		  $copy2 = array(); 
		  foreach ($attribs as $zza=>$kka) {
		       if (strpos($kka, "\0")===0) continue;
			   $copy2[$kka] = $product[$kka]; 
		  }
		  $attribs = $copy2; 
		}
		
	
    foreach ($attribs as $k=> $v) {
		if (strpos($k, "\0")===0) continue;
		if ($isO) {
	      $copy->{$k} = $v; 	
		}
		else
		{
			$copy[$k] = $v; 
		}
		
		//if ($recursion < 5)
		if ((is_object($v)) && (!($v instanceof stdClass))) {
		   $recursion++; 
		   if ($isO) {
		     OPCmini::toObject($copy->{$k}, $recursion); 
		   }
		   else
		   {
			   OPCmini::toObject($copy[$k], $recursion); 
		   }
		}
		else
		{
			if (is_array($v)) {
			   $recursion++; 
			   if ($isO) {
		        OPCmini::toObject($copy->{$k}, $recursion); 
			   }
			   else
			   {
				   OPCmini::toObject($copy[$k], $recursion); 
			   }
			}
		}
		/*
		if (is_array($v)) {
		
		  $keys = array_keys($v); 
	  
		  foreach ($keys as $kk2=>$z2) {
		     if (strpos($z2, "\0")===0) continue;
			 $copy->{$k}[$z2] = $v[$z2]; 
			 if ((is_object($v[$z2])) && (!($v[$z2] instanceof stdClass))) {
				$recursion++; 
			    OPCmini::toObject($copy->{$k}[$z2]); 
			 }
			 else
			 if (is_array($v[$z2])) {
			    $recursion++; 
			    OPCmini::toObject($copy->{$k}[$z2]); 
			 }
			 
		  }
		}
		*/
		
		
	}
	$recursion--;
	$product = $copy; 
 }
 
 
 public function onBeforeRender() {
	
	 
	 
	 
 }

}


// No closing tag