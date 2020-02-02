<?php
/**
 * SEF module for Joomla!
 *
 * @author      $Author: stAn Scholtz $
 * @copyright   http://www.rupostel.sk
 * @package     JoomSEF for Virtuemart
 *
 * Tested on:
 * Joomla 1.5.15
 * ARTIO JoomSEF 3.x
 * VirtueMart 1.1.4
 * 
 */


// Security check to ensure this file is being included by a parent file.


// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access.');
require_once(JPATH_SITE.DS.'components'.DS.'com_sef'.DS.'sef_ext'.DS.'com_virtuemart_helper.php'); 

class SefExt_com_virtuemart extends SefExt
{
	function afterCreate(&$uri)
	{
	  return false; 
	  if ($this->loadedUri) return; 
      $path = $uri->getPath();  
	  $qu = $this->beforeUri->getQuery(); 
	  $md5 = md5($qu); 
	  $db = JFactory::getDBO(); 
	  //var_dump($uri);
	  $uri2 = $uri->getPath(); 
	  if (empty($uri2)) return; 
	  if (substr($uri2, 0,1)=='/') $uri2 = substr($uri2, 1); 
	  //var_dump($uri2); die(); 
	  $q = "insert delayed into #__sef_virtuemart (`hash`, `data`) values ('".$db->escape($md5)."', '".$db->escape($uri2)."') on duplicate key update data = '".$db->escape($uri2)."'"; 
	  $db->setQuery($q); 
	  $db->query(); 
	  $e = $db->getErrorMsg(); 
	  if (!empty($e)) die($e); 
	  
	}
	
	function getSefUrlFromDatabase($uri)
	{
	  return false; 
	  $qu = $uri->getQuery(); 
	  $md5 = md5($qu); 
	  $db = JFactory::getDBO(); 
	  
	  $q = "select `data` from #__sef_virtuemart where hash = '".$db->escape($md5)."' limit 0,1"; 
	  
	  $db->setQuery($q); 
	  $res = $db->loadResult(); 
	  $e = $db->getErrorMsg(); 
	  
	  if (!empty($e)) die($e); 
	  if (!empty($res))
	   {
	      $this->loadedUri = true; 
	      return $res; 
	   }
	   $this->loadedUri = false; 
	   return false; 
	}
	
	var $beforeUri; 
	var $loadedUri; 
	
	function setCurrentUri($uri)
	{
	   $this->beforeUri = clone($uri); 
	}
	
	function beforeCreate(&$uri)
	{
	
	 $task = $uri->getVar('task', '');
	 if (($task == 'delete') || ($task == 'update')) return; 
	 $p = $uri->getVar('virtuemart_product_id', null);
	 if (isset($p) && ($p!=""))
	 {
	  $uri->delVar('virtuemart_category_id');
	  $uri->delVar('keyword');
	  $uri->delVar('virtuemart_manufacturer_id');
	  // legacy: 
	  $uri->delVar('vmcchk');
	  $uri->delVar('flypage'); 
	  //$uri->setVar('flypage', 'flypage-ask.tpl');
	  $uri->delVar('pop');
	 }
	 else
	 {
	  $m = $uri->getVar('virtuemart_manufacturer_id', null);
	  if (isset($m) && ($m!=""))
	  {
	  	//vm2: $uri->delVar('virtuemart_category_id');
	  	$uri->delVar('pop');
	  	$uri->delVar('keyword');
	  }
	}
	
	
	}
	
	public function getNonSefVars(&$uri)
    {
	    if (!isset($this->nonSefVars) && !isset($this->ignoreVars)) {
            $this->nonSefVars = array();
            $this->ignoreVars = array();
        }
		
       if (!is_null($uri->getVar('limitstart'))) 
		{
		  $limit = $uri->getVar('limitstart'); 
		        if (!is_numeric($limit)) $uri->delVar('limitstart'); 
		  $this->nonSefVars['limitstart'] = $uri->getVar('limitstart');
		}
		
		if (!is_null($uri->getVar('dir'))) 
		{
		
		  $limit = $uri->getVar('dir'); 
		       
		  $this->nonSefVars['dir'] = $uri->getVar('dir');
		}
		
		if (!is_null($uri->getVar('orderby'))) 
		{
		  $limit = $uri->getVar('orderby'); 
		       
		  $this->nonSefVars['orderby'] = $uri->getVar('orderby');
		}
		
		if (!is_null($uri->getVar('order'))) 
		{
		  $limit = $uri->getVar('order'); 
		        
		  $this->nonSefVars['order'] = $uri->getVar('order');
		}
		
		if (!is_null($uri->getVar('filter_product'))) 
		{
		  $limit = $uri->getVar('filter_product'); 
		        
		  $this->nonSefVars['filter_product'] = $uri->getVar('filter_product');
		}
		
		if (!is_null($uri->getVar('keyword'))) 
		{
		  $limit = $uri->getVar('keyword'); 
		        
		  $this->nonSefVars['keyword'] = $uri->getVar('keyword');
		}
		
		if (!is_null($uri->getVar('limit'))) {
		  $limit = $uri->getVar('limit'); 
		        if (!is_numeric($limit)) $uri->delVar('limit'); 
                $this->nonSefVars['limit'] = $uri->getVar('limit');
            }
			
		if (!is_null($uri->getVar('virtuemart_user_id'))) {
		  
                $this->nonSefVars['virtuemart_user_id'] = $uri->getVar('virtuemart_user_id');
            }
		
		if (!is_null($uri->getVar('addrtype'))) {
		  
                $this->nonSefVars['addrtype'] = $uri->getVar('addrtype');
            }
			
		if (!is_null($uri->getVar('new'))) {
		  
                $this->nonSefVars['new'] = $uri->getVar('new');
            }
			
		if (!is_null($uri->getVar('virtuemart_userinfo_id'))) {
		  
                $this->nonSefVars['virtuemart_userinfo_id'] = $uri->getVar('virtuemart_userinfo_id');
            }
			
			if (!is_null($uri->getVar('langswitch'))) {
		  
                $this->nonSefVars['langswitch'] = $uri->getVar('langswitch');
            }
		
		 if (!is_null($uri->getVar('1'))) {
            $this->nonSefVars['1'] = $uri->getVar('1');
         }	

		 if (!is_null($uri->getVar('token'))) {
            $this->nonSefVars['token'] = $uri->getVar('token');
        }
        if(!is_null($uri->getVar('return'))) {
        	$this->nonSefVars['return']=$uri->getVar('return');
        }
			
        return array($this->nonSefVars, $this->ignoreVars);
    }
	
