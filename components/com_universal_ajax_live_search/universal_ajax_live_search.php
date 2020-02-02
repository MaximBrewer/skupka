<?php
/*------------------------------------------------------------------------
# com_universal_ajaxlivesearch - Universal AJAX Live Search
# ------------------------------------------------------------------------
# author    Janos Biro
# copyright Copyright (C) 2011 Offlajn.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.offlajn.com
-------------------------------------------------------------------------*/
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
ob_end_clean();

if(isset($_SERVER['HTTP_X_REQUESTED_WITH']))
  unset($_SERVER['HTTP_X_REQUESTED_WITH']); // MAGEBRIDGE fix

$jinput = JFactory::getApplication()->input->get;

if ($jinput->getString('suggest')) {
	$db = JFactory::getDbo();

	$params = JComponentHelper::getParams('com_search');
	$enable_log_searches = $params->get('enabled');
  if(defined('DEMO')){
    $enable_log_searches = 1;
  }
  if(version_compare(JVERSION,'1.6.0','ge')){
		$search_term = $db->escape(trim($jinput->getString('search_exp')));
  }else{
		$search_term = mysql_real_escape_string(trim($jinput->getString('search_exp')));
  }

	if($search_term==""){
		print_r(json_encode(""));exit;
  }

	if (@$enable_log_searches)
	{
		$db = JFactory::getDbo();
		$query = 'SELECT search_term as suggestion'
		. ' FROM #__core_log_searches'
		. ' WHERE LOWER(search_term) LIKE "'.$search_term.'%"'
    . ' AND LENGTH(search_term) >=4'
    . ' AND LENGTH(search_term) >= LENGTH("'.$search_term.'")+2'
		. ' ORDER BY hits DESC'
		;
		$db->setQuery($query);
		$hits = $db->loadObjectList();
		print_r(json_encode($hits));exit;
	}
}

