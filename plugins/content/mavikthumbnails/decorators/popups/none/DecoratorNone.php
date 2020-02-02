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
 * Empty decorator
 */
class DecoratorNone extends DecoratorAbstract
{
    public function __construct(&$plugin)
    {
        $this->type = 'none';
        parent::__construct($plugin);
    }

    public function hasWrapper()
    {
        return false;
    }
}