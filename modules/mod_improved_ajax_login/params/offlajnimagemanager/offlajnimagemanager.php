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
defined('_JEXEC') or die('Restricted access');

class JElementOfflajnImagemanager extends JOfflajnFakeElementBase
{
  var $_moduleName = '';

	var	$_name = 'Offlajnimagemanager';

	function universalfetchElement($name, $value, &$node){
    $this->loadFiles();
    $attrs = $node->attributes();
    $imgs = JFolder::files(JPATH_SITE.$attrs['folder'], $filter= '([^\s]+(\.(?i)(jpe?g|png|gif|bmp|svg))$)');
    $this->loadFiles('offlajnscroller', 'offlajnlist');

    $identifier = md5($name.$attrs['folder']);
    ${'_SESSION'}['offlajnupload'][$identifier] = JPATH_SITE.$attrs['folder'];
    $html = "";
    $desc = (isset($attrs['description']) && $attrs['description'] != "") ? $attrs['description'] : "";
    $imgs = ($imgs && count($imgs) != 0) ? json_encode((array)$imgs) : "{}";

    if(!$imgs) $imgs = "{}";

    $url='';
    $upload = '';
    if(defined('WP_ADMIN')){
      $url = smartslider_url('joomla/');
      $upload = 'admin.php?page=smartslider.php/slider&option=offlajnupload';
    }else{
      $url = JURI::root(true);
      $upload = 'index.php?option=offlajnupload';
    }

    if (!preg_match('/\.(jpe?g|png|gif|bmp|svg)$/i', $value)) $value = '';

    DojoLoader::addScript('
        new OfflajnImagemanager({
          id: "'.$this->id.'",
          folder: "'.str_replace(DIRECTORY_SEPARATOR,'/',$attrs['folder']).'",
          root: "'.$url.'",
          uploadurl: "'.$upload.'",
          imgs: '. $imgs .',
          identifier: "'.$identifier.'",
          description: "'.$desc.'",
          siteurl: "'.JURI::root().'"
        });
    ');

    $html = '<div id="offlajnimagemanager'.$this->id.'" class="offlajnimagemanager">';
    $html .= '<div class="offlajnimagemanagerimg">
                <div></div>
              </div>';
    $html .= '<div class="offlajnimagemanagerbtn"></div>';
    $html .= '<input type="hidden" data-folder="'.$attrs['folder'].'" name="'.$name.'" id="'.$this->id.'" value="'.$value.'"/>';
    $html .= "</div>";

		return $html;

	}
}

if(version_compare(JVERSION,'1.6.0','ge')) {
  class JFormFieldOfflajnImagemanager extends JElementOfflajnImagemanager {}
}

