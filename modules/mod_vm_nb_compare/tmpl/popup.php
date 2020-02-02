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
$user = JFactory::getUser();
if (!class_exists( 'VmConfig' ))
	require_once (JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
    VmConfig::loadConfig();
if (!class_exists('VmImage'))
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'image.php');
/************************************************************************************************/
/*Load language*/
$jlang = JFactory::getLanguage();
$jlang->load('com_virtuemart', JPATH_SITE.'/components/com_virtuemart', $jlang->getDefault(), true);
$jlang->load('com_virtuemart', JPATH_ADMINISTRATOR.'/components/com_virtuemart',$jlang->getDefault(), true);

/*---Khai báo---------------------------------------------------------------------------------------------*/
$product_name = array();
$images = array();
$price = array();
$description = array();
$manufacturer = array();
$availability = array();
$sku = array();
$weight = array();
$length = array();
$cart = array();
$category = array();
$customfields = array();
$http = str_ireplace('modules/mod_vm_nb_compare/tmpl/','',JURI::root());

/*-----Lấy param-----------------------*/
$module = JModuleHelper::getModule('mod_vm_nb_compare');
$moduleParams=json_decode($module->params);
$addtocart = $moduleParams->addtocart;
$ratings = $moduleParams->rating;
$prices = $moduleParams->price;
$descriptions = $moduleParams->description;
$categorys = $moduleParams->category;
$manufacturers = $moduleParams->manufacturer;
$availabilitys = $moduleParams->availability;
$product_skus = $moduleParams->product_sku;
$weights = $moduleParams->weight;
$lengths = $moduleParams->length;
//$customs = $moduleParams->custom;
$customs = 0;
//$customtitleArray = explode('|',$customtitle);
/*----Get ID ------------*/

