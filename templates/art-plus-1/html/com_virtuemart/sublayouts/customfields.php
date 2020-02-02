<?php
/**
 * sublayout products
 *
 * @package    VirtueMart
 * @author Max Milbers
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2014 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL2, see LICENSE.php
 * @version $Id: cart.php 7682 2014-02-26 17:07:20Z Milbo $
 */

defined('_JEXEC') or die('Restricted access');

$product = $viewData['product'];
$position = $viewData['position'];


$customTitle = isset($viewData['customTitle']) ? $viewData['customTitle'] : false;;
if (isset($viewData['class'])) {
    $class = $viewData['class'];
} else {
    $class = 'product-fields';
}

if (!empty($product->customfieldsSorted[$position])) {
    ?>
    <div class="<?php echo $class ?>">
        <?php
        if ($customTitle and isset($product->customfieldsSorted[$position][0])) {
            $field = $product->customfieldsSorted[$position][0]; ?>
            <div class="product-fields-title-wrapper">
            <span class="product-fields-title">
                <strong>
                    <?php echo vmText::_($field->custom_title) ?>
                </strong>
            </span>
                <?php if ($field->custom_tip) {
                    echo JHtml::tooltip(vmText::_($field->custom_tip), vmText::_($field->custom_title), 'tooltip.png');
                } ?>
            </div>
            <?php
        }
        $custom_title = null;
        if ("related_products" == $position) {
            //**************сопутствующие товары******************************
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__modules');
            $query->where('`module` = \'mod_virtuemart_product_slider\'');
            $db->setQuery($query);

            $results = $db->loadObjectList();
        if ($results) {
            $params = null;
            foreach ($results as $result) {
                if ($result->position == "Releated") {
                    $params = new JRegistry($result->params);
                }
            }
        if ($params) {
            $module = JModuleHelper::getModule('mod_virtuemart_product_slider');
            $params = new JRegistry($module->params);
            $max_items = $params->get('max_items', 2); //maximum number of items to display
            $layout = $params->get('layout', 'default');
            $category_id = $params->get('virtuemart_category_id', null); // Display products from this category only
            $filter_category = (bool)$params->get('filter_category', 0); // Filter the category
            $display_style = $params->get('display_style', "div"); // Display Style
            $products_per_row = $params->get('products_per_row', 1); // Display X products per Row
            $show_price = (bool)$params->get('show_price', 1); // Display the Product Price?
            $show_addtocart = (bool)$params->get('show_addtocart', 1); // Display the "Add-to-Cart" Link?
            $headerText = $params->get('headerText', ''); // Display a Header Text
            $footerText = $params->get('footerText', ''); // Display a footerText
            $Product_group = $params->get('product_group', 'featured'); // Display a footerText
            $large = $params->get('large', ''); // широкоформатные компьютеры
            $medium = $params->get('medium', ''); // настольные компьютеры
            $small = $params->get('small', ''); // планшеты
            $extrasmall = $params->get('extrasmall', ''); // смартфоны
            $nav_dots = $params->get('nav_dots', 1); // Dots
            $nav_nav = $params->get('nav_nav', 0); // кнопки навигации
            $loop = $params->get('loop', '1'); // зацикливание
            $autoplay = $params->get('autoplay', '0'); // автозапуск
            $autoplayTimeout = $params->get('autoplayTimeout', ''); // скорость смены слайдов
            $twoImage = $params->get('twoImage', '0'); // показ дополнительного изображения
            $productMargin = $params->get('productMargin', ''); // отступ между товарами
            $colorSlider = $params->get('colorSlider', '');// основной цвет
            $shadow = $params->get('shadow', 0); // тени
            $heightIMG = $params->get('heightIMG', ''); // высота блока с изображением
            $customfield = $params->get('customfield', '0'); // Custom Field
            $cartStyle = $params->get('cartStyle', '1'); // кнопка Купить
            $moduleclass_sfx = 'ReleatedWrap';
            $style = '.vmslider{margin:0 -' . round($productMargin / 2) . 'px}.vmslider-product-wrap{padding: 5px ' . round($productMargin / 2) . 'px}';
            if ($shadow == '0') {
                $style .= '.vmslider-wrap' . $moduleclass_sfx . ' .vmslider-product:hover{border-color:' . $colorSlider . '}';
            }
            $style .= '.vmslider-wrap' . $moduleclass_sfx . ' .vmslider-product .vmslider-image{height:' . $heightIMG . 'px;}';
            if ($twoImage == '1') {
                $style .= '.vmslider-wrap' . $moduleclass_sfx . ' .vmslider-product:hover .vmslider-image .image1{opacity:1}';
            }
            $style .= '.vmslider-wrap' . $moduleclass_sfx . ' .vmslider-product .vmslider-name:hover a{color:' . $colorSlider . ';}';
            if ($cartStyle == '1') {
                $style .= '.vmslider-wrap' . $moduleclass_sfx . ' .vmslider-product .vmslider-cart.sliderCart span.addtocart-button > input,.vmslider-wrap' . $moduleclass_sfx . ' .vmslider-product .vmslider-cart.sliderCart span.addtocart-button >input:hover{background:' . $colorSlider . ';}';
            }
            if ($nav_dots == '1') {
                $style .= '.vmslider-wrap' . $moduleclass_sfx . ' .vmslider .slick-dots li button:before{background:' . $colorSlider . ';}';
            }
            ?>
            <link href="/modules/mod_virtuemart_product_slider/assets/product-slider.css" rel="stylesheet">
            <script src="/modules/mod_virtuemart_product_slider/assets/product-slider.js"></script>
            <style type="text/css">
                <?php
                   echo $style;
                 ?>
            </style>
            <div class=" vmslider-wrapReleatedWrap">
                <div class="vmslider <?php if ($nav_nav == '1') echo 'nav-top';
                if ($nav_dots == '1' && $nav_nav == '0') echo 'nav-dots'; ?>">
                    <?php
                    foreach ($product->customfieldsSorted[$position] as $field) {
                        ?>
                        <div class="vmslider-product-wrap">
                            <div class="vmslider-product <?php if ($shadow == '1') echo 'shadow' ?>">
                                <?php echo $field->display; ?>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <script>
                jQuery('.vmslider-wrapReleatedWrap .vmslider').slick({
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
         <?php
        }
        }


        }
        else{
          foreach ($product->customfieldsSorted[$position] as $field) {
        if ($field->is_hidden || empty($field->display)) // *!* 2016-02-08
            continue;
        ?>
            <div class="product-field product-field-type-<?php echo $field->field_type ?>">
                <?php if (!$customTitle and $field->custom_title != $custom_title and $field->show_title) { ?>
                    <span class="product-fields-title-wrapper">
                        <span class="product-fields-title">
                            <strong><?php echo vmText::_($field->custom_title) ?></strong>
                        </span>
                        <?php if ($field->custom_tip) {
                            echo JHtml::tooltip(vmText::_($field->custom_tip), vmText::_($field->custom_title), 'tooltip.png');
                        } ?>
                    </span>
                <?php }
                if (!empty($field->display)) {
                    ?>
                    <div class="product-field-display">
                        <?php
                        /*
                         * если тип поля редактор
                         * пропускаем его через плагины контента
                         */
                        if ($field->field_type == "X") {
                            $text = JHtml::_('content.prepare', $field->display);
                            echo $text;
                        } else {
                            echo $field->display;
                        }
                        ?>
                    </div>
                    <?php
                }
                if (!empty($field->custom_desc)) {
                    ?>
                    <div class="product-field-desc">
                        <?php echo vmText::_($field->custom_desc) ?>
                    </div>
                    <?php
                }
                ?>
            </div>
            <?php
            $custom_title = $field->custom_title;
        }
        }
        ?>
        <div class="clear"></div>
    </div>
    <?php
} ?>