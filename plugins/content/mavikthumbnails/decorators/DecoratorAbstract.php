<?php
/**
 * @package Joomla
 * @subpackage mavikThumbnails 2
 * @copyright 2014 Vitaliy Marenkov
 * @author Vitaliy Marenkov <admin@mavik.com.ua>
 * @license GNU General Public License version 2 or later; see LICENSE.txt
 * 
 * Plugin automatic replaces big images to thumbnails.
 */

namespace Plugin\Content\MavikThumbnails;

defined('_JEXEC') or die();

/**
 * Decoration of img-tag
 */
abstract class DecoratorAbstract
{
    /**
     * @var DecoratorAbstract
     */
    protected static $instances = array();

    /**
     * @var \plgContentMavikThumbnails
     */
    protected $plugin;

    /**
     * Type of decorator
     * 
     * @var string
     */
    protected $type;
    
    protected $headerAdded = false;

    /**
     * Check can be the decorator used or not
     *
     * @param \stdClass $item
     * @return boolean
     */
    static function check($item)
    {
        return true;
    }

    /**
     * 
     * @param \plgContentMavikThumbnails $plugin
     * @return DecoratorAbstract
     */
    public static function getInstance(&$plugin)
    {
        $class = get_called_class();
        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new $class($plugin);
        }
        return self::$instances[$class];
    }

    /**
     * @param \plgContentMavikThumbnails $plugin
     */
    protected function __construct(&$plugin)
    {
        $this->plugin = $plugin;
        if($this->plugin->params->get('link_scripts')) {
            $this->addHeader();
        }
    }

    /**
     * Add code in header of document 
     */
    public function addHeader()
    {
        $document = \JFactory::getDocument();
        
        // Hover-effect can be used in every decorators
        if ($this->hoverAllowed() && $this->plugin->params->get('hover')) {
            $base     = \JUri::base(true);
            \JHtml::_('jquery.framework');
            $document->addScript($base.'/media/plg_content_mavikthumbnails/js/hover.js');
        }
        
        $this->headerAdded = true;
    }

    /**
     * Action for each item
     */
    public function item()
    {
    }

    /**
     * Decoration of img-tag
     *
     * @return string
     */
    public function decorate()
    {
        if ($this->plugin->params->get('move_style', 1) && $this->hasWrapper() && $this->isThumbnail()) {
            $this->linkStyle = $this->plugin->imgTag->getStyleWithoutSize();
            $this->plugin->imgTag->setStyleOnlySize();
        } else {
            $this->linkStyle = '';
        }

        $path              = \JPluginHelper::getLayoutPath('content', 'mavikthumbnails', $this->type);
        $this->image       = $this->plugin->imgTag;
        $this->info        = $this->plugin->imageInfo;
        $this->item        = $this->plugin->item;
        $this->params      = $this->plugin->itemParams;
        $this->isThumbnail = $this->isThumbnail();

        ob_start();
        include $path;
        return ob_get_clean();
    }

    public function isThumbnail()
    {
        return !empty($this->plugin->imageInfo->thumbnail->url);
    }

    public function hoverAllowed()
    {
        return true;
    }

    public function hasWrapper()
    {
        return true;
    }
}