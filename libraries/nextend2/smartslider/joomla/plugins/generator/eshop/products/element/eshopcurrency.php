<?php

N2Loader::import('libraries.form.element.list');

class N2ElementEShopCurrency extends N2ElementList
{

    function fetchElement() {

        $model = new N2Model("eshop_currencies");

        $query = 'SELECT currency_code
                  FROM #__eshop_currencies
                  ORDER BY id';

        $codes = $model->db->queryAll($query, false, "object");

        $this->_xml->addChild('option', htmlspecialchars(n2_('Default')))
                   ->addAttribute('value', 0);
        if (count($codes)) {
            foreach ($codes AS $code) {
                $this->_xml->addChild('option', htmlspecialchars($code->currency_code))
                           ->addAttribute('value', $code->currency_code);
            }
        }
        return parent::fetchElement();
    }

}
