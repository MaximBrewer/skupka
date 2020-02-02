<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<?php
header("HTTP/1.1 404 Not Found");
echo file_get_contents(JURI::root().'/oshibka-404-stranitsa-ne-najdena.html');
?>