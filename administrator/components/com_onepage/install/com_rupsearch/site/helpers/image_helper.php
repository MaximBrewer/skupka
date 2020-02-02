<?php
/**
* @package mod_vm_ajax_search
*
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL, see LICENSE.php
* VM Live Product Search is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/

/*
---------------------------------------------------------------------
Credits: Bit Repository

Source URL: http://www.bitrepository.com/resize-an-image-keeping-its-aspect-ratio-using-php-and-gd.html

Modified by stAn, RuposTel 
---------------------------------------------------------------------
*/
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 
class rupResize_Image {

var $image_to_resize;
var $new_width;
var $new_height;
var $ratio;
var $new_image_name;
var $save_folder;

function resize()
{
if(!file_exists($this->image_to_resize))
{
  exit("File ".$this->image_to_resize." does not exist.");
}

$info = @GetImageSize($this->image_to_resize);


if(empty($info))
{
  exit("The file ".$this->image_to_resize." doesn't seem to be an image.");
}

$width = $info[0];
$height = $info[1];
$mime = $info['mime'];

/*
Keep Aspect Ratio?

Improved, thanks to Larry
*/

if($this->ratio)
{
// if preserving the ratio, only new width or new height
// is used in the computation. if both
// are set, use width

if (isset($this->new_width) && ((float)$width>(float)$height))
{
$factor = (float)$this->new_width / (float)$width;
$this->new_height = $factor * $height;
}
else if (isset($this->new_height))
{
$factor = (float)$this->new_height / (float)$height;
$this->new_width = $factor * $width;
}
else
exit('neither new height or new width has been set');
}

// What sort of image?

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

	// New Image
	$image_c = ImageCreateTrueColor($this->new_width, $this->new_height);
	$new_image = $image_create_func($this->image_to_resize);
	
	
	// stAn from php.net: will preserve transparency
	
	/*
	
	$originaltransparentcolor = imagecolortransparent( $new_image );
	if(
    $originaltransparentcolor >= 0 // -1 for opaque image
    && $originaltransparentcolor < imagecolorstotal( $new_image )
    // for animated GIF, imagecolortransparent will return a color index larger
    // than total colors, in this case the image is treated as opaque ( actually
    // it is opaque )
	) {
    $transparentcolor = imagecolorsforindex( $new_image, $originaltransparentcolor );
    $newtransparentcolor = imagecolorallocate(
        $image_c,
        $transparentcolor['red'],
        $transparentcolor['green'],
        $transparentcolor['blue']
    );
    // for true color image, we must fill the background manually
    imagefill( $image_c, 0, 0, $newtransparentcolor );
    // assign the transparent color in the thumbnail image
    imagecolortransparent( $image_c, $newtransparentcolor );
	}
	else
	{
	 $black = imagecolorallocate($im, 0, 0, 0);
	 imagecolortransparent($im, $black);
	}
	
	*/
	// preserve transparency
  if($type == "gif" or $type == "png"){
    imagecolortransparent($image_c, imagecolorallocatealpha($image_c, 0, 0, 0, 127));
    imagealphablending($image_c, false);
    imagesavealpha($image_c, true);
  }
	
	
	// end: preserving transparency

	ImageCopyResampled($image_c, $new_image, 0, 0, 0, 0, $this->new_width, $this->new_height, $width, $height);
	//imagecopy($image_c, $new_image, 0, 0, 0, 0, $this->new_width, $this->new_height, $width, $height);

        if($this->save_folder)
		{
	       if($this->new_image_name)
	       {
	       $new_name = $this->new_image_name.'.'.$new_image_ext;
	       }
	       else
	       {
	       $new_name = $this->new_thumb_name( basename($this->image_to_resize) ).'_resized.'.$new_image_ext;
	       }

		$save_path = $this->save_folder.$new_name;
		}
		else
		{
		/* Show the image without saving it to a folder */
		   header("Content-Type: ".$mime);

	       $image_save_func($image_c);

		   $save_path = '';
		}

	    $process = $image_save_func($image_c, $save_path);

		return array('result' => $process, 'new_file_path' => $save_path, 'name' => $new_name);

	}

