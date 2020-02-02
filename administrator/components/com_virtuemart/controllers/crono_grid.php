<?php
/**
* @version: 2.3 (16.01.15)
* @author: Vahrushev Konstantin
* @copyright: Copyright (C) 2015 crono.ru
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
* http://crono.ru
**/
defined('_JEXEC') or die('Restricted access');

// Load the controller framework
jimport('joomla.application.component.controller');
require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/crono.php';
class VirtuemartControllerCrono_Grid extends VmController {
     function display($cachable = false, $urlparams = false){
        $view = $this->getView('crono_grid', 'html');
        $view->setLayout('crono');
        $view->display(); 
     }   
     function getData(){
        error_reporting(E_ERROR);
        $view = $this->getView('crono_grid', 'json'); 
        $view->display(null);
     }
     function showEditor(){
        $view = $this->getView('crono_grid', 'html');
        $view->editor(); 
     }
     function saveData(){
         global $db, $model;
         $view = $this->getView('crono_grid', 'json');
         $grid = vmCronoGrid_Grid::getInstance();
         $saveresult = $grid->save();
         $view->product_SaveResultAnswer($saveresult); 
     }
     function productAdd(){
         $view = $this->getView('crono_grid', 'json');
         $model = new VirtueMartModelCrono_Grid();
         $product = $model->product_Add();
         $view->sendjson($product);
     }
     function productDelete(){
         $ids = explode(',', JRequest::getString('_rowid'));
         $view = $this->getView('crono_grid', 'json');
         $model = new VirtueMartModelCrono_Grid();
         $product_id = $model->product_Delete($ids);
     }
     function viewList(){
        $view = $this->getView('crono_grid', 'html');
        $view->viewList();  
     }
     function viewForm(){
        $view = $this->getView('crono_grid', 'html');
        $view->viewForm();   
     }
}