<?php
/*
 * ARI Framework
 *
 * @package		ARI Framework
 * @version		1.0.0
 * @author		ARI Soft
 * @copyright	Copyright (c) 2009 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 *
 */
namespace Arisoft\Layout\Mediagallery;

defined('_JEXEC') or die;

use JURI, JFactory, JText;
use \Arisoft\Layout\Mediagallery\Delegate\Base as Delegate;
use \Arisoft\Template\Template as Template;
use \Arisoft\Html\Helper as HtmlHelper;
use \Joomla\Utilities\ArrayHelper as ArrayHelper;
use \Arisoft\Parameters\Helper as ParametersHelper;

class MediagalleryFieldsMapping
{
    public $previewUrl = 'Preview_Url';

    public $previewWidth = 'Preview_Width';

    public $previewHeight = 'Preview_Height';

    public $url = 'Item_Url';

    public $width = 'Item_Width';

    public $height = 'Item_Height';

    public $title = 'Title';

    public $description = 'Description';
}

class MediagalleryPagingSettings
{
    public $enabled = false;

    public $position = 'bottom';

    public $currentPage = 0;

    public $pageSize = 20;

    public $pageList = array(
        array(
            'value' => 10,

            'text' => 10
        ),
        array(
            'value' => 20,

            'text' => 20
        ),
        array(
            'value' => 30,

            'text' => 30
        ),
        array(
            'value' => 40,

            'text' => 40
        ),
        array(
            'value' => 50,

            'text' => 50
        ),
    );

    function __construct($params = null)
    {
        $this->populate($params);
    }

    protected function populate($params)
    {
        $this->enabled = (bool)ArrayHelper::getValue($params, 'enabled');
        $this->position = ArrayHelper::getValue($params, 'position');

        $currentPage = max(0, intval(ArrayHelper::getValue($params, 'currentPage', 0), 10));
        if ($currentPage > 0)
            --$currentPage;

        $this->currentPage = $currentPage;

        $pageList = ArrayHelper::getValue($params, 'pagelist');

        if ($pageList)
        {
			$pageSize = null;
            $list = array();

            $pageList = json_decode($pageList, true);
            foreach ($pageList as $pageListItem)
            {
                $size = $pageListItem['size'];
                $label = $pageListItem['label'];
                $isDefault = (bool)$pageListItem['default'];

                if ($size === '')
                    continue ;

                $size = intval($size, 10);
                if (empty($label))
                    $label = $size;
                else
                    $label = JText::_($label);

                if ($isDefault)
                    $pageSize = $size;

                $list[] = array(
                    'value' => $size,

                    'text' => $label
                );
            }

            if (count($list) > 0)
			{
                $this->pageList = $list;
				
				if (is_null($pageSize))
				{
					$pageSize = $list[0]['value'];
				}
				
				$this->pageSize = $pageSize;
			}
        }
    }
}

class MediagallerySettings
{
    public $visibleItemCount = 0;

    public $paging = null;

    public $lazyLoad = false;

    public $cssClass = '';

    public $theme = '';

    function __construct($params = null)
    {
        $this->populate($params);
    }

    protected function populate($params)
    {
        $this->cssClass = ArrayHelper::getValue($params, 'mainClass', '');
        if (empty($this->cssClass))
            $this->cssClass = '';

        if ((bool)ArrayHelper::getValue($params, 'frame', ''))
            $this->cssClass .= ' ari-media-gallery-frame';

        $hoverFx = ArrayHelper::getValue($params, 'hoverFx', '');
        if ($hoverFx)
            $this->cssClass .= ' ari-media-gallery-fx-' . $hoverFx;

        $visibleItemCount = intval(ArrayHelper::getValue($params, 'visibleItemCount', 0), 10);
        if ($visibleItemCount < 0)
            $visibleItemCount = 0;

        $this->visibleItemCount = $visibleItemCount;

        $this->lazyLoad = (bool)ArrayHelper::getValue($params, 'lazyLoad', true);

        $pagingParams = ArrayHelper::getValue($params, 'paging', array());
        $this->paging = new MediagalleryPagingSettings($pagingParams);
    }
}

