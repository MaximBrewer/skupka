<?php
N2Loader::import('libraries.form.element.list');
require_once(JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_jcart' . DIRECTORY_SEPARATOR . 'config.php');

class N2ElementJCartLanguages extends N2ElementList
{

    function fetchElement()
    {
        $db = JFactory::getDBO();

        $query = 'SELECT language_id, name FROM ' . DB_PREFIX . 'language';

        $db->setQuery($query);
        $options = $db->loadObjectList();

        $this->_xml->addChild('option', htmlspecialchars(n2_('All')))
            ->addAttribute('value', '0');

        if (count($options)) {
            foreach ($options AS $option) {
                $this->_xml->addChild('option', htmlspecialchars($option->name))
                    ->addAttribute('value', $option->language_id);
            }
        }
        return parent::fetchElement();
    }

}
