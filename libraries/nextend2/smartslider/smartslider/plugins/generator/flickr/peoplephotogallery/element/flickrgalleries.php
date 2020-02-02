<?php

N2Loader::import('libraries.form.element.list');
N2Loader::import('libraries.parse.parse');

class N2ElementFlickrGalleries extends N2ElementList
{

    function fetchElement() {

        /** @var N2GeneratorInfo $info */
        $info   = $this->_form->get('info');
        $client = $info->getConfiguration()
                       ->getApi();

        $result = $client->galleries_getList('');

        if (isset($result['galleries']) && isset($result['galleries']['gallery'])) {
            $galleries = $result['galleries']['gallery'];

            if (count($galleries)) {
                foreach ($galleries AS $gallery) {
                    $this->_xml->addChild('option', htmlentities($gallery['title']))
                               ->addAttribute('value', $gallery['id']);
                }
                if ($this->getValue() == '') {
                    $this->setValue($galleries[0]['id']);
                }
            }
        }

        return parent::fetchElement();
    }

}
