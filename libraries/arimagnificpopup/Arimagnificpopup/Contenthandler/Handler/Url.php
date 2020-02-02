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

class Url extends HandlerBase
{
	function processContent($params, $content)
	{
        $urlList = $params->get('url_url');
        $params->set('opt_gallery_enabled', true);
        $params->set('opt_type', 'iframe');
        $class = uniqid('amp_', false);
        $title = $params->get('url_title');

        $urlList = array_map('trim', explode(';', $urlList));

        if (!is_array($urlList) && count($urlList) == 0)
            return '';

        Helper::initInstance('.' . $class, $params);

        $html = array();

        $html[] = '<a href="' . htmlentities($urlList[0]) . '" class="amp-link ' . $class . '"' . ($title ? ' data-popup-title="' . htmlentities($title) . '"' : '') . '>' . $content . '</a>';

        for ($i = 1; $i < count($urlList); $i++)
        {
            $url = $urlList[$i];

            $html[] = '<a href="' . htmlentities($url) . '" class="' . $class . ' amp-hide amp-link"></a>';
        }

        return join('', $html);
	}
}