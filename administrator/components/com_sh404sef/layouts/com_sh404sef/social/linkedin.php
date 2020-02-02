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
 * $displayData['layout']
 * $displayData['loadScript']
 * $displayData['url']
 * $displayData['languageTag']
 */
// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC')) die('Direct Access to this location is not allowed.');

$layout = empty($displayData['layout']) || $displayData['layout'] == 'none' ? '' : 'data-counter="' . $displayData['layout'] . '"';
?>
<!-- LinkedIn share button -->
<?php if (!empty($displayData['loadScript'])) : ?>
	<script src="//platform.linkedin.com/in.js"
	        type="text/javascript">lang: <?php echo $displayData['languageTag']; ?></script>
<?php endif; ?>
<script type="IN/Share" data-url="<?php echo $displayData['url']; ?>" <?php echo $layout; ?>></script>
<!-- End of LinkedIn share button -->
