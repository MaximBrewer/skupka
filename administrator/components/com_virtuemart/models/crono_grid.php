<?php
/**
* @version: 3.0 (18.02.15)
* @author: Vahrushev Konstantin
* @copyright: Copyright (C) 2015 crono.ru
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
* http://crono.ru
**/
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.model');
JTable::addIncludePath(JPATH_VM_ADMINISTRATOR.DS.'tables');
class VirtueMartModelCrono_Grid  extends VmModel{
    const tableProduct   = "#__virtuemart_products";
    const tableCategory  = "#__virtuemart_categories";
    const tableXrefPC    = "#__virtuemart_product_categories";
    const tableXrefCC    = "#__virtuemart_category_categories";
    const tableShopperG  = "#__virtuemart_shoppergroups";
    const tablePrice     = "#__virtuemart_product_prices";
    const viewCookieName = 'CRG_view_cfg';
    
    private $errors      = array();
    
    static private $xref_cc_pc; // category2category xref: parent_id=>array(child_id)
    static private $xref_cc_cp; // category2category xref: child_id=>parent_id
    static private $xref_pc;
    static private $xref_pr;    // product2price xref: product_id =>array(shopper_group_id=>array('price_id', 'product_price', 'product_currency')
    static private $category_list;
    
    var $_total;
    var $_pagination;
    public  function __construct($cidName='cid', $config=array()){
        $l = JFactory::getLanguage();
        $l->load('mod_crono_vmhelper.sys');
        parent::__construct();
    }

    /**
     * Параметры не используются, добавлены для совместимости с родительским классом
     * @param null $i
     * @param bool $toString
     * @return string
     */
    function getError($i = NULL, $toString = true){
        return @$this->errors[0]; 
    }
    function ViewGetList(){
        $viewscfg = JRequest::getString(self::viewCookieName, '', 'COOKIE'); 
        if(!$viewscfg || $viewscfg=='{}'){
            $viewscfg = '{"1":{"name":"default","desc":"","fields":{"general_product_desc":{"width":0,"order":1},"general_product_name":{"width":0,"order":2},"price_0_value":{"width":0,"order":3},"category":{"width":0,"order":4}}}}';
        }
        $viewscfg = json_decode($viewscfg);
        
        if(is_object($viewscfg)){
            $viewscfg_arr = array();
            foreach($viewscfg as $k=>$v){
                $viewscfg_arr[$k] = $v;
            }
            $viewscfg = $viewscfg_arr;
        }
        if(!is_array($viewscfg) || !count($viewscfg)){
            return array();
        }
        return $viewscfg;
    }
    function ViewGetByID(&$id){
        $views = $this->ViewGetList();
        if(isset($views[$id]))
            return @$views[$id]; 
        else{            
            $views[]= $view = current($views);
            end($views);
            $id   = key($views);
            $view->name = 'New view '.$id;
            return $view;               
        }
    }
    static public function loadProduct($fields='virtuemart_product_id AS id', $where='',$join='',$order='',$limit=0, $limitstart=0,$key=''){
        $db = JFactory::getDbo();
        $q = "SELECT SQL_CALC_FOUND_ROWS $fields FROM ". self::tableProduct ." t \n";
        $q.= "JOIN ". self::tableProduct.'_'.VMLANG." tl USING(virtuemart_product_id) \n";
        if($join ){
            $q.="$join \n";
        }
        if($where ){
            $q.=" WHERE $where \n";
        }
        if($order){
            $q.="ORDER BY $order \n";
        }
        if($limit){
            $q.="LIMIT $limitstart, $limit";
        }
        //echo nl2br($q);exit();
        $db->setQuery($q);
        $list = $db->loadObjectList($key);
        return $list;
    }
    static public function loadPrice($fields, $where='', $join='', $typeResult='AssocList'){
        $db = JFactory::getDbo();
        $fields = $fields?$fields:'virtuemart_product_id AS product_id, virtuemart_product_price_id AS price_id, virtuemart_shoppergroup_id as group_id, product_price, product_currency,product_tax_id,product_discount_id';
        $q = "SELECT $fields FROM ". VirtueMartModelCrono_Grid::tablePrice."\n"; 
        $q.= "WHERE 1=1 ";
        if($where) $q.=" AND ".$where;
        $db->setQuery($q);
        
        $typeResult = "load$typeResult";
        $price_list = $db->$typeResult();
        return $price_list;    
    }
    static public function loadCategory($fields='*', $where='',$join='',$order='',$limit=0, $limitstart=0){
        $db = JFactory::getDbo();
        $q = "SELECT $fields FROM ". self::tableCategory." AS t \n";
        $q.= "JOIN ". self::tableCategory.'_'.VMLANG. " AS tl USING(virtuemart_category_id) \n";
        
        if($join){
            $q.="$join \n";
        }
        if($order){
            $q.="ORDER BY $order \n";
        }
        $db->setQuery($q);
        $data = $db->loadObjectList();
        return $data;
    }
    