class Mediagallery
{
    private static $assetsLoaded = false;

    public $assetsRootUri = 'media/arisoft/mediagallery';
	
	public $textPrefix = null;

    protected $id = null;

    protected $originalParams = null;

    protected $params = null;

    protected $options = null;

    protected $mapping = null;

    protected $delegate = null;

    public function __construct($params = array(), $options = array(), Delegate $delegate = null)
    {
        $this->id = uniqid('mg_', false);
        $this->originalParams = $params;
        $this->mapping = new MediagalleryFieldsMapping();
        $this->params = new MediagallerySettings($params);
        $this->options = $options;

        if (is_null($delegate))
            $delegate = new Delegate();

        $this->delegate = $delegate;
    }
	
    public function getOption($name, $defaultValue = null)
    {
        return ArrayHelper::getValue($this->options, $name, $defaultValue);
    }

    public function loadAssets()
    {
        if (self::$assetsLoaded)
            return ;

        $baseUri = $this->assetsRootUri;
        $jsUri = $baseUri . '/js/';

        $document = JFactory::getDocument();

        $document->addScript($baseUri . '/../holder/holder.js');
        $document->addScriptDeclaration(
            ';Holder.add_theme("mediagallery", {background: "transparent", text: " "});'
        );

        $document->addScript($jsUri . 'gallery.js');
        $document->addStyleSheet($baseUri . '/css/style.css');

        self::$assetsLoaded = true;
    }

    public function initClientInstance($itemCount = 0)
    {
        $this->loadAssets();

        $id = $this->id;
        $initCode = '';
        $useHolderJs = true;
        if ($useHolderJs)
        {
            $initCode .= sprintf(';$("#%1$s").on("mediagallery.changed", function() { Holder.run({images:".ari-media-gallery-img"}); });',
                $id
            );
        }

        $jsOptions = $this->getJsOptions($itemCount);

        $initCode .= sprintf('var opts = %1$s;', !empty($jsOptions) ? json_encode($jsOptions) : '{}');
        $initCode .= sprintf('$("#%1$s").ARIMediaGallery(opts);', $id);

        $loadMethod = 'domready';
        $document = JFactory::getDocument();
        if ($loadMethod == 'load')
            $document->addScriptDeclaration(';(window["ascJQuery"] || jQuery)(window).load(function() { var $ = window["ascJQuery"] || jQuery;' . $initCode . '});');
        else
            $document->addScriptDeclaration(';(window["ascJQuery"] || jQuery)(document).ready(function($) { ' . $initCode . '});');
    }

    protected function getJsOptions($itemCount = 0)
    {
        $options = ArrayHelper::getValue($this->originalParams, 'widget');

        $defOptions = array(
        );

        $jsOptions = ParametersHelper::getUniqueOverrideParameters($defOptions, $options);

        if ($this->params->paging->enabled)
        {
            $jsOptions['pagingEnabled'] = true;
            $jsOptions['paging'] = $this->getPagingJsOptions($itemCount);
        }

        $jsOptions['galleryType'] = 'ARIMediaGallerySimple';

        return $jsOptions;
    }

    protected function getPagingJsOptions($itemCount = 0)
    {
        $pagingParams = $this->params->paging;
        $id = $this->id;

        $jsOptions = array(
            'el' => '#' . $id . ' .ari-media-gallery-paging',
            'pageSize' => $pagingParams->pageSize,
            'itemCount' => $itemCount,
            'currentPage' => $pagingParams->currentPage
        );

        return $jsOptions;
    }

    public function getContent($items)
    {
        $params = $this->params;

        $visibleItems = $items;
        $hiddenItems = array();

        if ($params->visibleItemCount > 0)
        {
            $hiddenItems = array_splice($visibleItems, $params->visibleItemCount);
        }

        $data = array(
            'items' => array(),

            'hiddenItems' => array()
        );

        foreach ($visibleItems as $item)
        {
            $data['items'][] = array_merge($item, array('__content' => $this->getItemContent($item, $params->lazyLoad)));
        }

        foreach ($hiddenItems as $item)
        {
            $data['hiddenItems'][] = array_merge($item, array('__content' => $this->getHiddenItemContent($item)));
        }

        $this->initClientInstance(count($data['items']));

        return $this->getOutput($data);
    }

