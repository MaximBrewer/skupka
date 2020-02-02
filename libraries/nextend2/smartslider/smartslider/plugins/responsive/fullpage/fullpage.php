<?php
class N2SSPluginResponsiveFullPage extends N2PluginBase {

    private static $name = 'fullpage';

    function onResponsiveList(&$list, &$labels) {
        $list[self::$name]   = $this->getPath();
        $labels[self::$name] = n2_x('Fullpage', 'Slider responsive mode');
    }

    static function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . self::$name . DIRECTORY_SEPARATOR;
    }
}

N2Plugin::addPlugin('ssresponsive', 'N2SSPluginResponsiveFullPage');

class N2SSResponsiveFullPage {

    private $params, $responsive;

    public function __construct($params, $responsive, $features) {
        $this->params     = $params;
        $this->responsive = $responsive;

        $this->responsive->scaleDown                      = 1;
        $this->responsive->scaleUp                        = 1;

        $features->align->align = 'normal';

        $this->responsive->maximumSlideWidth = intval($this->params->get('responsiveSlideWidthMax', 3000));

        $this->responsive->focusUser     = intval($this->params->get('responsiveFocusUser', 0));
        $this->responsive->focusAutoplay = intval($this->params->get('responsiveFocusAutoplay', 0));

        $this->responsive->forceFull                   = intval($this->params->get('responsiveForceFull', 1));
		$this->responsive->forceFullHorizontalSelector = $this->params->get('responsiveForceFullHorizontalSelector', '');
        $this->responsive->constrainRatio              = intval($this->params->get('responsiveConstrainRatio', 1));
        $this->responsive->verticalOffsetSelectors     = $this->params->get('responsiveHeightOffset', '#wpadminbar');

    }
}
