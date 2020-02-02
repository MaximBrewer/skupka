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
 * $Revision: 5 $
 * $LastChangedDate: 2017-09-20 20:07:57 +0530 (Wed, 20 Sep 2017) $
 * $Id: helper.php 5 2017-09-20 14:37:57Z Abhshek Das $
 * --------------------------------------------------------------------------------------
*/
defined('_JEXEC') or die;

class VPDownloadKeyHelper extends JObject
{
	protected $uname;
	protected $dlk;
	protected $data;
	protected $time_start;
	protected $time_end;
	protected $input;
	protected $adapter;
	protected $processed;
	protected $remote_host;
	protected $remote_host_secured;
	protected $try_count;
	protected $source  = 'www.virtueplanet.com';
	protected $updates = null;
	
	protected static $instance = null;
	
	public function __construct($params = null)
	{
		$app = JFactory::getApplication();

		if(version_compare(JVERSION, '3.0.0', 'ge'))
		{
			$input       = $app->input;
			$this->uname = $input->get('uname', '', 'GET', 'USERNAME');
			$this->dlk   = $input->get('dlk', '', 'GET', 'STRING');
			$this->data  = $input->get('data', '', 'GET', 'BASE64');
		}
		else
		{
			$this->uname = JRequest::getVar('uname', '', 'GET', 'USERNAME');
			$this->dlk   = JRequest::getVar('dlk', '', 'GET', 'STRING');
			$this->data  = JRequest::getVar('data', '', 'GET', 'BASE64');
		}
		
		$this->dlk         = self::cleanDlk($this->dlk);
		$this->adapter     = self::getAdapter();
		$this->input       = $app->input;
		$this->time_start  = microtime(true);
		$this->processed   = 0;
		$this->remote_host = 'http://validate.virtueplanet.com';
		$this->try_count   = 0;
		
		if(function_exists('extension_loaded') && extension_loaded('openssl'))
		{
			// Secured verification over SSL
			$this->remote_host_secured = 'https://www.virtueplanet.com/validate';
		}
	}

	/**
	* Method to get an instance of the the VPDownloadKeyHelper class
	* 
	* @return object VPDownloadKeyHelper class object
	*/
	public static function getInstance()
	{
		if(self::$instance === null)
		{
			self::$instance = new VPDownloadKeyHelper();
		}
		
		return self::$instance;
	}
	
	public static function getAdapter()
	{
		$options = new JRegistry(array('follow_location' => true));
		$adapter = JHttpFactory::getAvailableDriver($options);
		
		return $adapter;
	}
	
	public function validate($product_id)
	{
		$query = array();
		
		$query['uname']  = $this->input->get('uname', '', 'GET', 'USERNAME');
		$dlk             = $this->input->get('dlk', '', 'GET', 'STRING');
		$query['dlk']    = self::cleanDlk($dlk);
		$query['pid']    = (int) $product_id;
		$query['host']   = base64_encode(self::getHost());
		$query['_pkey']  = md5('time:' . $this->time_start . '.rand:' . mt_rand());

		return $this->_validate($query);
	}
	
	public function revalidate($product_id)
	{
		$data  = self::decodeData($this->data, true);
		$query = array();
		
		$query['uname']  = $data['uname'];
		$query['dlk']    = $data['dlk'];
		$query['pid']    = (int) $product_id;
		$query['host']   = base64_encode(self::getHost());
		$query['_pkey']  = md5('time:' . $this->time_start . '.rand:' . mt_rand());

		return $this->_validate($query);
	}
	
