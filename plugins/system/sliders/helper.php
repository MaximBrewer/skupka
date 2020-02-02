<?php
/**
 * @package         Sliders
 * @version         6.0.8
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2016 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

// Load common functions
require_once JPATH_LIBRARIES . '/regularlabs/helpers/functions.php';
require_once JPATH_LIBRARIES . '/regularlabs/helpers/tags.php';
require_once JPATH_LIBRARIES . '/regularlabs/helpers/text.php';
require_once JPATH_LIBRARIES . '/regularlabs/helpers/protect.php';

RLFunctions::loadLanguage('plg_system_sliders');

/**
 * Plugin that replaces stuff
 */
class PlgSystemSlidersHelper
{
	var $params  = null;
	var $helpers = array();

	public function __construct(&$params)
	{
		$this->params = $params;

		$this->params->comment_start = '<!-- START: Sliders -->';
		$this->params->comment_end   = '<!-- END: Sliders -->';

		$this->params->tag_open  = trim(preg_replace('#[^a-z0-9-_]#si', '', $this->params->tag_open));
		$this->params->tag_close = trim(preg_replace('#[^a-z0-9-_]#si', '', $this->params->tag_close));

		$this->params->tag_link = isset($this->params->tag_link) ? $this->params->tag_link : 'tablink';
		$this->params->tag_link = trim(preg_replace('#[^a-z0-9-_]#si', '', $this->params->tag_link));


		require_once __DIR__ . '/helpers/helpers.php';
		$this->helpers = PlgSystemSlidersHelpers::getInstance($this->params);
	}

	public function onContentPrepare(&$article, $context, $params)
	{
		$area    = isset($article->created_by) ? 'articles' : 'other';
		$context = (($params instanceof JRegistry) && $params->get('rl_search')) ? 'com_search.' . $params->get('readmore_limit') : $context;

		RLHelper::processArticle($article, $context, $this, 'replaceTags', array($area, $context));
	}

	public function onAfterDispatch()
	{
		// only in html
		if (JFactory::getDocument()->getType() !== 'html' && !RLFunctions::isFeed())
		{
			return;
		}

		$this->helpers->get('head')->addHeadStuff();

		if (!$buffer = RLFunctions::getComponentBuffer())
		{
			return;
		}

		$this->replaceTags($buffer, 'component');

		JFactory::getDocument()->setBuffer($buffer, 'component');
	}

	public function onAfterRender()
	{
		// only in html and feeds
		if (JFactory::getDocument()->getType() !== 'html' && !RLFunctions::isFeed())
		{
			return;
		}

		$html = JFactory::getApplication()->getBody();

		if ($html == '')
		{
			return;
		}

		if (
			strpos($html, '{' . $this->params->tag_open) === false
			&& strpos($html, 'rl_sliders-scrollto') === false
		)
		{
			$this->helpers->get('head')->removeHeadStuff($html);

			$this->helpers->get('clean')->cleanLeftoverJunk($html);

			JFactory::getApplication()->setBody($html);

			return;
		}

		// only do stuff in body
		list($pre, $body, $post) = RLText::getBody($html);
		$this->replaceTags($body, 'body');
		$html = $pre . $body . $post;

		$this->helpers->get('clean')->cleanLeftoverJunk($html);

		JFactory::getApplication()->setBody($html);
	}

	public function replaceTags(&$string, $area = 'article', $context = '')
	{
		$this->helpers->get('replace')->replaceTags($string, $area, $context);
	}
}
