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

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

jimport('joomla.plugin.plugin');

class plgSh404sefcoresh404sefSocial extends JPlugin
{

	private $_params = null;
	private $_enabledButtons = array('facebooklike', 'facebooksend', 'facebookshare', 'twitter', 'googleplusone', 'googlepluspage', 'pinterestpinit', 'linkedin');
	private $_underscoredLanguageTag = '';
	private $_needFBSdk = false;
	private $_needTracking = false;

	public function __construct(&$subject, $config = array())
	{
		parent::__construct($subject);
		// get plugin params
		$plugin        = JPluginHelper::getPlugin('sh404sefcore', 'sh404sefsocial');
		$this->_params = new JRegistry;
		$this->_params->loadString($plugin->params);
		// some networks use underscore in language tags
		$this->_underscoredLanguageTag = str_replace('-', '_', JFactory::getLanguage()->getTag());
		$this->_shortLanguageCode      = explode('_', $this->_underscoredLanguageTag);
		$this->_shortLanguageCode      = empty($this->_shortLanguageCode) ? 'en' : $this->_shortLanguageCode[0];
		$this->_linkedinScriptLoaded   = false;
	}

	/**
	 * Insert appropriate script links into document
	 */
	public function onSh404sefInsertSocialButtons(&$page, $sefConfig)
	{
		$app = JFactory::getApplication();

		// are we in the backend - that would be a mistake
		if (!defined('SH404SEF_IS_RUNNING') || $app->isAdmin())
		{
			return;
		}

		// don't display on errors
		$pageInfo = Sh404sefFactory::getPageInfo();
		if (!empty($pageInfo->httpStatus) && ($pageInfo->httpStatus == 404 || $pageInfo->httpStatus == 503))
		{
			return;
		}

		// regexp to catch plugin requests
		$regExp = '#{sh404sef_social_buttons(.*)}#Uus';

		// search for our marker}
		if (preg_match_all($regExp, $page, $matches, PREG_SET_ORDER) > 0)
		{
			// process matches
			foreach ($matches as $id => $match)
			{
				$url       = '';
				$imageSrc  = '';
				$imageDesc = '';
				// extract target URL
				if (!empty($match[1]))
				{
					//normally, there is no quotes around attributes
					// but a description will probably have spaces, so we
					// now try to get attributes from both syntax
					jimport('joomla.utilities.utility');
					$attributes = JUtility::parseAttributes($match[1]);
					$url        = empty($attributes['url']) ? '' : $attributes['url'];
					$imageSrc   = empty($attributes['img']) ? '' : $attributes['img'];
					$imageDesc  = empty($attributes['desc']) ? '' : $attributes['desc'];

					// now process usual tags
					$raw            = explode(' ', $match[1]);
					$enabledButtons = array();
					foreach ($raw as $attribute)
					{
						$attribute = JString::trim($attribute);
						if (strpos($attribute, '=') === false)
						{
							continue;
						}
						$bits = explode('=', $attribute);
						if (empty($bits[1]))
						{
							continue;
						}
						switch ($bits[0])
						{
							case 'url':
								if (empty($url))
								{
									$base = JURI::base(true);
									if (substr($bits[1], 0, 10) == 'index.php?')
									{
										$url = JURI::getInstance()->toString(array('scheme', 'host', 'port')) . JRoute::_($bits[1]);
									}
									else if (!empty($base) && substr($bits[1], 0, JString::strlen($base)) == $base)
									{
										$url = JURI::getInstance()->toString(array('scheme', 'host', 'port')) . $bits[1];
									}
									else if (substr($bits[1], 0, 1) == '/')
									{
										$url = JString::rtrim(JURI::base(), '/') . $bits[1];
									}
									else
									{
										$url = $bits[1];
									}
								}
								break;
							case 'type':
								$newType = trim(strtolower($bits[1]));
								if (!in_array($newType, $enabledButtons))
								{
									$enabledButtons[] = $newType;
								}
								break;
							case 'img':
								$imageSrc = empty($imageSrc) ? strtolower($bits[1]) : $imageSrc;
								break;
						}
					}

					if (!empty($enabledButtons))
					{
						$this->_enabledButtons = $enabledButtons;
					}
				}
				// get buttons html
				$buttons = $this->_sh404sefGetSocialButtons($sefConfig, $url, $context = '', $content = null, $imageSrc, $imageDesc, $isTag = true);
				$buttons = str_replace('\'', '\\\'', $buttons);

				// replace in document
				$page = str_replace($match[0], $buttons, $page);
			}
		}

		// insert head links as needed
		$this->_insertSocialLinks($page, $sefConfig);

	}

