<?php
N2Loader::import('libraries.slider.slides.slide.item.itemFactoryAbstract', 'smartslider');

class N2SSPluginItemFactoryAudio extends N2SSPluginItemFactoryAbstract {

    public $type = 'audio';

    protected $layerProperties = array(
        "desktopportraitwidth" => 300
    );

    protected $priority = 21;

    protected $group = 'Media';

    protected $class = 'N2SSItemAudio';

    public function __construct() {
        $this->_title = n2_x('Audio', 'Slide item');
    }

    /**
     * @return array
     */
    function getValues() {
        return array(
            'audio_mp3'  => '',
            'volume'     => 1,
            'autoplay'   => 0,
            'loop'       => 0,
            'reset'      => 0,
            'color'      => '000000B2',
            'color2'     => 'ffffff',
            'videoplay'  => '',
            'videopause' => '',
            'videoend'   => ''
        );
    }

    /**
     * @return string
     */
    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->type . DIRECTORY_SEPARATOR;
    }

    public function getFilled($slide, $data) {
        $data->set('audio', $slide->fill($data->get('audio', '')));

        return $data;
    }

    public function prepareExport($export, $data) {
        $export->addImage($data->get('audio'));
    }

    public function prepareImport($import, $data) {
        $data->set('audio', $import->fixImage($data->get('audio')));

        return $data;
    }

    public function prepareSample($data) {
        $data->set('audio', N2ImageHelper::fixed($data->get('audio')));

        return $data;
    }

    public function loadResources($slider) {
        parent::loadResources($slider);

        N2LESS::addFile($this->getPath() . "/audio.n2less", $slider->cacheId, array(
            "sliderid" => $slider->elementId
        ), NEXTEND_SMARTSLIDER_ASSETS . '/less' . NDS);
    }

}

N2Plugin::addPlugin('ssitem', 'N2SSPluginItemFactoryAudio');