    function getCategoryFullList(){
        if(self::$category_list){
            return self::$category_list;
        }
        $data = $this->loadCategory('t.virtuemart_category_id AS category_id, category_name, published','','','category_name');
        $return = array();
        foreach($data as $item){
            $return[$item->category_id] = $item;
        }
        self::$category_list = $return;
        return $return;
    }
    function getProductCategoryXref(){
        $db = JFactory::getDbo();
        if(self::$xref_pc){
            return self::$xref_pc;
        }
        
        $xref = array();
        $q = "SELECT virtuemart_product_id AS product_id, virtuemart_category_id AS category_id FROM ". self::tableXrefPC;
        $db->setQuery($q);
        $data = $db->loadObjectList();
        foreach($data as $item){
            if($item->category_id && $item->product_id){
                if(!isset($xref[$item->product_id]) || !in_array($item->category_id, $xref[$item->product_id])){
                    $xref[$item->product_id][] = $item->category_id;
                }
            }
        }
        self::$xref_pc = $xref;
        return $xref;
    }
    /**
    * Связи между категориями. Возвращает массив Category_id=>parent_id
    * @param cp(child_id=>parent_id) or pc(parent_id=>array(child_id))
    */
    function getCategoryXref($destintation='cp'){
        $db = JFactory::getDbo();
        $cat_list = $this->getCategoryFullList();
        if(!self::$xref_cc_cp){
            $q = "SELECT category_child_id AS child, category_parent_id AS parent FROM ". self::tableXrefCC. " AS x ORDER BY `ordering`";
            $db->setQuery($q);
            $data = $db->loadObjectList();
            
            self::$xref_cc_cp= array();
            self::$xref_cc_pc= array();
            
            foreach($data as $item){
                if($item->child){
                    self::$xref_cc_cp[$item->child] = $item->parent;
                    if(!isset(self::$xref_cc_pc[$item->parent]))
                        self::$xref_cc_pc[$item->parent] = array();
                    if(!in_array($item->child, self::$xref_cc_pc[$item->parent]) && isset($cat_list[$item->child]))
                        self::$xref_cc_pc[$item->parent][] = $item->child;
                }
            }
        }
        //var_dump($this->xref_cc_cp);exit;
        if($destintation=='pc'){
            return self::$xref_cc_pc;
        }
        else{
            return self::$xref_cc_cp;
        }
        
    }
    function product_Add(){
        $clone_id = JRequest::getInt('clone');
        $db = JFactory::getDbo();
        if($clone_id){
             $xml = simplexml_load_string(JHTML::_( 'form.token' ));
             JRequest::setVar((string)$xml['name'], 1,'post');
             $model = VmModel::getModel('product');
             $id = $model->createClone($clone_id);
        }
        else{
            $q = "INSERT INTO ". self::tableProduct."(published) VALUES (0)";
            $db->setQuery($q);
            $db->query();
            $id = $db->insertid();
            $q = "INSERT INTO ". self::tableProduct.'_'.VMLANG."(virtuemart_product_id, slug) VALUES ($id, $id)";
            $db->setQuery($q);
            $db->query();
        }
        $answer = array('error'=>false, 'data'=>'');
        if($id){
            /* извлечение данных для новой строки, имитируя фильтр по ИД через тубар*/
            $json_filter = '{"rules":[{"field":"id","op":"bw","data":"'.$id.'"}]}';
            JRequest::setVar('filters', $json_filter);
            $grid = vmCronoGrid_Grid::getInstance();
            $data = $grid->dataLoad();
            $answer['row'] = $data->rows[0]['cell'];
        }
        else{
            $answer['error'] = 'Error product create';
        }
        return $answer;
    }
    function product_Delete($ids){
        $ps_product = new ps_product();
        $d = array('product_id'=>$ids);
        $ps_product->delete($d);
    }
    public function MenuModelGetItems(){
        $items = array();
        $items[]= array('CRONO_VMGRID_MENU_TITLE', 'crono_grid', '', 21, 'vmicon vmicon-16-document_move');
        $items[]= array('CRONO_VMGRID_MENU_ITEM_VIEW','crono_grid', 'viewList', 22, 'vmicon vmicon-16-calculator');
        return $items;
    }
    static public function trimString($string, $length=100){
        $string = strip_tags($string);
        $string = str_replace(array("\t", "\n", "\r", "\0","\x0B"), ' ', $string);
        $string = strlen($string)<$length?$string:substr( $string ,0,$length).'...';
        $string = JString::trim($string);
        if( function_exists('iconv')){
            $string = iconv('utf-8', 'utf-8//IGNORE', $string);
        }
        return $string;
    }
}

