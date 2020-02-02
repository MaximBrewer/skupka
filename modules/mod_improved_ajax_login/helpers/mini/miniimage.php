<?php
/*-------------------------------------------------------------------------
# mod_improved_ajax_login - Improved AJAX Login and Register
# -------------------------------------------------------------------------
# @ author    Balint Polgarfi
# @ copyright Copyright (C) 2017 Offlajn.com  All Rights Reserved.
# @ license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @ website   http://www.offlajn.com
-------------------------------------------------------------------------*/
?><?php
// no direct access
defined('_JEXEC') or die('Restricted access');

if(!class_exists('OfflajnMiniImageHelper')){
    require_once(dirname(__FILE__).'/color.php');

    class OfflajnMiniImageHelper{
        var $cache;

        var $cacheUrl;

        var $step = 1;

        var $c;

        function __construct($cacheDir, $cacheUrl){
          $this->cache = $cacheDir;
          $this->cacheUrl = $cacheUrl;
          $this->c = new OfflajnColorHelper();
        }

        function colorizeImage($img, $targetColor, $baseColor){
					preg_match('/(\d+),\s*(\d+),\s*(\d+),\s*(\d\.?\d*)/', $targetColor, $m);
					$targetColor = count($m) ? dechex($m[1]).dechex($m[2]).dechex($m[3]) : substr($targetColor, 1);
					$alpha = count($m) ? (float)$m[4] : 1;
          $c1 = $this->c->hex2hsl($baseColor);
          $c2 = $this->c->hex2hsl($targetColor);
          $im = imagecreatefrompng($img);
          $height = imagesy($im);
          $width = imagesx($im);
          $imnew = imagecreatetruecolor($width, $height);
          imagesavealpha($imnew, true);
          imagealphablending($imnew, false);
          $transparent = imagecolorallocatealpha($imnew, 255, 255, 255, 127);
          imagefilledrectangle($imnew, 0, 0, $width, $height, $transparent);
          $rgb = $this->c->rgb2array($targetColor);
          for($x=0; $x<$width; $x++){
              for($y=0; $y<$height; $y++){
                  $rgba = ImageColorAt($im, $x, $y);
                  $rgb = array(($rgba >> 16) & 0xFF, ($rgba >> 8) & 0xFF, $rgba & 0xFF);
                  $hsl = $this->c->rgb2hsl($rgb);
                  $a[0] = $hsl[0] + ($c2[0] - $c1[0]);
                  $a[1] = $hsl[1] * ($c2[1] / $c1[1]);
                  if($a[1] > 1) $a[1] = 1;
                  $a[2] = exp(log($hsl[2]) * log($c2[2]) / log($c1[2]) );
                  if($a[2] > 1) $a[2] = 1;
                  $rgb = $this->c->hsl2rgb($a);
                  $A = 0xFF-(($rgba >> 24)*2) & 0xFF;
                  $A = (int)($A * $alpha);
                  if($A > 0xFF) $A = 0xFF;
                  $A = (int)((0xFF-$A)/2);
                  imagesetpixel($imnew, $x, $y, imagecolorallocatealpha($imnew, $rgb[0], $rgb[1], $rgb[2], $A));
              }
          }
          $hash = md5($img.$targetColor.$alpha).'.png';
          imagepng($imnew, $this->cache.'/'.$hash);
          imagedestroy($imnew);
          imagedestroy($im);
          return $this->cacheUrl.$hash;
        }

        function colorizeImages($img, $color1, $color2, $baseColor){
					//if ($color1 == color2) return $this->colorizeImage($img, $color1, $baseColor);
					preg_match('/(\d+),\s*(\d+),\s*(\d+),\s*(\d\.?\d*)/', $color1, $m);
					$color1 = count($m) ? dechex($m[1]).dechex($m[2]).dechex($m[3]) : substr($color1, 1);
					$alpha1 = count($m) ? (float)$m[4] : 1;
					preg_match('/(\d+),\s*(\d+),\s*(\d+),\s*(\d\.?\d*)/', $color2, $m);
					$color2 = count($m) ? dechex($m[1]).dechex($m[2]).dechex($m[3]) : substr($color2, 1);
					$alpha2 = count($m) ? (float)$m[4] : 1;
          $c = $this->c->hex2hsl($baseColor);
          $c1 = $this->c->hex2hsl($color1);
					$c2 = $this->c->hex2hsl($color2);
          $im = imagecreatefrompng($img);
          $height = imagesy($im);
          $width = imagesx($im);
          $imnew = imagecreatetruecolor(2 * $width, $height);
          imagesavealpha($imnew, true);
          imagealphablending($imnew, false);
          $transparent = imagecolorallocatealpha($imnew, 255, 255, 255, 127);
          imagefilledrectangle($imnew, 0, 0, 2 * $width, $height, $transparent);
          $rgb = $this->c->rgb2array($color1);
          for($x=0; $x<$width; $x++){
              for($y=0; $y<$height; $y++){
                  $rgba = ImageColorAt($im, $x, $y);
                  $rgb = array(($rgba >> 16) & 0xFF, ($rgba >> 8) & 0xFF, $rgba & 0xFF);
                  $hsl = $this->c->rgb2hsl($rgb);
                  $a[0] = $hsl[0] + ($c1[0] - $c[0]);
                  $a[1] = $hsl[1] * ($c1[1] / $c[1]);
                  if($a[1] > 1) $a[1] = 1;
                  $a[2] = exp(log($hsl[2]) * log($c1[2]) / log($c[2]) );
                  if($a[2] > 1) $a[2] = 1;
                  $rgb = $this->c->hsl2rgb($a);
                  $A = 0xFF-(($rgba >> 24)*2) & 0xFF;
                  $A = (int)($A * $alpha1);
                  if($A > 0xFF) $A = 0xFF;
                  $A = (int)((0xFF-$A)/2);
                  imagesetpixel($imnew, $x, $y, imagecolorallocatealpha($imnew, $rgb[0], $rgb[1], $rgb[2], $A));
              }
          }
          $rgb = $this->c->rgb2array($color2);
          for($x=$width; $x<2*$width; $x++){
              for($y=0; $y<$height; $y++){
                  $rgba = ImageColorAt($im, $x - $width, $y);
                  $rgb = array(($rgba >> 16) & 0xFF, ($rgba >> 8) & 0xFF, $rgba & 0xFF);
                  $hsl = $this->c->rgb2hsl($rgb);
                  $a[0] = $hsl[0] + ($c2[0] - $c[0]);
                  $a[1] = $hsl[1] * ($c2[1] / $c[1]);
                  if($a[1] > 1) $a[1] = 1;
                  $a[2] = exp(log($hsl[2]) * log($c2[2]) / log($c[2]) );
                  if($a[2] > 1) $a[2] = 1;
                  $rgb = $this->c->hsl2rgb($a);
                  $A = 0xFF-(($rgba >> 24)*2) & 0xFF;
                  $A = (int)($A * $alpha2);
                  if($A > 0xFF) $A = 0xFF;
                  $A = (int)((0xFF-$A)/2);
                  imagesetpixel($imnew, $x, $y, imagecolorallocatealpha($imnew, $rgb[0], $rgb[1], $rgb[2], $A));
              }
          }
          $hash = md5($img.$color1.$alpha1.$color2.$alpha2).'.png';
          imagepng($imnew, $this->cache.'/'.$hash);
          imagedestroy($imnew);
          imagedestroy($im);
          return $this->cacheUrl.$hash;
        }
    }
}
?>