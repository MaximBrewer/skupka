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
use \Arisoft\Youtube\Helper as YoutubeHelper;
use \Arimagnificpopup\Layout\Mediagallery\Imagegallery as ImageGallery;
use \Arisoft\Layout\Mediagallery\Delegate\Youtubesimple as YoutubesimpleDelegate;

class Youtube extends HandlerBase
{
    function processContent($params, $content)
    {
        $videoList = $params->get('youtube_video');
        $imageSize = $params->get('youtube_imagesize');
        $layout = $params->get('youtube_layout', 'text');
        $params->set('opt_type', 'iframe');
        $class = uniqid('amp_', false);

        $params->set('opt_gallery_enabled', true);

        Helper::initInstance('.' . $class, $params);

        $videoList = preg_split('/\s+/', $videoList);

        return $this->getLayoutContent($layout, $videoList, $class, $content, $params, $imageSize);
    }

    protected function layout_Text($videoList, $class, $content, $params, $imageSize)
    {
        $firstVideo = $videoList[0];
		$isImage = !!(preg_match('/^<img .+>$/i', $content));

        $html = array();

        $html[] = '<a href="' . YoutubeHelper::getVideoLink($firstVideo) . '" class="' . $class . ($isImage ? ' mfp-video-preview' : '') . ' amp-link">' . $content . '</a>';

        for ($i = 1; $i < count($videoList); $i++) {
            $videoId = $videoList[$i];

            $html[] = '<a href="' . YoutubeHelper::getVideoLink($videoId) . '" class="' . $class . ' amp-link amp-hide"></a>';
        }

        return join('', $html);
    }
}