/* класс формирования основной таблицы */
class vmCronoGrid_Grid{
    public $license;
    /** Текущее представлене таблицы в виде объекта с набором полей, каждое из полей соответствует столбцу таблицы 
    * НЕ JView !!!
    */
    public $view;
    /**
    * массив полей таблицы, array of vmCronoGrid_Field
    * 
    * @var vmCronoGrid_FieldBasic
    */
    private $fields;
    /**
    * Список полей представленных в таблице, для простого перебора всех полей. Массив связейц имя поля=>индекс в $fields
    * 
    * @var mixed
    */
    private $fields_name;
    /** 
    * Фильтры в заголовках  стобцов. массив объектов с полями field и data
    * 
    * @var array
    */
    public  $toolbar_filters = array(); 
    public  $extensions = array();
    public  $product_ids = array();
    private $_grid_data;
    private $_map_rowid; /* Таблица преобразования строки таблицы в ИД товара */
    
    static private $instance;    
    
    public function jsGenerate(){
        $js_fields = array();
        $js_captions = array();
        $js_templates = array();
        foreach($this->fields as $field){
            $js_fields[] = $field->JsConfiguration();
            $js_templates[$field->gName] = $field->gCellTpl;
            $js_captions[]=$field->gCaption;
        }
        // заголовки полей таблицы
        $js_captions = 'CRG.fields_captions = '.json_encode($js_captions).';';
        // описание полей
        $js_fields   = implode(",\n", $js_fields);
        // имена функций JS не должны быть в кавычках
        $js_fields   = 'CRG.fields = ['.preg_replace('["(CRG.*?)"]si', '$1', $js_fields).'];';
        // шаблоны полей
        $js_templates = 'CRG.templates = '. json_encode($js_templates).';';
        
        $doc = JFactory::getDocument();
        $doc->addScriptDeclaration($js_captions);
        $doc->addScriptDeclaration($js_fields);
        $doc->addScriptDeclaration($js_templates);
        //var_dump($js_fields, $js_captions);
    }
    /**
    * put your comment there...
    * 
    * @param string $name
    * @return vmCronoGrid_Field
    */
    public  function fieldGetByName($name){
        return $this->fields[$this->fields_name[$name]];
    }
    public  function dataLoad(){
        $db = JFactory::getDbo();
        $app = JFactory::getApplication();
        // лимит строк. Защита от дурака :-) 
        $demo_limit = 20;
        $limit  = $app->getUserStateFromRequest('vmcronogrid.product','_rows',$demo_limit);
        if(!$this->license && $limit>$demo_limit){
            $limit = $demo_limit;
            $app->setUserState('vmcronogrid.product','_rows',$demo_limit);
        }
        $offset = JRequest::getInt('_page',1);
        
        // составные части запроса
        $select_fields = array();
        $select_from   = array(VirtueMartModelCrono_Grid::tableProduct." AS t");
        $select_where = array(); //product_parent_id=0
        $select_join  = array();
         
        // параметры сортировки
        $sortingField = JRequest::getString('_sidx', 'id');
        $sortingOrder = JRequest::getString('_sord')=='asc'?'ASC':'DESC';

        // define ORDER BY
        $orderby = "$sortingField $sortingOrder";
        /* Формирование запроса по используемым клссам */
        foreach(self::ExtensionList() as $classname){
            eval($classname.'::prepareGeneralSelect($select_fields, $select_join, $select_where);');
        }
        /* Формирование запроса по полям */
        foreach($this->fields as $field){
            $field->prepareFieldSelect($select_fields, $select_join, $select_where);
        }
        if($this->product_ids){
            $select_where = array("t.virtuemart_product_id IN(".implode(',', $this->product_ids).")");
        }
        $model = VmModel::getModel('crono_grid');        
        $select_fields = implode(", ", $select_fields);
        $select_join   = implode("\n", $select_join);
        $select_where  = implode(" AND ", $select_where);
        $list = $model->loadProduct($select_fields, $select_where, $select_join, $orderby, $limit, $limit*($offset-1),'id');
        $this->product_ids = array_keys($list);
        $db->setQuery('SELECT FOUND_ROWS()');
        $total_rows = $db->loadResult();
        $i=0;
        foreach($list as $product_id=>$product){
            $response->rows[$i]['id'] = $product->id;
            $cell_data = array();
            foreach($this->fields as $field){                
                $cell_data[]= $field->prepareDisplay($product);
            }
            $response->rows[$i]['cell'] = $cell_data;
            $i++;
        }
        
        $response->page = $offset;
        $response->total = ceil($total_rows / $limit);;
        $response->records = $total_rows;
                    
        return $response;
    }
    public function  getGridData(){
        if(!$this->_grid_data){
            $this->_grid_data = $this->dataLoad();
            foreach($this->_grid_data->rows as $rownumber=>$row){
                $this->_map_rowid[$rownumber]= $row['id'];
            }
        }
        return $this->_grid_data;
    }
    private function init(){                                                                     
        // список полей определяем из куки, если куки нет, то
        $model = VmModel::getModel('crono_grid');
        $view_id = JRequest::getInt('view_id');
        $view  = $model->ViewGEtByID($view_id);
    
        // обязательно добавляем поле ИД в таблицу
        if(!isset($view->fields->id)){
            $view->fields->id = json_decode('{"order":0, "width":10}');
        }
        // перебираем поля из представления
        foreach($view->fields as $fieldname=>$field_cfg){
            $field = self::getInstanceField($fieldname, @$field_cfg->width);
            if(!@$field->gName){
                JError::raiseNotice(0,"Not difined gName for field '$fieldname'");
                continue;
            }
            if($field){
                // определяем порядок отображения полей в таблице, если несколько полей по какой либо причине имеюют одинаковый порядок, то исправляем
                $order = (int)@$field_cfg->order;
                while(isset($this->fields[$order]))$order++;
                $this->fields[$order] = $field;
                $this->fields_name[$fieldname] = $order;
            }
        }
        ksort($this->fields);
    }
    public  function generateFilterForm(){
        $filter = '';
        foreach($this->extensions as $class_name){
            eval("\$extfilter= $class_name::GenerateFilterFields();");
            $filter.= "<div>$extfilter</div>\n";
        }
        return $filter;
    }
    /**
    * put your comment there...
    * @return vmCronoGrid_SaveResult
    */
    public  function Save(){
         $id  = JRequest::getInt('_rowid');
         $ids = JRequest::getVar('_rowids');
         $this->product_ids = $ids = is_array($ids)?$ids:array();
         if(!in_array($id, $ids))$ids[]=$id;

         $this->product_ids = $ids;
         $fieldname = JRequest::getString('_cellname');
         
         $value = JRequest::getVar($fieldname, '','','',2); // для категорий и полного описания
         $value = $this->OnSaveSetRowContext($value, $ids);
         
         $field  = vmCronoGrid_Grid::getInstanceField($fieldname);
         $saveresult = new vmCronoGrid_SaveResult();
         $field->save($ids, $value, $saveresult);
         if(!$saveresult->value)$saveresult->value = $value;
         if(!$saveresult->displayvalue)$saveresult->displayvalue = VirtueMartModelCrono_Grid::trimString( $value );
         if(count($ids)>1){
             $saveresult->reload  = true;
         }
         return $saveresult;
    }    
    /**
    * Подставляет в значение вместо {N} значения столбцов с номером N
    * @param mixed $value
    * @param mixed $ids
    */
    public function OnSaveSetRowContext($value, $ids){
        if(is_array($value)) return $value;
        preg_match_all('[\{(\d+)\}]si', $value, $m);
        if(!$m[1]) return $value;
        $values = $this->OnSaveOldVersionCompability($ids, $value);
        $griddata = $this->getGridData();
        foreach($values as $pid=>&$value){
            $rownumer = array_search($pid, $this->_map_rowid);
            foreach($m[1] as $column){
                $value = str_replace('{'.$column.'}', $griddata->rows[$rownumer]['cell'][$column], $value);
            }
        }
        return $values;
    }
    /* преобразует значение в массив значений для каждой строки */
    public function OnSaveOldVersionCompability($ids, $value){
        if(!is_array($value) || count( array_intersect($ids, array_keys($value)))!=count($ids)){
            $values = array();
            foreach($ids as $id){
                $values[$id]= $value;
            }
            $value = $values;
        }
        return $value;
    }
    /**
    * 
    * @return vmCronoGrid_Grid
    */
    static public function getInstance(){
        $helper = VmCronoHelper::getInstance();
        if(!self::$instance){
            $grid = new self();
            $grid->license = $helper->license('grid');
            $grid->extensions = self::ExtensionList();
            /* toolbar filters */
            if(isset($_REQUEST['filters'])){
                $filters = json_decode($_REQUEST['filters']); //{"groupOp":"AND","rules":[{"field":"product_name","op":"bw","data":"ла"},{"field":"description","op":"bw","data":"фы"}]}
                if(is_array($filters->rules)){
                    foreach($filters->rules as $rule){
                        $grid->toolbar_filters[$rule->field] = $rule;
                    }
                }
            }
            $grid->init();
            self::$instance = $grid; 
        }
        return self::$instance;
    }
    static public function ExtensionList(){
        $classes_extension = array();
        /* Расширения */
        $extension = glob(JPATH_COMPONENT_ADMINISTRATOR.DS.'assets/crono/ext/grid/*.php');
        if(is_array($extension)&& count($extension)>0){
            foreach($extension as $file){
                require_once($file);
                $name =  'vmCronoGrid_FieldExt_'. str_replace('.php','', JFile::getName($file));
                $classes = function_exists($name)?$name():array($name);
                foreach($classes as $classname){
                    if(class_exists($classname)){
                        $classes_extension[]= $name;
                    }
                }
            }
        }
        return $classes_extension;
    }
    /**
    * put your comment there...
    * 
    * @param mixed $fieldname
    * @param mixed $width
    * @return vmCronoGrid_FieldBasic
    */
    static public function getInstanceField($fieldname, $width=0){
       $fieldname_parts = explode('_', $fieldname);
       $class = $fieldname=='id'?'general':$fieldname_parts[0];
       $classname = "vmCronoGrid_FieldExt_".$class;       
       if(!class_exists($classname)) return false; 
       $field =  new $classname($fieldname, $width);
       $field->classname = $classname;
       return $field->gName?$field:false;
    }
}
/* Вспомогательный класс для формирования древовидной таблицы выбора полей */
class vmCronoGrid_FieldSelector{
    private $data;
    private $grid;
    
