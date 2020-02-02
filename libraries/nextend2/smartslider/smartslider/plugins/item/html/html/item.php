<?php
N2Loader::import('libraries.slider.slides.slide.itemFactory', 'smartslider');

class N2SSItemHTML extends N2SSItemAbstract {

    protected $type = 'html';

    public function render() {
        return $this->getHtml();
    }

    public function _renderAdmin() {
        return $this->getHtml();
    }

    private function getHtml() {
        $slide = $this->layer->getSlide();
        $css   = '';
        if ($cssCode = $this->data->get('css', '')) {
            $css = N2Html::style($cssCode);
        }

        return N2Html::tag("div", array(
            'class' => 'n2-notow'
        ), $this->closeTags('<div style="text-align:' . $this->data->get("textalign") . ';">' . $slide->fill($this->data->get("html")) . '</div>') . $css);
    }

    private function closeTags($html) {

        if (class_exists('tidy', false)) {
            $tidy_config = array(
                'input-xml'           => true,
                'output-xml'          => true,
                'show-body-only'      => true,
                'wrap'                => 0,
                'new-blocklevel-tags' => 'menu,mytag,article,header,footer,section,nav,svg,path,g,a',
                'new-inline-tags'     => 'video,audio,canvas,ruby,rt,rp',
                'doctype'             => '<!DOCTYPE HTML>',
            );
            $tidy        = new tidy();

            return $tidy->repairString($html, $tidy_config, 'UTF8');
        }

        return $html;
    }

}
