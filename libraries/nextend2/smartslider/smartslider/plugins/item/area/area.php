<?php
N2Loader::import('libraries.slider.slides.slide.item.itemFactoryAbstract', 'smartslider');

class N2SSPluginItemFactoryArea extends N2SSPluginItemFactoryAbstract {

    public $type = 'area';

    protected $priority = 100;

    protected $class = 'N2SSItemArea';

    protected $layerProperties = array(
        "desktopportraitwidth"  => 150,
        "desktopportraitheight" => 150
    );

    protected $group = 'Advanced';

    public function __construct() {
        $this->_title = n2_x('Area', 'Slide item');
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->type . DIRECTORY_SEPARATOR;
    }

    function getValues() {
        return array(
            'width'        => '',
            'height'       => '',
            'color'        => '000000ff',
            'gradient'     => 'off',
            'color2'       => '000000ff',
            'css'          => '',
            'borderWidth'  => 0,
            'borderColor'  => 'ffffff1f',
            'borderRadius' => 0,
            'link'         => '#|*|_self'
        );
    }

    public function prepareExport($export, $data) {
        $export->addLightbox($data->get('link'));
    }

    public function prepareImport($import, $data) {
        $data->set('link', $import->fixLightbox($data->get('link')));

        return $data;
    }

}

N2Plugin::addPlugin('ssitem', 'N2SSPluginItemFactoryArea');
