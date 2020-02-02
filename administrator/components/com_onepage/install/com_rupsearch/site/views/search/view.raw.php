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
	    jimport( 'joomla.registry.registry' );
		require_once(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php'); 
		
		RupHelper::getIncludes(); 
		$keyword = JRequest::getVar('keyword', ''); 
RupHelper::updateStats(); 
		$prods = JRequest::getInt('prods', 5); 
		$popup = JRequest::getInt('popup', false); 
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		$cache_dir = JPATH_SITE.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'com_rupsearch'; 
		if (!file_exists($cache_dir))
		 {
		   JFolder::create($cache_dir); 
		 }
		$orderBy = JRequest::getVar('order_by', ''); 
		$md5 = md5($keyword.'_'.$orderBy); 
		
		$file = JFile::makesafe($keyword); 
		$cache_file = $cache_dir.DIRECTORY_SEPARATOR.$file.'_'.$md5.'.html'; 
		$cache = JRequest::getVar('internal_caching', true); 
		//if (true)		
		//
		//if ((!file_exists($cache_file)) || (empty($cache)))
			if (true)
		{

		$ids = RupHelper::getProducts($keyword, $prods, $popup, $orderBy); 
		
		
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
		
		$layout = JRequest::getVar('layout', 'default'); 
		
		$productModel = VmModel::getModel('product');
		$products = $productModel->getProducts ($ids);
		
		
		
		
		
		
		if ($layout !== 'default')
		 {
			 
			 if (!class_exists('rupResize_Image'))
			{
			   require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_rupsearch'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'image_helper.php'); 
			}
			
		    $this->setLayout($layout); 
			
		    $this->products = $products; 
			$this->assignRef('products', $products); 
			$this->keyword = $keyword; 
			$this->prods = $prods; 
			
			$this->myid = JRequest::getInt('myid', 0); 
			
			JFactory::getLanguage()->load('com_rupsearch', JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_rupsearch'); 
			JFactory::getLanguage()->load('com_rupsearch'); 
			
		if ($next)
		{
			$this->next = true; 
		}
		else {
			$this->next = false; 
		}
			
			
			$id = JRequest::getVar('module_id', 0); 
			$params = RupHelper::getParams($id); 
			
			$this->module_params = $params; 
			
			$app = JFactory::getApplication(); 
			if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$app->getTemplate().DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'com_rupsearch'.DIRECTORY_SEPARATOR.'default_dropdown.php'))
			require(JPATH_SITE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$app->getTemplate().DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'com_rupsearch'.DIRECTORY_SEPARATOR.'default_dropdown.php'); 
			else
			require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_rupsearch'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'search'.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR.'default_dropdown.php'); 
			
			//parent::display($layout); 
			return false; 
		 }
		
		
		$show_prices  = VmConfig::get('show_prices',1);
		$this->app = JFactory::getApplication();
		$this->searchcustom = '';
		$this->searchCustomValues = '';
		$this->search = null; 
		$vars = array(); 
		$vars['show_prices'] = $show_prices;
		$vars['app'] = $this->app; 
		

		// add javascript for price and cart, need even for quantity buttons, so we need it almost anywhere
		
			
		//vmJsApi::jPrice();

		$document = JFactory::getDocument();

		

		
		$categoryModel = VmModel::getModel('category');
		


		
		//$search = VmRequest::uword('keyword', null);
		$vars['searchcustom'] = '';
		$vars['searchcustomvalues'] = '';
		if (!empty($keyword)) {
			$vars['searchcustom'] = $this->getSearchCustom();
			$search = $keyword;
		} else {
			$keyword ='';
			$search = NULL;
		}
		$vars['search'] = $search;
		$vars['keyword'] = $keyword;


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
				//$pag = $productModel->getPagination($perRow);
				
				require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_rupsearch'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'pagination.php'); 
				$rupPagination = new rupPagination($prods+1, 0, $prods+1, $perRow); 
				//$rupPagination = new rupPagination(10000, 0, 10000, $perRow); 
				
				$vars['vmPagination'] = $rupPagination;
				
				
				 
				$vars['search'] = null; 

				$ratingModel = VmModel::getModel('ratings');
				$showRating = $ratingModel->showRating();
				$productModel->withRating = $showRating;

				$vars['showRating']= $showRating;

				
				
				//$products = $productModel->getProductsInCategory($categoryId);
				$productModel->addImages($products,1);

				$vars['products'] = $products;

				if ($products) {
					$currency = CurrencyDisplay::getInstance( );
					$vars['currency'] =  $currency;
					foreach($products as $product){
						$product->stock = $productModel->getStockIndicator($product);
					}
				}


				
				$orderByList = array(); //$productModel->getOrderByList($categoryId);
				$orderByList['orderby'] = ''; 
				$orderByList['manufacturer'] = ''; 				
				$vars['orderByList'] = $orderByList;
 
				

				
				$showBasePrice = false; 
				$vars['showBasePrice'] = $showBasePrice;

			

			
			


			$categoryModel->addImages($category,1);

			
			$categoryModel->addImages($category->children,1);
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
		$title = ''; 
	    // Set the titles
		if (!empty($category->customtitle)) {
        	$title = strip_tags($category->customtitle);
     	} elseif (!empty($category->category_name)) {
     		$title = strip_tags($category->category_name);
		} else {
		    if (method_exists($this, 'setTitleByJMenu'))
			$title = $this->setTitleByJMenu($app);
		}

	  	if(JRequest::getInt('error')){
			$title .=' '.JText::_('COM_VIRTUEMART_PRODUCT_NOT_FOUND');
		}
		if(!empty($keyword)){
			$title .=' ('.$keyword.')';
		}

		if ($virtuemart_manufacturer_id and !empty($products[0])) $title .=' '.$products[0]->mf_name ;
		
		// Override Category name when viewing manufacturers products !IMPORTANT AFTER page title.
		if (JRequest::getInt('virtuemart_manufacturer_id' ) and !empty($products[0]) and isset($category->category_name)) $category->category_name =$products[0]->mf_name ;
		$this->cache_file = $cache_file; 
		
		
		$categoryView = RupHelper::getVMView($this, 'category', $vars, 'category', 'default', 'html'); 
		
		if (is_object($categoryView))
		foreach ($categoryView as $k=>$v)
		{
			$this->$k = $v; 
		}
		JFile::write($cache_file, $categoryView); 
		$this->categoryView = $categoryView; 
		
		
		
		if ($layout == 'default')
		{
		 $oldoption = JRequest::getVar('option'); 
	     $oldview = JRequest::getVar('view'); 
	     JRequest::setVar('view', 'category'); 
	     JRequest::setVar('option', 'com_virtuemart'); 
		}
		
		parent::display($tpl);
		
		if ($layout == 'default')
		{
		 JRequest::setVar('option', $oldoption); 
	     JRequest::setVar('view', $oldview); 
		}
		
		}
		else
		{
		  echo file_get_contents($cache_file); 
		}
		
		/*
		

CREATE TABLE IF NOT EXISTS `udx46_com_rupsearch_stats` (
  `keyword` varchar(1000) CHARACTER SET utf8 NOT NULL,
  `md5` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `count` bigint(20) NOT NULL,
  `accessstamp` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `md5` (`md5`),
  KEY `count` (`count`),
  KEY `accessstamp` (`accessstamp`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
*/
    
	$qt = "CREATE TABLE IF NOT EXISTS `#__com_rupsearch_stats` (
  `keyword` varchar(1000) CHARACTER SET utf8 NOT NULL,
  `md5` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `count` bigint(20) NOT NULL,
  `accessstamp` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `md5` (`md5`),
  KEY `count` (`count`),
  KEY `accessstamp` (`accessstamp`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1; "; 
   
	
	
	// flush buffer: 
	echo @ob_get_clean(); echo @ob_get_clean(); echo @ob_get_clean(); echo @ob_get_clean(); echo @ob_get_clean(); 
     $db = JFactory::getDBO(); 
	 
	    $prefix = $db->getPrefix();
		
		$table = '#__com_rupsearch_stats'; 
   $table = str_replace('#__', '', $table); 
   $table = str_replace($prefix, '', $table); 
 
   $q = "SHOW TABLES LIKE '".$db->getPrefix().$table."'";
	 $db->setQuery($q);
	  $r = $db->loadResult();
	   if (empty($r)) 
	   {
	     $db->setQuery($qt); 
		 $db->query(); 
	   }

	 
	 $q = 'insert into #__com_rupsearch_stats (`keyword`, `md5`, `count`, `accessstamp`) values '; 
	  $q .= " ('".$db->escape($keyword)."', '".$md5."', 1, ".time().") "; 
	  $q .= ' on duplicate key update count = count + 1, accessstamp = '.time(); 
	 $db->setQuery($q); 
	 $db->query();
	 $e = $db->getErrorMsg(); if (!empty($e)) echo $e; 
	 
	 // 2 months old will get deleted
	 $old = time() - (60*60*24*60); 
	 $q = 'delete from #__com_rupsearch_stats where accessstamp < '.$old; 
	 $db->setQuery($q); 
	 $db->query();
			
			
	 $app = JFactory::getApplication(); 
	 $app->close(); 
	}

}
