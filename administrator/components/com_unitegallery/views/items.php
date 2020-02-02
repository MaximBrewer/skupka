<?php

$isGalleryPage = GlobalsUGGallery::$isInited;

$headerTitle = __("Items", UNITEGALLERY_TEXTDOMAIN);

//set gallery related items
if($isGalleryPage == true){
	$galleryTitle = GlobalsUGGallery::$gallery->getTitle();
	$headerTitle = $galleryTitle ." - ". __("[images]", UNITEGALLERY_TEXTDOMAIN);
}


require HelperGalleryUG::getPathHelperTemplate("header"); 

//put gallery tabs

if($isGalleryPage == true){
	$selectedGalleryTab = "items";
	require HelperGalleryUG::getPathHelperTemplate("gallery_edit_tabs");
}


$manager = new UniteGalleryManagerMain();
$manager->outputHtml();

require GlobalsUG::$pathViews."system/video_dialog.php";
