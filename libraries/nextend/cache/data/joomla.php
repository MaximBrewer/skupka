<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php

nextendimport('nextend.cache.data.data');
class NextendCacheData extends NextendCacheDataAbstract {
    var $cache = null;

    var $conf = null;

    function NextendCacheData() {
        $this->conf = JFactory::getConfig();
        $this->tmpCaching = $this->conf->get('caching') >= 1 ? true : false;
        $this->tmpLifetime = (int)$this->conf->get('cachetime');
    }

    // $time in minutes
    function cache($group = '', $time = 1440, $callable = null, $params = null) {
        $this->cache = JFactory::getCache($group, 'callbackNextend', 'file');
        $this->cache->setCaching(1);
        $this->cache->setLifeTime($time * 60);
        //$data = $this->cache->call($callable, $params);
        $_args = array($callable);
        if(!is_array($params)) $params = (array)$params;
        $_args = array_merge($_args, $params);
        $data = call_user_func_array(array($this->cache, 'call'), $_args);
        $this->cache->setCaching($this->tmpCaching);
        $this->cache->setLifeTime($this->tmpLifetime);
        return $data;
    }

    function check($group = '', $callable = null, $params = null) {
        $this->cache = JFactory::getCache($group, 'callbackNextend', 'file');
        $this->cache->setCaching(1);
        return $this->cache->checkData($callable, $params);
    }
}

if(version_compare(JVERSION, '1.6.0', 'ge')){
    class JCacheControllerCallbackNextend extends JCacheControllerCallback{
    
        function checkData($callable, $params = null){
            return !!$this->cache->get($this->_makeId($callable, $params));
        }
        
      	protected function _makeId($callback, $args){
          $hash = '';
          if(is_array($callback)){
              if(isset($callback[0]) && is_object($callback[0])) $hash.= get_class($callback[0]);
              if(isset($callback[1]) && is_string($callback[1]))  $hash.= $callback[1];
          }
      		return md5($hash.json_encode($args));
      	}
        
    }
}else{

    JFactory::getCache('', 'callback', 'file');
    
    class JCacheCallbackNextend extends JCacheCallback{
    
        function checkData($callable, $params = null, $group = null){
        		// Get the default group
        		$group = ($group) ? $group : $this->_options['defaultgroup'];
        
        		// Get the storage handler
        		$handler =& $this->_getStorage();
        		if (!JError::isError($handler) && $this->_options['caching']) {
        			return !!$handler->get($this->_makeId($callable, $params), $group, (isset($this->_options['checkTime']))? $this->_options['checkTime'] : true);
        		}
        		return false;
        }
        
      	function _makeId($callback, $args){
          $hash = '';
          if(is_array($callback)){
              if(isset($callback[0]) && is_object($callback[0])) $hash.= get_class($callback[0]);
              if(isset($callback[1]) && is_string($callback[1]))  $hash.= $callback[1];
          }
      		return md5($hash.json_encode($args));
      	}
        
    }
}