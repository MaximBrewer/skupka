<?php

class N2SmartSliderTypeBlock extends N2SmartSliderType {

    public function getDefaults() {
        return array(
            'background'       => '',
            'background-size'  => 'cover',
            'background-fixed' => 0,
            'slider-css'       => '',

            'kenburns-animation' => ''
        );
    }

    protected function renderType() {

        $params = $this->slider->params;
        N2JS::addStaticGroup(N2Filesystem::translate(dirname(__FILE__)) . '/dist/smartslider-block-type-frontend.min.js', 'smartslider-block-type-frontend');
    

        $background = $params->get('background');
        $css        = $params->get('slider-css');
        if (!empty($background)) {
            $css = 'background-image: url(' . N2ImageHelper::fixed($background) . ');';
        }

        echo $this->openSliderElement();
        ?>

        <div class="n2-ss-slider-1 n2-ow" style="<?php echo $css; ?>">
            <?php
            echo $this->getBackgroundVideo($params);
            ?>
            <div class="n2-ss-slider-2 n2-ow">
                <?php
                echo $this->slider->staticHtml;

                $slide = $this->slider->slides[$this->slider->_activeSlide];
                echo N2Html::tag('div', $slide->attributes + array(
                        'class' => 'n2-ss-slide n2-ss-canvas n2-ow ' . $slide->classes,
                        'style' => $slide->style
                    ), $slide->background . $slide->getHTML());
                ?>
            </div>
        </div>
        <?php
        $this->widgets->echoRemainder();
        echo N2Html::closeTag('div');

        N2Plugin::callPlugin('nextendslider', 'onNextendSliderProperties', array(&$this->javaScriptProperties));

        N2JS::addFirstCode("new N2Classes.SmartSliderBlock('#{$this->slider->elementId}', " . json_encode($this->javaScriptProperties) . ");");

        echo N2Html::clear();
    }

    private function getBackgroundVideo($params) {
        $mp4  = N2ImageHelper::fixed($params->get('backgroundVideoMp4', ''));

        if (empty($mp4)) {
            return '';
        }

        $sources = '';

        if ($mp4) {
            $sources .= N2Html::tag("source", array(
                "src"  => $mp4,
                "type" => "video/mp4"
            ), '', false);
        }

        $attributes = array(
            'autoplay' => 1
        );

        if ($params->get('backgroundVideoMuted', 1)) {
            $attributes['muted'] = 'muted';
        }

        if ($params->get('backgroundVideoLoop', 1)) {
            $attributes['loop'] = 'loop';
        }

        return N2Html::tag('div', array('class' => 'n2-ss-slider-background-video-container n2-ow'), N2Html::tag('video', $attributes + array(
                'class'              => 'n2-ss-slider-background-video n2-ow',
                'data-mode'          => $params->get('backgroundVideoMode', 'fill'),
                'playsinline'        => 1,
                'webkit-playsinline' => 1
            ), $sources));

    }

    /**
     * @param $params N2Data
     */
    public function limitParams($params) {

        $params->loadArray(array(
            'controlsScroll'          => 0,
            'controlsDrag'            => 0,
            'controlsTouch'           => 0,
            'controlsKeyboard'        => 0,
            'controlsTilt'            => 0,
            'autoplay'                => 0,
            'autoplayStart'           => 0,
            'widgetarrow'             => 'disabled',
            'widgetbullet'            => 'disabled',
            'widgetautoplay'          => 'disabled',
            'widgetindicator'         => 'disabled',
            'widgetbar'               => 'disabled',
            'widgetthumbnail'         => 'disabled',
            'randomize'               => 0,
            'randomizeFirst'          => 0,
            'randomize-cache'         => 0,
            'maximumslidecount'       => 1,
            'imageload'               => 0,
            'imageloadNeighborSlides' => 0,
            'maintain-session'        => 0
        ));
    }
}

