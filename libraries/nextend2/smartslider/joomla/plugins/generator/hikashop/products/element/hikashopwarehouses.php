<?php

N2Loader::import('libraries.form.element.list');

class N2ElementHikaShopWarehouses extends N2ElementList
{

    function fetchElement() {
        $model = new N2Model('tags');

        $query = "SELECT warehouse_name, warehouse_id FROM #__hikashop_warehouse WHERE warehouse_published = 1";

        $warehouses = $model->db->queryAll($query, false, "object");

        $this->_xml->addChild('option', 'All')
                   ->addAttribute('value', 0);
        if (count($warehouses)) {
            foreach ($warehouses AS $warehouse) {
                $this->_xml->addChild('option', htmlspecialchars($warehouse->warehouse_name))
                           ->addAttribute('value', $warehouse->warehouse_id);
            }
        }
        return parent::fetchElement();
    }

}
