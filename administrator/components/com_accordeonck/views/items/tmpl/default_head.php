<?php
/**
 * @name		Accordeon Menu CK params
 * @package		com_accordeonck
 * @copyright	Copyright (C) 2014. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - http://www.template-creator.com - http://www.joomlack.fr
 */
defined('_JEXEC') or die;
?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>Accordeon Menu CK - Menu Items Edition Area</title>
<link href="<?php echo JUri::base(true) ?>/components/com_accordeonck/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon" />
<link rel="stylesheet" href="<?php echo JUri::root(true) ?>/administrator/components/com_accordeonck/assets/template.css" type="text/css" />
<link rel="stylesheet" href="<?php echo JUri::root(true) ?>/administrator/components/com_accordeonck/assets/modal.css" type="text/css" />
<link rel="stylesheet" href="<?php echo JUri::root(true) ?>/administrator/components/com_accordeonck/assets/accordeonck_items.css" type="text/css" />
<link rel="stylesheet" href="<?php echo JUri::root(true) ?>/modules/mod_accordeonck/assets/font-awesome.min.css" type="text/css" />
<link rel="stylesheet" href="<?php echo JUri::root(true) ?>/administrator/components/com_accordeonck/assets/ckbox.css" type="text/css" />
<script type="text/javascript">
	var URIROOT = "<?php echo JUri::root(true); ?>";
	var URIBASE = "<?php echo JUri::base(true); ?>";
</script>
<script src="<?php echo JUri::root(true) ?>/administrator/components/com_accordeonck/assets/mootools-core.js" type="text/javascript"></script>
<script src="<?php echo JUri::root(true) ?>/administrator/components/com_accordeonck/assets/core.js" type="text/javascript"></script>
<script src="<?php echo JUri::root(true) ?>/administrator/components/com_accordeonck/assets/mootools-more.js" type="text/javascript"></script>
<script src="<?php echo JUri::root(true) ?>/administrator/components/com_accordeonck/assets/modal.js" type="text/javascript"></script>
<script src="<?php echo JUri::root(true) ?>/administrator/components/com_accordeonck/assets/jquery.min.js" type="text/javascript"></script>
<script src="<?php echo JUri::root(true) ?>/administrator/components/com_accordeonck/assets/jquery-ui-1.10.2.custom.min.js" type="text/javascript"></script>
<script src="<?php echo JUri::root(true) ?>/administrator/components/com_accordeonck/assets/nestedsortable.js" type="text/javascript"></script>
<script src="<?php echo JUri::root(true) ?>/administrator/components/com_accordeonck/assets/accordeonck_items.js" type="text/javascript"></script>
<script src="<?php echo JUri::root(true) ?>/administrator/components/com_accordeonck/assets/jquery.min.js" type="text/javascript"></script>
<script src="<?php echo JUri::root(true) ?>/administrator/components/com_accordeonck/assets/jquery-noconflict.js" type="text/javascript"></script>
<script src="<?php echo JUri::root(true) ?>/administrator/components/com_accordeonck/assets/jquery-migrate.min.js" type="text/javascript"></script>
<script src="<?php echo JUri::root(true) ?>/administrator/components/com_accordeonck/assets/bootstrap.min.js" type="text/javascript"></script>
<script src="<?php echo JUri::root(true) ?>/administrator/components/com_accordeonck/assets/ckbox.js" type="text/javascript"></script>
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
	
	function jModalClose (){
		SqueezeBox.close();
		jQuery('.modal').modal('hide');
	}

	Joomla.orderTable = function()
	{
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != 'a.lft')
		{
			dirn = 'asc';
		}
		else
		{
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}

	jQuery(document).ready(function()
	{
		jQuery('.hasTooltip').tooltip({"html": true,"container": "body"});
		window.setInterval("keepAlive()", 600000);
	});

	(function() {
		var strings = {"CK_CONFIRM_DELETE": "<?php echo JText::_('CK_CONFIRM_DELETE') ?>", 
			"CK_FAILED_SET_TYPE": "<?php echo JText::_('CK_FAILED_SET_TYPE') ?>",
			"CK_FAILED_SAVE_ITEM_ERRORMENUTYPE": "<?php echo JText::_('CK_FAILED_SAVE_ITEM_ERRORMENUTYPE') ?>",
			"CK_ALIAS_EXISTS_CHOOSE_ANOTHER": "<?php echo JText::_('CK_ALIAS_EXISTS_CHOOSE_ANOTHER') ?>",
			"CK_FAILED_SAVE_ITEM_ERROR500": "<?php echo JText::_('CK_FAILED_SAVE_ITEM_ERROR500') ?>",
			"CK_FAILED_SAVE_ITEM": "<?php echo JText::_('CK_FAILED_SAVE_ITEM') ?>",
			"CK_FAILED_TRASH_ITEM": "<?php echo JText::_('CK_FAILED_TRASH_ITEM') ?>",
			"CK_FAILED_CREATE_ITEM": "<?php echo JText::_('CK_FAILED_CREATE_ITEM') ?>",
			"CK_UNABLE_UNPUBLISH_HOME": "<?php echo JText::_('CK_UNABLE_UNPUBLISH_HOME') ?>",
			"CK_TITLE_NOT_UPDATED": "<?php echo JText::_('CK_TITLE_NOT_UPDATED') ?>",
			"CK_LEVEL_NOT_UPDATED": "<?php echo JText::_('CK_LEVEL_NOT_UPDATED') ?>",
			"CK_SAVE_LEVEL_FAILED": "<?php echo JText::_('CK_SAVE_LEVEL_FAILED') ?>",
			"CK_SAVE_ORDER_FAILED": "<?php echo JText::_('CK_SAVE_ORDER_FAILED') ?>",
			"CK_CHECKIN_NOT_UPDATED": "<?php echo JText::_('CK_CHECKIN_NOT_UPDATED') ?>",
			"CK_CHECKIN_FAILED": "<?php echo JText::_('CK_CHECKIN_FAILED') ?>",
			"CK_PARAM_NOT_UPDATED": "<?php echo JText::_('CK_PARAM_NOT_UPDATED') ?>",
			"CK_PARAM_UPDATE_FAILED": "<?php echo JText::_('CK_PARAM_UPDATE_FAILED') ?>",
			"CK_FIRST_CREATE_ROW": "<?php echo JText::_('CK_FIRST_CREATE_ROW') ?>",
			"CK_NO_COLUMN_ON_ROOT_ITEM": "<?php echo JText::_('CK_NO_COLUMN_ON_ROOT_ITEM') ?>",
			"CK_SAVE": "<?php echo JText::_('JSAVE') ?>",
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