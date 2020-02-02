<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

?>
<div id="sidebar">
	<div class="sidebar-nav">
		<?php
		if ($displayData->displayMenu) :
			echo ShlMvcLayout_Helper::render('com_sh404sef.submenus.menu_layout', $displayData);
		endif;

		if ($displayData->displayFilters) :
			echo ShlMvcLayout_Helper::render('com_sh404sef.submenus.filters', $displayData->filters);
		endif; ?>
	</div>
</div>
