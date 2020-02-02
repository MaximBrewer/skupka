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
 * $displayData['item']
 */
// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC')) die('Direct Access to this location is not allowed.');

if (empty($displayData['item']))
{
	return;
}

?>
	{
	  "@type": "ListItem",
	  "position": <?php echo $displayData['item']['position']; ?>,
	  "item":
	  {
	    "@id": "<?php echo empty($displayData['item']['id']) ? '' : Sh404sefHelperUrl::routedToAbs(JRoute::_($displayData['item']['id'])); ?>",
	    "name": "<?php echo $this->escape(str_replace('"', "'", $displayData['item']['name'])); ?>"
	  }
	}
