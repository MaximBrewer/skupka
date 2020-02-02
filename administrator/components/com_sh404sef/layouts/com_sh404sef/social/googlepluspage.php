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
<!-- Google+ Page button -->
<a class="google-page-badge"
   onclick="_sh404sefSocialTrack.GPageTracking('<?php echo $displayData['page']; ?>', '<?php echo $displayData['url']; ?>')"
   href="https://plus.google.com/<?php echo $displayData['page']; ?>/?prsrc=3">
	<?php echo ShlMvcLayout_Helper::render('com_sh404sef.social.googlepluspage_' . $displayData['googlePlusPageSize'], $displayData); ?>
</a>
<!-- End of Google+ Page button -->
