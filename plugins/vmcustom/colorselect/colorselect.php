<?php
defined('_JEXEC') or 	die( 'Direct Access to ' . basename( __FILE__ ) . ' is not allowed.' ) ;
/**

 * @author Max Milbers
 * @version $Id:$
 * @package VirtueMart
 * @subpackage payment
 * @author Max Milbers
 * @copyright Copyright (C) 2004-2008 soeren - All rights reserved.
 * @copyirght Copyright (C) 2011 - 2014 The VirtueMart Team and authors
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
 *
 * http://virtuemart.org
 */

if (!class_exists('vmCustomPlugin')) require(JPATH_VM_PLUGINS . DS . 'vmcustomplugin.php');

class plgVmCustomColorSelect extends vmCustomPlugin {

	function __construct(& $subject, $config) {

		parent::__construct($subject, $config);

		$varsToPush = array(	
            'path_select'=>array('','string'),
            'path_img'=>array('','string'),
            'class_img'=>array('','string'),
            'class_add_img'=>array('','string'),
            'trigger'=>array('','bool'),
            'empty_option'=>array('','bool'),
            'hidden_add_img'=>array('','bool'),
            'textarea'=>array('','string')
		);

		$this->setConfigParameterable('customfield_params',$varsToPush);

	}

	// get product param for this plugin on edit
	function plgVmOnProductEdit($field, $product_id, &$row,&$retValue) {

		if ($field->custom_element != $this->_name) return '';

        $html ='
			<fieldset>
				<legend>'. JText::_('VMCUSTOM_COLORSELECT') .'</legend>
                
                <style>
                .virtuemart-admin-area fieldset #custom_fields .key{width: 50%}
                </style>
                
				<table class="admintable">
					'.VmHTML::row('input','VMCUSTOM_COLORSELECT_PATH_SELECT','customfield_params['.$row.'][path_select]',$field->path_select).'
                    '.VmHTML::row('input','VMCUSTOM_COLORSELECT_PATH_IMG','customfield_params['.$row.'][path_img]',$field->path_img).'
                    '.VmHTML::row('input','VMCUSTOM_COLORSELECT_CLASS_IMG','customfield_params['.$row.'][class_img]',$field->class_img).'
                    '.VmHTML::row('input','VMCUSTOM_COLORSELECT_CLASS_ADD_IMG','customfield_params['.$row.'][class_add_img]',$field->class_add_img).'
                    '.VmHTML::row('booleanlist','VMCUSTOM_COLORSELECT_TRIGGER','customfield_params['.$row.'][trigger]',$field->trigger).'
                    '.VmHTML::row('booleanlist','VMCUSTOM_COLORSELECT_EMPTY_OPTION','customfield_params['.$row.'][empty_option]',$field->empty_option).'
                    '.VmHTML::row('booleanlist','VMCUSTOM_COLORSELECT_HIDDEN_ADD_IMG','customfield_params['.$row.'][hidden_add_img]',$field->hidden_add_img).'
                    '.VmHTML::row('textarea','VMCUSTOM_COLORSELECT_TEXTAREA','customfield_params['.$row.'][textarea]',$field->textarea).'
				</table>
			</fieldset>';
        
		$retValue .= $html;
		$row++;
		return true ;
	}

	function plgVmOnDisplayProductFEVM3(&$product,&$group) {

		if ($group->custom_element != $this->_name) return '';
		$group->display .= $this->renderByLayout('default',array(&$product,&$group) );

		return true;
	}


	/**
	 * Function for vm3
	 * @see components/com_virtuemart/helpers/vmCustomPlugin::plgVmOnViewCart()
	 * @author Patrick Kohl
	 */
	function plgVmOnViewCart($product,$row,&$html) {
		if (empty($product->productCustom->custom_element) or $product->productCustom->custom_element != $this->_name) return '';
		if (!$plgParam = $this->GetPluginInCart($product)) return '' ;

		foreach($plgParam as $k => $item){

			if(!empty($item) ){
				if($product->productCustom->virtuemart_customfield_id==$k){
					$html .='<span>'.vmText::_($product->productCustom->custom_title).' '.$item.'</span>';
				}
			}
		}
		return true;
	}

	/**
	 * Trigger for VM3
	 * @author Max Milbers
	 * @param $product
	 * @param $productCustom
	 * @param $html
	 * @return bool|string
	 */
	function plgVmOnViewCartVM3(&$product, &$productCustom, &$html) {
		if (empty($productCustom->custom_element) or $productCustom->custom_element != $this->_name) return false;

		if(empty($product->customProductData[$productCustom->virtuemart_custom_id][$productCustom->virtuemart_customfield_id])) return false;
        
        $currency = CurrencyDisplay::getInstance(); 
        
		foreach( $product->customProductData[$productCustom->virtuemart_custom_id] as $k =>$item ) {
			if($productCustom->virtuemart_customfield_id == $k) {
				if(isset($item)){
                    // получаем список параметров из настроек
                    $textarea = $productCustom->textarea;
                    $path_select = $productCustom->path_select; // путь к папке с изображениями для select
                    
                    // каждую строку помещаем в массив                    
                    if(strpos($textarea, '&#13;&#10;')){
                       $textRows = explode("&#13;&#10;",$textarea); 
                    } else {
                       $textRows = explode("\r\n",$textarea); 
                    }
                    
                    foreach($textRows as $textRow){
                        // получаем массив параметров
                        $option = explode('}{',$textRow);
                        
                        //удаляем скобки у первого и последнего параметра
                        $option[0] = str_replace('{', '', $option[0]);
                        $option[3] = str_replace('}', '', $option[3]);
                        
                        $optionName = $option[0];
                        
                        // название совпадает с выбранным значением в настраиваемом поле
                        if($optionName == $item){
                           if(isset($option[3]) && !empty($option[3])){
                               $optionPrice = ' ('.$option[3].$currency->getSymbol().')'; 
                            } else{
                               $optionPrice = ''; 
                            }
                            
                            if(!empty($option[1]) && !empty($productCustom->path_select)){
                                $data_img = $path_select.$option[1];
                                $img = '<img src="'.$data_img.'">';
                            } else {
                                $img ='';
                            }
                           $html .= '<span class="color-select">'.vmText::_($productCustom->custom_title).'<br> '.$img.$optionName.$optionPrice.'</span>';
                        }
                    }
				}
			}
		}
		return true;
	}

