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
?>
<?php $imgfloat = (empty($this->images->float_fulltext)) ? $this->itemParams->get('float_fulltext') : $this->images->float_fulltext; ?>
<img src="<?php echo htmlspecialchars($this->images->image_fulltext); ?>"
    alt="<?php echo htmlspecialchars($this->images->image_fulltext_alt); ?>"
    <?php if ($this->images->image_fulltext_caption): ?>
        class="caption pull-<?php echo $imgfloat; ?>" title="<?php echo htmlspecialchars($this->images->image_fulltext_caption); ?>"
    <?php else: ?>
        class="pull-<?php echo $imgfloat; ?>" 
    <?php endif; ?>    
    width="<?php echo $this->plugin->params->get('article_images_full_width', 200); ?>"
    height="<?php echo $this->plugin->params->get('article_images_full_height', 200); ?>"
/>