    private function __construct(){/* disable vmCronoGrid_FieldSelector() */}
    /**
    * Добавляет узел в дерево выбора полей 
    * 
    * @param string $caption
    * @param string $fieldname
    * @param int $level
    * @param int $parent
    * @param boolean $isLeaf
    * @param boolean $expanded
    */
    public function AddNode($caption, $fieldname, $level=0, $parent="",  $isLeaf=false, $expanded=false, $is_selected=null){
        if(is_null($is_selected)){
            $is_selected = isset($this->view->fields->$fieldname);
        }
        $checkbox = $fieldname?"<input type='checkbox' name='$fieldname' value='1' ".($is_selected?'checked="checked"':'')."/>":'';
        $id = count($this->data)+1;
        $field['id'] = $id;
        $field["caption"] = $caption;
        $field["field"] = $checkbox;
        $field["level"]=$level; 
        $field["parent"]="$parent";
        $field["isLeaf"]=$isLeaf;
        $field["expanded"]=$expanded;
        $field["loaded"]=true;
        $this->data[]=$field;
        return $id;
    }
    public function JsGet(){
         return 'CRG.FieldSelector.data='.json_encode($this->data);
    }
    /**
    * @param  vmCronoGrid_Grid grid
    * @return vmCronoGrid_FieldSelector
    */
    static public function getInstance($view_id){
        $model = VmModel::getModel('crono_grid');
        $selector = new self();
        $selector->view = $model->ViewGetByID($view_id);
        $class_names = vmCronoGrid_Grid::ExtensionList();
        foreach($class_names as $classname){
            call_user_func_array(array($classname, 'PrepareFieldSelector'), array(&$selector));
        }
        return $selector;
    }
}
/** Класс формирования JS конфигурации поля jqGrid */
class vmCronoGrid_JSField{
    /**
    * Defines the alignment of the cell in the Body layer, not in header cell. Possible values: left, center, right.
    * 
    * @var string    
    */
    public $align = 'left';
    /**
    * This function add attributes to the cell during the creation of the data - i.e dynamically. By example all valid attributes for the table cell can be used or a style attribute with different properties. The function should return string. Parameters passed to this function are:
    * 
    * @var function
    */
    public $cellattr;
    /**
    * the id of the row
    * 
    * @var int
    */
    public $rowId;
    /**
    * the value which will be added in the cell
    * 
    * @var mixed
    */
    public $val;
    /**
    * the raw object of the data row - i.e if datatype is json - array, if datatype is xml xml node.
    * 
    * @var mixed
    */
    public $rawObject;
    /**
    * all the properties of this column listed in the colModel
    * 
    * @var mixed
    */
    public $cm;
    /**
    * the data row which will be inserted in the row. This parameter is array of type name:value, where name is the name in colModel
    * 
    * @var mixed
    */
    public $rdata;
    /**
    * This option allow to add classes to the column. If more than one class will be used a space should be set. By example classes:'class1 class2' will set a class1 and class2 to every cell on that column. In the grid css there is a predefined class ui-ellipsis which allow to attach ellipsis to a particular row. Also this will work in FireFox too.    
    * 
    * @var string
    */
    public $classes;
    /** Governs format of sorttype:date (when datetype is set to local) and editrules {date:true} fields. Determines the expected date format for that column. Uses a PHP-like date formatting. Currently ”/”, ”-”, and ”.” are supported as date separators. Valid formats are:
    * y,Y,yyyy for four digits year
    * YY, yy for two digits year
    * m,mm for months
    * d,dd for days.
    * See Array Data     ISO Date (Y-m-d)
    * 
    * @var mixed
    */
    public $datefmt;
    /**
    * The default value for the search field. This option is used only in Custom Searching and will be set as initial search.
    * 
    * @var string
    */
    public $defval;
    /**
    * Defines if the field is editable. This option is used in cell, inline and form modules. See editing 
    * 
    * @var boolean
    */
    public $editable;
    /**
    *    array    Array of allowed options (attributes) for edittype option editing
    * 
    * @var mixed
    */
    public $editoptions;
    /**
    * sets additional rules for the editable field editing
    * 
    * @var array
    */
    public $editrules;
    /**
    * Defines the edit type for inline and form editing Possible values: text, textarea, select, checkbox, password, button, image and file. See also editing    text
    * 
    * @var string
    */
    public $edittype;
    /**
    * If set to asc or desc, the column will be sorted in that direction on first sort.Subsequent sorts of the column will toggle as usual
    * 
    * @var string
    */
    public $firstsortorder;
    /**
    * If set to true this option does not allow recalculation of the width of the column if shrinkToFit option is set to true. Also the width does not change if a setGridWidth method is used to change the grid width.    
    * 
    * @var boolean
    */
    public $fixed;
    /**
    * Defines various options for form editing. See Form options
    * 
    * @var array
    */
    public $formoptions;
    /**
    * Format options can be defined for particular columns, overwriting the defaults from the language file. See Formatter for more details
    * 
    * @var array
    */
    public $formatoptions;
    /**
    * The predefined types (string) or custom function name that controls the format of this field. See Formatter for more details.
    * 
    * @var mixed
    */
    public $formatter;
    /**
    * If set to true determines that this column will be frozen after calling the setFrozenColumns method
    * 
    * @var boolean
    */
    public $frozen;
    /**
    * If set to true this column will not appear in the modal dialog where users can choose which columns to show or hide. See Show/Hide Columns
    * 
    * @var boolean
    */
    public $hidedlg;
    /**
    * Defines if this column is hidden at initialization
    * 
    * @var boolean
    */
    public $hidden;
    /**
    * Set the index name when sorting. Passed as sidx parameter.    empty string
    * 
    * @var string
    */
    public $index;
    /**
    * Defines the json mapping for the column in the incoming json string. See Retrieving Data
    * 
    * @var string
    */
    public $jsonmap;
    /**
    * In case if there is no id from server, this can be set as as id for the unique row id. Only one column can have this property. If there are more than one key the grid finds the first one and the second is ignored.    
    * 
    * @var boolean
    */
    public $key;
    /**
    * When colNames array is empty, defines the heading for this column. If both the colNames array and this setting are empty, the heading for this column comes from the name property.
    * 
    * @var string
    */
    public $label;
    /**
    * Set the unique name in the grid for the column. This property is required. As well as other words used as property/event names, the reserved words (which cannot be used for names) include subgrid, cb and rn.    Required
    * 
    * @var string
    */
    public $name;
    /**
    * Defines if the column can be re sized
    * 
    * @var boolean
    */
    public $resizable;
    /**
    * When used in search modules, disables or enables searching on that column. Search Configuration
    * 
    * @var boolean
    */
    public $search;
    /**
    * Defines the search options used searching Search Configuration
    * 
    * @var array
    */
    public $searchoptions;
    /**
    * Defines is this can be sorted
    * 
    * @var boolean
    */
    public $sortable;
    /**
* mixed    Used when datatype is local. Defines the type of the column for appropriate sorting.Possible values:
* int/integer - for sorting integer
* float/number/currency - for sorting decimal numbers
* date - for sorting date
* text - for text sorting
* function - defines a custom function for sorting. To this function we pass the value to be sorted and it should return a value too.
* See Array Data     text
*/
    public $sorttype;
    /**
    * Determines the type of the element when searching. See Search Configuration    text
    * 
    * @var string
    */
    public $stype;
    /**
    * Valid only in Custom Searching and edittype : 'select' and describes the url from where we can get already-constructed select element    empty string
    * 
    * @var string
    */
    public $surl;
    /**
    * Set of valid properties for the colModel. This option can be used if you want to overwrite a lot of default values in the column model with easy. See cmTemplate in grid options
    * 
    * @var object
    */
    public $template;
    /**
    * If this option is false the title is not displayed in that column when we hover a cell with the mouse
    * 
    * @var boolean
    */
    public $title;
    /**
    * Set the initial width of the column, in pixels. This value currently can not be set as percentage
    * 
    * @var float 
    */
    public $width;
    /**
    * Defines the xml mapping for the column in the incomming xml file. Use a CSS specification for this See Retrieving Data    none
    * 
    * @var string
    */
    public $xmlmap;
    /**
    * Custom function to “unformat” a value of the cell when used in editing See Custom Formatter. (Unformat is also called during sort operations. The value returned by unformat is the value compared during the sort.)     
    * 
    * @var function
    */
    public $unformat;
    /**
    * This option is valid only when viewGridRow method is activated. When the option is set to false the column does not appear in view Form
    * 
    * @var boolean
    */
    public $viewable;
    public function widthSet($width){
        if($width)$this->width = $width;
    }
    public function jsGet(){
        $tmp = array();
        foreach($this as $k=>$v){
            if(is_null($v)) continue;
            $tmp[$k]=$v;
        }
        $js = json_encode($tmp);
        //var_dump($tmp, $json);
        return $js;
    }

