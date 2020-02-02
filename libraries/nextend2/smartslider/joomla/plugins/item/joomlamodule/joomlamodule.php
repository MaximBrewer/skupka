<?php
N2Loader::import('libraries.slider.slides.slide.item.itemFactoryAbstract', 'smartslider');

class N2SSPluginItemFactoryJoomlaModule extends N2SSPluginItemFactoryAbstract {

    var $type = 'joomlamodule';

    protected $priority = 101;

    protected $group = 'Advanced';

    protected $class = 'N2SSItemJoomlaModule';

    public function __construct() {
        $this->_title = n2_x('Module', 'Slide item');
    }

    function getValues() {
        return array(
            'positiontype'  => 'loadposition',
            'positionvalue' => ''
        );
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->type . DIRECTORY_SEPARATOR;
    }
}

N2Plugin::addPlugin('ssitem', 'N2SSPluginItemFactoryJoomlaModule');
