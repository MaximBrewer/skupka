<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author       Yannick Gaultier
 * @copyright    (c) Yannick Gaultier - Weeblr llc - 2016
 * @package      sh404SEF
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version      4.7.3.3292
 * @date        2016-03-15
 */

defined('_JEXEC') or die('Restricted access');

// V 1.2.4.t  check if sh404SEF is running
if (defined('SH404SEF_IS_RUNNING'))
{

	// support for improved TITLE, DESCRIPTION, KEYWORDS and ROBOTS head tag
	global $shCustomTitleTag, $shCustomDescriptionTag, $shCustomKeywordsTag, $shCustomRobotsTag, $shCustomLangTag, $shCanonicalTag;
	// these variables can be set throughout your php code in components, bots or other modules
	// the last one wins !

	/**
	 * @param $title
	 * @return mixed|string
	 *
	 * @deprecated any time
	 */
	function shCleanUpTitle($title)
	{
		return Sh404sefHelperMetadata::cleanUpTitle($title);
	}

	/**
	 * @param $title
	 * @return string
	 *
	 * @deprecated any time
	 */
	function shProtectPageTitle($title)
	{
		return Sh404sefHelperMetadata::protectPageTitle($title);
	}

	/**
	 * @param $desc
	 * @return mixed
	 *
	 * @deprecated any time
	 */
	function shCleanUpDesc($desc)
	{
		return Sh404sefHelperMetadata::cleanUpDesc($desc);
	}

	/**
	 * @deprecated any time
	 */
	function shIncludeMetaPlugin()
	{
		Sh404sefHelperMetadata::includeMetaPlugin();
	}

	// utility function to insert data into an html buffer, after, instead or before
	// one or more instances of a tag. If last parameter is 'first', then only the
	// first occurence of the tag is replaced, or the new value is inserted only
	// after or before the first occurence of the tag

	function shInsertCustomTagInBuffer($buffer, $tag, $where, $value, $firstOnly)
	{
		if (!$buffer || !$tag || !$value)
		{
			return $buffer;
		}
		$bits = explode($tag, $buffer);
		if (count($bits) < 2)
		{
			return $buffer;
		}
		$result = $bits[0];
		$maxCount = count($bits) - 1;
		switch ($where)
		{
			case 'instead':
				for ($i = 0; $i < $maxCount; $i++)
				{
					$result .= ($firstOnly == 'first' ? ($i == 0 ? $value : $tag) : $value) . $bits[$i + 1];
				}
				break;
			case 'after':
				for ($i = 0; $i < $maxCount; $i++)
				{
					$result .= $tag . ($firstOnly == 'first' ? ($i == 0 ? $value : $tag) : $value) . $bits[$i + 1];
				}
				break;
			default:
				for ($i = 0; $i < $maxCount; $i++)
				{
					$result .= ($firstOnly == 'first' ? ($i == 0 ? $value : $tag) : $value) . $tag . $bits[$i + 1];
				}
				break;
		}
		return $result;
	}

	function shPregInsertCustomTagInBuffer($buffer, $tag, $where, $value, $firstOnly, $rawPattern = false)
	{
		if (!$buffer || !$tag || !$value)
		{
			return $buffer;
		}
		$pattern = $rawPattern ? $tag : '#(' . $tag . ')#iUsu';

		switch ($where)
		{
			case 'instead':
				$replacement = $value;
				break;
			case 'after':
				$replacement = '$1' . $value;
				break;
			default:
				$replacement = $value . '$1';
				break;
		}

		$result = preg_replace($pattern, $replacement, $buffer, $firstOnly ? 1 : 0);
		if (empty($result))
		{
			$result = $buffer;
			ShlSystem_Log::error(
				'shlib', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__,
				'RegExp failed: invalid character on page ' . Sh404sefFactory::getPageInfo()->currentSefUrl
			);
		}

		return $result;
	}

	function shDoRedirectOutboundLinksCallback($matches)
	{
		if (count($matches) != 2)
		{
			return empty($matches) ? '' : $matches[0];
		}
		if (strpos($matches[1], Sh404sefFactory::getPageInfo()->getDefaultFrontLiveSite()) === false)
		{
			$mask = '<a href="' . Sh404sefFactory::getPageInfo()->getDefaultFrontLiveSite()
				. '/index.php?option=com_sh404sef&shtask=redirect&shtarget=%%shM1%%"';
			$result = str_replace('%%shM1%%', $matches[1], $mask);
		}
		else
		{
			$result = $matches[0];
		}
		return $result;
	}

	function shDoInsertOutboundLinksImageCallback($matches)
	{
		//if (count($matches) != 2 && count($matches) != 3) return empty($matches) ? '' : $matches[0];
		$orig = $matches[0];
		$bits = explode('href=', $orig);
		$part2 = $bits[1]; // 2nd part, after the href=
		$sep = substr($part2, 0, 1); // " or ' ?
		$link = JString::trim($part2, $sep); // remove first " or '
		if (empty($sep))
		{
			// this should not happen, but it happens (Fireboard)
			$result = $matches[0];
			return $result;
		}
		$link = explode($sep, $link);
		$link = $link[0]; // keep only the link

		$shPageInfo = &Sh404sefFactory::getPageInfo();
		$sefConfig = &Sh404sefFactory::getConfig();

		if (substr($link, 0, strlen($shPageInfo->getDefaultFrontLiveSite())) != $shPageInfo->getDefaultFrontLiveSite()
			&& (substr($link, 0, 7) == 'http://' || substr($link, 0, 7) == 'https://')
			&& (empty($shPageInfo->basePath) || substr($link, 0, strlen($shPageInfo->basePath)) != $shPageInfo->basePath)
			&& strpos($link, 'pinterest.com') === false
		)
		{

			$mask = '%%shM1%%href="%%shM2%%" %%shM3%% >%%shM4%%<img border="0" alt="%%shM5%%" src="' . $shPageInfo->getDefaultFrontLiveSite()
				. '/components/com_sh404sef/images/' . $sefConfig->shImageForOutboundLinks . '"/></a>';

			$result = str_replace('%%shM1%%', $bits[0], $mask);
			$result = str_replace('%%shM2%%', $link, $result);

			$m3 = str_replace($sep . $link . $sep, '', str_replace('</a>', '', $part2)); // remove link from part 2
			$bits2 = explode('>', $m3);
			$m3 = $bits2[0];
			$result = str_replace('%%shM3%%', $m3, $result);

			array_shift($bits2); // remove first bit
			$m4 = implode($bits2, '>');
			$result = str_replace('%%shM4%%', $m4, $result);

			$m5 = strip_tags($m4);
			$result = str_replace('%%shM5%%', $m5, $result);
		}
		else
		{
			$result = $matches[0];
		}
		return $result;
	}

	function shDoTitleTags(&$buffer)
	{
		// Replace TITLE and DESCRIPTION and KEYWORDS
		if (empty($buffer))
		{
			return null;
		}

		$shPageInfo = Sh404sefFactory::getPageInfo();
		$sefConfig = Sh404sefFactory::getConfig();

		// V 1.2.4.t protect against error if using shCustomtags without sh404SEF activated
		// this should not happen, so we simply do nothing
		if (!isset($sefConfig) || empty($shPageInfo->currentNonSefUrl))
		{
			return null;
		}

		// read custom meta data from database
		if ($sefConfig->shMetaManagementActivated)
		{
			$metadata = Sh404sefHelperMetadata::getFinalizedCustomMetaData();

			// group new tags insertion, better perf
			$tagsToInsert = '';

			// meta data have been set already through Joomla API at onAfterDispatch
			// Optionnaly, we will force them into the page again, in case some
			// other extension has modified them
			$metaDataOverride = !defined('SH404SEF_OTHER_DO_NOT_OVERRIDE_EXISTING_META_DATA') || SH404SEF_OTHER_DO_NOT_OVERRIDE_EXISTING_META_DATA == 0;
			$document = JFactory::getDocument();
			if ($metaDataOverride)
			{
				$headData = $document->getHeadData();

				// if document title is != from the one we have in store, override
				if (!empty($metadata->pageTitle) && $document->getTitle() != $metadata->pageTitle)
				{
					$shPageInfo->pageTitle = $metadata->pageTitle;
					$shPageInfo->pageTitlePr = Sh404sefHelperMetadata::protectPageTitle($shPageInfo->pageTitle);
					$buffer = ShlSystem_Strings::pr('/\<\s*title\s*\>.*\<\s*\/title\s*\>/isuU', '<title>' . $shPageInfo->pageTitlePr . '</title>', $buffer);
					$buffer = ShlSystem_Strings::pr('/\<\s*meta\s+name\s*=\s*"title.*\/\>/isuU', '', $buffer); // remove any title meta
				}

				if (!empty($metadata->pageDescription) && $document->getDescription() != $metadata->pageDescription)
				{
					$shPageInfo->pageDescription = $metadata->pageDescription;
					if (strpos($buffer, '<meta name="description" content=') !== false)
					{
						$buffer = ShlSystem_Strings::pr(
							'/\<\s*meta\s+name\s*=\s*"description.*\/\>/isUu',
							'<meta name="description" content="' . $shPageInfo->pageDescription . '" />', $buffer
						);
					}
					else
					{
						$tagsToInsert .= "\n" . '<meta name="description" content="' . $shPageInfo->pageDescription . '" />';
					}
				}

				if (!empty($metadata->pageKeywords) && !empty($headData['metaTags']['standard']['keywords'])
					&& $headData['metaTags']['standard']['keywords'] != $metadata->pageKeywords
				)
				{
					$shPageInfo->pageKeywords = $metadata->pageKeywords;
					if (strpos($buffer, '<meta name="keywords" content=') !== false)
					{
						$buffer = ShlSystem_Strings::pr(
							'/\<\s*meta\s+name\s*=\s*"keywords.*\/\>/isUu',
							'<meta name="keywords" content="' . $shPageInfo->pageKeywords . '" />', $buffer
						);
					}
					else
					{
						$tagsToInsert .= "\n" . '<meta name="keywords" content="' . $shPageInfo->pageKeywords . '" />';
					}
				}

				if (!empty($metadata->pageRobotsTag) && !empty($headData['metaTags']['standard']['robots'])
					&& $headData['metaTags']['standard']['robots'] != $metadata->pageRobotsTag
				)
				{
					$shPageInfo->pageRobotsTag = $metadata->pageRobotsTag;
					if (strpos($buffer, '<meta name="robots" content=') !== false)
					{
						$buffer = ShlSystem_Strings::pr(
							'/\<\s*meta\s+name\s*=\s*"robots.*\/\>/isUu',
							'<meta name="robots" content="' . $shPageInfo->pageRobotsTag . '" />', $buffer
						);
					}
					else
					{
						$tagsToInsert .= "\n" . '<meta name="robots" content="' . $shPageInfo->pageRobotsTag . '" />';
					}
				}

				if (!empty($metadata->pageLangTag))
				{
					$shPageInfo->pageLangTag = $metadata->pageLangTag;
					if (strpos($buffer, '<meta http-equiv="Content-Language"') !== false)
					{
						$buffer = ShlSystem_Strings::pr(
							'/\<\s*meta\s+http-equiv\s*=\s*"Content-Language".*\/\>/isUu',
							'<meta http-equiv="Content-Language" content="' . $metadata->pageLangTag . '" />', $buffer
						);
					}
					else
					{
						$tagsToInsert .= "\n" . '<meta http-equiv="Content-Language" content="' . $metadata->pageLangTag . '" />';
					}
				}
			}
			else
			{
				$shPageInfo->pageTitle = $document->getTitle();
				$shPageInfo->pageTitlePr = Sh404sefHelperMetadata::protectPageTitle($shPageInfo->pageTitle);
				$shPageInfo->pageDescription = $document->getDescription();
			}

			// custom handling of canonical
			$canonicalPattern = '/\<\s*link[^>]+rel\s*=\s*"canonical[^>]+\/\>/isUu';
			$matches = array();
			$canonicalCount = preg_match_all($canonicalPattern, $buffer, $matches);
			// more than one canonical already: kill them all
			if ($canonicalCount > 1 && Sh404sefFactory::getConfig()->removeOtherCanonicals)
			{
				$buffer = ShlSystem_Strings::pr($canonicalPattern, '', $buffer);
				$canonicalCount = 0;
			}
			// only one and J3: must be the one inserted by J3 SEF plugin
			else if ($canonicalCount > 0 && Sh404sefFactory::getConfig()->removeOtherCanonicals && version_compare(JVERSION, '3.0', 'ge')
				&& JFactory::getApplication()->input->getCmd('option') == 'com_content'
			)
			{
				// kill it, if asked to
				$buffer = ShlSystem_Strings::pr($canonicalPattern, '', $buffer);
				$canonicalCount = 0;
			}

			// always add a canonical on home page
			// especially useful on multilingual sites where language code is used
			// also on default language
			if (empty($metadata->canonical) && Sh404sefHelperUrl::isNonSefHomepage())
			{
				$metadata->canonical = JUri::root();
			}

			// make sure canonical is absolute, to avoid users complaining despite links being totally fine see #342
			if (!empty($metadata->canonical) && substr($metadata->canonical, 0, 1) == '/')
			{
				$metadata->canonical = Sh404sefHelperUrl::routedToAbs($metadata->canonical);
			}

			// store finally computed canonical, for other uses (OGP,...)
			Sh404sefFactory::getPageInfo()->pageCanonicalUrl = $metadata->canonical;

			// if there' a custom canonical for that page, insert it, or replace any existing ones
			if (!empty($metadata->canonical) && $canonicalCount == 0)
			{
				// insert a new canonical
				$tagsToInsert .= "\n" . '<link href="' . htmlspecialchars($metadata->canonical, ENT_COMPAT, 'UTF-8') . '" rel="canonical" />' . "\n";
			}
			else if (!empty($metadata->canonical))
			{
				// replace existing canonical
				$buffer = ShlSystem_Strings::pr(
					$canonicalPattern,
					'<link href="' . htmlspecialchars($metadata->canonical, ENT_COMPAT, 'UTF-8') . '" rel="canonical" />', $buffer
				);
			}

			// insert all tags in one go
			if (!empty($tagsToInsert))
			{
				$buffer = shInsertCustomTagInBuffer($buffer, '<head>', 'after', $tagsToInsert, 'first');
			}

			// remove Generator tag
			if ($sefConfig->shRemoveGeneratorTag)
			{
				$buffer = ShlSystem_Strings::pr('/<meta\s*name="generator"\s*content=".*\/>/isUu', '', $buffer);
			}

			// put <h1> tags around content elements titles
			if ($sefConfig->shPutH1Tags)
			{
				if (strpos($buffer, 'class="componentheading') !== false)
				{
					$buffer = ShlSystem_Strings::pr(
						'/<div class="componentheading([^>]*)>\s*(.*)\s*<\/div>/isUu',
						'<div class="componentheading$1><h1>$2</h1></div>', $buffer
					);
					$buffer = ShlSystem_Strings::pr(
						'/<td class="contentheading([^>]*)>\s*(.*)\s*<\/td>/isUu',
						'<td class="contentheading$1><h2>$2</h2></td>', $buffer
					);
				}
				else
				{ // replace contentheading by h1
					$buffer = ShlSystem_Strings::pr(
						'/<td class="contentheading([^>]*)>\s*(.*)\s*<\/td>/isUu',
						'<td class="contentheading$1><h1>$2</h1></td>', $buffer
					);
				}
			}

			// version x : if multiple h1 headings, replace them by h2
			if ($sefConfig->shMultipleH1ToH2 && substr_count(JString::strtolower($buffer), '<h1>') > 1)
			{
				$buffer = str_replace('<h1>', '<h2>', $buffer);
				$buffer = str_replace('<H1>', '<h2>', $buffer);
				$buffer = str_replace('</h1>', '</h2>', $buffer);
				$buffer = str_replace('</H1>', '</h2>', $buffer);
			}

			// V 1.3.1 : replace outbounds links by internal redirects
			if (sh404SEF_REDIRECT_OUTBOUND_LINKS)
			{
				$tmp = preg_replace_callback('/<\s*a\s*href\s*=\s*"(.*)"/isUu', 'shDoRedirectOutboundLinksCallback', $buffer);
				if (empty($tmp))
				{
					ShlSystem_Log::error(
						'shlib', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__,
						'RegExp failed: invalid character on page ' . Sh404sefFactory::getPageInfo()->currentSefUrl
					);
				}
				else
				{
					$buffer = $tmp;
				}
			}

			// V 1.3.1 : add symbol to outbounds links
			if ($sefConfig->shInsertOutboundLinksImage)
			{
				$tmp = preg_replace_callback("/<\s*a\s*href\s*=\s*(\"|').*(\"|')\s*>.*<\/a>/isUu", 'shDoInsertOutboundLinksImageCallback', $buffer);
				if (empty($tmp))
				{
					ShlSystem_Log::error(
						'shlib', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__,
						'RegExp failed: invalid character on page ' . Sh404sefFactory::getPageInfo()->currentSefUrl
					);
				}
				else
				{
					$buffer = $tmp;
				}
			}

			// all done
			return $buffer;
		}
	}

	function shDoAnalytics(&$buffer)
	{
		// get sh404sef config
		$config = Sh404sefFactory::getConfig();

		// check if set to insert snippet
		if (!Sh404sefHelperAnalytics::isEnabled())
		{
			return;
		}

		// calculate params
		$className = 'Sh404sefAdapterAnalytics' . strtolower($config->analyticsType);
		$handler = new $className();

		// do insert
		$snippet = $handler->getSnippet();
		if (empty($snippet))
		{
			return;
		}

		// use page rewrite utility function to insert as needed
		if ($config->analyticsEdition != 'gtm')
		{
			$buffer = shInsertCustomTagInBuffer($buffer, '</head>', 'before', $snippet, $firstOnly = 'first');
		}
		else
		{
			$buffer = shPregInsertCustomTagInBuffer($buffer, '<\s*body[^>]*>', 'after', $snippet, $firstOnly = 'first');
		}
	}

	function shDoSocialButtons(&$buffer)
	{
		// get sh404sef config
		$sefConfig = Sh404sefFactory::getConfig();
		$dispatcher = ShlSystem_factory::dispatcher();

		// fire event so that social plugin can attach required external js and css
		$dispatcher->trigger('onSh404sefInsertSocialButtons', array(&$buffer, $sefConfig));

		// fire event so that social plugin can attach required external js
		$dispatcher->trigger('onSh404sefInsertFBJavascriptSDK', array(&$buffer, $sefConfig));
	}

	function shDoSocialAnalytics(&$buffer)
	{
		// get sh404sef config
		$sefConfig = Sh404sefFactory::getConfig();

		// check if set to insert snippet
		if (!Sh404sefHelperAnalytics::isEnabled())
		{
			return;
		}

		// fire event so that social plugin can attach required external js
		$dispatcher = ShlSystem_factory::dispatcher();
		$dispatcher->trigger('onSh404sefInsertFBJavascriptSDK', array(&$buffer, $sefConfig));
	}

	function shDoShURL(&$buffer)
	{
		// get sh404sef config
		$sefConfig = Sh404sefFactory::getConfig();

		// check if shURLs are enabled
		if (!$sefConfig->Enabled || !$sefConfig->enablePageId)
		{
			return;
		}

		// get current page information
		$shPageInfo = &Sh404sefFactory::getPageInfo();

		// insert shURL if tag found, except if editing item on frontend
		if (strpos($buffer, '{sh404sef_pageid}') !== false || strpos($buffer, '{sh404sef_shurl}') !== false)
		{
			// replace editor contents with placeholder text
			$buffer = str_replace(array('{sh404sef_pageid}', '{sh404sef_shurl}'), $shPageInfo->shURL, $buffer);
		}
	}

	function shInsertOpenGraphData(&$buffer)
	{
		$tags = Sh404sefHelperOgp::buildOpenGraphTags();

		// actually insert the tags
		if (!empty($tags['openGraphData']))
		{
			$buffer = shInsertCustomTagInBuffer($buffer, '</head>', 'before', $tags['openGraphData'], 'first');
		}

		if (!empty($tags['fbNameSpace']) || !empty($tags['ogNameSpace']))
		{
			// insert as well namespaces
			$buffer = str_replace('<html ', '<html ' . $tags['ogNameSpace'] . ' ' . $tags['fbNameSpace'] . ' ', $buffer);
		}
	}

	function shInsertGoogleAuthorshipData(&$buffer)
	{
		// quick check, do we have a createdBy field on the page?
		if (strpos($buffer, '<dd class="createdby">') === false)
		{
			return;
		}

		// get sh404sef config
		$sefConfig = Sh404sefFactory::getConfig();
		$pageInfo = Sh404sefFactory::getPageInfo();

		if (empty($sefConfig->shMetaManagementActivated) || !isset($sefConfig) || empty($pageInfo->currentNonSefUrl)
			|| (!empty($pageInfo->httpStatus) && $pageInfo->httpStatus == 404)
		)
		{
			return;
		}

		$customData = Sh404sefHelperMetadata::getCustomMetaDataFromDb();

		// user can disable per url
		if (isset($customData->google_authorship_enable) && $customData->google_authorship_enable == SH404SEF_OPTION_VALUE_NO
			|| (empty($sefConfig->enableGoogleAuthorship)
				&& (!isset($customData->google_authorship_enable) || $customData->google_authorship_enable == SH404SEF_OPTION_VALUE_USE_DEFAULT))
		)
		{
			return;
		}
		// figure out if we should insert authorship info: only on article page
		if (!Sh404sefHelperMetadata::shouldInsertMeta($input = null, $sefConfig->googleAuthorshipCategories))
		{
			return;
		}

		// site
		$authorUrl = empty($customData->google_authorship_author_profile) ? $sefConfig->googleAuthorshipAuthorProfile
			: $customData->google_authorship_author_profile;
		$authorUrl = JString::trim($authorUrl, '/');
		$authorName = empty($customData->google_authorship_author_name) ? $sefConfig->googleAuthorshipAuthorName
			: $customData->google_authorship_author_name;

		if (empty($authorUrl) || empty($authorName))
		{
			return;
		}
		$authorUrl = 'https://plus.google.com/' . htmlspecialchars($authorUrl, ENT_COMPAT, 'UTF-8') . '?rel=author';
		$authorName = htmlspecialchars($authorName, ENT_COMPAT, 'UTF-8');

		$googleAuthorshipData = JText::sprintf('COM_CONTENT_WRITTEN_BY', JHtml::_('link', $authorUrl, $authorName));

		// actually insert the tags
		if (!empty($googleAuthorshipData))
		{
			$buffer = ShlSystem_Strings::pr(
				'#\<dd\s*class="createdby"\s*\>.*\<\/dd\>#iUsu',
				'<dd class="createdby">' . $googleAuthorshipData . '</dd>', $buffer
			);
		}
	}

	function shInsertGooglePublisherData(&$buffer)
	{
		// don't insert head link to publisher page if there's
		// already a visible badge (see sh404sef core social plugin
		if (strpos($buffer, 'rel=\'publisher\'') !== false)
		{
			return;
		}

		// get sh404sef config
		$sefConfig = Sh404sefFactory::getConfig();
		$pageInfo = Sh404sefFactory::getPageInfo();

		if (empty($sefConfig->shMetaManagementActivated) || !isset($sefConfig) || empty($pageInfo->currentNonSefUrl)
			|| (!empty($pageInfo->httpStatus) && $pageInfo->httpStatus == 404)
		)
		{
			return;
		}

		$customData = Sh404sefHelperMetadata::getCustomMetaDataFromDb();

		// user can disable per url
		if (isset($customData->google_publisher_enable) && $customData->google_publisher_enable == SH404SEF_OPTION_VALUE_NO
			|| (empty($sefConfig->enableGooglePublisher)
				&& (!isset($customData->google_publisher_enable) || $customData->google_publisher_enable == SH404SEF_OPTION_VALUE_USE_DEFAULT))
		)
		{
			return;
		}

		// site
		$publisherUrl = empty($customData->google_publisher_url) ? $sefConfig->googlePublisherUrl
			: $customData->google_publisher_url;
		$publisherUrl = JString::trim($publisherUrl, '/');

		if (empty($publisherUrl))
		{
			return;
		}
		$publisherUrl = 'https://plus.google.com/' . htmlspecialchars($publisherUrl, ENT_COMPAT, 'UTF-8');
		$publisherTag = sprintf('  <link href="%s" rel="publisher" />', $publisherUrl);

		// actually insert the tags
		if (!empty($publisherTag))
		{
			$buffer = shInsertCustomTagInBuffer($buffer, '</head>', 'before', "\n" . $publisherTag . "\n", 'first');
		}
	}

	function shInsertGoogleBreadcrumb(& $buffer)
	{
		// get sh404sef config
		$sefConfig = Sh404sefFactory::getConfig();
		if (empty($sefConfig->insertGoogleBreadcrumb) || empty($sefConfig->shMetaManagementActivated))
		{
			return;
		}
		$breadcrumb = JFactory::getApplication()->getPathway();
		$breadcrumbItems = empty($breadcrumb) ? null : $breadcrumb->getPathway();
		$displayData = array();
		if (!empty($breadcrumbItems))
		{
			// add other crumbs
			foreach ($breadcrumbItems as $key => $item)
			{
				$itemData = array(
					'position' => $key + 2,
					'id' => $item->link,
					'name' => $item->name
				);
				$displayData['items'][] = $itemData;
			}
			if (!empty($displayData))
			{
				// load breadcrumb module language and params
				$module = JModuleHelper::getModule('mod_breadcrumbs');
				$lang = JFactory::getLanguage();
				$lang->load('mod_breadcrumbs', JPATH_BASE, null, false, true) ||
				$lang->load('mod_breadcrumbs', JPATH_BASE . '/modules/mod_breadcrumbs', null, false, true);

				if (!empty($module) && !empty($module->id))
				{
					$params = new JRegistry;
					$params->loadString($module->params);
					$homeTitle = htmlspecialchars($params->get('homeText', JText::_('MOD_BREADCRUMBS_HOME')));
				}
				else
				{
					$homeTitle = JText::_('MOD_BREADCRUMBS_HOME');
				}
				// home link
				if (JLanguageMultilang::isEnabled())
				{
					$home = JFactory::getApplication()->getMenu()->getDefault($lang->getTag());
				}
				else
				{
					$home = JFactory::getApplication()->getMenu()->getDefault();
				}
				// insert home crumb
				array_unshift(
					$displayData['items'], array(
						                     'position' => 1,
						                     'id' => 'index.php?Itemid=' . $home->id,
						                     'name' => $homeTitle
					                     )
				);
			}
		}

		if (!empty($displayData))
		{
			$markup = ShlMvcLayout_Helper::render('com_sh404sef.markup.google_breadcrumb', $displayData);
			$buffer = shInsertCustomTagInBuffer($buffer, '</head>', 'before', "\n" . $markup . "\n", 'first');
		}
	}

	function shInsertGoogleSitename(&$buffer)
	{
		// get sh404sef config
		$sefConfig = Sh404sefFactory::getConfig();
		if (empty($sefConfig->insertGoogleSitename) || empty($sefConfig->shMetaManagementActivated))
		{
			return;
		}

		if (!Sh404sefHelperUrl::isHomepage())
		{
			return;
		}

		// prepare markup data
		$siteName = $sefConfig->ogSiteName;
		$siteName = empty($siteName) ? JFactory::getConfig()->get('sitename') : $siteName;
		$displayData = array(
			'sitename' => $siteName,
			'alternate_sitename' => '',
			'url' => JUri::current()
		);

		// actually insert the tags
		$markup = ShlMvcLayout_Helper::render('com_sh404sef.markup.google_sitename', $displayData);
		$buffer = shInsertCustomTagInBuffer($buffer, '</head>', 'before', "\n" . $markup . "\n", 'first');
	}

	function shInsertGoogleSitelinksSearch(&$buffer)
	{
		// get sh404sef config
		$sefConfig = Sh404sefFactory::getConfig();
		if (empty($sefConfig->insertGoogleSitelinksSearch) || empty($sefConfig->shMetaManagementActivated))
		{
			return;
		}

		// prepare markup data
		$prefix = rtrim(str_replace(JUri::base(true), '', Juri::base(false)), '/');
		$target = empty($sefConfig->insertGoogleSitelinksSearchCustom) ?
			$prefix . JRoute::_('index.php?option=com_search&searchword=') . '{search_term_string}' :
			$sefConfig->insertGoogleSitelinksSearchCustom;
		$displayData = array(
			'url' => Juri::base(false),
			'target' => $target
		);

		// actually insert the tags
		$markup = ShlMvcLayout_Helper::render('com_sh404sef.markup.google_sitelinks_search', $displayData);
		$buffer = shInsertCustomTagInBuffer($buffer, '</head>', 'before', "\n" . $markup . "\n", 'first');
	}

	function shInsertTwitterCardsData(&$buffer)
	{
		$twitterCardsData = Sh404sefHelperTcards::buildTwitterCardsTags();

		// actually insert the tags
		if (!empty($twitterCardsData))
		{
			$buffer = shInsertCustomTagInBuffer($buffer, '</head>', 'before', $twitterCardsData, 'first');
		}
	}

	function shDoHeadersChanges()
	{
		global $shCanonicalTag;

		$sefConfig = Sh404sefFactory::getConfig();
		$pageInfo = Sh404sefFactory::getPageInfo();

		if (!isset($sefConfig) || empty($sefConfig->shMetaManagementActivated) || empty($pageInfo->currentNonSefUrl))
		{
			return;
		}

		// include plugin to build canonical if needed
		Sh404sefHelperMetadata::includeMetaPlugin();

		// issue headers for canonical
		if (!empty($shCanonicalTag))
		{
			jimport('joomla.utilities.string');
			$link = JURI::base() . ltrim($sefConfig->shRewriteStrings[$sefConfig->shRewriteMode], '/')
				. JString::ltrim($shCanonicalTag, '/');
			JResponse::setHeader('Link', '<' . htmlspecialchars($link, ENT_COMPAT, 'UTF-8') . '>; rel="canonical"');
		}
	}

	function shAddPaginationHeaderLinks(&$buffer)
	{
		$sefConfig = &Sh404sefFactory::getConfig();

		if (!isset($sefConfig) || empty($sefConfig->shMetaManagementActivated) || empty($sefConfig->insertPaginationTags))
		{
			return;
		}

		$pageInfo = Sh404sefFactory::getPageInfo();

		// handle pagination
		if (!empty($pageInfo->paginationNextLink))
		{
			$link = "\n  " . '<link rel="next" href="' . $pageInfo->paginationNextLink . '" />';
			$buffer = shInsertCustomTagInBuffer($buffer, '<head>', 'after', $link, 'first');
		}

		if (!empty($pageInfo->paginationPrevLink))
		{
			$link = "\n  " . '<link rel="prev" href="' . $pageInfo->paginationPrevLink . '" />';
			$buffer = shInsertCustomTagInBuffer($buffer, '<head>', 'after', $link, 'first');
		}
	}

	function shFixHrefLang(&$buffer)
	{
		// if not home page
		$pageInfo = Sh404sefFactory::getPageInfo();
		if ($pageInfo->currentNonSefUrl != $pageInfo->homeLink)
		{
			return;
		}

		if (strpos($buffer, 'rel="alternate" hreflang=') !== false)
		{
			return;
		}

		$languageFilterParams = Sh404sefHelperGeneral::getExtensionParams('plg_language_filter', array('type' => 'plugin', 'element' => 'languagefilter', 'folder' => 'system'));
		if (!$languageFilterParams->get('alternate_meta'))
		{
			return;
		}

		$languages = Sh404sefHelperLanguage::getActiveLanguages();
		$config = Sh404sefFactory::getConfig();
		foreach ($languages as $key => $language)
		{
			$sefCode = JString::ltrim($config->shRewriteStrings[$config->shRewriteMode] . $language->sef, '/');
			if ($language->active)
			{
				$url = JURI::base();
			}
			else
			{
				$url = JURI::base() . $sefCode . '/';
			}
			$link = "\n  " . '<link href="' . $url . '" rel="alternate" hreflang="' . $language->lang_code . '" />';
			$buffer = shInsertCustomTagInBuffer($buffer, '<head>', 'after', $link, 'first');
		}
	}

	// begin main output --------------------------------------------------------

	// check we are outputting document for real
	$document = JFactory::getDocument();
	$pageInfo = Sh404sefFactory::getPageInfo();
	if ($document->getType() == 'html')
	{
		$shPage = JResponse::getBody();

		// provide a hreflang set when language filter fails to do so
		shFixHrefLang($shPage, $document, $pageInfo);

		// do TITLE and DESCRIPTION and KEYWORDS and ROBOTS tags replacement
		shDoTitleTags($shPage);

		// sharing buttons
		shDoSocialButtons($shPage);

		// insert analytics snippet
		shDoAnalytics($shPage);
		shDoSocialAnalytics($shPage);

		// insert short urls stuff
		shDoShURL($shPage);

		// Google autorship
		shInsertGoogleAuthorshipData($shPage);
		shInsertGooglePublisherData($shPage);

		// Google sitename info
		if (empty($pageInfo->httpStatus) || $pageInfo->httpStatus == 200)
		{
			shInsertGoogleSitename($shPage);
			shInsertGoogleSitelinksSearch($shPage);
			shInsertGoogleBreadcrumb($shPage);
		}

		// Twitter cards data
		shInsertTwitterCardsData($shPage);

		// Open Graph data
		shInsertOpenGraphData($shPage);

		// pagination links for lists
		shAddPaginationHeaderLinks($shPage);

		if (Sh404sefFactory::getConfig()->displayUrlCacheStats)
		{
			$shPage .= Sh404sefHelperCache::getCacheStats();
		}

		JResponse::setBody($shPage);
	}
	else
	{
		shDoHeadersChanges();
	}
}
