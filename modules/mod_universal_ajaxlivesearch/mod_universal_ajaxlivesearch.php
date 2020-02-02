<?php
/*------------------------------------------------------------------------
# mod_universal_ajaxlivesearch - Universal AJAX Live Search
# ------------------------------------------------------------------------
# author    Janos Biro
# copyright Copyright (C) 2011 Offlajn.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.offlajn.com
-------------------------------------------------------------------------*/
?>
<?php
defined('_JEXEC') or die('Restricted access');

if (!extension_loaded('gd') || !function_exists('gd_info')) {
    echo "Universal AJAX Live Search needs the <a href='http://php.net/manual/en/book.image.php'>GD module</a> enabled in your PHP runtime
    environment. Please consult with your System Administrator and he will
    enable it!";
    return;
}
$document = JFactory::getDocument();
//To check the user level
$user = JFactory::getUser();
if(version_compare(JVERSION,'1.6.0','>=')) {
  $groups = implode(',', $user->getAuthorisedViewLevels());
}

$params->def('theme', 'elegant');
$theme = $params->get('theme', 'elegant');
if(is_object($theme)){ //For 1.6, 1.7, 2.5
  $params->merge(new JRegistry($params->get('theme')));
  $params->set('theme', $theme->theme);
  $theme = $params->get('theme');
}

$searchresultwidth = $params->get('resultareawidth', 250);
$dynamicresult = $params->get('dynamicresult', 0);
$productimageheight = $params->get('productimageheight', 40);
$productsperplugin = $params->get('itemsperplugin', 3);
$minchars = $params->get('minchars', 2);
$resultalign = $params->get('resultalign', 0); // 0-left 1-right
$scrolling = $params->get('scrolling', 1);
$intro = $params->get('intro', 1);
$scount = $params->get('scount', 10);
$stext = addslashes($params->get('stext'));
$catchooser = $params->get('catchooser', 1);
$searchresult = $params->get('searchresult',1);
$seemoreenabled = $params->get('seemoreenable',1);
$seemoreafter = $params->get('seemoreafter',30);

$plugins = $params->get('plugins', '');

$searchboxcaption = addslashes($params->get('searchbox', 'Search..'));
$noresultstitle = addslashes($params->get('noresultstitle', 'Results(0)'));
$noresults = addslashes($params->get('noresults', 'No results found for the keyword!'));
$seemoreresults = addslashes($params->get('seemoreresults', 'See more results...'));

$keypresswait = $params->get('stimeout', 500);
$searchformurl = JRoute::_(JURI::root(true).'/'.(version_compare(JVERSION,'1.6.0','>=') ? 'index' : 'index2').".php");

/*
Build the Javascript cache and scopes
*/
require_once(dirname(__FILE__).'/classes/cache.class.php');
$cache = new OfflajnSearchThemeCache('default', $module, $params);
/*
Build the CSS
*/
$cache->addCss(dirname(__FILE__) .'/themes/clear.css.php');
$cache->addCss(dirname(__FILE__) .'/themes/'.$theme.'/theme.css.php');

/*
Load image helper
*/
require_once(dirname(__FILE__).'/helper/Helper.class.php');

/*
Set up enviroment variables for the cache generation
*/
$module->url = JURI::root(true).'/modules/'.$module->module.'/';
$themeUrl = $module->url.'themes/'.$theme.'/';
$cache->addCssEnvVars('themeurl', $themeUrl);
$cache->addCssEnvVars('module', $module);
$helper = new OfflajnAJAXSearchHelper($cache->cachePath);
$cache->addCssEnvVars('helper', $helper);
$cache->addCssEnvVars('productsperplugin', $productsperplugin);
$cache->addCssEnvVars('searchresultwidth', $searchresultwidth);

$cache->addJs(dirname(__FILE__).'/themes/AJAXSearchBase.js');
$cache->addJs(dirname(__FILE__).'/themes/'.$theme.'/js/engine.js');
if($params->get('dojo', 0) == 1){
  $document->addScript(JURI::root().'modules/mod_universal_ajaxlivesearch/engine/localdojo.js');
}else{
  $document->addScript(JURI::root().'modules/mod_universal_ajaxlivesearch/engine/dojo.js');
  $document->addScript('https://ajax.googleapis.com/ajax/libs/dojo/1.6/dojo/dojo.xd.js');
}

$cache->addCustomCss($params->get('customcss'));
$cache->addCustomJs($params->get('customjs'));
/*
Add cached contents to the document
*/
$cacheFiles = $cache->generateCache();
$document->addStyleSheet($cacheFiles[0]);
$document->addScript($cacheFiles[1]);

/*
  Check that the keyword suggestion parameter is enabled, and also check that the search gathering enabled in the default search component
*/
$comparams = JComponentHelper::getParams('com_search');
$keywordSuggestion = 0;
if($params->get('keywordsuggestion', 1) && $comparams->get('enabled', 1)) {
  $keywordSuggestion = 1;
}
//$document->addScript('modules/'.$module->module.'/themes/'.$theme.'/js/engine.js');

  //fix if user provide percent value for the result width
