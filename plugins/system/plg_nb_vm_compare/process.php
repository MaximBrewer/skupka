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
include_once dirname(__FILE__) . '/bootstrap.php';
defined('_JEXEC') or die('Direct Access to ' . basename(__file__) . ' is not allowed.');
$user = JFactory::getUser();
if (!class_exists( 'VmConfig' ))
	require_once (JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
    VmConfig::loadConfig();
if (!class_exists('VmImage'))
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'image.php');
/*----------------------POST-------------------------------------*/
$virtuemart_product_id = (!empty($_POST['product_id'])? $_POST['product_id']:0);
$product_id_remove = (!empty($_POST['product_id_remove'])? $_POST['product_id_remove']:0);
$clear_all = (!empty($_POST['clear_all'])? $_POST['clear_all']:0);
/*-------------REMOVE--------------------------------------------------------------*/

if($product_id_remove != 0){
    unset($_SESSION['compare'][$product_id_remove]);
}

/*-------------CLEAR ALL--------------------------------------------------------------*/
if($clear_all){
    unset($_SESSION['compare']);
}
/**-----------------ADD----------------------------------------------------*/
if($virtuemart_product_id){
?>
    <script type="text/javascript">
        jQuery(document).ready(function(){
           jQuery('.nb_no-item').hide();
        });
    </script>
<?php
foreach($_SESSION['compare'] as $virtuemart_product_id){
$proModel = VmModel::getModel('product'); //Lấy model
$product = $proModel->getProduct($virtuemart_product_id); //Lấy sản phẩm
$proModel->addImages($product);//Lấy hình ảnh
$timthumb = str_replace('plugins/system/plg_nb_vm_compare/','',JURI::root());
//$img = $timthumb.$product->images[0]->file_url_thumb; // *!* 2016-08-21
$img = JURI::root().$product->images[0]->file_url_folder_thumb.$product->images[0]->file_name_thumb.'.'.$product->images[0]->file_extension; // *!* 2016-08-21
?>

<div class="nb_product_compare <?php echo $product->virtuemart_product_id ?>">
    <img src="<?php echo $img ?>" height="50" width="55" />
    <span class="nb_name" title="<?php echo $product->product_name ?>">
        <a href="<?php echo $product->link ?>" target="_blank">
            <?php
                if(strlen($product->product_name) > 18){
                    $product->product_name = substr($product->product_name,0,15) . '...';
                    }
                echo $product->product_name;
            ?>
        </a>
    </span>
    <i class="nb_remove_product fa fa-times" title="Remove compare"></i>
    <input type="hidden" class="product_id_remove" value="<?php echo $product->virtuemart_product_id ?>" />
</div>
<?php }} ?>