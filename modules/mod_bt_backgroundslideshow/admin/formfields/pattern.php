<?php
/**
 * @package     formfields
 * @version		1.0
 * @created		Oct 2011

 * @author		BowThemes
 * @email		support@bowthems.com
 * @website		http://bowthemes.com
 * @support		Forum - http://bowthemes.com/forum/
 * @copyright           Copyright (C) 2012 Bowthemes. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.formfield');

class JFormFieldPattern extends JFormField {

    protected $type = 'pattern';

    public function getInput(){
		$relative_path  = '/modules/mod_bt_backgroundslideshow/tmpl/images/pattern/';
		$images =  $this->getAllImages(JPATH_ROOT. $relative_path);
        $html = '<div class="bg-pattern" style="background:#fff url('.JURI::root().$relative_path.$this->value.') left top repeat">';
		if($this->value == ''){
			$html.= '<div class="active bg-pattern-item" onclick="changePattern(this)">None</div>';
		}else{
			$html.= '<div class="bg-pattern-item" style="display:none" onclick="changePattern(this)">None</div>';
		}
		foreach ($images as $image) {
			if($this->value == $image){
				$html .= '<div onclick="changePattern(this)" class="active bg-pattern-item" title="'.$image.'" style="background:#fff url('.JURI::root().$relative_path.$image.') left top repeat"></div>';
			}
			else{
				$html .= '<div onclick="changePattern(this)" class="bg-pattern-item" title="'.$image.'" style="display:none;background:#fff url('.JURI::root().$relative_path.$image.') left top repeat"></div>';
			}
		}
        $html .= '</div>';
		$html .= '<input id="'.$this->id.'" type="hidden" name="'.$this->name.'" value="'. $this->value.'">';
		
		$document = JFactory::getDocument();
		
		$js = 'function changePattern(el){
				if(!jQuery(el).hasClass("active")){
				jQuery(".bg-pattern-item").removeClass("active");
				jQuery(el).addClass("active");
				jQuery("#'.$this->id.'").val(jQuery(el).attr("title"));
				jQuery(".bg-pattern-item").hide();
				jQuery(".bg-pattern .active").stop().show();
				jQuery(".bg-pattern").attr("style",jQuery(el).attr("style")).show();
			}else{
				jQuery(".bg-pattern").attr("style","");
				jQuery(".bg-pattern-item").fadeIn(300);
				jQuery(".bg-pattern .active").stop().show();
			}
		};';
		
		$document->addScriptDeclaration($js);
		return $html;
    }

    public function getAllImages($folder){
		$result = array();
        $files = JFolder::files($folder);
        foreach ($files as $file) {
			$paths = pathinfo($file);
			$ext = $paths['extension'];
           if(in_array($ext,array('png','jpeg','gif','bmp'))){
				$result[] = $file;
		   }
        }
		return $result;
    }

}

?>
