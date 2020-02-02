<?php
/*------------------------------------------------------------------------
# mod_universal_ajaxlivesearch - Universal AJAX Live Search
# ------------------------------------------------------------------------
# author    Janos Biro
# copyright Copyright (C) 2011 Offlajn.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.offlajn.com
-------------------------------------------------------------------------*/
?><?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');

class JElementFont extends JOfflajnFakeElementBase
{
  var $_moduleName = '';

	var	$_name = 'font';

	var $_node = '';

	var $_google = array();
	var $_googleName = array();
	var $_googlefonts = array();

	function universalfetchElement($name, $value, &$node){
		$this->_node = &$node;
		$this->_googlefonts = array();
		$this->_google = array();
		$this->_googleName = array();
    $this->init();

    $html = '';
    $document =& JFactory::getDocument();
    if(version_compare(JVERSION,'3.0.0','lt'))
      $document->addScript(JURI::base().'../modules/'.$this->_moduleName.'/params/jpicker/jquery-1.4.4.min.js');

    $document->addScript(JURI::base().'../modules/'.$this->_moduleName.'/params/jpicker/jpicker-1.1.6.min.js');
    $document->addStyleSheet(JURI::base().'../modules/'.$this->_moduleName.'/params/jpicker/css/jPicker-1.1.6.min.css');

    $document->addScript(JURI::base().'../modules/'.$this->_moduleName.'/params/font.js');
    $document->addStyleSheet(JURI::base().'../modules/'.$this->_moduleName.'/params/font.css');
    $h = '
<fieldset style="position:relative; top: 20%; margin: 0 auto; width: 740px; background-color: #fff;border-radius: 8px; box-shadow: 0 0 5px rgba(0,0,0,0.4);">
  <h3 class="title" ><span>Font Chooser</span></h3>
  <div id="closefont" style="cursor: pointer; position: absolute; top:-14px; right:-15px; width: 30px; height: 30px; background: url(\'../modules/'.$this->_moduleName.'/params/images/closebox.png\') repeat;"></div>
  <table class="fontchooser" style="width: 100%;font-weight: bold;">
    <tr>
      <td width="10%">Font type: </td>
      <td width="37%">'.$this->genChooser().'</td>
      <td width="16%">Alternative fonts: </td>
      <td width="40%">'.$this->genAlternative().'</td>
    </tr>
    <tr class="blue">
      <td>Font family: </td>
      <td>'.$this->genFamily().'</td>
      <td>Font size: </td>
      <td>'.$this->genSize().'</td>
    </tr>
    <tr>
      <td>Bold:<br />Italic:</td>
      <td>
      <input type="checkbox" value="1" id="fontbold" style="float: none;">
      <br />
      <input type="checkbox" value="1" id="fontitalic" style="float: none;">
      </td>
      <td>Color:</td>
      <td>
        <input style="padding: 2px 0; width: 197px;" type="text" id="fontcolor1" value="" class="color" />
      </td>
    </tr>
    <tr class="blue">
      <td>Font shadow:</td>
      <td>
        <input type="checkbox" value="1" id="fontshadowbox">
      </td>
      <td>Font shadow properties:</td>
      <td>
        <input id="fontshadowx" value="0px" type="text" size="4">
        <input id="fontshadowy" value="0px" type="text" size="4">
        <input id="fontshadowsize" value="0px" type="text" size="4">
        <input style="padding: 2px 0; width: 197px;" type="text" id="fontshadowcolor" value="" class="color" />
        </td>
    </tr>
    <tr>
      <td>Font decoration:</td>
      <td>
        <select id="fontdecoration">
          <option value="none">None</option>
          <option value="underline">Underline</option>
          <option value="overline">Overline</option>
          <option value="line-through">Line-through</option>
        </select>
      </td>
      <td>Text transform:</td>
      <td>
        <select id="fonttransform">
          <option value="none">None</option>
          <option value="capitalize">Capitalize</option>
          <option value="uppercase">Uppercase</option>
          <option value="lowercase">Lowercase</option>
        </select>
      </td>
    </tr>
    <tr class="blue">
      <td>Line height:</td>
      <td>
        <input type="text" id="fontlineheight" value="100%" />
      </td>
      <td>Text align:</td>
      <td>
        <select id="fontalign">
          <option value="inherit">Inherit</option>
          <option value="left">Left</option>
          <option value="right">Right</option>
          <option value="center">Center</option>
        </select>
      </td>
    </tr>
    <tr>
      <td>Preview text:</td>
      <td>
        <input style="padding: 2px 0;width: 197px; margin-bottom: 5px;" type="text" id="previewbg" value="ffffff" class="color" /><input id="textpreview" type="text" value="Preview Text"></td>
      <td>Preview:</td>
      <td style="padding: 5px;" id="fontpreview"></td>
    </tr>
    <tr>
      <td></td>
      <td></td>
      <td></td>
      <td style="padding-top: 20px; float:right; margin: 0 15px 15px 0;"><button id="savefont" type="button" style="margin-right: 20px; font-size: 14px;font-weight: bold;">Save</button><button style="font-size: 14px;font-weight: bold;" id="cancelfont" type="button">Cancel</button></td>
    </tr>
  </table>
</fieldset>';
    $id = $this->generateId($name);
    $GLOBALS['themescripts'][] ='
      dojo.addOnLoad(function(){
        new FontConfigurator({
          module: "'.$this->_moduleName.'",
          hidden: dojo.byId("'.$id.'"),
          changefont: dojo.byId("'.$id.'change"),
          fonts: '.json_encode($this->_googlefonts).',
          h: '.json_encode($h).'
        });
      });
    ';
		$html.="<a style='float: left;' id='".$id."change' href='#'>[".JText::_('Change font settings')."]</a>&nbsp;&nbsp;";
    if(is_object($this->_parent) && $this->_parent->get('admindebug', 0) == 1){
		  $html.='<span>Raw font data: </span><input type="text" name="'.$name.'" id="'.$id.'" value="'.str_replace('"',"'",$value).'" />';
    }else{
		  $html.='<input type="hidden" name="'.$name.'" id="'.$id.'" value="'.str_replace('"',"'",$value).'" />';
    }
		return $html;
	}

