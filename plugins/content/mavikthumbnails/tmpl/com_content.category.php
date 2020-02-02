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
defined('_JEXEC') or die();

$class = $this->image->getAttribute('class');
$class = $class ? $class.' thumbnail':'thumbnail';

if (
    (isset($this->item->readmore) && $this->item->readmore) ||
    (isset($this->item->params) && is_object($this->item->params) && $this->item->params->get('show_title') && $this->item->params->get('link_titles'))
) {    
   if ($this->item->params->get('access-view')) {
		$link = JRoute::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid));
   } else {
		$menu = JFactory::getApplication()->getMenu();
		$active = $menu->getActive();
		$itemId = $active->id;
		$link1 = JRoute::_('index.php?option=com_users&view=login&Itemid=' . $itemId);
		$returnURL = JRoute::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid));
		$link = new JUri($link1);
		$link->setVar('return', base64_encode($returnURL));
   }
   
   echo '<a href="'. $link .'" class="'.$class.'" style="'.$this->linkStyle.'">' . $this->image . '</a>';
   
} else {
    $this->image->setAttribute('class', $class);
    echo (string) $this->image;
}