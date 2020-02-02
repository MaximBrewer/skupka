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
use \Arisoft\Vimeo\Helper as VimeoHelper;
use \Arimagnificpopup\Layout\Mediagallery\Imagegallery as ImageGallery;
use \Arisoft\Layout\Mediagallery\Delegate\Vimeosimple as VimeosimpleDelegate;

class Vimeo extends HandlerBase
{
	function processContent($params, $content)
	{
        $videoList = $params->get('vimeo_video');
        $layout = $params->get('vimeo_layout', 'text');
        $params->set('opt_type', 'iframe');
        $class = uniqid('amp_', false);

        $params->set('opt_gallery_enabled', true);

        Helper::initInstance('.' . $class, $params);

        $videoList = preg_split('/\s+/', $videoList);

        return $this->getLayoutContent($layout, $videoList, $class, $content, $params);

        $html = '';
        foreach ($videoList as $videoId)
        {
            $videoData = VimeoHelper::getVideoMetadata($videoId);
			$thumb = $videoData->thumbnail_url;
			$title = $videoData->title;

            $html .=
                '<a href="https://vimeo.com/' . $videoId . '" class="amp-link amp-link-vimeo mfp-video-preview' . $class . '" data-popup-title="' . htmlspecialchars($title) . '"><img src="' . $thumb . '" /></a>';
        }

		return $html;
	}

    protected function layout_Text($videoList, $class, $content, $params)
    {
        $html = array();

		$isImage = !!(preg_match('/^<img .+>$/i', $content));

        $firstVideoData = VimeoHelper::getVideoMetadata($videoList[0]);
        $html[] = '<a href="' . VimeoHelper::getVideoLink($videoList[0]) . '" class="' . $class . ($isImage ? ' mfp-video-preview' : '') . ' amp-link"' . ($firstVideoData->title ? ' title="' . htmlspecialchars($firstVideoData->title) . '"' : '') . '>' . $content . '</a>';

        for ($i = 1; $i < count($videoList); $i++)
        {
            $videoId = $videoList[$i];
            $videoData = VimeoHelper::getVideoMetadata($videoId);

            $html[] = '<a href="' . VimeoHelper::getVideoLink($videoId) . '" class="' . $class . ' amp-link amp-hide"' . ($videoData->title ? ' title="' . htmlspecialchars($videoData->title) . '"' : '') . '></a>';
        }

        return join('', $html);
    }
}