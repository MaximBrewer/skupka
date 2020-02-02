<?php
/**
 * @package Joomla
 * @subpackage mavikThumbnails 2
 * @copyright 2014 Vitaliy Marenkov
 * @author Vitaliy Marenkov <admin@mavik.com.ua>
 * @license GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Plugin\Content\MavikThumbnails;

defined('_JEXEC') or die();

/**
 * Decorator for Category Blog of Content Component
 */
class DecoratorComContentCategory extends DecoratorAbstract
{
    /**
     * Check can be the decorator used or not
     *
     * @param \stdClass $item
     * @return boolean
     */
    static function check($item)
    {
        // is it article?
        return isset($item->catid);
    }
   
    public function __construct(&$plugin)
    {
        $this->type = 'com_content.category';
        parent::__construct($plugin);
    }
    
    public function hoverAllowed()
    {
        return false;
    }

    public function hasWrapper()
    {
        $item = $this->plugin->item;
        return
            (isset($item->readmore) && $item->readmore) ||
            (isset($item->params) && is_object($item->params) && $item->params->get('show_title') && $item->params->get('link_titles'))
        ;
    }
}
