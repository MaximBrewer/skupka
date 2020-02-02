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
 * $displayData['items']
 */
// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC')) die('Direct Access to this location is not allowed.');

if (empty($displayData['items']))
{
	return;
}

$itemRenderer = new ShlMvcLayout_File('com_sh404sef.markup.google_breadcrumb_item', sh404SEF_LAYOUTS);
$renderedItems = array();
foreach ($displayData['items'] as $item)
{
	$renderedItems[] = $itemRenderer->render(array('item' => $item));
}

?>

<!-- Google breadcrumb markup-->
<script type="application/ld+json">
{
  "@context" : "http://schema.org",
  "@type" : "BreadcrumbList",
  "itemListElement":
  [
  <?php echo implode(",\n", $renderedItems); ?>
  ]
}
</script>
<!-- End of Google breadcrumb markup-->
