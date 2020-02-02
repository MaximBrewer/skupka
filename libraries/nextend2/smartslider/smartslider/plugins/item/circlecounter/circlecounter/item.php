<?php
N2Loader::import('libraries.slider.slides.slide.itemFactory', 'smartslider');

class N2SSItemCircleCounter extends N2SSItemAbstract {

    protected $type = 'circlecounter';

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

        $value       = intval($this->data->get('value'));
        $min         = min(0, $value);
        $strokewidth = intval($this->data->get('strokewidth'));
        $size        = max(intval($this->data->get('size')), $strokewidth + 1);
        $startvalue  = max(intval($this->data->get('startvalue')), $min);
        $total       = max(max(intval($this->data->get('total')), $startvalue), $value);
        $duration    = max(0, intval($this->data->get('animationduration')));

        $center      = $size / 2;
        $r           = ($size - $strokewidth) / 2;
        $c           = pi() * $r * 2;
        $fromPercent = (min($startvalue, $total) - $min) / ($total - $min);
        $toPercent   = (min($value, $total) - $min) / ($total - $min);
        if ($duration == 0 || $this->isEditor) {
            // We do not have animation
            $fromPercent = $toPercent;
        }

        $pct = (1 - $fromPercent) * $c;

        $label     = $this->data->get('label');
        $placement = '';
        if (!empty($label)) {
            $fontLabel = N2FontRenderer::render($this->data->get('fontlabel'), 'simple', $slider->elementId, 'div#' . $slider->elementId . ' .n2-ss-layer ', $slider->fontSize);

            $labelHTML = N2Html::tag('div', array(
                'class' => $fontLabel
            ), $label);
            $placement = $this->data->get('labelplacement');
        }

        $html = '';

        if ($placement == 'before') {
            $html .= $labelHTML;
        }

        $font = N2FontRenderer::render($this->data->get('font'), 'simple', $slider->elementId, 'div#' . $slider->elementId . ' .n2-ss-layer ', $slider->fontSize);

        $pre             = $this->data->get('pre');
        $post            = $this->data->get('post');
        $countingDivHTML = N2Html::tag('div', array(
            'class' => 'n2-ss-item-circlecounter-counting-div n2-ow ' . $font
        ), $pre . round($min + $fromPercent * ($total - $min)) . $post);

        $html .= N2Html::openTag('div', array(
            'id'    => $this->id,
            'class' => 'n2-ow n2-ss-item-circlecounter-svg-container',
            'style' => 'width:' . $size . 'px;'
        ));

        $color  = $this->data->get('color');
        $color2 = $this->data->get('color2');
        $html .= '<svg class="svg" viewBox="0 0 ' . $size . ' ' . $size . '" version="1.1" preserveAspectRatio="xMinYMin meet">';
        $html .= '<circle class="fl-bar-bg" r="' . $r . '" cx="' . $center . '" cy="' . $center . '" stroke="#' . substr($color, 0, 6) . '" stroke-opacity="' . N2Color::hex2alpha($color) / 127 . '" stroke-width="' . $strokewidth . '" stroke-dashoffset="0" stroke-dasharray="' . $c . '" stroke-dashoffset="0" fill="transparent"></circle>';
        $html .= '<circle class="fl-bar" r="' . $r . '" cx="' . $center . '" cy="' . $center . '" stroke="#' . substr($color2, 0, 6) . '" stroke-opacity="' . N2Color::hex2alpha($color2) / 127 . '" stroke-width="' . $strokewidth . '" stroke-dasharray="' . $c . '" stroke-dashoffset="' . $pct . '" transform="rotate(-90 ' . $center . ' ' . $center . ')" fill="transparent"></circle>';
        $html .= '</svg>';

        $html .= N2Html::openTag('div', array(
            'class' => 'n2-ow n2-ss-item-circlecounter-svg-overlay'
        ));

        if ($placement == 'innerbefore') {
            $html .= $labelHTML;
        }

        $html .= $countingDivHTML;


        if ($placement == 'innerafter') {
            $html .= $labelHTML;
        }

        $html .= '</div>';
        $html .= '</div>';


        if ($placement == 'after') {
            $html .= $labelHTML;
        }

        if ($duration != 0) {

            $jsData = array(
                'pre'         => $pre,
                'post'        => $post,
                'fromPercent' => $fromPercent,
                'toPercent'   => $toPercent,
                'duration'    => $duration,
                'delay'       => $this->data->get('animationdelay'),
                'min'         => $min,
                'total'       => $total,
                'c'           => $c,
                'counting'    => '.n2-ss-item-circlecounter-counting-div',
                'displayMode' => 'circle',
                'display'     => 'circle:eq(1)'
            );
            if (!$this->isEditor) {
                $slider->features->addInitCallback('new N2Classes.FrontendItemCounter(arguments[0], "' . $this->id . '", ' . json_encode($jsData) . ');');
            }
        }

        return $html;
    }

    public function loadResources($slider) {
        N2LESS::addFile(dirname(__FILE__) . "/circlecounter.n2less", $slider->cacheId, array(
            "sliderid" => $slider->elementId
        ), NEXTEND_SMARTSLIDER_ASSETS . '/less' . NDS);
    }
}
