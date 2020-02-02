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

$document = JFactory::getDocument();
$document->addStyleSheet(JUri::base().'/media/plg_content_mavikthumbnails/gallery/gallery.css');
?>
<ul class="gallery">
    <?php foreach ($this->images as $src): ?>
    <li class="gallery-item"><img
        src="<?php echo $src ?>"
        alt=""
        width="<?php echo $this->width; ?>"
        height="<?php echo $this->height; ?>"
        data-mavikthumbnails-resize_type="<?php echo $this->resizeType; ?>"
        data-mavikthumbnails-thumbnails_for=""
        data-mavikthumbnails-default_size=""
    /></li>
    <?php endforeach; ?>
</ul>