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

namespace Arimagnificpopup\Plugin;

defined('_JEXEC') or die;

use JFactory, JText;
use Arisoft\Plugin\Content as ContentPlugin;
use Arisoft\Parameters\Helper as ParametersHelper;
use Arisoft\Utilities\ObjectFactory as ObjectFactory;

class Content extends ContentPlugin
{
	private $params;

    private $ignoreParamKeys = array(
    );

    private $aliasParamKeys = array(
        'alignTop' => 'opt_alignTop',

        'fxOpen' => 'opt_fx_fxOpen',

        'escape' => 'opt_enableEscapeKey',

        'closeOnBgClick' => 'opt_closeOnBgClick',

        'showCloseBtn' => 'opt_showCloseBtn',

        'fxOpen' => 'opt_fx_fxOpen',

        'fxClose' => 'opt_fx_fxClose',

        'slideshowPause' => 'opt_slideshow_pauseDuration',

        'slideshowAuto' => 'opt_slideshow_autoPlay',

        'slideshowEndless' => 'opt_slideshow_endless',

        'slideshowMeter' => 'opt_slideshow_showIndicator',

        'slideshowPlayBtn' => 'opt_slideshow_playTemplate',

        'slideshowPauseBtn' => 'opt_slideshow_pauseTemplate',

        'mobileDisable' => 'ext_mobile_disabled',

        'mobileWidth' => 'ext_mobile_breakpoint_width',

        'retina' => 'retina_enabled',

        'retinaRatio' => 'retina_ratio',

        'galleryClass' => 'mediagallery_mainClass',

        'lazyLoad' => 'mediagallery_lazyLoad',

        'galleryFrame' => 'mediagallery_frame',

        'galleryHover' => 'mediagallery_hoverFx',

        'paging' => 'paging_enabled',

        'pagingPos' => 'paging_position',

        'page' => 'paging_currentPage',

        'slideshow' => 'gallery_slideshow_enabled',

        'fileFilter' => 'gallery_search_fileFilter',

        'subdir' => 'gallery_search_subdir',

        'sortBy' => 'gallery_search_sortBy',

        'sortDir' => 'gallery_search_sortDirection',

        'metaFile' => 'gallery_search_metaFile',

        'thumb' => 'gallery_search_thumb_thumb_generateThumbs',

        'thumbWidth' => 'gallery_search_thumb_thumb_thumbWidth',

        'thumbHeight' => 'gallery_search_thumb_thumb_thumbHeight',

        'ytThumb' => 'youtube_imagesize',

        'autoConvert' => 'nametotitle_enabled',

        'autoConvertType' => 'nametotitle_transform',

        'autoConvertTemplate' => 'nametotitle_transformtemplate'
    );
	
	function __construct($params, $tag, $nested = false)
	{
		$this->params = $params;

		parent::__construct($tag, $nested);
	}

	protected function contentHandler($attrs, $content, $sourceContent) 
	{
        $type = $this->detectContentType($attrs);
        if (empty($type))
        {
            return '<div style="color:red;">' . JText::sprintf('PLG_ARIMAGNIFICPOPUP_WARNING_TYPEISNOTDEFINED', $sourceContent) . '</div>';
        }

		$engine = ObjectFactory::getObject($type, 'Arimagnificpopup\\Plugin\\Contenttype\\Engine');
        if (is_null($engine))
        {
            return '<div style="color:red;">' . JText::sprintf('PLG_ARIMAGNIFICPOPUP_WARNING_TYPEISNOTSUPPORTED', $type) . '</div>';
        }

        $params = $this->prepareParameters($attrs);

        return $engine->processContent($attrs, $params, $content);
	}

    private function prepareParameters($attrs)
    {
        $params = clone($this->params);

        if (!empty($attrs['splash']) && (bool)$attrs['splash'])
        {
            unset($attrs['splash']);
            $attrs['splash_enabled'] = true;

            $treeAttrs = ParametersHelper::flatParametersToTree($attrs);
            $splashAttrs = $treeAttrs['splash'];

            foreach ($splashAttrs as $key => $val)
            {
                $params->set('splash_' . $key, $val);
            }
        }

        foreach ($attrs as $key => $val)
        {
            if (isset($this->aliasParamKeys[$key]))
                $key = $this->aliasParamKeys[$key];

            if ($params->exists($key) && $params->get($key) != $val && !in_array($key, $this->ignoreParamKeys))
            {
                $params->set($key, $val);
            }
        }

        return $params;
    }

    private function detectContentType($attrs)
    {
        $type = '';

        if (isset($attrs['article']))
        {
            $type = 'article';
        }
        else if (isset($attrs['youtube']))
        {
            $type = 'youtube';
        }
        else if (isset($attrs['vimeo']))
        {
            $type = 'vimeo';
        }
        else if (isset($attrs['url']))
        {
            $type = 'url';
        }
        else if (isset($attrs['gallery']))
        {
            $type = 'gallery';
        }

        if (empty($type))
            $type = 'inline';

        return $type;
    }
}