    static public function getInstance($name, $template, $width){
        $txt_yes = JText::_('CRONO_VMGRID_YES');
        $txt_no  = JText::_('CRONO_VMGRID_NO');
        $field = new self();
        $template = strtolower($template);
        $field->name = $name;
        $field->index = $name;
        $field->editable = true;
        $field->align = "left";
        /* Установка специфических значений*/
        switch($template){
            case 'id':{
                $field->editable = false;
                $field->width = 15;
                $field->formatter = "CRG.Format.ID";
                break;
            }
            case 'published':
            case 'boolean':{
                if($template=='published'){
                    $field->edittype = 'custom';
                    $field->editoptions =  array('custom_element'=>'CRG.CustomInput', 'custom_value'=>'CRG.CustomOutput');
                }   
                else{
                    $field->edittype ='select';    
                    $field->editoptions = array("value"=> ":;1:$txt_yes;0:$txt_no", 'multiple'=>false, 'size'=>3);
                }    
                $field->align = "center";
                $field->width = 30;
                $field->formatter = "CRG.Format.Boolean";
                $field->unformat  = "CRG.Unformat.Boolean";    
                
                $field->stype ='select';
                $field->searchoptions = array("sopt" => array('eq'), 
                                              "value"=> ":;1:$txt_yes;0:$txt_no"
                                             );
                break;
            }
            case 'price':{
                $field->width = 30;
                $field->align = "right";
                $field->formatter = 'number';
                $field->formatoptions = array("decimalSeparator"=>".", "thousandsSeparator"=>" ", "decimalPlaces"=>2, "defaultValue"=>'0.00');
                $field->search = false;
                break;
            }
            case 'select':{
                $field->edittype = 'select';
                $field->formatter = 'select';
                $field->sortable = false; 
                $field->search = false;
                break;
            }
            case 'text':{
                $field->width = 80;
                $field->align = "right";
                $field->edittype = 'custom';
                $field->editoptions = array("custom_element"=>"CRG.CustomInput", "custom_value"=>"CRG.CustomOutput"); 
                break;
            }
            case 'category':{
                $field->sortable = false;
                $field->width = 100;
                $field->edittype = 'custom';
                $field->editoptions = array("custom_element"=>"CRG.CustomInput", "custom_value"=>"CRG.CustomOutput");
                break;
            }
            default:{
                $field->width = 120;
            }
        }
        $field->widthSet($width);
        return $field;
    }
}
/* Класс для описания возвращаемого результата при сохранении*/
class vmCronoGrid_SaveResult{
    /**
    * put your comment there...
    * 
    * @var boolean
    */
    public $saved = false;
    /**
    * put your comment there...
    * 
    * @var string
    */
    public $error = '';
    /**
    * put your comment there...
    * 
    * @var array of int
    */
    public $notsaved_ids = array();
    /***
    * string
    * 
    * @var mixed
    */
    public $message='';
    /**
    * put your comment there...
    * 
    * @var string
    */
    public $value='';
    /**
    * put your comment there...
    * 
    * @var string
    */
    public $displayvalue='';
    /**
    * Обновить таблицу 
    * 
    * @var bool 
    */
    public $reload;
}