	function new_thumb_name($filename)
	{
	$string = trim($filename);
	$string = strtolower($string);
	$string = trim(ereg_replace("[^ A-Za-z0-9_]", " ", $string));
	$string = ereg_replace("[ tnr]+", "_", $string);
	$string = str_replace(" ", '_', $string);
	$string = ereg_replace("[ _]+", "_", $string);

	return $string;
	}

	function getVm1Path($img)
	{
	  
	   return $img_path; 
	  
	}
	 function getMediaData($id)
 {
   if (empty($id)) return;
   if (is_array($id)) $id = reset($id);
   $db = JFactory::getDBO(); 
   $q = "select * from #__virtuemart_medias where virtuemart_media_id = '".$db->escape($id)."' "; 
   $db->setQuery($q); 
   $res = $db->loadAssoc(); 
   $err = $db->getErrorMsg(); 
   
   return $res; 
 }
 public static function getImageFile($id_a, $w=0, $h=0)
 {
	if (!is_array($id_a)) $id_a = array($id_a); 
	foreach ($id_a as $id)
	{
	 
   $img = self::getMediaData($id);
   if (empty($img)) continue; 
   
   if (!empty($img['file_url_thumb']))
    {
	  $th = $img['file_url_thumb']; 
	  
	  if (!empty($w) && (!empty($h)))
	  {
	  $th2 = str_replace('/resized/', '/resized_'.$w.'x'.$h, $th); 
	  $thf = JPATH_SITE.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $th2); 
	  $thf = str_replace(JPATH_SITE.DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, JPATH_SITE.DIRECTORY_SEPARATOR, $thf); 
	 
	  if (file_exists($thf)) return $thf;
	  }
	  $thf = JPATH_SITE.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $th); 
	  $thf = str_replace(JPATH_SITE.DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, JPATH_SITE.DIRECTORY_SEPARATOR, $thf); 
	  
	  
	  if (file_exists($thf)) 
	  {
	  $tocreate = true; 
	  return $thf;
	  }
	  
	}
   
    
	  $th = $img['file_url']; 
	  if (!empty($w) && (!empty($h)))
	  {
	  $th2 = str_replace('/virtuemart/', '/virtuemart/resized_'.$w.'x'.$h, $th); 
	  $thf = JPATH_SITE.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $th2); 
	  $thf = str_replace(JPATH_SITE.DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, JPATH_SITE.DIRECTORY_SEPARATOR, $thf); 
	  if (file_exists($thf)) return $thf;
	  }
	  $thf = JPATH_SITE.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $th); 
	  $thf = str_replace(JPATH_SITE.DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, JPATH_SITE.DIRECTORY_SEPARATOR, $thf); 
	  if (file_exists($thf)) 
	  	{
	    $tocreate = true; 
		return $thf;
		}
	
	}
	return ''; 
 
 }
 function getImageUrl($id, &$tocreate, $w=0, $h=0)
 {
   $img = self::getMediaData($id);
   if (empty($img)) return ''; 
   if (!empty($img['file_url_thumb']))
    {
	  $th = $img['file_url_thumb']; 
	  $th2 = str_replace('/resized/', '/resized_'.$w.'x'.$h, $th); 
	  $thf = JPATH_SITE.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $th2); 
	  $thf = str_replace(JPATH_SITE.DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, JPATH_SITE.DIRECTORY_SEPARATOR, $thf); 
	  if (file_exists($thf)) return $th2;
	  $thf = JPATH_SITE.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $th); 
	  $thf = str_replace(JPATH_SITE.DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, JPATH_SITE.DIRECTORY_SEPARATOR, $thf); 
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
	  $thf = JPATH_SITE.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $th2); 
	  $thf = str_replace(JPATH_SITE.DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, JPATH_SITE.DIRECTORY_SEPARATOR, $thf); 
	  if (file_exists($thf)) return $th2;
	  $thf = JPATH_SITE.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $th); 
	  $thf = str_replace(JPATH_SITE.DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, JPATH_SITE.DIRECTORY_SEPARATOR, $thf); 
	  if (file_exists($thf)) 
	  	{
	    $tocreate = true; 
		return $th;
		}
	}
 }
	public static function showImage($img, $width, $height)
	{
	  $img_path = $img; 
	  //echo $img.'<br />';
	  //if (!empty($img))
	  {
	  
	    $fi = pathinfo($img); 
	    $filename = $fi['filename']; 
		if (empty($fi['extension'])) 
		 {
		  return ""; 
		 }
	    $ext = $fi['extension']; 
	    // should return /components/com_virtuemart/shop_image/product
		
		{
		  $dest = JPATH_SITE.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'thumb_'.$width.'x'.$height;
		  $rel = JURI::base(true).'/'.'images'.DIRECTORY_SEPARATOR.'thumb_'.$width.'x'.$height;
		  $rel = str_replace('modules/mod_vm_ajax_search/ajax/', '', $rel); 
		}
		
	    if (!file_exists($dest)) 
	    {
		 jimport('joomla.filesystem.folder'); 
		 JFolder::create($dest); 
	     //mkdir($dest); 
	    }
	    //$a = explode('_', $fi['basename']); 
	    
	    if (false)
	    if (count($a)>1)
	    {
	      $ni = str_replace('_'.$a[count($a)-1], '', $fi['basename']); 
	    }
	    //else 
	    
	    
	    $ni = $fi['filename']; 
	    
	    $ni .= '_'.$width.'x'.$height.'.'.$fi['extension'];
	    //echo $ni.'<br />';
	    //if (!file_exists($dest.'/'.$ni))
	    if (true)
	    {
	    // resize image
	    $resize_image = new rupResize_Image;
	    
	    $resize_image->new_width = $width;
		$resize_image->new_height = $height;
		$resize_image->image_to_resize = $img_path; // Full Path to the file
		// new name without extension 
		$ffi = pathinfo($ni); 
		$resize_image->new_image_name = $ffi['filename'];
		$resize_image->save_folder = $dest.'/';
		$resize_image->ratio = true; // Keep aspect ratio

		$process = $resize_image->resize(); // Output image
		$ni = $process['name']; 
		
	    }
	   
	    if (!file_exists($dest.'/'.$ni)) return "";
	    
		
	    $size = @getimagesize($dest.'/'.$ni); 
		if (empty($size)) return ""; 
	   
	    
	    $rheight = $size[0]; 
	    $rwidth = $size[1]; 
	    
	    
	    $difh = round(($height - $rheight)/2);
	    $difw = round(($width-$rwidth)/2); 
	    $difh = $difh-1;
	    if ($difw<0) $difw = 0; 
	    if ($difh<0) $difh = 0;
	    $x = strpos($dest, 'components'); 
	    $relpath = '/'.substr($dest, $x);
	    //$relpath = str_replace(JPATH_ROOT, '', $dest);
		if (empty($rel))
	    $relpath = str_replace('\\', "/", $relpath);  
		else $relpath=$rel;
	    //$relpath = str_replace(JPATH_ROOT, '', $dest);
	    if ($rheight<$height)
	    {
//	      echo '<div style="float: left; width: 100%; height: '.$difh.'px; display: inline-block; white-space: no-wrap;">';
//	      echo '&nbsp;</div>';  
	    }
	    if (!empty($difw))
	    {
	      $h = $height - $difh; 
//	      echo '<div style="float: left; width: '.$difh.'px; height: '.$h.'px; display: inline-block; white-space: no-wrap">';
//	      echo '&nbsp;</div>';
	    }
	    echo '<div style="margin-top: '.$difw.'px; margin-left: '.$difh.'px; position: absolute;">';
	    echo '<img style="magin: 0;" src="'.$relpath."/".$ni.'" alt="" />';
	    echo '</div>';
	    
	    //if (!empty($difh)) echo '</div>';
	    //echo '</div>';
	    
	    //echo $src; die();
	  }
	 // else
	  {
	    //echo 'empty';
	  }
	 
	}
}
class ajaxProductHelper 
{
  // returns deepest category in VM
  function get_lowcat($product_id)
  {
  		    $database = JFactory::getDBO();
  		    //najde kategoriu druhej alebo mensej urovne viac menej nahodne
  		    $sql = "SELECT #__vm_category.category_id FROM #__vm_product_category_xref, #__vm_category, #__vm_category_xref WHERE #__vm_category_xref.category_child_id=#__vm_product_category_xref.category_id AND #__vm_category.category_publish='Y' AND #__vm_category.category_id=#__vm_category_xref.category_child_id and #__vm_category_xref.category_parent_id <> 0 AND #__vm_product_category_xref.product_id = '".$product_id."' ";
   	        
			$database->setQuery($sql);
			
			//$res =  $database->loadResult();
			$resA = $database->loadAssocList();
			if (!empty($resA))
			{
			foreach ($resA as $res)
			{
			 
			 {
			  $arr = array();
			  $cats = $this->build_cats($res['category_id'], $arr);
			  //$x = end($cats);

			  if (!empty($cats))
			  // if (end($cats)!='262') IF YOU USE A CATEGORY SUCH AS LATEST PRODUCTS
			   {

			    return $res['category_id'];
			   }
			 }
			}
			//echo $product_id.'...cat...'.$res['category_id']; die();
			// nechame novinky ak inde nie je
			return $res['category_id'];
			}
			
			
			
			if (!isset($res) || ($res==false))
			{
			 // ak podkategoria neexistuje, najde top kategoriu
			  	$sql = "SELECT #__vm_category.category_id FROM #__vm_product_category_xref, #__vm_category, #__vm_category_xref WHERE #__vm_category_xref.category_child_id=#__vm_product_category_xref.category_id AND #__vm_category.category_publish='Y' AND #__vm_category.category_id=#__vm_category_xref.category_child_id AND #__vm_product_category_xref.product_id = '$product_id' LIMIT 0,1";
			  	$database->setQuery($sql);
				$res =  $database->loadResult();
				return $res;
			}

			return 0;

  }	

