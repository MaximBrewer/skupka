<?php/*------------------------------------------------------------------------* Color Swatch Plugin for Virtuemart* author    CMSMart Team* copyright Copyright (C) 2012 Cmsmart Team. All Rights Reserved.* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL* Websites: http://cmsmart.net* Email: team@cmsmart.net* Technical Support:  Forum - http://cmsmart.net/forum* version 2.0.0-------------------------------------------------------------------------*/if(!class_exists('vRequest')) require(dirname(dirname(dirname(dirname(__DIR__)))).'/administrator/components/com_virtuemart/helpers/vrequest.php');$path = vRequest::getString('path');
$type = vRequest::getVar('type');
$cid = vRequest::getInt('cid');$pathList = dirname(dirname(dirname(dirname(__DIR__)))).'/images/stories/virtuemart/color_swatch/images/'.$cid;
foreach ($_FILES["images"]["error"] as $key => $error) {
    if ($error == UPLOAD_ERR_OK) {
        $name = $_FILES["images"]["name"][$key];				
        if($type)		{			foreach(glob($pathList.'/*.*') as $filename){				if(strpos($filename, 'thumbnail_')){					unlink($filename);				}			}
       		 move_uploaded_file( $_FILES["images"]["tmp_name"][$key], "../../../".$path."/" .$type."_".$_FILES['images']['name'][$key]);		}
        else 		{
        	move_uploaded_file( $_FILES["images"]["tmp_name"][$key], "../../../".$path."/" .$_FILES['images']['name'][$key]);			//unset($_FILES['images'][0])		}
    }
}
echo "<span>Successfully Uploaded Images !</span><span id='resultthum".$cid."' style='display: none'>".$type.'_'.$_FILES['images']['name'][$key]."</span><br>";
if ($type){
	return $type.$_FILES['images']['name'][$key];
}