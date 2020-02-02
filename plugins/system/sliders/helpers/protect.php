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

class PlgSystemSlidersHelperProtect
{
	var $helpers = array();
	var $params  = null;

	public function __construct()
	{
		require_once __DIR__ . '/helpers.php';
		$this->helpers = PlgSystemSlidersHelpers::getInstance();
		$this->params  = $this->helpers->getParams();

		list($tag_start, $tag_end) = $this->helpers->get('replace')->getTagCharacters();

		$this->params->protected_tags = array(
			$tag_start . $this->params->tag_open,
			$tag_start . '/' . $this->params->tag_close,
			$tag_start . $this->params->tag_link,
		);
	}

	public function protect(&$string)
	{
		RLProtect::protectFields($string);
		RLProtect::protectSourcerer($string);
	}

	public function protectTags(&$string)
	{
		RLProtect::protectTags($string, $this->params->protected_tags);
	}

	public function unprotectTags(&$string)
	{
		RLProtect::unprotectTags($string, $this->params->protected_tags);
	}
}
