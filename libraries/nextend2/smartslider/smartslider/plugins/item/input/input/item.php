<?php
N2Loader::import('libraries.slider.slides.slide.itemFactory', 'smartslider');

class N2SSItemInput extends N2SSItemAbstract {

    protected $type = 'input';

    public function render() {
        $slide  = $this->layer->getSlide();
        $slider = $slide->getSlider();

        $style = N2StyleRenderer::render($this->data->get('style'), 'heading', $slider->elementId, 'div#' . $slider->elementId . ' ');


        $inputFont  = N2FontRenderer::render($this->data->get('inputfont'), 'paragraph', $slider->elementId, 'div#' . $slider->elementId . ' .n2-ss-layer ', $slider->fontSize);
        $inputStyle = N2StyleRenderer::render($this->data->get('inputstyle'), 'heading', $slider->elementId, 'div#' . $slider->elementId . ' .n2-ss-layer ');

        $slider->features->addInitCallback('n2("#' . $this->id . '").closest(".n2-ss-slide").on("' . $this->data->get('submit') . '", function(e){n2("#' . $this->id . '").trigger("submit")})');

        $parameters     = explode('&', $this->data->get('parameters'));
        $parametersHTML = '';
        foreach ($parameters AS $parameter) {
            $parameter = explode('=', $parameter);
            if (count($parameter) == 2) {
                $parametersHTML .= N2Html::tag('input', array(
                    'type'  => 'hidden',
                    'name'  => $parameter[0],
                    'value' => $parameter[1],
                    'class' => 'n2-ow'
                ), false);
            }
        }


        $td2         = '';
        $buttonLabel = $this->data->get('buttonlabel');
        if (!empty($buttonLabel)) {

            $buttonFont  = N2FontRenderer::render($this->data->get('buttonfont'), 'hover', $slider->elementId, 'div#' . $slider->elementId . ' .n2-ss-layer ', $slider->fontSize);
            $buttonStyle = N2StyleRenderer::render($this->data->get('buttonstyle'), 'heading', $slider->elementId, 'div#' . $slider->elementId . ' .n2-ss-layer ');

            $td2 = N2Html::tag('td', array(), N2Html::tag('input', array(
                'style' => 'white-space:nowrap;',
                'type'  => 'submit',
                'value' => $buttonLabel,
                'class' => $buttonFont . ' ' . $buttonStyle . ' n2-ow'
            ), false));
        }


        return N2Html::tag('form', array(
            'class'    => $style . ' n2-ow ' . $this->data->get('class', ''),
            'id'       => $this->id,
            'action'   => $this->data->get('action'),
            'method'   => $this->data->get('method'),
            'target'   => $this->data->get('target'),
            'style'    => 'display: ' . ($this->data->get('fullwidth', 0) ? 'block' : 'inline-block') . '; width: auto;',
            'onsubmit' => $this->data->get('onsubmit')
        ), N2Html::tag('table', array(
            'style'       => 'width:auto',
            'class'       => 'n2-ow',
            'cellpadding' => 0,
            'cellspacing' => 0
        ), N2Html::tag('tr', array(
            'class' => 'n2-ow'
        ), N2Html::tag('td', array(
                'class' => 'n2-ow',
                'style' => ($this->data->get('fullwidth', 0) ? 'width:100%' : '')
            ), N2Html::tag('input', array(
                    'name'        => $this->data->get('name', ''),
                    'type'        => 'text',
                    'placeholder' => strip_tags($slide->fill($this->data->get('placeholder', ''))),
                    'class'       => 'n2-ow ' . $inputFont . $inputStyle,
                    'style'       => 'display: block; width: 100%;white-space:nowrap;',
                    'onkeyup'     => $this->data->get('onkeyup')
                ), false) . $parametersHTML) . $td2)));
    }

    public function _renderAdmin() {
        $slide  = $this->layer->getSlide();
        $slider = $slide->getSlider();

        $style = N2StyleRenderer::render($this->data->get('style'), 'heading', $slider->elementId, 'div#' . $slider->elementId . ' ');


        $inputFont  = N2FontRenderer::render($this->data->get('inputfont'), 'paragraph', $slider->elementId, 'div#' . $slider->elementId . ' .n2-ss-layer ', $slider->fontSize);
        $inputStyle = N2StyleRenderer::render($this->data->get('inputstyle'), 'heading', $slider->elementId, 'div#' . $slider->elementId . ' .n2-ss-layer ');

        $td2         = '';
        $buttonLabel = $this->data->get('buttonlabel');
        if (!empty($buttonLabel)) {
            $buttonFont  = N2FontRenderer::render($this->data->get('buttonfont'), 'hover', $slider->elementId, 'div#' . $slider->elementId . ' .n2-ss-layer ', $slider->fontSize);
            $buttonStyle = N2StyleRenderer::render($this->data->get('buttonstyle'), 'heading', $slider->elementId, 'div#' . $slider->elementId . ' .n2-ss-layer');

            $td2 = N2Html::tag('td', array(), N2Html::tag('div', array(
                'style' => 'white-space:nowrap;display:inline-block;',
                'class' => $buttonFont . ' ' . $buttonStyle . ' n2-ow'
            ), $buttonLabel));
        }


        return N2Html::tag('div', array(
            'style' => 'display:' . ($this->data->get('fullwidth', 0) ? 'block' : 'inline-block') . ';',
            'class' => $style . ' ' . $this->data->get('class', '') . ' n2-ow'
        ), N2Html::tag('table', array(
            'style'       => 'width:auto',
            'class'       => 'n2-ow',
            'cellpadding' => 0,
            'cellspacing' => 0
        ), N2Html::tag('tr', array(
            'class' => 'n2-ow'
        ), N2Html::tag('td', array(
                'class' => 'n2-ow',
                'style' => ($this->data->get('fullwidth', 0) ? 'width:100%' : '')
            ), "<div class='n2-fake-input n2-ow " . $inputFont . " " . $inputStyle . "' style='width:100%;white-space:nowrap;'>" . strip_tags($slide->fill($this->data->get('placeholder', ''))) . "</div>") . $td2)));

    }
}