	private function _sh404sefGetSocialButtons($sefConfig, $url = '', $context = '', $content = null, $imageSrc = '', $imageDesc = '', $isTag = false)
	{
		$url = $this->_computeUrl($url, $sefConfig, $context, $content, $isTag);
		if (empty($url))
		{
			return $url;
		}

		// JLayout renderers data array
		$displayData            = array();
		$displayData['buttons'] = array();

		// Tweet
		if ($this->_params->get('enableTweet', true) && in_array('twitter', $this->_enabledButtons))
		{
			$displayData['buttons']['twitter'] = array('viaAccount'  => $this->_params->get('viaAccount', ''),
			                                           'url' => $url, 'languageTag' => $this->_shortLanguageCode);
			$this->_needTracking = true;
		}

		// plus One
		if ($this->_params->get('enablePlusOne', true) && in_array('googleplusone', $this->_enabledButtons))
		{
			$displayData['buttons']['googleplusone'] = array('plusOneAnnotation' => $this->_params->get('plusOneAnnotation', 'none'),
			                                                 'plusOneSize'       => $this->_params->get('plusOneSize', ''), 'url' => $url);
			$this->_needTracking = true;
		}

		// Google plus page badge
		$page = JString::trim($this->_params->get('googlePlusPage', ''), '/');
		if ($this->_params->get('enableGooglePlusPage', true) && in_array('googlepluspage', $this->_enabledButtons) && !empty($page))
		{
			$displayData['buttons']['googlepluspage']                          = array();
			$displayData['buttons']['googlepluspage']['page']                  = $page;
			$displayData['buttons']['googlepluspage']['url']                   = $url;
			$displayData['buttons']['googlepluspage']['googlePlusPageSize']    = $this->_params->get('googlePlusPageSize', 'medium');
			$displayData['buttons']['googlepluspage']['googlePlusCustomText']  = $this->_params->get('googlePlusCustomText', '');
			$displayData['buttons']['googlepluspage']['googlePlusCustomText2'] = $this->_params->get('googlePlusCustomText2', '');
			$this->_needTracking = true;
		}

		// Pinterest
		if ($this->_params->get('enablePinterestPinIt', 1) && in_array('pinterestpinit', $this->_enabledButtons))
		{
			// we use either the first image in content, or the provided one (from a user created tag)
			if (empty($imageSrc))
			{
				// we're using the first image in the content
				$regExp = '#<img([^>]*)/>#ius';
				$text   = empty($content->fulltext) ? (empty($content->introtext) ? '' : $content->introtext) : $content->introtext
					. $content->fulltext;
				$img    = preg_match($regExp, $text, $match);
				if (empty($img) || empty($match[1]))
				{
					// could not find an image in the article
					// last chance is maybe webmaster is using Joomla! full text image article feature
					// note: if we are not on the canonical page (ie the full article display), Joomla!
					// uses the image_intro instead. However, I decided to still pin the full image
					// in such case, as the image_intro will most often be a thumbnail
					// Is this correct? can there be side effects?
					$imageSrc = '';
					if ($context == 'com_content.article' && !empty($content->images))
					{
						$registry = new JRegistry;
						$registry->loadString($content->images);
						$fulltextImage = $registry->get('image_fulltext');
						if (!empty($fulltextImage))
						{
							$imageSrc  = $fulltextImage;
							$imageDesc = $registry->get('image_fulltext_alt', '');
						}
					}
					else if ($context == 'com_k2.item')
					{
						// handle K2 images feature
						if (!empty($content->imageMedium))
						{
							$imageSrc  = JURI::root() . str_replace(JURI::base(true) . '/', '', $content->imageMedium);
							$imageDesc = $content->image_caption;
						}
					}
				}
				else
				{
					// extract image details
					jimport('joomla.utilities.utility');
					$attributes = JUtility::parseAttributes($match[1]);
					$imageSrc   = empty($attributes['src']) ? '' : $attributes['src'];
					$imageDesc  = empty($attributes['alt']) ? '' : $attributes['alt'];
				}
			}
			if (!empty($imageSrc))
			{
				if (substr($imageSrc, 0, 7) != 'http://' && substr($imageSrc, 0, 8) != 'https://' && substr($imageSrc, 0, 2) != '//')
				{
					// relative url, prepend root url
					$imageSrc = JURI::base() . JString::ltrim($imageSrc, '/');
				}
				$displayData['buttons']['pinterest']                     = array();
				$displayData['buttons']['pinterest']['url']              = $url;
				$displayData['buttons']['pinterest']['imageSrc']         = $imageSrc;
				$displayData['buttons']['pinterest']['imageDesc']        = $imageDesc;
				$displayData['buttons']['pinterest']['pinItCountLayout'] = $this->_params->get('pinItCountLayout', 'none');
				$displayData['buttons']['pinterest']['pinItButtonText']  = $this->_params->get('pinItButtonText', 'Pin it');
				$this->_needTracking = true;
			}
		}

		// FB Like
		if ($this->_params->get('enableFbLike', 1) && in_array('facebooklike', $this->_enabledButtons))
		{
			$layout                  = $this->_params->get('fbLayout', '') == 'none' ? '' : $this->_params->get('fbLayout', '');
			$fbData                  = array();
			$fbData['fbLayout']      = $layout;
			$fbData['url']           = $url;
			$fbData['fbAction']      = $this->_params->get('fbAction', '');
			$fbData['fbWidth']       = $this->_params->get('fbWidth', '');
			$fbData['fbShowFaces']   = $this->_params->get('fbShowFaces', 'true');
			$fbData['fbColorscheme'] = $this->_params->get('fbColorscheme', 'light');
			if ($this->_params->get('fbUseHtml5', true))
			{
				$displayData['buttons']['fb-like-html5'] = $fbData;
			}
			else
			{
				$displayData['buttons']['fb-like'] = $fbData;
			}

			$this->_needFBSdk = true;
			$this->_needTracking = true;
		}

		// FB Send
		if ($this->_params->get('enableFbSend', 1) && in_array('facebooksend', $this->_enabledButtons))
		{
			$fbData                  = array();
			$fbData['url']           = $url;
			$fbData['fbColorscheme'] = $this->_params->get('fbColorscheme', 'light');

			if ($this->_params->get('fbUseHtml5', true))
			{
				$displayData['buttons']['fb-send-html5'] = $fbData;
			}
			else
			{
				$displayData['buttons']['fb-send'] = $fbData;
			}
			$this->_needFBSdk = true;
			$this->_needTracking = true;
		}

		// FB Share
		if ($this->_params->get('enableFbShare', 1) && in_array('facebookshare', $this->_enabledButtons))
		{
			$fbData             = array();
			$fbData['url']      = $url;
			$fbData['fbLayout'] = $this->_params->get('fbShareLayout', 'button_count');

			if ($this->_params->get('fbUseHtml5', true))
			{
				$displayData['buttons']['fb-share-html5'] = $fbData;
			}
			else
			{
				$displayData['buttons']['fb-share'] = $fbData;
			}
			$this->_needFBSdk = true;
			$this->_needTracking = true;
		}

		if ($this->_params->get('enableLinkedIn', 1) && in_array('linkedin', $this->_enabledButtons))
		{
			$displayData['buttons']['linkedin'] = array('loadScript'  => !$this->_linkedinScriptLoaded, 'url' => $url,
			                                            'languageTag' => $this->_underscoredLanguageTag, 'layout' => $this->_params->get('linkedinlayout', 'none'));
			$this->_linkedinScriptLoaded        = true;
			$this->_needTracking = true;
		}

		// perform replace
		if (!empty($displayData['buttons']))
		{
			$buttonsHtml = ShlMvcLayout_Helper::render('com_sh404sef.social.wrapper', $displayData);
		}
		else
		{
			$buttonsHtml = '';
		}

		return $buttonsHtml;
	}

