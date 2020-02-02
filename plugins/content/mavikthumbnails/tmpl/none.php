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
$class = $this->image->getAttribute('class') . ($this->isThumbnail ? ' thumbnail' : '');
$title =  htmlspecialchars($this->image->getAttribute('title'));
$hasCaption = $title && $this->image->hasCaption();

if ($hasCaption) {
    $html .= "<figure class=\"$class\" style=\"{$this->linkStyle}\">";
    $class = '';
} else {
    $this->image->setAttribute('class' , $class);
}
$html .= (string) $this->image;
if ($hasCaption) {
    $html .= "<figcaption>$title</figcaption>";
}
if ($hasCaption) {
    $html .= '</figure>';
}

echo $html;