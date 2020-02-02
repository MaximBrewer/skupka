<?php
/**
 * @name		Maximenu CK params
 * @package		com_maximenuck
 * @copyright	Copyright (C) 2014. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - http://www.template-creator.com - http://www.joomlack.fr
 */
defined('_JEXEC') or die;
?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>Maximenu CK Params - Migration Tool</title>
<link href="<?php echo JUri::base(true) ?>/components/com_maximenuck/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon" />
<link rel="stylesheet" href="<?php echo JUri::base(true) ?>/components/com_maximenuck/assets/template.css" type="text/css" />
<link rel="stylesheet" href="<?php echo JUri::base(true) ?>/components/com_maximenuck/assets/modal.css" type="text/css" />
<link rel="stylesheet" href="<?php echo JUri::base(true) ?>/components/com_maximenuck/assets/maximenuck.css" type="text/css" />
<style type="text/css">

</style>
<script src="<?php echo JUri::base(true) ?>/components/com_maximenuck/assets/mootools-core.js" type="text/javascript"></script>
<script src="<?php echo JUri::base(true) ?>/components/com_maximenuck/assets/core.js" type="text/javascript"></script>
<script src="<?php echo JUri::base(true) ?>/components/com_maximenuck/assets/mootools-more.js" type="text/javascript"></script>
<script src="<?php echo JUri::base(true) ?>/components/com_maximenuck/assets/modal.js" type="text/javascript"></script>
<script src="<?php echo JUri::base(true) ?>/components/com_maximenuck/assets/jquery.min.js" type="text/javascript"></script>
<script src="<?php echo JUri::base(true) ?>/components/com_maximenuck/assets/jquery-ui-1.10.2.custom.min.js" type="text/javascript"></script>
<script src="<?php echo JUri::base(true) ?>/components/com_maximenuck/assets/maximenuck.js" type="text/javascript"></script>
<script src="<?php echo JUri::base(true) ?>/components/com_maximenuck/assets/jquery-noconflict.js" type="text/javascript"></script>
<script src="<?php echo JUri::base(true) ?>/components/com_maximenuck/assets/jquery-migrate.min.js" type="text/javascript"></script>
<script src="<?php echo JUri::base(true) ?>/components/com_maximenuck/assets/bootstrap.min.js" type="text/javascript"></script>
<script type="text/javascript">
	function keepAlive() {
		jQuery.ajax({type: "POST", url: "index.php"});
	}

	jQuery(document).ready(function() {

		SqueezeBox.initialize({});
		SqueezeBox.assign($$('a.modal'), {
			parse: 'rel'
		});
	});

	jQuery(document).ready(function()
	{
		jQuery('.hasTooltip').tooltip({"container": false});
		window.setInterval("keepAlive()", 600000);
	});

	(function() {
		var strings = {"CK_CONFIRM_DELETE": "<?php echo JText::_('CK_CONFIRM_DELETE') ?>",
			"CK_FIRST_CLEAR_VALUE": "<?php echo JText::_('CK_FIRST_CLEAR_VALUE') ?>"};
		if (typeof Joomla == 'undefined') {
			Joomla = {};
			Joomla.JText = strings;
		}
		else {
			Joomla.JText.load(strings);
		}
	})();
</script>