	protected function _validate($query)
	{
		$this->try_count++;
		
		$result = array('error' => true, 'msg' => '', 'return' => null, 'adapter' => '');
		
		if(!JSession::checkToken('get'))
		{
			$result['msg'] = 'Invalid token. Please refresh page and try again.';
			
			return $this->doReturn($result);
		}
		
		if(!$this->adapter)
		{
			$result['msg'] = 'JHttpFactory not present. Please upgrade your version of Joomla.';
			
			return $this->doReturn($result);
		}
		
		$url = $this->remote_host . '/index.php';
		$uri = JUri::getInstance($url);
		$userAgent = !empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'VirtuePlanet Verifier/2.0';
		
		try
		{
			$queryString = http_build_query($query, '', '&');
			$response    = $this->adapter->request('GET', $uri, $queryString, null, 30, $userAgent);
		}
		catch(Exception $e)
		{
			$result['msg'] = $e->getMessage();
			
			return $this->doReturn($result);
		}
		
		if(empty($response))
		{
			$result['msg'] = 'Verification failed. No data received.';
			
			return $this->doReturn($result);
		}
		
		if($response->code != 200)
		{
			if($this->try_count == 1 && !empty($this->remote_host_secured))
			{
				// Try again using secured host
				$this->remote_host = $this->remote_host_secured;
				
				return $this->_validate($query);
			}
			
			$result['msg'] = 'Error:' . $response->code . '. Verification failed. Invalid data received.';
			
			return $this->doReturn($result);
		}
		
		// Create a shortcut to response body
		$return = $response->body;
		
		if(empty($return))
		{
			$result['msg'] = 'Could not fetch data from remote server.';
		}
		else
		{
			if(is_string($return))
			{
				$return = @json_decode($return, true);
				
				if(!is_array($return) || empty($return))
				{
					$result['msg'] = 'Verification failed. Invalid data received.';
				}
				else
				{
					$host   = isset($return['host']) ? $return['host'] : '';
					$_pkey  = isset($return['_pkey']) ? $return['_pkey'] : '';
					
					if($host != $query['host'])
					{
						$result['msg'] = 'Host name verification failed.';
					}
					elseif($_pkey != $query['_pkey'])
					{
						$result['msg'] = 'Verification failed.';
					}
					else
					{
						// Verification success
						$result['error'] = false;
					}
					
					$date = JFactory::getDate();
					
					$return['dlk']  = $query['dlk'];
					$return['data'] = base64_encode($host . '|*|' . $query['dlk'] . '|*|' . $query['uname'] . '|*|' . intval($return['access']). '|*|' . $date->toSql());
					$return['last_checked'] = JHtml::_('date', $date, 'F d, Y H:i:s');
				}
			}
			else
			{
				$result['msg'] = 'Verification failed. Invalid data format.';
			}
		}
		
		if(!$result['error'] && !empty($return['dlk']))
		{
			if(!$result['error'])
			{
				$this->refreshUpdates($return['dlk']);
			}
		}
		
		if($result['error'] && !$result['msg'] && $return['message'])
		{
			$result['msg'] = $return['message'];
		}
		
		$result['return'] = $return;
		
		return $this->doReturn($result);
	}
	
	public function addDlk(&$url, &$headers)
	{
		$this->processed++;
		
		if(strpos($url, 'https://' . $this->source) !== 0 && strpos($url, 'http://' . $this->source) !== 0)
		{
			return true;
		}
		
		// If dlid is already added we do not need to do anything
		if(strpos($url, 'dlid=') !== false)
		{
			return true;
		}
		
		if(!$extension = $this->getExtension())
		{
			return true;
		}
		
		if(!$dlk = $extension->params->get('dlk', null))
		{
			return true;
		}
		
		if(strpos($url, '?') === false)
		{
			$url .= '?dlid=' . $dlk;
		}
		else
		{
			$url .= '&dlid=' . $dlk;
		}

		return true;
	}
	
