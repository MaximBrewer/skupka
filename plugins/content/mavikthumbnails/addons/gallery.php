<?php
/**
 * @package Joomla
 * @subpackage mavikThumbnails 2
 * @copyright 2014 Vitaliy Marenkov
 * @author Vitaliy Marenkov <admin@mavik.com.ua>
 * @license GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Plugin\Content\MavikThumbnails;

defined( '_JEXEC' ) or die();

/**
 * Class for Gallery
 *
 */
class Gallery
{
    public $plugin;
    public $baseMediaPath;
    public $baseMediaUrl;
    public $context;
    public $item;
    public $itemParams;
    public $page;
    public $images;
    public $params = array();

    public function __construct(&$plugin, $context, &$item, &$params, $page)
    {
        $mediaParams = \JComponentHelper::getParams('com_media');
        $this->baseMediaPath = JPATH_ROOT . '/' . $mediaParams->get('image_path', 'images');
        $this->baseMediaUrl = \JUri::root() . $mediaParams->get('image_path', 'images');        
     
        $this->plugin = $plugin;
        $this->context = $context;
        $this->item = $item;
        $this->itemParams = $params;
        $this->page = $page;

        $this->plugin->params->set('defaultSize', '');
    }
    
    public function process()
    {
        $regex = '/\{gallery(?<params>.*?)\}(?<path>.*?)\{\/gallery\}/is';
        $this->item->text = preg_replace_callback($regex, array($this, 'replacer'), $this->item->text);
    }
    
    public function replacer(&$matches)
    {
        $path = strip_tags($matches['path']);
        $fullPath = JPATH_ROOT.'/'.$path;
        $this->images = \JFolder::files($fullPath, '.*\.(jpg|jpeg|png|gif|JPG|JPEG|PNG|GIF)$');
        foreach ($this->images as &$image) {
            $image = \JUri::root() . $path . '/' . $image;
        }

        if ($matches['params']) {
            $this->params = $this->parseParams($matches['params']);
        }        
        $this->width = !empty($this->params['width']) ? $this->params['width'] : $this->plugin->params->get('gallery_width', 200);
        $this->height = !empty($this->params['height']) ? $this->params['height'] : $this->plugin->params->get('gallery_height', 200);
        $this->resizeType = !empty($this->params['resize']) ? $this->params['resize'] : $this->plugin->params->get('gallery_resize_type', 'fill');
        
        $layout = \JPluginHelper::getLayoutPath('content', 'mavikthumbnails', 'gallery');
        ob_start();
        include $layout;
        return ob_get_clean();        
    }
    
    protected function parseParams($params)
    {
        $result = array();
        preg_match_all('/(?<=\s)(?<param>[\w\-_]+)(\s*=\s*(?<quote>[\"\']?)(?<value>.*?)\k<quote>)?(?=(\s|$))/s', $params, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $result[$match['param']] = $match['value'];
        }
        return $result;
    }
}