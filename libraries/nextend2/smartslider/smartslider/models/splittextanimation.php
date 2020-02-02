<?php
N2Loader::import(array(
    'libraries.splittextanimation.storage'
), 'smartslider');

class N2SmartSliderSplitTextAnimationModel extends N2SystemVisualModel
{

    public $type = 'splittextanimation';

    public function __construct($tableName = null) {

        parent::__construct($tableName);
        $this->storage = N2Base::getApplication('smartslider')->storage;
    }

    public function renderForm() {
        $form = new N2Form();
        $form->loadXMLFile(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'splittextanimation' . DIRECTORY_SEPARATOR . 'form.xml');
        $form->render('n2-splittextanimation-editor');
    }

    public function renderFormExtra() {
        $form = new N2Form();
        $form->loadXMLFile(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'splittextanimation' . DIRECTORY_SEPARATOR . 'extra.xml');
        $form->render('n2-splittextanimation-editor');
    }

    protected function getPath() {
        return dirname(__FILE__);
    }
}
