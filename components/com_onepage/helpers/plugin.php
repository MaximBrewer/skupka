<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/*
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
* NOTE: It's not compatible with ArtioSEF to call JRoute from onAfterRoute
*/

class OPCplugin {
	public static function checkCartLoad()
	{
		$plugin = JPluginHelper::getPlugin('system', 'opccart');
		if (!empty($plugin))
		{
			// here we should check if to load a cart. once any extension does getCart, it will not get overwritten
		}
	}
	public static function modifyHeader()
	{
		
		

		// check for VM first: 
		/*
	if (!file_exists(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php')) return false; 
	
	require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');
	$opc_load_jquery = OPCConfig::getValue('opc_load_jquery', '', 0, false, false); 
	
	if (!empty($opc_load_jquery))
		*/
		{
			// unset other jquery libraries: 
			$document = JFactory::getDocument(); 
			$headers = $document->getHeadData();
			
			$tmpl = JRequest::getVar('tmpl'); 
			$tmpl = strtolower($tmpl); 
			if ($tmpl == 'component') return; 
			
			
			$scripts = isset($headers['scripts']) ? $headers['scripts'] : array();
			
			$priority = array(); 
			
			if (!OPCJ3)
			$pr = array('mootools', 'mootools-more.js', 'core.js', 'modal.js', 'media/system/js/calendar.js', 'media/system/js/calendar-setup.js'); 
			else
			$pr = array('mootools', 'mootools-more.js',  '/modal.js'); 
			
			$f = false; 
			
			$jquery_i = array(); 
			
			$noconflict = array(); 
			$ui_unset = false; 
			
			foreach($scripts as $url=>$type) {
				
				$e = explode('?', $url); 
				$url_s = $url; 
				if (count($e)>1)
				{
					$url_s = $e[0]; 
					
					
				}		

				
				if ((substr($url_s, -3) === '.js') || ((stripos($url_s, '.js?'))!==false))
				{
					
					foreach ($pr as $s2)
					{
						if (stripos($url_s, $s2) !== false) 
						{
							if (isset($headers['scripts'][$url]))
							{
								$priority[$url] = $headers['scripts'][$url]; 
								unset($headers['scripts'][$url]); 
							}
						}
					}
					
					
					
					// it will unset these: 
					$arr = array(
					'/jquery-min.js', 
					'/jquery.min.js', 
					'/jquery-latest.min.js', 
					'/jquery-latest.js',
					'/jquery.js',  
					'md_stylechanger', 
					'jquery.ui.core.min.js',
					'jquery-ui.min.js',
					'jqueryopc-1.11.2.min.js',
					'jquery-1.11.2.min.js',
					'jquery-migrate'); 
					
					foreach ($arr as $s)
					{
						
						
						
						if (stripos($url_s, 'jquery.noConflict')!==false)
						{
							$noconfict[$url] = $type; 
						}
						
						if (stripos($url_s, $s) !== false)
						{
							if ((stripos($s, 'jquery.ui')!==false) || (stripos($s, 'jquery-ui'))!==false)
							$ui_unset = true; 
							
							/*
				if (!$f) 
				{
				
				// $jquery_i[] = $url; 
				
				$f = true; 
				continue 2; 
				}
				*/
							
							
							

							// unset all jquery libraries
							unset($headers['scripts'][$url]);
							
							
							
						}
					}
					$x = stripos($url_s, '/jquery-'); 
					
					if ($x !==false)
					{
						
						$ver = substr($url,$x+strlen('/jquery-'));
						$ver = str_replace('.min.js', '', $ver); 
						$ver = str_replace('.js', '', $ver); 
						
						$a = explode('.', $ver); 
						$z = 0; 
						foreach ($a as $x)
						{
							if (ctype_digit($x)) $z++; 
						}
						if ($z === 3) 
						{
							
							if (!$f) 
							{
								$jquery_i[] = $url; 
								$f = true; 
								//continue; 
							}
							
							unset($headers['scripts'][$url]);
						}
					}
					
				}
			}
			
			
			
			$jquery = array(); 
			
			require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'loader.php'); 
			$url = OPCloader::getUrl(true); 
			if (substr($url, 0, 5) === 'http:') {
				$url = substr($url, 5); 
			}
			if (substr($url, 0, 6) === 'https:') {
				$url = substr($url, 6); 
			}
			
			//stAn: let's rather include local library: $jquery['//code.jquery.com/jquery-latest.min.js'] = array('mime'=>'text/javascript', 'defer'=>false, 'async'=>false); 
			$suf = ''; 
			if (defined('OPCVERSION')) { 
			
				require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
				$debug = OPCconfig::get('opc_debug', false); 
					
				

			
				$v = str_replace('.', 'Z', OPCVERSION); 
				
				if ($debug) {
				  $v .= 'Z'.rand(); 
				}
				
				$suf = '?opcver='.$v; 
			}
			
			$jquery[$url.'components/com_onepage/themes/extra/jquery-ui/jquery-1.11.2.min.js'.$suf] = array('mime'=>'text/javascript', 'defer'=>false, 'async'=>false); 
			$jquery['//code.jquery.com/jquery-migrate-1.2.1.min.js'] = array('mime'=>'text/javascript', 'defer'=>false, 'async'=>false); 
			$jquery['//code.jquery.com/jquery-migrate-1.2.1.min.js?defer=true'] = array('mime'=>'text/javascript', 'defer'=>true, 'async'=>false);
			
			
			// attach UI if it was unset: 
			if (!empty($ui_unset))
			{
				$jquery[$url.'components/com_onepage/themes/extra/jquery-ui/jquery-ui.min.js'.$suf] = array('mime'=>'text/javascript', 'defer'=>false, 'async'=>false); 
			}
			
			if (!empty($noconfict))
			{
				$jquery = array_merge($jquery, $noconfict); 
			}

			
			
			$mn = false; 
			if (!empty($priority))
			{
				
				
				$priority[$url.'components/com_onepage/themes/extra/mootools/mootools.noconflict.js'] = array('mime'=>'text/javascript', 'defer'=>false, 'async'=>false); 
				$mn = true; 
			}
			
			$priority2 = array_merge($priority, $jquery); 
			$new_headers = array_merge($priority2, $headers['scripts']); 
			if ($mn)
			{
				define('OPC_MOO', true); 
				$new_headers[$url.'components/com_onepage/themes/extra/mootools/mootools.noconflict.load.dollar.js'] = array('mime'=>'text/javascript', 'defer'=>false, 'async'=>false); 	   
				$new_headers[$url.'components/com_onepage/themes/extra/mootools/mootools.noconflict.load.dollar.js?defer=true'] = array('mime'=>'text/javascript', 'defer'=>true, 'async'=>false); 	 
			}

			
			
			
			$headers['scripts'] = $new_headers; 
			
			
			
			$document->setHeadData($headers);
			
			
		}
		

	}
	public static function detectMobile()
	{
		if (defined('OPC_DETECTED_DEVICE'))
		if (OPC_DETECTED_DEVICE != 'DESKTOP') return true; 
		else return false; 
		
		$isMobile = false;
		
		$app = JFactory::getApplication(); 
		/*$jtouch = $app->getUserStateFromRequest('jtpl', 'jtpl', -1, 'int');*/
		jimport('joomla.plugin.helper'); 
		jimport( 'joomla.registry.registry' );
		$jtouch_plg = JPluginHelper::getPlugin('system', 'jtouchmobile'); 
		$jtouch = 0; 
		if (!empty($jtouch_plg))
		{
			
			
			$params = new JRegistry($jtouch_plg->params); 
			$jtouch_template = $params->get('jtouch_mobile_template'); 
			$template = $app->getTemplate(); 
			if (($jtouch_template == $template) || (stripos($template, 'jtouch')!==false))
			{
				$jtouch = 1; 
			}
			

			if ($jtouch > 0) 
			{
				// forced mobile view: 
				define('OPC_DETECTED_DEVICE', 'MOBILE');
				return true; 
			}
		}

		
		
		if(!class_exists('uagent_info')){
			require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'third_party'.DIRECTORY_SEPARATOR.'mdetect.php');
		}
		
		$ua = new uagent_info();
		
		if($ua->DetectMobileQuick()){
			
			define('OPC_DETECTED_DEVICE', 'MOBILE');
			$isMobile = true;
			return $isMobile; 
		}
		if ($ua->DetectTierTablet() ){
			define('OPC_DETECTED_DEVICE', 'TABLET');
			$isMobile = true;
			return $isMobile; 
		}
		
		if($isMobile == false){
			define('OPC_DETECTED_DEVICE', 'DESKTOP');
		}
		
		
		
		return $isMobile;
		
	}

	public static function checkGiftCoupon(&$order, $last_state)
	{
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		
		$do_not_allow_gift_deletion = OPCconfig::get('do_not_allow_gift_deletion', false);
		$gift_order_statuses = OPCconfig::get('gift_order_statuses', array());
		
		if (!empty($do_not_allow_gift_deletion))
		if (!empty($gift_order_statuses))
		{
			
			
			if (is_object($order))
			{
				
				if (!isset($order->order_status)) return; 
				$status = $order->order_status; 
				if (in_array($status, $gift_order_statuses))
				{
					$coupon_code = $order->coupon_code; 
					$value = abs($order->coupon_discount);
					$value = (double)str_replace(',', '.', $value); 
					if (!empty($coupon_code))
					{
						$db=JFactory::getDBO(); 
						$q = "delete from `#__virtuemart_coupons` where `coupon_code` = '".$db->escape($coupon_code)."' and `coupon_type` = 'gift' limit 1"; 
						$db->setQuery($q); 
						$db->query(); 
						
						
						
					}
				}
			}
		}
		
		return null; 
	}

	public static function alterActivation()
	{
		//index.php?option=com_users&task=registration.activate&token=64e7109fe98d1f9988f9e6560f9b644a

		
		$user = JFactory::getUser(); 
		$uid = $user->get('id');
		$task = JRequest::getWord('task'); 
		$option = JRequest::getWord('option'); 
		
		//if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) 
		if ((($task == 'registration.activate') || ($task == 'registrationactivate')) && ($option == 'com_users'))
		{
			
			require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
			
			$opc_do_not_alter_registration = OPCconfig::get('opc_do_not_alter_registration', false);
			
			
			if (!empty($opc_do_not_alter_registration)) return true;   
			//if (!empty($uid))
			{
				
				$blocked = $user->get('block');
				$uParams	= JComponentHelper::getParams('com_users');
				$useractivation = $uParams->get('useractivation');
				//if (!empty($blocked))
				if ($useractivation == 1)
				{
					
					// if user is already logged in with a blocked account we need to enable the activation here   
					$db		= JFactory::getDBO(); 
					$token = JRequest::getVar('token', null, 'request', 'alnum');
					if (empty($token)) return true; 
					// Get the user id based on the token.
					$db->setQuery(
					'SELECT '.$db->quoteName('id').' FROM '.$db->quoteName('#__users') .
					' WHERE '.$db->quoteName('activation').' = '.$db->Quote($token) .
					' AND '.$db->quoteName('block').' = 1 limit 1'
					);
					$userId = (int) $db->loadResult();
					if ((!empty($userId)) && ($userId > 0))
					{
						
						/*
			$q = 'update #__users set (block=0, activation='') where id = '.$userId.' limit 1'; 
			$db->setQuery($q); 
			$db->query(); 
			*/
						$user = JFactory::getUser($userId);
						$user->set('activation', '');
						$user->set('block', '0');
						if ($user->save())
						{
							
							$jlang = JFactory::getLanguage(); 
							

							$jlang->load('com_users', JPATH_SITE, 'en-GB', true); 
							$jlang->load('com_users', JPATH_SITE, $jlang->getDefault(), true); 
							$jlang->load('com_users', JPATH_SITE, null, true); 

							$mainframe = JFactory::getApplication();
							$mainframe->enqueueMessage(JText::_('COM_USERS_REGISTRATION_ACTIVATE_SUCCESS'), 'notice');
							$msg = JText::_('COM_USERS_REGISTRATION_ACTIVATE_SUCCESS'); 
							//$link = JRoute::_('index.php?option=com_users&view=login'); 
							
							JRequest::setVar('task', null); 
							JRequest::setVar('layout', null); 
							JRequest::setVar('option', 'com_users'); 
							JRequest::setVar('view', 'login'); 
							
							//$mainframe->redirect($link, $msg, 'notice'); 
							//$mainframe->close(); 
							
							
							
							return true; 
						}
						else
						{
							
						}
						//$this->setRedirect(JRoute::_('index.php?option=com_users&view=login', false));
					}
					/*
			ob_start(); 
					$options = array('silent' => true, 'skip_joomdlehooks'=>true );
					$mainframe = JFactory::getApplication(); 
					$mainframe->logout($uid, $options); 
					ob_get_clean(); 
			*/
				}
			}
			
			return true;
		}
		
		// proceed further
		return false; 
	}
	
	public static function loadVM() {
		
		static $run; 
		if (!empty($run)) return; 
		$run = true; 
		
		if (!class_exists('VmConfig'))
			require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');
			VmConfig::loadConfig(); 
			
			$tag = JFactory::getLanguage()->getTag(); 
			
			if( JFactory::getApplication()->isAdmin()){
			 $tag = JRequest::getVar('vmlang', $tag);
			}
			
			if (class_exists('vmLanguage')) {
				if (method_exists('vmLanguage', 'setLanguageByTag')) {
					vmLanguage::setLanguageByTag($tag); 
				}
			}
	}
	
	public static function loadShoppergroups()
	{


		$option = JRequest::getCmd('option');   
		$task = JRequest::getWord('task'); 
		$view = JRequest::getWord('view');

		if (($option == 'com_onepage') && (($task=='opc') || ($task=='checkout')))
		{
			if (!class_exists('VmConfig'))
			require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');
			VmConfig::loadConfig(); 
			
			if (!class_exists ('VirtueMartCart')) {
				require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'cart.php');
			}
			
			$cart = VirtueMartCart::getCart(false);
			$euvatid = JRequest::getVar('eu_vat_id', ''); 
			if (is_array($cart->BT))
			if (isset($cart->BT['eu_vat_id']))
			if ($cart->BT['eu_vat_id'] != $euvatid)
			{
				$cart->BT['eu_vat_id'] = $euvatid; 
				$cart->setCartIntoSession(); 
				
				
			}
		}

		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'loader.php'); 
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'shoppergroups.php'); 
		OPCShopperGroups::getSetInitShopperGroup(); 
		//  OPCloader::getSetShopperGroup(true); 
	}

	public static function getContinueLink()
	{
		
		
		$session = JFactory::getSession(); 
	   $urls = $session->get('product_urls', array()); 
	   if (empty($urls)) $urls = array();
       if (!empty($urls)) $urls = json_decode($urls, true); 	   
	   
	   {
		   $t1 = JRequest::getVar('product_addtocart_url', ''); 
		   $id = JRequest::getVar('virtuemart_product_id', 0); 
		   if (!empty($id)) {
			   if (is_array($id)) $id = reset($id); 
			   else
			   if (is_numeric($id)) $id = (int)$id; 
		       else $id = 0; 
			   
		   }
			
		   if ((!empty($t1) && (!empty($id))))
		   {
			   $urls[$id] = base64_decode($t1); 
			   
			   $session->set('product_urls', json_encode($urls)); 
		  
			   $session->set('lastcontiuelink', $urls[$id], 'opc');
			   
			  
			   
			   //this got highest priority here
			   return; 
		   }
		  
	   
	      
	   }

		$format = JRequest::getVar('format', 'html'); 
		
		$option = JRequest::getCmd('option'); 
		$session = JFactory::getSession();
		$task = JRequest::getWord('task'); 
		$view = JRequest::getWord('view');
		if ($option == 'com_k2')
		{
			
			if ($view == 'item')
			{
				
				$id = JRequest::getVar('id'); 
				$itemid = JRequest::getInt('Itemid', 0); 
				$lang = JRequest::getWord('lang'); 
				$url = 'index.php?option=com_k2&view='.$view.'&id='.$id;
				
				if (!empty($itemid))
				$url .= '&Itemid='.$itemid; 
				//$u = JRoute::_($url);
				
				$session->set('lastcontiuelink', $url, 'opc');
				return;
			}
		}
		
		if(('com_virtuemart' == $option))
		{
			$session = JFactory::getSession();
			
			if (($view == 'productdetails') && (empty($task)))
			{
				
				$id = JRequest::getInt('virtuemart_product_id', 0); 
				if (!empty($id))
				{
					$session = JFactory::getSession(); 
					$urls = $session->get('product_urls', array()); 
					if ((!empty($urls)) && (isset($urls[$id])))
					{
						$url = $urls[$id]; 
					}
					else
					{
						require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');  
						
						$opc_only_parent_links = OPCconfig::get('opc_only_parent_links', false); 
						
						if (!empty($opc_only_parent_links))
						{
							$db = JFactory::getDBO(); 
							$q = 'select product_parent_id from #__virtuemart_products where virtuemart_product_id = '.(int)$id.' limit 0,1'; 
							$db->setQuery($q); 
							$virtuemart_parent_id = $db->loadResult(); 
							
							if (!empty($virtuemart_parent_id)) $id = $virtuemart_parent_id; 
						}
						
						$cid = JRequest::getInt('virtuemart_category_id', 0); 
						$url = 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.$id; 
						
						if (!empty($cid))
						$url .= '&virtuemart_category_id='.$cid; 
						
						$itemid = JRequest::getInt('Itemid', 0); 
						if (!empty($itemid))
						$url .= '&Itemid='.$itemid; 
						
						//$u = JRoute::_($url);
					}
					$session->set('lastcontiuelink', $url, 'opc');
					return;
				}
			}
			
			if (($view == 'category') && (empty($task)))
			{
				
				$cid = JRequest::getInt('virtuemart_category_id', 0); 
				if (!empty($cid))
				{
					
					
					
					
					$url = 'index.php?option=com_virtuemart&view=category&virtuemart_category_id='.$cid; 
					
					
					
					$itemid = JRequest::getInt('Itemid', 0); 
					if (!empty($itemid))
					$url .= '&Itemid='.$itemid; 
					
					//$u = JRoute::_($url);
					
					$session->set('lastcontiuelink', $url, 'opc');
					return; 
				}
			}
			
			
			
		}
	}

	 public static function checkTasks() {
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');
		$render_in_third_address = OPCconfig::get('render_in_third_address', array()); 
			if (!empty($render_in_third_address)) {
			   
			   
					require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'third_address.php');
					OPCthirdAddress::checkTasks(); 
				 
				}
			
			$task = JRequest::getVar('task', ''); 
			$task2 = JRequest::getVar('task2', ''); 
			$view = JRequest::getVar('view', ''); 
			$option = JRequest::getVar('option', ''); 
			if (($option === 'com_virtuemart') && ($task == 'deletecoupons')) {
			     $session = JFactory::getSession(); 
				 $nc = $session->set('opc_last_coupon', ''); 
			}
				
	}
	
	
	public static function checkOPCtask()
	{
		
		$view = JRequest::getWord('view');
		
		$task = JRequest::getWord('task'); 
		//$session = JFactory::getSession(); 
		$task = strtolower($task); 
		$tasks = array('tracker', 'checkout', 'opc', 'opcregister', 'loadjs', 'ping'); 
		
		// site refresh, logout, hacking attempt protection
		if (($view == 'opc') && (!in_array($task, $tasks)))
		{
			
			JRequest::setVar('controller', 'virtuemart'); 
			JRequest::setVar('view', 'virtuemart'); 
			JRequest::setVar('layout', 'default'); 
			JRequest::setVar('format', 'html'); 

			// we can safely remove all variables in session: 
			/*
		if (($task!='tracker') && ($task != 'opc'))
		{
		$session = JFactory::getSession();
		$session->clear('opcuniq');
		$session->clear($rand2); 
		}
		*/
			return false; 
		}
		if ($task == 'edit') return false; 
		// continue
		// only load rest for virtuemart and onepage
		
		$option = JRequest::getVar('option'); 
		if (($view === 'cart') && ($task === 'checkout') && ($option === 'com_virtuemart'))
		{
			JRequest::setVar('task', ''); 
			
		}
		
		
		if(('com_virtuemart' == $option) || ('com_onepage' == $option)) return true; 
		else return false; 
		
		return true; 
	}
	// not used any more
	public static function checkOPCunique()
	{

		
	}
	public static function isOPCcheckoutEnabled()
	{
		// we must load vmplugin override in all cases:
		if (!class_exists('vmPlugin'))
		{
			
			require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'vmplugin.php'); 
		}
		



		
		$set = array('com_virtuemart', 'com_user', 'com_users', 'com_onepage'); 
		$set2 = array('com_user', 'com_users'); 
		$option = JRequest::getVar('option'); 
		
		
		
		if (!((in_array($option, $set) && (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php')))))
		{
			return false; 
		}
		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		
		$opc_disable_for_mobiles = OPCconfig::get('opc_disable_for_mobiles', false); 
		
		$disable_onepage = OPCconfig::get('disable_onepage', false); 
		
		if (!empty($opc_disable_for_mobiles))
		{
			
			$isMobile = self::detectMobile(); 
			
			if (!empty($isMobile)) return false; 
		}
		$task = JRequest::getCMD('task');
		$view = JRequest::getVar('view'); 

		if (!empty($disable_onepage)) return false;
		
		
		if (stripos($task, 'reset')!==false) 
		{
			return false; 
		}
		if (stripos($task, 'login')!==false) 
		{
			return false; 
		}
		if (stripos($task, 'remind')!==false) 
		{
			return false; 
		}
		
		if (stripos($view, 'reset')!==false) 
		{
			return false; 
		}
		if (stripos($view, 'login')!==false) 
		{
			return false; 
		}
		if (stripos($view, 'remind')!==false) 
		{
			return false; 
		}
		
		
		
		return true; 
	}

	public static function getCache()
	{
		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		$opc_calc_cache = OPCconfig::get('opc_calc_cache', false); 
		
		
		
		if (!class_exists('calculationHelper'))
		if (!empty($opc_calc_cache))
		if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'calculationh_patched.php'))
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'calculationh_patched.php'); 
		
		
		
	}

	public static function checkLoad()
	{

		$app = JFactory::getApplication();
		// if we are not at FE, do not alter anything
		if ($app->getName() != 'site') {
			return false;
		}


		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'compatibility.php'); 

		
		if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'language.php')) return false;

		// test format: 
		$format = JRequest::getVar('format', 'html'); 
		$option = JRequest::getCmd('option'); 
		
		$tmpl = JRequest::getVar('tmpl', ''); 

		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 

		if (($tmpl == 'component') && ($format != 'opchtml')) return false; 
		
		// speed up json requests 

		$okformat = array('opchtml', 'html'); 
		if (!in_array($format, $okformat)) return false; 
		
		if ($app->isAdmin()) return false; 
		
		$doc = JFactory::getDocument(); 
		$class = get_class($doc); 
		$class = strtolower($class); 
		
			if (method_exists($doc, 'getType')) {
			$type = $doc->getType(); 
			if (($type === 'html') || ($type === 'opchtml')) {
				
			}
			else {
				return false; 
			}
			}
			else {
		$format = str_replace('jdocument', '', $class); 
		if (!in_array($format, $okformat)) return; 
			}
		
		
		
		JLoader::register('OPCLang', JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'language.php' );
		
		
		
		
		// load basic stuff:
		self::loadLang(); 
		if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR); 
		
		if (!defined('JPATH_OPC'))
		define('JPATH_OPC', JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'); 
		
		
		
		
		

		return true; 
	}


	public static function loadLang()
	{
		
		$lang = JFactory::getLanguage();
		$lang->load('com_onepage', JPATH_SITE, 'en-GB', true);
		$lang->load('com_onepage', JPATH_SITE, null, true);
		
		if (class_exists('VmConfig'))
		{
			if (method_exists('VmConfig', 'loadJLang'))
			{
				
				VmConfig::loadJLang('com_virtuemart', TRUE);
				VmConfig::loadJLang('com_virtuemart_shoppers', TRUE);
				VmConfig::loadJLang('com_virtuemart_orders',TRUE);
				VmConfig::loadJLang('com_onepage', TRUE);
				
				
				//return; 
				
				
			}
		}
		
		//if (defined('VM_VERSION') && (VM_VERSION >= 3)) return; 
		
		$extension = 'com_virtuemart';
		$lang->load($extension, JPATH_SITE, 'en-GB');
		$tag = $lang->getTag();
		
		
		
		$lang->load('com_virtuemart_orders', JPATH_SITE, 'en-GB', false);
		
		
		
		
		
		$lang->load('com_virtuemart_shoppers', JPATH_SITE, 'en-GB', false);
		$lang->load($extension, JPATH_SITE, $tag, true, true);

		if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.$tag.DIRECTORY_SEPARATOR.$tag.'.com_virtuemart_orders.ini')) {
		  $x = $lang->load('com_virtuemart_orders', JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart', $tag, true );
		}
		
		
		if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.$tag.DIRECTORY_SEPARATOR.$tag.'.com_virtuemart_shoppers.ini'))  {
		 $x = $lang->load('com_virtuemart_shoppers', JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart', $tag, true );
		 $x = $lang->load('com_virtuemart_orders', JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart', $tag, true );
		 
		}
		
		
		if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.$tag.DIRECTORY_SEPARATOR.$tag.'.com_virtuemart_orders.ini')) {
		 $x = $lang->load('com_virtuemart_orders', JPATH_SITE, $tag, true );
		}
		
		if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.$tag.DIRECTORY_SEPARATOR.$tag.'.com_virtuemart_shoppers.ini'))  {
		 $y = $lang->load('com_virtuemart_shoppers', JPATH_SITE, $tag, true);
		}
		
		
	}
	public static function alterRegistration()
	{
		
		
		
		$user = JFactory::getUser(); 

		
		$set2 = array('com_user', 'com_users'); 
		
		$user = JFactory::getUser(); 
		$uid = $user->get('id');
		$task = JRequest::getWord('task'); 
		$option = JRequest::getWord('option'); 
		$view = JRequest::getWord('view'); 
		$layout = JRequest::getVar('layout', ''); 
		
		$task = strtolower($task); 
		
		if ($uid <= 0)
		{
			
			if (($view == 'profile') && ($task == 'saveuser'))
			{
				//return; 
				JRequest::setVar('option', 'com_virtuemart'); 
				JRequest::setVar('view', 'user'); 
			}
			
			require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
			
			$op_redirect_joomla_to_vm = OPCconfig::get('op_redirect_joomla_to_vm', false); 
			
			if (!empty($op_redirect_joomla_to_vm))
			{
				
				if (in_array($option, $set2))
				{
					
					
					// allowed tasks
					
					
					$set3 = array('user.logout', 'logout', 'user.login', 'login', 'reset', 'remind'); 
					foreach ($set3 as $i=>$s)
					{
						if (stripos($task, $s)!==false) return true; 
					}
					if ((in_array($task, $set3)) || (in_array($view, $set3)))
					{
						
						return true; 
					}
					
					// do not redirect, but show the proper page: 
					JRequest::setVar('option', 'com_virtuemart'); 
					JRequest::setVar('view', 'user'); 
					JRequest::setVar('task', 'display'); 
					JRequest::setVar('layout', 'edit'); 
					JRequest::setVar('controller', 'user'); 
					
					
					$url = 'index.php?option=com_virtuemart&view=user'; 
					JFactory::getApplication()->redirect($url); 
				    JFactory::getApplication()->close(); 
					
				}
			}
		}
		
		$task = strtolower($task); 
		
		// dont' proceed OPC when user is logged and is editing the address: 
		if ($option == 'com_virtuemart')
		if ($uid>0)
		{
			// virtuemart_user_id[]=51&virtuemart_userinfo_id=18
			$virtuemart_user_id = JRequest::getVar('virtuemart_user_id', ''); 
			$virtuemart_userinfo_id = JRequest::getVar('virtuemart_userinfo_id', ''); 
			//http://vm2onj25.rupostel.com/index.php?option=com_virtuemart&view=user&task=editaddresscart&addrtype=BT&virtuemart_userinfo_id=11&cid[]=51
			if (($view=='user') && ($task == 'editaddresscart') && (!empty($virtuemart_userinfo_id))) return true;
			if (($view=='user') && ($task == 'editaddressst') && (!empty($virtuemart_user_id)) && (!empty($virtuemart_userinfo_id)))
			{
				
				return true;
			}
		}
		
		
		
		if (($uid > 0) && (($view == 'user') && ($layout == 'edit')))
		{
			return true;
		}
		return false; 
	}

	public static function loadOPCcartView()
	{



		
		
		
		$task = JRequest::getWord('task'); 
		$option = JRequest::getWord('option'); 
		$view = JRequest::getWord('view'); 
		$layout = JRequest::getVar('layout', ''); 
		$controller = JRequest::getWord('controller', JRequest::getWord('view', 'virtuemart'));
		
		if  ($view == 'cart2')
		{
			$view = 'opc'; 
			
			$_POST['view'] = 'opc'; 
			$_GET['view'] = 'opc'; 
			$_REQUEST['view'] = 'opc';
			$controller = 'opc';
			JRequest::setVar('view', 'opc'); 
			JRequest::setVar('task', 'cart'); 
		}

		$task = strtolower($task); 
		
		if ((($view == 'cart') || ($view == 'opc') ) || (($view=='user') && ($task=='editaddresscheckout')) || ($task == 'pluginuserpaymentcancel') || ($task=='editaddresscart' || $task == 'add'))
		{
			
			if (!defined('JPATH_VM_SITE'))
			{
				if (!class_exists('VmConfig'))
				require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');
				VmConfig::loadConfig(); 
				
			}
			
			
			if (!class_exists('VirtueMartViewCart'))
			{
				$tax_cloud = JPluginHelper::getPlugin('vmcalculation', 'taxcloud'); 
				if (class_exists('plgVmCalculationTaxCloud'))
				{
					plgVmCalculationTaxCloud::$OPCMode = true; 
				}
				
				//check any express cart layouts: 
				if (!class_exists ('VirtueMartCart')) {
					require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'cart.php');
				}
				
				$cart = VirtueMartCart::getCart();
				
			
				
				if (stripos($cart->layoutPath, DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR)!==false)
				{
					
					
					return false; 
				}
				
				$opc_memory = OPCconfig::get('opc_memory', 0); 
				
				if (!empty($opc_memory))
				ini_set('memory_limit',$opc_memory);
				else
				if (!isset($opc_memory))
				ini_set('memory_limit','128M');
				// we must disable chosen as it causes lot's of troubles: 
				define('OPC_VIEW_LOADED', true); 
				require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'virtuemart.cart.view.html.php'); 
				
				/*
				require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'checkboxproducts.php'); 
				require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
				OPCCheckBoxProducts::setUpVMCaches(); 
				*/
				
				
			}
			else
			{
				// opc will not load because some other extension is using cart view override
				return false; 
			}
			
			if ($view == 'user')
			{
				if (!class_exists('VirtueMartViewUser'))
				require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'virtuemart.user.view.html.php'); 
				
				JRequest::setVar('layout', 'default'); 
				JRequest::setVar('view', 'cart'); 
				
			}
			
			unset($_POST['checkout']); unset($_GET['checkout']); unset($_REQUEST['checkout']); 
			unset($_POST['confirm']); unset($_GET['confirm']); unset($_REQUEST['confirm']); 
			
			if (!class_exists ('VirtueMartCart')) {
				require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'cart.php');
			}
			
			$cart = VirtuemartCart::getCart(); 
			$cart->_redirect = false; 
			$cart->_redirect_disabled = true; 
			

			
			
		}
		else return false;
		
		if ($controller === 'opc')
		{
			
			if (strpos($controller, '..')!==false) die('?'); 
			require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'opc.php'); 
			
			// fix 206 bug here:
			
			
		}
		
		// proceed
		return true; 
		

	}
	// this function fixes vm206 bug on adding a new address
	// it also fixes 'name' field when it not generated within VM for Joomla
	public static function fixVMbugNewShippingAddress()
	{
		
		$task = JRequest::getWord('task'); 
		$option = JRequest::getWord('option'); 
		$view = JRequest::getWord('view'); 
		$layout = JRequest::getVar('layout', ''); 
		
		
		if (defined('VM_VERSION') && (VM_VERSION >= 3))
		{
			if ((($task=='update') && ($option == 'com_virtuemart')) || ((($task=='updatecart') && ($option == 'com_virtuemart'))))
			{
				$quantity = JRequest::getVar('quantity'); 
				if (!is_array($quantity))
				{
					$cart_virtuemart_product_id = JRequest::getVar('cart_virtuemart_product_id', null);
					if (!is_null($cart_virtuemart_product_id))
					{
						$arr = array($cart_virtuemart_product_id => (int)$quantity); 
						JRequest::setVar('quantity', $arr); 
					}
					
				}
			}
			/*
			if (($task=='delete') && ($option == 'com_virtuemart'))
			{
				
				JRequest::setVar('task', 'updatecart'); 
				
				$quantity = JRequest::getVar('quantity'); 
				if (!is_array($quantity))
				{
					$cart_virtuemart_product_id = JRequest::getVar('cart_virtuemart_product_id', null);
					if (!is_null($cart_virtuemart_product_id))
					{
						$arr = array($cart_virtuemart_product_id => (int)$quantity); 
						JRequest::setVar('quantity', $arr); 
						JRequest::setVar('delete.'.$cart_virtuemart_product_id, '1'); 
					}
					
				}
			}
			*/
			
		}
		// We need to fix a VM206 bugs when a new shipping address cannot be added, savecartuser
		if( ('user'==$view && (('savecartuser' == $task) || ('editaddresscart' == $task)) ))
		{
			

			if ('ST' == JRequest::getCMD('address_type'))
			{
				if (!isset($_POST['shipto_virtuemart_userinfo_id']))
				{
					$_POST['shipto_virtuemart_userinfo_id'] = '0'; 
					JRequest::setVar('shipto_virtuemart_userinfo_id', 0); 
					
				}
				
			}
			if ('BT' == JRequest::getCMD('address_type'))
			{
				if (isset($_POST['shipto_virtuemart_userinfo_id']))
				{
					JRequest::setVar('shipto_virtuemart_userinfo_id', null); 
					unset($_POST['shipto_virtuemart_userinfo_id']); 
					
				}
			}
			
			
			// this fixes vm206 bug: Please enter your name. after changing BT address
			if ('BT' == JRequest::getCMD('address_type'))
			{
				$user = JFactory::getUser();
				
				
				//$x = JRequest::getVar('name'); 
				if (!isset($_POST['name']))
				{
					if (!empty($user->name)) 
					{
						$_POST['name'] = $user->name; 
						JRequest::setVar('name', $_POST['name']); 
					}
					else
					{
						$_POST['name'] = $user->get('first_name', '').' '.$user->get('middle_name', '').' '.$user->get('last_name', ''); 
						JRequest::setVar('name', $_POST['name']); 
					}
					
				}
				
			}
			
		}
	}
	public static function enableSilentRegistration()
	{
		
			$user = JFactory::getUser(); 
			$id = $user->get('id'); 
			if (!empty($id)) return;
		
		// let's enable silent registration when show login is disabled, but only registered users can checkout: 
		$t1 = JRequest::getCmd('controller', '', 'post'); 
		$t2 = JRequest::getCmd('view', 'user', 'post'); 
		$t3 = JRequest::getCmd('address_type', '', 'post'); 
		$t32 = JRequest::getWord('addrtype','');
		$t4 = JRequest::getCmd('task', 'saveUser', 'post'); 
		$t4 = strtolower($t4); 
		
		if (($t1 == 'user') && ($t2 == 'user') && (($t3 == 'BT') || ($t32=='BT')) && ($t4=='saveuser'))
		{
		
			$t5 = JRequest::getVar('username'); 
			if (empty($t5))
			{
				
				$email = JRequest::getVar('email'); 
				if (!empty($email))
				{
					JRequest::setVar('username', $email); 
				}
				// address name: 
				$name = JRequest::getVar('name'); 
				if (empty($name))
				{
					$firstname = JRequest::getVar('first_name', 'default'); 
					$lastname = JRequest::getVar('last_name', ' address'); 
					JRequest::setVar('name', $firstname.' '.$lastname); 
				}
				
			}
			JRequest::setVar('task', 'saveUser'); 
			
			
			
			
			
			
			
		}
	}

	public static function loadOpcForLoggedUser()
	{
		
		
		$task = JRequest::getWord('task'); 
		$option = JRequest::getWord('option'); 
		$view = JRequest::getWord('view'); 
		$layout = JRequest::getVar('layout', ''); 
		if( ('user'==$view && (('savecartuser' == $task) || (strpos($task, 'editadd')!==false ))) )
		{
			
			
			
			if ($view != 'opc')
			$config = array ( "base_path"=> JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart',  "layout"=>  "default" );
			else $config = array ( "base_path"=> JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage',  "layout"=>  "default" );
			
			
			require_once (JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'renderer.php'); 
			
			
			
			$OPCrenderer = new OPCrenderer($config); 
			
			require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'loader.php'); 
			
			
			
			if (!class_exists('VirtueMartCart'))
			require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'cart.php');

			$cart = VirtueMartCart::getCart();
			
			if ($view != 'opc')
			if (!OPCloader::logged($cart))
			{
				// we will load OPC for all edit address links for unlogged
				JRequest::setVar('view', 'cart'); 
				
			}
		}
	}

	public static function updateAmericanTax()
	{

		// this part disables taxes for US mode an all pages unless a proper state is selected
		
		
		$view = JRequest::getVar('view'); 
		if ($view != 'cart' && ($view != 'opc'))
		{
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		$opc_usmode = OPCconfig::get('opc_usmode', false); 
			
		if (!empty($opc_usmode)) 
		{
			if (!class_exists('VirtueMartCart'))
			require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'cart.php');

			$cart = VirtueMartCart::getCart();
			
			
			
			if (empty($cart->ST) && (!empty($cart->BT)))
			{
				if (empty($cart->BT['virtuemart_state_id'])) $cart->BT['virtuemart_state_id'] = ' '; 
				//$GLOBALS['st_opc_state_empty'] = true; 
				$GLOBALS['opc_state_empty'] = true; 
			}
			else
			if (empty($cart->ST) && (empty($cart->BT)))
			{
				$cart->BT = array(); 
				$cart->BT['virtuemart_state_id'] = ' '; 
				$GLOBALS['opc_state_empty'] = true; 
			}
			if (!empty($cart->ST))
			{
				if (empty($cart->ST['virtuemart_state_id'])) $cart->BT['virtuemart_state_id'] = ' '; 
				$GLOBALS['st_opc_state_empty'] = true; 	
			}
			
		}
		}
	}

	public static function updateJoomlaCredentials()
	{
		// next few lines update user's access rights for each view of the page
		// there is a bug in joomla 1.7 to joomla 2.5.x which does not update the cached authLevels variable of the user in some cases (right after registration)
		if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) {
			$instance = JFactory::getSession()->get('user');
			if ((!empty($instance->id) && (empty($instance->opc_checked))))
			{
				$u = new JUser((int) $instance->id);
				$u->opc_checked = true; 
				JFactory::getSession()->set('user', $u); 
			}
		}
	}
	public static function setItemid()
	{
		$view = JRequest::getWord('view'); 
		$view = strtolower($view); 
		$task = JRequest::getWord('task', ''); 
		$task = strtolower($task);
		$option = JRequest::getVar('option', '');  


		$allowed = array('com_virtuemart', 'com_onepage'); 
		if (!in_array($option, $allowed)) return; 

		$task_a = array('pluginuserpaymentcancel', 'add'); 
		$views = array('cart', 'opc'); 
		if ( (in_array($view, $views) && ($task !== 'checkout')) || (defined('OPC_VIEW_LOADED')) || (in_array($task, $task_a)))
		{
			
			
			require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
			$newitemid = OPCconfig::getValue('opc_config', 'newitemid', 0, 0, true); 
			
			if (!empty($newitemid))
			{
				$GLOBALS['Itemid'] = $newitemid; 
				$_REQUEST['Itemid'] = $newitemid; 
				$_POST['Itemid'] = $newitemid; 
				$_GET['Itemid'] = $newitemid; 
				JRequest::setVar('Itemid', $newitemid); 
			}
			return; 
		}
		
		$views = array('pluginresponse', 'cart', 'opc'); 
		$tasks = array('pluginresponsereceived', 'checkout'); 
		if ((in_array($view, $views)) && (in_array($task, $tasks)))
		{
			
			
			
			
			require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
			$op_customitemidty = OPCconfig::getValue('opc_config', 'op_customitemidty', 0, 0, true); 
			
			if (!empty($op_customitemidty))
			{
				$GLOBALS['Itemid'] = $op_customitemidty; 
				$_REQUEST['Itemid'] = $op_customitemidty; 
				$_POST['Itemid'] = $op_customitemidty; 
				$_GET['Itemid'] = $op_customitemidty; 
				JRequest::setVar('Itemid', $op_customitemidty); 
				
				
			}
		}
		
		
		

		
	}

	public static function fixVMbugVirtuemartUser()
	{
		$task = JRequest::getWord('task'); 
		$option = JRequest::getWord('option'); 
		$view = JRequest::getWord('view'); 
		$layout = JRequest::getVar('layout', ''); 
		if (($view == 'user') && empty($task) && ($layout=='default'))
		{
			JRequest::setVar('default', null); 
			unset($_REQUEST['layout']); 
			
		}
	}
	public static function keyCaptchaSupport()
	{

		$option = JRequest::getVar('option'); 
		if(('com_virtuemart' == $option) || ('com_onepage' == $option)) {
			
			$controller = JRequest::getWord('controller', JRequest::getWord('view', 'virtuemart'));
			$view = JRequest::getWord('view', 'virtuemart'); 
			$task = JRequest::getCMD('task');
			
			$db = JFactory::getDBO(); 
			$q = 'select `enabled` from `#__extensions` where `element` = "keycaptcha" and enabled = 1 limit 0,1'; 
			$db->setQuery($q); 
			$r = $db->loadResult(); 
			
			///index.php?option=com_virtuemart&view=opc&controller=opc&task=checkout
			if (!empty($r))
			if (($view == 'opc') && ($task == 'checkout'))
			{
				// disable key captcha: 
				$first_name = JRequest::getVar('first_name', ''); 
				JRequest::setVar('opc_first_name', $first_name); 
				JRequest::setVar('first_name', null); 
				unset($_POST['first_name']); 
				unset($_GET['first_name']); 
				unset($_REQUEST['first_name']); 
				
			}
			
			
			
		}
	}
}
