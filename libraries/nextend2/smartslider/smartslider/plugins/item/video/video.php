<?php
N2Loader::import('libraries.slider.slides.slide.item.itemFactoryAbstract', 'smartslider');

class N2SSPluginItemFactoryVideo extends N2SSPluginItemFactoryAbstract {

    public $type = 'video';

    protected $layerProperties = array(
        "desktopportraitwidth"  => 300,
        "desktopportraitheight" => 180
    );

    protected $priority = 20;

    protected $group = 'Media';

    protected $class = 'N2SSItemVideo';

    public function __construct() {
        $this->_title = n2_x('Video', 'Slide item');
    }

    /**
     * @return array
     */
    function getValues() {
        return array(
            'autoplay'     => 0,
            'video_mp4'    => '',
            'showcontrols' => 1,
            'volume'       => 1,
            'autoplay'     => 0,
            'center'       => 0,
            'loop'         => 0,
            'reset'        => 0,
            'videoplay'    => '',
            'videopause'   => '',
            'videoend'     => ''
        );
    }

    /**
     * @return string
     */
    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->type . DIRECTORY_SEPARATOR;
    }

    public function getFilled($slide, $data) {
        $data->set('poster', $slide->fill($data->get('poster', '')));
        $data->set('video_mp4', $slide->fill($data->get('video_mp4', '')));

        return $data;
    }

    public function prepareExport($export, $data) {
        $export->addImage($data->get('poster'));
        $export->addImage($data->get('video_mp4'));
    }

    public function prepareImport($import, $data) {
        $data->set('poster', $import->fixImage($data->get('poster')));
        $data->set('video_mp4', $import->fixImage($data->get('video_mp4')));

        return $data;
    }

    public function prepareSample($data) {
        $data->set('poster', N2ImageHelper::fixed($data->get('poster')));
        $data->set('video_mp4', N2ImageHelper::fixed($data->get('video_mp4')));

        return $data;
    }

}

N2Plugin::addPlugin('ssitem', 'N2SSPluginItemFactoryVideo');
