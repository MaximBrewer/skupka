<?php
/*-------------------------------------------------------------------------
# plg_offlajnparams - Offlajn Params
# -------------------------------------------------------------------------
# @ author    Balint Polgarfi
# @ copyright Copyright (C) 2016 Offlajn.com  All Rights Reserved.
# @ license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @ website   http://www.offlajn.com
-------------------------------------------------------------------------*/
?><?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$er = error_reporting();
if ($er & E_STRICT || $er & E_DEPRECATED)
  error_reporting($er & ~E_STRICT & ~E_DEPRECATED);

jimport( 'joomla.plugin.plugin' );
require_once dirname(__FILE__).'/offlajnjoomlacompat.php';

$option = isset($_REQUEST['option']) ? $_REQUEST['option'] : '';
$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';

if ($option == 'offlajnupload')
  require_once(dirname(__FILE__).'/imageuploader.php');
switch ($task) {
  case 'offlajnimport': require_once(dirname(__FILE__).'/importexport.php');
  case 'offlajnmenu': require_once(dirname(__FILE__).'/menuloader.php');
  case 'offlajninfo': require_once(dirname(__FILE__).'/generalinfo.php');
  case 'offlajnnews': require_once(dirname(__FILE__).'/relatednews.php');
}

require_once(dirname(__FILE__).'/formrenderer.php');

class  plgSystemOfflajnParams extends JPlugin
{
  function __construct(& $subject, $config){
		parent::__construct($subject, $config);
	}

  function onBeforeCompileHead() {
    $gsap = array();
    $latest = '';
    $version = '0.0.0';
    $scripts = &JFactory::getDocument()->_scripts;
    // get latest GSAP script
    foreach ($scripts as $src => $attr) {
      if (preg_match('#^(https?:)?//cdnjs\.cloudflare\.com/ajax/libs/gsap/(\d+\.\d+\.\d+)/(TweenMax|TweenLite)\.min\.js(\?.*)?$#', $src, $match)) {
        $gsap[] = $src;
        if (version_compare($version, $match[2], '<')) {
          $latest = $src;
          $version = $match[2];
        }
      }
    }
    $gsapCdn = $this->params->get('gsap_cdn', 0);
    // remove older GSAP scripts
    foreach ($gsap as $src) {
      if (!$gsapCdn || $src != $latest) {
        unset($scripts[$src]);
      }
    }
    if (!$gsapCdn && !empty($gsap)) {
      $scripts = array(rtrim(JURI::root(true), '/').'/plugins/system/offlajnparams/compat/greensock.js' => array('mime' => 'text/javascript', 'defer' => '', 'async' => '')) + $scripts;
    }
  }

  function addNewTab($id, $title, $text, $position = 'last', $class=''){
    global $offlajnParams;
    if($position != 'first') $position = 'last';
    $offlajnParams[$position][] = self::renderNewTab($id, $title, $text, $class);
  }

  function renderNewTab($id, $title, $text, $class=''){
    ob_start();
    if(version_compare(JVERSION,'1.6.0','ge'))
      include(dirname(__FILE__).'/tab16.tpl.php');
    else
      include(dirname(__FILE__).'/tab15.tpl.php');

    return ob_get_clean();
  }

  function getElementById(&$dom, $id){
    $xpath = new DOMXPath($dom);
    return $xpath->query("//*[@id='$id']")->item(0);
  }

  function getElementByClass(&$dom, $class, $item = 0){
    $xpath = new DOMXPath($dom);
    return $xpath->query("//*[@class='$class']")->item($item);
  }