	private function _computeUrl($url, $sefConfig, $context, $content, $isTag = false)
	{
		// if no URL, use current
		if (empty($url))
		{
			// no url set on social button tag, we should
			// use current URL, except if we are on a page
			// where this would cause the wrong url to be shared
			// try identify this condition
			if ($isTag || $this->_shouldDisplaySocialButtons($url, $sefConfig, $context, $content))
			{
				Sh404sefHelperShurl::updateShurls();
				$pageInfo = Sh404sefFactory::getPageInfo();
				if (empty($url))
				{
					$url = !$this->_params->get('useShurl', true) || empty($pageInfo->shURL) ? $pageInfo->currentSefUrl
						: JURI::base() . ltrim($sefConfig->shRewriteStrings[$sefConfig->shRewriteMode], '/') . $pageInfo->shURL;
				}
			}
		}

		return $url;
	}

	private function _shouldDisplaySocialButtons(&$url, $sefConfig, $context = '', $content = null)
	{
		// if SEO off, don't do anything
		if (!$sefConfig->shMetaManagementActivated)
		{
			return false;
		}

		$shouldDisplay = true;
		$updatedUrl    = '';

		// user can disable this attempt to identify possible failure
		// to select the correct url
		if (!$this->_params->get('onlyDisplayOnCanonicalUrl', true))
		{
			return $shouldDisplay;
		}

		$app      = JFactory::getApplication();
		$printing = $app->input->getInt('print');
		if (!empty($printing))
		{
			return false;
		}

		// get request details
		if (empty($context))
		{
			$component = '';
			$view      = '';
		}
		else
		{
			$bits = explode('.', $context);
			if (!empty($bits))
			{
				$component = $bits[0];
				$view      = empty($bits[1]) ? $app->input->getCmd('view', '') : $bits[1];
			}
		}

		if (empty($component) && empty($view))
		{
			return false;
		}

		switch ($component)
		{
			case 'com_content':
				// only display if on an article page
				if ($view == 'article')
				{
					$id = $app->input->getInt('id', 0);
					if (!empty($content->id) && $id != $content->id && !empty($content->link))
					{
						$updatedUrl = JURI::getInstance()->toString(array('scheme', 'host')) . $content->link;
					}
				}
				else if ($view == 'featured')
				{
					if (!empty($content->id))
					{
						$link       = JRoute::_('index.php?option=com_content&view=article&id=' . $content->id);
						$updatedUrl = JURI::getInstance()->toString(array('scheme', 'host')) . $link;
					}
				}
				else
				{
					$shouldDisplay = false;
				}
				// check category
				if ($shouldDisplay)
				{
					$cats  = $this->_params->get('enabledCategories', array());
					$catid = null;
					if (!empty($cats) && ($cats[0] != 'show_on_all'))
					{
						// find about article category
						if (!empty($content))
						{
							// we have article details
							$catid = empty($content->catid) ? 0 : (int) $content->catid;
						}
						else
						{
							// no article details, use request
							$catid = JRequest::getInt('catid', 0);
						}
						if (empty($catid))
						{
							if (!empty($content) && !empty($content->id))
							{
								$article = JTable::getInstance('content');
								$article->load($content->id);
								$catid = !empty($article->catid) ? (int) $article->id : 0;
							}
						}
						if (!empty($catid))
						{
							$shouldDisplay = in_array($catid, $cats);
						}
					}
				}
				break;
			case 'com_k2':
				$shouldDisplay = $view == 'item';
				break;
			default:
				break;
		}

		if (!empty($updatedUrl) && $shouldDisplay)
		{
			$url = $updatedUrl;
		}

		return $shouldDisplay;
	}

