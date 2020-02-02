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
defined('_JEXEC') or die('Restricted access');
$cart_check = JRequest::getVar('view');
if(!empty($_SESSION['compare'])){
    $toltal_compare = count($_SESSION['compare']);
}else{
    $toltal_compare = 0;
}
?>
<style type="text/css">
    <?php if(!$addtocart){ ?> .addtocart-button-plg{display:none} <?php } ?>
    <?php if(!$rating){ ?> .tr_compare_rating{display:none} <?php } ?>
    <?php if(!$price){ ?> .tr_compare_price{display:none} <?php } ?>
    <?php if(!$description){ ?> .tr_compare_description{display:none} <?php } ?>
    <?php if(!$category){ ?> .tr_compare_category{display:none} <?php } ?>
    <?php if(!$manufacturer){ ?> .tr_compare_manufacturer{display:none} <?php } ?>
    <?php if(!$availability){ ?> .tr_compare_availability{display:none} <?php } ?>
    <?php if(!$product_sku){ ?> .tr_compare_sku{display:none} <?php } ?>
    <?php if(!$weight){ ?> .tr_compare_weight{display:none} <?php } ?>
    <?php if(!$length){ ?> .tr_compare_length{display:none} <?php } ?>
</style>
<!-- HTML -->
<div class="nb_compare">
    <!-- short tab compare -->
<?php if($position != 'none'){ ?>
    <div class="nb_sort_compare">
        <span></span>
    </div>
<?php } ?>
    <!-- List compare -->
    <div class="nb_list_compare">
        <h3>
        <?php if($position != 'none'){ ?><i class="nb_hide_list fa fa-random"></i><?php }?>
        СРАВНЕНИЕ (<i class="toltal-compare"><?php echo $toltal_compare ?></i>)

        </h3>
        <div class="nb_list">
            <!--Show list-->
            <?php

                if(!empty($_SESSION['compare'])){
                foreach($_SESSION['compare'] as $key=>$product_id){
                    $proModel = VmModel::getModel('product'); //Lấy model
                    $product = $proModel->getProduct($product_id); //Lấy sản phẩm
                    $proModel->addImages($product);//Lấy hình ảnh
//-                    $img = JURI::root().$product->images[0]->file_url_thumb; // *!* 2016-08-21
                    $img = JURI::root().$product->images[0]->file_url_folder_thumb.$product->images[0]->file_name_thumb.'.'.$product->images[0]->file_extension; // *!* 2016-08-21
            ?>
                <div class="nb_product_compare <?php echo $product->virtuemart_product_id ?>">
                    <img src="<?php echo $img ?>" height="50" width="55" />
                    <span class="nb_name" title="<?php echo $product->product_name ?>">
                        <a href="<?php echo $product->link ?>" target="_blank">
                            <?php
                                if(strlen($product->product_name) > 18){
                                    $product->product_name = mb_substr($product->product_name,0,30) . '';
                                }
                                echo $product->product_name;
                            ?>
                        </a>
                    </span>
                    <i class="nb_remove_product fa fa-times" title="Убрать из избранного"></i>
                    <input type="hidden" class="product_id_remove" value="<?php echo $product->virtuemart_product_id ?>" />
                </div>
            <?php } } ?>
                <div class="nb_no-item" <?php if(!empty($_SESSION['compare'])){ ?> style="display: none;"  <?php } ?>>
                    Нет продуктов для сравнения
                </div>
        </div>
        <div class="nb_action">
            <span class="nb_start_compare" data-link="<?= $link ?>"><i class="fa fa-random"></i>Сравнить</span>
        </div>
    </div>
</div>
<!-- Css -->
<?php if($position != 'none'){ ?>
<style type="text/css">
    .nb_compare{
        position: fixed;
        z-index:9999!important;
        top:110px;
        box-shadow:0px 1px 1px 1px #eee;
        <?php if($position == 'left'){?>
        left:0px;
        <?php }elseif($position == 'right'){?>
        right:0px;
        <?php } ?>
    }
    .nb_compare .nb_sort_compare span{
        <?php if($position == 'left'){?>
            background: url('<?php echo JURI::base(true) ?>/modules/mod_vm_nb_compare/assets/images/bg_left.png');
        <?php }else{?>
            background: url('<?php echo JURI::base(true) ?>/modules/mod_vm_nb_compare/assets/images/bg_right.png');
        <?php } ?>
    }
</style>
<?php } ?>

<a class="nb_click_comparison" href="<?php echo JURI::root(true) ?>/modules/mod_vm_nb_compare/tmpl/popup.php?view=<?php echo $cart_check ?>"></a>























