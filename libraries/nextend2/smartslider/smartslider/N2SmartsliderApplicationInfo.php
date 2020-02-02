<?php

class N2SmartsliderApplicationInfo extends N2ApplicationInfo {

    public function __construct() {
        $this->path      = dirname(__FILE__);
        $this->assetPath = realpath(dirname(__FILE__) . "/../media");
        parent::__construct();
    }

    public function isPublic() {
        return true;
    }

    public function getName() {
        return 'smartslider';
    }

    public function getLabel() {
        return 'Smart Slider';
    }

    public function getInstance() {
        require_once $this->path . NDS . "N2SmartsliderApplication.php";

        return new N2SmartSliderApplication($this);
    }

    public function getPathKey() {
        return '$ss$';
    }

    public function onNextendBaseReady() {
        parent::onNextendBaseReady();

        require_once dirname(__FILE__) . '/libraries/storage.php';
    }

    public function assetsBackend() {
        static $once;
        if ($once != null) {
            return;
        }
        $once = true;

        $path = $this->getAssetsPath();
        N2CSS::addStaticGroup($path . '/admin/dist/smartslider-backend.min.css', 'smartslider-backend');
    

        N2Localization::addJS(array(
            'Insert',
            'Insert variable',
            'Choose the group',
            'Choose the variable',
            'Result',
            'Filter',
            'No',
            'Clean HTML',
            'Remove HTML',
            'Split',
            'Chars',
            'Words',
            'Start',
            'Length',
            'Find image',
            'Index',
            'Find link',
            'Index'
        ));
        N2JS::addStaticGroup($path . '/dist/smartslider-backend.min.js', 'smartslider-backend');
    }

    public function assetsFrontend() {
        if (N2Platform::$isAdmin) {
            N2JS::addInline('window.N2SSPRO=' . N2SSPRO . ';', true);
            N2JS::addInline('window.N2SS3C="' . N2SS3::$campaign . '";', true);
        }
    


        $path = $this->getAssetsPath();
        N2JS::addStaticGroup($path . '/dist/smartslider-frontend.min.js', 'smartslider-frontend');
    
    }
}

return new N2SmartsliderApplicationInfo();