    // last item is a category's top cat 
	function build_cats($cat, $arr = array())
	{
		$database = JFactory::getDBO();
			
			// keby sme sa chceli nahodou zacyklit, tak radsej skocime pri 15tej hlbke...
			if (sizeof($arr) > 15) return $arr;
			// zisti nadradenu kategoriu
			$sql = "SELECT category_parent_id FROM #__vm_category_xref, #__vm_category WHERE jos_vm_category.category_id=jos_vm_category_xref.category_child_id and jos_vm_category.category_publish='Y' and  jos_vm_category_xref.category_child_id ='$cat' ORDER BY category_parent_id DESC LIMIT 0,1";
			$database->setQuery($sql);
			$parent_cat_id = $database->loadResult();
			//$this->logger($parent_cat_id, "parent_cat_id for child ".$cat.$database->getErrorMsg());
			
			// zisti nazov kategorie
			$sql = "SELECT category_name FROM #__vm_category WHERE category_id ='".$cat."' LIMIT 0,1";
			$database->setQuery($sql);
			$parent_name = $database->loadResult();
			$arr[] = $parent_name;
			//$this->logger($parent_name, "parent_name for catid ".$cat.' '.$database->getErrorMsg());
			
			if (($parent_cat_id == '0') || (!isset($parent_cat_id))) {
				return $arr;
			}
			else
			{
			  return $this->build_cats($parent_cat_id, $arr);
			}  
	}//build_cats()	
	

}


