<?php
N2Loader::import('libraries.slider.slides.slide.itemFactory', 'smartslider');

class N2SSItemCaption extends N2SSItemAbstract {

    protected $type = 'caption';

    public function render() {
        return $this->getHtml();
    }

    public function _renderAdmin() {
        return $this->getHtml();
    }

    private function getHtml() {
        $slide  = $this->layer->getSlide();
        $slider = $slide->getSlider();

        $this->loadResources($slider);

        list($mode, $direction, $scale) = N2Parse::parse($this->data->get('animation', 'Simple|*|left|*|0'));
        $slider->features->addInitCallback('new N2Classes.FrontendItemCaption(arguments[0], "' . $this->id . '", "' . $mode . '", "' . $direction . '", ' . intval($scale) . ');');

        $html = N2Html::tag('div', array('class' => 'n2-ss-img-wrapper n2-ow'), N2Html::tag('img', $this->optimizeImage($slide->fill($this->data->get('image', ''))) + array(
                'alt'   => htmlspecialchars($slide->fill($this->data->get('alt', ''))),
                'class' => 'n2-ow'
            ), false));

        list($hex, $rgba) = N2Color::colorToCss($this->data->get('color', '00000080'));
        $html .= N2Html::openTag("div", array(
            "class"              => "n2-ss-item-caption-content n2-ow",
            "style"              => "background:#{$hex}; background: {$rgba};",
            "data-verticalalign" => $this->data->get('verticalalign', 'center')
        ));

        $title = $slide->fill($this->data->get('content', ''));
        if ($title != '') {
            $fontTitle = N2FontRenderer::render($this->data->get('fonttitle'), 'paragraph', $slider->elementId, 'div#' . $slider->elementId . ' ', $slider->fontSize);
            $html .= N2Html::tag("div", array("class" => 'n2-ow n2-div-h4 ' . $fontTitle), $title);
        }

        $description = $slide->fill($this->data->get('description', ''));
        if ($description != '') {
            $font = N2FontRenderer::render($this->data->get('font'), 'paragraph', $slider->elementId, 'div#' . $slider->elementId . ' ', $slider->fontSize);
            $html .= N2Html::tag("p", array("class" => 'n2-ow ' . $font), $description);
        }

        $html .= N2Html::closeTag("div");

        $linkAttributes = array(
            'class' => 'n2-ow'
        );
        if ($this->isEditor) {
            $linkAttributes['onclick'] = 'return false;';
        }

        return N2Html::tag("div", array(
            "id"    => $this->id,
            "class" => "n2-ss-item-caption n2-ow"
        ), $this->getLink($html, $linkAttributes));
    }

    public function loadResources($slider) {
        N2LESS::addFile(dirname(__FILE__) . "/caption.n2less", $slider->cacheId, array(
            "sliderid" => $slider->elementId
        ), NEXTEND_SMARTSLIDER_ASSETS . '/less' . NDS);
    }
}
