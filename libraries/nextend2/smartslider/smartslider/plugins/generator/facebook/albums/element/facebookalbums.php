<?php

N2Loader::import('libraries.form.element.list');
N2Loader::import('libraries.parse.parse');

class N2ElementFacebookAlbums extends N2ElementList {

    function fetchElement() {

        N2JS::addInline('
            new N2Classes.FormElementFacebookAlbums("' . $this->_id . '", "' . N2Base::getApplication('smartslider')->router->createAjaxUrl(array(
                "generator/getData",
                array(
                    'group' => N2Request::getVar('group'),
                    'type'  => N2Request::getVar('type')
                )
            )) . '");
        ');

        /** @var N2GeneratorInfo $info */
        $info = $this->_form->get('info');
        $api  = $info->getConfiguration()
                     ->getApi();
        try {
            /** @var Facebook\FacebookResponse $result */
            $result = $api->get($this->_form->get('facebook-id', 'me') . '/albums')
                          ->getDecodedBody();
            if (count($result['data'])) {
                foreach ($result['data'] AS $album) {
                    $this->_xml->addChild('option', htmlentities($album['name']))
                               ->addAttribute('value', $album['id']);
                }
                if ($this->getValue() == '') {
                    $this->setValue($result['data'][0]['id']);
                }
            }
        } catch (Exception $e) {
            N2Message::error($e->getMessage());
        }

        return parent::fetchElement();
    }

}
