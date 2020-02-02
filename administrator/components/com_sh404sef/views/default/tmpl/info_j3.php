<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author       Yannick Gaultier
 * @copyright    (c) Yannick Gaultier - Weeblr llc - 2016
 * @package      sh404SEF
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version      4.7.3.3292
 * @date        2016-03-15
 */

// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC'))
{
	die('Direct Access to this location is not allowed.');
}

?>

<div class="shl-main-content wbl-theme-default">

	<div id="shl-sidebar-container" class="span2 shl-no-margin">
		<?php echo $this->sidebar; ?>
	</div>

	<div class="span10">
		<?php
		include($this->readmeFilename);
		?>
	</div>
</div>

<div class="sh404sef-footer-container">
	<?php echo $this->footerText; ?>
</div>
  
