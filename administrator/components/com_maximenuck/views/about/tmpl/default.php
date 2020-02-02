<?php
/**
 * @name		Maximenu CK params
 * @package		com_maximenuck
 * @copyright	Copyright (C) 2014. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - http://www.template-creator.com - http://www.joomlack.fr
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// require_once JPATH_COMPONENT . '/helpers/templateck.php';

// $document = JFactory::getDocument();
// $document->addStyleSheet('components/com_templateck/assets/css/templateck.css');

// check for the update
// $latest_version = TemplateckHelper::get_latest_version();
// $is_outdated = TemplateckHelper::is_outdated();
// if ($latest_version !== false) {
	// if ($is_outdated) {
		// echo '<p class="alertck">' . JText::_('CK_IS_OUTDATED') . ' : <b>' . $latest_version . '</b></p>';
	// } else {
		// echo '<p class="infock">' . JText::_('CK_IS_UPTODATE') . '</p>';
	// }
// } else {
	//echo '<p class="alertck">Impossible de lire le fichier de mise à jour sur http://www.template-creator.com</p>';
// }
?>
<style>
	.aboutversion {
		margin: 10px;
		padding: 10px;
		font-size: 20px;
		font-color: #000;

	}
</style>
<div class="aboutversion"><?php echo JText::_('CK_CURRENT_VERSION') . ' ' . $this->component_version; ?> <span class="maximenuckchecking" data-name="maximenuck" data-type="component" data-folder=""></span></div>

<?php echo JText::_('COM_MAXIMENUCK_XML_DESCRIPTION'); ?>
<br /><hr />
<?php echo JText::_('COM_MAXIMENUCK_ABOUT_DESC'); ?>
<?php
// $release_notes = TemplateckHelper::get_release_notes();
// if ($release_notes !== false) {
	// if (substr($release_notes, 0, 1) != '*') {
		// echo '<p class="alertck">Unable to read the file tck_update, there is an error with the characters</p>';
	// } else {
		// $versions = explode('*', htmlspecialchars($release_notes));
		// if ($is_outdated) {
			// echo '<br /><p style="text-transform:uppercase;text-decoration: underline;">Release notes :</p><br />';
		// }
		// foreach ($versions as $v) {
			// if (strlen($v) > 0) {
				// $lines = explode("\n", $v);
				// if (version_compare(trim($lines[0]), $this->tckversion) <= 0) {
					// break;
				// }
				// echo '<h4>' . htmlspecialchars($lines[1]) . '</h4>';
				// echo '<ul>';
				// for ($i = 2; $i < count($lines); $i++) {
					// if (strlen(trim($lines[$i])) > 0) {
						// echo '<li>' . htmlspecialchars($lines[$i]) . '</li>';
					// }
				// }
				// echo '</ul>';
			// }
		// }
	// }
// }
