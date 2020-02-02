<?php
N2Loader::import(array(
    'libraries.postbackgroundanimation.storage'
), 'smartslider');

class N2SmartSliderPostBackgroundAnimationModel extends N2SystemVisualModel
{

    public $type = 'postbackgroundanimation';

    public function __construct($tableName = null) {

        parent::__construct($tableName);
        $this->storage = N2Base::getApplication('smartslider')->storage;
    }

    protected function getPath() {
        return dirname(__FILE__);
    }

    public function renderForm() {
        $form = new N2Form();
        $form->loadXMLFile(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'postbackgroundanimation' . DIRECTORY_SEPARATOR . 'form.xml');
        $form->render('n2-post-background');
    }
}
