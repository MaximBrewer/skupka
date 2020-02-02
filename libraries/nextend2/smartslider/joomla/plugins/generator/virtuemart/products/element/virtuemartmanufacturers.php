<?php

N2Loader::import('libraries.form.element.list');

class N2ElementVirtueMartManufacturers extends N2ElementList
{

    function fetchElement() {
        $model = new N2Model('virtuemart_manufacturers');
        require_once(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_virtuemart' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'config.php');
        VmConfig::loadConfig();
        $query = 'SELECT virtuemart_manufacturer_id AS id, mf_name AS name FROM #__virtuemart_manufacturers_' . VMLANG . ' ORDER BY id';

        $manufacturers = $model->db->queryAll($query, false, "object");

        $this->_xml->addChild('option', htmlspecialchars(n2_('All')))
                   ->addAttribute('value', 0);
        if (count($manufacturers)) {
            foreach ($manufacturers AS $manufacturer) {
                $this->_xml->addChild('option', htmlspecialchars($manufacturer->name))
                           ->addAttribute('value', $manufacturer->id);
            }
        }
        return parent::fetchElement();
    }

}
