<?php
N2Loader::import('libraries.slider.slides.slide.item.itemFactoryAbstract', 'smartslider');

class N2SSPluginItemFactoryImageArea extends N2SSPluginItemFactoryAbstract {

    public $type = 'imagearea';

    protected $priority = 6;

    protected $layerProperties = array(
        "desktopportraitwidth"  => 150,
        "desktopportraitheight" => 150
    );

    protected $group = 'Basic';

    protected $class = 'N2SSItemImageArea';

    public function __construct() {
        $this->_title = n2_x('Image area', 'Slide item');
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->type . DIRECTORY_SEPARATOR;
    }

    function getValues() {
        return array(
            'image'     => '$system$/images/placeholder/image.png',
            'link'      => '#|*|_self',
            'fillmode'  => 'cover',
            'positionx' => 50,
            'positiony' => 50
        );
    }

    public function prepareExport($export, $data) {
        $export->addImage($data->get('image'));
        $export->addLightbox($data->get('link'));
    }

    public function prepareImport($import, $data) {
        $data->set('image', $import->fixImage($data->get('image')));
        $data->set('link', $import->fixLightbox($data->get('link')));

        return $data;
    }

    public function prepareSample($data) {
        $data->set('image', N2ImageHelper::fixed($data->get('image')));

        return $data;
    }

}

N2Plugin::addPlugin('ssitem', 'N2SSPluginItemFactoryImageArea');
