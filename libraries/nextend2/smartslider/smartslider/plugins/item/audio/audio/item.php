<?php
N2Loader::import('libraries.slider.slides.slide.itemFactory', 'smartslider');

class N2SSItemAudio extends N2SSItemAbstract {

    protected $type = 'audio';

    protected $controls = array(
        '<div class="n2-ss-item-audio-play"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32"><path fill="{color2}" d="M20 15.99c0 .41-.21.772-.52.967l-6.867 4.87c-.003 0-.006.002-.01.004l-.003.004c-.158.1-.342.156-.54.156-.585 0-1.06-.504-1.06-1.125v-9.752c0-.622.475-1.126 1.06-1.126.198 0 .382.058.54.157l.004.002.01.006 6.865 4.868c.31.196.52.556.52.97z"></path></svg></div>',
        '<div class="n2-ss-item-audio-pause"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32"><path fill="{color2}" d="M17 22V10h4v12h-4zm-6-12h4v12h-4V10z"></path></svg></div>',
        '<div class="n2-ss-item-audio-progress-container"><div class="n2-ss-item-audio-progress" style="background:{bar};"><div style="background:{color2};" class="n2-ss-item-audio-progress-playhead"></div></div></div>',
        '<div class="n2-ss-item-audio-time" style="color:{color2};">00:00 / 00:00</div>',
        '<div class="n2-ss-item-audio-unmute"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32"><path fill="{color2}" d="M15 22h-1l-4-4H9c-.45 0-1-.527-1-1v-3c0-.474.55-1 1-1h1l4-4h1v13z"/></svg></div>',
        '<div class="n2-ss-item-audio-mute"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32"><path fill="{color2}" d="M15 22h-1l-4-4H9c-.45 0-1-.527-1-1v-3c0-.474.55-1 1-1h1l4-4h1v13zm3.147-1.954l-.06.002c-.215 0-.423-.09-.577-.25l-.11-.116c-.286-.3-.32-.776-.078-1.117.612-.865.935-1.892.935-2.968 0-1.158-.367-2.244-1.06-3.14-.266-.342-.24-.837.055-1.148l.11-.115c.162-.172.38-.265.618-.25.23.012.446.126.592.313.963 1.236 1.472 2.737 1.472 4.34 0 1.49-.45 2.912-1.3 4.106-.143.2-.36.324-.597.342zm3.38 2.65c-.15.183-.363.293-.59.303H20.9c-.215 0-.423-.09-.577-.25l-.107-.114c-.3-.314-.32-.817-.048-1.158 1.32-1.644 2.045-3.733 2.045-5.88 0-2.236-.778-4.387-2.19-6.058-.285-.34-.27-.853.034-1.174l.107-.112c.16-.168.365-.26.603-.252.225.006.438.11.587.287C23.06 10.303 24 12.9 24 15.596c0 2.595-.878 5.116-2.474 7.1z"></path></svg></div>',
        '<div class="n2-ss-item-audio-volume-container"><div class="n2-ss-item-audio-volume" style="background:{bar};"><div style="background:{color2};" class="n2-ss-item-audio-volumehead"></div></div></div>'
    );

    public function render() {
        $slide  = $this->layer->getSlide();
        $slider = $slide->getSlider();

        N2JS::addInline('window["' . $slider->elementId . '"].ready(function(){
            new N2Classes.FrontendItemAudio(this, "' . $this->id . '", ' . $this->data->toJSON() . ');
        });');


        return $this->getHTML();
    }

    public function _renderAdmin() {
        return $this->getHTML();
    }

    public function getHTML() {
        $slide  = $this->layer->getSlide();
        $slider = $slide->getSlider();

        $this->loadResources($slider);

        return N2HTML::tag('div', array(
            'class'       => 'n2-ss-item-audio-bar n2-ow n2-ow-all',
            'id'          => $this->id,
            'data-state'  => 'paused',
            'data-volume' => '1',
            'style'       => 'background-color:' . N2Color::colorToRGBA($this->data->get('color')) . ';'
        ), $this->getAudioHTML($slide, $this->data) . str_replace(array(
                '{bar}',
                '{color2}'
            ), array(
                N2Color::colorToRGBA($this->data->get('color2') . '33'),
                '#' . $this->data->get('color2')
            ), implode('', $this->controls)));
    }

    private function getAudioHTML($slide, $data) {
        $attributes = array();

        if ($data->get("volume", 1) == 0) {
            $attributes['muted'] = true;
        }

        return N2Html::tag("audio", $attributes, $this->setContent($slide, $this->data));
    }

    private function setContent($slide, $data) {
        $videoContent = "";

        if ($data->get("audio_mp3", false)) {
            $videoContent .= N2Html::tag("source", array(
                "src"  => N2ImageHelper::fixed($slide->fill($data->get("audio_mp3"))),
                "type" => "audio/mpeg"
            ), '', false);
        }

        return $videoContent;
    }

    public function loadResources($slider) {
        N2LESS::addFile(dirname(__FILE__) . "/audio.n2less", $slider->cacheId, array(
            "sliderid" => $slider->elementId
        ), NEXTEND_SMARTSLIDER_ASSETS . '/less' . NDS);
    }
}
