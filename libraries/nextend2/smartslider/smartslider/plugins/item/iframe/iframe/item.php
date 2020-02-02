<?php
N2Loader::import('libraries.slider.slides.slide.itemFactory', 'smartslider');

class N2SSItemIframe extends N2SSItemAbstract {

    protected $type = 'iframe';

    public function render() {
        return $this->getHtml();
    }

    public function _renderAdmin() {
        return $this->getHtml();
    }

    private function getHtml() {
        $slide  = $this->layer->getSlide();
        $slider = $slide->getSlider();

        $size = (array)N2Parse::parse($this->data->get('size', ''));
        if (!isset($size[0])) $size[0] = '100%';
        if (!isset($size[1])) $size[1] = '100%';

        $attrs = array(
            "encode"      => false,
            "frameborder" => 0,
            "class"       => "n2-ow",
            "width"       => $size[0],
            "height"      => $size[1],
            "scrolling"   => $this->data->get("scroll")
        );

        if ($slider->features->lazyLoad->isEnabled) {
            $attrs['data-lazysrc'] = $slide->fill($this->data->get("url"));
        } else {
            $attrs['src'] = $slide->fill($this->data->get("url"));
        }

        return N2Html::tag("iframe", $attrs, "");
    }

    public function needSize() {
        return true;
    }
}
