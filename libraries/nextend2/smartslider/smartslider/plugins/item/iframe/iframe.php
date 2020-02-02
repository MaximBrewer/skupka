<?php
N2Loader::import('libraries.slider.slides.slide.item.itemFactoryAbstract', 'smartslider');

class N2SSPluginItemFactoryIFrame extends N2SSPluginItemFactoryAbstract {

    var $type = 'iframe';

    protected $priority = 100;

    protected $layerProperties = array(
        "desktopportraitwidth"  => 300,
        "desktopportraitheight" => 300
    );

    protected $group = 'Advanced';

    protected $class = 'N2SSItemIframe';

    public function __construct() {
        $this->_title = n2_x('iframe', 'Slide item');
    }

    function getValues() {
        return array(
            'url'    => 'https://smartslider3.com',
            'size'   => '100%|*|100%',
            'scroll' => 'yes'
        );
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->type . DIRECTORY_SEPARATOR;
    }

    public function getFilled($slide, $data) {
        $data->set('url', $slide->fill($data->get('url', '')));

        return $data;
    }
}

N2Plugin::addPlugin('ssitem', 'N2SSPluginItemFactoryIFrame');
