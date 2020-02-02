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
$class = $this->image->getAttribute('class');
$class = $class ? $class.' thumbnail':'thumbnail';
$item = $this->item;

echo '<a href="'. JRoute::_(TagsHelperRoute::getItemRoute($item->content_item_id, $item->core_alias, $item->core_catid, $item->core_language, $item->type_alias, $item->router)) .'" class="'.$class.'" style="'.$this->linkStyle.'">' . $this->image . '</a>';