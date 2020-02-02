<?php

N2Loader::import('libraries.form.element.list');

class N2ElementEShopManufacturers extends N2ElementList
{

    function fetchElement() {

        $model = new N2Model("eshop_manufacturers");

        $query = 'SELECT manufacturer_name, manufacturer_id
                  FROM #__eshop_manufacturerdetails
                  ORDER BY manufacturer_id';

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
