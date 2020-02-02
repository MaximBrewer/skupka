<?php

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');

class Excel2vmViewExport extends JViewLegacy {

	function display($tpl = null) {
		$db = JFactory::getDBO();

		$title = $GLOBALS['component_name'].'. '.JText::_('EXPORT');
		$model =  $this->getModel();

		$this->assign('config', $model->config);
		$this->assign('fields', $model->active);
		$this->assign('versions', $model->core->get_last_version()); 
        $this->assign('categories', $model->getCategoryList(0));
		$this->assign('manufacturers', $model->getManufacturers());
        if($model->price_label){
            $this->assign('price_labels', $model->getPriceLabels());
            $this->assign('price_label', true);
        }
        else{
            $this->assign('price_label', false);
        }
		$this->assign('profiles', $model->profile_list());
		$this->assign('files', $model->get_files());
		JToolBarHelper :: title($title, 'logo');
		JToolBarHelper :: preferences('com_excel2vm');


		parent :: display($tpl);
	}

}

?>