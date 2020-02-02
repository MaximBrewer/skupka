<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     sh404SEF
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     4.7.3.3292
 * @date		2016-03-15
 */

// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC'))
	die('Direct Access to this location is not allowed.');

class Sh404sefHelperUpdates
{

	private static $_endPoint = 'https://u1.weeblr.com/public/direct/sh404sef/legacy';
	private static $_configEndPoint = 'https://u1.weeblr.com/public/direct/sh404sef/config';

	private static $_product = 'sh404sef';
	private static $_updateFile = 'sh404sef_updates_j3.xml';
	private static $_config = 'sh404sef_config_j3.xml';
	private static $_devConfig = 'sh404sef_config_dev_j3.xml';

	// store whether doing a forced update. Reason to store it
	// is to keep _doCheck() method w/o any parameters
	// as this would otherwise prevent caching from operating
	// normally
	private static $_forced = array();

	/**
	 * Obtain update information, as stored in cache
	 * by checkForUpdates
	 *
	 */
	public static function getUpdatesInfos($forced = false)
	{
		// store whether doing a forced check for updates
		self::$_forced['updates'] = $forced;

		// get cache object
		$cache = JFactory::getCache('sh404sef_updates');
		$cache->setLifetime(1440); // cache result for 24 hours
		$cache->setCaching(1); // force caching on

		// empty cache if we are going to look for updates
		if ($forced)
		{
			// clean our cache
			$cache->clean('sh404sef_updates');
		}

		$response = $cache->call(array('Sh404sefHelperUpdates', '_doCheck'));

		// find out whether we should update
		$response = self::_updateRequired($response);

		// return response, either dummy or from cache
		return $response;
	}

	/**
	 * Obtain remote configuration info
	 *
	 */
	public static function getRemoteConfig($forced = false)
	{
		static $storedResponse = false;

		if($storedResponse === false || $forced)
		{
			// store whether doing a forced check for updates
			self::$_forced['rconfig'] = $forced;

			// get cache object
			$cache = JFactory::getCache('sh404sef_rconfig');
			$cache->setLifetime(60); // cache result for an hour
			$cache->setCaching(1); // force caching on

			// empty cache if we are going to look for updates
			if ($forced)
			{
				// clean our cache
				$cache->clean('sh404sef_rconfig');
			}

			$storedResponse = $cache->call(array('Sh404sefHelperUpdates', '_doRemoteConfig'));
		}

		// return response, either dummy or from cache
		return $storedResponse;
	}

