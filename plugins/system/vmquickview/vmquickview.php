<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.redirect
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

class plgSystemVmquickview extends JPlugin
{
    public function __construct(& $subject, $config) {
        parent::__construct($subject, $config);
        $this->loadLanguage();
    }
    public function onBeforeRender() {
        if (JFactory::getApplication()->isAdmin()) {
            return;
        }
        $doc = JFactory::getDocument(); 

        $type = $this->params->get('type',1); //тип шаблона
        $class = $this->params->get('class'); //класс родителя
        $xs = $this->params->get('xs',1); //показ на смартфонах
        $modalWidth = $this->params->get('modalWidth'); //ширина модульного окна
        $modalColor = $this->params->get('modalColor'); //цвет шапки модального окна
        $maximg = $this->params->get('maxImg'); //максимальная высота изображения        
        
        $fullscreen = $this->params->get('fullscreen',1); //кнопка полного просмотра
        $fullscreen = ($fullscreen) ? 'true' : 'false';
        $field = $this->params->get('field',1); //настраиваемые поля
        $css = $this->params->get('css'); //css файлы для подключения
        $js = $this->params->get('js'); //js файлы для подключения
        $addjs = $this->params->get('addjs'); //дополнительный js

        // подключение скриптов и стилей
        $doc->addStyleSheet(JURI::base().'plugins/system/vmquickview/assets/css/vmquickview.css');
        $doc->addScript(JURI::base().'plugins/system/vmquickview/assets/js/vmquickview.js');
        
        $style = $class.'{position: relative}';
        if($xs) $style .= '@media(max-width:768px){.vmquickview-button{display: none !important}}';
        if($maximg) $style .= '.vmqv-image #vmqv-slider,.vmqv-image #vmqv-slider img{max-height: '.$maximg.'px}';
        if(!$field) $style .= '.vmqv-wrap .product-cart .vm-customfields-wrap{display: none;}';
        $doc->addStyleDeclaration($style);
        
        if($type == 1){
            $js = "jQuery(document).ready(function($){
                    $('.vmquickview-button').click(function(e){
                        var productId = 'productId='+$(this).data('product-id');
                        $('#vmquickview').iziModal({
                            title: $(this).data('name'),
                            fullscreen: ".$fullscreen.",
                            headerColor: '".$modalColor."',
                            closeButton : true,
                            iframeHeight: 600,
                            width: ".$modalWidth.",
                            padding: 30,
                            onOpening: function(modal){
                                modal.startLoading();
                                $.get('/?option=com_ajax&plugin=vmquickview&group=system&format=debug', productId, function(data) {
                                    $('#vmquickview .iziModal-content').html(data);   
                                    modal.stopLoading();        
                                });
                            },
                            transitionIn: 'fadeInLeft',
                            onClosed: function(){
                                $('#vmquickview').iziModal('destroy');
                                jQuery('#vmCartModule').updateVirtueMartCartModule();
                                
                            }
                        });
                    });
                });";
        } else{
            $js = "jQuery(document).ready(function($){
                    $('.vmquickview-button').click(function(e){
                        $('#vmquickview').iziModal({
                            title: '&nbsp;',
                            fullscreen: ".$fullscreen.",
                            headerColor: '".$modalColor."',
                            closeButton : true,
                            iframe: true,
                            width: ".$modalWidth.",
                            iframeHeight: 600,
                            iframeURL: $(this).data('url')+'?tmpl=component',
                            transitionIn: 'fadeInLeft',
                            onClosed: function(){
                                $('#vmquickview').iziModal('destroy');
                                jQuery('#vmCartModule').updateVirtueMartCartModule();
                                
                            }
                            
                        });
                    });
                });	";
        }
        $doc->addScriptDeclaration($js);        
	}
    public function onAjaxVmquickview() {
        $doc = JFactory::getDocument(); 
        
        $type = $this->params->get('type',1); //тип шаблона
        $img = $this->params->get('img',1); //режим показа изображения
        $price = $this->params->get('price',1); //цена
        $sku = $this->params->get('sku',1); //артикул
        $mf = $this->params->get('mf',1); //производитель
        $stock = $this->params->get('stock',1); //наличие
        $cart = $this->params->get('cart',1); //кнопка Купить
        $field = $this->params->get('field',1); //настраиваемые поля
        $desc = $this->params->get('desc',1); //полное описание
        $sdesc = $this->params->get('sdesc',1); //краткое описание
        $css = $this->params->get('css'); //css файлы для подключения
        $js = $this->params->get('js'); //js файлы для подключения
        $addjs = $this->params->get('addjs'); //дополнительный js
        
        $productId = htmlspecialchars($_GET['productId']); // id товара
        
        if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
        VmConfig::loadConfig();
        VmConfig::loadJLang('com_virtuemart', true);
        
        vmJsApi::jPrice();
        vmJsApi::cssSite();
        
        $productModel = VmModel::getModel('Product');
        $product = $productModel->getProduct($productId);

        $productModel->addImages($product);
        
        if(!class_exists('shopFunctionsF'))require(VMPATH_SITE.DS.'helpers'.DS.'shopfunctionsf.php');

        $customfieldsModel = VmModel::getModel ('Customfields');
        
        if ($product->customfields){
            if (!class_exists ('vmCustomPlugin')) {
                require(VMPATH_PLUGINLIBS . DS . 'vmcustomplugin.php');
            }
            $customfieldsModel -> displayProductCustomfieldFE ($product, $product->customfields);
        }

        $isCustomVariant = false;
        if (!empty($product->customfields)) {
            foreach ($product->customfields as $k => $custom) {
                if($custom->field_type == 'C' and $custom->virtuemart_product_id != $virtuemart_product_id){
                    $isCustomVariant = $custom;
                }
                if (!empty($custom->layout_pos)) {
                    $product->customfieldsSorted[$custom->layout_pos][] = $custom;
                } else {
                    $product->customfieldsSorted['normal'][] = $custom;
                }
                unset($product->customfields);
            }
        }
        
        if (!class_exists('CurrencyDisplay'))
        require(VMPATH_ADMIN . DS . 'helpers' . DS . 'currencydisplay.php');
        $currency = CurrencyDisplay::getInstance( );

        echo vmJsApi::writeJS();

        if($type == 1 && !empty($css)){ // подключение дополнительных css
            $css = explode("\r\n", $css);
            foreach($css as $link){
                echo '<link href="'.$link.'" rel="stylesheet" type="text/css" />';
            }
        } 
        if($type == 1 && !empty($js)){ // подключение дополнительных js
            $js = explode("\r\n", $js);
            foreach($js as $link){
                echo '<script src="'.$link.'" type="text/javascript"></script>';
            }
        }
        
        require_once 'tmpl/default.php'; // подключение шаблона быстрого просмотра товара

        if($type == 1 && !empty($addjs)){ // подключение дополнительного js кода
            echo '<script>'.$addjs.'</script>';
        }
        
    }
}
