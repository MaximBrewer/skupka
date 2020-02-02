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

defined('_JEXEC') or die;

/**
 * Handle commercial extension update authorization
 *
 * @since       2.5
 */
class PlgInstallerSh404sef extends JPlugin
{
	/**
	 * @var    String  base update url, to decide whether to process the event or not
	 * @since  2.5
	 */
	private $baseUrl = 'https://u1.weeblr.com/dist/sh404sef/full';

	/**
	 * @var    String  extension identifier, to retrieve its params
	 * @since  2.5
	 */
	private $extension = 'com_sh404sef';

	/**
	 * @var    String    An id for your product, to be used by the web site when deciding to allow access
	 *                    Not mandatory, depends on subscription management system
	 * @since  2.5
	 */
	private $productId = 'sh404sef';

	/**
	 * @var    String    An edition type (full, free, lite,...) for the product
	 *                    Not mandatory, depends on subscription management system
	 * @since  2.5
	 */
	private $productEdition = 'full';

	/**
	 * Handle adding credentials to package download request
	 *
	 * @param   string $url url from which package is going to be downloaded
	 * @param   array $headers headers to be sent along the download request (key => value format)
	 *
	 * @return  boolean    true        always true
	 *
	 * @since   2.5
	 */
	public function onInstallerBeforePackageDownload(&$url, &$headers)
	{
		// are we trying to update our extension?
		if (strpos($url, $this->baseUrl) !== 0)
		{
			return true;
		}

		// read credentials from extension params or any other source
		$credentials = $this->fetchCredentials($url, $headers);

		// bind credentials to request, either in the urls, or using headers
		// or a combination of both
		$this->bindCredentials($credentials, $url, $headers);

		return true;
	}

	/**
	 * Bind credentials to the download request.
	 *
	 * @param    array $credentials whatever credentials were retrieved for the current user/website
	 * @param   string $url url from which package is going to be downloaded
	 * @param   array $headers headers to be sent along the download request (key => value format)
	 *
	 * @return void
	 */
	private function bindCredentials($credentials, &$url, &$headers)
	{
		$headers['X-download-auth-ts'] = time();
		$headers['X-download-auth-id'] = $credentials['id'];
		$headers['X-download-auth-token'] = sha1($headers['X-download-auth-ts'] . mt_rand() . $credentials['secret'] . $url);
		$headers['X-download-auth-sig'] = sha1(
			$credentials['id'] . $headers['X-download-auth-token'] . $credentials['secret'] . $headers['X-download-auth-ts'] . $this->productId
			. $this->productEdition
		);
	}

	/**
	 * Retrieve user credentials
	 *
	 * @param $url
	 * @param $headers
	 *
	 * @return mixed an array with credentials (id, secret), or null if none found
	 */
	private function fetchCredentials($url, $headers)
	{

		$credentials = null;

		// fetch credentials from extension parameters
		// Get the component information from the #__extensions table
		JLoader::import('joomla.application.component.helper');
		$component = JComponentHelper::getComponent($this->extension);

		$credentials = array('id' => trim($component->params->get('update_credentials_access', '')),
		                     'secret' => trim($component->params->get('update_credentials_secret', '')));

		if (empty($credentials['id']) || empty($credentials['secret'])
		)
		{
			JFactory::getApplication()->enqueueMessage("You haven't entered your update access and/or secret keys, needed for this update. Please read the <strong>Updating</strong> section of <a href='https://weeblr.com/documentation'>this extension documentation</a>", 'error');
		}

		return $credentials;
	}
}
