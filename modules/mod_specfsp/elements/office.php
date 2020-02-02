<?php
defined('JPATH_BASE') or die;
jimport('joomla.form.formfield');
if(JVERSION>=1.6) {
	class JFormFieldOffice extends JFormField {
        protected $type = 'office';
       protected function getInput() {
			$database =  JFactory::getDBO();		
			$database->setQuery( "SELECT id, name FROM #__ttfsp_sprsect WHERE published=1 ORDER BY ordering" );
			$rowsect = $database->loadObjectList();
			$sprsect = array();
			$sprsect[0] = '';   
			$sprsect = array_merge( $sprsect, $rowsect);
			$lists['sprsect']	 = JHTML::_('select.genericlist', $sprsect, $this->name, 'size="1" class="inputbox"', 'id', 'name',$this->value);
			return $lists['sprsect'];
        }
	}
} else {
	class JElementOffice extends JElement {
		var $_name = 'office';
		function fetchElement($name, $value, &$node, $control_name) {
			$database =  JFactory::getDBO();		
			$database->setQuery( "SELECT id, name FROM #__ttfsp_sprsect WHERE published=1 ORDER BY ordering" );
			$rowsect = $database->loadObjectList();
			$sprsect = array();
			$sprsect[0] = '';   
			$sprsect = array_merge( $sprsect, $rowsect);
			$lists['sprsect']	 = JHTML::_('select.genericlist', $sprsect, $name, 'size="1" class="inputbox"', 'id', 'name',$value);
			return $lists['sprsect'];		
 
		}
	}
}

















?>