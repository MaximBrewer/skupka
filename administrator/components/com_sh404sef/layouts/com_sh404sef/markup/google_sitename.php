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
 * $displayData['sitename']
 * $displayData['alternate_sitename']
 * $displayData['url']
 */
// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC')) die('Direct Access to this location is not allowed.');

?>

<!-- Google sitename markup-->
<script type="application/ld+json">
{
  "@context" : "http://schema.org",
  "@type" : "WebSite",
  "name" : "<?php echo $this->escape(str_replace('"', "'", $displayData['sitename'])); ?>",
  <?php if (!empty($displayData['alternate_sitename'])): ?>
  "alternateName" : "<?php echo $this->escape(str_replace('"', "'", $displayData['alternate_sitename'])); ?>",
  <?php endif; ?>
  "url" : "<?php echo $this->escape($displayData['url']); ?>"
}
</script>
<!-- End of Google sitename markup-->
