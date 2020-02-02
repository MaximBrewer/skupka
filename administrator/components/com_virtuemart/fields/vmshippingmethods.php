<?php
defined ('_JEXEC') or die();
if(version_compare(JVM_VERSION,'3','ge')){
	JFormHelper::loadFieldClass('list');
jimport('joomla.form.formfield');

class JFormFieldVmShippingMethods extends JFormFieldList {

	/**
	 * Element name
	 * @access    protected
	 * @var        string
	 */
	var $type = 'ShippingMethods';

	protected function getInput() {
		$this->multiple=true;
		return parent::getInput();
	}
	protected function getOptions() {
		$options = array();
		$this->multiple=true;
		$table = '#__virtuemart_shipmentmethods';
		$enable = 'published';
		$ext_id = 'virtuemart_shipmentmethod_id ';
		$query = 'SELECT s.virtuemart_shipmentmethod_id as id,l.shipment_name as name FROM `'.$table.'` as s left join '.$table.'_'.VMLANG.' as l on l.virtuemart_shipmentmethod_id =s.virtuemart_shipmentmethod_id   WHERE s.`'.$enable.'`="1" ';
		$db = JFactory::getDBO();
		$db->setQuery($query);
		$values = $db->loadObjectList();
		foreach ($values as $v) {
			$options[] = JHtml::_('select.option', $v->id, $v->name);
		}

		//BAD $class = 'multiple="true" size="10"';
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
} else {
class JElementVmShippingMethods extends JElement {

	/**
	 * Element name
	 *
	 * @access    protected
	 * @var        string
	 */
	var $_name = 'ShippingMethods';

	function fetchElement ($name, $value, &$node, $control_name) {

		$db = JFactory::getDBO ();
		$table = '#__virtuemart_shipmentmethods';
		$enable = 'published';
		$ext_id = 'virtuemart_shipmentmethod_id ';
		$q = 'SELECT s.virtuemart_shipmentmethod_id as id,l.shipment_name as name FROM `'.$table.'` as s left join '.$table.'_'.VMLANG.' as l on l.virtuemart_shipmentmethod_id =s.virtuemart_shipmentmethod_id   WHERE s.`'.$enable.'`="1" ';
		$db->setQuery($q);
		$class = 'multiple="true" size="10"';
		$result = $db->loadAssocList('id');
		return JHtml::_('select.genericlist', $result, $control_name . '[' . $name . '][]', $class,'id', 'name',  $value, $control_name . $name);
	}

}

}

