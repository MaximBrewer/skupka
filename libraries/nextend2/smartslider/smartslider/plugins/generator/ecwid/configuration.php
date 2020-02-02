<?php

class N2SliderGeneratorEcwidConfiguration
{

    private $configuration;

    /**
     * @param $info N2GeneratorInfo
     */
    public function __construct($info) {
        $this->configuration = new N2Data(array(
            'storeID' => ''
        ));

        $this->configuration->loadJSON(N2Base::getApplication('smartslider')->storage->get('ecwid'));
    }

    public function wellConfigured() {
        return $this->isValidStoreID();
    }

    public function getData() {
        return $this->configuration->toArray();
    }

    public function addData($data, $store = true) {
        $this->configuration->loadArray($data);
        if ($store) {
            N2Base::getApplication('smartslider')->storage->set('ecwid', null, json_encode($this->configuration->toArray()));
        }
    }

    public function render() {
        $form = new N2Form();
        $form->loadArray($this->getData());

        $form->loadXMLFile(dirname(__FILE__) . '/configuration.xml');

        $this->checkStoreID();

        echo $form->render('generator');
    }

    public function getStoreID() {
        return $this->configuration->get('storeID');
    }

    private function isValidStoreID() {
        $storeID = $this->configuration->get('storeID');
        if (!empty($storeID)) {
            $categories   = "http://app.ecwid.com/api/v1/" . $storeID . "/categories";
            $categoryList = @file_get_contents($categories);
            if ($categoryList === false) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    public function checkStoreID() {
        if (!$this->isValidStoreID()) {
            N2Message::error(n2_('The store ID is not valid!'));
            return false;
        }
        return true;
    }

}
