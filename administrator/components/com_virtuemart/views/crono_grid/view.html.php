<?php
/**
* @version: 3.0 (18.02.2015)
* @author: Vahrushev Konstantin
* @copyright: Copyright (C) 2015 crono.ru
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
* http://crono.ru
**/
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');
if(!class_exists('VmView'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmviewadmin.php');
class VirtuemartViewCrono_Grid extends VmViewAdmin {
    /** @var VirtueMartModelCrono_Grid */
    private $model;
    private $ui_theme = 'redmond';
	function __construct($config=array()){
        $doc = JFactory::getDocument();
        $this->model = VmModel::getModel('crono_grid');        
        /* Определяем языковые переменные для JS */
        $lang_vars = array('FIELD_LIST','EMPTY_NAME');
        $js= '';
        foreach($lang_vars as $v) $js.= "CRG.lang.$v='". JText::_("CRONO_VMGRID_$v")."'\n";
        $doc->addScriptDeclaration($js);
        parent::__construct($config);
    }
    function loadAssetsFiles($startadminarea=false){
        $doc = JFactory::getDocument();
        $path = 'components/com_virtuemart/assets/crono/';
        // JS
        //$doc->addScript($path.'js/jquery.js');
        //$doc->addScript($path.'js/jquery.scrollto.js'); // без этого плагина всплывающие окна неправильно позиционировались при прокрутке страницы
        //$doc->addScript($path.'js/jquery.ui.js');
        // $doc->addScript($path.'js/jquery.ui.progressbar.js');
        $lang = JLanguage::getLanguagePath();
        $doc->addScript($path.'js/jquery.jqgrid.locale-'.strtolower(VmConfig::$vmlang).'.js'); /* Обязательно ДО jquery-jqgrid.js!!!*/
        $doc->addScript($path.'js/jquery.jqgrid.js');
        //$doc->addScript($path.'js/jquery.colorbox.js');
        $doc->addScript($path.'js/jquery.cookie.js');  
        $doc->addScript($path.'js/jquery.form.js');  // http://malsup.com/jquery/form/
        $doc->addScript($path.'js/jquery.blockUI.js'); // http://malsup.com/jquery/block/#demos
        $doc->addScript($path.'js/cronogrid.js');
       
        // CSS
        $doc->addStyleSheet($path."css/".$this->ui_theme."/jquery-ui-1.8.16.custom.css");
        $doc->addStyleSheet($path."css/ui.jqgrid.css");
        
        //$doc->addScriptDeclaration();
        //$doc->addStyleDeclaration();        
        if($startadminarea){
            echo AdminUIHelper::startAdminArea($this);
            $skip_css = array(
                '/components/com_virtuemart/assets/css/ui/jquery.ui.all.css',
                '/components/com_virtuemart/assets/css/chosen.css',
            );      
            foreach($doc->_styleSheets as $path=>$attr){
                if(in_array(substr($path, strpos($path, '/components/')), $skip_css)){
                    unset($doc->_styleSheets[$path]);        
                }
            } 
        }
    }
    function display($tpl = null) {
        $app = JFactory::getApplication();
        JToolBarHelper::addNew();
        JToolBarHelper::apply();
        JToolBarHelper::custom('','refresh', '',JText::_('refresh'));
        JToolBarHelper::save2copy('',JText::_('CRONO_VMGRID_TOOLBAR_CLONE'));
        JToolBarHelper::deleteList();
        JToolBarHelper::custom('','options', 'options',JText::_('CRONO_VMGRID_TOOLBAR_VIEW'));
        JToolBarHelper::custom('','featured','',JText::_('CRONO_VMGRID_TOOLBAR_COLUMN_CFG_SAVE'));
        JToolBarHelper::title(JText::_('COM_VIRTUEMART_CRONO_GRID'));
        
		$option = JRequest::getVar('option');
		$task = JRequest::getVar('task');
        
        $grid = vmCronoGrid_Grid::getInstance();
        $grid->jsGenerate();
        $this->assignRef('grid', $grid);
        /* Представления */
        $views = $this->model->ViewGetList();
        $view_id = JRequest::getInt('view_id');
        $this->assignRef('view_id', $view_id);
        $this->assignRef('views', $views);
        
        /* Фильтры */
        $filter_form = $grid->generateFilterForm();
		$this->assignRef('filter_form', $filter_form);
        
        $doc = JFactory::getDocument();
        $limit  = $app->getUserStateFromRequest('vmcronogrid.product','_rows',20);
        if(!$grid->license){
            $app->enqueueMessage(JText::_('CRONO_VMGRID_DEMO'));
            $doc->addScriptDeclaration("jQuery(document).ready(function(){ CRG.GridInit($limit, [10,20]);})");
        }
        else{
            $doc->addScriptDeclaration("jQuery(document).ready(function(){ CRG.GridInit($limit, [10,20,50,100,200,500,1000,5000]);})");
        }
        parent::display();
	}
    function editor(){
        $this->loadAssetsFiles();
        $this->setLayout('editor');
        $field = JRequest::getString('field');
        $product_id = JRequest::getInt("product_id");
        $type = JRequest::getCmd('editortype');
        
        $this->assignRef('product_id', $product_id);
        $this->assignRef('field', $field);
        $type = "editor$type";
        $editor = $this->$type();
        $this->assignRef('editor', $editor);
        echo $this->loadTemplate();
    }
    function editorCategory(){
        $this->loadAssetsFiles();
        return $this->loadTemplate('category');
    }
    function editorText(){
        $product_id = JRequest::getInt('product_id');
        $field = JRequest::getString('field');
        vmCronoGrid_Grid::ExtensionList();
        $field = vmCronoGrid_Grid::getInstanceField($field);        
        $text = $field->ValueByProductID($product_id);
        $this->loadAssetsFiles();
        $this->assign('text', $text);
        return $this->loadTemplate('text');
    }
    function viewList(){
        $app = JFactory::getApplication();
        JToolBarHelper::addNew('viewForm');
        JToolBarHelper::deleteList();
        JToolBarHelper::title(JText::_('CRONO_VMGRID_MENU_ITEM_VIEW'));
        $views = $this->model->ViewGetList();
        if(!count($views)) $app->enqueueMessage(JText::_('CRONO_VMGRID_MSG_NOVIEWS'),'notice');
        $this->setLayout('view');
        $this->assignRef('views', $views);
        $page = $this->loadTemplate('list');
        echo $page;
    }
    function viewForm(){
        $app = JFactory::getApplication();
        $doc = JFactory::getDocument();
        JToolBarHelper::save('viewSave', JText::_('COM_VIRTUEMART_SAVE'));
        JToolBarHelper::apply('viewApply', JText::_('CRONO_VMGRID_USE_VIEW'));
        JToolBarHelper::cancel('viewApply');
        $this->SetViewTitle('CRONO_GRID');
        
        $this->setLayout('view');
        $view_id = JRequest::getInt('view_id');
        $view = $this->model->ViewGetByID($view_id);
        $this->assignRef('view', $view);
        $this->assignRef('view_id', $view_id);
        /*********************** выбор полей ************************************/
        $fieldSelector = vmCronoGrid_FieldSelector::getInstance($view_id);
        $js_fieldselector = $fieldSelector->JsGet();
        $doc->addScriptDeclaration($js_fieldselector);
        
        $page = $this->loadTemplate('form');
        echo $page;
    }
}
