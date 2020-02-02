<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     sh404SEF
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     4.7.3.3292
 * @date        2016-03-15
 */

/**
 * Input:
 *
 * $displayData['page']
 * $displayData['url']
 * $displayData['googlePlusPageSize']
 * $displayData['googlePlusCustomText']
 * $displayData['googlePlusCustomText2']
 *
 */
// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC')) die('Direct Access to this location is not allowed.');

?>
<div style="display: inline-block;">
	<?php if (!empty($displayData['googlePlusCustomText'])) : ?>
		<span style="float: left; font: bold 13px/16px arial,sans-serif; margin-right: 4px; margin-top: 7px;">
		<?php echo htmlspecialchars($displayData['googlePlusCustomText'], ENT_COMPAT, 'UTF-8'); ?>
		</span>
		<span style="float: left; font: 13px/16px arial,sans-serif; margin-right: 11px;margin-top: 7px;">
		<?php echo htmlspecialchars($displayData['googlePlusCustomText2'], ENT_COMPAT, 'UTF-8'); ?>
		</span>
	<?php endif; ?>
	<div style="float: left;"><img src="https://ssl.gstatic.com/images/icons/gplus-32.png" width="32" height="32"
	                               style="border: 0;"/></div>
	<div style="clear: both"></div>
</div>
