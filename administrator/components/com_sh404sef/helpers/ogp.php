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
{
	die('Direct Access to this location is not allowed.');
}

class Sh404sefHelperOgp
{
	protected static $_definitions = null;

	public static function getDefinitions()
	{
		if (is_null(self::$_definitions))
		{
			self::$_definitions = ShlSystem_Xml::fromFile(sh404SEF_ADMIN_ABS_PATH . 'helpers/ogp.xml');
		}

		return self::$_definitions;
	}

	/**
	 * Method to create a select list of possible Open Graph object types
	 *
	 * @access  public
	 * @param int ID of current item
	 * @param string name of select list
	 * @param boolean if true, changing selected item will submit the form (assume is an "adminForm")
	 * @param boolean , if true, a line 'Select all' is inserted at the start of the list
	 * @param string the "Select all" to be displayed, if $addSelectAll is true
	 * @return  string HTML output
	 */
	public static function buildOpenGraphTypesList($current, $name, $autoSubmit = false, $addSelectDefault = false, $selectDefaultTitle = '',
	                                               $customSubmit = '')
	{
		// build html options
		$data = array();
		foreach (self::getDefinitions() as $typeDef)
		{
			$data[] = array('id' => $typeDef['name'], 'title' => JText::_($typeDef['title']));
		}

		// add select all option
		if ($addSelectDefault)
		{
			$selectDefault = array('id' => SH404SEF_OPTION_VALUE_USE_DEFAULT, 'title' => $selectDefaultTitle);
			array_unshift($data, $selectDefault);
		}

		// use helper to build html
		$list = Sh404sefHelperHtml::buildSelectList($data, $current, $name, $autoSubmit, $addSelectAll = false, $selectAllTitle = '', $customSubmit);

		// return list
		return $list;
	}

