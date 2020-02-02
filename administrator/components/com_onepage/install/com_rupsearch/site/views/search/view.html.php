<?php
/**
 * @package		RuposTel Ajax search pro
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2014 RuposTel.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */


defined('_JEXEC') or die;
	 if (!class_exists('VmConfig'))	  
	 {
	  require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	  VmConfig::loadConfig(); 
	 }

if (!class_exists('VirtuemartViewCategory')) require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'category'.DIRECTORY_SEPARATOR.'view.html.php');

class RupsearchViewSearch extends VirtuemartViewCategory
{
	
	function display($tpl = null)
	{
		
		if (!class_exists ('CurrencyDisplay'))
				require(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'currencydisplay.php');
		
			JFactory::getLanguage()->load('com_rupsearch', JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_rupsearch'); 
			JFactory::getLanguage()->load('com_rupsearch'); 

		
		
		jimport( 'joomla.registry.registry' );
	    $document = JFactory::getDocument();
		require_once(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php'); 
		RupHelper::updateStats(); 
		RupHelper::getIncludes(); 
		$this->app = JFactory::getApplication();
		$this->searchcustom = '';
		$this->searchCustomValues = '';
		$this->show_prices  = (int)VmConfig::get('show_prices',1);
		
		$css = ' .orderby-displaynumber { display: none; } '; 
		$doc = JFactory::getDocument(); 
		if (method_exists($doc, 'addStyleDeclaration'))
		$doc->addStyleDeclaration($css); 
		
		if (class_exists('vmJsApi'))
		{
			if (method_exists('vmJsApi', 'jQuery'))
				vmJsApi::jQuery();
			if (method_exists('vmJsApi', 'jSite'))
				vmJsApi::jSite();
			if (method_exists('vmJsApi', 'cssSite'))
				vmJsApi::cssSite(); 
			if (method_exists('vmJsApi', 'jDynUpdate')) 
				vmJsApi::jDynUpdate(); 
		}
		
		$id = JRequest::getVar('module_id', 0); 
		$params = RupHelper::getParams($id); 
		/*
		if (!empty($id))
		 {
		    $id = (int)$id; 
			$q = 'select params from `#__modules` where id = '.$id.' limit 1'; 
			
			$db = JFactory::getDBO(); 
			$db->setQuery($q); 
			$params_s = $db->loadResult(); 
			$params = new JRegistry($params_s); 
			
		 }
		 */
		
		$keyword = JRequest::getVar('keyword'); 
		
		if (!isset($params) || (!is_object($params)))
		{
		$prods = JRequest::getInt('prods', 5); 
		$cache = (bool)JRequest::getVar('internal_caching', true); 
		}
		else
		{
		  $prods = (int)$params->get('number_of_products', 5); 
		  $cache = (bool)$params->get('internal_caching', false); 
		}
		
		$po = (int)$prods; 
		
		$limit = JRequest::getVar('limit', $prods); 
		if (empty($limit)) $limit = $prods; 
		$limit = (int)$limit; 
		$prods = $limit; 
		
		
		$current_position = $this->current_position = $limit; 
		
		$popup = JRequest::getInt('popup', false); 
		
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		$cache_dir = JPATH_SITE.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'com_rupsearch'; 
		$orderBy = JRequest::getVar('order_by', ''); 
		if (is_writable($cache_dir))
		{
		if (!file_exists($cache_dir))
		 {
		   JFolder::create($cache_dir); 
		 }
		 
		
		$md5 = md5($keyword.'_'.$orderBy); 
		$file = JFile::makesafe($keyword); 
		$cache_file = $cache_dir.DIRECTORY_SEPARATOR.$file.'_'.$md5.'.html'; 
		}
		else
		{
			$cache = false; 
		}
		
		if ((empty($cache)) || ((!empty($cache_file)) && (!file_exists($cache_file))))
		{
        		
		$ids = RupHelper::getProducts($keyword, $prods, $popup, $orderBy); 
		
		$dispatcher = JDispatcher::getInstance();
		$array = array('Search Results', $ids ); 
	    $res = $dispatcher->trigger('onTrackingOPCEvent',$array);
		
		$next = false; 
		
		if (count($ids) > $prods)
		{
		  $next = true; 
		}
		
		//$ids = array_pop($ids); 
		$i=0; 
		foreach ($ids as $k=>$v)
		{
		  $i++; 
		  if ($i > $prods) unset($ids[$k]); 
		}
		
		
		$show_prices  = VmConfig::get('show_prices',1);
		
		$vars = array(); 
		$vars['search'] = null; 
		$vars['show_prices'] = $show_prices;
	    $vars['number_of_prods'] = $prods; 
		$vars['showproducts'] = true; 
		$vars['app'] = $this->app; 
		$vars['productsLayout'] = VmConfig::get('productsublayout','products');
		if(empty($vars['productsLayout'])) $vars['productsLayout'] = 'products';
		// add javascript for price and cart, need even for quantity buttons, so we need it almost anywhere
		if (method_exists('vmJsApi', 'jPrice')) {
		  vmJsApi::jPrice();
		}

		$vars['showsearch'] = false; 
		$vars['keyword'] = false; 

		

		
		$categoryModel = VmModel::getModel('category');
		$productModel = VmModel::getModel('product');


		
		//$search = VmRequest::uword('keyword', null);
		$vars['searchcustom'] = '';
		$vars['searchcustomvalues'] = '';
		/*
		if (!empty($keyword)) {
			$vars['searchcustom'] = $this->getSearchCustom();
			$search = $keyword;
		} else {
			$keyword ='';
			$search = NULL;
		}
		*/
		$vars['search'] = null;
		//$vars['keyword'] = $keyword;
		$vars['keyword'] = false;


		$categoryId = JRequest::getInt('virtuemart_category_id', false);
		$virtuemart_manufacturer_id = JRequest::getInt('virtuemart_manufacturer_id', false );
	

		
				$category = new stdClass();
				$category->category_name = '';
				$category->category_description= '';
				$category->haschildren= false;
				$category->children = array();
				$category = $categoryModel->getCategory(0);
				
				$perRow = empty($category->products_per_row)? VmConfig::get('products_per_row',3):$category->products_per_row;
				
				$vars['perRow'] =  $perRow;
				require_once(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'pagination.php'); 
				
				
				
				 
				

				$ratingModel = VmModel::getModel('ratings');
				$showRating = $ratingModel->showRating();
				$productModel->withRating = $showRating;

				$vars['showRating']= $showRating;

				$products = $productModel->getProducts ($ids);
				$count = count($ids); 
				$rp = new rupPagination($count, 0, $prods , $perRow );
				$vars['vmPagination'] = $rp;
				
				$this->injectPositions($products); 
				
				//$products = $productModel->getProductsInCategory($categoryId);
				
				$this->currency = CurrencyDisplay::getInstance( );
			    $display_stock = VmConfig::get('display_stock',1);
			    $showCustoms = VmConfig::get('show_pcustoms',1);
				if (!class_exists ('vmCustomPlugin')) {
						require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR . 'vmcustomplugin.php');
					}
				$productModel->addImages($products,1);
				foreach ($products as $k=>$prod) {
					if (empty($prod->images[0])) {
						if (class_exists('VmMediaHandler')) {
						$products[$k]->images[0] = new VmMediaHandler(); 
						}
						else {
							if (class_exists('VmImage')) {
								$products[$k]->images[0] = new VmImage(); 
							}
							
						}
					}
					if($display_stock or $showCustoms){
					if(!$showCustoms){
					$products[$k]->stock = $productModel->getStockIndicator($products[$k]);
					}
					
					}
					
					
				}
				
				if($showCustoms){
				 if (method_exists('shopFunctionsF', 'sortLoadProductCustomsStockInd')) {
				 shopFunctionsF::sortLoadProductCustomsStockInd($products,$productModel);
				 }
				}
				
				$legacylayouts = VmConfig::get('legacylayouts', true); 
				
				if ((self::isLess(3018)) || (!empty($legacylayouts))) {
					
				  $vars['products'] = $products;
				  $vars['fallback'] = true; 
				}
				else {
					$vars['fallback'] = false; 
					$vars['products'] = array('products'=>$products, 'discontinued'=>array());
				}

				if ($products) {
					$currency = CurrencyDisplay::getInstance( );
					$vars['currency'] =  $currency;
					foreach($products as $product){
						$product->stock = $productModel->getStockIndicator($product);
					}
				}



				//$orderByList = $productModel->getOrderByList($categoryId);
				
				$orderByList = array(); //$productModel->getOrderByList($categoryId);
				$orderByList['orderby'] = ''; 
				$orderByList['manufacturer'] = ''; 				
				$vars['orderByList'] = $orderByList;

				

				
				$showBasePrice = false; 
				$vars['showBasePrice'] = $showBasePrice;

			

			
			


			$categoryModel->addImages($category,1);
			if (empty($category->images)) $category->images = array(); 
			if (class_exists('VmImage'))
			if (!isset($category->images[0])) $category->images[0] = new VmImage(); 
			
			$categoryModel->addImages($category->children,1);
			
			if (empty($category->children->images)) $category->children->images = array(); 
			if (class_exists('VmImage'))
			if (!isset($category->children->images[0])) $category->children->images[0] = new VmImage(); 
			
			if (method_exists('shopFunctionsF', 'triggerContentPlugin'))
			if (VmConfig::get('enable_content_plugin', 0)) {
				shopFunctionsF::triggerContentPlugin($category, 'category','category_description');
			}
			



			$app = JFactory::getApplication(); 
			$menus	= $app->getMenu();
			$menu = $menus->getActive();
			if(!empty($menu->query['categorylayout']) and $menu->query['virtuemart_category_id']==$categoryId){
				$category->category_layout = $menu->query['categorylayout'];
			}
			shopFunctionsF::setVmTemplate($this,$category->category_template,0,$category->category_layout);
		
		

		$vars['category'] =  $category;
		
		if ($next)
		{
			
		$prods2 = $params->get('number_of_products', $prods); 
		$p = JRequest::getVar('limit', $prods2); 
		$p = (int)$p; 
		$p = $p+$prods2; 
		$limit = $p;
		
		$vm_cat_id = JRequest::getInt('vm_cat_id', ''); 
		$opt_search = JRequest::getInt('opt_search', 0); 
		$id = (int)$id; 
		
	    $vars['next_link'] = 'index.php?keyword='.urlencode($keyword).'&opt_search='.$opt_search.'&module_id='.$id.'&view=search&limitstart=0&limit='.$limit.'&view=search&option=com_rupsearch&nosef=1';
		if (!empty($vm_cat_id)) { 
		$vars['next_link'] .= '&vm_cat_id='.$vm_cat_id; 
		}
		
		$vars['current_position'] = $current_position; 
		
		
		
		}
		// Override Category name when viewing manufacturers products !IMPORTANT AFTER page title.
		if (JRequest::getInt('virtuemart_manufacturer_id' ) and !empty($products[0]) and isset($category->category_name)) $category->category_name =$products[0]->mf_name ;
		
		if (!empty($cache) && (!empty($cache_file)))
		$this->cache_file = $cache_file; 
		else $this->cache_file = ''; 
		
		$categoryView = RupHelper::getVMView($this, 'category', $vars, 'category', 'default', 'html'); 
		
		
		ob_start(); 
		
		echo $categoryView; 
		
		$ex = '<script> if (typeof sessMin == \'undefined\') var sessMin = 15; </script>'; 
		echo $ex; 
		
		
	
		if (!empty($vars['next_link']))
		{
			
			$add_next = $params->get('add_next', false); 
			
		if (!empty($add_next))
		{
		
  
     ?><div class="sourcecoast" style="width: 100%; clear: both; margin-top:10px; margin-left: auto; margin-right: auto; "><span class="span_inside" style="margin-left: auto; margin-right: auto;"><a href="<?php echo JRoute::_($vars['next_link']); 
	 
	 
	 
	 if (!empty($this->current_position)) 
	 {
	    echo '#product_iter_'.$this->current_position; 
	 }
	 
	 
	 $next = JText::_(JText::_('COM_RUPSEARCH_NEXT')); 
	 ?>" class="btn btn-primary button_for_ajaxsearch"><?php echo $next; ?></a></span></div>
	 <?php
  
		}
		}
		
		$cd = ob_get_clean(); 
		echo $cd; 
		if (!empty($cache) && (!empty($cache_file)))
		JFile::write($cache_file, $cd); 
		
		}
		else
		{
		  echo file_get_contents($cache_file); 
		}
		
		$document->setTitle($keyword); 
		$document->setMetaData( 'title', $keyword );
		
		
		$app    = JFactory::getApplication();
		$pathway = $app->getPathway();
		$all = $pathway->getPathway();
		
		JFactory::getLanguage()->load('com_finder'); 
		$title = JText::_('COM_FINDER_DEFAULT_PAGE_TITLE'); 
		
		$item = new stdClass;
		$item->name = html_entity_decode($title, ENT_COMPAT, 'UTF-8');
		$item->link = '';
		
		$item2 = new stdClass;
		$item2->name = html_entity_decode($keyword, ENT_COMPAT, 'UTF-8');
		$item2->link = '';
		
		$all_pathway = array($item, $item2); 
		$pathway->setPathway($all_pathway);
		
		
		
		
	}
	private static function isLess($x) {
	  
	  if ((!defined('VM_VERSION')) || (VM_VERSION < 3)) return true; 
	  
	  
	  if (!class_exists('VmVersion')) {
		  require(JPATH_VM_ADMINISTRATOR.DIRECTORY_SEPARATOR.'version.php'); 
		}	
	  
	  if (!isset(VmVersion::$REVISION)) return false; 
	  $rev = (int)VmVersion::$REVISION; 
	  //3.0.16....9204
	  //3.0.14....9194
	  //3.0.13.2..9162
	  //3.0.12....9058
	  //3.0.9.6...8956
	  //3.0.6.2...8771
	  //3.0.4.....8672
	  //3.0.2.....8615
	  //3.0.8.....8836
	  //3.0.0.....8578
	  $x = (int)$x; 
	  switch ($x) {
	    case 3016: 
		  if ($rev > 9204) return false; 
		case 3014: 
		  if ($rev <= 9203) return true; 
		case 3012:
		  if ($rev <= 9162) return true; 
		case 306: 
		  if ($rev <= 8771) return true; 
		case 304: 
		  if ($rev <= 8672) return true; 
		case 302: 
		  if ($rev <= 8615) return true; 
		case 308: 
		  if ($rev <= 8836) return true; 
		case 3090: 
		  if ($rev <= 8847) return true; 
		case 30910: 
		 if ($rev <= 8986) return true; 
		case 3098: 
		 if ($rev <= 8971) return true; 
		case 3096: 
		 if ($rev <= 8956) return true; 
		case 3094: 
		 if ($rev <= 8872) return true; 
		case 309:
		 if ($rev <= 8847) return true; 
		case 300: 
		  if ($rev <= 8578) return true; 
		
		
		
	  }
	  //custom build: 
		return false; 
	  
	}
	
	public function injectPositions(&$products) {
		$n = 0; 
		foreach ($products as &$product) {
			$n++; 
			
			if (!isset($product->customfieldsSorted)) $product->customfieldsSorted = array(); 
			if (!isset($product->customfieldsSorted['addtocart'])) $product->customfieldsSorted['addtocart'] = array(); 
			
			$nf = new stdClass; 
			$nf->virtuemart_custom_id = 0; 
			$nf->custom_parent_id = 0; 
			$nf->virtuemart_vendor_id = 1; 
			$nf->custom_jplugin_id = 0; 
			$nf->custom_element = ''; 
			$nf->admin_only = 0; 
			$nf->custom_title = ''; 
			$nf->show_title = 0; 
			$nf->custom_tip = 0; 
			$nf->custom_value = 0; 
			$nf->custom_desc = 0; 
			$nf->field_type = 'S'; 
			$nf->is_list = 0; 
			$nf->is_hidden = 0; 
			$nf->is_cart_attribute = 0; 
			$nf->is_input = 0; 
			$nf->layout_pos = 'addtocart'; 
			$nf->custom_params = ''; 
			$nf->shared = 0; 
			$nf->published = 1; 
			$nf->virtuemart_customfield_id = 0; 
			$nf->virtuemart_product_id = $product->virtuemart_product_id; 
			$nf->customfield_value = ''; 
			$nf->customfield_price = 0; 
			$nf->customfield_params = ''; 
			$nf->fpublished = 0; 
			$nf->disabler = 0; 
			$nf->_varsToPushParamCustom = array(); 
			$nf->_varsToPushParamCustomField = array(); 
			$nf->addEmpty = 0; 
			$nf->selectType = 0; 
			$nf->_xParams = 'customfield_params'; 
			$nf->display = '<div id="product_iter_'.$n.'">&nbsp;</div>'; 
			$product->customfieldsSorted['addtocart'][] = $nf; 
			
			
			
			
			
		}
	}
	
	

}
