<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

$menuRenderer = new ShlMvcLayout_File('com_sh404sef.submenus.menu', sh404SEF_LAYOUTS);
?>
<div id="shl-main-menu">
	<div id="shl-main-menu-coll-container" class="shl-navbar">
		<a class="btn btn-navbar" data-target="#sh404-main-menu-collapsible" data-toggle="collapse">
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</a>
		<div id="sh404-main-menu-collapsible" class="collapse" style="clear:both;">
		  <?php echo $menuRenderer->render($displayData); ?>
		</div>
	</div>
	<div id="shl-main-menu-full">
		<?php echo $menuRenderer->render($displayData); ?>
	</div>
</div>
