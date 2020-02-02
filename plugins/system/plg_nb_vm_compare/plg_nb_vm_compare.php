<?php
/*------------------------------------
* System Compare Products for Virtuemart
* Author    CMSMart Team
* Copyright Copyright (C) 2012 http://cmsmart.net. All Rights Reserved.
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* Websites: http://cmsmart.net
* Email: team@cmsmart.net
* Technical Support:  Forum - http://bloorum.com/forums
* Version 1.0.0
-----------------------------------------------------*/
defined('_JEXEC') or die('Direct Access to ' . basename(__file__) .
    ' is not allowed.');
class plgSystemPLG_NB_VM_Compare extends JPlugin
{
    public function __construct(&$subject, $config)
    {
        if (JFactory::getApplication()->isAdmin()) {
            return;
        }
        parent::__construct($subject, $config);
    }
    function onAfterInitialise()
    {
        $app = JFactory::getApplication();
        if ($app->isSite()) {
        }
    }
    public function onAfterRoute ()
	{
	    $app = JFactory::getApplication();
		$doc = JFactory::getDocument();
		if($app->isAdmin()) return;

		$compare = JRequest::getCmd('compare',0);
        $tack = JRequest::getCmd('tack',0);
        if($compare){
            if (!class_exists( 'VmConfig' ))
        	   require_once (JPATH_ADMINISTRATOR . '/components/com_virtuemart/helpers/config.php');
            VmConfig::loadConfig();
            if (!class_exists('VmImage'))
    			require(JPATH_VM_ADMINISTRATOR . '/helpers/image.php');
            if (!class_exists('VmImage'))
                require(JPATH_VM_ADMINISTRATOR . '/helpers/currencydisplay.php');
            //Add
            if($tack == 'add'){
                $virtuemart_product_id = JRequest::getVar('virtuemart_product_id');
                $this->addproduct($virtuemart_product_id);
            }elseif($tack == 'remove'){
                $virtuemart_product_id = JRequest::getVar('product_id_remove');
                unset($_SESSION['compare'][$virtuemart_product_id]);
            }elseif($tack == 'remove_all'){
                unset($_SESSION['compare']);
            }
            die();
        }
	}
    function onAfterDispatch() {
        $document = JFactory::getDocument();
        $app = JFactory::getApplication();
        $option = JRequest::getVar('option');
        $view = JRequest::getVar('view');

        // var_dump($option);
        // die();

        // if($option != 'com_virtuemart') return false;

        $link_a = $this->params->get('link');
        $max = $this->params->get('max_item');
		if ($app->isAdmin()) return ;
		$isSEF = $app->getCfg('sef');
        if($isSEF){
           $link = '?compare=1&tack=add';
        }else{
           $link = '&compare=1&tack=add';
        }
        if($view == 'productdetails')  $link = '&compare=1';
        $plugin_url = 'plugins/system/plg_nb_vm_compare/assets';
        $document->addStyleSheet($plugin_url.'/css/style.css');
        $http = JURI::root(true);
        $blook = $this->params->get('element_block');
        $img_class = $this->params->get('img_class');
$js = <<<ENDJS
//<![CDATA[
var blook_compare = "$blook";
var link_compare = "$link_a";
var request_compare = "$link";
var http_compare = "$http";
var img_compare = "$img_class";
var max_compare = "$max";
//]]>
ENDJS;
$document->addScriptDeclaration("$js");
$document->addScript($plugin_url.'/js/compare.js');
$document->addStyleDeclaration("$blook{position: relative}");

    }
    protected function addproduct($id){
        $max = $this->params->get('max_item');
        if(count($_SESSION['compare']) == $max){
            echo 1;
//-        }elseif(in_array($id,$_SESSION['compare'])){ // *!* 2016-08-21
        }elseif(is_array($_SESSION['compare']) AND in_array($id,$_SESSION['compare'])){ // *!* 2016-08-21
            echo 2;
        }else{
            $_SESSION["compare"][$id] = $id;
            $proModel = VmModel::getModel('product'); //Lấy model
            $product = $proModel->getProduct($virtuemart_product_id); //Lấy sản phẩm
            $proModel->addImages($product);//Lấy hình ảnh
            $timthumb = str_replace('plugins/system/plg_nb_vm_compare/','',JURI::root());
//-            $img = $timthumb.$product->images[0]->file_url_thumb; // *!* 2016-08-21
			$img = JURI::root().$product->images[0]->file_url_folder_thumb.$product->images[0]->file_name_thumb.'.'.$product->images[0]->file_extension; // *!* 2016-08-21
            if(strlen($product->product_name) > 18){
                    $product->product_name = mb_substr($product->product_name,0,30) . '';
            }
            echo "<div class='nb_product_compare $product->virtuemart_product_id'>";
            echo "<img src='$img' height='50' width='55' />";
            echo "<span class='nb_name' title='$product->product_name'>";
            echo "<a href='$product->link' target='_blank'>$product->product_name</a>";
            echo "</span>";
            echo "<i class='nb_remove_product fa fa-times' title='Remove compare'></i>";
            echo "<input type='hidden' class='product_id_remove' value='$product->virtuemart_product_id' />";
            echo "</div>";
        }

	}
}

