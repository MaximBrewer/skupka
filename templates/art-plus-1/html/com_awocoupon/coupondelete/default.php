<?php
/*OPC fix for Awo Coupons display */
defined('_JEXEC') or die( 'Restricted access' );
if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'com_awocoupon'.DIRECTORY_SEPARATOR.'coupondelete'.DIRECTORY_SEPARATOR.'default.php')) {
	include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'com_awocoupon'.DIRECTORY_SEPARATOR.'coupondelete'.DIRECTORY_SEPARATOR.'default.php'); 
}
else {
	include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_awocoupon'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'coupondelete'.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR.'default.php'); 
}
