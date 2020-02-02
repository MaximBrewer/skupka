<?php

/*
  # author Roland Soos
  # copyright Copyright (C) Nextendweb.com. All Rights Reserved.
  # @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');
?><?php

jimport('joomla.plugin.plugin');

class plgSystemNextendLibrary extends JPlugin {

    var $compiled;
    var $translate = '';

    function plgSystemNextendLibrary(&$subject, $config) {

	$this->compiled = false;
	parent::__construct($subject, $config);
    }

    function onAfterInitialise() {
	if (isset($_REQUEST['nextendajax'])) {
	    jimport('nextend.library');
	    jimport('nextend.ajax.ajax');
	}
    }

    /*
      Artisteer jQuery fix
     */

    function onAfterDispatch() {
	if (class_exists('Artx', true)) {
	    Artx::load("Artx_Page");
	    if (isset(ArtxPage::$inlineScripts))
		ArtxPage::$inlineScripts[] = '<script type="text/javascript">if(typeof jQuery != "undefined") window.artxJQuery = jQuery;</script>';
	}
    }

    function onInitNextendLibrary() {

	nextendimport('nextend.data.data');
	$this->_data = new NextendData();
	$config = $this->params->toArray();
	if (!isset($config['config']))
	    $config['config'] = array();
	$this->_data->loadArray(version_compare(JVERSION, '1.6.0', 'l') ? $config : $config['config']);
	$cachetime = $this->_data->get('cachetime', 900);
	if ($cachetime != 0) {
	    setNextend('cachetime', $cachetime);
	}
	$cachepath = '/' . trim($this->_data->get('cachepath', '/media/nextend/cache/'), '/') . '/';
	if ($cachepath != '') {
	    $cachepath = rtrim(JPATH_SITE, DIRECTORY_SEPARATOR) . str_replace('/', DIRECTORY_SEPARATOR, $cachepath);
	    setNextend('cachepath', $cachepath);
	}
	setNextend('gzip', $this->_data->get('gzip', 0));
	setNextend('debuglng', $this->_data->get('debuglng', 0));

	if (isset($_GET['nextendclearcache'])) {
	    $app = JFactory::getApplication();
	    if ($app->isAdmin()) {
		nextendimport('nextend.uri.uri');
		nextendimport('nextend.filesystem.filesystem');
		nextendimport('nextend.cache.cache');
		$cache = new NextendCache();
		$cache->deleteCacheFolder();
	    }
	}
    }

    function onBeforeCompileHead() {
	if (defined('NEXTENDLIBRARY')) {
	    if (getNextend('debuglng', 0)) {
		ob_start();
		if (count(NextendText::$untranslated)) {
		    echo "<h3>Untranslated strings:</h3><pre>";
		    NextendText::toIni();
		    echo "</pre>";
		}
		echo "<h3>Loaded or not loaded language files:</h3><pre>";
		print_r(NextendText::$loadedfiles);
		echo "</pre>";

		$this->translate = ob_get_clean();
	    }
	    $this->compiled = true;
	    if (class_exists('NextendCss')) {
		$css = NextendCss::getInstance();
		$css->generateCSS();
	    }
	    if (class_exists('NextendJavascript')) {
		$js = NextendJavascript::getInstance();
		$js->generateJs();
	    }
	}
    }

    function onAfterRender() {
	if (defined('NEXTENDLIBRARY') && $this->compiled === false) {
	    ob_start();
	    if (class_exists('NextendCss')) {
		$css = NextendCss::getInstance();
		$css->_echo = true;
		$css->generateCSS();
	    }
	    if (class_exists('NextendJavascript')) {
		$js = NextendJavascript::getInstance();
		$js->_echo = true;
		$js->generateJs();
	    }
	    $head = ob_get_clean();
	    if ($head != '') {
		$body = JResponse::getBody();
		$body = str_replace('</head>', $head . '</head>', $body);
		JResponse::setBody($body);
	    }
	}

	global $nextend_menu_loadposition;
	if (is_array($nextend_menu_loadposition) && count($nextend_menu_loadposition)) {
	    $body = JResponse::getBody();
	    foreach ($nextend_menu_loadposition AS $lp) {
		$body = str_replace(strip_tags($lp[0]), $lp[1], $body);
	    }
	    JResponse::setBody($body);
	}
	if ($this->translate)
	    JResponse::setBody(preg_replace('/<body(.*?)>/', '<body${1}>' . $this->translate, JResponse::getBody()));
    }

}

?>