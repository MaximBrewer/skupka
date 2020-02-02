<?php
N2Loader::import('libraries.slider.slides.slide.item.itemFactoryAbstract', 'smartslider');

class N2SSPluginItemFactoryHTML extends N2SSPluginItemFactoryAbstract {

    var $type = 'html';

    protected $priority = 102;

    protected $layerProperties = array("desktopportraitwidth" => 200);

    protected $group = 'Advanced';

    protected $class = 'N2SSItemHTML';

    public function __construct() {
        $this->_title = n2_x('HTML', 'Slide item');
    }

    function getValues() {
        return array(
            'html'      => '<div>Empty element</div>',
            'css'       => '.selector{

}',
            'textalign' => 'inherit'
        );
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->type . DIRECTORY_SEPARATOR;
    }

    public function getFilled($slide, $data) {
        $data->set('html', $slide->fill($data->get('html', '')));

        return $data;
    }
}

N2Plugin::addPlugin('ssitem', 'N2SSPluginItemFactoryHTML');