	function onAfterDispatch(){
    global $offlajnParams, $offlajnDashboard;
    $app = JFactory::getApplication();
    if (!defined('OFFLAJNADMIN') || isset($_REQUEST['output']) && $_REQUEST['output'] == 'json') {
        return;
    }

    $doc = JFactory::getDocument();
    $c = $doc->getBuffer('component');

		$dom = new DomDocument();
    if(function_exists("mb_convert_encoding")) {
      @$dom->loadHtml('<?xml encoding="UTF-8"><div>'.mb_convert_encoding($c, 'HTML-ENTITIES', "UTF-8").'</div>');
    } else {
      @$dom->loadHtml('<?xml encoding="UTF-8"><div>'.htmlspecialchars_decode(utf8_decode(htmlentities($c, ENT_COMPAT, 'utf-8', false))).'</div>');
    }
		$lis = array();

    $moduleparams = "";
    $advanced = JRequest::getCmd('option') == 'com_advancedmodules';

    if(version_compare(JVERSION,'3.0.0','ge') && !$this->getElementById($dom, 'module-sliders')) {

      // Joomla 3.0.3 fix
      if(version_compare(JVERSION,'3.1.99','ge')) {
        $moduleparams = $this->getElementByClass($dom, 'span9');
      }elseif(version_compare(JVERSION,'3.0.3','ge')) {
        $moduleparams = $this->getElementById($dom, 'collapse0');
      }else{
        $moduleparams = $this->getElementById($dom, 'options-basic');
      }
      if ($advanced){
        $moduleparams = version_compare(JVERSION,'3.2.2','ge')?
          $this->getElementByClass($dom, 'span9') :
          $this->getElementByClass($dom, 'span6', 1);
      }
      if($moduleparams){
        $element = $dom->createElement('div');
        $element->setAttribute ('id','content-box');
        $moduleparams->appendChild($element);
        $moduleparams = $element;
        $element = $dom->createElement('div');
        $element->setAttribute ('id','module-sliders');
        $element->setAttribute ('class','pane-sliders');
        $moduleparams->appendChild($element);
        $moduleparams = $element;
      }
    }elseif(version_compare(JVERSION,'1.6.0','ge')) {
      $moduleparams = $this->getElementById($dom, 'module-sliders');
    }else{
      $moduleparams = $this->getElementById($dom, 'menu-pane');
    }
    if($moduleparams){
      $removed = array();
      while($cNode = $moduleparams->firstChild){
        $removed[] = $moduleparams->removeChild($cNode);
      }
      if(version_compare(JVERSION,'1.6.0','ge')) {
        array_splice($removed, 0, 2);
      }else{
        array_splice($removed, 0, 1);
      }
      $html = '<div>';
      $html.= isset($offlajnDashboard) ? $offlajnDashboard : '';
      $html.= isset($offlajnParams['first']) && is_array($offlajnParams['first']) ? implode("\n",$offlajnParams['first']) : '';
      $html.= isset($offlajnParams['last']) && is_array($offlajnParams['last']) ? implode("\n",$offlajnParams['last']) : '';
      $html.= '</div>';
      $tabsDom = new DomDocument();
      if(function_exists("mb_convert_encoding")) {
        @$tabsDom->loadHtml('<?xml encoding="UTF-8">'.mb_convert_encoding($html, 'HTML-ENTITIES', "UTF-8"));
      } else {
        @$tabsDom->loadHtml('<?xml encoding="UTF-8">'.htmlspecialchars_decode(utf8_decode(htmlentities($html, ENT_COMPAT, 'utf-8', false))));
      }

      $node = $dom->importNode( $tabsDom->getElementsByTagName('div')->item(0), true );
      while($cNode = $node->firstChild){
        if(@$cNode->tagName == 'div')
          $moduleparams->appendChild($cNode);
        else
          $node->removeChild($cNode);
      }

      if(count($removed) > 0){
        foreach($removed as $r){
          if($r instanceof DOMElement){
            $r->setAttribute("class", $r->getAttribute("class")." legacy");
            $moduleparams->appendChild($r);
          }
        }
      }

      if(!version_compare(JVERSION,'1.6.0','ge')) {
        $tables = $dom->getElementsByTagName('table');
        foreach ($tables as $table) {
          $table->setAttribute("cellspacing", "0");
        }
      }

      $params = $moduleparams->getElementsByTagName('h3');
      foreach ($params as $param) {
        $span = $param->getElementsByTagName('span')->item(0);
        $titleWords = explode(" ", $span->textContent);
        $titleWords[count($titleWords)-1] = "<b>".$titleWords[count($titleWords)-1]."</b>";
        $newTitle = implode(' ', $titleWords);

        $span->removeChild($span->firstChild);
        $newText = $dom->createCDATASection($newTitle);
        $span->appendChild($newText);
      }

      $j=0;
      foreach ($moduleparams->childNodes as $param) {
        $param->setAttribute("id", "offlajnpanel-".$j);
        $j++;
      }
    }

    if (!isset($doc->_script['text/javascript'])) $doc->_script['text/javascript'] = array();
    $doc->_script['text/javascript'] = preg_replace("/window.addEvent.*?pane-toggler.*?\}\);.*?\}\);/i", '',  $doc->_script['text/javascript']);

    $doc->_script['text/javascript'].='
      window.addEvent && window.addEvent("domready", function(){
        if(document.formvalidator)
          document.formvalidator.isValid = function() {return true;};
      });';

    if(version_compare(JVERSION,'3.0.0','ge')) {
      if($moduleparams && $moduleparams->parentNode){
        function getInnerHTML($Node){
             $Document = new DOMDocument();
             $Document->appendChild($Document->importNode($Node,true));
             return $Document->saveHTML();
        }
        $nc = getInnerHTML($moduleparams->parentNode);
      }else{
        $nc = $dom->saveHTML();
      }

      $nc = preg_replace("/.*?<body>/si", '',  $nc, 1);
      $nc = preg_replace("/<\/body>.*/si", '', $nc, 1);

      $pattern = '/<div\s*class="tab-pane"\s*id="options-basic".*?>/';

      if (version_compare(JVERSION,'3.1.99','ge')) {
        $pattern = '/<div\s*class="span9".*?>/';
      } elseif (version_compare(JVERSION,'3.0.3','ge')) {
        $pattern = '/<div\s*class="accordion-body collapse in"\s*id="collapse0".*?>/';
      }
      if ($advanced) {
        $pattern = version_compare(JVERSION,'3.2.2', 'ge')?
          '/<div\s*class="span9".*?>/' :
          '/<\/div>\s*<div\s*class="span6".*?>/';
      }
      preg_match($pattern, $c, $matches);
      if(count($matches) > 0){
        $c = str_replace($matches[0], $matches[0].$nc, $c);
      }else{
        $c = $nc;
      }
    }else{
      $c = $dom->saveHtml();
      $c = preg_replace("/.*?<body><div>/si", '',  $c, 1);
      $c = preg_replace("/<\/div><\/body>.*/si", '',  $c, 1);
    }

    $doc->setBuffer($c, 'component');
	}

