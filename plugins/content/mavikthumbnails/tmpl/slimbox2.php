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

$html = '';
$class = $this->image->getAttribute('class') ? $this->image->getAttribute('class').' thumbnail' : 'thumbnail';
$title =  htmlspecialchars($this->image->getAttribute('title'));
$hasCaption = $title && $this->image->hasCaption();

if ($hasCaption) {
    $html .= "<figure class=\"$class\" style=\"{$this->linkStyle}\">";
    $class = '';
    $this->linkStyle = '';
}
if ($this->isThumbnail) {
    $html  .= '<a href="'.$this->info->original->url.'" class="'.$class.' zoomin" style="'.$this->linkStyle.'" rel="lightbox'.(isset($this->item->id) ? '-'.$this->item->id : '').'" title="'.$this->image->getAttribute('alt').'">';
}
$html .= (string) $this->image;
if ($hasCaption) {
    $html .= "<figcaption>$title</figcaption>";
}
if ($this->isThumbnail) {
    $html .= '</a>';
}
if ($hasCaption) {
    $html .= '</figure>';
}

echo $html;