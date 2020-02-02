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

// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC')) die('Direct Access to this location is not allowed.');

?>

<table class="adminlist">
  <tr>
    <td><?php include( $this->readmeFilename ); ?>
    </td>
  </tr>
</table>

<form method="post" name="adminForm" id="adminForm">
    <input type="hidden" name="c" value="default" />
    <input type="hidden" name="view" value="default" />
    <input type="hidden" name="option" value="com_sh404sef" />
    <input type="hidden" name="task" value="" />
</form>

<div class="sh404sef-footer-container">
	<?php echo $this->footerText; ?>
</div>
