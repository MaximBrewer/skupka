<?php

N2Loader::import('libraries.slider.slides.slide.itemFactory', 'smartslider');

class N2SSItemHeading extends N2SSItemAbstract {

    protected $type = 'heading';

    public function render() {
        return $this->getHtml();
    }

    public function _renderAdmin() {
        return $this->getHtml();
    }

    private function getHtml() {
        $slide  = $this->layer->getSlide();
        $slider = $slide->getSlider();

        $attributes = array();
        $inDelay  = $this->data->get('split-text-delay-in', 0) / 1000;
        $outDelay = $this->data->get('split-text-delay-out', 0) / 1000;

        $in  = $this->data->get('split-text-animation-in', '');
        $out = $this->data->get('split-text-animation-out', '');

        $transformOrigin    = implode('% ', explode('|*|', $this->data->get('split-text-transform-origin', '50|*|50|*|0'))) . 'px';
        $backfaceVisibility = $this->data->get('split-text-backface-visibility', 1) ? 'visible' : 'hidden';

        if (!empty($in) || !empty($out)) {
            if ($this->layer->getAttribute('data-animations') === null) {
                $this->layer->setAttribute('data-animations', '1');
            }
            if ($this->isEditor) {
                $slider->features->addInitCallback('new N2Classes.HeadingItemSplitTextAdmin(arguments[0], "' . $this->id . '", "' . $transformOrigin . '", "' . $backfaceVisibility . '",  "' . $in . '",' . $inDelay . ', "' . $out . '", ' . $outDelay . ');');
            } else {

                if (!empty($in)) {
                    $in = base64_decode($in);
                } else {
                    $in = 'false';
                }
                if (!empty($out)) {
                    $out = base64_decode($out);
                } else {
                    $out = 'false';
                }

                $slider->features->addInitCallback('new N2Classes.FrontendItemHeadingSplitText(arguments[0], "' . $this->id . '", "' . $transformOrigin . '", "' . $backfaceVisibility . '",  ' . $in . ',' . $inDelay . ', ' . $out . ', ' . $outDelay . ');');
            }
        }
    

        $font  = N2FontRenderer::render($this->data->get('font'), 'hover', $slider->elementId, 'div#' . $slider->elementId . ' .n2-ss-layer ', $slider->fontSize);
        $style = N2StyleRenderer::render($this->data->get('style'), 'heading', $slider->elementId, 'div#' . $slider->elementId . ' ');

        $linkAttributes = array(
            'class' => 'n2-ow'
        );
        if ($this->isEditor) {
            $linkAttributes['onclick'] = 'return false;';
        }

        $title = $this->data->get('title', '');
        if (!empty($title)) {
            $attributes['title'] = $title;
        }

        list($link) = (array)N2Parse::parse($this->data->get('link', '#|*|'));
        if (!empty($link) && $link != '#') {
            $linkAttributes['class'] .= ' ' . $font . $style;

            $font  = '';
            $style = '';
        }

        $linkAttributes['style'] = "display:" . ($this->data->get('fullwidth', 1) ? 'block' : 'inline-block') . ";";

        return $this->heading($this->data->get('priority', 'div'), $attributes + array(
                "id"    => $this->id,
                "class" => $font . $style . " " . $this->data->get('class', '') . ' n2-ow',
                "style" => "display:" . ($this->data->get('fullwidth', 1) ? 'block' : 'inline-block') . ";" . ($this->data->get('nowrap', 0) ? 'white-space:nowrap;' : '')
            ), $this->getLink(str_replace("\n", '<br />', strip_tags($slide->fill($this->data->get('heading', '')))), $linkAttributes));
    }

    private function heading($type, $attributes, $content) {
        if ($type > 0) {
            return N2Html::tag("h{$type}", $attributes, $content);
        }

        return N2Html::tag("div", $attributes, $content);
    }
}