    function plgVmOnViewCartModuleVM3( &$product, &$productCustom, &$html) {
        return $this->plgVmOnViewCartVM3($product,$productCustom,$html);
    }

    function plgVmDisplayInOrderBEVM3( &$product, &$productCustom, &$html) {
        $this->plgVmOnViewCartVM3($product,$productCustom,$html);
    }

	function plgVmDisplayInOrderFEVM3( &$product, &$productCustom, &$html) {
		$this->plgVmOnViewCartVM3($product,$productCustom,$html);
	}


	/**
	 *
	 * vendor order display BE
	 */
	function plgVmDisplayInOrderBE(&$item, $productCustom, &$html) {
		if(!empty($productCustom)){
			$item->productCustom = $productCustom;
		}
		if (empty($item->productCustom->custom_element) or $item->productCustom->custom_element != $this->_name) return '';
		$this->plgVmOnViewCart($item,$productCustom,$html); //same render as cart
    }


	/**
	 *
	 * shopper order display FE
	 */
	function plgVmDisplayInOrderFE(&$item, $productCustom, &$html) {
		if(!empty($productCustom)){
			$item->productCustom = $productCustom;
		}
		if (empty($item->productCustom->custom_element) or $item->productCustom->custom_element != $this->_name) return '';
		$this->plgVmOnViewCart($item,$productCustom,$html); //same render as cart
    }



	/**
	 * Trigger while storing an object using a plugin to create the plugin internal tables in case
	 *
	 * @author Max Milbers
	 */
	public function plgVmOnStoreInstallPluginTable($psType,$data,$table) {

		if($psType!=$this->_psType) return false;
		if(empty($table->custom_element) or $table->custom_element!=$this->_name ){
			return false;
		}
		if(empty($table->is_input)){
			vmInfo('COM_VIRTUEMART_CUSTOM_IS_CART_INPUT_SET');
			$table->is_input = 1;
			$table->store();
		}
		//Should the textinput use an own internal variable or store it in the params?
		//Here is no getVmPluginCreateTableSQL defined
 		//return $this->onStoreInstallPluginTable($psType);
	}

	/**
	 * Declares the Parameters of a plugin
	 * @param $data
	 * @return bool
	 */
	function plgVmDeclarePluginParamsCustomVM3(&$data){

		return $this->declarePluginParams('custom', $data);
	}

	function plgVmGetTablePluginParams($psType, $name, $id, &$xParams, &$varsToPush){
		return $this->getTablePluginParams($psType, $name, $id, $xParams, $varsToPush);
	}

	function plgVmSetOnTablePluginParamsCustom($name, $id, &$table,$xParams){
		return $this->setOnTablePluginParams($name, $id, $table,$xParams);
	}

	/**
	 * Custom triggers note by Max Milbers
	 */
	function plgVmOnDisplayEdit($virtuemart_custom_id,&$customPlugin){
		return $this->onDisplayEditBECustom($virtuemart_custom_id,$customPlugin);
	}

	public function plgVmPrepareCartProduct(&$product, &$customfield,$selected,&$modificatorSum){
        
        if ($customfield->custom_element !==$this->_name) return ;
		
        // получаем список из настроек
        $textarea = $customfield->textarea;

        // каждую строку помещаем в массив    
        if(strpos($textarea, '&#13;&#10;')){
           $textRows = explode("&#13;&#10;",$textarea); 
        } else {
           $textRows = explode("\r\n",$textarea); 
        }

        foreach($textRows as $textRow){
            // получаем массив параметров
            $option = explode('}{',$textRow);
            
            //удаляем скобки у первого и последнего параметра
            $option[0] = str_replace('{', '', $option[0]);
            $option[3] = str_replace('}', '', $option[3]);

            $optionName = $option[0];
            
            // название совпадает с выбранным значением в настраиваемом поле
            if($optionName == $selected){
               if(isset($option[3]) && !empty($option[3])){
                   $custom_price = substr($option[3], 1);
                   if(substr($option[3], 0, 1) == '+'){
                      $modificatorSum += $custom_price; 
                   } else{
                        $modificatorSum -= $custom_price; 
                   }   
               }
            }
        }

		return true;
	}


	public function plgVmDisplayInOrderCustom(&$html,$item, $param,$productCustom, $row ,$view='FE'){
		$this->plgVmDisplayInOrderCustom($html,$item, $param,$productCustom, $row ,$view);
	}

	public function plgVmCreateOrderLinesCustom(&$html,$item,$productCustom, $row ){
// 		$this->createOrderLinesCustom($html,$item,$productCustom, $row );
	}
	function plgVmOnSelfCallFE($type,$name,&$render) {
		$render->html = '';
	}

}

// No closing tag