	function create(&$uri) {
        $ignoreVars = $this->ignoreVars;
        $nonSefVars = $this->nonSefVars; 

		$uriCreated = false;
		$this->metadata = array(); 
		$newUri = $uri; 
			
		if(isset($this->lang)) {
        	$lang=$this->lang;
        }
		
		$query = $uri->getQuery(true);
		
		if (isset($query['langswitch']))
		$lang = $query['langswitch']; 

		if (empty($lang))
		{
		  $lango = JFactory::getLanguage(); 
		  $lang = $lango->getTag(); 
		  
		}
		
		$langvm = strtolower(str_replace('-', '_', $lang)); 
		
		$helper = vmrouterHelperSEF::getInstance($query);
		
		$jmenu = $helper->menu ;
		
        $sefConfig = SEFConfig::getConfig();
        $database = JFactory::getDBO();
		 $segments = array(); 
        // Use this to get variables from the original Joomla! URL, such as $task, $view, $id, $catID, ...
        
		
		if (isset($query['task']))
		$task = $query['task']; 
		else 
		$task = ''; 
		
		if (($task == 'update') || ($task == 'delete')) return $uri; 
		
		
		if(isset($query['view'])){
		$view = $query['view'];
		unset($query['view']);
		}
		else $view=''; 
		
		$priority = 15;
        $sitemap = $this->getSitemapParams($uri);

        switch ($view) {
		case 'virtuemart';
			$query['Itemid'] = $jmenu['virtuemart'] ;
			break;
		/* Shop category or virtuemart view
		 All ideas are wellcome to improve this
		 because is the biggest and more used */
		case 'category';
		
			$start = null;
			$limitstart = null;
			$limit = null;
			/*
			if ( isset($query['virtuemart_manufacturer_id'])  ) {
				$segments[] = $helper->lang('manufacturer').'/'.$helper->getManufacturerName($query['virtuemart_manufacturer_id']) ;
				unset($query['virtuemart_manufacturer_id']);

			}
			*/
			if ( isset($query['search'])  ) {
				$segments[] = trim($helper->lang('search')) ;
				unset($query['search']);
			}
			if ( isset($query['keyword'] )) {
				$segments[] = trim($query['keyword']);
				unset($query['keyword']);
			}
			if (!isset($query['virtuemart_category_id'])) break;
			$cx = $query['virtuemart_category_id']; 
			
			if ( isset($query['virtuemart_category_id']) ) {
				if (isset($jmenu['virtuemart_category_id'][ $query['virtuemart_category_id'] ] ) )
				{
					$query['Itemid'] = $jmenu['virtuemart_category_id'][$query['virtuemart_category_id']];
					
					
				}
				{
				
					
				
					$categoryRoute = $helper->getCategoryRoute($query['virtuemart_category_id']);
				
				

					
					
					
					
					
					if (isset($categoryRoute->route))
					{
					$arr = explode('~/~', $categoryRoute->route);
					if (count($arr)>0)
					{
					$catname = ''; 
					foreach ($arr as $c)
					  {
					   $catname = $c; 
					   $catname = str_replace("\xc2\xa0", '', $catname);
					   $segments[] = trim($c);
					   $helper->catsOfCats[$query['virtuemart_category_id']][] = trim($c); 
					  }
					}
					else
					{
					$segments[] = trim($categoryRoute->route); 
					$helper->catsOfCats[$query['virtuemart_category_id']][] = trim($categoryRoute->route);
					}
					}
					
					
					
					if ($categoryRoute->itemId) $query['Itemid'] = $categoryRoute->itemId;
				}
				
				
			}
			if ( isset($jmenu['category']) ) $query['Itemid'] = $jmenu['category'];

			/*
			if ( isset($query['order']) ) {
				if ($query['order'] =='DESC') $segments[] = $helper->lang('orderDesc') ;
				unset($query['order']);
			}
			*/
			/*
			if ( isset($query['orderby']) ) {
				$segments[] = $helper->lang('by').','.$helper->lang( $query['orderby']) ;
				unset($query['orderby']);
			}
			*/

			// Joomla replace before route limitstart by start but without SEF this is start !
			if ( isset($query['limitstart'] ) ) {
				$limitstart = $query['limitstart'] ;
				unset($query['limitstart']);
			}
			if ( isset($query['start'] ) ) {
				$start = $query['start'] ;
				unset($query['start']);
			}
			if ( isset($query['limit'] ) ) {
				$limit = $query['limit'] ;
				unset($query['limit']);
			}
			
			
			
			if ($start !== null &&  $limitstart!== null ) {
				//$segments[] = $helper->lang('results') .',1-'.$start ;
			} else if ( $start>0 ) {
				// using general limit if $limit is not set
				if ($limit === null) $limit= vmrouterHelperSEF::$limit ;

				$segments[] = $helper->lang('results') .','. ($start+1).'-'.($start+$limit);
			} else if ($limit !== null && $limit != vmrouterHelperSEF::$limit ) $segments[] = $helper->lang('results') .',1-'.$limit ;//limit change
			
			
			
			$title = $segments;
			
			if (count($title) > 0)
			{
			if (!isset($this->metatags)) $this->metatags = array(); 
			


			$metadb = $helper->getCategoryMeta($query['virtuemart_category_id'], $langvm); 
			$catname = $helper->my_ucfirst($catname); 
			    if (!empty($metadb['metadesc']))
				{
				  $desc = $metadb['metadesc']; 
				}
				else
				if (!empty($metadb['category_description']))
				{
				  $desc = strip_tags($metadb['category_description']); 
				}
				else
				{
				  $desc = $helper->getCatDescPerRandomProduct($query['virtuemart_category_id'], $langvm); 
				}
				$descc = $catname; 
				
				
				
				
				if (!empty($desc)) $descc .= ' | '.$desc;
				$this->metadata['metadesc'] = $metadata['metadesc'] = $descc; 
				
				$this->metadata['metakey'] = $metadata['metakey'] = $helper->getCategoryKeywords($query['virtuemart_category_id'], $title, $langvm);
				$this->metadata['metalang'] = $metadata['metalang'] = $lang;
				
				$this->metadata['metatitle'] = $metadata['metalang'] = $catname; 
				$this->metadata['metarobots'] = $metadata['metarobots'] = 'index, follow'; 
				


			unset($query['virtuemart_category_id']);
			//$newUri = JoomSEF::_sefGetLocation($uri, $title, @$task, null, null, @$lang, $this->nonSefVars, null, $this->metatags, $priority, true,null, $sitemap);
			$newUri = JoomSEF::_sefGetLocation($uri, $title, @$task, @$limit, @$limitstart, @$lang, $this->nonSefVars, $this->ignoreVars, $this->metadata);


			
			return $newUri; 
			}
			break;
		/* Shop product details view  */
		case 'productdetails';
			$virtuemart_product_id = false;
				$virtuemart_product_id = (int)$query['virtuemart_product_id'];
				
				
				$categoryRoute = $helper->getBestProductPath($virtuemart_product_id, $this->params);

				//var_dump($categoryRoute); die(); 

					
					
					
					$topcat = ''; 
					if (!empty($categoryRoute))
					{
					if (isset($categoryRoute->route))
					{
					$arr = explode('~/~', $categoryRoute->route);
					if (count($arr)>0)
					{
					
					foreach ($arr as $c)
					  {
					  if (empty($topcat)) $topcat = $c; 
					  $segments[] = trim($c);
					  }
					}
					else
					{
					if (empty($topcat)) $topcat = trim($categoryRoute->route); 
					$segments[] = trim($categoryRoute->route); 
					}
					}
					
					
					
					
					if (isset($categoryRoute->itemId)) $query['Itemid'] = $categoryRoute->itemId;
				
				
				
				
				$arr = $categoryRoute; 
				
				
				

				
					
					}
			
			if($virtuemart_product_id)
			{
					$pn = $helper->getProductName($virtuemart_product_id);
					$pn = iconv("UTF-8", "UTF-8//IGNORE", $pn);
					$pn = trim($pn); 
					//%20 
					
					
					
					$pn = str_replace('%20', '-', $pn); 
					$pn = str_replace("\xc2\xa0", ' ', $pn);
					$pn = JoomSEF::_titleToLocation($pn); 
					//if (stripos($pn, '1911-A')!==false)
					/*
					if (stripos($pn, 'HPA')!==false)
					 {
					 for($i = 0; $i < strlen($pn); $i++)
						{
						echo ord($pn[$i])."<br/>";
						}
					 
					   echo bin2hex($pn); var_dump($pn); die(); 
					   //var_dump($pn); die(); 
					 }
					 */
					$segments[] = $pn;
			}
			if (!isset($this->metatags)) $this->metatags = array(); 
			


			$metadb = $helper->getProductMeta($virtuemart_product_id, $langvm); 
			
			$catname = $helper->my_ucfirst($pn); 
			    if (!empty($metadb['metadesc']))
				{
				  $desc = $metadb['metadesc']; 
				}
				else
				if (!empty($metadb['product_s_desc']))
				{
				  $desc = strip_tags($metadb['product_s_desc']); 
				}
				else
				if (!empty($metadb['product_desc']))
				{
				  $desc = strip_tags($metadb['product_desc']); 
				}
				
				$descc = $catname; 
				
				
				
				
				if (!empty($desc)) $descc .= ' | '.$desc;
				$this->metadata['metadesc'] = $metadata['metadesc'] = $descc; 
				
				$this->metadata['metakey'] = $metadata['metakey'] = $helper->getProductKeywords($query['virtuemart_product_id'], $segments, $langvm);
				$this->metadata['metalang'] = $metadata['metalang'] = $lang;
				if (!empty($topcat))
				$catname .= ' | '.$helper->my_ucfirst($topcat); 
				$this->metadata['metatitle'] = $metadata['metalang'] = $catname; 
				$this->metadata['metarobots'] = $metadata['metarobots'] = 'index, follow'; 
				
				//$this->metadata['canonicallink'] = 
			
			

			 $title =  $segments;
			 if (count($title) > 0) 
			 $newUri = JoomSEF::_sefGetLocation($uri, $title, @$task, @$limit, @$limitstart, @$lang, $this->nonSefVars, $this->ignoreVars, $this->metadata);
			 //$newUri = JoomSEF::_sefGetLocation($uri, $title, @$task, null, null, @$lang, $this->nonSefVars);
			  
			 return $newUri; 
			 
			break;
		case 'manufacturer';

			if(isset($query['virtuemart_manufacturer_id'])) {
				if (isset($jmenu['virtuemart_manufacturer_id'][ $query['virtuemart_manufacturer_id'] ] ) ) {
					$query['Itemid'] = $jmenu['virtuemart_manufacturer_id'][$query['virtuemart_manufacturer_id']];
				} else {
					$segments[] = $helper->lang('manufacturers').'~/~'.$helper->getManufacturerName($query['virtuemart_manufacturer_id']) ;
					if ( isset($jmenu['manufacturer']) ) $query['Itemid'] = $jmenu['manufacturer'];
					else $query['Itemid'] = $jmenu['virtuemart'];
				}
				unset($query['virtuemart_manufacturer_id']);
			} else {
				if ( isset($jmenu['manufacturer']) ) $query['Itemid'] = $jmenu['manufacturer'];
				else $query['Itemid'] = $jmenu['virtuemart'];
			}
			break;
		case 'user';

			if ( isset($jmenu['user']) ) $query['Itemid'] = $jmenu['user'];
			else {
				$segments[] = $helper->lang('user') ;
				$query['Itemid'] = $jmenu['virtuemart'];
			}

			if (isset($query['task'])) {
				//vmdebug('my task in user view',$query['task']);
				if($query['task']=='editaddresscart'){
					if ($query['addrtype'] == 'ST'){
						$segments[] = $helper->lang('editaddresscartST') ;
					} else {
						$segments[] = $helper->lang('editaddresscartBT') ;
					}
				}

				else if($query['task']=='editaddresscheckout'){
					if ($query['addrtype'] == 'ST'){
						$segments[] = $helper->lang('editaddresscheckoutST') ;
					} else {
						$segments[] = $helper->lang('editaddresscheckoutBT') ;
					}
				}

				else {
					/*
					if (isset($query['addrtype']) and $query['addrtype'] == 'ST'){
						$segments[] = $helper->lang('editaddressST') ;
					} else {
						$segments[] = $helper->lang('editaddressBT') ;
					}
					else
					*/
				    $segments[] =  $helper->lang($query['task']);
					
					if (isset($query['addrtype']))
					$segments[] = $query['addrtype']; 
					
					if (isset($query['virtuemart_user_id']))
					if (!is_array($query['virtuemart_user_id']))
					$segments[] = $query['virtuemart_user_id']; 
					else $segments[] = reset($query['virtuemart_user_id']); 
					
					
					if (isset($query['virtuemart_userinfo_id']))
					if (!is_array($query['virtuemart_userinfo_id']))
					$segments[] = $query['virtuemart_userinfo_id']; 
					else $segments[] = reset($query['virtuemart_userinfo_id']); 
					
					if (isset($query['new']))
					$segments[] = $query['new']; 
					
				}
				
				/*	if ($query['addrtype'] == 'BT' && $query['task']='editaddresscart') $segments[] = $helper->lang('editaddresscartBT') ;
								elseif ($query['addrtype'] == 'ST' && $query['task']='editaddresscart') $segments[] = $helper->lang('editaddresscartST') ;
								elseif ($query['addrtype'] == 'BT') $segments[] = $helper->lang('editaddresscheckoutST') ;
								elseif ($query['addrtype'] == 'ST') $segments[] = $helper->lang('editaddresscheckoutST') ;
								else $segments[] = $query['task'] ;*/

				
			//var_dump($query); 
			
			unset ($query['task'] , $query['addrtype']);
			}
			
			break;
		case 'vendor';
/* VM208 */
			if(isset($query['virtuemart_vendor_id'])) {
				if (isset($jmenu['virtuemart_vendor_id'][ $query['virtuemart_vendor_id'] ] ) ) {
					$query['Itemid'] = $jmenu['virtuemart_vendor_id'][$query['virtuemart_vendor_id']];
				} else {
					if ( isset($jmenu['vendor']) ) {
						$query['Itemid'] = $jmenu['vendor'];
					} else {
						$segments[] = $helper->lang('vendor') ;
						$query['Itemid'] = $jmenu['virtuemart'];
					}
				}
			} else if ( isset($jmenu['vendor']) ) {
				$query['Itemid'] = $jmenu['vendor'];
			} else {
				$segments[] = $helper->lang('vendor') ;
				$query['Itemid'] = $jmenu['virtuemart'];
			}
			if (isset($query['virtuemart_vendor_id'])) {
				//$segments[] = $helper->lang('vendor').'/'.$helper->getVendorName($query['virtuemart_vendor_id']) ;
				$segments[] =  $helper->getVendorName($query['virtuemart_vendor_id']) ;
				unset ($query['virtuemart_vendor_id'] );
			}


			break;
		case 'cart';
			if ( isset($jmenu['cart']) ) $query['Itemid'] = $jmenu['cart'];
			else {
				$segments[] = $helper->lang('cart') ;
				$query['Itemid'] = $jmenu['virtuemart'];
			}

			break;
		case 'orders';
			if ( isset($jmenu['orders']) ) $query['Itemid'] = $jmenu['orders'];
			else {
				$segments[] = $helper->lang('orders') ;
				$query['Itemid'] = $jmenu['virtuemart'];
			}
			if ( isset($query['order_number']) ) {
				$segments[] = 'number/'.$query['order_number'];
				unset ($query['order_number'],$query['layout']);
			} else if ( isset($query['virtuemart_order_id']) ) {
				$segments[] = 'id/'.$query['virtuemart_order_id'];
				unset ($query['virtuemart_order_id'],$query['layout']);
			}

			//else unset ($query['layout']);
			break;

		// sef only view
		default ;
		   if (!empty($view))
			$segments[] = $view;


	}

	//	if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
	//	vmdebug("case 'productdetails'",$query);

	if (isset($query['task'])) {
		$segments[] = $helper->lang($query['task']);
		unset($query['task']);
	}
	if (isset($query['layout'])) {
		$segments[] = $helper->lang($query['layout']) ;
		unset($query['layout']);
	}
	// sef the slimbox View
/*	if (isset($query['tmpl'])) {
		//if ( $query['tmpl'] = 'component') $segments[] = 'modal' ;
		$segments[] = $query['tmpl'] ;
		unset($query['tmpl']);
	}*/
	//var_dump($segments); die(); 
	//$helper->storeSegments($oldquery, $query, $segments); 
	$title = $segments; 
	if (count($title) > 0) 
	$newUri = JoomSEF::_sefGetLocation($uri, $title, @$task, @$limit, @$limitstart, @$lang, $this->nonSefVars, $this->ignoreVars, $this->metadata);
	//$newUri = JoomSEF::_sefGetLocation($uri, $title, @$task, null, null, @$lang);
	  
	return $newUri;
	// end of original vm router
        $title = array();
		
		

		
        $title[] = JoomSEF::_getMenuTitle(@$option, @$task, @$Itemid);
        
        //$title[] = $view;
        //unset($vars['page'];
        //echo("<SCRIPT LANGUAGE='JavaScript'>window.alert('Virtue mart'".var_dump($vars).")</SCRIPT>");
        //echo 'hello';
		$oldUri = &$uri;
		
		if (isset($pshop_mode))
		{
			return $oldUri;
		}
		
		// ak konci s obrazkom alebo javascriptom a podobne...
		if ($this->endsWith($oldUri->_uri, 'jpg')) return $oldUri;
		if ($this->endsWith($oldUri->_uri, 'js')) return $oldUri;
		if ($this->endsWith($oldUri->_uri, 'css')) return $oldUri;
		
		    if (isset($section)/* && $sefConfig->showSection*/) {
            $sql = "SELECT title, id FROM #__sections WHERE id=".$section;
            $database->setQuery($sql);
            if (($section_name = $database->loadResult())) {
                $title[] = $section_name; //section name
		$uriCreated = true;
            } else {
                if( $section == '0' ) {
                    $title[] = JText::_('No section');
                } else {
                    if( strpos($section, 'com_') === 0 ) {
                        $title[] = substr($section, 4);
			$uriCreated = true;
                    } else {
                        $title[] = $section."test";
			$uriCreated = true;
                    }
                }
            }
            unset($vars['section']);
        }

        if (isset($cat)/* && $sefConfig->showCat*/) {
            $sql = "SELECT title, id FROM #__categories WHERE id=".$cat;
            $database->setQuery($sql);
            if ($cat_name = $database->loadResult()) {
                $title[] = $cat_name."test"; //category name
		$uriCreated = true;
            }
            unset($vars['cat']);
        }

        if (isset($id)) {
            $sql = "SELECT title, id FROM #__content WHERE id = $id";
            $database->setQuery($sql);
            if ($cTitle = $database->loadResult()) {
                $title[] = $cTitle."test"; //item title
		$uriCreated = true;
            }
            unset($vars['id']);
            if (@$task == 'view') unset($task);
        }

        // Add letter name.
        if (isset($alpha)) {
            $title[] = $alpha;
	    $uriCreated = true;
        }
        
        if (isset($view))         {
	if ($view == 'account.order_details')
	{
	 return $oldUri;
	}
	if ($view == 'account.billing')
	{
	 return $oldUri;
	}
	if ($view == 'account.shipto')
	{
	 return $oldUri;
	}
	if ($view == 'account.index')
	{
	 return $oldUri;
	}
	if ($view == 'shop.savedcart')
	{
	  return $oldUri;
	}
        if ($view == "shop.browse"){
			// stAn show manufacturer name
			if (isset($manufacturer_id))
			{
				$sql = "SELECT mf_name FROM #__virtuemart_manufacturer_".VMLANG." WHERE virtuemart_manufacturer_id='".$manufacturer_id."' LIMIT 0,1";
				$database->setQuery($sql);
			
				$manufacturer_name = $database->loadResult();
				$title[] = trim('Tovar od '.$manufacturer_name);
				$uriCreated = true;
				
				$sql = "SELECT product_s_desc FROM #__vm_product AS p, #__vm_product_mf_xref AS m  WHERE m.manufacturer_id = '".$manufacturer_id."' AND m.product_id = p.product_id ORDER BY LENGTH(product_s_desc) DESC LIMIT 0,1";
				
				$database->setQuery($sql);
				$desc = $database->loadResult(); 
				
				if (strlen($desc)<30) 
				{
				 $sql = "SELECT product_desc FROM #__vm_product AS p, #__vm_product_mf_xref AS m  WHERE m.manufacturer_id = '".$manufacturer_id."' AND m.product_id = p.product_id ORDER BY LENGTH(product_desc) DESC LIMIT 0,1";
  				 $database->setQuery($sql);
				 $desc .= strip_tags($database->loadResult());

				 
				}

				$metadata = array();
				
			
				
				$this->metadata['metadesc'] = $metadata['metadesc'] = $manufacturer_name.' | '.$desc;
				$this->metadata['metakey'] = $metadata['metakey'] = $this->getManufacturerKeywords($manufacturer_id, $title);
				$this->metadata['metalang'] = $metadata['metalang'] = $lang;
				
				
				
			}
			else
			//show category only
			if (isset($category_id))
			{
			    if ($category_id!='')
			    {
			    //$this->build_cats($category_id);
			
			    $sql = "SELECT category_name FROM #__vm_category WHERE category_id=".$category_id." LIMIT 0,1";
			    $database->setQuery($sql);
			
			    $category_name = $database->loadResult();
			    
			    // plna cesta ku kategorii
			    $ar0 = array();
			    $arr = $this->build_cats($category_id, $ar0);
			    $arr2 = array_reverse($arr, false);
				$title = array_merge($title, $arr2);
			    
			    //$this->logger($arr2, "get_topcat".$database->getErrorMsg());
			    
			    //$title[] = trim($category_name);
			    $uriCreated = true;
			    
			    $sql = "SELECT `category_description` FROM `#__vm_category` WHERE category_id='".$category_id."' LIMIT 0,1";
			    $database->setQuery($sql);
			    
			    $catdesc = $database->loadResult();
			    $catdesc = strip_tags($catdesc);
			    if (isset($catdesc))
			    if (strlen($catdesc)>10) $cattext = $catdesc;
			    
			   
			    
		    	
				
				$pdesc = $this->getRandomProductOfCat($category_id, 0);

  				if (empty($cattext)) $cattext = '';
		  		$metadata = array();
			  	$this->metadata['metadesc'] = $metadata['metadesc'] = $category_name.' | '.$cattext.' '.$pdesc;
			  	$this->metadata['metakey'] = $metadata['metakey'] = $this->getCategoryKeywords($category_id, $title);
			  	$this->metadata['metalang'] = $metadata['metalang'] = "sk-SK";

			    
			    }
			    else
			    {
				    
				    $uriCreated = true; 
			    }
		
			}
			else
			if (isset($keyword))
			{
			  $title[] = 'hladat/'.$keyword;
			  $uriCreated = true;
			}			
		}
		if ($view == "shop.manufacturer_page"){
			$sql = "SELECT mf_name FROM #__vm_manufacturer WHERE manufacturer_id=".$manufacturer_id." LIMIT 1";
			$database->setQuery($sql);
			$manufacturer_name = $database->loadResult();
			$title[] = $manufacturer_name;
			$uriCreated = true;
		}
		//view item
		if ($view == "shop.product_details"){
		//show category/itemname.html
			
//			if (isset($category_id))
//			$this->build_cats($category_id);
			
//			$sql = "SELECT category_name FROM #__vm_category WHERE category_id=".$category_id." LIMIT 0,1";
//			$database->setQuery($sql);
			
//			$category_name = $database->loadResult();
      		
      		//generujeme iba najvyssiu kategoriu
      		$category_name = $this->get_topcat($product_id);
      		
			
			$title[] = $category_name;
			$uriCreated = true;

			//itemname
			$sql = "SELECT product_name FROM #__vm_product WHERE product_id=".$product_id." LIMIT 0,1";
			$database->setQuery($sql);
			$product_name = $database->loadResult();
			$title[] = trim($product_name);

		    	$sql = "SELECT `product_s_desc` FROM `#__vm_product` WHERE product_id = '".$product_id."' LIMIT 0,1";
  				$database->setQuery($sql);
  				$pdesc = $database->loadResult();
  				if (strlen($pdesc) < 100) 
  				{
  				    $sql = "SELECT `product_desc` FROM `#__vm_product` WHERE product_id = '".$product_id."' LIMIT 0,1";
   					$database->setQuery($sql);
    				$pdesc .= strip_tags($database->loadResult());

  				}
		  		$metadata = array();
			  	$this->metadata['metadesc'] = $metadata['metadesc'] = $product_name." | ".$pdesc;
          		$this->metadata['metakey'] = $metadata['metakey'] = $this->getProductKeywords($product_id, $title, $langvm);
          		$this->metadata['metalang'] = $metadata['metalang'] = $lang;
          		
          		$p = $product_id;
  	 if (isset($p) && ($p!=""))
	 {
      		// zistit najhlbsiu kategoriu
      		$lowcat = $this->get_lowcat($p);
      		// pridame category_id do parametrov
      		if (!empty($lowcat))
      		{
	  		$uri->setVar('category_id', $lowcat);
	  		}
	  	
	  	
	 }
			    //$metadata['metagoogle'] = getProductKeywords($product_id);
			    
//			$nonSefVars['category_id'] = $category_id;
//			$nonSefVars['keyword'] = $keyword;
//			$nonSefVars['Itemid'] = $Itemid;
//			$nonSefVars['manufacturer_id'] = $manufacturer_id;
//			$nonSefVars['flypage'] = $flypage;
			//echo $product_name;
			
		}
		if ($view == 'shop.registration')
			return $oldUri;
		
		if ($view == 'shop.cart')
		{
				return $oldUri;
		}	
		if ($view == 'checkout.index')
		{
				return $oldUri;
		}	
		if ($view == 'shop.ask')
		{
				return $oldUri;
		}	
		
		unset($vars['page']);
		}
		
		// next_page
		if (isset($next_page))
		{
		if ($next_page == 'shop.registration')
		{
			return $oldUri;
		}
		if ($next_page == 'checkout.index')
		{
			return $oldUri;
		}
		if ($next_page == 'shop.ask')
		{
			return $oldUri;
		}
		if ($next_page == 'shop.cart')
		{
			return $oldUri;
		}
		}
	if (!$uriCreated)
	  return $oldUri;
	  
        $newUri = $uri;
        if (count($title) > 0) {
            if( isset($vars['sort']) )          $ignoreVars['sort'] = $vars['sort'];
            if( isset($vars['pop']))		$ignoreVars['pop'] = $vars['pop'];
//	    if( isset($vars['itemid']))         $ignoreVars['Itemid'] = $vars['Itemid'];

            if( isset($vars['limit']) )         $nonSefVars['limit'] = $vars['limit'];
            if( isset($vars['limitstart']) )    $nonSefVars['limitstart'] = $vars['limitstart'];
            $this->metatags = $this->getMetaTags();
			$nonSefVars = $this->nonSefVars; 
			
			$ignoreVars = $this->ignoreVars; 
			$newUri = JoomSEF::_sefGetLocation($uri, $title, @$task, @$limit, @$limitstart, @$lang, $this->nonSefVars, $this->ignoreVars, $this->metadata);
            //$newUri = JoomSEF::_sefGetLocation($uri, $title, @$task, @$limit, @$limitstart, @$lang, $nonSefVars, $ignoreVars, $metadata);
        }
        
        return $newUri;
    
	}
	
	
	function getMetaTags()
	{
	  if (!empty($this->metadata)) 
	  $this->metadata = $this->metatags; 
	  
	  if (!isset($this->metatags))
	   {
	     $this->metatags = new stdClass(); 
	   }
	  return $this->metatags; 
	}
	
