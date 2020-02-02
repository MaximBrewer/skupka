<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     sh404SEF
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     4.7.3.3292
 * @date        2016-03-15
 *
 */

// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC')) die('Direct Access to this location is not allowed.');

class Sh404sefHelperAnalytics_auth
{
	const GA_OAUTH2_ENDPOINT = 'https://www.googleapis.com/oauth2/v3/token';

	public static function getGaAuthClientId($key = null)
	{
		$clientIds = Sh404sefFactory::getPConfig()->gaAuthClientIds;
		if (is_null($key))
		{
			$key = mt_rand(0, count($clientIds) - 1);
		}
		else if (empty($clientIds[$key]))
		{
			$msg = 'Analytics oAuth: invalid client id key ' . $key;
			ShlSystem_Log::error('sh404sef', '%s::%d: %s', __METHOD__, __LINE__, $msg);
			throw new Exception($msg);
		}

		return array('key' => $key, 'client_id_def' => $clientIds[$key]);
	}

	/**
	 * Performs a token exchange with Google, based on provided
	 * authorization token
	 *
	 * @param $data
	 * @return array
	 */
	public static function exchangeTokens(&$data)
	{
		// get back the client id key
		$clientIdKey = isset($data['wbgaauth_client_id_key']) ? $data['wbgaauth_client_id_key'] : null;
		if (is_null($clientIdKey))
		{
			self::_failExchangeTokens($data, 'Analytics: Invalid or no clientId key trying to authorize Google Analytics');

		}
		// then the client id definition
		$clientIds = Sh404sefFactory::getPConfig()->gaAuthClientIds;
		if (empty($clientIds[$clientIdKey]))
		{
			self::_failExchangeTokens($data, 'Analytics: No valid client ID record trying to authorize Google Analytics (' . $clientIdKey . ')');
		}

		// get the authorization token obtained from Google
		$authToken = empty($data['wbgaauth_auth_token']) ? null : $data['wbgaauth_auth_token'];
		if (empty($authToken))
		{
			self::_failExchangeTokens($data, 'Analytics: Invalid or no authorization token trying to authorize Google Analytics');
		}

		// request access and refresh tokens from Google
		$hClient = Sh404sefHelperAnalytics::getHttpClient();

		// set params
		$hClient->setUri(self::GA_OAUTH2_ENDPOINT);
		$hClient->setConfig(array('maxredirects' => 3, 'timeout' => 10));
		$hClient->setMethod(Zendshl_Http_Client::POST);
		$hClient->setEncType('application/x-www-form-urlencoded');

		// request data
		$postData = array('code' => $authToken, 'client_id' => $clientIds[$clientIdKey]['id'], 'client_secret' => $clientIds[$clientIdKey]['secret'],
			'redirect_uri' => $clientIds[$clientIdKey]['redirect_uri'], 'grant_type' => $clientIds[$clientIdKey]['grant_type']);

		ShlSystem_Log::debug('sh404sef', '%s::%d: %s', __METHOD__, __LINE__, 'Analytics: preparing to make token exchange request with  ' . print_r($postData, true));
		$hClient->setParameterPost($postData);

		// perform the request
		try
		{
			$rawResponse = Sh404sefHelperAnalytics::request($hClient);
			$status = $rawResponse->getStatus();
			if ($status != 200)
			{
				ShlSystem_Log::debug('sh404sef', '%s::%d: %s', __METHOD__, __LINE__, 'Google token exchange: error status code ' . $status);
				throw new Sh404sefExceptionDefault('Google token exchange: error status code :' . $status);
			}

			// collect json
			$body = $rawResponse->getBody();
			$response = json_decode($body);
			if (!empty($response))
			{
				// supposedly valid response, transfer to our data structure
				$data['wbgaauth_access_token'] = empty($response->access_token) ? '' : $response->access_token;
				$data['wbgaauth_refresh_token'] = empty($response->refresh_token) ? '' : $response->refresh_token;
				$data['wbgaauth_expires_on'] = empty($response->expires_in) ? '' : time() + $response->expires_in;
				$data['wbgaauth_token_type'] = empty($response->token_type) ? '' : $response->token_type;
			}
			else
			{
				$msg = 'Google token exchange: empty or invalid response body ' . print_r($body, true);
				ShlSystem_Log::debug('sh404sef', '%s::%d: %s', __METHOD__, __LINE__, $msg);
				throw new Sh404sefExceptionDefault($msg);
			}
		}
		catch (Exception $e)
		{
			self::_failExchangeTokens($data, $e->getMessage());
		}
	}

	private static function _failExchangeTokens(&$data, $msg)
	{
		ShlSystem_Log::error('sh404sef', '%s::%d: %s', __METHOD__, __LINE__, $msg);
		// nuke GA auth elements, so as not to override current data
		unset($data['wbgaauth_access_token']);
		unset($data['wbgaauth_refresh_token']);
		unset($data['wbgaauth_expires_on']);
		unset($data['wbgaauth_token_type']);
		throw new Exception($msg);
	}

