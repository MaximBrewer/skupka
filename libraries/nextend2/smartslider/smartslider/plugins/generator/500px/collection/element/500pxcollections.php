<?php

N2Loader::import('libraries.form.element.list');

class N2Element500pxCollections extends N2ElementList
{

    function fetchElement() {

        /** @var N2GeneratorInfo $info */
        $info   = $this->_form->get('info');
        $client = $info->getConfiguration()
                       ->getApi();

        $responseCode = $client->request('GET', $client->url('collections'));
        if ($responseCode == 200) {
            $r           = json_decode($client->response['response'], true);
            $collections = $r['collections'];

            if (count($collections)) {
                foreach ($collections AS $collection) {
                    $this->_xml->addChild('option', htmlentities($collection['title']))
                               ->addAttribute('value', $collection['id']);
                }
                if ($this->getValue() == '') {
                    $this->setValue($collections[0]['id']);
                }
            }
        }

        return parent::fetchElement();
    }

}