	// zluci max 2-dimenzionalne polia
  
  
  
  function getAllProductsOfMf($mfid)
  {
      $database =& JFactory::getDBO();
      $sql = "SELECT #__vm_product.product_name FROM #__vm_product, #__vm_product_mf_xref as ref WHERE ref.manufacturer_id = '".$mfid."' and #__vm_product.product_id = ref.product_id LIMIT 0,10";
      $database->setQuery($sql);
      $prods = $database->loadResultArray(0);
	  return $prods;
  }
	
  
    function getProductDescription($product_id)
    {
    }
    // vrati nahodny popis produktu v kategorii, alebo podkategorii
	function getRandomProductOfCat($category_id, $d = 0)
	{
		$database =& JFactory::getDBO();
			
			    $sql = "SELECT product_s_desc FROM `#__vm_product` AS p, `#__vm_product_category_xref` AS c  WHERE c.category_id = '".$category_id."' AND c.product_id = p.product_id AND p.product_publish = 'Y' ORDER BY LENGTH(product_s_desc) DESC LIMIT 0,1";
  				$database->setQuery($sql);
  				$pdesc = $database->loadResult();
  				if (strlen($pdesc)<30) 
				{
				 $sql = "SELECT product_desc FROM #__vm_product AS p, `#__vm_product_category_xref` AS c  WHERE c.category_id = '".$category_id."' AND c.product_id = p.product_id AND p.product_publish = 'Y' ORDER BY LENGTH(product_desc) DESC LIMIT 0,1";
  				 $database->setQuery($sql);
				 $pdesc = strip_tags($database->loadResult());
				}
				if (strlen($pdesc)>0) return $pdesc;

			
			
			// keby sme sa chceli nahodou zacyklit, tak radsej skocime pri 15tej hlbke...
			if ($d > 15) return "";
			
			// zisti podradenu kategoriu
			$sql = "SELECT category_child_id FROM #__vm_category_xref, #__vm_category WHERE jos_vm_category.category_id=jos_vm_category_xref.category_parent_id and jos_vm_category.category_publish='Y' and  jos_vm_category_xref.category_parent_id ='$category_id' ORDER BY category_child_id DESC LIMIT 0,10";
			$database->setQuery($sql);
			$childs = $database->loadResultArray();
			foreach ($childs as $ch)
			{
			  return $this->getRandomProductOfCat($ch, $d++);
			}
			return "";
	}
  
    
    // posledna polozka v poli je top kategoria
	function build_cats($cat, $arr = array())
	{
		$database =& JFactory::getDBO();
			
			// keby sme sa chceli nahodou zacyklit, tak radsej skocime pri 15tej hlbke...
			if (sizeof($arr) > 15) return $arr;
			// zisti nadradenu kategoriu
			//$sql = "SELECT category_parent_id FROM #__vm_category_xref, #__vm_category WHERE //jos_vm_category.category_id=jos_vm_category_xref.category_child_id and jos_vm_category.category_publish='Y' and  //jos_vm_category_xref.category_child_id ='$cat' ORDER BY category_parent_id DESC LIMIT 0,1";
			
			$sql = "SELECT x.category_parent_id FROM jos_vm_category_xref as x, jos_vm_category WHERE jos_vm_category.category_id=x.category_parent_id and jos_vm_category.category_publish='Y' and  x.category_child_id ='$cat' ORDER BY x.category_parent_id DESC LIMIT 0,1";
			
			$database->setQuery($sql);
			$parent_cat_id = $database->loadResult();
			//$this->logger($parent_cat_id, "parent_cat_id for child ".$cat.$database->getErrorMsg());
			
			// zisti nazov kategorie
			$sql = "SELECT category_name FROM #__vm_category WHERE category_id ='".$cat."' LIMIT 0,1";
			$database->setQuery($sql);
			$parent_name = $database->loadResult();
			$arr[] = $parent_name;
			//$this->logger($parent_name, "parent_name for catid ".$cat.' '.$database->getErrorMsg());
			
			if (($parent_cat_id == '0') || (!isset($parent_cat_id))) {
				return $arr;
			}
			else
			{
			  return $this->build_cats($parent_cat_id, $arr);
			}  
	}//build_cats()
	
