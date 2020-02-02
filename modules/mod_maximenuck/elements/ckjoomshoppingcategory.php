<?php

/**
 * @copyright	Copyright (C) 2011 Cedric KEIFLIN alias ced1870
 * http://www.joomlack.fr
 * Module Maximenu CK
 * @license		GNU/GPL
 * */
defined('JPATH_BASE') or die;
jimport('joomla.filesystem.file');
jimport('joomla.form.formfield');
JFormHelper::loadFieldClass('cklist');

class JFormFieldCkjoomshoppingcategory extends JFormFieldCklist {

    protected $type = 'ckjoomshoppingcategory';

    protected function getOptions() {
        // if the component is not installed
        if (!JFolder::exists(JPATH_ROOT . '/administrator/components/com_jshopping')
                OR !JFile::exists(JPATH_ROOT . '/modules/mod_maximenuck/helper_joomshopping.php')) {
            // add the root item
            $option = new stdClass();
            $option->text = JText::_('MOD_MAXIMENUCK_JOOMSHOPPING_NOTFOUND');
            $option->value = '0';
            $options[] = $option;
            // Merge any additional options in the XML definition.
            $options = array_merge(parent::getOptions(), $options);

            return $options;
        }

        // get the categories form the helper
        $params = new JRegistry();
        require_once JPATH_ROOT . '/modules/mod_maximenuck/helper_joomshopping.php';
        $cats = modMaximenuckjoomshoppingHelper::getItems($params, true);

        // add the root item
        $option = new stdClass();
        $option->text = JText::_('MOD_MAXIMENUCK_JOOMSHOPPING_ROOTNODE');
        $option->value = '0';
        $options[] = $option;
        foreach ($cats as $cat) {
            $option = new stdClass();
            $option->text = str_repeat(" - ", $cat->level - 1) . $cat->name;
            $option->value = $cat->category_id;
            $options[] = $option;
        }
        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }

}
