<?php
/**
 *---------------------------------------------------------------------------------------
 * @package      VP One Page Checkout - Joomla! System Plugin
 * @subpackage   For VirtueMart 3+
 *---------------------------------------------------------------------------------------
 * @copyright    Copyright (C) 2012-2017 VirtuePlanet Services LLP. All rights reserved.
 * @license      GNU General Public License version 2 or later; see LICENSE.txt
 * @authors      Abhishek Das
 * @email        info@virtueplanet.com
 * @link         https://www.virtueplanet.com
 *---------------------------------------------------------------------------------------
 * $Revision: 3 $
 * $LastChangedDate: 2017-09-20 20:00:08 +0530 (Wed, 20 Sep 2017) $
 * $Id: vponepagecheckout.php 3 2017-09-20 14:30:08Z Abhshek Das $
 * --------------------------------------------------------------------------------------
*/
defined('_JEXEC') or die;

if(!class_exists('VPOPCHelper'))
{
	// We need VP One Page Checkout helper
	require dirname(__FILE__) . '/cart/helper.php';
}

/**
* VP One Page Checkout system plugin class
* For VirtueMart 3. Comaptible to Joomla! 2.5 and Joomla! 3
* 
* @since 3.1
*/
class plgSystemVPOnePageCheckout extends JPlugin
{
	/**
	* Joomla! Plugin Standard Constructor
	* @param undefined $subject
	* @param undefined $params
	* 
	* @return void
	*/
	function __construct(&$subject, $params)
	{
		parent::__construct($subject, $params);
	}
	
	/**
	* After dispatch events
	* 
	* @return void
	*/
	public function onAfterDispatch()
	{
		$app = JFactory::getApplication();

		// If admin do nothing
		if($app->isAdmin()) 
		{
			return;
		}
		
		// Create a helper instance
		$helper = VPOPCHelper::getInstance($this->params);
		
		// Handle SSL redirections
		$helper->setSSLRules('onAfterDispatch');
	}
	
	/**
	* After route events
	* 
	* @return void
	*/
	public function onAfterRoute()
	{
		$app = JFactory::getApplication();

		// If admin do nothing
		if($app->isAdmin()) 
		{
			if($app->input->getCmd('ctask') == 'getplgversion')
			{
				// Create a helper instance
				$helper = VPOPCHelper::getInstance($this->params);
				
				// Return installed plugin version on admin request
				$helper->getOPCPluginVersion();
			}
			
			if(!version_compare(JVERSION, '3.0.0', 'ge'))
			{
				if($app->input->getCmd('plugin') == 'vponepagecheckout')
				{
					// Register dlk helper class
					JLoader::register('VPDownloadKeyHelper', dirname(__FILE__) . '/fields/vpdownloadkey/helper.php');
					
					if($app->input->getCmd('method') == 'validatedlk')
					{
						// Return validation result
						return VPDownloadKeyHelper::getInstance()->validate($this->params->get('pid', 9));
					}
					elseif($app->input->getCmd('method') == 'revalidatedlk')
					{
						// Return validation result
						return VPDownloadKeyHelper::getInstance()->revalidate($this->params->get('pid', 9));
					}
				}
			}
			
			return;
		}
		
		// Create a helper instance
		$helper = VPOPCHelper::getInstance($this->params);
		
		// If it is VirtueMart Cart Page
		if($helper->isCart())
		{
			// If not compatible then we can not proceed
			if(!$helper->isCompatible())
			{
				return false;
			}
			
			if(!class_exists('VirtueMartViewCart'))
			{
				$viewPath = JPath::clean(dirname(__FILE__) . '/cart/cartview.html.php');
				require $viewPath;
			}
			else
			{
				$msg  = 'VP One Page Checkout plugin could not be loaded. ';
				$msg .= 'You are already using another third party VirtueMart Checkout system ';
				$msg .= 'in your site which does not allow the plugin to get loaded. ';
				$msg .= 'Please disable the same and try again.';
				$app->enqueueMessage($msg);
			}
			
			// Handle all after route actions
			$result = $helper->handleAfterRouteActions();
			
			if($result === true)
			{
				return;
			}
		}
		
		// Handle SSL redirections
		$helper->setSSLRules('onAfterRoute');
	}
	
