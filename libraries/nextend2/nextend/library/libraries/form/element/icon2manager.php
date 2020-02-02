<?php
N2Loader::import('libraries.form.element.hidden');
N2Loader::import('libraries.form.form');

N2Loader::import('libraries.icons.icons');

class N2ElementIcon2Manager extends N2ElementHidden {

    public $_tooltip = true;

    function fetchElement() {
        N2Icons::serveAdmin();

        $html = N2Html::tag('div', array(
            'class' => 'n2-form-element-text n2-form-element-icon n2-border-radius'
        ), N2Html::tag('div', array(
                'class' => 'n2-form-element-preview'
            ), '') . (N2XmlHelper::getAttribute($this->_xml, 'clearable') == 1 ? N2Html::tag('a', array(
                'href'  => '#',
                'class' => 'n2-form-element-clear'
            ), N2Html::tag('i', array('class' => 'n2-i n2-it n2-i-empty n2-i-grey-opacity'), '')) : '') . '<a id="' . $this->_id . '_edit" class="n2-form-element-button n2-icon-button n2-h5 n2-uc" href="#"><i class="n2-i n2-it  n2-i-layer-image"></i></a>' . parent::fetchElement());

        N2JS::addInline('
        new N2Classes.FormElementIcon2Manager("' . $this->_id . '");
    ');

        return $html;
    }

}
