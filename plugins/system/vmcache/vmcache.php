<?php
/**
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Joomla! Page Cache Plugin
 *
 * @package		Joomla.Plugin
 * @subpackage	System.cache
 */
class plgSystemVmcache extends JPlugin
{

	var $_cache = null;

	/**
	 * Constructor
	 *
	 * @access	protected
	 * @param	object	$subject The object to observe
	 * @param	array	$config  An array that holds the plugin configuration
	 * @since	1.0
	 */
	function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);

		//Set the language in the class
		$config = JFactory::getConfig();
		$options = array(
			'defaultgroup'	=> 'page',
			'browsercache'	=> $this->params->get('browsercache', false),
			'caching'		=> false,
		);

		$this->_cache = JCache::getInstance('page', $options);
	}

	/**
	* Converting the site URL to fit to the HTTP request
	*
	*/
	function onAfterInitialise()
	{
		global $_PROFILER;
		$app	= JFactory::getApplication();
		$user	= JFactory::getUser();

		if ($app->isAdmin() || JDEBUG) {
			return;
		}

		if (count($app->getMessageQueue())) {
			return;
		}
		$user_id = $user->get('id'); 
		
		
		
		//loads virtuemart: 
		if (!class_exists('VmConfig'))	  
		{
			require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		}
		VmConfig::loadConfig(); 
		if(!class_exists('shopFunctionsF'))require(JPATH_VM_SITE.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'shopfunctionsf.php');
		
		if (!class_exists('VmImage'))
			require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'image.php');
		
		
		$app = JFactory::getApplication();
		if ($app->getName() != 'site') {
		    $this->_cache->setCaching(false);
			return false;
		}
		
		
		
		
		
		 if (!class_exists('VirtueMartCart'))
	     require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'cart.php');
	   
		$cart = VirtueMartCart::getCart(false); 
	    
	    if (!empty($cart->products)) 
		{
		$this->_cache->setCaching(false);
		return false; 
		}

		$format = JRequest::getVar('format', 'html'); 
		if ($format != 'html') 
		{
		$this->_cache->setCaching(false);
		return false;
		}

		$doc = JFactory::getDocument(); 
		$class = strtoupper(get_class($doc)); 
		if ($class != 'JDOCUMENTHTML') 
		{
		$this->_cache->setCaching(false);
		return false; 
		}
		
		$format = JRequest::getVar('format', 'html'); 
		if ($format != 'html') {
		$this->_cache->setCaching(false);
		return false;
		}
		
		$doc = JFactory::getDocument(); 
		$class = strtoupper(get_class($doc)); 
		if ($class != 'JDOCUMENTHTML') {
		$this->_cache->setCaching(false);
		return false;
		} 
		
		$tmpl = JRequest::getVar('tmpl', ''); 
		if (!empty($tmpl)) {
		$this->_cache->setCaching(false);
		return false;
		} 
		
		$tmpl = JRequest::getVar('nosef', ''); 
		if (!empty($tmpl)) {
		$this->_cache->setCaching(false);
		return false;
		} 
		
		$tmpl = JRequest::getVar('virtuemart_userinfo_id', ''); 
		if (!empty($tmpl)) {
		$this->_cache->setCaching(false);
		return false;
		} 

		
		if (empty($user_id))
		if ($user->get('guest') && $_SERVER['REQUEST_METHOD'] == 'GET') {
			$this->_cache->setCaching(true);
		}
		

		$data  = $this->_cache->get();

		if ($data !== false)
		{
			JResponse::setBody($data);

			echo JResponse::toString($app->getCfg('gzip'));

			if (JDEBUG)
			{
				$_PROFILER->mark('afterCache');
				echo implode('', $_PROFILER->getBuffer());
			}

			$app->close();
		}
	}
	
	function onAfterRoute()
	{
	    $format = JRequest::getVar('format', 'html'); 
		if ($format != 'html') 
		{
		$this->_cache->setCaching(false);
		return false;
		}

		$doc = JFactory::getDocument(); 
		$class = strtoupper(get_class($doc)); 
		if ($class != 'JDOCUMENTHTML') 
		{
		$this->_cache->setCaching(false);
		return false; 
		}
		
		$format = JRequest::getVar('format', 'html'); 
		if ($format != 'html') {
		$this->_cache->setCaching(false);
		return false;
		}
		
		$doc = JFactory::getDocument(); 
		$class = strtoupper(get_class($doc)); 
		if ($class != 'JDOCUMENTHTML') {
		$this->_cache->setCaching(false);
		return false;
		} 
		
		$tmpl = JRequest::getVar('tmpl', ''); 
		if (!empty($tmpl)) {
		$this->_cache->setCaching(false);
		return false;
		} 
		
		$tmpl = JRequest::getVar('nosef', ''); 
		if (!empty($tmpl)) {
		$this->_cache->setCaching(false);
		return false;
		} 
		
		$tmpl = JRequest::getVar('virtuemart_userinfo_id', ''); 
		if (!empty($tmpl)) {
		$this->_cache->setCaching(false);
		return false;
		} 
		
		
	}

	function onAfterRender()
	{
	
		$app = JFactory::getApplication();

		if ($app->isAdmin() || JDEBUG) {
			return;
		}

		if (count($app->getMessageQueue())) {
			return;
		}
		$enabled = $this->_cache->options['caching']; 
		if (empty($enabled)) return false; 
		
		$cache = $this->_cache->getCaching();
		
		$user = JFactory::getUser();
		$user_id = $user->get('id'); 
		
		if (empty($user_id))
		if ($user->get('guest')) {
			//We need to check again here, because auto-login plugins have not been fired before the first aid check
			$this->_cache->store();
		}
	}
}
