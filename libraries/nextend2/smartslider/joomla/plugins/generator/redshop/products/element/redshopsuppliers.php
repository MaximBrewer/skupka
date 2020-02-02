<?php

N2Loader::import('libraries.form.element.list');

class N2ElementRedShopSuppliers extends N2ElementList
{

    function fetchElement() {

        $model = new N2Model('redshop_supplier');

        $query = 'SELECT supplier_name, supplier_id FROM #__redshop_supplier WHERE published = 1 ORDER BY supplier_id';

        $suppliers = $model->db->queryAll($query, false, "object");

        $this->_xml->addChild('option', htmlspecialchars(n2_('All')))
                   ->addAttribute('value', 0);

        if (count($suppliers)) {
            foreach ($suppliers AS $supplier) {
                $this->_xml->addChild('option', htmlspecialchars($supplier->supplier_name))
                           ->addAttribute('value', $supplier->supplier_id);
            }
        }
        return parent::fetchElement();
    }

}
