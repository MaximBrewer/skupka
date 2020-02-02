<?php
N2Loader::import('libraries.slider.slides.slide.itemFactory', 'smartslider');

class N2SSItemImageArea extends N2SSItemAbstract {

    protected $type = 'imagearea';

    public function render() {

        return $this->getLink($this->getHtml(), array(
            'style' => 'display: block; width:100%;height:100%;',
            'class' => 'n2-ow'
        ));
    }

    public function _renderAdmin() {
        return $this->getHtml();
    }

    private function getHtml() {
        $slide = $this->layer->getSlide();

        return N2Html::tag('span', array(
            'class' => 'n2-ow',
            'style' => 'display:inline-block;vertical-align:top;width:100%;height:100%;background: url(' . N2ImageHelper::fixed($slide->fill($this->data->get('image', ''))) . ') no-repeat;background-size:' . $this->data->get('fillmode', 'cover') . ';background-position: ' . $this->data->get('positionx', 50) . '% ' . $this->data->get('positiony', 50) . '%;'
        ));
    }

    public function needSize() {
        return true;
    }
}
