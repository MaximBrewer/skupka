<?php
$type = vRequest::getVar('type');
$cid = vRequest::getInt('cid');
foreach ($_FILES["images"]["error"] as $key => $error) {
    if ($error == UPLOAD_ERR_OK) {
        $name = $_FILES["images"]["name"][$key];
        if($type)
       		 move_uploaded_file( $_FILES["images"]["tmp_name"][$key], "../../../".$path."/" .$type."_".$_FILES['images']['name'][$key]);
        else 
        	move_uploaded_file( $_FILES["images"]["tmp_name"][$key], "../../../".$path."/" .$_FILES['images']['name'][$key]);
    }
}
echo "<span>Successfully Uploaded Images !</span><span id='resultthum".$cid."' style='display: none'>".$type.'_'.$_FILES['images']['name'][$key]."</span><br>";
if ($type){
	return $type.$_FILES['images']['name'][$key];
}