    private function getOutput($data)
    {
        @ob_start();

        Template::display(
            dirname(__FILE__) . '/tmpl/gallery.php',
            array(
                'id' => $this->id,

                'items' => $data['items'],

                'hiddenItems' => $data['hiddenItems'],

                'params' => $this->params,

                'textPrefix' => $this->textPrefix
            )
        );

        $output = ob_get_contents();

        @ob_end_clean();

        return $output;
    }

    protected function getItemContent($item, $lazyLoad)
    {
        $mapping = $this->mapping;
        $item = $this->delegate->prepareItem($item, $mapping);

        $bgImg = $this->assetsRootUri . '/css/images/bg.png';

        $title = ArrayHelper::getValue($item, $mapping->title);
        $description = ArrayHelper::getValue($item, $mapping->description);
        $aAttrs = null;
        $imgAttrs = array('class' => 'ari-media-gallery-img');
        if (!empty($title))
            $imgAttrs['title'] = $title;

        if (!empty($item[$mapping->previewUrl]))
        {
            $imgAttrs['src'] = $item[$mapping->previewUrl];

            if (!empty($item[$mapping->previewWidth]))
                $imgAttrs['width'] = $item[$mapping->previewWidth];

            if (!empty($item[$mapping->previewHeight]))
                $imgAttrs['height'] = $item[$mapping->previewHeight];

            $aAttrs = array(
                'href' => $item[$mapping->url]
            );

            if (!empty($item[$mapping->width]))
                $aAttrs['width'] = $item[$mapping->width];

            if (!empty($item[$mapping->height]))
                $aAttrs['height'] = $item[$mapping->height];

            if (!empty($description))
                $aAttrs['title'] = $description;
            else if (!empty($title))
                $aAttrs['title'] = $title;
        }
        else
        {
            $imgAttrs['src'] = $item[$mapping->url];

            if (!empty($item[$mapping->width]))
                $imgAttrs['width'] = $item[$mapping->width];

            if (!empty($item[$mapping->height]))
                $imgAttrs['height'] = $item[$mapping->height];
        }

        if ($lazyLoad)
        {
            $preloadSize = null;
            if (isset($imgAttrs['width']) && isset($imgAttrs['height']))
                $preloadSize = $imgAttrs['width'] . 'x' . $imgAttrs['height'];

            $src = $imgAttrs['src'];
            if ($preloadSize)
                $imgAttrs['data-src'] = 'holder.js/' . $preloadSize . '/mediagallery';

            $imgAttrs['data-original-src'] = $src;
            $imgAttrs['src'] = $bgImg;
            $imgAttrs['class'] .= ' lazyload';
        }

        $this->prepareItemElements($aAttrs, $imgAttrs);

        $content = sprintf(
            '<img%1$s/>',
            HtmlHelper::getAttrStr($imgAttrs)
        );

        if ($aAttrs)
            $content = sprintf(
                '<a%1$s>%2$s</a>',
                HtmlHelper::getAttrStr($aAttrs),
                $content
            );

        return $content;
    }

    protected function prepareItemElements(&$containerAttrs, &$elAttrs)
    {

    }

    protected function getHiddenItemContent($item)
    {
        $mapping = $this->mapping;
        $item = $this->delegate->prepareHiddenItem($item, $mapping);

        $title = ArrayHelper::getValue($item, $mapping->title);
        $description = ArrayHelper::getValue($item, $mapping->description);

        $aAttrs = array();
        if (!empty($item[$mapping->previewUrl]))
        {
            $aAttrs['href'] = $item[$mapping->url];
        }
        else
        {
            $aAttrs['href'] = $item[$mapping->url];
        }

        if (!empty($description))
            $aAttrs['title'] = $description;
        else if (!empty($title))
            $aAttrs['title'] = $title;

        $this->prepareHiddenItemElements($aAttrs);

        $content = sprintf(
            '<a%1$s></a>',
            HtmlHelper::getAttrStr($aAttrs)
        );

        return $content;
    }

    protected function prepareHiddenItemElements(&$containerAttrs)
    {

    }
}