	/**
	* Before head compile events
	* 
	* @return void
	*/
	public function onBeforeCompileHead()
	{
		$app = JFactory::getApplication();
		
		// If admin do nothing
		if($app->isAdmin())
		{
			return;
		}
		
		// Create a helper instance
		$helper = VPOPCHelper::getInstance($this->params);
		
		// If it is VirtueMart Cart Page
		if($helper->isCart())
		{
			// If not compatible then we can not proceed
			if(!$helper->isCompatible())
			{
				return false;
			}
			
			$helper->loadAssets();
			VPOPCHelper::loadVPOPCScripts();
		}
	}
	
	/**
	* Before render events
	* 
	* @return void
	*/	
	public function onBeforeRender()
	{
		$app = JFactory::getApplication();
		
		// If admin do nothing
		if($app->isAdmin())
		{
			return;
		}
		
		// Create a helper instance
		$helper = VPOPCHelper::getInstance($this->params);
		
		// Get the original system messages in the cart page.
		// Only if hide system message option enabled.
		if($helper->isCart() && $this->params->get('hide_system_msg', 1))
		{
			// If not compatible then we can not proceed
			if(!$helper->isCompatible())
			{
				return false;
			}
			
			// This will save the original messages and rendered html in helper instance
			$helper->getRenderedMessages(false);
			$helper->saveOriginalMessages();
		}
	}
	
	/**
	* After render events
	* 
	* @return void
	*/
	public function onAfterRender()
	{
		$app = JFactory::getApplication();
		
		// If admin do nothing
		if($app->isAdmin())
		{
			return;
		}
		
		// Create a helper instance
		$helper = VPOPCHelper::getInstance($this->params);
		
		// Hide system message it cart page.
		// Only if hide system message option enabled.
		if($helper->isCart())
		{
			// If not compatible then we can not proceed
			if(!$helper->isCompatible())
			{
				return false;
			}
			
			if($this->params->get('hide_system_msg', 1))
			{
				// Hide necessary system messages
				$helper->hideSystemMessages();
			}
			
			if($this->params->get('show_preloader', 1))
			{
				$helper->addPreloader();
			}
		}
		
		// Save the last visited page url after render to avoid error pages
		$helper->saveLastVisitedPage();
	}
	
	/**
	 * On content form preparation.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	function onContentPrepareForm($form, $data)
	{
		if(!($form instanceof JForm))
		{
			return;
		}
		
		$context = $form->getName();
		
		if($context == 'com_plugins.plugin' && !empty($data->element) && $data->element == 'vponepagecheckout')
		{
			if(!JPluginHelper::isEnabled('system', 'vpadvanceduser') || !defined('JPATH_ADVANCEDUSER_ADMIN') || !version_compare(JVERSION, '3.5', 'ge'))
			{
				$form->setFieldAttribute('show_social_login', 'readonly', 'true', 'params');
				$form->setFieldAttribute('social_btn_size', 'readonly', 'true', 'params');
				$form->setFieldAttribute('vpau_registration_mail', 'readonly', 'true', 'params');
				$form->setFieldAttribute('vpau_registration_mail', 'default', '0', 'params');
			}
		}
	}
	
	/**
	* Ajax dlk validation event
	* 
	* @return json
	*/
	public function onAjaxVPOnePageCheckout()
	{
		$app = JFactory::getApplication();
		
		if($app->isAdmin())
		{
			// Register dlk helper class
			JLoader::register('VPDownloadKeyHelper', dirname(__FILE__) . '/fields/vpdownloadkey/helper.php');
			
			if($app->input->getCmd('method') == 'validatedlk')
			{
				// Return validation result
				return VPDownloadKeyHelper::getInstance()->validate($this->params->get('pid', 9));
			}
			elseif($app->input->getCmd('method') == 'revalidatedlk')
			{
				// Return validation result
				return VPDownloadKeyHelper::getInstance()->revalidate($this->params->get('pid', 9));
			}
		}
	}
	
	public function onInstallerBeforePackageDownload(&$url, &$headers)
	{
		// Register dlk helper class
		JLoader::register('VPDownloadKeyHelper', dirname(__FILE__) . '/fields/vpdownloadkey/helper.php');
		
		return VPDownloadKeyHelper::getInstance()->addDlk($url, $headers);
	}
	
	public function plgVmOnShowOrderBEShipment($virtuemart_order_id, $virtuemart_payment_id)
	{
		// Create a helper instance
		return VPOPCHelper::getInstance($this->params)->onShowOrderAdmin($virtuemart_order_id, $virtuemart_payment_id);
	}
}