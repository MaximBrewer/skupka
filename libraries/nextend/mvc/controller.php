<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php
nextendimport('nextend.mvc.router');
nextendimport('nextend.mvc.view');
nextendimport('nextend.acl.acl');

class NextendController{
    
    var $_baseControllerPath;
    
    var $_name;
    
    var $_routeMap;
    
    var $_controller;

    var $_acl;

    var $_aclKey;
    
    var $_controllersPath;
    
    var $_router;
    
    var $_modelsPath;
    
    var $_viewsPath;
    
    var $_fragmentsPath;

    var $_controllerName;
    
    var $_viewName;
    
    var $_tplName;
    
    function NextendController($key){
        $this->fixMagicQuotes();
        $this->_baseControllerPath = dirname(__FILE__);
        $this->_routeMap = array();
        $this->_availableControllers = array();
        $this->_controller = $this;
        $this->_acl = new NextendAcl();
        $this->_aclKey = $key;
    }
    
    function init(){
        $this->resetRoute();
        $this->initRouter();
        $this->initModel();
        $this->initView();
    }
    
    function resetRoute(){
        $controllerRoute = null;
        $viewRoute = null;
        $controller = NextendRequest::getCmd('controller', '');
        if(isset($this->_routeMap[$controller])){
            $this->_controllerName = $controller;
        }else{
            list($this->_controllerName) = array_keys($this->_routeMap);
            NextendRequest::set('controller', $this->_controllerName);
        }
        $controllerRoute = $this->_routeMap[$this->_controllerName];
        
        $view = NextendRequest::getCmd('view', 'default');
        if(isset($controllerRoute[$view])){
            $this->_viewName = $view;
        }else{
            list($this->_viewName) = array_keys($controllerRoute);
        }
        $viewRoute = $this->_routeMap[$this->_controllerName][$this->_viewName];
        
        $tpl = NextendRequest::getCmd('tpl', 'default');
        if(in_array($tpl, $viewRoute)){
            $this->_tplName = $tpl;
        }else{
            $this->_tplName = $viewRoute[0];
        }
    }
    
    function initRouter(){
        $this->_router = NextendRouter::getInstance();
    }
    
    function initModel(){
        $this->_modelsPath = $this->_baseControllerPath.'/models/';
    }
    
    function initView(){
        $this->_viewsPath = $this->_baseControllerPath.'/views/';
        $this->_fragmentsPath = $this->_baseControllerPath.'/fragments/';
    }
    
    function initBase(){
        $this->initAction();
    }
    
    function initAction(){
        $this->_controllersPath = $this->_baseControllerPath.'/controllers/';
        if($this->_controllerName!= '' && isset($this->_routeMap[$this->_controllerName])){
            require_once($this->_controllersPath.$this->_controllerName.'.php');
            $class = 'Nextend'.$this->_name.'Controller'.$this->_controllerName;
            $this->_controller = new $class($this->_aclKey);
        }
        
    }
    
    function run(){
        $this->doAction(NextendRequest::getCmd('action', 'default'));
    }
    
    function doAction($action){
        $action = $action.'Action';
        if(method_exists($this->_controller, $action)){
            $this->_controller->$action();
        }else{
            $this->_controller->defaultAction();
        }
    }
    
    function display($action, $tpl = null){
        require_once($this->_viewsPath.$this->_viewName.'/view.html.php');
        $class = 'Nextend'.$this->_name.'View'.$this->_viewName;
        $view = new $class($this);
        $view->_path = $this->_viewsPath.$this->_viewName.'/';
        if($tpl === null){
            $tpl = $this->_tplName;
        }
        
        $view->display($action, $tpl);
    }
    
    function route($query){
        return $this->_router->route($query);
    }

    function canDo($action, $key = null){
        return $this->_acl->authorise(array($action, !$key ? $this->_aclKey : $key));
    }
    
    function getModel($model){
        require_once($this->_modelsPath.$model.'.php');
        $class = 'Nextend'.$this->_name.'Model'.$model;
        $model = new $class($this);
        return $model;
    }
    
    function noaccess(){
        $this->display('noaccess', 'noaccess');
    }
    
    function fixMagicQuotes(){
        static $run;
        if(!$run){
            if (get_magic_quotes_gpc() || nextendIsWordPress()) {
                $_GET     = self::stripslashes_r($_GET);
                $_POST    = self::stripslashes_r($_POST);
                $_COOKIE  = self::stripslashes_r($_COOKIE);
                $_REQUEST = self::stripslashes_r($_REQUEST);
            }
            $run = true;
        }
    }
    
    static function stripslashes_r($array) {
        foreach ($array as $key => $value) {
            $array[$key] = is_array($value) ? self::stripslashes_r($value) : stripslashes($value);
        }
        return $array;
    }
}
?>
