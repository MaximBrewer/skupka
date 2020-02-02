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

use \Joomla\Utilities\ArrayHelper as ArrayHelper;
use \Arisoft\Parameters\Helper as ParametersHelper;
use \Arisoft\Image\Thumbnail as Thumbnail;
use \Arimagnificpopup\Contenthandler\Base As HandlerBase;
use \Arimagnificpopup\Helper as Helper;
use \Arimagnificpopup\Layout\Mediagallery\Imagegallery as ImageGallery;
use \Arisoft\Layout\Mediagallery\Delegate\Imagegallery as ImagegalleryDelegate;

class Gallery extends HandlerBase
{
	public function processContent($params, $content)
	{
		$slideShowEnabled = (bool)$params->get('gallery_slideshow_enabled');

		if ($slideShowEnabled)
			$params->set('opt_slideshow_enabled', true);

        $params->set('opt_gallery_enabled', true);
        $params->set('opt_type', 'image');

        $defaultTitle = Helper::getDefaultTitle($params);
        $params->set('gallery_search_defaultTitle', $defaultTitle);

        $paramsTree = ParametersHelper::flatParametersToTree($params);

        $galleryParams = ArrayHelper::getValue($paramsTree, 'gallery', array());
        $searchParams = ArrayHelper::getValue($galleryParams, 'search', array());
        $layout = ArrayHelper::getValue($galleryParams, 'layout', 'text');

        if ($layout == 'text')
            $searchParams['thumb']['thumb']['generateThumbs'] = false;

        $thumbProvider = new Thumbnail('test', $searchParams, 'arithumb', JPATH_ROOT . '/images/arimagnificpopup');
        $images = $thumbProvider->getStoredData();

        if (!is_array($images) || count($images) == 0)
            return '';

        $class = uniqid('amp_', false);
        Helper::initInstance('.' . $class, $params);



        return $this->getLayoutContent($layout, $images, $class, $content, $params);
	}

    protected function layout_Text($images, $class, $content)
    {
        $firstImage = $images[0];
        $title = ArrayHelper::getValue($firstImage, 'Title');

        if ($title)
            $title = htmlentities($title, ENT_QUOTES, 'UTF-8');

        $html = array();

        $html[] = '<a href="' . $firstImage['image']['url'] . '" class="' . $class . ' amp-link"' . ($title ? ' title="' . $title . '"' : '') . '>' . $content . '</a>';

        for ($i = 1; $i < count($images); $i++)
        {
            $image = $images[$i];
            $title = ArrayHelper::getValue($image, 'Title');

            if ($title)
                $title = htmlentities($title, ENT_QUOTES, 'UTF-8');

            $html[] = '<a href="' . $image['image']['url'] . '" class="' . $class . ' amp-link amp-hide"' . ($title ? ' title="' . $title . '"' : '') . '></a>';
        }

        return join('', $html);
    }

    protected function layout_Gallery($images, $class, $content, $params)
    {
        $containerClass = $params->get('mediagallery_mainClass', '');
        if (empty($containerClass))
            $containerClass = '';

        $params->set('mediagallery_mainClass', $containerClass . ' amp-gallery-images');
		
        $imageGallery = new ImageGallery(
            Helper::getMediaGalleryParameters($params, 'gallery'),
            array('class' => $class),
            new ImagegalleryDelegate()
        );

        return $imageGallery->getContent($images);
    }
}