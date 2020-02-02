<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     sh404SEF
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     4.7.3.3292
 * @date		2016-03-15
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<jdoc:include type="head" />
	<link rel="stylesheet" href="<?php echo JURI::base( true); ?>/templates/system/css/offline.css" type="text/css" />
	<?php if($this->direction == 'rtl') : ?>
	<link rel="stylesheet" href="<?php echo JURI::base( true); ?>/templates/system/css/offline_rtl.css" type="text/css" />
	<?php endif; ?>
	<link rel="stylesheet" href="<?php echo JURI::base( true); ?>/templates/system/css/system.css" type="text/css" />
</head>
<body>
<jdoc:include type="message" />
	<div id="frame" class="outline">
		<h1>
			<?php echo $app->getCfg('sitename'); ?>
		</h1>
	<p>
		<?php echo $app->getCfg('offline_message'); ?>
	</p>
	</div>
</body>
</html>
