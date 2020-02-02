<?php
N2Loader::import('libraries.slider.slides.slide.item.itemFactoryAbstract', 'smartslider');

class N2SSPluginItemFactoryTransition extends N2SSPluginItemFactoryAbstract {

    var $type = 'transition';

    protected $priority = 7;

    protected $layerProperties = array("desktopportraitwidth" => 200);

    protected $group = 'Content';

    protected $class = 'N2SSItemTransition';

    public function __construct() {
        $this->_title = n2_x('Transition', 'Slide item');
    }

    public function loadResources($slider) {
        parent::loadResources($slider);

        N2LESS::addFile($this->getPath() . "/transition.n2less", $slider->cacheId, array(
            "sliderid" => $slider->elementId
        ), NEXTEND_SMARTSLIDER_ASSETS . '/less' . NDS);
    }

    function getValues() {
        return array(
            'animation'      => 'Fade',
            'image'          => '$system$/images/placeholder/imagefront.png',
            'image2'         => '$system$/images/placeholder/imageback.png',
            'alt'            => n2_('Image not available'),
            'link'           => '#|*|_self',
            'image-optimize' => 1
        );
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->type . DIRECTORY_SEPARATOR;
    }

    public function getFilled($slide, $data) {
        $data->set('image', $slide->fill($data->get('image', '')));
        $data->set('image2', $slide->fill($data->get('image2', '')));
        $data->set('alt', $slide->fill($data->get('alt', '')));
        $data->set('link', $slide->fill($data->get('link', '#|*|')));

        return $data;
    }

    public function prepareExport($export, $data) {
        $export->addImage($data->get('image'));
        $export->addImage($data->get('image2'));
        $export->addLightbox($data->get('link'));
    }

    public function prepareImport($import, $data) {

        $data->set('image', $import->fixImage($data->get('image', '')));
        $data->set('image2', $import->fixImage($data->get('image2', '')));
        $data->set('link', $import->fixLightbox($data->get('link')));

        return $data;
    }

    public function prepareSample($data) {
        $data->set('image', N2ImageHelper::fixed($data->get('image')));
        $data->set('image2', N2ImageHelper::fixed($data->get('image2')));

        return $data;
    }

}

N2Plugin::addPlugin('ssitem', 'N2SSPluginItemFactoryTransition');
