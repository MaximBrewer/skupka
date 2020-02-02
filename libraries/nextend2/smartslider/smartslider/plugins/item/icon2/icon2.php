<?php
class N2SSPluginItemFactoryIcon2 extends N2SSPluginItemFactoryAbstract {

    public $type = 'icon2';

    protected $priority = 7;

    protected $layerProperties = array("desktopportraitwidth" => 50);

    protected $group = 'Basic';

    protected $class = 'N2SSItemIcon2';

    public function __construct() {
        $this->_title = n2_x('Icon', 'Slide item');
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->type . DIRECTORY_SEPARATOR;
    }

    function getValues() {
        return array(
            'icon'        => 'fa:smile-o',
            'color'       => '00000080',
            'colorhover' => '00000000',
            'size'        => 100,
            'link'        => '#|*|_self',
            'style'       => ''
        );
    }

    public function getFilled($slide, $data) {
        $data->set('icon', $slide->fill($data->get('icon', '')));
        $data->set('link', $slide->fill($data->get('link', '#|*|')));

        return $data;
    }

    public function prepareExport($export, $data) {
        $export->addVisual($data->get('style'));
        $export->addLightbox($data->get('link'));
    }

    public function prepareImport($import, $data) {
        $data->set('style', $import->fixSection($data->get('style')));
        $data->set('link', $import->fixLightbox($data->get('link')));

        return $data;
    }

}

N2Plugin::addPlugin('ssitem', 'N2SSPluginItemFactoryIcon2');
