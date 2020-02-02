<?php

N2Loader::import('libraries.form.element.list');

class N2ElementMijoShopLanguages extends N2ElementList
{

    function fetchElement() {

        $model = new N2Model('mijoshop_language');

        $query = 'SELECT lang_id, title
                FROM #__languages
                WHERE published = 1';

        $languages = $model->db->queryAll($query, false, "object");
        $this->_xml->addChild('option', htmlspecialchars(n2_('Automatic')))
                   ->addAttribute('value', '');

        if (count($languages)) {
            foreach ($languages AS $language) {
                $this->_xml->addChild('option', htmlspecialchars($language->title))
                           ->addAttribute('value', $language->lang_id);
            }
        }
        return parent::fetchElement();
    }

}