	public static function _doCheck()
	{
		// if not set to auto check and not forced to do so
		// when user click on "check updates" button
		// we don't actually try to get updates info
		$sefConfig = Sh404sefFactory::getConfig();

		// prepare a default response object
		$response = new stdClass();
		$response->status = true;
		$response->statusMessage = JText::_('COM_SH404SEF_CLICK_TO_CHECK_UPDATES');
		$response->current = 0;
		$response->note = '';
		$response->noteHtml = '';
		$response->changelogLink = '';
		$response->minVersionToUpgrade = 0;
		$response->maxVersionToUpgrade = 0;
		$response->shouldUpdate = false;
		$response->excludes = array();

		// check if allowed to auto check, w/o user clicking on button
		if (!$sefConfig->autoCheckNewVersion && empty(self::$_forced['updates']))
		{
			return $response;
		}

		// get an http client
		$hClient = new Zendshl_Http_Client;
		$hClient->setConfig(array('maxredirects' => 3, 'timeout' => 10));

		// find where to call ...
		$remoteConfig = self::getRemoteConfig($forced = false);
		// hardcoded default update server
		$defaultServers = array(array('url' => self::$_endPoint . '/' . self::$_updateFile));
		// try to read from remote, central, configuration file
		$remoteServers = empty($remoteConfig->config['updateservers']) ? array() : $remoteConfig->config['updateservers'];
		// merge default and remote
		$servers = array_merge($remoteServers, $defaultServers);
		// check for urls in both defaults and remote
		$serverList = array();
		foreach ($servers as $server)
		{
			if(!empty($server['url']))
			{
				$serverList[] = JString::trim(($server['url']));
			}	
		}
		$serverList = array_unique($serverList);
		// now iterate over server list, until we find one that responds
		foreach ($serverList as $server)
		{
			$response->status = true;

			// actually place a call to find about available updates
			try
			{
				$hClient->setUri($server);
			}
			catch (Exception $e)
			{
				//  move to next update server in list
				$response->status = false;
				continue;
			}

			// request file content
			$adapters = array('Zendshl_Http_Client_Adapter_Curl', 'Zendshl_Http_Client_Adapter_Socket');
			$rawResponse = null;

			foreach ($adapters as $adapter)
			{
				try
				{
					$hClient->setAdapter($adapter);
					$rawResponse = $hClient->request();
					break;
				}
				catch (Exception $e)
				{
					// we failed, let's try another method
				}
			}

			// (un)set flag if we have a response
			if (empty($rawResponse))
			{
				$response->status = false;
				$msg = 'unknown code';
				$response->statusMessage = JText::sprintf('COM_SH404SEF_COULD_NOT_CHECK_FOR_NEW_VERSION', $msg);
			}
			else if (!is_object($rawResponse) || $rawResponse->isError())
			{
				$response->status = false;
				$msg = method_exists($rawResponse, 'getStatus') ? $rawResponse->getStatus() : 'unknown code';
				$response->statusMessage = JText::sprintf('COM_SH404SEF_COULD_NOT_CHECK_FOR_NEW_VERSION', $msg);
			}
			else
			{
				// communication was fine, check the file type
				$type = $rawResponse->getHeader('Content-type');
				if (strtolower($type) != 'text/xml' && strtolower($type) != 'application/xml')
				{
					$response->status = false;
					$response->statusMessage = JText::sprintf('COM_SH404SEF_COULD_NOT_CHECK_FOR_NEW_VERSION', $rawResponse->getStatus());
				}
			}

			// if there was a valid response, break out of
			// loop over available update servers
			if ($response->status)
			{
				break;
			}
		}

		// if we were not able to contact any update server
		// return this
		if (!$response->status)
		{
			return $response;
		}

		try
		{
			// get an xml object and parse the response
			$xml = new SimpleXMLElement($rawResponse->getBody());

			// into our response object
			// first version of version check used an xml file that could only hold one
			// version data set. So we only iterate over the "update" group if it exists
			if (empty($xml->update))
			{
				$response = self::_readUpdateInformation($xml, $response);
			}
			else
			{
				foreach ($xml->update as $updateRecord)
				{
					$product = (string) $updateRecord->product[0];

					// only use if this update record is for us
					if (strtolower($product) == strtolower(self::$_product))
					{
						$response = self::_readUpdateInformation($updateRecord, $response);

						// find if user should update
						$response = self::_updateRequired($response);

						// if found an update, break out
						if ($response->shouldUpdate)
						{
							break;
						}
					}
				}
			}
		}
		catch (Exception $e)
		{

		}

		// check if this is a valid information file
		return $response;
	}

	protected static function _readUpdateInformation($updateRecord, $response)
	{

		$response->current = (string) $updateRecord->current[0];
		$response->note = (string) $updateRecord->note[0];
		$response->noteHtml = (string) $updateRecord->noteHtml[0];
		$response->changelogLink = (string) $updateRecord->changelogLink[0];
		$response->downloadLink = (string) $updateRecord->downloadLink[0];
		$response->minVersionToUpgrade = (string) $updateRecord->minVersionToUpgrade[0];
		$response->maxVersionToUpgrade = (string) $updateRecord->maxVersionToUpgrade[0];
		$rawExcludes = $updateRecord->exclude;
		$response->excludes = array();
		if (!empty($rawExcludes))
		{
			foreach ($rawExcludes as $exclude)
			{
				$response->excludes[] = (string) $exclude;
			}
		}

		return $response;
	}

