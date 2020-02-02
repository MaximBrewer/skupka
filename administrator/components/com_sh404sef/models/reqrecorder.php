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
 */

// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC'))
	die();

class Sh404sefModelReqrecorder
{

	const REQUEST_404 = '404s';
	const REQUEST_SHURL = 'shurls';
	const REQUEST_ALIAS = 'aliases';

	private $type = '';

	private static $_instance = null;

	/**
	 * Singleton method
	 *
	 * @return object instance of Sh404sefModelReqrecorder
	 */
	public static function getInstance($type)
	{
		if (is_null(self::$_instance))
		{
			self::$_instance = new Sh404sefModelReqrecorder($type);
		}

		return self::$_instance;
	}

	private function __construct($type)
	{
		$type = strtolower($type);
		switch ($type)
		{
			case self::REQUEST_404:
			case self::REQUEST_SHURL:
			case self::REQUEST_ALIAS:
				$this->type = $type;
				break;
			default:
				throw new RuntimeException('Invalid request type ' . $type . ' instantiating Sh404sefModelReqrecorder');
				break;
		}
	}

	/**
	 * Record detailed info on a URL hit
	 *
	 * Whether request is internal, and the actual referrer can be passed
	 * If missing, they'll be computed
	 *
	 * @param string $url
	 * @param boolean $isInternal
	 * @param string $referrer
	 */
	public function record($url, $target = '', $isInternal = null, $referrer = null)
	{
		// build record
		$record = array();
		$record['url'] = $url;
		$record['target'] = $target;
		$record['target_domain'] = empty($target) ? '' : $this->_getDomain($target);
		$record['referrer'] = is_null($referrer) ? (empty($_SERVER['HTTP_REFERER']) ? '' : $_SERVER['HTTP_REFERER']) : $referrer;
		$record['referrer_domain'] = empty($record['referrer']) ? '' : $this->_getDomain($record['referrer']);
		$record['user_agent'] = empty($_SERVER['HTTP_USER_AGENT']) ? '' : $_SERVER['HTTP_USER_AGENT'];
		$record['ip_address'] = empty($_SERVER['REMOTE_ADDR']) ? '-' : $_SERVER['REMOTE_ADDR'];
		$record['datetime'] = ShlSystem_Date::getSiteNow('Y-m-d H:i:s');
		if (is_null($isInternal))
		{
			$record['type'] = !empty($record['referrer']) && JUri::isInternal($record['referrer']) ? Sh404sefHelperUrl::IS_INTERNAL : Sh404sefHelperUrl::IS_EXTERNAL;
		}
		else
		{
			$record['type'] = $isInternal;
		}

		ShlDbHelper::insert('#__sh404sef_hits_' . $this->type, $record);
	}

	private function _getDomain($url)
	{
		if (empty($url))
		{
			$domain = '';
		}
		else
		{
			$uri = new JUri($url);
			$domain = $uri->toString(array('host'));
		}

		return $domain;
	}
}