if(strpos($searchresultwidth, "%") !== false) {
  $searchresultwidth = 2000;
  $dynamicresult = 1;
}
$document->addCustomTag("<script type=\"text/javascript\">
dojo.addOnLoad(function(){
    document.search = new AJAXSearch".$theme."({
      id : '".$module->id."',
      node : dojo.byId('offlajn-ajax-search".$module->id."'),
      searchForm : dojo.byId('search-form".$module->id."'),
      textBox : dojo.byId('search-area".$module->id."'),
      suggestBox : dojo.byId('suggestion-area".$module->id."'),
      searchButton : dojo.byId('ajax-search-button".$module->id."'),
      closeButton : dojo.byId('search-area-close".$module->id."'),
      searchCategories : dojo.byId('search-categories".$module->id."'),
      productsPerPlugin : $productsperplugin,
      dynamicResult : '$dynamicresult',
      searchRsWidth : $searchresultwidth,
      searchImageWidth : '".intval($params->get('imagew', 180))."',
      minChars : $minchars,
      searchBoxCaption : '$searchboxcaption',
      noResultsTitle : '$noresultstitle',
      noResults : '$noresults',
      searchFormUrl : '$searchformurl',
      enableScroll : '$scrolling',
      showIntroText: '$intro',
      scount: '$scount',
      lang: '".JRequest::getCmd('lang')."',
      stext: '$stext',
      moduleId : '$module->id',
      resultAlign : '$resultalign',
      targetsearch: '".$params->get('targetsearch', 0)."',
      linktarget: '".$params->get('linktarget', 0)."',
      keypressWait: '$keypresswait',
      catChooser : $catchooser,
      searchResult : $searchresult,
      seemoreEnabled : $seemoreenabled,
      seemoreAfter : $seemoreafter,
      keywordSuggestion : '".$keywordSuggestion."',
      seeMoreResults : '$seemoreresults',
      resultsPerPage : '".$params->get('resultsperpage', 4)."',
      resultsPadding : '".$params->get('resultspadding', 10)."',
      controllerPrev : '".JTEXT::_('SEARCH_PREV')."',
      controllerNext : '".JTEXT::_('SEARCH_NEXT')."',
      fullWidth : '1',
      resultImageWidth : '".$params->get('imagew', 180)."',
      resultImageHeight : '".$params->get('imageh', 140)."',
      showCat : '".$params->get('showcat', 1)."',
      voicesearch : '".$params->get('voicesearch', 0)."'
    })
  });</script>"
);
if(!function_exists('buildPluginNameArray')){
  function buildPluginNameArray($a){
    $newa = array();
    $tmp = '';
    foreach($a AS $k => $v){
      ($k % 2 == 0) ? $tmp = $v : $newa[$tmp] = $v;
    }
    return $newa;
  }
}
$db = JFactory::getDBO();
if (version_compare(JVERSION,'1.6.0','>=')){
  $pluginnames = $params->get('plugins');
  $pluginnames = @$pluginnames->pluginsname? $pluginnames->pluginsname : array() ;
  $plugins = (array)$plugins;
}else{
  $pluginnames = $params->get('pluginsname');
}
$pluginnames = buildPluginNameArray($pluginnames);

$enabledplugins = (array)$plugins;

if (version_compare(JVERSION,'1.6.0','>=')){
  $db->setQuery("SELECT extension_id, name FROM #__extensions WHERE type = 'plugin' AND folder = 'search' AND enabled =1 AND access IN (".$groups.") ORDER BY ordering");
}else{
  $db->setQuery("SELECT id, name FROM #__plugins WHERE folder = 'search' AND published=1 ORDER BY ordering");
}
$pluginlist = $db->loadRowList();
?>