if ($jinput->getString('search_exp')) {

  require_once JPATH_ADMINISTRATOR.'/components/com_search/helpers/search.php';
  require_once( dirname(__FILE__).'/helpers/functions.php' );
  require_once( dirname(__FILE__).'/helpers/caching.php' );
  require_once JPATH_SITE.'/modules/mod_universal_ajaxlivesearch/params/library/parameter.php';

  if($jinput->getString('savesuggest')) {
    SearchHelper::logSearch($jinput->getString('search_exp'));
    exit;
  }

  $areas="";
  $db = JFactory::getDBO();
  $pluginlist = array();
  $searchresult = array();
  $moduleid  = $jinput->getInt('module_id');
  settype($moduleid, 'integer');
  $q =  sprintf("SELECT params, id FROM #__modules WHERE id = %d ", $jinput->getInt('module_id'));


  $showPrice = "";
  $db->setQuery($q);
  $res = $db->loadResult();
  $params = new SearchOfflajnJParameter("");
  parseParams($params, $res);
  $params->def('theme', 'elegant');
  $theme = $params->get('theme', 'elegant');
  if(is_object($theme)){ //For 1.6, 1.7, 2.5
    $params->merge(new JRegistry($params->get('theme')));
    $params->set('theme', $theme->theme);
    $theme = $params->get('theme');
    $showPrice = $params->get('showprice', '1');
  }
  $plugins = (array)$params->get('plugins', '');

  $imageresizemode = (int) $params->get('imagetype', 1);
  switch ($params->get('theme')) {
    case "flat" :
    case "minimal" :
      $imageresizemode = (int) $params->get('imagetype', 0);
      break;
  }

//  $imageresizemode = $params->get('theme')!="minimal"; // check which image resizer must be used

  $suggestionlang = $params->get('suggestionlang', 'en');
  $introlength = $params->get('introlength', 50);
  $catchooser = $params->get('catchooser', 1);
  jimport( 'joomla.application.component.model' );
  JPluginHelper::importPlugin('search');
  $imagecache = new OfflajnImageCaching;

  $order = $params->get('order', 'newest');
  $mode = $params->get('searchmode', 'all');
  $searchparams = array($jinput->getString('search_exp'), $mode, $order); // next two parameters for exprt search plugins

  $dispatcher = JDispatcher::getInstance();

  $results = null;
  if(version_compare(JVERSION,'1.6.0','ge')) {
    $results = $dispatcher->trigger( 'onContentSearch', $searchparams );
    $db->setQuery("SELECT extension_id AS id, name FROM #__extensions WHERE type = 'plugin' AND folder = 'search' AND enabled =1 ORDER BY ordering");

    $pluginnames = $params->get('plugins');
    $pluginnames = @$pluginnames->pluginsname? $pluginnames->pluginsname : array() ;
  }else{
    $results = $dispatcher->trigger( 'onSearch', $searchparams );
    $db->setQuery("SELECT id, name FROM #__plugins WHERE folder = 'search' AND published=1 ORDER BY ordering");

    $pluginnames = $params->get('pluginsname');
  }

  $pluginlist = $db->loadRowList();
  $pluginnames = buildPluginNameArray($pluginnames);

  if($catchooser==1)
    $plugins = $jinput->get('categ', array(), null);

  // $plugin : the plugin's search result
  foreach ($results as $pluginkey=>$plugin) {
  	if (count($plugin)){
      if(is_array($plugins)){
        if(!in_array($pluginlist[$pluginkey][0], $plugins)) continue; // Skip if the plugin disabled in the module configuration.
      }else{
        if($pluginlist[$pluginkey][0]!= $plugins) continue; // Skip if the plugin disabled in the module configuration.
      }

      $pluginname = isset($pluginnames[$pluginlist[$pluginkey][0]]) ? $pluginnames[$pluginlist[$pluginkey][0]] : $pluginlist[$pluginkey][1];
      $i=0;
      foreach ($plugin as $key=>$value) {
        $searchresult[$pluginname][$i] = new stdclass();
        if($params->get('image', 1)){
          $image_url = isset($value->product_full_image) ? $value->product_full_image : (isset($value->image) ? $value->image : null);
          if($image_url){ //If it is a product get the product image
            if (( !empty($value->add_path) && $value->add_path==true)||
                ( !empty($value->vmversion) && $value->vmversion=="VM2") ){ //check for VirtueMart2 Search Plugin
              $searchresult[$pluginname][$i]->product_img = $imagecache->generateImage($image_url, intval($params->get('imagew', 60)), intval($params->get('imageh', 60)), $value->title, $imageresizemode);
            }else{
              $searchresult[$pluginname][$i]->product_img = @$imagecache->generateImage($image_url, intval($params->get('imagew', 60)), intval($params->get('imageh', 60)), $value->title, $imageresizemode);
            }
          }elseif ($value->text){ //If it is an article get the first image
            preg_match_all('/<img.*?src=["\'](.*?((jpg)|(png)|(jpeg)|(gif)))["\'].*?>/i',$value->text, $result);
            if (isset($result[1]) && isset($result[1][0])){
              $result[1][0] = urldecode($result[1][0]);
              $searchresult[$pluginname][$i]->product_img = $imagecache->generateImage($result[1][0], intval($params->get('imagew', 60)), intval($params->get('imageh', 60)), $value->title, $imageresizemode);
            }else{
              $searchresult[$pluginname][$i]->product_img = $imagecache->generateImage('/modules/mod_universal_ajaxlivesearch/images/'.$params->get('noimage'), intval($params->get('imagew', 60)), intval($params->get('imageh', 60)), $value->title, $imageresizemode);
          }
          }else{
            $searchresult[$pluginname][$i]->product_img = $imagecache->generateImage('/modules/mod_universal_ajaxlivesearch/images/'.$params->get('noimage'), intval($params->get('imagew', 60)), intval($params->get('imageh', 60)), $value->title, $imageresizemode);
          }
        }
        $searchresult[$pluginname][$i]->title = $value->title;
        if($params->get('triggercontent', 0)) {
          $value->text = $text = JHtml::_('content.prepare', $value->text);
        }
        $searchresult[$pluginname][$i]->text = LiveSearchHelper::prepareSearchContent($value->text, $jinput->getString('search_exp'),$introlength);
        $searchresult[$pluginname][$i]->href = html_entity_decode(JRoute::_($value->href));
        $searchresult[$pluginname][$i]->id = md5($searchresult[$pluginname][$i]->href.$searchresult[$pluginname][$i]->text.$value->title);
        $searchresult[$pluginname][$i]->price = ($showPrice) ? @$value->price : "";
        $i++;
      }
    }
  }

  if ((!is_array($searchresult) || count($searchresult)<=0) &&  $params->get('suggest', 0)) {
    $sugg = "";
    if(function_exists("curl_init") && $params->get('usecurl', 0)==1 && curl_init("http://google.com")!==false) {
      $curl = curl_init("http://www.google.com/complete/search?output=toolbar&ie=utf-8&oe=utf-8&hl=$suggestionlang&q=".urlencode($jinput->getString('search_exp')));
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
      $sugg = curl_exec ($curl);
      curl_close ($curl);
    } else if(@file_get_contents("http://google.com")!==false && $params->get('usecurl', 0)==0) {
      $sugg = file_get_contents("http://www.google.com/complete/search?output=toolbar&ie=utf-8&oe=utf-8&hl=$suggestionlang&q=".urlencode($jinput->getString('search_exp')));
    }
    $tags = explode('<suggestion data="', $sugg);
    $i = 0;
    $searchresult['nores'] = array();
    foreach ($tags as $tag) {
      if ($i != 0) {
        $temp = explode('"/>', $tag);
        $tag = $temp[0];
        $searchresult['nores'][$i]->tag = $tag;
      }
      $i++;
    }
    $searchresult['nores'] = array_slice($searchresult['nores'], 0, $params->get('scount', 10));
  }
  echo "startofofflajnsearchresult".json_encode($searchresult)."endofofflajnsearchresult";
  exit;
}
?>