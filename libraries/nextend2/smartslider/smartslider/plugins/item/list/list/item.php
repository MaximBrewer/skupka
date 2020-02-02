<?php
N2Loader::import('libraries.slider.slides.slide.itemFactory', 'smartslider');

class N2SSItemList extends N2SSItemAbstract {

    protected $type = 'list';

    public function render() {
        return $this->getHtml();
    }

    public function _renderAdmin() {
        return $this->getHtml();
    }


    private function getHTML() {
        $slide  = $this->layer->getSlide();
        $slider = $slide->getSlider();

        $font      = N2FontRenderer::render($this->data->get('font'), 'list', $slider->elementId, 'div#' . $slider->elementId . ' ', $slider->fontSize);
        $listStyle = N2StyleRenderer::render($this->data->get('liststyle'), 'heading', $slider->elementId, 'div#' . $slider->elementId . ' ');
        $itemStyle = N2StyleRenderer::render($this->data->get('itemstyle'), 'heading', $slider->elementId, 'div#' . $slider->elementId . ' ');


        $html = '';
        $lis  = explode("\n", $slide->fill($this->data->get('content', '')));
        foreach ($lis AS $li) {
            $html .= '<li class="' . $itemStyle . ' n2-ow">' . $li . '</li>';
        }

        return N2Html::tag('ol', array(
            'class' => $font . '' . $listStyle . ' n2-ow',
            'style' => "list-style-type:" . $this->data->get('type')
        ), $html);
    }
}
