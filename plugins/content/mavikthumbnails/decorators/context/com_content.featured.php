<?php
/**
 * @package Joomla
 * @subpackage mavikThumbnails 2
 * @copyright 2014 Vitaliy Marenkov
 * @author Vitaliy Marenkov <admin@mavik.com.ua>
 * @license GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Plugin\Content\MavikThumbnails;
use Plugin\Content\MavikThumbnails\DecoratorComContentCategory;

defined('_JEXEC') or die();

require_once 'com_content.category.php';

/**
 * Decorator for Featured of Content Component
 */
class DecoratorComContentFeatured extends DecoratorComContentCategory
{
}