	function endsWith( $str, $sub ) {
		return ( substr( $str, strlen( $str ) - strlen( $sub ) ) == $sub );
//		return true;
	}
	
  
  // vrati top kategoiu produktu
  function get_topcat($product_id)
	{
	  $database =& JFactory::getDBO();
	  
	  $novinky = 262; // vylucime novinky zo seo cesty, cislo je category_id z tabulky jos_vm_category

	  $sql = "SELECT category_id FROM jos_vm_product_category_xref WHERE jos_vm_product_category_xref.product_id = '$product_id' ORDER BY category_id DESC LIMIT 0,10";
	  $database->setQuery($sql);
	  
	  // zistime vsetky kategorie v ktorych sa produkt nachadza
	  $cat_id =  $database->loadResultArray();
	  $cesty = array(); 
	  
	  //$this->logger("topcat", "som v topcat, novinky: ".$novinky);
	  
	  foreach ($cat_id as $id)
	  {
	   $arr = array();
	   $arr = $this->build_cats($id, $arr);
	   //$this->logger($arr, "get_topcat".$database->getErrorMsg());
	   $cesty[] = $arr;
	  }
	  
	  	  // www.test.municak.sk/street-wear/dlhe-nohavice
	  // http://www.municak.sk/26-08-2011-helikon/nohavice-tactical-pants-cierna
	  /*
	  if ($product_id == 10449)
	   {
	     $cestys = var_export($cesty, true); 
	     file_put_contents(JPATH_SITE.DS.'out.txt', $cestys); 
	     
	   }
	   */

	  
	  // nastavime radsej manualne
	  //$sql = "SELECT category_name FROM jos_vm_category WHERE category_id = '$novinky'";
	  //$database->setQuery($sql);
	  //$novinky_text = $database->loadResult();
	  
	  //$this->logger($cesty, "cesty ku produktu, novinky text: ".$novinky_text.$sql);
	  
	  foreach ($cesty as $ar)
	  {
	    $b = false;
	    foreach($ar as $title)
	    {
	     if (($title == "NOVINKY") || ($title=="DOPREDAJ"))
	      $b = true;
	    }
	    if ($b == false)
	     {
	  		if (count($ar)>0)
	  		return $ar[count($ar)-1];
	     }
	  }
	  // ak sme sa dostali az sem, tak produkt sa nachadza iba v novinkach
	  
	  if (count($cesty)>0)
	  if (count($cesty[0])>0)
	  return $cesty[0][count($cesty[0])-1];
	  
	  // povodny kod, ktory nefunguje pri vacsom ako 2-hlbke
		    	 $sql = "SELECT jos_vm_category.category_name FROM jos_vm_category, jos_vm_category_xref, jos_vm_product_category_xref, jos_vm_product WHERE  jos_vm_category.category_publish='Y' AND (jos_vm_category.category_id=jos_vm_category_xref.category_child_id OR jos_vm_category.category_id=jos_vm_category_xref.category_parent_id) AND (jos_vm_category_xref.category_parent_id=jos_vm_product_category_xref.category_id or jos_vm_category_xref.category_child_id=jos_vm_product_category_xref.category_id) AND jos_vm_product_category_xref.product_id=jos_vm_product.product_id and jos_vm_product.product_id = '$product_id' and jos_vm_category.category_id <> '$novinky'  LIMIT 0,1";
		         //$sql ="SELECT jos_vm_category.category_name FROM jos_vm_category, jos_vm_category_xref, jos_vm_product_category_xref, jos_vm_product WHERE  jos_vm_category.category_publish='Y' AND (jos_vm_category.category_id=jos_vm_category_xref.category_child_id OR jos_vm_category.category_id=jos_vm_category_xref.category_parent_id) AND (jos_vm_category_xref.category_parent_id=jos_vm_product_category_xref.category_id or jos_vm_category_xref.category_child_id=jos_vm_product_category_xref.category_id) AND jos_vm_product_category_xref.product_id=jos_vm_product.product_id and jos_vm_product.product_id = '$product_id' and jos_vm_category_xref.category_parent_id = '0' and jos_vm_category.category_id <> '$novinky' LIMIT 0,1";
			     //$sql = "SELECT #__vm_category.category_name FROM #__vm_product_category_xref, #__vm_category, #__vm_category_xref WHERE #__vm_category_xref.category_child_id=#__vm_product_category_xref.category_id AND #__vm_category_xref.category_parent_id='0' AND #__vm_category.category_publish='Y' AND #__vm_category.category_id=#__vm_category_xref.category_child_id AND #__vm_product_category_xref.product_id = '$product_id' LIMIT 0,10";
			     $database->setQuery($sql);
			     $res =  $database->loadResult();
			     return $res;
	
	}
  
