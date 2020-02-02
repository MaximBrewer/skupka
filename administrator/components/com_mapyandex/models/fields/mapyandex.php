<?php
// No direct access to this file
defined('_JEXEC') or die;
 
// import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');
 
/**
 * HelloWorld Form Field class for the HelloWorld component
 */
class JFormFieldMapYandex extends JFormFieldList
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 'MapYandex';
 
	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array		An array of JHtml options.
	 */
	protected function getOptions() 
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('id,name_map_yandex');
		$query->from('#__map_yandex');
		$db->setQuery((string)$query);
		$messages = $db->loadObjectList();
		$options = array();
		if ($messages)
		{
			foreach($messages as $message) 
			{
				$options[] = JHtml::_('select.option', $message->id, $message->name_map_yandex);
			}
		}
		$options = array_merge(parent::getOptions(), $options);
		return $options;
	}
}