/*------Lấy data và gán giá trị----------------------------------------------------------------------------------------------*/
foreach($_SESSION['compare'] as $key=>$product_id){
    /*----Lấy data---------------------------------------------*/
    $imgrating = '';
    $showRatingHtml = '';
    $echo = '';
    $app = JFactory::getApplication('site');
    $proModel = VmModel::getModel('product'); //Lấy model
    $product = $proModel->getProduct($product_id); //Lấy sản phẩm
    $proModel->addImages($product);//Lấy hình ảnh
    $currency = CurrencyDisplay::getInstance( );
    /*--------Lấy rating-----------------------*/
    $ratingModel = VmModel::getModel('ratings');
    $showRating = $ratingModel->getRatingByProduct($product->virtuemart_product_id);

    $assets_img = $http.'modules/mod_vm_nb_compare/assets/images/';
    if($showRating==null) $imgrating =' <img src="'.$assets_img.'rating/stars_00.png" />';
    if($showRating!=null && $showRating->rating){
        $maxrating = VmConfig::get('vm_maximum_rating_scale',5);
        if (empty($showRating->rating))
            $echo = JText::_('COM_VIRTUEMART_RATING').' '.JText::_('COM_VIRTUEMART_UNRATED');
        if (!empty($showRating->rating)) {
            $showRatingHtml =  '<img src="'.$assets_img.'rating/stars_'.round($showRating->rating).'0.png" alt="'.round($showRating->rating).'stars out of 5" />';
        } else {
            $showRatingHtml = '<img src="'.$assets_img.'rating/stars_00.png" alt="'.round($showRating->rating).' stars out of 5" />';
        }
    }
    /*-------------end rating-----------------------*/
    /*---Lấy giá---------------------------------*/
    $show_prices  = VmConfig::get('show_prices',1);
        if ($show_prices == '1') {
            if (empty($product->prices['salesPrice']) and VmConfig::get ('askprice', 1) and  !$product->images[0]->file_is_downloadable) {
                $jtextprice = JText::_ ('COM_VIRTUEMART_PRODUCT_ASKPRICE');
            }
                $currencyHtml = $currency->createPriceDiv ('variantModification', 'COM_VIRTUEMART_PRODUCT_VARIANT_MOD', $product->prices);
            if (round($product->prices['salesPriceWithDiscount'],$currency->_priceConfig['salesPrice'][1]) != $product->prices['salesPrice']) {
                $currencyHtml2 = $currency->createPriceDiv ('salesPriceWithDiscount', 'COM_VIRTUEMART_PRODUCT_SALESPRICE_WITH_DISCOUNT', $product->prices);
            }
            $currencyHtml3 = $currency->createPriceDiv ('basePrice', 'Price: ', $product->prices);
            $currencyHtml4 = $currency->createPriceDiv ('salesPrice', '', $product->prices);
            $unitPriceDescription = JText::sprintf ('COM_VIRTUEMART_PRODUCT_UNITPRICE', $product->product_unit);
            $currencyHtml5 = $currency->createPriceDiv ('unitPrice', $unitPriceDescription, $product->prices);
        }
    /*----------End lấy giá-----------------*/
    /*----------Customfield-----------------*/
    $customfieldsModel = VmModel::getModel ('Customfields');
    if ($product->customfields){
        if (!class_exists ('vmCustomPlugin')) {
            require(JPATH_VM_PLUGINS . DS . 'vmcustomplugin.php');
        }
        $customfieldsModel -> displayProductCustomfieldFE ($product, $product->customfields);
    }

    if($customs){
        foreach($product->customfields as $value){
            if($value->is_cart_attribute){
                $customfields[$key][$value->custom_title] = $value->display;
            }
        }
    }
    if(empty($customfields[$key])){
        $customfields[$key]['No'] = '';
    }
    //foreach($customfields)
   /*----------End Customfield-----------------*/
    /*--Addtocart-----------------------------*/

    /*------------Gán giá trị-------------------------------*/
    //test customfield



    //
    /*Get manufacturer*/

    //$product->virtuemart_manufacturer_id
    if($product->virtuemart_manufacturer_id)  {
        $maf = array();
        $proMaf = VmModel::getModel('manufacturer');
        foreach($product->virtuemart_manufacturer_id as $m){
            $mafs = $proMaf->getManufacturer($m);
            $maf[] = $mafs->mf_name;
        }
    }
    if($product->categoryItem)  {
        $cate = array();
        foreach($product->categoryItem as $m){
            $cate[] = $m['category_name'];
        }
    }

    if($showRating==null){
        $ratingcount = 0;
    }else{
        $ratingcount = $showRating->ratingcount;
    }
    $rating[$key] = $imgrating.$echo.$showRatingHtml. ' Review(' . $ratingcount . ')';
    $product_name[$key]=$product->product_name;
//    $images[$key] = $http.$product->images[0]->file_url_thumb; // *!* 2016-08-21
    $images[$key] = '/'.$product->images[0]->file_url_folder_thumb.$product->images[0]->file_name_thumb.'.'.$product->images[0]->file_extension; // *!* 2016-08-21
    $price[$key]= $currencyHtml4;
    $description[$key] = $product->product_desc;
    $manufacturer[$key] = implode(', ',$maf);
    $availability[$key] = $product->product_in_stock;
    $sku[$key] = $product->product_sku;
    $weight[$key] = $product->product_weight.' '.strtolower($product->product_weight_uom);
    $length[$key] = $product->product_length . ' ' . strtolower($product->product_lwh_uom);
    $category[$key] = implode(', ',$cate);
	 $url_ = JRoute::_ ('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' .$product_id . '&virtuemart_category_id='. $product->categoryItem[0]['virtuemart_category_id']);
    $url[$key]=str_replace('/modules/mod_vm_nb_compare/tmpl',"",$url_);
// Kiểm tra lại virtuemart_manufacturer_id
}
$cart_check = $_GET['view'];
/*-------------------------------------------------------------------------------------------------*/

?>
<script src="<?php echo $http ?>modules/mod_vm_nb_compare/assets/js/vmprices.js"></script>