  // vrati najvnorenejsiu kategoriu produktu
  function get_lowcat($product_id)
  {
  		    $database =& JFactory::getDBO();
  		    //najde kategoriu druhej alebo mensej urovne viac menej nahodne
  		    $sql = "SELECT #__vm_category.category_id FROM #__vm_product_category_xref, #__vm_category, #__vm_category_xref WHERE #__vm_category_xref.category_child_id=#__vm_product_category_xref.category_id AND #__vm_category.category_publish='Y' AND #__vm_category.category_id=#__vm_category_xref.category_child_id and #__vm_category_xref.category_parent_id <> 0 AND #__vm_product_category_xref.product_id = '".$product_id."' ";
   	        
			$database->setQuery($sql);
			
			//$res =  $database->loadResult();
			$resA = $database->loadAssocList();
			if (!empty($resA))
			{
			foreach ($resA as $res)
			{
			 
			 {
			  $arr = array();
			  $cats = $this->build_cats($res['category_id'], $arr);
			  //$x = end($cats);
			  //var_dump($x);
			  if (!empty($cats))
			  if (end($cats)!='262') 
			   {
			    //var_dump($res['category_id']); die();
			    return $res['category_id'];
			   }
			 }
			}
			//echo $product_id.'...cat...'.$res['category_id']; die();
			// nechame novinky ak inde nie je
			return $res['category_id'];
			}
			
			
			
			if (!isset($res) || ($res==false))
			{
			 // ak podkategoria neexistuje, najde top kategoriu
			  	$sql = "SELECT #__vm_category.category_id FROM #__vm_product_category_xref, #__vm_category, #__vm_category_xref WHERE #__vm_category_xref.category_child_id=#__vm_product_category_xref.category_id AND #__vm_category.category_publish='Y' AND #__vm_category.category_id=#__vm_category_xref.category_child_id AND #__vm_product_category_xref.product_id = '$product_id' LIMIT 0,1";
			  	$database->setQuery($sql);
				$res =  $database->loadResult();
				return $res;
			}

			return 0;

  }
  
  
  function get_allCats($product_id)
	{
	die('h'); 
		    global $novinky;
		    $database =& JFactory::getDBO();
		    	$sql = "SELECT jos_vm_category.category_name FROM jos_vm_category, jos_vm_category_xref, jos_vm_product_category_xref, jos_vm_product WHERE  jos_vm_category.category_publish='Y' AND (jos_vm_category.category_id=jos_vm_category_xref.category_child_id OR jos_vm_category.category_id=jos_vm_category_xref.category_parent_id) AND (jos_vm_category_xref.category_parent_id=jos_vm_product_category_xref.category_id or jos_vm_category_xref.category_child_id=jos_vm_product_category_xref.category_id) AND jos_vm_product_category_xref.product_id=jos_vm_product.product_id and jos_vm_product.product_id = '$product_id' and jos_vm_category.category_id <> '$novinky' LIMIT 0,10";
			    // $sql = "SELECT #__vm_category.category_name FROM #__vm_product_category_xref, #__vm_category, #__vm_category_xref WHERE #__vm_category_xref.category_child_id=#__vm_product_category_xref.category_id AND #__vm_category.category_publish='Y' AND #__vm_category.category_id=#__vm_category_xref.category_child_id AND #__vm_product_category_xref.product_id = '$product_id' LIMIT 0,10";
			     $database->setQuery($sql);
			     $res =  $database->loadResultArray(0);
				  
			     return $res;
	
	}
	
