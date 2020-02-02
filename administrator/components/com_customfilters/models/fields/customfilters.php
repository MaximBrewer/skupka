<?php
/**
 * @package customfilters
 * @author Sakis Terzis (sakis@breakDesigns.net)
 * @copyright	Copyright (C) 2012-2018 breakDesigns.net. All rights reserved
 * @license	GNU/GPL v2
 */
defined('JPATH_BASE') or die();

jimport('joomla.html.html');
jimport('joomla.access.access');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 *
 * Class that generates a filter list
 *
 * @author Sakis Terzis
 */
class JFormFieldCustomfilters extends JFormFieldList
{

    /**
     * Method to get the field options.
     *
     * @return array The field option objects.
     *
     * @since 11.1
     */
    protected function getOptions()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        // table cf_customfields
        $query->select('cf.vm_custom_id AS value');
        $query->from('#__cf_customfields AS cf');
        // table vituemart_customfields
        $query->select('vmc.custom_title AS text');
        // joins
        $query->join('INNER', '#__virtuemart_customs AS vmc ON cf.vm_custom_id=vmc.virtuemart_custom_id');

        $db->setQuery($query);
        $options = $db->loadObjectList();
        $nullOption = new stdClass();
        $nullOption->text = '- ' . JText::_('JALL') . ' / ' . JText::_('JGLOBAL_AUTO') . ' -';
        $nullOption->value = '';
        array_unshift($options, $nullOption);
        $options = array_merge(parent::getOptions(), $options);
        return $options;
    }
}
