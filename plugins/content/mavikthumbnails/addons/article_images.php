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
 * Images of article
 *
 */
class ArticleImages
{
    /**
     * @var \plgContentMavikThumbnails
     */
    public $plugin;

    /**
     * @var string
     */
    public $context;

    /**
     * @var \stdClass
     */
    public $item;

    /**
     * @var \JRegistry
     */
    public $itemParams;

    /**
     * @var int
     */
    public $page;

    /**
     * @var array
     */
    public $images;

    public function __construct(&$plugin, $context, &$item, &$params, $page)
    {
        $this->plugin = $plugin;
        $this->context = $context;
        $this->item = $item;
        $this->itemParams = $params ?: new \Joomla\Registry\Registry;
        $this->page = $page;
    }
    
    public function process()
    {
        /**
         * @todo Для описания тега вычислить тег по запросу, чтобы обработать корректно его изобржение
         */

        $image = '';
        $layout = '';
        
        if (!empty($this->item->images)) {
            $this->images = json_decode($this->item->images);
        } elseif (!empty ($this->item->core_images)) {
            $this->images = json_decode($this->item->core_images);
            $this->item->core_images = null;
        }
        
        if ($this->images && $this->context == 'com_content.article') {
            $place = 'Full';
            if (
                !$this->images->image_fulltext &&
                $this->plugin->params->get('article_images_use_intro', 1) &&
                $this->images->image_intro
            ) {
                $this->images->image_fulltext = $this->images->image_intro;
                $this->images->float_fulltext = $this->images->float_intro;
                $this->images->image_fulltext_alt = $this->images->image_intro_alt;
                $this->images->image_fulltext_caption = $this->images->image_intro_caption;
            }
            if ($this->images->image_fulltext) {
                $layout = \JPluginHelper::getLayoutPath('content', 'mavikthumbnails', 'article_images.full');
            }
        } elseif ($this->images && !empty($this->images->image_intro)) {
            $place = 'Intro';
            $layout = \JPluginHelper::getLayoutPath('content', 'mavikthumbnails', 'article_images.intro');
        }
        
        if (!empty($layout)) {
            $this->item->images = '';
            ob_start();
            include $layout;
            $image = ob_get_clean();
            $property = "mavikImage".$place;
            $this->item->$property = $this->plugin->imageReplacer($image);            
            if ($this->plugin->params->get(strtolower("article_images_{$place}_place")) == 'text') {
                $this->item->text = $image.$this->item->text;
            }
        }
    }
}