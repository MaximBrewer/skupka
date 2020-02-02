<?php
N2Form::importElement('hidden');
N2Loader::import('libraries.splittextanimation.manager', 'smartslider');

class N2ElementSplitTextAnimation extends N2ElementHidden {

    public $_tooltip = true;

    function fetchElement() {

        N2JS::addInline('new N2Classes.FormElementSplitTextAnimationManager("' . $this->_id . '", {
        font: "' . N2XmlHelper::getAttribute($this->_xml, 'font') . '",
        style: "' . N2XmlHelper::getAttribute($this->_xml, 'style') . '",
        preview: ' . json_encode((string)$this->_xml) . ',
        group: "' . N2XmlHelper::getAttribute($this->_xml, 'group') . '",
        transformOrigin: "' . N2XmlHelper::getAttribute($this->_xml, 'transformorigin') . '"
    });');

        return N2Html::tag('div', array(
            'class' => 'n2-form-element-option-chooser n2-border-radius'
        ), parent::fetchElement() . N2Html::tag('input', array(
                'type'     => 'text',
                'class'    => 'n2-h5',
                'style'    => N2XmlHelper::getAttribute($this->_xml, 'css'),
                'disabled' => 'disabled'
            ), false) . N2Html::tag('a', array(
                'href'  => '#',
                'class' => 'n2-form-element-clear'
            ), N2Html::tag('i', array('class' => 'n2-i n2-it n2-i-empty n2-i-grey-opacity'), '')) . N2Html::tag('a', array(
                'href'  => '#',
                'class' => 'n2-form-element-button n2-icon-button n2-h5 n2-uc'
            ), '<i class="n2-i n2-it n2-i-animation"></i>'));
    }
}
