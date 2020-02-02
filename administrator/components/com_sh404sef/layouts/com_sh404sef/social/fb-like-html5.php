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

/**
 * Input:
 *
 * $displayData['fbLayout']
 * $displayData['url']
 * $displayData['fbAction']
 * $displayData['fbWidth']
 * $displayData['fbShowFaces']
 * $displayData['fbColorscheme']
 */
// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC')) die('Direct Access to this location is not allowed.');

?>
<!-- HTML5 Facebook like button -->
<div class="fb-like" data-href="<?php echo $displayData['url']; ?>"
     data-action="<?php echo $displayData['fbAction']; ?>" data-width="<?php echo $displayData['fbWidth']; ?>"
     data-layout="<?php echo $displayData['fbLayout']; ?>" data-show-faces="<?php echo $displayData['fbShowFaces']; ?>"
     data-colorscheme="<?php echo $displayData['fbColorscheme']; ?>">
</div>
<!-- End of HTML5 Facebook like button -->
