<?php
$path = vRequest::getString('path');
        $imagefile = $_REQUEST['file'];
        $imagefileend = "../../../".$path."/".$imagefile;
        unlink($imagefileend);
?>