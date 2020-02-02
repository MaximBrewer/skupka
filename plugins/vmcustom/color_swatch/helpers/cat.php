<?php/*------------------------------------------------------------------------* Color Swatch Plugin for Virtuemart* author    CMSMart Team* copyright Copyright (C) 2012 Cmsmart Team. All Rights Reserved.* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL* Websites: http://cmsmart.net* Email: team@cmsmart.net* Technical Support:  Forum - http://cmsmart.net/forum* version 2.0.0-------------------------------------------------------------------------*/defined('_JEXEC') or die();if(!class_exists('vRequest')) require(dirname(dirname(dirname(dirname(__DIR__)))).'/administrator/components/com_virtuemart/helpers/vrequest.php');
$path = vRequest::getInt('path');;
$pathList = dirname(dirname(dirname(dirname(__DIR__)))).'/images/stories/virtuemart/color_swatch/images/'.$path;
$images = list_files($pathList);
function list_files($directory = '.')
{
	$array = array();
	if ($directory != '.')
	{
		$directory = rtrim($directory, '/') . '/';
	}
	if ($handle = opendir($directory))
	{
		while (false !== ($file = readdir($handle)))
		{
			if ($file != '.' && $file != '..')
			{
				$subfile = substr($file, 0, 6);
				if($subfile != 'thumbn')
					array_push($array, $file);
			}
		}
		closedir($handle);
	}
	return $array;
}
echo "images/stories/virtuemart/color_swatch/images/".$path."/".$images[0];