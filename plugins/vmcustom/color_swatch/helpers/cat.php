<?php
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