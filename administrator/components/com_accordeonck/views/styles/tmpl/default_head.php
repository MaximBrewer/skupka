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
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr-fr" lang="fr-fr" dir="ltr">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>Accordeon Menu CK - Edition Area</title>
<link href="<?php echo JUri::base(true) ?>/components/com_accordeonck/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon" />
<link rel="stylesheet" href="<?php echo JUri::base(true) ?>/components/com_accordeonck/assets/template.css" type="text/css" />
<link rel="stylesheet" href="<?php echo JUri::base(true) ?>/components/com_accordeonck/assets/modal.css" type="text/css" />
<link rel="stylesheet" href="<?php echo JUri::base(true) ?>/components/com_accordeonck/assets/accordeonck.css" type="text/css" />
<link rel="stylesheet" href="<?php echo JUri::root(true) ?>/modules/mod_accordeonck/assets/font-awesome.min.css" type="text/css" />
<link rel="stylesheet" href="<?php echo JUri::root(true) ?>/administrator/components/com_accordeonck/assets/ckbox.css" type="text/css" />
<script type="text/javascript">
	var URIROOT = "<?php echo JUri::root(true); ?>";
	var URIBASE = "<?php echo JUri::base(true); ?>";
</script>
<script src="<?php echo JUri::base(true) ?>/components/com_accordeonck/assets/jquery.min.js" type="text/javascript"></script>
<script src="<?php echo JUri::base(true) ?>/components/com_accordeonck/assets/jquery-noconflict.js" type="text/javascript"></script>
<script src="<?php echo JUri::base(true) ?>/components/com_accordeonck/assets/jquery-migrate.min.js" type="text/javascript"></script>
<script src="<?php echo JUri::base(true) ?>/components/com_accordeonck/assets/jquery-ui-1.10.2.custom.min.js" type="text/javascript"></script>
<script src="<?php echo JUri::base(true) ?>/components/com_accordeonck/assets/bootstrap.min.js" type="text/javascript"></script>
<script src="<?php echo JUri::base(true) ?>/components/com_accordeonck/assets/mootools-core.js" type="text/javascript"></script>
<script src="<?php echo JUri::base(true) ?>/components/com_accordeonck/assets/core.js" type="text/javascript"></script>
<script src="<?php echo JUri::base(true) ?>/components/com_accordeonck/assets/mootools-more.js" type="text/javascript"></script>
<script src="<?php echo JUri::base(true) ?>/components/com_accordeonck/assets/modal.js" type="text/javascript"></script>
<script src="<?php echo JUri::base(true) ?>/components/com_accordeonck/assets/jscolor/jscolor.js" type="text/javascript"></script>
<script src="<?php echo JUri::base(true) ?>/components/com_accordeonck/assets/accordeonck.js" type="text/javascript"></script>
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

	jQuery(document).ready(function()
	{
		jQuery('.hasTip').tooltip({"html": true,"container": "body"});
		 window.setInterval("keepAlive()", 600000);
	});

	function jModalClose() {
		SqueezeBox.close();
	}

	(function() {
		var strings = {
			"CK_FAILED": "<?php echo JText::_('CK_FAILED') ?>",
			"CK_PUSHDOWN_PREVIEW_NOT_AVAILABLE": "<?php echo JText::_('CK_PUSHDOWN_PREVIEW_NOT_AVAILABLE') ?>",
			"CK_IS_NOT_GOOGLE_FONT": "<?php echo JText::_('CK_IS_NOT_GOOGLE_FONT') ?>"
			};
		if (typeof Joomla == 'undefined') {
			Joomla = {};
			Joomla.JText = strings;
		}
		else {
			Joomla.JText.load(strings);
		}
	})();
</script>
</head>
<body>