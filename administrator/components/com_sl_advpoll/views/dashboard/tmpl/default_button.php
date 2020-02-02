<?php
/**
 * @copyright	Copyright (c) 2013 Skyline Technology Ltd (http://extstore.com). All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

?>

<a href="<?php echo $this->button['link']; ?>">
	<?php echo JHtml::_('image', $this->button['image'], null, null, true); ?>
	<div>
		<?php echo $this->button['text']; ?>
	</div>
</a>

