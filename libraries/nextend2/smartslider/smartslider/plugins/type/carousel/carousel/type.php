<?php

class N2SmartSliderTypeCarousel extends N2SmartSliderType {

    public function getDefaults() {
        return array(
            'single-switch'                  => 0,
            'carousel-dynamic-slider-height' => 0,
            'slide-width'                    => 600,
            'slide-height'                   => 400,
            'maximum-pane-width'             => 3000,
            'minimum-slide-gap'              => 10,
            'background-color'               => 'dee3e6ff',
            'background'                     => '',
            'background-size'                => 'cover',
            'background-fixed'               => 0,
            'animation'                      => 'horizontal',
            'animation-duration'             => 800,
            'animation-delay'                => 0,
            'animation-easing'               => 'easeOutQuad',
            'carousel'                       => 1,
            'border-width'                   => 0,
            'border-color'                   => '3E3E3Eff',
            'border-radius'                  => 0,
            'slide-background-color'         => 'ffffff',
            'slide-border-radius'            => 0
        );
    }

    protected function renderType() {
        if ($this->slider->params->get('single-switch', 0)) {
            $this->renderTypeSingle();
        } else {
            $this->renderTypeOriginal();
        }
    }

    protected function renderTypeOriginal() {

        $params = $this->slider->params;
        N2JS::addStaticGroup(N2Filesystem::translate(dirname(__FILE__)) . '/dist/smartslider-carousel-type-frontend.min.js', 'smartslider-carousel-type-frontend');
    

        $background = $params->get('background');
        $css        = $params->get('slider-css');
        if (!empty($background)) {
            $css = 'background-image: url(' . N2ImageHelper::fixed($background) . ');';
        }

        echo $this->openSliderElement();
        ?>
        <div class="n2-ss-slider-1 n2-ow" style="<?php echo $css; ?>">
            <div class="n2-ss-slider-2 n2-ow">
                <div class="n2-ss-slider-pane n2-ow">
                    <?php
                    echo $this->slider->staticHtml;
                    ?>
                    <?php
                    foreach ($this->slider->slides AS $i => $slide) {
                        echo N2Html::tag('div', array('class' => 'n2-ss-slide-group n2-ow ' . $slide->classes), N2Html::tag('div', $slide->attributes + array(
                                'class' => 'n2-ss-slide ' . $slide->classes . ' n2-ss-canvas n2-ow',
                                'style' => $slide->style . $params->get('slide-css')
                            ), $slide->background . $slide->getHTML()));
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php
        $this->widgets->echoRemainder();
        echo N2Html::closeTag('div');


        $this->javaScriptProperties['mainanimation'] = array(
            'type'     => $params->get('animation'),
            'duration' => intval($params->get('animation-duration')),
            'delay'    => intval($params->get('animation-delay')),
            'ease'     => $params->get('animation-easing')
        );

        $this->javaScriptProperties['dynamicSliderHeight'] = intval($params->get('carousel-dynamic-slider-height'));

        $this->javaScriptProperties['carousel']                      = intval($params->get('carousel'));
        $this->javaScriptProperties['maxPaneWidth']                  = intval($params->get('maximum-pane-width'));
        $this->javaScriptProperties['responsive']['minimumSlideGap'] = intval($params->get('minimum-slide-gap'));

        $this->javaScriptProperties['parallax']['enabled'] = 0;

        N2Plugin::callPlugin('nextendslider', 'onNextendSliderProperties', array(&$this->javaScriptProperties));

        N2JS::addFirstCode("new N2Classes.SmartSliderCarousel('#{$this->slider->elementId}', " . json_encode($this->javaScriptProperties) . ");");

        echo N2Html::clear();
    }

    protected function renderTypeSingle() {
        $params = $this->slider->params;
        N2JS::addStaticGroup(N2Filesystem::translate(dirname(__FILE__)) . '/dist/smartslider-carousel-single-type-frontend.min.js', 'smartslider-carousel-single-type-frontend');
    

        $background = $params->get('background');
        $css        = $params->get('slider-css');
        if (!empty($background)) {
            $css = 'background-image: url(' . N2ImageHelper::fixed($background) . ');';
        }

        echo $this->openSliderElement();
        ?>
        <div class="n2-ss-slider-1 n2-ow" style="<?php echo $css; ?>">
            <div class="n2-ss-slider-2 n2-ow">
                <?php
                echo $this->slider->staticHtml;
                ?>
                <div class="n2-ss-slider-pane-single n2-ow">
                <div class="n2-ss-slider-pipeline n2-ow"><?php
                    foreach ($this->slider->slides AS $i => $slide) {
                        echo N2Html::tag('div', $slide->attributes + array(
                                'class' => 'n2-ss-slide ' . $slide->classes . ' n2-ss-canvas n2-ow',
                                'style' => $slide->style . $params->get('slide-css')
                            ), $slide->background . $slide->getHTML());
                    }
                    ?></div></div>
            </div>
        </div>
        <?php
        $this->widgets->echoRemainder();
        echo N2Html::closeTag('div');

        $this->javaScriptProperties['mainanimation'] = array(
            'duration' => intval($params->get('animation-duration')),
            'delay'    => intval($params->get('animation-delay')),
            'ease'     => $params->get('animation-easing')
        );

        $this->javaScriptProperties['dynamicSliderHeight'] = intval($params->get('carousel-dynamic-slider-height'));

        $this->javaScriptProperties['carousel']                      = intval($params->get('carousel'));
        $this->javaScriptProperties['maxPaneWidth']                  = intval($params->get('maximum-pane-width'));
        $this->javaScriptProperties['responsive']['minimumSlideGap'] = intval($params->get('minimum-slide-gap'));

        $this->javaScriptProperties['layerMode']['playOnce'] = 1;


        N2Plugin::callPlugin('nextendslider', 'onNextendSliderProperties', array(&$this->javaScriptProperties));

        N2JS::addFirstCode("new N2Classes.SmartSliderCarouselSingle('#{$this->slider->elementId}', " . json_encode($this->javaScriptProperties) . ");");

        echo N2Html::clear();
    }
}