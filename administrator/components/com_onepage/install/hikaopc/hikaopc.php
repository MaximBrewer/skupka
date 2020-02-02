<?php 
/** 
* @version		$Id: opc.php$
* @copyright	Copyright (C) 2005 - 2014 RuposTel.com
* @license		GNU General Public License version 2 or later; see LICENSE.txt
*/


// no direct access
defined('_JEXEC') or die;
jimport('joomla.plugin.plugin');
class plgSystemHikaopc extends JPlugin
{
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
	}
	
	private function hikaAutoload() {
		if (!defined('AUTOLOADREGISTERED')) {
			define('AUTOLOADREGISTERED', true); 
			spl_autoload_register(function ($class_name) {
				$cn = strtolower($class_name); 
				if (substr($cn, 0, 7)==='opchika') {
					
					$fn = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'hika'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.$cn.'.php'; 

					if (file_exists($fn)) {
						require_once($fn);
					}
				}
			});
			
			
			jimport( 'joomla.session.session' );

			//stAn - this line will make sure that joomla uses it's session handler all the time. If any other extension is using $ _SESSION before this line, the session may not be consistent
			JFactory::getSession(); 
			// many 3rd party plugins faild on JParameter not found: 
			jimport( 'joomla.html.parameter' );
			// many 3rd party plugins also fail on JRegistry not found: 
			jimport( 'joomla.registry.registry' );
			// basic security classes should also be globally included: 
			jimport( 'joomla.filesystem.folder' );
			jimport( 'joomla.filesystem.file' );

			if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR); 
			
			
		}
	}
	
	
	
	
	
	public function onTriggerPlughikaopc() {
		$document = JFactory::getDocument();
		$type = $document->getType(); 
		if (($type === 'html') || ($type === 'opchtml')) {
			
			$this->hikaAutoload(); 
			
			

			
			$path = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'hika'.DIRECTORY_SEPARATOR.'opc.php'; 
			if (file_exists($path)) {
				require($path); 
				return true; 
			}
		}
	}
	
	public function onAfterRoute() {

		
		$view = JRequest::getWord('view', JRequest::getVar('ctrl', '')); 
		$option = JRequest::getWord('option', ''); 
		$layout = JRequest::getWord('layout', 'default'); 
		$importHika = false; 
		
		//com_hikashop
		if ($option === 'com_hikashop') {
			
			$views = array('cart', 'checkout'); 
			if (in_array($view, $views)) {
				if ($layout === 'default') {
					$importHika = true; 
					
					
					JFactory::getLanguage()->load('com_onepage', JPATH_SITE); 	
					JRequest::setVar('task', 'triggerplug-hikaopc'); 
					
				}
			}
			
			
			
			
			
		}
		
		if (($option === 'com_onepage') ) {
			
			$importHika = true; 
			
		}
		
		if (!empty($importHika)) {
			JRequest::setVar('hikashop_front_end_main',1);
			
			//mystery lines from hikashop.php		
			$session = JFactory::getSession();
			if(is_null($session->get('registry'))) {
				jimport('joomla.registry.registry');
				$session->set('registry', new JRegistry('session'));
			}
			
			if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR); 
			if(!include_once(rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikashop'.DS.'helpers'.DS.'helper.php')) {
				return;
			};
			
			$this->hikaAutoload(); 
		}
		

		
	}
	
	
}