	public static function _doRemoteConfig()
	{

		// if not set to auto check and not forced to do so
		// when user click on "check updates" button
		// we don't actually try to get updates info
		$sefConfig = &Sh404sefFactory::getConfig();

		// prepare a default response object
		$response = new stdClass();
		$response->status = true;
		$response->config = array();

		// check if allowed to read central configuration file
		if (!$sefConfig->canReadRemoteConfig)
		{
			return $response;
		}

		// get an http client
		$hClient = new Zendshl_Http_Client;

		// request file content
		$adapters = array('Zendshl_Http_Client_Adapter_Curl', 'Zendshl_Http_Client_Adapter_Socket');
		$rawResponse = null;

		foreach ($adapters as $adapter)
		{
			try
			{
				// set params
				//$hClient->setUri( self::$_configEndPoint . '/' . self::$_devConfig);
				$hClient->setUri(self::$_configEndPoint . '/' . self::$_config);
				$hClient->setConfig(array('maxredirects' => 3, 'timeout' => 10));
				$hClient->setAdapter($adapter);
				$rawResponse = $hClient->request();
				break;
			}
			catch (Exception $e)
			{
				// we failed, let's try another method
			}
		}

		// return if error
		if (empty($rawResponse))
		{
			$response->status = false;
			return $response;
		}
		if (!is_object($rawResponse) || $rawResponse->isError())
		{
			$response->status = false;
			return $response;
		}

		// check the file
		$type = $rawResponse->getHeader('Content-type');
		if (strtolower($type) != 'text/xml' && strtolower($type) != 'application/xml')
		{
			$response->status = false;
			return $response;

		}
		// should be OK then
		$response->status = true;

		// get an xml object and parse the response
		$rawConfig = simplexml_load_string($rawResponse->getBody());

		// into our response object
		$response->config = array();
		if (!empty($rawConfig))
		{
			foreach ($rawConfig->config as $configName => $configElement)
			{
				foreach ($configElement as $configKey => $values)
				{
					if (is_string($values))
					{
						$response->config[$configKey] = $values;
					}
					else
					{
						$counter = 0;
						foreach ($values as $arrayName => $arrayValues)
						{
							$children = $arrayValues->children();
							$attr = (string) $arrayValues->attributes();
							$id = empty($attr) ? $counter : $attr;
							$count = count($children);
							if ($count > 0)
							{
								foreach ($arrayValues as $key => $value)
								{
									$response->config[$configKey][$id][$key] = (string) $value;
								}
							}
							else
							{
								$response->config[$configKey][] = (string) $arrayValues;
							}
							$counter++;
						}
					}
				}
			}
		}

		// check if this is a valid information file
		return $response;
	}

	/**
	 * Use response object from request to update info server
	 * to find if an update is required
	 *
	 * @param object $response
	 */
	private static function _updateRequired($response)
	{
		// get configuration
		$sefConfig = &Sh404sefFactory::getConfig();
		// compare versions
		$response->shouldUpdate = version_compare($sefConfig->version, $response->current, '<');
		$response->shouldUpdate = $response->shouldUpdate && version_compare($sefConfig->version, $response->minVersionToUpgrade, 'ge');
		$response->shouldUpdate = $response->shouldUpdate
			&& (empty($response->maxVersionToUpgrade) || version_compare($sefConfig->version, $response->maxVersionToUpgrade, '<'));
		if ($response->shouldUpdate)
		{
			// check specific versions exclusion list
			$response->shouldUpdate = $response->shouldUpdate && !in_array($sefConfig->version, $response->excludes);
		}

		// build status message based on result of should update calculation
		$newVersionText = version_compare(JVERSION, '3.0.0', 'ge') ? 'COM_SH404SEF_NEW_VERSION_AVAILABLE_NO_HTML' : 'COM_SH404SEF_NEW_VERSION_AVAILABLE';
		$response->statusMessage = $response->shouldUpdate ? JText::sprintf($newVersionText)
			: (JText::sprintf('COM_SH404SEF_YOU_ARE_UP_TO_DATE'));

		// return whatever we found
		return $response;
	}
}