	function init(){
	  $p = dirname(__FILE__).'/google/';
    $google = JFolder::files($p, '.txt');
    foreach($google as $g){
      $this->_google[] = JFile::stripExt($g);
      preg_match_all('/((?:^|[A-Z])[a-z]+)/',JFile::stripExt($g),$matches);
      $this->_googleName[] = implode(' ', $matches[1]);
      $fp = @fopen($p.$g, 'r');
      if ($fp) {
        $this->_googlefonts[JFile::stripExt($g)] = explode("\r\n", fread($fp, filesize($p.$g)));
      }
      fclose($fp);
    }
  }

	function genChooser(){
    $chooser = "
    <select id='fonttype'>
      <option value='0'>". JText::_('Use only the alternative fonts') ."</value>
      <optgroup label='Google fonts'>
    ";
    foreach($this->_google AS $k => $g){
      $chooser.= "<option value='".$g."'>". JText::_($this->_googleName[$k]) ."</option>";
    }
    $chooser.= "
      </optgroup>
    </select>
    ";

    return $chooser;
  }

	function genAlternative(){
    $alt = "<input id='alternativefont' type='text' value='Arial, Helvetica'>";

    return $alt;
  }

	function genFamily(){
    $fam = "
        <select id='fontfamily'>

        </select>
    ";

    return $fam;
  }

	function genSize(){
    $fam = "
        <input type='text' value='12px' id='fontsize'>
    ";

    return $fam;
  }

}

if(version_compare(JVERSION,'1.6.0','ge')) {
  class JFormFont extends JElementFont {}
}