	protected function getExtension()
	{
		if($this->updates === null)
		{
			$app = JFactory::getApplication();
			$uids = version_compare(JVERSION, '3.0.0', 'ge') ?
			        $app->input->get('cid', array(), 'array') :
			        JRequest::getVar('cid', array(), '', 'array');
			
			JArrayHelper::toInteger($uids, array());
			
			$db = JFactory::getDbo();
			$query = $db->getQuery(true)
			            ->select('u.update_id, u.detailsurl, e.extension_id, e.element, e.params')
			            ->from('#__updates AS u')
			            ->join('INNER', '#__extensions AS e ON e.extension_id = u.extension_id')
			            ->where('u.update_id IN (' . implode(',', $uids) . ')');
			$db->setQuery($query);
			$updates = $db->loadObjectList('update_id');
			
			if($ucount = count($updates))
			{
				if($ucount > 1)
				{
					// Sort the updates as per order of uids array
					$updates = $this->sortArrayByArray($updates, $uids);
				}

				// Now reset the keys
				$this->updates = array_values($updates);
			}
			else
			{
				$this->updates = array();
			}
		}
		
		if($this->processed > 0)
		{
			$key       = ($this->processed - 1);
			$extension = isset($this->updates[$key]) ? $this->updates[$key] : false;
		}
		else
		{
			$extension = isset($this->updates[0]) ? $this->updates[0] : false;
		}
		
		if(is_object($extension))
		{
			$params = new JRegistry;
			$params->loadString($extension->params);
			
			if($download_key = $params->get('download_key', null))
			{
				$data = self::decodeData($download_key);
				$params->set('dlk', $data['dlk']);
			}
			
			$extension->params = $params;
		}
		
		return $extension;
	}
	
	public static function decodeData($string, $renewHost = false)
	{
		$data = array('host' => '', 'dlk' => '', 'uname' => '', 'access' => 0, 'last_checked' => '');

		if(empty($string) || !is_string($string))
		{
			return $data;
		}
		
		$string = @base64_decode($string);
		
		if(empty($string) || !is_string($string))
		{
			return $data;
		}
		
		$parts  = !empty($string) && strpos($string, '|*|') !== false ? explode('|*|', $string) : array();
		$newHost = self::getHost();
		
		if($renewHost)
		{
			$data['host']         = $newHost;
			$data['dlk']          = isset($parts[1]) ? self::cleanDlk($parts[1]) : '';
			$data['uname']        = isset($parts[2]) ? $parts[2] : '';
			$data['access']       = isset($parts[3]) ? intval($parts[3]) : 0;
			$data['last_checked'] = isset($parts[4]) ? $parts[4] : null;
		}
		else
		{
			$host = '';
			
			if(!empty($parts[0]))
			{
				$host = @base64_decode($parts[0]);
				
				if($host === false)
				{
					$host = $parts[0];
				}
			}
			
			$prefix = 'www.';
			
			if(!empty($host) && substr($host, 0, strlen($prefix)) == $prefix)
			{
				$host = substr($host, strlen($prefix));
			}

			if(!empty($newHost) && substr($newHost, 0, strlen($prefix)) == $prefix)
			{
				$newHost = substr($newHost, strlen($prefix));
			}

			if(!empty($newHost) && $host == $newHost)
			{
				$data['host']         = $newHost;
				$data['dlk']          = isset($parts[1]) ? self::cleanDlk($parts[1]) : '';
				$data['uname']        = isset($parts[2]) ? $parts[2] : '';
				$data['access']       = isset($parts[3]) ? intval($parts[3]) : 0;
				$data['last_checked'] = isset($parts[4]) ? $parts[4] : null;
			}
		}
		
		return $data;
	}
	
	protected static function getHost($host = null)
	{
		$host = $host ? $host : JUri::root();
		
		if(empty($host))
		{
			return '';
		}
		
		$parts = parse_url($host);
		
		$result = '';
		
		if(!empty($parts['host']))
		{
			$result .= $parts['host'];
		}
		
		if(!empty($parts['path']))
		{
			$result .= $parts['path'];
		}
		
		return $result;
	}
	
