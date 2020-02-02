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
 * $displayData['url']
 * $displayData['target']
 */
// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC')) die('Direct Access to this location is not allowed.');
?>

<!-- Google sitelinks search markup-->
<script type="application/ld+json">
{
  "@context": "http://schema.org",
  "@type": "WebSite",
  "url" : "<?php echo $this->escape($displayData['url']); ?>",
  "potentialAction": {
    "@type": "SearchAction",
    "target": "<?php echo $this->escape($displayData['target']); ?>",
    "query-input": "required name=search_term_string"
  }
}
</script>
<!-- End of Google sitelinks search markup-->
