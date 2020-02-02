<?php
N2Loader::import('libraries.slider.slides.slide.itemFactory', 'smartslider');

class N2SSItemVideo extends N2SSItemAbstract {

    protected $type = 'video';

    public function render() {
        $slide  = $this->layer->getSlide();
        $slider = $slide->getSlider();
        N2JS::addInline('window["' . $slider->elementId . '"].ready(function(){
            var video = new N2Classes.FrontendItemVideo(this, "' . $this->id . '", ' . $this->data->toJSON() . ');
        });');

        return N2Html::tag("video", $this->setOptions($this->data, $this->id), $this->setContent($slide, $this->data));
    }

    public function _renderAdmin() {
        return N2Html::tag('div', array(
            "class" => 'n2-ow',
            "style" => 'width: 100%; height: 100%; background: url(' . N2ImageHelper::fixed('$system$/images/placeholder/video.png') . ') no-repeat 50% 50%; background-size: cover;'
        ));
    }

    private function setOptions($data, $id) {
        $videoOptions = array(
            'style'        => 'width: 100%; height: 100%;',
            'class'        => 'n2-ow',
            'encode'       => false,
            'controlsList' => 'nodownload'
        );

        $videoOptions["data-volume"] = $data->get("volume", 1);


        $videoOptions["id"] = $id;

        if ($data->get("showcontrols")) {
            $videoOptions["controls"] = "yes";
        }

        $poster = $data->get("poster");
        if (!empty($poster)) {
            $videoOptions["poster"] = N2ImageHelper::fixed($poster);
        }

        $videoOptions["preload"] = $data->get("preload", "auto");

        return $videoOptions;
    }

    private function setContent($slide, $data) {
        $videoContent = "";

        if ($data->get("video_mp4", false)) {
            $videoContent .= N2Html::tag("source", array(
                "src"  => N2ImageHelper::fixed($slide->fill($data->get("video_mp4"))),
                "type" => "video/mp4"
            ), '', false);
        }

        return $videoContent;
    }

    public function needSize() {
        return true;
    }
}
