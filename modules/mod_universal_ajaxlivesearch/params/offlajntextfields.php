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

class JElementOfflajnTextFields extends JOfflajnFakeElementBase
{
  var $_moduleName = '';

	var	$_name = 'OfflajnTextFields';

	function universalfetchElement($name, $value, &$node)
	{
		$size = ( $node->attributes('size') ? 'size="'.$node->attributes('size').'"' : '' );

    if(!is_array($value)){
      $value = htmlspecialchars(html_entity_decode($value, ENT_QUOTES), ENT_QUOTES);
      $v = explode('|*', $value);
    }else{
      $v = $value;
    }

    $document =& JFactory::getDocument();
    if(version_compare(JVERSION,'3.0.0','lt'))
    $document->addStyleSheet(JURI::base().'../modules/'.$this->_moduleName.'/params/jpicker/css/jPicker-1.1.6.min.css');

    $id = $this->generateId($name);

    $field = "";
    $i = 0;
    foreach ($node->children() as $option)
		{
      $field.= '<div style="font-size:11px; float:left; margin-right:10px;" ><div style="float:left; padding:5px 5px 0 0;">'.$option->data().'</div><div style="float:left;"><input type="text" name="'.$name.'['.$i.']" id="'.$id.$i.'" style="width:20px;" value="'.@$v[$i].'"  '.$size.' /></div></div>';
		  $i++;
    }
		return $field;
	}
}

if(version_compare(JVERSION,'1.6.0','ge')) {
  class JFormOfflajnTextFields extends JElementOfflajnTextFields {}
}