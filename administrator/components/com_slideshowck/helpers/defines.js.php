<?php
/**
 * @copyright	Copyright (C) 2019. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - https://www.template-creator.com - https://www.joomlack.fr
 */

// No direct access
defined('_JEXEC') or die;
?>
<script>
	var SLIDESHOWCK = {
		TOKEN : '<?php echo JFactory::getSession()->getFormToken() ?>=1'
		, URIBASE : '<?php echo JUri::base(true) ?>'
		, URIBASEABS : '<?php echo JUri::base() ?>'
		, URIROOT : '<?php echo JUri::root(true) ?>'
		, URIROOTABS : '<?php echo JUri::root() ?>'
		, HASPAGEBUILDERCK : '<?php echo (int)file_exists(JPATH_ROOT . '/administrator/components/com_pagebuilderck') ?>'
		, ADMIN_URL : '<?php echo JUri::root(true) ?>/administrator/index.php?option=com_slideshowck'
		, FRONT_URL : '<?php echo JUri::root(true) ?>/index.php?option=com_slideshowck'
		, BASE_URL : '<?php echo JUri::base(true) ?>/index.php?option=com_slideshowck'
	};
</script>