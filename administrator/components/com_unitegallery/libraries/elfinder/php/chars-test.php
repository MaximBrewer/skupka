<?php
/**
 * @package Unite Gallery
 * @author UniteCMS.net / Valiano
 * @copyright (C) 2012 Unite CMS, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('_JEXEC') or die('Restricted access');


setlocale(LC_ALL, 'en_US.UTF-8');

header('Content-Type: text/html; charset=utf-8');
echo 'Ð±ÐµÐ»Ð°Ñ� Ñ�Ð¾Ð±Ð°ÐºÐ°';
echo strToLower('Ð±ÐµÐ»Ð°Ñ� Ñ�Ð¾Ð±Ð°ÐºÐ°');

exit();
$orig = 'Ñ‘Ð�Ð¹Ð™Ã˜Ã…Å»';
echo $orig.'<br>';
$path = '../../../files/tmp/';

if (!touch($path.$orig)) {
	exit('unable to create file');
}


$orig = str_replace('"', '', json_encode($orig));
echo "original: ".$orig.'<br>';

$origParts = explode('\\', $orig);
array_shift($origParts);

//                     Ð¹                 Ñ‘              Ð™               Ð�              Ã˜         Ã…
$patterns = array("\u0438\u0306", "\u0435\u0308", "\u0418\u0306", "\u0415\u0308", "\u00d8A", "\u030a");
$replace  = array("\u0439",        "\u0451",       "\u0419",       "\u0401",       "\u00d8", "\u00c5");


foreach(scandir($path) as $f) {
	if ($f != '.' && $f != '..' && substr($f, 0, 1) != '.') {
		
		// echo mb_detect_encoding($f);
		
		$name = str_replace('"', '', json_encode($f));
		echo "before replace: $name<br>";
		// $name = str_replace($patterns, $replace, $name);
		// echo "after replace: $name<br>";
		break;
	}
}


$parts = explode('\\', $name);
array_shift($parts);


$diff = array_diff($parts, $origParts);

if (count($diff)) {
	echo "Following symbols not found in original";
	echo '<pre>';
	print_r($diff);
	echo '</pre>';
} else {
	echo "OK";
}




?>