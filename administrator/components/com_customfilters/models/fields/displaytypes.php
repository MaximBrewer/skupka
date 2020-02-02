<?php
/**
 * @package customfilters
 * @version $Id: fields/displayTypes.php  2014-6-03 sakisTerzis $
 * @author Sakis Terzis (sakis@breakDesigns.net)
 * @copyright	Copyright (C) 2012-2018 breakDesigns.net. All rights reserved
 * @license	GNU/GPL v2
 */

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.access.access');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 *
 * Class that generates a filter list
 * @author Sakis Terzis
 */
Class JFormFieldDisplaytexts extends JFormFieldList{

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   11.1
	 */
	protected function getOptions()
	{
		$options=array(
		array('value' => '1','text' => 'select'),
		array('value' => '2','text' => 'radio'),
		array('value' => '3','text' => 'checkbox'),
		array('value' => '4','text' => 'link'),
		array('value' => '5','text' => 'range_inputs'),
		array('value' => '6','text' => 'range_slvalueer'),
		array('value' => '5,6','text' => 'range_input_slvalueer'),
		array('value' => '8','text' => 'range_calendars'),
		array('value' => '9','text' => 'color_btn_sinlge'),
		array('value' => '10','text' => 'color_btn_multi'),
		array('value' => '11','text' => 'button_single'),
		array('value' => '12','text' => 'button_multi')
		);
		$options = array_merge(parent::getOptions(), $options);
		return $options;
	}


}
