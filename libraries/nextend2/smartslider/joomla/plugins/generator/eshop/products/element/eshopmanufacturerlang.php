<?php

N2Loader::import('libraries.form.element.list');

class N2ElementEShopManufacturerlang extends N2ElementList
{

    function fetchElement() {

        $model = new N2Model("eshop_manufacturerdetails");

        $query = 'SELECT language
                  FROM #__eshop_manufacturerdetails
                  GROUP BY language';

        $languages = $model->db->queryAll($query, false, "object");

        $this->_xml->addChild('option', htmlspecialchars(n2_('Default')))
                   ->addAttribute('value', 0);
        if (count($languages)) {
            foreach ($languages AS $language) {
                $this->_xml->addChild('option', htmlspecialchars($language->language))
                           ->addAttribute('value', $language->language);
            }
        }
        return parent::fetchElement();
    }

}
