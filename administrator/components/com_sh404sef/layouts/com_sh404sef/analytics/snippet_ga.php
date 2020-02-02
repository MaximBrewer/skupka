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
 * $displayData['custom_vars'][] = {name:string, value: string}
 * $displayData['custom_url']
 */
// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC')) die('Direct Access to this location is not allowed.');

?>

<!-- Google Analytics Classic snippet -->
<script type='text/javascript'>
  var sh404SEFAnalyticsType = sh404SEFAnalyticsType || [];
  sh404SEFAnalyticsType.classic = true;
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', '<?php echo $displayData['tracking_code']; ?>']);
  <?php 
    if(!empty($displayData['custom_vars'])) :
      foreach($displayData['custom_vars'] as $index => $customVar) :
      	echo "\n  _gaq.push(['_setCustomVar', " . $index . ", '" . htmlentities($customVar->name, ENT_QUOTES, 'UTF-8') . "', '"
      		. htmlentities($customVar->value, ENT_QUOTES, 'UTF-8') . "', 3]);";
	  endforeach;
	  echo "\n";
    endif;
    if(!empty($displayData['anonymize'])) :
  ?>
  _gaq.push (['_gat._anonymizeIp']);
  <?php 
    endif;
  ?>  
  _gaq.push(['_trackPageview'<?php echo empty($displayData['custom_url']) ? '' : ",'" . $displayData['custom_url'] . "'"; ?>]);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
<!-- End of Google Analytics Classic snippet -->
