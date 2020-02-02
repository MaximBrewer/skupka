<?php
N2Loader::import('helpers.controllers.VisualManager', 'system.backend');

class N2SmartSliderBackendPostBackgroundAnimationController extends N2SystemBackendVisualManagerController
{

    protected $type = 'postbackgroundanimation';

    public function __construct($appType, $defaultParams) {
        $this->logoText = n2_('Ken Burns effect');
        parent::__construct($appType, $defaultParams);
    }

    protected function loadModel() {

        N2Loader::import(array(
            'models.' . $this->type
        ), 'smartslider');
    }

    public function getModel() {
        return new N2SmartSliderPostBackgroundAnimationModel();
    }

}