	public static function buildOpenGraphTags()
	{
		$tags = array(
			'openGraphData' => '',
			'ogNameSpace' => '',
			'fbNameSpace' => ''
		);

		// get sh404sef config
		$sefConfig = Sh404sefFactory::getConfig();
		$pageInfo = Sh404sefFactory::getPageInfo();

		if (empty($sefConfig->shMetaManagementActivated) || !isset($sefConfig) || empty($pageInfo->currentNonSefUrl)
			|| (!empty($pageInfo->httpStatus) && $pageInfo->httpStatus == 404)
		)
		{
			return $tags;
		}

		$customData = Sh404sefHelperMetadata::getCustomMetaDataFromDb();

		// user can disable per url
		if ($customData->og_enable == SH404SEF_OPTION_VALUE_NO
			|| (empty($sefConfig->enableOpenGraphData) && $customData->og_enable == SH404SEF_OPTION_VALUE_USE_DEFAULT)
		)
		{
			return $tags;
		}

		$openGraphData = '';

		// add locale -  FB use underscore in language tags
		$locale = str_replace('-', '_', JFactory::getLanguage()->getTag());
		$openGraphData .= "\n" . '  <meta property="og:locale" content="' . $locale . '" />';

		// insert title
		if (!empty($pageInfo->pageTitle))
		{
			$openGraphData .= "\n" . '  <meta property="og:title" content="' . $pageInfo->pageTitle . '" />';
		}

		// insert description
		if ((($sefConfig->ogEnableDescription && $customData->og_enable_description == SH404SEF_OPTION_VALUE_USE_DEFAULT)
				|| $customData->og_enable_description == SH404SEF_OPTION_VALUE_YES) && !empty($pageInfo->pageDescription)
		)
		{
			$openGraphData .= "\n" . '  <meta property="og:description" content="' . $pageInfo->pageDescription . '" />';
		}

		// insert type
		$content = $customData->og_type == SH404SEF_OPTION_VALUE_USE_DEFAULT ? $sefConfig->ogType : $customData->og_type;
		if (!empty($content))
		{
			$openGraphData .= "\n" . '  <meta property="og:type" content="' . $content . '" />';
		}

		// insert url. If any, we insert the canonical url rather than current, to consolidate
		$content = empty($pageInfo->pageCanonicalUrl) ? $pageInfo->currentSefUrl : $pageInfo->pageCanonicalUrl;
		$content = Sh404sefHelperUrl::stripTrackingVarsFromSef($content);
		$openGraphData .= "\n" . '  <meta property="og:url" content="' . htmlspecialchars($content, ENT_COMPAT, 'UTF-8') . '" />';

		// insert image
		$content = empty($customData->og_image) ? $sefConfig->ogImage : $customData->og_image;
		if (!empty($content))
		{
			$content = JURI::base(false) . JString::ltrim($content, '/');
			$openGraphData .= "\n" . '  <meta property="og:image" content="' . $content . '" />';
			$secure = JUri::getInstance()->isSSL();
			if ($secure)
			{
				$openGraphData .= "\n" . '  <meta property="og:image:secure_url" content="' . $content . '" />';
			}
		}

		// insert site name
		if (($sefConfig->ogEnableSiteName && $customData->og_enable_site_name == SH404SEF_OPTION_VALUE_USE_DEFAULT)
			|| $customData->og_enable_site_name == SH404SEF_OPTION_VALUE_YES
		)
		{
			$content = empty($customData->og_site_name) ? $sefConfig->ogSiteName : $customData->og_site_name;
			$content = empty($content) ? JFactory::getApplication()->getCfg('sitename') : $content;
			if (!empty($content))
			{
				$content = htmlspecialchars(Sh404sefHelperMetadata::cleanUpDesc($content), ENT_COMPAT, 'UTF-8');
				$openGraphData .= "\n" . '  <meta property="og:site_name" content="' . $content . '" />';
			}
		}

		// insert location
		// disabled: Facebook removed all of that after reducing number of object types to bare minimum
		if (false
			&& (($sefConfig->ogEnableLocation && $customData->og_enable_location == SH404SEF_OPTION_VALUE_USE_DEFAULT)
				|| $customData->og_enable_location == SH404SEF_OPTION_VALUE_YES)
		)
		{
			$content = empty($customData->og_latitude) ? $sefConfig->ogLatitude : $customData->og_latitude;
			if (!empty($content))
			{
				$content = htmlspecialchars(Sh404sefHelperMetadata::cleanUpDesc($content), ENT_COMPAT, 'UTF-8');
				$openGraphData .= "\n" . '  <meta property="og:latitude" content="' . $content . '" />';
			}
			$content = empty($customData->og_longitude) ? $sefConfig->ogLongitude : $customData->og_longitude;
			if (!empty($content))
			{
				$content = htmlspecialchars(Sh404sefHelperMetadata::cleanUpDesc($content), ENT_COMPAT, 'UTF-8');
				$openGraphData .= "\n" . '  <meta property="og:longitude" content="' . $content . '" />';
			}
			$content = empty($customData->og_street_address) ? $sefConfig->ogStreetAddress : $customData->og_street_address;
			if (!empty($content))
			{
				$content = htmlspecialchars(Sh404sefHelperMetadata::cleanUpDesc($content), ENT_COMPAT, 'UTF-8');
				$openGraphData .= "\n" . '  <meta property="og:street-address" content="' . $content . '" />';
			}
			$content = empty($customData->og_locality) ? $sefConfig->ogLocality : $customData->og_locality;
			if (!empty($content))
			{
				$content = htmlspecialchars(Sh404sefHelperMetadata::cleanUpDesc($content), ENT_COMPAT, 'UTF-8');
				$openGraphData .= "\n" . '  <meta property="og:locality" content="' . $content . '" />';
			}
			$content = empty($customData->og_postal_code) ? $sefConfig->ogPostalCode : $customData->og_postal_code;
			if (!empty($content))
			{
				$content = htmlspecialchars(Sh404sefHelperMetadata::cleanUpDesc($content), ENT_COMPAT, 'UTF-8');
				$openGraphData .= "\n" . '  <meta property="og:postal-code" content="' . $content . '" />';
			}
			$content = empty($customData->og_region) ? $sefConfig->ogRegion : $customData->og_region;
			if (!empty($content))
			{
				$content = htmlspecialchars(Sh404sefHelperMetadata::cleanUpDesc($content), ENT_COMPAT, 'UTF-8');
				$openGraphData .= "\n" . '  <meta property="og:region" content="' . $content . '" />';
			}
			$content = empty($customData->og_country_name) ? $sefConfig->ogCountryName : $customData->og_country_name;
			if (!empty($content))
			{
				$content = htmlspecialchars(Sh404sefHelperMetadata::cleanUpDesc($content), ENT_COMPAT, 'UTF-8');
				$openGraphData .= "\n" . '  <meta property="og:country-name" content="' . $content . '" />';
			}
		}

		// insert contact
		// disabled: Facebook removed all of that after reducing number of object types to bare minimum
		if (false
			&& (($sefConfig->ogEnableContact && $customData->og_enable_contact == SH404SEF_OPTION_VALUE_USE_DEFAULT)
				|| $customData->og_enable_contact == SH404SEF_OPTION_VALUE_YES)
		)
		{
			$content = empty($customData->og_email) ? $sefConfig->ogEmail : $customData->og_email;
			if (!empty($content))
			{
				$content = htmlspecialchars(Sh404sefHelperMetadata::cleanUpDesc($content), ENT_COMPAT, 'UTF-8');
				$openGraphData .= "\n" . '  <meta property="og:email" content="' . $content . '" />';
			}
			$content = empty($customData->og_phone_number) ? $sefConfig->ogPhoneNumber : $customData->og_phone_number;
			if (!empty($content))
			{
				$content = htmlspecialchars(Sh404sefHelperMetadata::cleanUpDesc($content), ENT_COMPAT, 'UTF-8');
				$openGraphData .= "\n" . '  <meta property="og:phone_number" content="' . $content . '" />';
			}
			$content = empty($customData->og_fax_number) ? $sefConfig->ogFaxNumber : $customData->og_fax_number;
			if (!empty($content))
			{
				$content = htmlspecialchars(Sh404sefHelperMetadata::cleanUpDesc($content), ENT_COMPAT, 'UTF-8');
				$openGraphData .= "\n" . '  <meta property="og:fax_number" content="' . $content . '" />';
			}
		}

		if (!empty($openGraphData))
		{
			$tags['ogNameSpace'] = 'xmlns:og="http://ogp.me/ns#"';
		}

		if ((!empty($sefConfig->fbAdminIds) && $customData->og_enable_fb_admin_ids == SH404SEF_OPTION_VALUE_USE_DEFAULT)
			|| $customData->og_enable_fb_admin_ids == SH404SEF_OPTION_VALUE_YES
		)
		{
			$content = empty($customData->fb_admin_ids) ? $sefConfig->fbAdminIds : $customData->fb_admin_ids;
			if ($customData->og_enable_fb_admin_ids != SH404SEF_OPTION_VALUE_NO && !empty($content))
			{
				$openGraphData .= "\n" . '  <meta property="fb:admins" content="' . $content . '" />';
				$tags['fbNameSpace'] = 'xmlns:fb="https://www.facebook.com/2008/fbml"';
			}
		}

		$tags['openGraphData'] = $openGraphData;

		return $tags;
	}
}