<link href="<?php echo $http ?>modules/mod_vm_nb_compare/assets/css/chosen.css" rel="stylesheet" type="text/css" />
<div id="popup_compare">
    <table class="popup_table">
        <tr class="tr_compare_name">
        	<td class="header_compare">Наименование</td>
            <!-- lặp -->
            <?php foreach($product_name as $key=>$value){ ?>
        	<td class="name_compare product_compare_item <?php echo $key ?>">
             
              <a href="<?=$url[$key]?>">
              <?php echo $value ?> 
              </a>               
            </td>
            <?php } ?>
        </tr>
        <tr class="tr_compare_images">
        	<td class="header_compare">Фото</td>
            <!-- lặp -->
            <?php foreach($images as $key=>$value){ ?>
        	<td class="images_compare product_compare_item <?php echo $key ?>">
                <a href="<?=$url[$key] ?>">
                    <img src="<?php echo $value ?>"/>
                </a>


            </td>
            <?php } ?>
        </tr>
        <tr class="tr_compare_rating">
        	<td class="header_compare">Рейтинг</td>
            <!-- lặp -->
            <?php foreach($rating as $key=>$value){ ?>
        	<td class="rating_compare product_compare_item <?php echo $key ?>"><?php echo $value ?></td>
            <?php } ?>
        </tr>
        <tr class="tr_compare_price">
        	<td class="header_compare">Цена</td>
            <!-- lặp -->
            <?php foreach($price as $key=>$value){ ?>
        	<td class="price_compare product_compare_item <?php echo $key ?>"><?php echo $value ?></td>
            <?php } ?>
        </tr>
        <tr class="tr_compare_description">
        	<td class="header_compare">Описание</td>
            <!-- lặp -->
            <?php foreach($description as $key=>$value){ ?>
        	<td class="description_compare product_compare_item <?php echo $key ?>">
                <div class="description_product_compare"><?php echo $value ?></div>
            </td>
            <?php } ?>
        </tr>
        <tr class="tr_compare_category">
        	<td class="header_compare">Категория</td>
            <!-- lặp -->
            <?php foreach($category as $key=>$value){?>
        	<td class="category_compare product_compare_item <?php echo $key ?>"><?php echo $value ?></td>
            <?php } ?>
        </tr>
        <tr class="tr_compare_manufacturer">
        	<td class="header_compare">Производитель</td>
            <!-- lặp -->
            <?php foreach($manufacturer as $key=>$value){?>
        	<td class="manufacturer_compare product_compare_item <?php echo $key ?>"><?php echo $value ?></td>
            <?php } ?>
        </tr>
        <tr class="tr_compare_availability">
        	<td class="header_compare">Наличие</td>
            <!-- lặp -->
            <?php foreach($availability as $key=>$value){ ?>
        	<td class="availability_compare product_compare_item <?php echo $key ?>">
                <?php
                    if($value){
                ?>
                    <span class="availability_TF">В наличии</span><span class="availability_item"><?php echo $value ?> item(s)</span>
                <?php
                    }else{
                ?>
                    <span class="availability_TF">Отсутсвует</span>
                <?php } ?>
            </td>
            <?php } ?>
        </tr>
        <?php /* ?>
        <tr class="tr_compare_availability">
            <td class="header_compare">Customfield</td>
            <!--Foreach-->
            <?php foreach($customfields as $key=>$customfield){ ?>
            <td class="product_compare_item customfield_compare <?php echo $key ?>">
                <?php foreach($customfield as $title=>$display){ ?>
                    <div class="customs-field-compare">
                        <span class="label-custom"><?php echo $title ?></span>
                        <span><?php echo $display ?></span>
                    </div>
                <?php } ?>
            </td>
            <?php } ?>
            <!--End-->
        </tr>
        <?php */?>
        <tr class="tr_compare_sku">
        	<td class="header_compare">Артикул</td>
            <!-- lặp -->
            <?php foreach($sku as $key=>$value){ ?>
        	<td class="sku_compare product_compare_item <?php echo $key ?>">
                <?php
                    if($value) {
                        echo $value;
                    }else{
                        echo 'None';
                    }
                ?>
            </td>
            <?php } ?>
        </tr>
        <tr class="tr_compare_weight">
        	<td class="header_compare">Вес</td>
            <!-- lặp -->
            <?php foreach($weight as $key=>$value){ ?>
        	<td class="weight_compare product_compare_item <?php echo $key ?>"><?php echo $value ?></td>
            <?php } ?>
        </tr>
        <tr class="tr_compare_length">
        	<td class="header_compare">Параметры</td>
            <!-- lặp -->
            <?php foreach($length as $key=>$value){ ?>
        	<td class="length_compare product_compare_item <?php echo $key ?>"><?php echo $value ?></td>
            <?php } ?>
        </tr>
        <tr class="tr_compare_action">
        	<td class="header_compare">Действия</td>
            <!-- lặp -->
            <?php
            foreach($_SESSION['compare'] as $key=>$product_id){
                $app = JFactory::getApplication('site');
                $proModel = VmModel::getModel('product'); //Lấy model
                $product = $proModel->getProduct($product_id); //Lấy sản phẩm
                $proModel->addImages($product);//Lấy hình ảnh
                $currency = CurrencyDisplay::getInstance( );
            ?>
        	<td class="action_compare product_compare_item <?php echo $key ?>">
           
            <!--Addtocart-->
                <?php if($cart_check != 'cart'){ ?>
                <form method="post" class="product" action="<?php echo JRoute::_ ('/compare/',false); ?>">
                    <!--Xóa mất đoạn cumtomfied-->
                    <div class="addtocart-bar-plg">
                        <!-- <label for="quantity<?php echo $product->virtuemart_product_id;?>" class="quantity_box"><?php echo JText::_ ('COM_VIRTUEMART_CART_QUANTITY'); ?>: </label> -->
                        <span class="quantity-box">
             			    <input type="hidden" class="quantity-input" name="quantity[]" value="1"/>
                  		</span>
                        <span class="addtocart-button-plg">
                            <i class="fa fa-shopping-cart"></i>
                            <input type="submit" name="addtocart" class="addtocart-button addtocart-plg-button" title="Купить" value="Купить" />
                        </span>
                        <div class="clear"></div>
                    </div>
                    <input type="hidden" class="pname" value="<?php echo $product->product_name ?>"/>
                    <input type="hidden" name="view" value="cart" class="noscript"/>
                    <input type="hidden" name="virtuemart_product_id[]" value="<?php echo $product->virtuemart_product_id ?>"/>
                    <input type="hidden" name="virtuemart_category_id[]" value="<?php echo $product->virtuemart_category_id ?>"/>
                    <input type="hidden" name="option" value="com_virtuemart"/>
                </form>
                <?php } ?> <div class="remove_compare_popup">
                <i class="fa fa-trash-o"></i>Удалить
                <input type="hidden" value="<?php echo $product->virtuemart_product_id?>" class="id_product_remove"/>
            </div>
            </td>
            <?php } ?>
        </tr>
    </table>
