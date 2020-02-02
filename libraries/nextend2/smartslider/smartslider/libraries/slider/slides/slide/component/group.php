<?php
class N2SSSlideComponentGroup extends N2SSSlideComponent {

    protected $type = 'group';

    protected $attributes = array(
        'class' => 'n2-ss-layer-group'
    );

    public function __construct($index, $slide, $group, $data, $placenentType) {
        parent::__construct($index, $slide, $group, $data, 'group');
        $this->container = new N2SSSlideContainer($slide, $this, $data['layers'], 'absolute');
        $this->data->un_set('layers');

        $this->attributes['style'] = '';

        $this->placement->attributes($this->attributes);

    }

    public function render() {
        if ($this->isRenderAllowed()) {
            $this->prepareHTML();
            $html = $this->renderPlugins(parent::renderContainer());

            return N2Html::tag('div', $this->attributes, $html);
        }

        return '';
    }

    public function admin() {
        $this->createProperty('opened', 1);

        parent::admin();
    }

    /**
     * @param N2SmartSliderExport $export
     * @param array               $layer
     */
    public static function prepareExport($export, $layer) {

        N2SmartSliderExport::prepareExportLayer($export, $layer['layers']);
    }

    public static function prepareImport($import, &$layer) {
        N2SmartSliderImport::prepareImportLayer($import, $layer['layers']);
    }

    public static function prepareSample(&$layer) {
        N2SmartsliderSlidesModel::prepareSample($layer['layers']);
    }

    public function getFilled(&$layer) {

        N2SmartSliderSlide::fillLayers($layer['layers']);
    }
}
