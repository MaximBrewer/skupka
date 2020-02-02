<?php
/*
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* GeoLocator is free software released under GNU/GPL  This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
* This php file was created by www.rupostel.com team
*
*/
 defined( '_JEXEC' ) or die( 'Restricted access' );
 $document = JFactory::getDocument();
 $document->addScript(JURI::root(true).'/administrator/components/com_geolocator/views/default/tmpl/ajax.js' );
 $firstrun = JRequest::getVar('firstrun', ''); 
 $document->addStyleDeclaration('
  div#toolbar-box, div.subhead, div.container-logo {
   display: none;
   }
   
   
 ');
?>
<div>
<h1>GeoLocator for Joomla</h1>
<p>This product includes GeoLite data created by MaxMind, available from <a href="http://maxmind.com/">http://maxmind.com</a>. This installer is distributed under GPLv2.</p>
<p>This Geo data installer was provided by <a href="http://www.rupostel.com">RuposTel.com</a> team.</p>
<p>and is now supported by <a href="http://extensions.virtuemart.net">iStraxx UG</a>.</p>
<p>For support of this extension please visit <a href="http://forum.virtuemart.net">VirtueMart Forum</a>. For support when used with RuposTel.com extensions please use forum at www.rupostel.com</p>
<?php
 include_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_geolocator'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'helper.php');
 include_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_geolocator'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'default.php');
$m = new DefaultModelDefault();
$m->createTable();
 $c = geoHelper::getCountry();

 if (!empty($c))
 echo '<p style="color: green;">Data already installed! You are from '.$c.'. Data installed properly.</p>';
?>
</div>
<script type="text/javascript">
/* <![CDATA[ */
  var from = parseInt('0'); 
  var to = parseInt('0'); 
  var op_ajaxurl = '<?php echo JURI::base(true).'/index.php'; ?>';
  var step = parseInt('0');
  var myTimer = null;
  var rows = parseInt('0');
/* ]]> */
</script>

  <?php 
   if (!empty($firstrun)) 
    {
	 
	$js = 'if(window.addEventListener){ // Mozilla, Netscape, Firefox
    window.addEventListener("load", function(){ op_runAjax(0,0,0); }, false);
	} else { // IE
    window.attachEvent("onload", function(){ op_runAjax(0,0,0); });
	}'; 
	$document->addScriptDeclaration($js);
	}
	
  ?>

<input type="hidden" name="option" value="com_geolocator" />
<input type="hidden" name="task" id="task" value="save" /> 
<div style="width: 100%; border: 1px solid black; height: 30px; height: 100%; display: none;" id="geo_progress">
 <div style="width: 1%; background-color: blue; overflow: visible; height: 100%;" id="geo_status_bar" >&nbsp;</div>
</div>
<div id="current_status" style="width: 100%;">&nbsp;</div>
<?php

// for later intenaration

echo '<fieldset><legend>GeoLocator Installation and Update</legend>'; 
?>
<label for="download_url">
Install from:</label>
<input type="text" style="width: 400px;" value="http://geolite.maxmind.com/download/geoip/database/GeoIPCountryCSV.zip" name="geourl" id="download_url" />
<br style="clear: both;"/>
<label for="localf">
OR Install from localfile in <?php echo JPATH_ROOT.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'GeoIPCountryCSV.zip' ?>
</label>
<input type="checkbox" name="localfile" id="localf" <?php 
if (file_exists(JPATH_ROOT.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'GeoIPCountryCSV.zip')) echo ' checked="checked" ';
?>/><br />
<label for="nrows">
Number of rows to insert at once: 
</label>
<input type="text" value="1000" name="rows" id="nrows" />
</fieldset>
<button onclick="op_runAjax(0,0,0);">
<?php echo JText::_( 'Install' ); ?>
</button>

