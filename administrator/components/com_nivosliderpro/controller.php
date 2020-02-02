<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

class NivoSliderProController extends JControllerNivoBaseRev {

    protected $default_view = GlobalsNivoPro::VIEW_SLIDERS;
    protected $default_layout = GlobalsNivoPro::LAYOUT_SLIDER_GENERAL;

    /**
     * show some image
     */
    public function showimage() {
        UniteFunctionJoomla::showImageFromRequest();
        exit();
    }

    /**
     *
     * display some view
     */
    public function display($cachable = false, $urlparams = false) {
    	
    	$isJoomla3 = UniteFunctions::isJoomla3();
    	
		if($isJoomla3)
 			JHtml::_('bootstrap.framework');		
    	
        $urlAssets = GlobalsNivoPro::$urlAssets;

        //add style
        $document = JFactory::getDocument();
        $document->addStyleSheet($urlAssets . "style.css");

        //add jquery
        //$document->addScript("http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js");
        //add jquery ui
        //$document->addScript("http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js");
        
        $document->addStyleSheet($urlAssets . "jui/jquery-ui-1.8.19.custom.css?app=revslider");

        //add custom scripts                
        if($isJoomla3 == false) {
        	$document->addScript($urlAssets . "jquery.min.js?app=nivopro");   
        }
        
        $document->addScript($urlAssets . "jquery-ui.min.js?app=nivopro");
        $document->addScript($urlAssets . "admin.js?app=nivopro");
        $document->addScript($urlAssets . "nivopro.js?app=nivopro");
        
		
        //$document->addScript($urlAssets."jsfunc.js");
        //add ajax url:
        $currentView = JRequest::getVar('view', $this->default_view);
        $ajaxUrl = UniteFunctionJoomla::getViewUrl($currentView, "ajax");
        $document->addScriptDeclaration("var g_urlAjax='$ajaxUrl';");

        /*
          $currentView = JRequest::getVar('view', $this->default_view);
          $currentLayout = JRequest::getVar('layout', $this->default_layout);
          HelperNivoPro::addSubmenu($currentView,$currentLayout);
         */
        
        if ($isJoomla3) {
            $document->addStyleSheet($urlAssets . "style-joomla3.css?app=nivopro");
        }
        parent::display();

        return $this;
    }

}