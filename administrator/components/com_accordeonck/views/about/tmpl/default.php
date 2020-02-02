<?php
/**
 * @name		Accordeon Menu CK params
 * @package		com_accordeonck
 * @copyright	Copyright (C) 2016. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - http://www.template-creator.com - http://www.joomlack.fr
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

?>
<style>
	.aboutversion {
		margin: 10px;
		padding: 10px;
		font-size: 20px;
		font-color: #000;

	}
</style>
<div style="text-align:center;">
	<div class="aboutversion"><?php echo JText::_('CK_CURRENT_VERSION') . ' ' . $this->component_version; ?> <span class="accordeonckchecking" data-name="accordeonck" data-type="component" data-folder=""></span></div>
	<?php echo JText::_('COM_ACCORDEONCK_XML_DESCRIPTION'); ?>
	<br /><hr />
	<?php echo JText::_('COM_ACCORDEONCK_ABOUT_DESC'); ?>
</div>
<?php