<div id="offlajn-ajax-search<?php echo $module->id; ?>" class="<?php echo $params->get('moduleclass_sfx') ?>">
  <div class="offlajn-ajax-search-container">
  <?php
    $itemid = $params->get('iid') ? "&Itemid=".$params->get('iid') : "";
    $formUrl = "option=com_search";
    if($params->get('targetsearch', 0) == 6) {
      $formUrl = "option=com_jreviews&controller=search&url=search-results";
    } else if($params->get('targetsearch', 0) == 5){
      $formUrl = "option=com_finder&view=search";
    }  else if($params->get('targetsearch', 0) == 8) {
      $formUrl = "option=com_djclassifieds&view=items&se=1";
    }  else if($params->get('targetsearch', 0) == 1) {
      $formUrl = "option=com_virtuemart&view=category&virtuemart_category_id=0&search=true&limitstart=0";
    } else if($params->get('targetsearch', 0) == 9) {
      $formUrl = "option=com_adsmanager&view=result";
    } else if($params->get('targetsearch', 0) == 4) {
      $formUrl = "option=com_jshopping&controller=search&task=result";
    }
  ?>
  <form id="search-form<?php echo $module->id; ?>" action="<?php echo JRoute::_('index.php?'.$formUrl.$itemid); ?>" method="get" onSubmit="return false;">
    <div class="offlajn-ajax-search-inner">
    <?php if ($catchooser== 1) : ?><div class="category-chooser"><div class="arrow"></div></div><?php endif; ?>
    <?php
      switch($params->get('targetsearch', 0)){
        case 0:
        case 3:
        ?>
        <input type="text" name="searchword" id="search-area<?php echo $module->id; ?>" value="" autocomplete="off" />
        <input type="text" tabindex="-1" name="searchwordsugg" id="suggestion-area<?php echo $module->id; ?>" value="" autocomplete="off" />
        <input type="hidden" name="option" value="com_search" />
        <?php
          break;
        case 1:
        ?>
        <input type="text" name="keyword" id="search-area<?php echo $module->id; ?>" value="" autocomplete="off" />
        <input type="text" tabindex="-1" name="searchwordsugg" id="suggestion-area<?php echo $module->id; ?>" value="" autocomplete="off" />
        <input type="hidden" name="option" value="com_virtuemart" />
        <input type="hidden" name="page" value="shop.browse" />
        <input type="hidden" name="view" value="category" />
        <input type="hidden" name="virtuemart_category_id" value="0" />
        <?php
          break;
        case 2:
        ?>
        <input type="text" name="searchword" id="search-area<?php echo $module->id; ?>" value="" autocomplete="off" />
        <input type="text" tabindex="-1" name="searchwordsugg" id="suggestion-area<?php echo $module->id; ?>" value="" autocomplete="off" />
        <input type="hidden" name="option" value="com_redshop" />
        <input type="hidden" name="view" value="search" />
        <?php
          break;
        case 4:
        ?>
        <input type="text" name="search" id="search-area<?php echo $module->id; ?>" value="" autocomplete="off" />
        <input type="text" tabindex="-1" name="searchwordsugg" id="suggestion-area<?php echo $module->id; ?>" value="" autocomplete="off" />
        <?php
          break;
         case 5:
        ?>
        <input type="text" name="q" id="search-area<?php echo $module->id; ?>" value="" autocomplete="off" />
        <input type="text" tabindex="-1" name="searchwordsugg" id="suggestion-area<?php echo $module->id; ?>" value="" autocomplete="off" />
        <input type="hidden" name="option" value="com_finder" />
        <input type="hidden" name="view" value="search" />
        <?php
          break;
        case 6:
        ?>
        <input type="text" name="keywords" id="search-area<?php echo $module->id; ?>" value="" autocomplete="off" />
        <input type="text" tabindex="-1" name="searchwordsugg" id="suggestion-area<?php echo $module->id; ?>" value="" autocomplete="off" />
        <?php
          break;
        case 7:
        ?>
        <input type="text" name="search" id="search-area<?php echo $module->id; ?>" value="" autocomplete="off" />
        <input type="text" tabindex="-1" name="searchwordsugg" id="suggestion-area<?php echo $module->id; ?>" value="" autocomplete="off" />
        <input type="hidden" name="option" value="com_mijoshop" />
        <input type="hidden" name="view" value="search" />
        <?php
          break;
       case 8:
        ?>
        <input type="text" name="search" id="search-area<?php echo $module->id; ?>" value="" autocomplete="off" />
        <input type="text" tabindex="-1" name="searchwordsugg" id="suggestion-area<?php echo $module->id; ?>" value="" autocomplete="off" />
        <input type="hidden" name="se" value="1" />
        <?php
          break;
	     case 9:
        ?>
        <input type="text" name="tsearch" id="search-area<?php echo $module->id; ?>" value="" autocomplete="off" />
        <input type="text" tabindex="-1" name="searchwordsugg" id="suggestion-area<?php echo $module->id; ?>" value="" autocomplete="off" />
        <input type="hidden" name="new_search" value="1" />
        <?php
          break;

      }
    ?>
      <div id="search-area-close<?php echo $module->id; ?>"></div>
      <div id="ajax-search-button<?php echo $module->id; ?>"><div class="magnifier"></div></div>
      <div class="ajax-clear"></div>
    </div>
  </form>
  <div class="ajax-clear"></div>
  </div>
    <?php if ($catchooser==1) : ?>
    <div id="search-categories<?php echo $module->id; ?>">
      <div class="search-categories-inner">
        <?php
            $i=0;
            foreach ($pluginlist as $plugin) {
            	if (count($plugin)){
                $selected="";
                $pluginname="";
                if(in_array($plugin[0], $enabledplugins)) $selected="selected"; // Skip if the plugin disabled in the module configuration.
                if((count($pluginlist))-1==$i) $selected.=" last";

                if (isset($pluginnames[$plugin[0]])){
                    $pluginname=$pluginnames[$plugin[0]];
                }else{
                    $pluginname=$plugin[1];
                }
                echo '<div id="search-category-'.$plugin[0].'" class="'.$selected.'">'.$pluginname.'</div>';
                $i++;
              }
            }
        ?>
      </div>
    </div>
    <?php endif;?>
</div>
<div class="ajax-clear"></div>
<svg style="position:absolute" height="0" width="0"><filter id="searchblur"><feGaussianBlur in="SourceGraphic" stdDeviation="3"/></filter></svg>