	function logger($msg, $caller="caller: ")
	{
		$myFile = "/domains1/do783500/public/www_root/tmp/log.txt";
		$fh = fopen($myFile, 'a');
		if ($fh)
		{
			$stringData = "$caller ".var_export($msg, true)."\n\r";
			fwrite($fh, $stringData);
			fclose($fh);
		}
	}
	
	/**
 * Strip numbers from text.
 */
function strip_numbers( $text )
{
    $urlchars      = '\.,:;\'=+\-_\*%@&\/\\\\?!#~\[\]\(\)';
    $notdelim      = '\p{L}\p{M}\p{N}\p{Pc}\p{Pd}' . $urlchars;
    $predelim      = '((?<=[^' . $notdelim . '])|^)';
    $postdelim     = '((?=[^'  . $notdelim . '])|$)';
 
    $fullstop      = '\x{002E}\x{FE52}\x{FF0E}';
    $comma         = '\x{002C}\x{FE50}\x{FF0C}';
    $arabsep       = '\x{066B}\x{066C}';
    $numseparators = $fullstop . $comma . $arabsep;
    $plus          = '\+\x{FE62}\x{FF0B}\x{208A}\x{207A}';
    $minus         = '\x{2212}\x{208B}\x{207B}\p{Pd}';
    $slash         = '[\/\x{2044}]';
    $colon         = ':\x{FE55}\x{FF1A}\x{2236}';
    $units         = '%\x{FF05}\x{FE64}\x{2030}\x{2031}';
    $units        .= '\x{00B0}\x{2103}\x{2109}\x{23CD}';
    $units        .= '\x{32CC}-\x{32CE}';
    $units        .= '\x{3300}-\x{3357}';
    $units        .= '\x{3371}-\x{33DF}';
    $units        .= '\x{33FF}';
    $percents      = '%\x{FE64}\x{FF05}\x{2030}\x{2031}';
    $ampm          = '([aApP][mM])';
 
    $digits        = '[\p{N}' . $numseparators . ']+';
    $sign          = '[' . $plus . $minus . ']?';
    $exponent      = '([eE]' . $sign . $digits . ')?';
    $prenum        = $sign . '[\p{Sc}#]?' . $sign;
    $postnum       = '([\p{Sc}' . $units . $percents . ']|' . $ampm . ')?';
    $number        = $prenum . $digits . $exponent . $postnum;
    $fraction      = $number . '(' . $slash . $number . ')?';
    $numpair       = $fraction . '([' . $minus . $colon . $fullstop . ']' .
        $fraction . ')*';
 
    return preg_replace(
        array(
        // Match delimited numbers
            '~/~' . $predelim . $numpair . $postdelim . '/u',
        // Match consecutive white space
            '/ +/u',
        ),
        ' ',
        $text );
}
/**
 * Remove HTML tags, including invisible text such as style and
 * script code, and embedded objects.  Add line breaks around
 * block-level tags to prevent word joining after tag removal.
 */
function strip_html_tags( $text )
{
    $text = preg_replace(
        array(
          // Remove invisible content
            '@<head[^>]*?>.*?</head>@siu',
            '@<style[^>]*?>.*?</style>@siu',
            '@<script[^>]*?.*?</script>@siu',
            '@<object[^>]*?.*?</object>@siu',
            '@<embed[^>]*?.*?</embed>@siu',
            '@<applet[^>]*?.*?</applet>@siu',
            '@<noframes[^>]*?.*?</noframes>@siu',
            '@<noscript[^>]*?.*?</noscript>@siu',
            '@<noembed[^>]*?.*?</noembed>@siu',
            // pridane by stAn
            '@<iframe[^>]*?>.*?</iframe>@siu', 
          // Add line breaks before and after blocks
            '@</?((address)|(blockquote)|(center)|(del))@iu',
            '@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
            '@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
            '@</?((table)|(th)|(td)|(caption))@iu',
            '@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
            '@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
            '@</?((frameset)|(frame)|(iframe))@iu',
        ),
        array(
            ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
            "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",
            "\n\$0", "\n\$0",
        ),
        $text );
    return strip_tags( $text );
}

   function getSitemapParams(&$uri)
    {
        if ($uri->getVar('format', 'html') != 'html') {
            // Handle only html links
            return array();
        }
        
        $view = $uri->getVar('view');
        
        $sm = array();
        switch ($view)
        {
            case 'product':
            case 'category':
                $indexed = $this->params->get('sm_'.$view.'_indexed', '1');
                $freq = $this->params->get('sm_'.$view.'_freq', '');
                $priority = $this->params->get('sm_'.$view.'_priority', '');
                
                if (!empty($indexed)) $sm['indexed'] = $indexed;
                if (!empty($freq)) $sm['frequency'] = $freq;
                if (!empty($priority)) $sm['priority'] = $priority;
                
                break;
        }
        
        return $sm;
    }
	
	
}
