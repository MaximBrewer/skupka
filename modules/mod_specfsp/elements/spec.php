<?php
defined('JPATH_BASE') or die;
jimport('joomla.form.formfield');
if(JVERSION>=1.6) {
	class JFormFieldSpec extends JFormField {
        protected $type = 'spec';
       protected function getInput() {
			$database =  JFactory::getDBO();		
			$database->setQuery( "SELECT id, name FROM #__ttfsp_sprspec WHERE published=1 ORDER BY ordering" );
			$rowspec = $database->loadObjectList();
			$sprspec = array();
			$sprspec[0] = '';   
			$sprspec = array_merge( $sprspec, $rowspec);
			$lists['sprspec']	 = JHTML::_('select.genericlist', $sprspec, $this->name, 'size="1" class="inputbox"', 'id', 'name',$this->value);
			return $lists['sprspec'];
        }
	}
} else {
	class JElementSpec extends JElement {
		var $_name = 'spec';
		function fetchElement($name, $value, &$node, $control_name) {
			$database =  JFactory::getDBO();		
			$database->setQuery( "SELECT id, name FROM #__ttfsp_sprspec WHERE published=1 ORDER BY ordering" );
			$rowspec = $database->loadObjectList();
			$sprspec = array();
			$sprspec[0] = '';   
			$sprspec = array_merge( $sprspec, $rowspec);
			$lists['sprspec']	 = JHTML::_('select.genericlist', $sprspec, $name, 'size="1" class="inputbox"', 'id', 'name',$value);
			return $lists['sprspec'];		
 
		}
	}
}

















?>