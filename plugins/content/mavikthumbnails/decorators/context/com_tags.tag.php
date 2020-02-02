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
class DecoratorComTagsTag extends DecoratorAbstract
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
        return !empty($item->core_content_id);
    }

    public function __construct(&$plugin)
    {
        $this->type = 'com_tags.tag';
        parent::__construct($plugin);
    }

    public function hoverAllowed()
    {
        return false; // $this->plugin->item->type_alias != 'com_content.article';
    }
}
