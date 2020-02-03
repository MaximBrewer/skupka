<?php
/**
 * @name		Slideshow CK
 * @package		com_slideshowck
 * @copyright	Copyright (C) 2015. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - https://www.template-creator.com - https://www.joomlack.fr
 */

use \Slideshowck\CKFof;

defined('_JEXEC') or die;

// get the version installed
$installed_version = 'UNKOWN';
if ($xml_installed = simplexml_load_file(JPATH_SITE .'/administrator/components/com_slideshowck/slideshowck.xml')) {
	$installed_version = (string)$xml_installed->version;
}

// loads the language files from the frontend
$lang	= JFactory::getLanguage();
$lang->load('mod_slideshowck', JPATH_SITE . '/modules/mod_slideshowck', $lang->getTag(), false);
$lang->load('mod_slideshowck', JPATH_SITE, $lang->getTag(), false);
?>
<style>
	.ckaboutversion {
		margin: 10px;
		padding: 10px;
		font-size: 20px;
		font-color: #000;
		text-align: center;
	}
	.ckcenter {
		margin: 10px 0;
		text-align: center;
	}
</style>
<div class="ckaboutversion">SLIDESHOW CK <?php echo $installed_version; ?> LIGHT</div>
<div class="ckcenter"><a href="https://www.joomlack.fr/en/joomla-extensions/slideshow-ck" target="_blank" class="btn btn-small btn-inverse"><?php echo JText::_('Get the Pro version'); ?></a></div>
<p class="ckcenter"><a href="https://www.joomlack.fr" target="_blank">https://www.joomlack.fr</a></p>
<div class="alert ckcenter"><a href="https://extensions.joomla.org/extensions/extension/photos-a-images/slideshow/slideshow-ck" target="_blank" class="btn btn-small btn-warning"><?php echo JText::_('SLIDESHOWCK_VOTE_JED'); ?></a></div>
<div class="ckcenter"><a href="https://www.joomlack.fr/en/documentation/48-slideshow-ck" target="_blank"><?php echo JText::_('SLIDESHOWCK_DOCUMENTATION')  ?></a></div>
<hr />