	protected function refreshUpdates($dlk)
	{
		$app = JFactory::getApplication();
		
		if(version_compare(JVERSION, '3.0.0', 'ge'))
		{
			$extension_id = $app->input->getInt('extension_id', 0);
		}
		else
		{
			$extension_id = JRequest::getInt('extension_id', 0);
		}

		if(!$extension_id || empty($dlk))
		{
			return false;
		}

		if(!$dlk = self::cleanDlk($dlk))
		{
			return false;
		}
		
		$db          = JFactory::getDbo();
		$extra_query = 'dlid=' . $dlk;
		
		// Get the update sites for current extension
		$query = $db->getQuery(true)
		            ->select('update_site_id')
		            ->from('#__update_sites_extensions')
		            ->where('extension_id = ' . (int) $extension_id);
		$db->setQuery($query);
		$updateSiteIDs = $db->loadColumn(0);

		if(count($updateSiteIDs))
		{
			foreach ($updateSiteIDs as $id)
			{
				$query->clear()
				      ->select('*')
				      ->from('#__update_sites')
				      ->where('update_site_id = ' . (int) $id);
				$db->setQuery($query);
				
				try
				{
					$updateSite = $db->loadObject();
				}
				catch (Exception $e)
				{
					return false;
				}
				
				if(!is_object($updateSite))
				{
					continue;
				}

				// Do we have the extra_query property (J 3.2+) and does it match?
				if (property_exists($updateSite, 'extra_query'))
				{
					if ($updateSite->extra_query == $extra_query)
					{
						continue;
					}
				}
				else
				{
					// Joomla! 3.1 or earlier. Updates may or may not work.
					continue;
				}
				
				$updateSite->update_site_id = $id;
				$updateSite->extra_query    = $extra_query;
				
				try
				{
					$db->updateObject('#__update_sites', $updateSite, 'update_site_id', true);
				}
				catch (Exception $e)
				{
					return false;
				}
			}
			
			return true;
		}
	}
	
	/**
	* Method to return JSON object values with proper header
	* 
	* @param arry $message Array to be return as JSON object
	* 
	* @return void
	*/
	protected function doReturn($output) 
	{
		$app     = JFactory::getApplication();
		$obLevel = ob_get_level();
		
		if($obLevel)
		{
			while ($obLevel > 0)
			{
				@ob_end_clean();
				$obLevel --;
			}
		}
		elseif(ob_get_contents())
		{
			@ob_clean();
		}
		
		header('Content-type: application/text');
		header('Content-type: application/json');
		header('Cache-Control: public,max-age=1,must-revalidate');
		header('Expires: ' . gmdate('D, d M Y H:i:s', ($_SERVER['REQUEST_TIME'] + 1)) . ' GMT');
		header('Last-modified: ' . gmdate('D, d M Y H:i:s', $_SERVER['REQUEST_TIME']) . ' GMT');
		
		if(function_exists('header_remove')) 
		{
			header_remove('Pragma');
		}
		
		$this->time_end = microtime(true);
		$execution_time = ($this->time_end - $this->time_start);
		
		if($execution_time < 1)
		{
			$execution_time = number_format(($execution_time * 1000), 2, '.', ',') . ' ms';
		}
		else
		{
			$execution_time = number_format($execution_time, 6, '.', ',') . ' s';
		}
		
		$output = (array) $output;
		$output['execution_time'] = $execution_time;
		
		echo json_encode($output);
		
		flush();
		$app->close();
	}
	
	protected static function cleanDlk($dlk)
	{
		if (empty($dlk))
		{
			return '';
		}
		
		$dlk = trim($dlk);

		if (empty($dlk))
		{
			return '';
		}
		
		// Is the Download Key too short?
		if (strlen($dlk) < 32)
		{
			return '';
		}
		
		if (strlen($dlk) > 32)
		{
			$dlk = substr($dlk, 0, 32);
		}
		
		$dlk = preg_replace("/[^a-zA-Z0-9]+/", "", $dlk);
		
		return $dlk;
	}
	
	protected function sortArrayByArray($array, $orderArray)
	{
		$array      = (array) $array;
		$orderArray = (array) $orderArray;
		$ordered    = array();
		
		foreach($orderArray as $key)
		{
			if(array_key_exists($key, $array))
			{
				$ordered[$key] = $array[$key];
				unset($array[$key]);
			}
		}
		
		return $ordered + $array;
	}
}