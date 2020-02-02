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

class Sh404sefHelperTcards
{
	public static function buildTwitterCardsTags()
	{
		// prepare data
		$twitterCardsData = '';

		// get sh404sef config
		$sefConfig = Sh404sefFactory::getConfig();
		$pageInfo = Sh404sefFactory::getPageInfo();

		if (empty($sefConfig->shMetaManagementActivated) || !isset($sefConfig) || empty($pageInfo->currentNonSefUrl)
			|| (!empty($pageInfo->httpStatus) && $pageInfo->httpStatus == 404)
		)
		{
			return $twitterCardsData;
		}

		$customData = Sh404sefHelperMetadata::getCustomMetaDataFromDb();

		// user can disable per url
		if (isset($customData->twittercards_enable) && $customData->twittercards_enable == SH404SEF_OPTION_VALUE_NO
			|| (empty($sefConfig->enableTwitterCards)
				&& (!isset($customData->twittercards_enable) || $customData->twittercards_enable == SH404SEF_OPTION_VALUE_USE_DEFAULT))
		)
		{
			return $twitterCardsData;
		}

		// check categories
		if (!Sh404sefHelperMetadata::shouldInsertMeta($input = null, $sefConfig->twitterCardsCategories))
		{
			return $twitterCardsData;
		}


		// card type
		$twitterCardsData .= "\n" . '  <meta name="twitter:card" content="summary" />';

		// site
		$siteAccount = !isset($customData->twittercards_site_account) || empty($customData->twittercards_site_account)
			? $sefConfig->twitterCardsSiteAccount : $customData->twittercards_site_account;
		if (!empty($siteAccount))
		{
			$twitterCardsData .= "\n" . '  <meta name="twitter:site" content="' . $siteAccount . '" />';
		}

		// creator
		$creatorAccount = empty($customData->twittercards_creator_account) ? $sefConfig->twitterCardsCreatorAccount
			: $customData->twittercards_creator_account;
		if (!empty($creatorAccount))
		{
			$twitterCardsData .= "\n" . '  <meta name="twitter:creator" content="' . $creatorAccount . '" />';
		}

		// title
		if (!empty($pageInfo->pageTitle))
		{
			$twitterCardsData .= "\n" . '  <meta name="twitter:title" content="' . $pageInfo->pageTitle . '" />';
		}

		// description: Twitter requires a title and description. If no description has been found at this stage
		// meaning not even a sitewide one, we use the page title, which would always exists
		$description = empty($pageInfo->pageDescription) ? $pageInfo->pageTitle : $pageInfo->pageDescription;
		if ($description)
		{
			$twitterCardsData .= "\n" . '  <meta name="twitter:description" content="' . $description . '" />';
		}

		// insert url. If any, we insert the canonical url rather than current, to consolidate
		$content = empty($pageInfo->pageCanonicalUrl) ? $pageInfo->currentSefUrl : $pageInfo->pageCanonicalUrl;
		$content = Sh404sefHelperUrl::stripTrackingVarsFromSef($content);
		$twitterCardsData .= "\n" . '  <meta name="twitter:url" content="' . htmlspecialchars($content, ENT_COMPAT, 'UTF-8') . '" />';

		// image : we share with OpenGraph image
		$image = empty($customData->og_image) ? $sefConfig->ogImage : $customData->og_image;
		if (!empty($image))
		{
			$image = JURI::root(false, '') . JString::ltrim($image, '/');
			$twitterCardsData .= "\n" . '  <meta name="twitter:image" content="' . $image . '" />';
		}
		return $twitterCardsData;
	}
}
