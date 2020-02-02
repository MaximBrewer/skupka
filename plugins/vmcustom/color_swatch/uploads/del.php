<?php/*------------------------------------------------------------------------* Color Swatch Plugin for Virtuemart* author    CMSMart Team* copyright Copyright (C) 2012 Cmsmart Team. All Rights Reserved.* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL* Websites: http://cmsmart.net* Email: team@cmsmart.net* Technical Support:  Forum - http://cmsmart.net/forum* version 2.0.0-------------------------------------------------------------------------*/if(!class_exists('vRequest')) require(dirname(dirname(dirname(dirname(__DIR__)))).'/administrator/components/com_virtuemart/helpers/vrequest.php');
$path = vRequest::getString('path');
        $imagefile = $_REQUEST['file'];
        $imagefileend = "../../../".$path."/".$imagefile;
        unlink($imagefileend);				$imagefilethumb = "../../../".$path."/".$_REQUEST['folder']."/".$imagefile;		unlink($imagefilethumb);
?>