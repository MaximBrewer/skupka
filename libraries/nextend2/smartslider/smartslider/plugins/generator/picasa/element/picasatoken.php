<?php

N2Loader::import('libraries.form.element.text');

class N2ElementPicasaToken extends N2ElementText
{

    function fetchElement() {

        N2JS::addInline('new N2Classes.FormElementPicasaToken("' . $this->_id . '", "' . N2Base::getApplication('smartslider')->router->createAjaxUrl(array(
                "generator/getAuthUrl",
                array(
                    'group' => N2Request::getVar('group'),
                    'type'  => N2Request::getVar('type')
                )
            )) . '", "' . N2Base::getApplication('smartslider')->router->createUrl(array(
                "generator/finishauth",
                array(
                    'group' => 'picasa'
                )
            )) . '");');

        return parent::fetchElement();
    }

    protected function post() {
        return '<a id="' . $this->_id . '_button" class="n2-form-element-button n2-h5 n2-uc" href="#">' . n2_('Request token') . '</a>';
    }
}


