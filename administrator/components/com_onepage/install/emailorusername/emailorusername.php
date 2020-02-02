<?php
/**
 * This is a modification of the default joomla's authentication plugin by RuposTel.com
 *  
 *
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Joomla Authentication plugin
 *
 * @package		Joomla.Plugin
 * @subpackage	Authentication.joomla
 * @since 1.5
 */
class plgAuthenticationEmailorusername extends JPlugin
{
	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param object $subject The object to observe
	 * @param array  $config  An array that holds the plugin configuration
	 * @since 1.5
	 */
	function plgAuthenticationJoomla(& $subject, $config) {
		parent::__construct($subject, $config);
	}

	/**
	 * This method should handle any authentication and report back to the subject
	 *
	 * @access	public
	 * @param   array 	$credentials Array holding the user credentials
	 * @param 	array   $options     Array of extra options
	 * @param	object	$response	 Authentication response object
	 * @return	boolean
	 * @since 1.5
	 */
	function onAuthenticate( $credentials, $options, &$response )
	{
		jimport('joomla.user.helper');

		return $this->onUserAuthenticate($credentials, $options, $response);
	}


	/**
	 * This method should handle any authentication and report back to the subject
	 *
	 * @access	public
	 * @param	array	Array holding the user credentials
	 * @param	array	Array of extra options
	 * @param	object	Authentication response object
	 * @return	boolean
	 * @since 1.5
	 */
	function onUserAuthenticate($credentials, $options, &$response)
	{
		$response->type = 'Joomla';
		// Joomla does not like blank passwords
		if (empty($credentials['password'])) {
			$response->status = JAuthentication::STATUS_FAILURE;
			$response->error_message = JText::_('JGLOBAL_AUTH_EMPTY_PASS_NOT_ALLOWED');
			return;
		}

		// Initialise variables.
		$conditions = '';

		// Get a database object
		$db		= JFactory::getDbo();
		if(version_compare(JVERSION,'1.6.0','ge')) {
		// Joomla! 1.6 code here

		$query	= $db->getQuery(true);

		$query->select('id, password');
		$query->from('#__users');
		$query->where('username=' . $db->Quote($credentials['username']));
		
		define('ISJ25', true); 
		}
		else 
		{
		$query = "select `id`,`gid`,`password` from `#__users` where username = ".$db->Quote($credentials['username'])." limit 0, 1"; 		
		define('ISJ25', false); 
		}
		$db->setQuery($query);
		$result = $db->loadObject();

		
		
		
		if ($result) {
		
			$parts	= explode(':', $result->password);
			$crypt	= $parts[0];
			$salt	= @$parts[1];
			$testcrypt = JUserHelper::getCryptedPassword($credentials['password'], $salt);
			
			$match = false; 
			if (method_exists('JUserHelper', 'verifyPassword'))
			{
			  $match = JUserHelper::verifyPassword($credentials['password'], $result->password, $result->id);
			  if ($match === true)
			   {
			     $crypt = $testcrypt = 1; 
			   }
			   else 
			   {
			     $crypt = 1; 
				 $testcrypt = 2; 
			   }
			}
			
			
			if (($crypt == $testcrypt)) {
				$user = JUser::getInstance($result->id); // Bring this in line with the rest of the system
				$response->email = $user->email;
				$response->fullname = $user->name;
				if (JFactory::getApplication()->isAdmin()) {
					$response->language = $user->getParam('admin_language');
				}
				else {
					$response->language = $user->getParam('language');
				}
				if (ISJ25)
				$response->status = JAuthentication::STATUS_SUCCESS;
				else 
				$response->status = JAUTHENTICATE_STATUS_SUCCESS;
				
				$response->error_message = '';
				
				return; 
			
			} else {
				if (ISJ25)
				{
				$response->status = JAuthentication::STATUS_FAILURE;
				$response->error_message = JText::_('JGLOBAL_AUTH_INVALID_PASS');
				}
				else 
				{
				$response->status = JAUTHENTICATE_STATUS_FAILURE;
				$response->error_message = 'Invalid password';
				}
				
			}
		} else {
		    if (ISJ25)
			{
			$response->status = JAuthentication::STATUS_FAILURE;
			$response->error_message = JText::_('JGLOBAL_AUTH_NO_USER');
			}
			else
			{
			$response->status = JAUTHENTICATE_STATUS_FAILURE;
			$response->error_message = 'User does not exist';
			}
		}

		$credentials['username'] = trim($credentials['username']); 
		
		$db = JFactory::getDBO(); 
		$q = "select `id`,`username`,`password` from `#__users` where email LIKE ".$db->Quote($credentials['username'])." limit 0, 1"; 		
		$db->setQuery($q);
		$result = $db->loadObject();
		
		
		if ($result) {
	
			$parts	= explode(':', $result->password);
			$crypt	= $parts[0];
			$salt	= @$parts[1];
			$testcrypt = JUserHelper::getCryptedPassword($credentials['password'], $salt);
			
			// username mod here: 
			JRequest::setVar('username', $credentials['username']); 
			$credentials['username'] = $result->username; 
			
			
			$match = false; 
			if (method_exists('JUserHelper', 'verifyPassword'))
			{
			  $match = JUserHelper::verifyPassword($credentials['password'], $result->password, $result->id);
			  if ($match === true)
			   {
			     $crypt = $testcrypt = 1; 
			   }
			   else 
			   {
			     $crypt = 1; 
				 $testcrypt = 2; 
			   }
			}
			
			
			
			if ($crypt == $testcrypt) {
				$user = JUser::getInstance($result->id); // Bring this in line with the rest of the system
				
				$response->email = $user->email;
				$response->fullname = $user->name;
				$response->username = $result->username; 
				
				if (JFactory::getApplication()->isAdmin()) {
					$response->language = $user->getParam('admin_language');
				}
				else {
					$response->language = $user->getParam('language');
				}
				
				if (ISJ25)
				$response->status = JAuthentication::STATUS_SUCCESS;
				else 
				$response->status = JAUTHENTICATE_STATUS_SUCCESS;
				
				$response->error_message = '';
				
				return; 
			
			} else {
				if (ISJ25)
				{
				$response->status = JAuthentication::STATUS_FAILURE;
				$response->error_message = JText::_('JGLOBAL_AUTH_INVALID_PASS');
				}
				else 
				{
				$response->status = JAUTHENTICATE_STATUS_FAILURE;
				$response->error_message = 'Invalid password';
				}
			}
		} else {
			if (ISJ25)
			{
			$response->status = JAuthentication::STATUS_FAILURE;
			$response->error_message = JText::_('JGLOBAL_AUTH_NO_USER');
			}
			else
			{
			$response->status = JAUTHENTICATE_STATUS_FAILURE;
			$response->error_message = 'User does not exist';
			}
		}
		
		

		
		
		
	}
}