	/**
	 * Get oauth access token
	 * If expired, use refresh token to renew
	 *
	 * @return string
	 */
	public static function getAccessToken()
	{
		$config = Sh404sefFactory::getConfig();
		$token = empty($config->wbgaauth_access_token) ? '' : $config->wbgaauth_access_token;
		if (empty($token))
		{
			throw new Exception(JText::_('COM_SH404SEF_ANALYTICS_AUTH_ERR_NO_AUTH'));
		}
		$config->wbgaauth_expires_on = (int) $config->wbgaauth_expires_on;
		if (empty($config->wbgaauth_expires_on))
		{
			throw new Exception('Invalid Analytics authorization configuration (empty expires_on), please redo Analytics authorization.');
		}

		if (time() > $config->wbgaauth_expires_on)
		{
			// expired, need to refresh
			if (empty($config->wbgaauth_refresh_token))
			{
				throw new Exception('Invalid Analytics authorization configuration (empty refresh_token), please redo Analytics authorization.');
			}

			self::refreshToken();
			// refresh token will have updated config, let's read it again
			$config = Sh404sefFactory::getConfig(true);
		}

		return $config->wbgaauth_access_token;
	}

	private static function refreshToken()
	{
		$config = Sh404sefFactory::getConfig();
		// then the client id definition
		$clientIds = Sh404sefFactory::getPConfig()->gaAuthClientIds;
		if (empty($clientIds[$config->wbgaauth_client_id_key]))
		{
			self::_failExchangeTokens($data, 'No valid client ID record trying to authorize Google Analytics (' . $config->wbgaauth_client_id_key . ')');
		}

		// request access and refresh tokens from Google
		// use a fresh http client, so as not disturb possibly ongoing http requests
		$hClient = Sh404sefHelperAnalytics::getHttpClient($new = true);

		// set params
		$hClient->setUri(self::GA_OAUTH2_ENDPOINT);
		$hClient->setConfig(array('maxredirects' => 3, 'timeout' => 10));
		$hClient->setMethod(Zendshl_Http_Client::POST);
		$hClient->setEncType('application/x-www-form-urlencoded');

		// request data
		$postData = array('refresh_token' => $config->wbgaauth_refresh_token,
			'client_id' => $clientIds[$config->wbgaauth_client_id_key]['id'],
			'client_secret' => $clientIds[$config->wbgaauth_client_id_key]['secret'],
			'grant_type' => 'refresh_token');

		ShlSystem_Log::debug('sh404sef', '%s::%d: %s', __METHOD__, __LINE__, 'Analytics: preparing to make token exchange request with  ' . print_r($postData, true));
		$hClient->setParameterPost($postData);

		// perform the request
		try
		{
			$rawResponse = Sh404sefHelperAnalytics::request($hClient);
			$status = $rawResponse->getStatus();
			if ($status != 200)
			{
				ShlSystem_Log::debug('sh404sef', '%s::%d: %s', __METHOD__, __LINE__, 'Google token exchange: error status code ' . $status);
				throw new Sh404sefExceptionDefault('Google token exchange: error status code :' . $status);
			}

			// collect json
			$body = $rawResponse->getBody();
			$response = json_decode($body);
			if (!empty($response))
			{
				// supposedly valid refreshed token, store to configuration
				$sh404sefSettings = Sh404sefHelperGeneral::getComponentParams($forceRead = true);
				if (!empty($response->access_token))
				{
					$sh404sefSettings->set('wbgaauth_access_token', $response->access_token);
				}
				if (!empty($response->expires_in))
				{
					$sh404sefSettings->set('wbgaauth_expires_on', time() + $response->expires_in);
				}
				if (!empty($response->token_type))
				{
					$sh404sefSettings->set('wbgaauth_token_type', $response->token_type);
				}

				$saved = Sh404sefHelperGeneral::saveComponentParams($sh404sefSettings);
				if (!$saved)
				{
					$msg = 'Google token exchange: error saving refreshed token to database ';
					ShlSystem_Log::debug('sh404sef', '%s::%d: %s', __METHOD__, __LINE__, $msg);
					throw new Sh404sefExceptionDefault($msg);
				}

				$cache = JFactory::getCache('sh404sef_analytics');
				$cache->clean();
			}
			else
			{
				$msg = 'Google token exchange: empty or invalid response body ' . print_r($body, true);
				ShlSystem_Log::debug('sh404sef', '%s::%d: %s', __METHOD__, __LINE__, $msg);
				throw new Sh404sefExceptionDefault($msg);
			}
		}
		catch (Exception $e)
		{
			self::_failExchangeTokens($data, $e->getMessage());
		}
	}
}
