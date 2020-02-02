<?php
/**
 * @package         Advanced Module Manager
 * @version         7.5.1PRO
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2018 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

/**
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>
	<div class="btn-toolbar">
		<div class="btn-group">
			<button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('module.save');">
				<?php echo JText::_('JSAVE'); ?></button>
		</div>
		<div class="btn-group">
			<button type="button" class="btn btn-default" onclick="Joomla.submitbutton('module.cancel');">
				<?php echo JText::_('JCANCEL'); ?></button>
		</div>
		<div class="clear"></div>
	</div>

<?php
$this->setLayout('edit');
echo $this->loadTemplate();
