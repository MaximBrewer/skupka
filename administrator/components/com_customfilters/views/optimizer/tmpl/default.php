<?php
/**
 * @package 	customfilters
 * @author		Sakis Terz
 * @link		http://breakdesigns.net
 * @copyright	Copyright (c) 2012-2018 breakdesigns.net. All rights reserved.
 * @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *				customfilters is free software. This version may have been modified
 *				pursuant to the GNU General Public License, and as distributed
 *				it includes or is derivative of works licensed under the GNU
 *				General Public License or other free or open source software
 *				licenses.
 * @since		1.9.5
 */

// no direct access
defined('_JEXEC') or die;
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('behavior.modal');
?>

<form action="administrator?index.php" method="post" name="adminForm" id="cfOptimizerForm">
<div id="optimizer_results"></div>
<input type="submit" class="btn" value="<?php echo JText::_('COM_CUSTOMFILTERS_CHECKNOPTIMIZE');?>" id="cf_optimizebtn"/>
<input type="hidden" name="option" value="com_customfilters" />
<input type="hidden" name="task" value="optimizer.optimize" />
<input type="hidden" name="format" value="json" />
<?php echo JHtml::_('form.token'); ?>
</form>