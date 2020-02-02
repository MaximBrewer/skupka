<?php
/*------------------------------------------------------------------------
# com_universal_ajaxlivesearch - Universal AJAX Live Search
# ------------------------------------------------------------------------
# author    Janos Biro
# copyright Copyright (C) 2011 Offlajn.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.offlajn.com
-------------------------------------------------------------------------*/
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$version = "";
$link = "";
if(version_compare(JVERSION,'1.6.0','ge')) {
  $version = "25";
  $link = "index.php?option=com_modules&filter_module=mod_universal_ajaxlivesearch";
} else {
  $version = "15";
  $link = "index.php?option=com_modules&filter_type=mod_universal_ajaxlivesearch";
}

echo '
<div class="live_search_info">
<h3>Component installed successfully!</h3>
You can find the Universal AJAX Live Search settings in the <a href="'.$link.'">module manager</a>.
Here you can download the Improved Search Plugins for your site. Just click on the download button, and install it.</div>';

$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root()."/administrator/components/com_universal_ajax_live_search/assets/style.css");

JToolBarHelper::title('Universal Ajax Live Search');

print("<iframe src=\"http://offlajn.com/index.php?option=com_offlajnsearchplugins&tmpl=component&jversion=".$version."\" frameborder=\"0\" height=\"600px\" width=\"100%\"></iframe> ");
?>