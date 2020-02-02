<?php
/*
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/

if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 

class OPCimage {
    public static function op_image_info_array($image, $args="", $resize=1, $path_appendix='product', $thumb_width=0, $thumb_height=0, $file_dimensions=array())
	{
	 return OPCimage::op_image_tag($image, $args, $resize, $path_appendix, $thumb_width, $thumb_height, true,  $file_dimensions);
	}
	
	public static function op_image_tag($image, $args="", $resize=1, $path_appendix='product', $thumb_width=0, $thumb_height=0, $retA = false, $file_dimensions = array() ) 
	{
	
	
	
	if (!function_exists('ImageCreateTrueColor'))
	if ($retA===true)
					{
					  return array(); 
					
					 
					}
					else
					{
					  return "&nbsp;"; 
					}
	
	
	
	$oi = $image; 
	if (empty($image)) 
	 {
		  $image = VmConfig::get('vm_themeurl', JURI::root().'components/com_virtuemart/').'assets/images/vmgeneral/'.VmConfig::get('no_image_set'); 
		  
		  
	 }
	 
	if (stripos($image, 'http')===0)
	{
	     // if the image starts with http
	     $imga = array();
		 $imga['width'] = $thumb_width;
		 $imga['height'] = $thumb_height;
		 $imga['iurl'] = $image;
		 
		 
		 $root = Juri::root(); 
		 if (stripos($image, $root)===0) {
		   //to path: 
		   
		 }
		 $ip = OPCimage::urlToPath($image); 
		 
		 if (!empty($ip)) {
		   $image = $ip; 
		 }
		 else
		 {
			 if ($retA) {
				return $imga; 
			 }
			 else {
			    if (empty($image)) return "&nbsp;"; 
				return '<img src="'.$image.'" />'; 
			 }
		 }
		
	}
	else
	if (!file_exists($image) || (!is_file($image)))
	{
		
		
	  $image = VmConfig::get('vm_themeurl', JURI::root().'components/com_virtuemart/').'assets/images/vmgeneral/'.VmConfig::get('no_image_set'); 
	  $imga = array();
	  $imga['width'] = $thumb_width;
	  $imga['height'] = $thumb_height;
	  $imga['iurl'] = $image;
	  
	  
	  
	}
	
	
	
		$height = $width = 0;
		
		$ow = $thumb_width; 
		$oh = $thumb_height; 
		
		if ($image != "") {
			
			
			
			
			$fi = pathinfo($image);
			
			//
			// to resize we need to know if to keep height or width
			if (!empty($file_dimensions)) { $arr = $file_dimensions; }
			else {
			$arr = getimagesize( $image );
			}
			
			
			$width = $arr[0]; $height = $arr[1];
			
			if (empty($thumb_width) && (empty($thumb_height)))
			{
				
				$thumb_width = $width; 
				$thumb_height = $height; 
				
				$imga = array();
				$imga['width'] = $thumb_width;
				$imga['height'] = $thumb_height;
				$imga['iurl'] = OPCimage::path2url($image);
				
				
				if (!file_exists($image)) return array(); 
				
				if ($retA===true) {
					return $imga; 
				}
				else
				{
					$url = OPCimage::path2url($image);
					return '<img src="'.$url.'" />'; 
				}
			}
			
			if (empty($thumb_width) && (!empty($thumb_height)))
			{
			  $rate = $height / $thumb_height; // 1.5
			  $thumb_width = round($width / $rate);
			  // if width<height do nothing
			  //if ($width>$height && ())
			}
			else
			if (empty($thumb_height))
			{
			 $rate = $width / $thumb_width; 
			 $thumb_height = round($height / $rate); 
			}
			else
			if (empty($thumb_height) && (empty($thumb_width)))
			{
			  $thumb_height = $height;
			  $thumb_width = $width;
			}
			
			// check ratio: 
			$r1 = round($thumb_height / $thumb_width, 3); 
			$r2 = round($height / $width, 3); 
			
			
			
			$dt = abs($r2-$r1); 
			
			if (($r1 != $r2) && ($dt > 0.01))
			 {
			   // the ratio got changed
			   $thumb_height = $thumb_height * $r2; 
			   if ($thumb_height > $oh)
			   {
			   // reverse
			   $thumb_height = $thumb_height / $r2; 
			   $thumb_width = $thumb_width / $r2; 
			   }
			   //$thumb_width = $thumb_width * $r2; 
			 }
			
			if (!empty($fi['extension']))
			{
			$basename = str_replace('.'.$fi['extension'], '', $fi['basename']); 
			$u = VmConfig::get('media_product_path', 'images/stories/virtuemart/product/'); 
			$u = str_replace('/', DS, $u); 
			
			$filename = JPATH_SITE.DIRECTORY_SEPARATOR.$u.$ow.'x'.$oh.DIRECTORY_SEPARATOR.$fi['basename']; 
			$dirname = JPATH_SITE.DIRECTORY_SEPARATOR.$u.$ow.'x'.$oh; 
			
			jimport( 'joomla.filesystem.file' );
			
			if (file_exists($filename)) 
			 { 
			   $arr = getimagesize( $filename );
			   if ($arr === false)
			    {
				
				  // we've got a corrupted image here
				  JFile::delete($filename); 
				}
			 }

			if (($width > $thumb_width) || ($height > $thumb_height) || (!(file_exists($filename))))
			 {
			 
			   if (!file_exists($dirname)) 
			    {
				 				  jimport( 'joomla.filesystem.folder' );
				  jimport( 'joomla.filesystem.file' );
				  
				  if (@JFolder::create($dirname)===false)
				   {
				 
				     // we can't create a directory and we don't want to get into a loop
				     return "&nbsp;"; 
				   }
				  $x = ' '; 
				   if (@JFile::write($dirname.DIRECTORY_SEPARATOR.'index.html', $x)===false)
				   {
				     // we can't create a directory and we don't want to get into a loop
				     return "&nbsp;"; 
				   }

				}


				
				if (file_exists($dirname) && (!file_exists($filename)))
				{
				
				
				$ret = OPCimage::resizeImg($image, $filename, $thumb_width, $thumb_height, $width, $height); 
			
				if ($ret === false) {
					if (!empty($oi))
					return OPCimage::op_image_tag("", $args, 0, 'product', $thumb_width, $thumb_height, $retA);
					else 
					if ($retA===true)
					{
					  return array(); 
					  return "&nbsp;"; 
					 
					}	
				
				}
				
				
			    $arr = @getimagesize( $filename );
				if ($arr === false) return array(); 
			    $width = $arr[0]; $height = $arr[1];
				
				}
				else
				if (file_exists($dirname) && (file_exists($filename)))
				{
					
					
					 $arr = @getimagesize( $filename );
					if ($arr === false) return array(); 
					$width = $arr[0]; $height = $arr[1];
				}
				else
				{


					if (!empty($oi))
					return OPCimage::op_image_tag("", $args, 0, 'product', $thumb_width, $thumb_height, $retA);
					else 
					if ($retA===true)
					{
					  return array(); 
					  return "&nbsp;"; 
					 
					}
					else
					{
					
					}

				}
				
			   // we need to create it
			   // should be here:
			   //
			   
			 }
			}

			

		}
		
		if (empty($filename)) {
			if ($retA===true) {
			 $imga = array();
			  return $imga; 
			}
			else {
				return ''; 
			}
		
		}
		
		if ($retA===true)
		{
		 
		 if (!file_exists($filename)) return array(); 
		 $imga = array();
		 $imga['width'] = $width;
		 $imga['height'] = $height;
		 $imga['iurl'] = OPCimage::path2url($filename);
		 
		 
		 return $imga;
		}
		else 
		{
			
			
		if (empty($url)) return "&nbsp;"; 
		return '<img src="'.$url.'" />'; 
		}
		//return vmCommonHTML::imageTag( $url, '', '', $height, $width, '', '', $args.' '.$border );

	}
	
	
	
	public static function checkRam($imageInfo)
	{
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
		if (!isset($imageInfo[0])) $imageInfo[0] = 1; 
		if (!isset($imageInfo['bits'])) $imageInfo['bits'] = 32; 
		if (!isset($imageInfo[1])) $imageInfo[0] = 1; 
		if (!isset($imageInfo['channels'])) $imageInfo['channels'] = 1; 
		
		$memoryNeeded = Round(($imageInfo[0] * $imageInfo[1] * $imageInfo['bits'] * $imageInfo['channels'] / 8 + Pow(2, 16)) * 1.65);
	 if (function_exists('memory_get_usage'))
	 {
		  $ramnneeded = memory_get_usage() + $memoryNeeded; 
		  $memory_limit = OPCmini::getMemLimit(); 
		  
		 
		 if (empty($memory_limit)) return true; 
		
	
		  
		  
		  if ($ramnneeded > $memory_limit) return false; 
	 }
	 return true; 
	}
	
public static function resizeImg($orig, $new,  $new_width, $new_height, $ow, $oh)
{
	
	jimport( 'joomla.filesystem.file' );
	  $now = time(); 
	  $stamp = (int)$now; 

	  $rand = rand(9999, 100000); 
	  
 if (file_exists($new.'.tmp')) {
  $stamp = file_get_contents($new.'.tmp'); 

  if ($stamp > 0 ) 
  {
	  
	  // 2 minutes to create thumb !: 
	  if (($now - $stamp) > 120) {
	   JFile::delete($new.'.tmp'); 
	  }
	  else
	  {
		  return false; 
	  }
  }
}

 if (@JFile::write($new.'.tmp', $stamp)===false) return false; 




if (!function_exists('GetImageSize')) return false; 

// What sort of image?
$info = @GetImageSize($orig);
if(empty($info))
{
  return false;
}

if (!self::checkRam($info)) return false; 


$width = $info[0];
$height = $info[1];
$mime = $info['mime'];

$type = substr(strrchr($mime, '/'), 1);

switch ($type)
{
case 'jpeg':
    $image_create_func = 'ImageCreateFromJPEG';
    $image_save_func = 'ImageJPEG';
	$new_image_ext = 'jpg';
    break;

case 'png':
    $image_create_func = 'ImageCreateFromPNG';
    $image_save_func = 'ImagePNG';
	$new_image_ext = 'png';
    break;

case 'bmp':
    $image_create_func = 'ImageCreateFromBMP';
    $image_save_func = 'ImageBMP';
	$new_image_ext = 'bmp';
    break;

case 'gif':
    $image_create_func = 'ImageCreateFromGIF';
    $image_save_func = 'ImageGIF';
	$new_image_ext = 'gif';
    break;

case 'vnd.wap.wbmp':
    $image_create_func = 'ImageCreateFromWBMP';
    $image_save_func = 'ImageWBMP';
	$new_image_ext = 'bmp';
    break;

case 'xbm':
    $image_create_func = 'ImageCreateFromXBM';
    $image_save_func = 'ImageXBM';
	$new_image_ext = 'xbm';
    break;

default:
	$image_create_func = 'ImageCreateFromJPEG';
    $image_save_func = 'ImageJPEG';
	$new_image_ext = 'jpg';
}
	
	$new_height = round($new_height); 
	$new_height = (int)$new_height; 
	
	if (class_exists('imagick'))
	{
		try {
		$blobdata = self::resizeImageImagick($orig, $new_width, $new_height, imagick::FILTER_LANCZOS, 1, TRUE, FALSE); 
		}
		catch (Exception $e) {
		  return false; 
		}
	}
	else
	{

	// New Image
	if (!function_exists('ImageCreateTrueColor'))
	{
		return false; 
	}
	
	// check for RAM: 
	$imageInfo = $info; 
	
try {
	$image_c = @ImageCreateTrueColor($new_width, $new_height);
} catch (Exception $e) {
		  return false; 
		}
		
	if (!function_exists($image_create_func))
	{
		return false; 
	}
	
	try {
	$new_image = @$image_create_func($orig);
	} catch (Exception $e) {
		  return false; 
		}
		try {
	if($type == "gif" or $type == "png"){
		
    @imagecolortransparent($image_c, @imagecolorallocatealpha($image_c, 0, 0, 0, 127));
    @imagealphablending($image_c, false);
    @imagesavealpha($image_c, true);
	}
	
	@ImageCopyResampled($image_c, $new_image, 0, 0, 0, 0, $new_width, $new_height, $ow, $oh);
	} 
		catch (Exception $e) {
			return false; 
		}
	ob_start(); 
	try {
	$process = @$image_save_func($image_c);
	}
	catch(Exception $e) {
	 $blobdata = ob_get_clean(); 
	 return false; 
	}
	$blobdata = ob_get_clean(); 
		
	
	
	}
	
	if (!empty($blobdata))
	{
	 $rand_file = $new.'._random_'.$rand; 
	 if (JFile::write($rand_file, $blobdata)!==false) {
	   @JFile::move($rand_file, $new); 
	   @JFile::delete($new.'.tmp'); 
	 }
	}
	else
	{
		return false; 
	}
	
	
	}

	public static function getSrcImage($image, $width, $height, $type='product') {
		$img = OPCimage::op_image_info_array($image, '', 1, $type, $width, $height);
		return $img['iurl']; 
	}
	
 	public static function op_show_image(&$image, $extra, $width, $height, $type)
	{
    $showimg = VmConfig::get('oncheckout_show_images', true); 
	if (empty($showimg)) return '&nbsp;'; 
	
	
	
	if (empty($image))
	{
	  if (!empty($width)) $w = 'width: '.$width.';'; else $w = ''; 
	  if (!empty($height)) $h = 'height: '.$height.';'; else $h = ''; 
	  return '<div style="'.$w.' '.$h.' ">&nbsp;</div>';
	}
	

		$class = '';
	   $alt = ''; 
	       $img = OPCimage::op_image_info_array($image, 'class="'.$class.'" border="0" title="'.$alt.'" alt="'.$alt.'"', 1, $type, $width, $height);
           
          if (!empty($img))
		    {
			  $real_height = $img['height'];
              $real_width =  $img['width']; 
			}
			else
			{
			  $real_height = 0;
              $real_width =  0;
			  $href = ''; 
			}
		   
		   $width = (int)$width; 
		   $height = (int)$height;
		   $real_width = (int)$real_width;
		   $real_height = (int)$real_height; 
		   if (empty($width)) $width = $real_width;
		   if (empty($height)) $height = $real_height;
           $w1 = floor((abs($real_width-$width))/2);
		   
           $w2 = $width-floor((abs($real_width-$width))/2);
           
           $h1 = floor((abs($real_height-$height))/2);
           $h2 = $height-floor((abs($real_height-$height))/2);
           
           $w3 = $width-$w1;
           $ret = '<div style="height: '.$height.'px; width: '.$width.'px; ">
           <div style="float: left; width: '.$w1.'px; height: 100%;"></div>
		   <div style="float: left; width: '.$w3.'px; height: '.$h1.'px;"></div>
           <div style="float: left; width: '.$w3.'px; height: '.$h2.'px;">';
		   if (!empty($img))
		   {
           if (!empty($href)) $ret .= '<a href="'.$href.'" title="'.$alt.'">';
			$ret .= '<img src="'.$img['iurl'].'" width="'.$img['width'].'px" height="'.$img['height'].'px" />'; 
           if (!empty($href)) $ret .= '</a>';
		   }
		   else $ret .= "&nbsp;"; 
           $ret .= '
           </div>
           </div>';
           
           return $ret; 

	  
	  
	}
	//http://php.net/manual/en/imagick.resizeimage.php
	public static function resizeImageImagick($imagePath, $width, $height, $filterType, $blur, $bestFit, $cropZoom) {
		
	if (empty($imagePath) || (!is_file($imagePath))) return ''; 
    //The blur factor where &gt; 1 is blurry, &lt; 1 is sharp.
	try { 
    $imagick = new \Imagick($imagePath);

    $imagick->resizeImage($width, $height, $filterType, $blur, $bestFit);

    $cropWidth = $imagick->getImageWidth();
    $cropHeight = $imagick->getImageHeight();

    if ($cropZoom) {
        $newWidth = round($cropWidth / 2);
        $newHeight = round($cropHeight / 2);

        $imagick->cropimage(
            round($newWidth),
            round($newHeight),
            round(($cropWidth - $newWidth) / 2),
            round(($cropHeight - $newHeight) / 2)
        );

        $imagick->scaleimage(
            round($imagick->getImageWidth() * 4),
            round($imagick->getImageHeight() * 4)
        );
    }


    
    return $imagick->getImageBlob();
	}
	catch (Exception $e) {
	  
	  
	  return ''; 
	}
}

	
    public static function path2url($path)
	{
	
		$len = strlen(JPATH_SITE); 
		
		$path = substr($path, $len); 
		$path = str_replace(DIRECTORY_SEPARATOR, '/', $path); 
		
		if (substr($path, 0, 1) != '/') $path = '/'.$path; 
		
		$base = JURI::root(true);
		
		
		if (substr($base, -1)=='/') $base = substr($base, 0, -1);

		$path = $base.$path; 
		
		return $path; 
	}
	
	public static function getMediaData($id)
	{
	   if (empty($id)) return;
   if (is_array($id)) $id = reset($id);
   
   $db = JFactory::getDBO(); 
   $id = (int)$id; 
   $q = "select * from #__virtuemart_medias where virtuemart_media_id = ".$id." limit 0,1"; 
   $db->setQuery($q); 
   $res = $db->loadAssoc(); 
   
   $err = $db->getErrorMsg(); 
   
   return $res; 
	}
	
	public static function getImageFile($id, &$w=0, &$h=0)
	{
		
		
	   if (is_object($id)) {
		   $img = (array)$id; 
	   }
	   else
	   {
	    $img = OPCImage::getMediaData($id);
	   }
  
   if (!empty($img['file_url_thumb']))
    {
	
	  $th = $img['file_url_thumb']; 
	  if (!empty($w) && (!empty($h)))
	  {
	  $th2 = str_replace('/resized/', '/resized_'.$w.'x'.$h, $th); 
	  $thf = JPATH_SITE.DIRECTORY_SEPARATOR.str_replace('/', DS, $th2); 
	  if (file_exists($thf)) return $thf;
	  }
	  $thf = JPATH_SITE.DIRECTORY_SEPARATOR.str_replace('/', DS, $th); 
	  
	  $useful = false; 
	  if (file_exists($thf)) 
	  {
	  
	  $tocreate = true; 
	  
	  
		  if (function_exists('getimagesize')) {
	        $arr = getimagesize( $thf );
			if ($arr !== false) {
			$width = $arr[0]; $height = $arr[1];
			
			if ($width < $w) 
			{
				$useful = true; 
			}
			else
			if ($height < $h)
			{
				$useful = true; 
			}
			else
			{
				$w = $width; 
		        $h = $height; 
			}
			}
				
		  }
	  if (!$useful)
	  {
		  
	  return $thf;
	  }
	  }
	  if ($useful) { 
	  $imgp = JPATH_SITE.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $img['file_url_thumb']); 
	  
	  if (file_exists($imgp) && (!is_dir($imgp)))
	   {
		  $useful = false; 
		  if (function_exists('getimagesize')) {
	        $arr = getimagesize( $imgp );
			if ($arr !== false) {
			$width = $arr[0]; $height = $arr[1];
			
			if ($width < $w) 
			{
				$useful = true; 
			}
			else
			if ($height < $h)
			{
				$useful = true; 
			}
			else
			{
				$w = $width; 
		        $h = $height; 
			}
			}
				
		  }
		  if (!$useful)
		  {
			  
	      return $imgp; 
		  }
	   }
	  }
	  
	}
   
    {
	  $th = $img['file_url']; 
	 
	  if (!empty($w) && (!empty($h)))
	  {
	  $th2 = str_replace('/virtuemart/', '/virtuemart/resized_'.$w.'x'.$h, $th); 
	  $thf = JPATH_SITE.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $th2); 
	  if (file_exists($thf)) {
		  
		  return $thf;
	  }
	  }
	  $thf = JPATH_SITE.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $th); 
	  if (file_exists($thf)) 
	  	{
	    $tocreate = true; 
		if (function_exists('getimagesize')) {
		$arr = getimagesize( $thf );
		    if ($arr !== false) {
			$w = $width = $arr[0]; $h = $height = $arr[1];
			}
		}
		
		return $thf;
		}
		
	  $imgp = JPATH_SITE.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $img['file_url']); 
	  if (file_exists($imgp) && (!is_dir($imgp)))
	   {
		  if (function_exists('getimagesize')) {
		    $arr = getimagesize( $imgp );
		    if ($arr !== false) {
			$w = $width = $arr[0]; $h = $height = $arr[1];
			}
		}
		  
	      return $imgp; 
	   }
	  
	
	}
	
	if ((!empty($img['file_type']))  && (!empty($img['file_url']))) {

	$aPath = self::getAlternativePath($img['file_type']); 
	 $th = $img['file_url']; 
	 $ip = $aPath.$th; 
	 
	 
	 
	 if (file_exists($ip)) return $ip; 
	}
	
	
	
	
	}
	
	public static function getAlternativePath($type)
	{
		
		if($type == 'product' || $type == 'products'){
			$relUrl = VmConfig::get('media_product_path');
			
		}
		else if($type == 'category' || $type == 'categories'){
			$relUrl = VmConfig::get('media_category_path');
			
		}
		else if($type == 'shop'){
			$relUrl = VmConfig::get('media_path');
			
		}
		else if($type == 'vendor' || $type == 'vendors'){
			$relUrl = VmConfig::get('media_vendor_path');
			
		}
		else if($type == 'manufacturer' || $type == 'manufacturers'){
			$relUrl = VmConfig::get('media_manufacturer_path');
			
		}
		else
		{
			$relUrl = 'images/stories/virtuemart/product/'; 
		}
		
		$relUrl = str_replace('/', DIRECTORY_SEPARATOR, $relUrl); 
		
		if (substr($relUrl, -1) !== DIRECTORY_SEPARATOR) $relUrl .= DIRECTORY_SEPARATOR; 
		
		if (substr($relUrl, 0, 1)=== DIRECTORY_SEPARATOR) $relUrl = substr($relUrl, 1); 
		
		$relUrl = JPATH_SITE.DIRECTORY_SEPARATOR.$relUrl; 
		
		return $relUrl; 
	}
	
	public static function getCreateImageUrl($vmimage, $w=0, $h=0, $cdn='') {
		return self::getCreateImageUrlAndSize($vmimage, $w, $h, $cdn); 
	}
	public static function getCreateImageUrlAndSizeById($image_id, &$w=0, &$h=0, $cdn='') {
	   static $qC; 
	   $image_id = (int)$image_id; 
	   $vmImage = new stdClass(); 
	   if (!isset($qC[$image_id])) {
	    $db = JFactory::getDBO(); 
	    $q = 'select * from #__virtuemart_medias where virtuemart_media_id = '.(int)$image_id.' limit 0,1'; 
		$db->setQuery($q); 
		$res = $db->loadObject(); 
		
		if (!empty($res)) {
		   $qC[$image_id] = $vmImage = $res; 
		}
		else
		{
			$qC[$image_id] = false; 
			return ''; 
		}
		
		
	   }
	   else
	   {
		   $vmImage = $qC[$image_id]; 
	   }
	   
	   return OPCimage::getCreateImageUrlAndSize($vmImage, $w, $h, $cdn); 
	   
	}
	public static function getCreateImageUrlAndSize($vmimage, &$w=0, &$h=0, $cdn='')
	{
		if (empty($vmimage)) return ''; 
		
		$w2 = $w; 
		$h2 = $h; 
		$ifile = self::getImageFile($vmimage, $w2, $h2); 
		
		if (empty($ifile)) return ''; 
		
		
		$file_dimensions = array(); 
		$file_dimensions[0] = $w2; 
		$file_dimensions[0] = $h2; 
		
		$ret = self::op_image_info_array($ifile, '', 1, 'product', $w, $h); 
		
		if ((!isset($ret['width'])) || (empty($ret['width']))) {
			return ''; 
		}
		
		$w = $ret['width']; 
		$h = $ret['height']; 
		$url = $ret['iurl']; 
		
		
		
		if ((strlen($url)>4) && (substr($url, 0,4) !== 'http')) {
			if (!empty($cdn)) {
			  $url = self::mergeRoot($url, $cdn, true); 
			 
			}
		}
		
		
		
		return $url;
		
	}
	
	public static function mergeRoot($url, $cdn, $withProtocol=true) {
	    if (!empty($cdn)) {
		    if ((stripos($cdn, 'http') !== 0) && (stripos($cdn, '//') !== 0)) {
			   $cdn = '//'.$cdn; 
			}
		}
		$root1 = Juri::root(true); 
		
		$mysite = false; 
		
		if (substr($root1, -1) !== '/') $root1 .= '/'; 
		
		if (stripos($url, $root1)===0) {
		   $url = substr($url, strlen($root1)); 
		}
		$root2 = Juri::root(false); 
		
		if (($cdn === $root2) || ($cdn===true)) {
		  $mysite = true; 
		}
		
		if (substr($root2, -1) !== '/') $root2 .= '/'; 
		
		if (stripos($url, $root2)===0) {
		   $url = substr($url, strlen($root2)); 
		}
	    
		
		
		if (substr($cdn, -1) !== '/') $cdn .= '/'; 
		
		if ($mysite) {
			if ($withProtocol) {
				
		      return $root2.$url; 
			}
			else
			{
				return $root1.$url; 
			}
		}
		else
		{
			return $cdn.$root1.$url; 
		}
		
		return $url; 
		
	
	
	}
	
	public static  function getImageUrl($id, &$tocreate, $w=0, $h=0) 
	{
	   $img = OPCImage::getMediaData($id);
   if (!empty($img['file_url_thumb']))
    {
	  $th = $img['file_url_thumb']; 
	  $th2 = str_replace('/resized/', '/resized_'.$w.'x'.$h, $th); 
	  $thf = JPATH_SITE.DIRECTORY_SEPARATOR.str_replace('/', DS, $th2); 
	  if (file_exists($thf)) return $th2;
	  $thf = JPATH_SITE.DIRECTORY_SEPARATOR.str_replace('/', DS, $th); 
	  if (file_exists($thf)) 
	  {
	  $tocreate = true; 
	  return $th;
	  }
	}
   else
    {
	  $th = $img['file_url']; 
	  $th2 = str_replace('/virtuemart/', '/virtuemart/resized_'.$w.'x'.$h, $th); 
	  $thf = JPATH_SITE.DIRECTORY_SEPARATOR.str_replace('/', DS, $th2); 
	  if (file_exists($thf)) return $th2;
	  $thf = JPATH_SITE.DIRECTORY_SEPARATOR.str_replace('/', DS, $th); 
	  if (file_exists($thf)) 
	  	{
	    $tocreate = true; 
		return $th;
		}
	}
	}

	
	/**
	 * This function displays the image, when the image is not already a resized one,
	 * it tries to get first the resized one, or create a resized one or fallback in case
	 *
	 * @author Max Milbers
	 *
	 * @param string $imageArgs Attributes to be included in the <img> tag.
	 * @param boolean $lightbox alternative display method
	 * @param string $effect alternative lightbox display
	 * @param boolean $withDesc display the image media description
	 */
	public static function displayMediaThumb(&$vmImage, $imageArgs=array(),$lightbox=true,$effect="class='modal' rel='group'",$return = true,$withDescr = false,$absUrl = false, $width=0,$height=0){
	
	if ((!is_object($vmImage)) || (empty($vmImage))) {
		
		if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR); 
	    if (!class_exists('VmImage'))
		require(VMPATH_ADMIN . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'image.php');
	    $vmImage = new VmImage(); 
	}
	
		if((!empty($vmImage->file_class)) && (method_exists($vmImage, 'filterImageArgs'))) {
			$imageArgs = $vmImage->filterImageArgs($imageArgs);
		}
		if (empty($imageArgs)) $imageArgs = array(); 
		if (!is_array($imageArgs)) {
		 $imageArgs = array(0=>$imageArgs); 
		}
		
		if (is_array($imageArgs)) {
		if (empty($width) && (!empty($imageArgs['width']))) {
			$ww = (int)$imageArgs['width']; 
			if (!empty($ww)) $width = $ww; 
		}
		
		if (empty($height) && (!empty($imageArgs['height']))) {
			$hh = (int)$imageArgs['height']; 
			if (!empty($hh)) $height = $hh; 
		}
		}
		
		

		if(empty($vmImage->file_name)){

			if($return){
				if($vmImage->file_is_downloadable){
					$file_url = $vmImage->theme_url.'assets/images/vmgeneral/'.VmConfig::get('downloadable','zip.png');
					$file_alt = vmText::_('COM_VIRTUEMART_NO_IMAGE_SET').' '.$vmImage->file_description;
					
					
					return $vmImage->displayIt($file_url, $file_alt, '',true,'',$withDescr);
				} else {
					$file_url = $vmImage->theme_url.'assets/images/vmgeneral/'.VmConfig::get('no_image_set');
					$file_alt = vmText::_('COM_VIRTUEMART_NO_IMAGE_SET').' '.$vmImage->file_description;
					return $vmImage->displayIt($file_url, $file_alt, $imageArgs,$lightbox, $effect);
				}
			}
		}
		
		
		
		$pa = pathinfo($vmImage->file_url); 
		if (empty($pa['extension'])) return ''; 

		//$file_url_thumb = $vmImage->getFileUrlThumb($width, $height);
		if (empty($width) && (empty($height))) {
		  
		  $height = VmConfig::get('img_height', 0); 
		  if (empty($height)) {
		    $width = VmConfig::get('img_width', 0);  
		  }
		}
		
		$w2 = $width; 
		$h2 = $height; 
		$file_url_thumb = self::getCreateImageUrlAndSize($vmImage, $w2, $h2, ''); 
		$root1 = Juri::root(true); 
		
		if (substr($root1, -1) !== '/') $root1 .= '/'; 
		//echo '<script>console.log(\''.$root1.'\');</script>'; 
		if (stripos($file_url_thumb, $root1)===0) {
		   $file_url_thumb = substr($file_url_thumb, strlen($root1)); 
		}
		$root2 = Juri::root(false); 
		if (substr($root2, -1) !== '/') $root2 .= '/'; 
		//echo '<script>console.log(\''.$root2.'\');</script>'; 
		if (stripos($file_url_thumb, $root2)===0) {
		   $file_url_thumb = substr($file_url_thumb, strlen($root2)); 
		}
		
		
		$imageArgs['width'] = $w2.'px'; 
		$imageArgs['height'] = $h2.'px'; 
		
		//if (substr($file_url_thumb, 0, 1) == '/') $file_url_thumb = substr($file_url_thumb, 1); 
		
		$media_path = VMPATH_ROOT.DS.str_replace('/',DS,$file_url_thumb);
		
		if(empty($vmImage->file_meta)){
			if(!empty($vmImage->file_description)){
				$file_alt = $vmImage->file_description;
			} else if(!empty($vmImage->file_name)) {
				$file_alt = $vmImage->file_name;
			} else {
				$file_alt = '';
			}
		} else {
			$file_alt = $vmImage->file_meta;
		}


		if($withDescr) $withDescr = $vmImage->file_description;
		/*
		if (empty($file_url_thumb) || !file_exists($media_path)) {
			//return $vmImage->getIcon($imageArgs,$lightbox,$return,$withDescr,$absUrl);
		}
		*/
		$imgTag = $vmImage->displayIt($file_url_thumb, $file_alt, $imageArgs,$lightbox,$effect,$withDescr,true);
		if($return) return $imgTag; 
		
		echo  $imgTag; 
		
		

	}
	
	public static function urlToPath($url, $ignoreFEx=false) {
		 if (empty($url)) return false; 
	     $root = Juri::root(); 
		 if (stripos($url, $root)===0) {
		   //to path: 
		   $url = substr($url, strlen($root));
		   $url = str_replace('/', DIRECTORY_SEPARATOR, $url); 
		   if (substr($url, 0, 1) === '/') $url = substr($url, 1); 
		   $url = JPATH_SITE.DIRECTORY_SEPARATOR.$url; 
		   if (!empty($ignoreFEx)) return $url; 
		   
		   if (file_exists($url)) {
		     return $url; 
		   }
		   else
		   {
			   return false; 
		   }
		   
		 }
		 return false; 
	}
	
	public static function displayMediaFull(&$vmImage, $imageArgs='',$lightbox=true,$effect="class='modal'",$description = true ){

		if($vmImage->file_is_forSale){
			return $vmImage->displayMediaThumb(array('id'=>'vm_display_image'),false);
		} else {
			//Media which should be sold, show them only as thumb (works as preview)
			if (is_array($imageArgs)) {
			 if (!isset($imageArgs['id'])) {
			    $imageArgs['id'] = 'vm_display_image'; 
			 }
			}
			return self::displayMediaThumb($vmImage, $imageArgs,$lightbox,$effect,true,$description, false);
		}


	}
	


}