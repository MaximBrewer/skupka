<?php
/**
 * @copyright	Copyright (c) 2013 Skyline Software (http://extstore.com). All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die();

/**
 * Skyline Advanced Poll Content Plugin
 *
 * @package		Joomla.Plugin
 * @subpackage	Skyline.AdvPoll
 */
class plgContentSL_AdvPoll extends JPlugin {
	/**
	 * Constructor
	 *
	 * @access      protected
	 * @param       object  $subject The object to observe
	 * @param       array   $config  An array that holds the plugin configuration
	 */
	public function __construct(& $subject, $config) {
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	/**
	 * onContentPrepare hook.
	 */
	public function onContentPrepare($context, &$article, &$params, $page = 0) {
		static $loadJs;

		$regex	= '#{sl_advpoll(.*?)}#s';

		if ($context == 'com_content.article' || $context == 'com_k2.item' || strpos($context, 'com_zoo.element.') !== false) {
			preg_match_all($regex, $article->text, $matches);
			JModelLegacy::addIncludePath(JPATH_ROOT . '/components/com_sl_advpoll/models');
			$model	= JModelLegacy::getInstance('poll', 'SL_AdvPollModel', array('ignore_request' => true));

			for ($i = 0, $n = count($matches[0]); $i < $n; $i++) {
				if (@$matches[1][$i]) {
					if (!$loadJs) {
						$loadJs	= true;
						JHtml::_('jquery.framework');
						JHtml::_('script', 'com_sl_advpoll/script.js', array(), true);
						JHtml::_('stylesheet', 'com_sl_advpoll/style.css', array(), true);
						JHtml::_('script', 'com_sl_advpoll/jquery.fancybox.js', array(), true);
						JHtml::_('stylesheet', 'com_sl_advpoll/jquery.fancybox.css', array(), true);

						$document	= JFactory::getDocument();
						$document->addScriptDeclaration("Skyline.AdvPoll.live_site = '" . JURI::root() . "';");
					}

					$inline_params	= $matches[1][$i];
					$inline_params	= $this->_parseParams($inline_params);

					$id				= $this->_getParam('id', $inline_params, 0);
					$title			= $this->_getParam('title', $inline_params, '');
					$width			= $this->_getParam('width', $inline_params, 250);
					$center			= $this->_getParam('center', $inline_params, 1) ? ' margin: 0 auto;' : '';

					$model->setState('filter.published', 1);
					$model->setState('filter.archived', 2);
					$item			= $model->getItem($id);

					if ($item) {
						ob_start();
						include(dirname(__FILE__) . '/tmpl/default.php');
						$content	= ob_get_contents();
						ob_end_clean();
					} else {
						$content	= '';
					}

					$article->text	= str_replace($matches[0][$i], $content, $article->text);
				}
			}
		} else {
			$article->text = preg_replace($regex, '', $article->text);
		}
	}

	/**
	 * Parse params to array
	 *
	 * @param string $params
	 * @return array
	 */
	protected function _parseParams($params) {
		$result		= array();
		$pattern	= '#\s*([^\s^=]+)?=\'(.*?)\'#s';
		if (preg_match_all($pattern, $params, $matches)) {
			for ($i = 0, $n = count($matches[0]); $i < $n; $i++) {
				$key	= $matches[1][$i];
				if ($key) {
					$result[$key]	= trim($matches[2][$i]);
				}
			}
		}

		return $result;
	}

	/**
	 * Get param value
	 * @param	string	$key
	 * @param	array	$params
	 * @return	bool|string
	 */
	protected function _getParam($key, $params, $default = false) {
		if (isset($params[$key])) {
			return $params[$key];
		} else {
			return $default;
		}
	}
}
