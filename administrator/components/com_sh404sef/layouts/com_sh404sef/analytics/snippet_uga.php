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
 * $displayData['tracking_code']
 * $displayData['custom_domain']
 * $displayData['custom_url']
 */
// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC')) die('Direct Access to this location is not allowed.');

?>
<!-- Google Analytics Universal snippet -->
<script type='text/javascript'>
	var sh404SEFAnalyticsType = sh404SEFAnalyticsType || [];
	sh404SEFAnalyticsType.universal = true;

	(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  	(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  	m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  	})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
<?php if(!empty($displayData['anonymize'])) : ?>
	ga('set', 'anonymizeIp', true);
<?php endif; ?>  
	ga('create', '<?php echo $displayData['tracking_code']; ?>'<?php echo empty($displayData['custom_domain']) ? '' : ",'" . $displayData['custom_domain'] . "'" ?>);
<?php if(!empty($displayData['enable_display_features'])): ?>
	ga('require', 'displayfeatures');
<?php endif; ?>
<?php if(!empty($displayData['enable_enhanced_link_attr'])): ?>
	ga('require', 'linkid');
<?php endif; ?>
	ga('send', 'pageview'<?php echo empty($displayData['custom_url']) ? '' : ",'" . $displayData['custom_url'] . "'" ?>);
</script>
<!-- End of Google Analytics Universal snippet -->
