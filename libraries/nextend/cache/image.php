<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php
nextendimport('nextend.cache.cache');
nextendimport('nextend.image.color');

class NextendCacheImage extends NextendCache {
    
    var $_folder;
    
    var $backgrouncolor;
    
    function NextendCacheImage() {

        $this->_subfolder = 'image' . DIRECTORY_SEPARATOR;
        $this->backgrouncolor = array(0,0,0);
        parent::NextendCache();
        
        if($this->_cacheTime == 'static' || $this->_cacheTime == 0){
            $this->_folder = $this->_path . 'static' . DIRECTORY_SEPARATOR;
            $currentcachetime = 0;
        }else{
            $time = time();
            $currentcachetime = $time - $time % $this->_cacheTime;
            $this->_folder = $this->_path . $this->_prename . $currentcachetime . DIRECTORY_SEPARATOR;
        }
        $this->createCacheSubFolder($this->_folder, $currentcachetime);
    }
    
    function setBackground($hex){
        $this->backgrouncolor = NextendColor::hex2rgb($hex);
    }
    
    function resizeImage($imageurl, $width, $height, $mode = 'cover', $resizeremote = false){
        $originalimageurl = $imageurl;
        if($width > 0 && $height > 0 && function_exists('exif_imagetype') && function_exists('imagecreatefrompng')){
            $extra = array();
            if(substr($imageurl, 0, 2) == '//'){
                $imageurl = parse_url(NextendUri::getBaseuri(), PHP_URL_SCHEME).':'.$imageurl;
            }
            $imageurl = NextendUri::relativetoabsolute($imageurl);
                        
            $imagepath = NextendFilesystem::absoluteURLToPath($imageurl);
            if($imagepath == $imageurl){
                if(!$resizeremote) return $originalimageurl;
                $imagepath = parse_url($imageurl, PHP_URL_PATH);
            }else{
                $extra[] = @filemtime($imagepath);
                $imageurl = $imagepath;
            }
            $extension = strtolower(pathinfo($imagepath, PATHINFO_EXTENSION));
            $filetype = '';
            if($extension == 'png'){
                $filetype = 'png';
            }else if($extension == 'jpg' || $extension == 'jpeg'){
                $filetype = 'jpg';
            }
            if($filetype != ''){
                $hash = $this->createHashFromArray(array_merge(func_get_args(), $this->backgrouncolor, $extra));
                $cachefile = $this->_folder.$hash.'.'.$filetype;
                if(!NextendFilesystem::existsFile($cachefile)){
                    $imagetype = @exif_imagetype($imageurl);
                    if($imagetype){
                        if($imagetype == IMAGETYPE_PNG){
                            $filetype = 'png';
                        }else if($imagetype == IMAGETYPE_JPEG){
                            $filetype = 'jpg';
                        }else{
                            $filetype = '';
                        }
                        if($filetype){
                            $img = null;
                            $rotated = null;
                            if($filetype == 'png'){
                                $img = @imagecreatefrompng($imageurl);
                            }else if($filetype == 'jpg'){
                                $img = @imagecreatefromjpeg($imageurl);
                                if(function_exists("exif_read_data")){ 
                                    $exif = exif_read_data($imageurl);
                                    if($exif && !empty($exif['Orientation'])){
                                        switch ($exif['Orientation']) {
                                            case 3:
                                                $rotated = imagerotate($img, 180, 0);
                                                break;
                            
                                            case 6:
                                                $rotated = imagerotate($img, -90, 0);
                                                break;
                            
                                            case 8:
                                                $rotated = imagerotate($img, 90, 0);
                                                break;
                                        }
                                    }
                                    if($rotated){
                                        imagedestroy($img);
                                        $img = $rotated;
                                    }
                                }
                            }
                            if($img){
                                $owidth = imagesx($img);
                                $oheight = imagesy($img);
                                if($rotated || $owidth != $width || $oheight != $height){
                                    $image = imagecreatetruecolor($width, $height);
                                    if($filetype == 'png'){
                                        imagesavealpha($image, true);
                                        imagealphablending($image, false);
                                        $white = imagecolorallocatealpha($image, 255, 255, 255, 127);
                                        imagefilledrectangle($image, 0, 0, $width, $height, $white);
                                    }else if($filetype == 'jpg'){
                                        $bg = imagecolorallocate($image, $this->backgrouncolor[0], $this->backgrouncolor[1], $this->backgrouncolor[2]);
                                        imagefilledrectangle($image, 0, 0, $width, $height, $bg);
                                    }
                                    $dst_x = 0;
                                    $dst_y = 0;
                                    $src_x = 0;
                                    $src_y = 0;
                                    $dst_w = $width;
                                    $dst_h = $height;
                                    $src_w = $owidth;
                                    $src_h = $oheight;
                                    $horizontalRatio = $width/$owidth;
                                    $verticalRatio = $height/$oheight;
                                    if($mode == 'cover'){
                                        if($horizontalRatio > $verticalRatio){
                                            $new_h = $horizontalRatio*$oheight;
                                            $dst_y = ($height-$new_h)/2;
                                            $dst_h = $new_h;
                                        }else{
                                            $new_w = $verticalRatio*$owidth;
                                            $dst_x = ($width-$new_w)/2;
                                            $dst_w = $new_w;
                                        }
                                    }else if($mode == 'contain'){
                                        if($horizontalRatio < $verticalRatio){
                                            $new_h = $horizontalRatio*$oheight;
                                            $dst_y = ($height-$new_h)/2;
                                            $dst_h = $new_h;
                                        }else{
                                            $new_w = $verticalRatio*$owidth;
                                            $dst_x = ($width-$new_w)/2;
                                            $dst_w = $new_w;
                                        }
                                    }
                                    imagecopyresampled($image, $img, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
                                    imagedestroy($img);
                                    if($filetype == 'png'){
                                        imagepng($image, $cachefile);
                                    }else if($filetype == 'jpg'){
                                        imagejpeg($image, $cachefile, 100);
                                    }
                                    imagedestroy($image);
                                    return NextendFilesystem::pathToAbsoluteURL($cachefile);
                                }else{
                                    imagedestroy($img);
                                }
                            }
                        }
                    }
                }else{
                    return NextendFilesystem::pathToAbsoluteURL($cachefile);
                }
            }
        }
        return $originalimageurl;
    }
}
if ( ! function_exists( 'exif_imagetype' ) ) {
    function exif_imagetype ( $filename ) {
        if ( ( list($width, $height, $type, $attr) = getimagesize( $filename ) ) !== false ) {
            return $type;
        }
    return false;
    }
}