	/**
	 * Insert appropriate script links into document
	 */
	private function _insertSocialLinks(&$page, $sefConfig)
	{
		$headLinks   = '';
		$bottomLinks = '';

		// what do we must link to
		$showFb        = strpos($page, '<div class="fb-"') !== false || strpos($page, '<fb:') !== false;
		$showTwitter   = strpos($page, '<a href="https://twitter.com/share"') !== false;
		$showPlusOne   = strpos($page, '<g:plusone callback="_sh404sefSocialTrackGPlusTracking"') !== false;
		$gPlusPage     = $this->_params->get('googlePlusPage', '');
		$gPlusPage     = JString::trim($gPlusPage, '/');
		$showGPlusPage = strpos($page, 'onclick="_sh404sefSocialTrack.GPageTracking') !== false && !empty($gPlusPage);
		$showPinterest = strpos($page, 'class="pin-it-button"') !== false;
		$showLinkedin  = strpos($page, '//platform.linkedin.com/in.js') !== false;

		// insert social tracking javascript
		if ($this->_needTracking && ($showFb || $showTwitter | $showPlusOne || $showGPlusPage || $showPinterest))
		{
			// G! use underscore in language tags
			$headLinks .= "\n<script src='" . JURI::base(true) . '/plugins/sh404sefcore/sh404sefsocial/sh404sefsocial.js'
				. "' type='text/javascript' ></script>";
			$headLinks .= "\n<script type='text/javascript'>
      _sh404sefSocialTrack.options = {enableGoogleTracking:" . ($this->_params->get('enableGoogleSocialEngagement') ? 'true' : 'false')
				. ",
      enableAnalytics:" . ($this->_params->get('enableSocialAnalyticsIntegration') && Sh404sefHelperAnalytics::isEnabled() ? 'true' : 'false')
				. ", trackerName:''};
      window.fbAsyncInit = _sh404sefSocialTrack.setup;
      </script>";
		}

		if ($showFb)
		{
			$page = str_replace('<html ', '<html xmlns:fb="http://ogp.me/ns/fb#" ', $page);
		}

		// twitter share
		if ($showTwitter)
		{
			$bottomLinks .= "\n<script src='//platform.twitter.com/widgets.js' type='text/javascript'></script>";
		}

		// plus one
		if ($showPlusOne)
		{
			$bottomLinks .= "
      <script type='text/javascript'>
      (function() {
      var po = document.createElement('script');
      po.type = 'text/javascript';
      po.async = true;
      po.src = 'https://apis.google.com/js/plusone.js';
      var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
    })();

    </script>
    ";
		}

		// google plus page badge
		if ($showGPlusPage)
		{
			$headLinks .= "\n<link href='https://plus.google.com/" . $gPlusPage . "/' rel='publisher' />";
		}

		// pinterest
		if ($showPinterest)
		{
			$headLinks .= "
      <script type='text/javascript'>

      (function() {
      window.PinIt = window.PinIt || { loaded:false };
      if (window.PinIt.loaded) return;
      window.PinIt.loaded = true;
      function async_load(){
      var s = document.createElement('script');
      s.type = 'text/javascript';
      s.async = true;
      if (window.location.protocol == 'https:')
      //s.src = 'https://assets.pinterest.com/js/pinit.js';
      s.src = '" . JURI::base()
				. "media/com_sh404sef/pinterest/pinit.js';
      else
      //s.src = 'http://assets.pinterest.com/js/pinit.js';
      s.src = '" . JURI::base()
				. "media/com_sh404sef/pinterest/pinit.js';
      var x = document.getElementsByTagName('script')[0];
      x.parentNode.insertBefore(s, x);
    }
    if (window.attachEvent)
    window.attachEvent('onload', async_load);
    else
    window.addEventListener('load', async_load, false);
    })();
    </script>
    ";
		}

		if ($this->_needTracking && ($showFb || $showTwitter | $showPlusOne || $showGPlusPage || $showPinterest || $showLinkedin))
		{
			// add our wrapping css
			$headLinks .= ShlMvcLayout_Helper::render('com_sh404sef.social.css');
		}

		// actually insert
		if (!empty($headLinks))
		{
			$headLinks .= "\n<script type='text/javascript'>var _sh404SEF_live_site = '" . JURI::base() . "';</script>\n";

			// insert everything in page
			$page = shInsertCustomTagInBuffer($page, '</head>', 'before', $headLinks, $firstOnly = 'first');
		}

		if (!empty($bottomLinks))
		{
			// insert everything in page
			$page = shInsertCustomTagInBuffer($page, '</body>', 'before', $bottomLinks, $firstOnly = 'first');
		}

	}

	public function onContentBeforeDisplay($context, &$row, &$params, $page = 0)
	{
		$app = JFactory::getApplication();

		// are we in the backend - that would be a mistake
		if (!defined('SH404SEF_IS_RUNNING') || $app->isAdmin())
		{
			return;
		}

		// don't display on errors
		$pageInfo = Sh404sefFactory::getPageInfo();
		if (!empty($pageInfo->httpStatus) && $pageInfo->httpStatus == 404)
		{
			return '';
		}

		if ($this->_params->get('buttonsContentLocation', 'onlyTags') == 'before')
		{
			$buttons = $this->_sh404sefGetSocialButtons(Sh404sefFactory::getConfig(), $url = '', $context, $row);
		}
		else
		{
			$buttons = '';
		}

		return $buttons;

	}

	public function onContentAfterDisplay($context, &$row, &$params, $page = 0)
	{
		if ($this->_params->get('buttonsContentLocation', 'onlyTags') == 'after')
		{
			$buttons = $this->_sh404sefGetSocialButtons(Sh404sefFactory::getConfig(), $url = '', $context, $row);
		}
		else
		{
			$buttons = '';
		}

		return $buttons;

	}

	public function onSh404sefInsertFBJavascriptSDK(&$page, $sefConfig)
	{
		static $_inserted = false;

		if ($this->_needFBSdk && $sefConfig->shMetaManagementActivated && !$_inserted
			&& ($this->_params->get('enableFbLike', true) || $this->_params->get('enableFbSend', true) || $this->_params->get('enableFbShare', true))
		)
		{
			$_inserted = true;

			// append Facebook SDK
			$socialSnippet = ShlMvcLayout_Helper::render('com_sh404sef.social.fb_sdk', array('languageTag' => $this->_underscoredLanguageTag));

			// use page rewrite utility function to insert as needed
			$page = shPregInsertCustomTagInBuffer($page, '<\s*body[^>]*>', 'after', $socialSnippet, $firstOnly = 'first');
		}
	}

}
