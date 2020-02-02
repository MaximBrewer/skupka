<?php
N2Loader::import('libraries.slider.slides.slide.itemFactory', 'smartslider');
N2Loader::import('libraries.icons.icons');

class N2SSItemImageBox extends N2SSItemAbstract {

    protected $type = 'imagebox';

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

        $style = N2StyleRenderer::render($this->data->get('style'), 'heading', $slider->elementId, 'div#' . $slider->elementId . ' ');

        $layout = $this->data->get('layout');

        $attr = array(
            'class'             => 'n2-ss-item-imagebox-container n2-ow ' . $style,
            'data-layout'       => $layout,
            'data-csstextalign' => $this->data->get('inneralign')
        );
        if ($layout == 'left' || $layout == 'right') {
            $attr['data-verticalalign'] = $this->data->get('verticalalign');
        }

        $html = N2HTML::openTag('div', $attr);

        $marginSide = array(
            'top'    => 'marginBottom',
            'bottom' => 'marginTop',
            'left'   => 'marginRight',
            'right'  => 'marginLeft'
        );

        // START IMAGE SECTION
        $imageHTML           = '';
        $imageContainerStyle = '';
        $icon                = $this->data->get('icon');
        $image               = $this->data->get('image');
        if (!empty($icon)) {
            $iconData = N2Icons::render($icon);
            $imageHTML .= N2HTML::tag('i', array(
                'class' => $iconData['class'],
                'style' => 'color: ' . N2Color::colorToRGBA($this->data->get('iconcolor')) . ';font-size:' . ($this->data->get('iconsize') / 16 * 100) . '%'
            ), $iconData['ligature']);
        } else if (!empty($image)) {

            if ($layout == 'top' || $layout == 'bottom') {
                $imageContainerStyle .= 'width:' . $this->data->get('imagewidth') . '%;';
            } else {
                $imageContainerStyle .= 'max-width:' . $this->data->get('imagewidth') . '%;';
            }
            $imageHTML .= N2HTML::image(N2ImageHelper::fixed($slide->fill($this->data->get('image'))), $slide->fill($this->data->get('imagealt')));
        }

        if (!empty($imageHTML)) {
            $html .= N2HTML::tag('div', array(
                'class' => 'n2-ss-item-imagebox-image n2-ow',
                'style' => $marginSide[$layout] . ':' . $this->data->get('spacing') . 'px;' . $imageContainerStyle
            ), $this->getLink($imageHTML));
        }
        // END IMAGE SECTION


        // START CONTENT SECTION
        $html .= N2HTML::openTag('div', array(
            'class' => 'n2-ss-item-imagebox-content n2-ow'
        ));

        $heading = $this->data->get('heading');
        if (!empty($heading)) {
            $font = N2FontRenderer::render($this->data->get('fonttitle'), 'hover', $slider->elementId, 'div#' . $slider->elementId . ' .n2-ss-layer ', $slider->fontSize);

            $html .= $this->getLink(N2HTML::tag($this->data->get('headingpriority'), array('class' => $font), $slide->fill($heading)));
        }

        $description = $this->data->get('description');
        if (!empty($description)) {
            $font = N2FontRenderer::render($this->data->get('fontdescription'), 'paragraph', $slider->elementId, 'div#' . $slider->elementId . ' .n2-ss-layer ', $slider->fontSize);

            $html .= N2HTML::tag('div', array('class' => $font), $slide->fill($description));
        }

        $html .= '</div>';
        // END CONTENT SECTION


        $html .= '</div>';

        return $html;
    }

    public function loadResources($slider) {
        N2LESS::addFile(dirname(__FILE__) . "/imagebox.n2less", $slider->cacheId, array(
            "sliderid" => $slider->elementId
        ), NEXTEND_SMARTSLIDER_ASSETS . '/less' . NDS);
    }
}
