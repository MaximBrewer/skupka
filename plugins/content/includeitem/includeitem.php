<?php

// No direct access.
defined('_JEXEC') or die;
require_once JPATH_SITE.'/components/com_content/helpers/route.php';

class plgContentIncludeitem extends JPlugin
{

	public function onContentPrepare($context, &$row, &$params, $page = 0)
	{
		// Don't run this plugin when the content is being indexed

        // ini_set("display_errors",1);
        // error_reporting(E_ALL);

        if(is_null($params)||$params==""){
            $params=$this->params;
        }


		if ($context == 'com_finder.indexer') {
			return true;
		}
		if (is_object($row)) {

			return $this->_includeitems($row->text, $params);
		}

		return $this->_includeitems($row, $params);
	}

	protected function _getPattern ($link, $text) {
		$pattern = '~(?:<a [\w "\'=\@\.\-]*href\s*=\s*"mailto:'
			. $link . '"[\w "\'=\@\.\-]*)>' . $text . '</a>~i';
		return $pattern;
	}
	protected function _includeitems(&$text, &$params)
	{
		global $app;
		$mainframe = JFactory::getApplication();
		$document = JFactory::getDocument();
		$result = false;



		JModelLegacy::addIncludePath(JPATH_SITE.'/components/com_content/models', 'ContentModel');
		$css_file= $this->params->def('cssfile', '');
		if ($css_file != '') {
			JHtml::stylesheet('plugins/content/includeitem/css/'.$css_file);
		}
		# include/replace article
		$output = '';
		$article_tag = $this->params->def('article_tag', 'article');
		$pattern = "/{".$article_tag.":([ 0-9]*)}/";

		while (preg_match($pattern, $text, $matches)) {
			// Get an instance of the generic article model
			$article = JModelLegacy::getInstance('Article', 'ContentModel', array('ignore_request' => true));
			$article->setState('params', $params);
			$article->setState('filter.published', 1);
			$article->setState('article.id', (int) $matches[1]);
			//$item = $article->getItem();
			$item = $this->_getArticle((int) $matches[1], 1);



			if ($item) {
				$item->slug = $item->id.':'.$item->alias;
				$authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
				$article->setState('filter.access', $access);
				if ($access || in_array($article->access, $authorised))
				{
					// We know that user has the privilege to view the article
					$item->link = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catid));
					$item->linkText = JText::_('PLG_INCLUDEITEM_ARTICLE_READMORE');
				}
				else {
					$item->link = JRoute::_('index.php?option=com_users&view=login');
					$item->linkText = JText::_('PLG_INCLUDEITEM_ARTICLE_READMORE_REGISTER');
					$item->fulltext='';
				}
				ob_start();
				require dirname(__FILE__).'/includeitem/article.php';
				$article_output = ob_get_contents();
				ob_end_clean();
				if ($this->params->def('article_facebox', '')) {
					$result=true;
					$article_output = ''
						.'<a href="#include_article_'.(int) $matches[1].'" class="manuModal" rel="{handler: \'iframe\', size: {x: 700, y: 850}}">'.$item->title.'</a>'
						.'<div id="include_article_'.(int) $matches[1].'" >'
						.$article_output
						.'</div>'
						;
				}
				$text = str_replace($matches[0], $article_output, $text);
			} else {
				$text = str_replace($matches[0], $output, $text);
			}
		}

		$text = str_replace($matches[0], $output, $text);

		# Virtuemart
		$product_tag = $this->params->def('vm_product_tag', 'product');
		$category_tag = $this->params->def('vm_category_tag', 'category');
		$max_items = $this->params->def('vm_max_items', 2);
		$category_id = $this->params->def('vm_virtuemart_category_id', null);
		$display_style = $this->params->def('vm_display_style', 'div');
		$show_price = $this->params->def('vm_show_price', 1);
		$show_addtocart = $this->params->def('vm_show_addtocart', 1);
		$headerText = $this->params->def('vm_headerText', '');
		$footerText = $this->params->def('vm_footerText', '');
		$customfields = $this->params->def('vm_customfields_by_id', '');
		$products_per_row = $this->params->def('vm_products_per_row', 4);
		# include/replace product
		$output = '';
		$pattern = "/{".$product_tag.":([ ,0-9]*)}/";
		$virtuemart_currency_id = $app->getUserStateFromRequest( "virtuemart_currency_id", 'virtuemart_currency_id',JRequest::getInt('virtuemart_currency_id',0) );

		while (preg_match($pattern, $text, $matches)) {
			if (!class_exists( 'plg_virtuemart_product' )) require('helper.php');
			if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
			if (!class_exists( 'VirtueMartModelProduct' )) require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'models'.DS.'product.php');
			VmConfig::loadConfig();
			// Load the language file of com_virtuemart.
			JFactory::getLanguage()->load('com_virtuemart');
			if ($show_addtocart) {
				/*
				vmJsApi::jQuery();
				vmJsApi::jPrice();
				vmJsApi::cssSite();
				echo vmJsApi::writeJS();
				*/
				vmJsApi::jPrice();
				vmJsApi::cssSite();
				echo vmJsApi::writeJS();
			}
			$productModel = new VirtueMartModelProduct();
			$products=array();
			$tmp_products_1=str_replace(' ','', $matches[1]);
			$tmp_products_2 = explode(',',$tmp_products_1);
			foreach ($tmp_products_2 as $product_id) {
				$product = $productModel->getProduct($product_id);
				if ($product) {
					$products[]=$product;
				}
			}
			if (count($products)) {
				$result=true;
				$productModel->addImages($products);
				$currency = CurrencyDisplay::getInstance( );


				//дописуем произвольные поля
				if (!class_exists('shopFunctionsF'))
					require(JPATH_VM_SITE . DS . 'helpers' . DS . 'shopfunctionsf.php');
				shopFunctionsF::sortLoadProductCustomsStockInd($products,$productModel);
				//дописуем произвольные поля end


				ob_start();
				require dirname(__FILE__).'/includeitem/products.php';
				$product_output = ob_get_contents();
				ob_end_clean();
				$text = str_replace($matches[0], $product_output, $text);
			} else {
				$text = str_replace($matches[0], $output, $text);
			}
		}
		$text = str_replace($matches[0], $output, $text);




		# include/replace products of category
		$output = '';
		$pattern = "/{".$category_tag.":([ 0-9]*)}/";
		$virtuemart_currency_id = $app->getUserStateFromRequest( "virtuemart_currency_id", 'virtuemart_currency_id',JRequest::getInt('virtuemart_currency_id',0) );
		while (preg_match($pattern, $text, $matches)) {
			if (!class_exists( 'plg_virtuemart_product' )) require('helper.php');
			if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
			if (!class_exists( 'VirtueMartModelProduct' )) require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'models'.DS.'product.php');
			VmConfig::loadConfig();
			// Load the language file of com_virtuemart.
			JFactory::getLanguage()->load('com_virtuemart');
			if ($show_addtocart) {
				vmJsApi::jQuery();
				vmJsApi::jPrice();
				vmJsApi::cssSite();
			}
			$productModel = new VirtueMartModelProduct();
			//getProductListing ($group = FALSE, $nbrReturnProducts = FALSE, $withCalc = TRUE, $onlyPublished = TRUE, $single = FALSE, $filterCategory = TRUE, $category_id = 0)
			$products = $productModel->getProductListing(false, $max_items, $show_price, true, false,true, (int) $matches[1]);
			$productModel->addImages($products);
			$currency = CurrencyDisplay::getInstance( );
			if (count($products)) {
				$result=true;
				ob_start();
				require dirname(__FILE__).'/includeitem/products.php';
				$product_output = ob_get_contents();
				ob_end_clean();
				$text = str_replace($matches[0], $product_output, $text);
			} else {
				$text = str_replace($matches[0], $output, $text);
			}
		}
		if ($result) {
			/*
			$document->addScriptDeclaration(
				"jQuery(document).ready(function($) {
				  jQuery('a[rel*=facebox]').facebox()
				});
			");
			*/
		}
		$text = str_replace($matches[0], $output, $text);

		return true;
	}
	protected function _getArticle($id, $published) {
		$db =& JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select(
			'a.id, a.asset_id, a.title, a.alias, a.introtext, a.fulltext, ' .
			// If badcats is not null, this means that the article is inside an unpublished category
			// In this case, the state is set to 0 to indicate Unpublished (even if the article state is Published)
			'CASE WHEN badcats.id is null THEN a.state ELSE 0 END AS state, ' .
			'a.catid, a.created, a.created_by, a.created_by_alias, ' .
			// use created if modified is 0
			'CASE WHEN a.modified = 0 THEN a.created ELSE a.modified END as modified, ' .
			'a.modified_by, a.checked_out, a.checked_out_time, a.publish_up, a.publish_down, ' .
			'a.images, a.urls, a.attribs, a.version, a.ordering, ' .
			'a.metakey, a.metadesc, a.access, a.hits, a.metadata, a.featured, a.language, a.xreference'
		);
		$query->from('#__content AS a');
		// Join on category table.
		$query->select('c.title AS category_title, c.alias AS category_alias, c.access AS category_access');
		$query->join('LEFT', '#__categories AS c on c.id = a.catid');
		// Join on user table.
		$query->select('u.name AS author');
		$query->join('LEFT', '#__users AS u on u.id = a.created_by');
		// Join on contact table
		$query->select('contact.id as contactid' ) ;
		$query->join('LEFT', '#__contact_details AS contact on contact.user_id = a.created_by');
		// Join over the categories to get parent category titles
		$query->select('parent.title as parent_title, parent.id as parent_id, parent.path as parent_route, parent.alias as parent_alias');
		$query->join('LEFT', '#__categories as parent ON parent.id = c.parent_id');
		// Join on voting table
		$query->select('ROUND(v.rating_sum / v.rating_count, 0) AS rating, v.rating_count as rating_count');
		$query->join('LEFT', '#__content_rating AS v ON a.id = v.content_id');
		$query->where('a.id = ' . (int) $id);
		// Filter by start and end dates.
		$nullDate = $db->Quote($db->getNullDate());
		$date = JFactory::getDate();
		$nowDate = $db->Quote($date->toSql());
		$query->where('(a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . ')');
		$query->where('(a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . ')');
		// Join to check for category published state in parent categories up the tree
		// If all categories are published, badcats.id will be null, and we just use the article state
		$subquery = ' (SELECT cat.id as id FROM #__categories AS cat JOIN #__categories AS parent ';
		$subquery .= 'ON cat.lft BETWEEN parent.lft AND parent.rgt ';
		$subquery .= 'WHERE parent.extension = ' . $db->quote('com_content');
		$subquery .= ' AND parent.published <= 0 GROUP BY cat.id)';
		$query->join('LEFT OUTER', $subquery . ' AS badcats ON badcats.id = c.id');
		// Filter by published state.
		if (is_numeric($published)) {
			$query->where('(a.state = ' . (int) $published.')');
		}
		$db->setQuery($query);
		$data = $db->loadObject();
		if ($error = $db->getErrorMsg()) {
			//echo $error;
		}
		return $data;
	}
}