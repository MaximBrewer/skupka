<?php
class N2SSPluginItemFactoryIcon extends N2SSPluginItemFactoryAbstract {

    public $type = 'icon';

    protected $priority = 8;

    protected $layerProperties = array("desktopportraitwidth" => 50);

    protected $group = 'Basic';

    protected $class = 'N2SSItemIcon';

    public function __construct() {
        $this->_title = n2_x('Icon - Legacy', 'Slide item');
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->type . DIRECTORY_SEPARATOR;
    }

    function getValues() {
        return array(
            'icon'        => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="32" height="32"><rect width="100" height="100" data-style="{style}" /></svg>',
            'color'       => '00000080',
            'color-hover' => '00000000',
            'size'        => '100%|*|auto',
            'link'        => '#|*|_self',
            'style'       => ''
        );
    }

    public function isLegacy() {
        return true;
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

N2Plugin::addPlugin('ssitem', 'N2SSPluginItemFactoryIcon');
