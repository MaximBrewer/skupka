<?php
/**
* @version: 2.3.0 (15.01.15)
* @author: Vahrushev Konstantin
* @copyright: Copyright (C) 2012 crono.ru
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
* http://crono.ru
**/
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');
if(!class_exists('VmView'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmviewadmin.php');
class VirtuemartViewCrono_Grid extends VmViewAdmin {
    /** @var VirtueMartModelCrono_Grid */
    private $model;
    function __construct($config=array()){
        $this->model = VmModel::getModel('crono_grid');
        parent::__construct($config);
    }
    /***************************** Продукты  ***********************************/
    function product_List(){
        global $db;
        $model = VmModel::getModel('crono_grid');
        $grid  = vmCronoGrid_Grid::getInstance();   
        return $grid->dataLoad();
    }
    /**
    * put your comment there...
    * 
    * @param vmCronoGrid_SaveResult $saveresult
    */
    function product_SaveResultAnswer($saveresult){
          $this->sendjson(array('message'=>$saveresult->message, 'saved'=>$saveresult->saved, 'data'=>$saveresult));
    }
    /***************************** Дерево категорий ***********************************/
    function category_TreeAjax(){
        // get request data
        $product_id = JRequest::getInt('product_id');
        $parent     = JRequest::getInt('nodeid');
        $level      = JRequest::getInt('n_level',-1);
        
        // загружаем данные
        $model   = $this->model;
        $xref_pp = $model->getProductCategoryXref();
        $xref_pc = $model->getCategoryXref('pc');
        $xref_cp = $model->getCategoryXref('cp');
        $list    = $model->getCategoryFullList();
        
        /**
        *  если есть выбранные категории, то:
        *   1. проверяем есть ли потомк, если есть, то мтим ее как notleaf
        *   2. выводим все одноуровневые категории
        *   3. по иерархии вверх всех родителей и одноуровневые с ними категории
        */
        $selected = $xref_pp[$product_id];
        if($selected && !$parent){
            $ids_all_parent = array();// для хранения ID всех родителей
            foreach($selected as $selected_id){
                $id     = (int)$selected_id;
                $parent = $xref_cp[$id];
                while($parent){
                    $ids_all_parent[] = $parent;
                    $parent  = $xref_cp[$parent];
                }
            }
            $ids_all_parent[] = 0;
            $this->category_TreeFull($selected, $ids_all_parent);
        }
        // иначе просто список дочерних
        else{
            $this->__categoryTreeSendHeader();
            if($xref_pc[$parent]){
                uasort($xref_pc[$parent],'crono_grid_uasrt_child_category');
                foreach($xref_pc[$parent] as $id){
                    $isleaf = !count(@$xref_pc[$id])?true:false;
                    $this->__categoryTreePrintNode(0, $id, $list[$id]->category_name, $level+1, $parent, $isleaf,false, $isleaf,$list[$id]->published);
                }
            }
            $this->__categoryTreePrintTail();
        }
    }
    function category_TreeFull(&$selected=false, &$parent_limit=false){
        $this->__categoryTreeSendHeader();
        $model = new VirtueMartModelCrono_Grid();
        $xref  = $model->getCategoryXref('cp'); 
        // найдем категории без родителей 
        foreach($xref as $child=>$parent){     
            if(!$parent){
                $this->__categoryFullTreeReqursive($child, 0,0, $selected, $parent_limit);
            }
        }
        $this->__categoryTreePrintTail();
    }
    private function __categoryFullTreeReqursive($category_id, $parent_id, $level, &$selected=false, &$parent_limit=false){
        $model = new VirtueMartModelCrono_Grid();
        $cat_list = $model->getCategoryFullList();
        $item = @$cat_list[$category_id];
        if(!$item) return;
        
        $xref  = $model->getCategoryXref('pc');
        // печатаем родителя
        $name    = str_replace("'","\\'",$item->category_name);
        $isleaf  = !count(@$xref[$category_id])?1:0;// если кол-во детей=0, значит конечный узел
        $checked = $selected?(int)in_array($category_id, $selected):0;
        $loaded  = $parent_limit?in_array($category_id, $parent_limit):true;
        $expanded= $parent_limit?$loaded:false;
        $this->__categoryTreePrintNode($checked, $category_id, $name, $level, $parent_id, $isleaf, $expanded, $loaded, $item->published);
        
        if(!$isleaf && (!$parent_limit || in_array($category_id, $parent_limit))){
            //uasort($xref[$category_id],'crono_grid_uasrt_child_category');
            foreach($xref[$category_id] as $children_id){
                $this->__categoryFullTreeReqursive($children_id, $category_id, $level+1, $selected, $parent_limit);
            }
        }
    } 
    private function __categoryTreePrintNode($checked, $id,$name, $level, $parent, $isleaf,$expanded,$loaded, $published){
        //$name     = $name."($id, level-$level, parent-$parent, isleaf-$isleaf)";
        $isleaf   = $isleaf?'true':'false';
        $expanded = $expanded?'true':'false';
        $loaded   = $loaded?'true':'false';
        echo "<row>";
        echo "  <cell>$checked</cell>";
        echo "  <cell>cat$id</cell>";   // добавляем префикы, т.к. если совпадают ИД товаров и категорий возникают конфликты между категориями
        echo "  <cell>$name</cell>";  
        echo "  <cell>$published</cell>";  
        echo "  <cell>$level</cell>";
        echo "  <cell>cat$parent</cell>";
        echo "  <cell>$isleaf</cell>";
        echo "  <cell>$expanded</cell>";
        echo "  <cell>$loaded</cell>";
        echo "</row>";
    }
    private function __categoryTreeSendHeader(){
        ob_clean();
        header ("content-type: text/xml");
        // JSON почеу-то не работает. ну и хрен с ним
        echo "<?xml version='1.0' encoding='utf-8'?>\n";
        echo "<rows>";
        echo "<page>1</page>";
        echo "<total>1</total>";
        echo "<records>1</records>\n";
    }
    private function __categoryTreePrintTail(){
        echo "</rows>";
        exit;
    }
    /***********************************************************************************/
    /**
    * put your comment there...
    * 
    * @param string $type - тип данных: категории, продукты и т.д
    * @param mixed $tpl
    */
    function display($type, $tpl = null) {    
        $type = JRequest::getVar('type', false);
        // products, category and etc
        switch($type){
            case 'categorry_full':$data = $this->category_TreeFull(); break;
            case 'categorry_ajax':$data = $this->category_TreeAjax(); break;
            default: $data = $this->product_List();
        }
        $this->sendjson($data);
    }
    public function sendjson($aData){
        // send json answer
        ob_clean();
        echo json_encode($aData);  
        exit;
    }
}
function crono_grid_uasrt_child_category($a, $b){
     $model = new VirtueMartModelCrono_Grid();
     $cat_list = $model->getCategoryFullList();
     return strcmp($cat_list[$a]->category_name, $cat_list[$b]->category_name);
}
// pure php no closing tag
