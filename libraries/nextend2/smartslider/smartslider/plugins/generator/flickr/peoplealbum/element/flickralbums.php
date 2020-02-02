<?php

N2Loader::import('libraries.form.element.list');
N2Loader::import('libraries.parse.parse');

class N2ElementFlickrAlbums extends N2ElementList
{

    function fetchElement() {

        /** @var N2GeneratorInfo $info */
        $info   = $this->_form->get('info');
        $client = $info->getConfiguration()
                       ->getApi();

        $result = $client->photosets_getList('');

        if (isset($result['photoset'])) {
            if (count($result['photoset'])) {
                foreach ($result['photoset'] AS $set) {
                    $this->_xml->addChild('option', htmlentities($set['title']))
                               ->addAttribute('value', $set['id']);
                }
                if ($this->getValue() == '') {
                    $this->setValue($result['photoset'][0]['id']);
                }
            }
        }

        return parent::fetchElement();
    }

}