/**************** Работа с полями **************/
interface vmCronoGrid_FieldInterface{
    public  function Init();
    /**
    * Формирование значения для jqGrid
    * @param mixed $product
    * @return string
    */
    public  function PrepareDisplay($product);
    /**
    * Сохранение значения поля
    * 
    * @param mixed $ids
    * @param mixed $value
    * @param vmCronoGrid_SaveResult
    */
    public  function Save($ids, $value, &$saveresult);
    /**
    * Добавление полей в селектор полей
    * 
    * @param vmCronoGrid_FieldSelector $selector
    * 
    */
    static  public  function PrepareFieldSelector(&$selector);
    /**
    * Формирование формы фильтра
    */
    static  public  function GenerateFilterFields();
    /** Формирование запроса на уровне поля. Вызывается по кол-ву полей в таблице 
    * @param array $select - ссылка на массив полей
    * @param array $join - ссылка на массив таблиц
    * @param array $where - ссылка на массив условий 
    */
    public function PrepareFieldSelect(&$select, &$join, &$where);
    /** Формирование запроса общее, вне котекста поля. Вызывается один раз для каждого класса */
    static public function PrepareGeneralSelect(&$select, &$join, &$where);
}
abstract class vmCronoGrid_Field    implements vmCronoGrid_FieldInterface{
    /** Обязательные поля, необходимые для формирования таблицы **/
    public $name;
    public $gName;
    public $gWidth;
    public $gCellTpl = 'string';
    public $gCaption;
    public $classname;
    /**
    * @var VirtueMartModelCrono_Grid
    */
    private static $model;
    public function __construct($name, $width=0){
        $this->name = $name;
        $this->gWidth = $width;
        $this->gName = $name;
        $this->init();
    }
    public function Init(){
        if(!self::$model){
            self::$model = VmModel::getModel('crono_export');
        }
    }
    public function JsConfiguration(){
        if(!$this->gCellTpl) JError::raiseError(0,'vmCronoGrid_Field property "grid_column_template" is not defined ');
        $jsfield = vmCronoGrid_JSField::getInstance($this->gName, $this->gCellTpl, $this->gWidth);
        return $jsfield->jsGet();
    }
}