<?php

N2Loader::import('libraries.form.element.list');

class N2ElementRedShopManufacturers extends N2ElementList
{

    function fetchElement() {

        $model = new N2Model('redshop_manufacturer');

        $query = 'SELECT manufacturer_name, manufacturer_id FROM #__redshop_manufacturer WHERE published = 1 ORDER BY ordering';

        $manufacturers = $model->db->queryAll($query, false, "object");

        $this->_xml->addChild('option', htmlspecialchars(n2_('All')))
                   ->addAttribute('value', 0);

        if (count($manufacturers)) {
            foreach ($manufacturers AS $manufacturer) {
                $this->_xml->addChild('option', htmlspecialchars($manufacturer->manufacturer_name))
                           ->addAttribute('value', $manufacturer->manufacturer_id);
            }
        }
        return parent::fetchElement();
    }

}