	function onAfterInitialise()
	{
		$app = JFactory::getApplication();
    $db = JFactory::getDbo();

    if ($app->isAdmin() && @$_REQUEST['option'] == 'com_installer' && @$_REQUEST['view'] == 'update') {
      $db->setQuery("SELECT * FROM #__updates WHERE detailsurl LIKE 'http://offlajn.com/%'");
      $updates = json_encode( $db->loadObjectList('update_id') );
      $doc = JFactory::getDocument();
      if (version_compare(JVERSION, '3.0.0', 'l')) {
        $doc->addScript('https://ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.min.js');
      } else JHtml::_('jquery.framework');
      $doc->addScriptDeclaration(";(function($) {
        offUpdates = $updates;
        offEnableUpdate = function(cid, key) {
          offUpdates[cid].extra_query = 'key='+key;
          offUpdates[cid].node.checked = true;
          $.post('index.php?option=com_installer&task=update.extra_query', {q: 'key='+key, cid: cid}, function() {
            alert('Valid key, Thank you!');
          });
        };
        $(function() {
          $('input[name=\"cid[]\"]').each(function() {
            if (this.value in offUpdates) {
              this.parentNode.nextElementSibling.innerHTML += '<a href=\"https://www.youtube.com/watch?v=Fr6ooBbq9QE\" target=\"_blank\"><span class=\"icon-info\" style=\"vertical-align:middle\"></span>[More Info]</a>';
            }
          });
        });
        $(document.documentElement).on('change', 'input[name=\"cid[]\"]', function(e) {
          if (this.checked && this.value in offUpdates) {
            var cid = this.value;
            var title = offUpdates[cid].name + '\\n';
            if (offUpdates[cid].extra_query) {
              var end = parseInt(offUpdates[cid].extra_query.substr(4, 6), 36) * 1000;
              if (end < Date.now()) {
                offUpdates[cid].extra_query = '';
                alert(title + '\\nYour license has been expired on '+ new Date(end).toDateString() +'.\\nPlease renew your license for updates!');
              }
            }
            if (!offUpdates[cid].extra_query) { // get update key
              this.checked = false;
              var key = prompt(title + 'Please insert your update key:');
              if (key) {
                if (key.length != 38) return alert('Invalid key!');
                offUpdates[cid].node = this;
                offUpdates[cid].node.disabled = true;
                var pkg = offUpdates[cid].detailsurl.match(/([^\/]+)\.xml?$/)[1];
                $.getScript(location.protocol+'//offlajn.com/index2.php?option=com_update&task=validate&cid='+cid+'&pkg='+pkg+'&key='+key, function() {
                  offUpdates[cid].node.disabled = false;
                });
              }
            }
          }
        }).on('change', 'input[name=\"checkall-toggle\"]', function() {
          $('input[name=\"cid[]\"]').change();
        });
      })(jQuery);");
    }
    if ($app->isAdmin() && @$_REQUEST['option'] == 'com_installer' && @$_REQUEST['task'] == 'update.extra_query') {
      $cid = (int) $_REQUEST['cid'];
      $q = @$_REQUEST['q'];
      if (!preg_match('/key=\w+/', $q)) die('nok');
      $db->setQuery("UPDATE #__updates
        SET extra_query = '$q' WHERE update_id = $cid AND detailsurl LIKE 'http://offlajn.com/%' LIMIT 1");
      $db->query();
      $db->setQuery("UPDATE #__update_sites
        SET extra_query = '$q' WHERE update_site_id = (SELECT update_site_id FROM #__updates
          WHERE update_id = $cid AND detailsurl LIKE 'http://offlajn.com/%' LIMIT 1) LIMIT 1");
      $db->query();
      die('ok');
    }


		if(!$app->isAdmin() || !isset(${'_SESSION'}['offlajnurl']) || !isset(${'_SESSION'}['offlajnurl'][$_SERVER['REQUEST_URI']])){
			return;
		}
    //if(version_compare(JVERSION,'3.0.0','ge')) return;

		$template_style_id = 2;
		if(version_compare(JVERSION,'1.6.0','ge')) {
		  if(version_compare(JVERSION,'3.0.0','ge')) {
        $db->setQuery('SELECT template, params FROM #__template_styles WHERE template LIKE "isis"');
      } else {
        $db->setQuery('SELECT template, params FROM #__template_styles WHERE `client_id` = 1 AND `id`= '. (int)$template_style_id.' ORDER BY id ASC');
      }
		  $row = $db->loadObject();

  		if(!$row){
  			return;
  		}

  		if(empty($row->template)){
  			return;
  		}

  		if(file_exists(JPATH_THEMES.'/'.$row->template)){
  		  $tmpl = $app->getTemplate(true);
  		  $tmpl->template = $row->template;
    		$tmpl->params = new JRegistry($row->params);
  		}
		}else{
		  if($app->getTemplate() != 'khepri'){
  		  $db->setQuery('UPDATE #__templates_menu SET template = "khepri" WHERE menuid = 0 AND client_id = 1');
  		  $db->query();
        header('LOCATION: '.$_SERVER['REQUEST_URI']);
        exit;
  		}
		}
	}

}
