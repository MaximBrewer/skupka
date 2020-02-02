 <?php
defined('_JEXEC') or die('Restricted access');
if (($this->error->getCode()) == '404') {
  header($_SERVER['SERVER_PROTOCOL'] .' 404 Not Found');
?>
<meta http-equiv="refresh" content=" 0; url=/oshibka-404-stranitsa-ne-najdena.html">
<?php
  exit();
}