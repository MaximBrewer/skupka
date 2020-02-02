<?php
/*
 * ARI Magnific Popup
 *
 * @package		ARI Magnific Popup
 * @version		1.0.0
 * @author		ARI Soft
 * @copyright	Copyright (c) 2010 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

namespace Arimagnificpopup\Contenthandler\Handler;

defined('_JEXEC') or die;

use Arimagnificpopup\Contenthandler\Base As HandlerBase;
use \Arimagnificpopup\Helper as Helper;

class Inline extends HandlerBase
{
	function processContent($params, $content)
	{
		$params->set('opt_type', 'inline');
		$id = uniqid('amp_', false);
		$contentId = $id . '_content';
        $title = $params->get('inline_title');
        $link = $params->get('inline_link');
        $content = $params->get('inline_content');

        Helper::initInstance('#' . $id, $params);

		return sprintf(
			'<a href="#%2$s" id="%1$s" class="amp-link"%4$s>%5$s</a><div id="%2$s" class="mfp-white-popup mfp-hide mfp-with-anim">%3$s</div>',
			$id,
			$contentId,
            $content,
            $title ? ' title="' . htmlentities($title) . '"' : '',
            $link
		);
	}
}