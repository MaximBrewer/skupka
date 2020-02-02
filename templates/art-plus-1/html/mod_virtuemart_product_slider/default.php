<?php // no direct access

defined('_JEXEC') or die('Restricted access');
// add javascript for price and cart, need even for quantity buttons, so we need it almost anywhere

//получаем пользователя
$user    =& JFactory::getUser();
$user_id = $user->guest ? $_COOKIE['virtuemart_wish_session'] : $user->id;
$db =& JFactory::getDBO();

vmJsApi::jPrice();
?>
<div class="vmslider-wrap<?php echo $moduleclass_sfx; ?>">
    <?php if ($headerText) { ?>
        <div class="vmheader"><?php echo $headerText ?></div>
        <?php
    }
    ?>
    <div class="vmslider <?php if ($nav_nav == '1') echo 'nav-top';
    if ($nav_dots == '1' && $nav_nav == '0') echo 'nav-dots'; ?>">
        <?php foreach ($products as $product) { ?>
            <div class="vmslider-product-wrap">
                <div class="vmslider-product <?php if ($shadow == '1') echo 'shadow' ?>">
                    <?php
                    // Product image
                    $url = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id);
                    if (!empty($product->images[0]))
                        $image = '<div class="image1">' . $product->images[0]->displayMediaThumb('', false) . '</div>';
                    else $image = '';
                    ?>
                    <?php echo shopFunctionsF::renderVmSubLayout('customfields', array('product' => $product, 'position' => 'saxar', 'class' => 'sahar_a'));
                    ?>
                    <?php echo shopFunctionsF::renderVmSubLayout('customfields', array('product' => $product, 'position' => 'cvet_1', 'class' => 'cvet_a'));
                    ?>
                    <div class="vmslider-image <?php if ($twoImage == '2') echo 'zoom-image'; ?>">
                        <a href="<?php echo $url ?>">
                            <?php if ($twoImage == 0) {
                                if (!empty($product->images[1])) {
                                    $image2 = '<div class="image2">' . $product->images[1]->displayMediaThumb('', false) . '</div>';
                                    echo $image . $image2;
                                } else {
                                    $image = '<div class="oneimage">' . $product->images[0]->displayMediaThumb('', false) . '</div>';
                                    echo $image;
                                }
                            } else {
                                echo $image;
                            }
                            ?>
                        </a>
                    </div>
                    <div class="vmslider-name"><a href="<?php echo $url ?>">
                            <?php echo $product->product_name ?></a>
                    </div>
                    <?php
                    echo '<div class="productdetails">';
                    if ($show_price) {
                        echo '<div class="vmslider-price">';
                        echo shopFunctionsF::renderVmSubLayout('prices', array('product' => $product, 'currency' => $currency));
                        echo '</div>';
                    }
                    if ($show_addtocart) { ?>
 <!-- добавить в избранное -->
                        
                       <div class="stroka_kupit">
  <div class="wishlist">
    <?php
                            $form_favorite = '<form class="form_wishlist_products" style="display: inline-block; text-align: center; margin:0px" ';
                            $form_favorite .= ' method="POST" name="deletefavo" id="' . uniqid('deletefavo_') . '">
						                             <input type="hidden" name="option" value="com_ajax">
					                                 <input type="hidden" name="module" value="virtuemart_wishlist_products">
					                                 <input type="hidden" name="format" value="json">';

                           $q = "SELECT COUNT(*) FROM #__virtuemart_favorites WHERE user_id ='" . $user_id . "' AND product_id=" . $product->virtuemart_product_id;
                           $db->setQuery($q);
                           $result = $db->loadResult();
                            if ($result > 0) {
                                $form_favorite .= '<button class="modns button art-button art-button del" title="' . JText::_('') . '" >';
                                $form_favorite .= JText::_('VM_REMOVE_FAVORITE') . '</button>';
                                $form_favorite .= '<input type="hidden" name="mode" value="fav_del" />';
                            } else {
                                $form_favorite .= '<button class="modns button art-button art-button" title="' . JText::_('') . '" >';
                                $form_favorite .= JText::_('VM_ADD_TO_FAVORITES') . '</button>';
                                $form_favorite .= '<input type="hidden" name="mode" value="fav_add" />';
                            }
                            $form_favorite .= '<input type="hidden" name="favorite_id" value="' . $product->virtuemart_product_id . '" />';
                            $form_favorite .= '</form>';
                            echo $form_favorite;
                            ?>
  </div>
                         <!-- добавить в избранное end -->
                         <div class="vmslider-cart <?php if ($cartStyle == '1') echo 'sliderCart';
                        if ($customfield == '0') echo ' nofield'; ?>">
                           
                           <?php echo shopFunctionsF::renderVmSubLayout('addtocart', array('product' => $product)); ?>
                            <div class="wmvo_bistraj_pokypka">
                           <?php $link_ = JURI::base() . JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id); ?>
                           
                           {popup url="index.php?option=com_rsform&formId=12&tmpl=component" }{/popup}
                           <a data-link="<?php echo $link_; ?>" data-name="<?= $product->product_name ?>"
                               class="tovar_zakaz" href="index.php?option=com_rsform&formId=22&tmpl=component">Запросить<br>цену</a>
                           </div>
                           </div>
                        
                       </div>


                       


                        <?php

                    }

                    echo '</div>';

                    ?>

                </div>

            </div>

            <?php

        } ?>

    </div>

    <?php

    if ($footerText) : ?>

        <div class="vmfooter<?php echo $params->get('moduleclass_sfx') ?>">

            <?php echo $footerText ?>

        </div>

    <?php endif; ?>

</div>

<script>

    jQuery('.vmslider-wrap<?php echo $moduleclass_sfx; ?> .vmslider').slick({

        dots: <?php echo ($nav_dots == '1') ? 'true' : 'false'; ?>,

        arrows: <?php echo ($nav_nav == '2') ? 'false' : 'true'; ?>,

        infinite: <?php echo ($loop == '1') ? 'true' : 'false'; ?>,

        autoplay: <?php echo ($autoplay == '1') ? 'true' : 'false'; ?>,

        autoplaySpeed: <?php echo $autoplayTimeout; ?>,

        slidesToShow: <?php echo $large?>,

        slidesToScroll: 1,

        responsive: [

            {

                breakpoint: 1200,

                settings: {

                    slidesToShow: <?php echo $medium; ?>,

                }

            },

            {

                breakpoint: 992,

                settings: {

                    slidesToShow: <?php echo $small; ?>,

                }

            },

            {

                breakpoint: 768,

                settings: {

                    slidesToShow: <?php echo $extrasmall; ?>,

                }

            }

        ]

    });

</script>