</div>
<script type="text/javascript">
    jQuery(document).ready(function(){
        //jQuery(".customs-field-compare").find('.vm-chzn-select').attr('multiple','');
        //jQuery(".customs-field-compare").find('.vm-chzn-select').chosen();
       var table_w = (230 * <?php echo count($_SESSION['compare']) ?>) + 230;
       jQuery('#popup_compare').width(table_w);
       if(<?php echo count($_SESSION['compare']) ?> == 2){
            jQuery('#popup_compare').width(920);
       }
       jQuery('.remove_compare_popup').click(function(){
            var product_id_remove = jQuery(this).find('.id_product_remove').val();
            jQuery.ajax({
          		url: '<?php echo $http ?>/plugins/system/plg_nb_vm_compare/process.php',
          		cache: false,
          		type: 'POST',
                data:{product_id_remove:product_id_remove},
          		success: function(html) {
                    jQuery('span.compa'+product_id_remove).closest('.btn-compare').removeClass('in-comparison');
                    var count_ss = jQuery('.name_compare').length;
                    if(count_ss > 1){
                        var table_w_r = (230 * count_ss) + 230;
                        jQuery('#popup_compare').width(table_w_r);
                    }else{
                        jQuery('#popup_compare').width(920);
                    }
                    var count_ss_r = jQuery('.name_compare').length;
                    if(count_ss_r == 0){
                        jQuery('.nb_no-item').show(500);
                    }
                    var total = jQuery('.nb_product_compare').length;
                    jQuery('.toltal-compare').text(total);
          		}
            });
             jQuery('.' + product_id_remove).remove();
       });




    });
</script>
<style type="text/css">
    <?php if(!$addtocart){ ?> .addtocart-button-plg{display:none} <?php } ?>
    <?php if(!$ratings){ ?> .tr_compare_rating{display:none} <?php } ?>
    <?php if(!$prices){ ?> .tr_compare_price{display:none} <?php } ?>
    <?php if(!$descriptions){ ?> .tr_compare_description{display:none} <?php } ?>
    <?php if(!$categorys){ ?> .tr_compare_category{display:none} <?php } ?>
    <?php if(!$manufacturers){ ?> .tr_compare_manufacturer{display:none} <?php } ?>
    <?php if(!$availabilitys){ ?> .tr_compare_availability{display:none} <?php } ?>
    <?php if(!$product_skus){ ?> .tr_compare_sku{display:none} <?php } ?>
    <?php if(!$weights){ ?> .tr_compare_weight{display:none} <?php } ?>
    <?php if(!$lengths){ ?> .tr_compare_length{display:none} <?php } ?>
</style>