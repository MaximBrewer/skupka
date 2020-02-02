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

class Sh404sefHelperShurl
{
	public static function updateShurls()
	{
		static $_updated = false;

		if ($_updated)
		{
			return;
		}
		else
		{
			$_updated = true;
		}

		$pageInfo = Sh404sefFactory::getPageInfo();
		// don't create shurls for URLs with query vars or on errors
		if(strpos($pageInfo->currentSefUrl, '?') !== false || (!empty($pageInfo->httpStatus) && $pageInfo->httpStatus != 200))
		{
			$_updated = true;
			return;
		}
		$sefConfig = Sh404sefFactory::getConfig();
		$pageInfo->shURL = empty($pageInfo->shURL) ? '' : $pageInfo->shURL;

		if ($sefConfig->enablePageId)
		{
			try
			{
				jimport('joomla.utilities.string');
				$nonSefUrl = JString::ltrim($pageInfo->currentNonSefUrl, '/');
				$nonSefUrl = Sh404sefHelperUrl::sortUrl($nonSefUrl);

				// make sure we have a language
				$nonSefUrl = Sh404sefHelperUrl::setUrlVar($nonSefUrl, 'lang', $pageInfo->currentLanguageShortTag);

				// remove tracking vars (Google Analytics)
				$nonSefUrl = Sh404sefHelperUrl::stripTrackingVarsFromNonSef($nonSefUrl);

				// try to get the current shURL, if any
				$shURL = ShlDbHelper::selectResult('#__sh404sef_pageids', array('pageid'), array('newurl' => $nonSefUrl));

				// if none, we may have to create one
				if (empty($shURL) && !$sefConfig->stopCreatingShurls)
				{
					$shURL = self::_createShurl($nonSefUrl);
				}

				// insert in head and header, if not empty
				if (!empty($shURL))
				{
					$fullShURL = JString::ltrim($pageInfo->getDefaultFrontLiveSite(), '/') . '/' . $shURL;
					$document = JFactory::getDocument();
					if($document->getType() == 'html')
					{
						if ($sefConfig->insertShortlinkTag)
						{
							$document->addHeadLink($fullShURL, 'shortlink');
							// also add header, especially for HEAD requests
							JResponse::setHeader('Link', '<' . $fullShURL . '>; rel=shortlink', true);
						}
						if ($sefConfig->insertRevCanTag)
						{
							$document->addHeadLink($fullShURL, 'canonical', 'rev', array('type' => 'text/html'));
						}
						if ($sefConfig->insertAltShorterTag)
						{
							$document->addHeadLink($fullShURL, 'alternate shorter');
						}
					}
					// store for reuse
					$pageInfo->shURL = $shURL;
				}

			}
			catch (Exception $e)
			{
				ShlSystem_Log::error('sh404sef', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, $e->getMessage());
			}
		}
	}

	protected static function _createShurl($nonSefUrl)
	{
		if (empty($nonSefUrl))
		{
			return '';
		}

		// only create a shURL if current page returns a 200
		$headers = JResponse::getHeaders();

		// check if we have a status
		foreach ($headers as $header)
		{
			if (strtolower($header['name']) == 'status' && $header['value'] != 200)
			{
				// error or redirection, don't shurl that
				return '';
			}
		}

		// check various conditions, to avoid overloading ourselves with shURL

		// not on homepage
		if (shIsAnyHomepage($nonSefUrl))
		{
			return '';
		}

		// not for format = raw, format = pdf or printing
		$format = Sh404sefHelperUrl::getUrlVar($nonSefUrl, 'format');
		if (in_array(strtolower($format), array('raw', 'pdf')))
		{
			return '';
		}
		$print = Sh404sefHelperUrl::getUrlVar($nonSefUrl, 'print');
		if ($print == 1)
		{
			return '';
		}
		// not if tmpl not empty or not index
		$tmpl = Sh404sefHelperUrl::getUrlVar($nonSefUrl, 'tmpl');
		if (!empty($tmpl) && $tmpl != 'index')
		{
			return '';
		}

		// force global setting
		shMustCreatePageId('set', true);

		// get a model and create shURL
		$model = ShlMvcModel_Base::getInstance('Pageids', 'Sh404sefModel');
		$shURL = $model->createPageId('', $nonSefUrl);

		return $shURL;

	}

}
