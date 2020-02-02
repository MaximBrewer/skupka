<?php
/*------------------------------------------------------------------------
# mod_universal_ajaxlivesearch - Universal AJAX Live Search
# ------------------------------------------------------------------------
# author    Janos Biro
# copyright Copyright (C) 2011 Offlajn.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.offlajn.com
-------------------------------------------------------------------------*/
?>
<?php
defined('_JEXEC') or die('Restricted access');

class JElementCombobox extends JElement
{
  var $_moduleName = '';

	var	$_name = 'combobox';

	function fetchElement($name, $value, &$node, $control_name){
		$class = ( $node->attributes('class') ? 'class="'.$node->attributes('class').'"' : 'class="inputbox"' );

		$options = array ();
		$options[] = JHTML::_('select.option', '', '');
		$options[] = JHTML::_('select.option', 'templatedefault', JText::_('Template default'));

		foreach ($node->children() as $option)
		{
			$val	= $option->attributes('value');
			$text	= $option->data();
			$options[] = JHTML::_('select.option', $val, JText::_($text));
		}

    $value = htmlspecialchars(html_entity_decode($value, ENT_QUOTES), ENT_QUOTES);

		return '<input type="text" name="'.$control_name.'['.$name.']" id="'.$control_name.$name.'" value="'.$value.'" '.$class.' />'.
		        JHTML::_('select.genericlist',  $options, $name, $class.' onchange="dojo.byId(\''.$control_name.$name.'\').value = this.options[this.selectedIndex].value; this.selectedIndex=0;"', 'value', 'text', '', $control_name.$name.'list');
	}

}