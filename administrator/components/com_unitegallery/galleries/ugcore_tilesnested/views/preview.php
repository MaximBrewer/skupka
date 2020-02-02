<?php


defined('_JEXEC') or die('Restricted access');

	
	$output = new UGTilesNestedOutput();
	echo $output->putGallery(GlobalsUGGallery::$galleryID);

?>