<?php
/**
 /*------------------------------------------------------------------------
 # VmZoomer 1.9
 # ------------------------------------------------------------------------
 # (C) 2015 Все права защищены.
 # Лицензия http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 # Автор: Vladimir Pronin
 # Сайт: http://virtuemart.su
 -------------------------------------------------------------------------*/

// No direct access

defined('_JEXEC') or die('Restricted access..');


jimport('joomla.plugin.plugin');

class plgSystemVmZoomer extends JPlugin {
	public function onBeforeRender() {
		$doc = JFactory::getDocument();
        $view = JRequest::getVar('view');
		$option = JRequest::getVar('option');
        
        // главное изображение
        $zoom = $this->params->get('zoom',1); //включить zoom
        $zoomEvent = $this->params->get('zoomEvent',1); // событие для zoom
        $zoomTouch = $this->params->get('zoomTouch',0); // zoom на смартфонах
        $zoomHeight = $this->params->get('zoomHeight'); // высота главного изображения
        $zoomNav = $this->params->get('zoomNav', 1); // стрелки навигации по изображениям
        $zoomAlt = $this->params->get('zoomAlt', 0); // название изображения
        $zoomAjax = $this->params->get('zoomAjax', 0); // поддержка ajax
        
        // дополнительные изображения
        $carousel = $this->params->get('carousel'); // вид карусели
        $addHeight = $this->params->get('addHeight'); // высота дополнительного изображения
        $change = $this->params->get('change', 1); // способ смены главного изображения
        $large = $this->params->get( 'large', ''); // широкоформатные компьютеры
        $medium = $this->params->get( 'medium', ''); // настольные компьютеры
        $small = $this->params->get( 'small', ''); // планшеты
        $extrasmall = $this->params->get( 'extrasmall', ''); // смартфоны
        
        // лайтбокс
        $lightboxTrumb = $this->params->get('lightboxTrumb', 1); // показать миниатюры
        $lightboxTrumbCarousel = $this->params->get('lightboxTrumbCarousel', 1); // положение миниатюр
        $lightboxNav = $this->params->get('lightboxNav', 1); // положение кнопок навигации
        $lightboxAlt = $this->params->get('lightboxAlt', 0); // название изображений в лайтбоксе
        $lightboxSmall = $this->params->get('lightboxSmall', 1); // отключить лайтбокс на смартфонах
        $lightboxLoop = $this->params->get('lightboxLoop', 1); // зациклить просмотр изображений
        
        if ($view == 'productdetails' && $option == 'com_virtuemart') {
            
            // подключение css и js        
            $doc->addStyleSheet(JURI::base().'plugins/system/vmzoomer/assets/css/vmzoomer.css');
            $doc->addScript(JURI::base().'plugins/system/vmzoomer/assets/js/vmzoomer.js');
            
            $js = $style = '';
            
            // настройки zoom
            if($zoom){
                if($zoomEvent == 1) $zoomEvent = "on: 'mouseover',";
                if($zoomEvent == 2) {
                    $zoomEvent = "on: 'click',";
                    $js .= "  jQuery('.vmzoomer-image').click(function(){ jQuery(this).addClass('no-show');});";
                    $js .= "  jQuery('body').click(function(){ jQuery('.vmzoomer-image').removeClass('no-show');});";
                    $style .= ".vmzoomer-image:hover a[rel='vm-additional-images'] img{ opacity: 1;}";
                }
                if($zoomEvent == 3) {
                    $zoomEvent = "on: 'grab',";
                    $js .= "  jQuery('.vmzoomer-image').mousedown(function(){ jQuery(this).addClass('no-show');});";
                    $js .= "  jQuery('body').mouseup(function(){ jQuery('.vmzoomer-image').removeClass('no-show');});";
                    $style .= ".vmzoomer-image:hover a[rel='vm-additional-images'] img{ opacity: 1;}";
                }    
                $zoomTouchScript = ($zoomTouch) ? 'touch: true' : 'touch: false';
                
                // стили если запрещен zoom на смартфонах
                if(!$zoomTouch){
                   $style .= "@media(max-width: 768px){.vmzoomer-image > img{opacity: 0 !important} .vmzoomer-image a img{opacity: 1 !important}}"; 
                }
                
                $js .= "jQuery('.vmzoomer-image').zoom({".$zoomEvent.$zoomTouchScript."});";  
            } else{
                // если zoom отключен, то показ главного изображения всегда
                $js .= "jQuery('.vmzoomer-image').addClass('no-zoom');";
            }
            // высота главного изображения
            $style .= ".vmzoomer-image{height: {$zoomHeight}px}";
            
            // стрелки навигации по изображениям
            if(!$zoomNav) $style .= ".vmzoomer-image-wrap .next-button,.vmzoomer-image-wrap .prev-button{display: none}";
            
            if($zoomNav && $change == 1){
                $js .= "var lastImgCar = jQuery('.vmzoomer-additional-images .item').last();       
                        var firstImgCar = jQuery('.vmzoomer-additional-images .item').first();
                        jQuery('.vmzoomer-image-wrap .next-button').click(function(){
                            var image = jQuery('.vmzoomer-additional-images .active');
                            if(image.get(0) == lastImgCar.get(0)){
                               firstImgCar.find('img').trigger('click');
                            }
                            image.parent().next().find('img').trigger('click');   
                        });
                        jQuery('.vmzoomer-image-wrap .prev-button').click(function(){
                            var prevImage = jQuery('.vmzoomer-additional-images .active');
                            if(prevImage.get(0) == firstImgCar.get(0)){
                               lastImgCar.find('img').trigger('click');
                            }
                            prevImage.parent().prev().find('img').trigger('click');
                        });";
            } else{
                $js .= "var lastImgCar = jQuery('.vmzoomer-additional-images .item').last();
                        var firstImgCar = jQuery('.vmzoomer-additional-images .item').first();
                        jQuery('.vmzoomer-image-wrap .next-button').click(function(){
                            var image = jQuery('.vmzoomer-additional-images .active');
                            if(image.get(0) == lastImgCar.get(0)){
                               firstImgCar.find('img').trigger('mouseover');
                            }
                            image.parent().next().find('img').trigger('mouseover');   
                        });
                        jQuery('.vmzoomer-image-wrap .prev-button').click(function(){
                            var prevImage = jQuery('.vmzoomer-additional-images .active');
                            if(prevImage.get(0) == firstImgCar.get(0)){
                               lastImgCar.find('img').trigger('mouseover');
                            }
                            prevImage.parent().prev().find('img').trigger('mouseover');
                        });";
            }
            
            // показать название главного изображения
            if($zoomAlt){
                $js .= "var alt = jQuery('.vmzoomer-image .product-zoom-image img').attr('alt');
                        var imgAlt = '<div class=\"vmzoomer-image-desc\">' + alt + '</div>';
                        jQuery('.vmzoomer-image-wrap').after(imgAlt);
                        jQuery('.vmzoomer-additional-images .item').click(function() {
                            var alt = jQuery(this).children('img').attr('alt');
                            jQuery('.vmzoomer-image-desc').html(alt);
                        });";
            }
            
            // высота дополнительного изображения
            if($carousel != 2){
                $style .= ".vmzoomer-additional-images .item{height: {$addHeight}px;}";
            }
                            
            // вертикальная карусель     
            if($carousel == 2){
                $js .= 'jQuery(".vmzoomer-wrap").addClass("vertical-carousel");';
                $height = floor(($zoomHeight+5)/$large); // высота дополнительного изображения
                $height2 = $zoomHeight+5; // высота блока с дополнительными изображениями
                $style .= ".vmzoomer-additional-images .additional-image-wrap {height: {$height}px;}.vmzoomer-additional-images.slick-vertical{height: {$height2}px;}";
            }          
            
            // карусель дополнительных изображений
            if($carousel != 3){ 
                $vertical = ($carousel == 1) ? 'false' : 'true';
                if($carousel == 2){
                   $medium = $small = $extrasmall = $large; // количество миниатюр при вертикальной карусели
                }
                
                $jsSlick = "jQuery('.vmzoomer-additional-images').slick({
                    dots: false,
                    arrows: true,
                    infinite: false,
                    slidesToShow: $large,
                    swipeToSlide: true,
                    swipe: true,
                    draggable: true,
                    slidesToScroll: 1,
                    speed: 200,
                    vertical: $vertical,
                    responsive: [
                        {
                          breakpoint: 1200,
                          settings: {
                            slidesToShow: $medium,
                          }
                        },
                        {
                          breakpoint: 992,
                          settings: {
                            swipeToSlide: true,
                            swipe: true,
                            slidesToShow: $small,
                          }
                        },
                        {
                          breakpoint: 768,
                          settings: {
                            swipe: true,
                            slidesToShow: $extrasmall,
                          }
                        }
                    ]
                });
                jQuery('.vmzoomer-additional-images').show();
                ";
            }
            
            // дополнительные изображения без карусели
            if($carousel == 3){
                $large = floor(100/$large);
                $medium = floor(100/$medium);
                $small = floor(100/$small);
                $extrasmall = floor(100/$extrasmall);
                
                $js .= 'jQuery(".vmzoomer-additional-images").addClass("noslider");';
                
                $style .= "@media(max-width:767px) {.vmzoomer-additional-images.noslider .additional-image-wrap {width: {$extrasmall}%;}} @media(min-width:768px) {.vmzoomer-additional-images.noslider .additional-image-wrap {width: {$small}%;}}@media(min-width:992px) {.vmzoomer-additional-images.noslider .additional-image-wrap {width: {$medium}%;}}@media (min-width:1200px) {.vmzoomer-additional-images.noslider .additional-image-wrap {width: {$large}%;}}";
                
                $js .= "jQuery('.vmzoomer-additional-images').show();";
            }
            
            // способ смены главного изображения
            if($change == 1){
                $js .= 'Virtuemart.updateImageEventListeners = function() {
                    jQuery(".vmzoomer-additional-images a.product-image.image-0").removeAttr("rel");
                    jQuery(".vmzoomer-additional-images .item").click(function() {
                        jQuery(".vmzoomer-additional-images a.product-image").attr("rel","vm-additional-images" );
                        jQuery(this).children("a.product-image").removeAttr("rel");
                        var src = jQuery(this).children("a.product-image").attr("href");
                        var alt = jQuery(this).children("img").attr("alt");
                        jQuery(".vmzoomer-image img").attr("src",src);
                        jQuery(".vmzoomer-image img").attr("alt",alt );
                        jQuery(".vmzoomer-image a").attr("href",src );
                        jQuery(".vmzoomer-image a").attr("alt",alt );
                        jQuery(".vmzoomer-image .vm-img-desc").html(alt);
                    }); 
                    jQuery(".vmzoomer-additional-images .item:first").addClass("active");

                    jQuery(".vmzoomer-additional-images .item").click(function() {
                         jQuery(".vmzoomer-additional-images .item").removeClass("active");         
                         jQuery(this).toggleClass("active");
                     });
                } 
                Virtuemart.updateImageEventListeners();';
            } else {
                $js .= 'Virtuemart.updateImageEventListeners = function() {
                    jQuery(".vmzoomer-additional-images a.product-image.image-0").removeAttr("rel");
                    jQuery(".vmzoomer-additional-images .item").hover(function() {
                        jQuery(".vmzoomer-additional-images a.product-image").attr("rel","vm-additional-images" );
                        jQuery(this).children("a.product-image").removeAttr("rel");
                        var src = jQuery(this).children("a.product-image").attr("href");
                        jQuery(".vmzoomer-image img").attr("src",src);
                        jQuery(".vmzoomer-image img").attr("alt",this.alt );
                        jQuery(".vmzoomer-image a").attr("href",src );
                        jQuery(".vmzoomer-image a").attr("title",this.alt );
                        jQuery(".vmzoomer-image .vm-img-desc").html(this.alt);
                    }); 
                    jQuery(".vmzoomer-additional-images .item:first").addClass("active");

                    jQuery(".vmzoomer-additional-images .item").hover(function() {
                         jQuery(".vmzoomer-additional-images .item").removeClass("active");         
                         jQuery(this).toggleClass("active");
                     });
                }
                Virtuemart.updateImageEventListeners();';
            }
            
            // лайтбок по кнопке с включенным zoom
            if($zoom){
            $js .= "jQuery('.lightbox-button').click(function() {
                        jQuery('.vmzoomer-additional-images .active a.fresco').trigger('click');
                    });";
            } else{
            
            // лайтбокс по изображению с отключенным zoom 
            $js .= "jQuery('.vmzoomer-image a.product-zoom-image').click(function(event){
                        event.preventDefault();
                        jQuery('.vmzoomer-additional-images .active a.fresco').trigger('click');
                    });";
            $style .= ".lightbox-button{display: none}";
            }
            
            // лайтбокс если нет дополнительных изображений
            $js .= 'if(!jQuery(".vmzoomer-additional-images").length){
                        jQuery(".vmzoomer-image a").addClass("fresco");
                        jQuery(".lightbox-button").click(function() {             
                           Fresco.show(jQuery(".vmzoomer-image a.fresco img").attr("src"));
                        });
                    };';
            
            // положение миниатюр
            if($lightboxTrumbCarousel == 2){
                $js .= "
                var firstImg = jQuery('.vmzoomer-additional-images a.product-image:first');
                var attrFirstImg = firstImg.attr('data-fresco-group-options');
                var newAttr = attrFirstImg  + ' thumbnails: \'vertical\',';
                firstImg.attr('data-fresco-group-options', newAttr);
                ";
            }
            
            // миниатюры в латбоксе
            if(!$lightboxTrumb){
                $js .= "
                var firstImg = jQuery('.vmzoomer-additional-images a.product-image:first');
                var attrFirstImg = firstImg.attr('data-fresco-group-options');
                var newAttr = attrFirstImg  + ' thumbnails: false,';
                firstImg.attr('data-fresco-group-options', newAttr);
                ";
            }
            
            // положение кнопок навигации
            if($lightboxNav == 2){
                $js .= "
                var firstImg = jQuery('.vmzoomer-additional-images a.product-image:first');
                var attrFirstImg = firstImg.attr('data-fresco-group-options');
                var newAttr = attrFirstImg  + ' ui: \'inside\',';
                firstImg.attr('data-fresco-group-options', newAttr);
                ";
            }
            
            // название изображений в лайтбоксе
            if($lightboxAlt){
                $js .= "jQuery('.vmzoomer-additional-images a.product-image').each(function(){
                        var alt = jQuery(this).attr('title');
                        jQuery(this).attr('data-fresco-caption', alt);
                        });";
            }
            
            // отключить лайтбокс на смартфонах
            if($lightboxSmall){
                $style .= "@media(max-width: 768px){.lightbox-button{display: none;}}";
            }
            
            // зациклить просмотр изображений в лайтбоксе
            if($lightboxLoop){
                $js .= "
                var firstImg = jQuery('.vmzoomer-additional-images a.product-image:first');
                var attrFirstImg = firstImg.attr('data-fresco-group-options');
                var newAttr = attrFirstImg  + ' loop: true,';
                firstImg.attr('data-fresco-group-options', newAttr);
                ";
            }
            
            //добавление css и js
            /*порядок $jsSlick $js важен, сначала должна сформироваться карусель из дополнительных изображений*/
            $doc->addStyleDeclaration($style);
            
            if(!$zoomAjax){
                if($carousel !== '3') {
                    $scriptSlick = "jQuery(document).ready(function($) { $jsSlick } );"; // карусель дополнительных изображений
                    $doc->addScriptDeclaration($scriptSlick);
                }
                $script = "jQuery(document).ready(function($) { $js } );"; // zoom и другие скрипты
                $doc->addScriptDeclaration($script);
            } else{ 
                $script = "
                function vmzoomer() { {$js} }
                function vmSlick() { {$jsSlick} }
                jQuery(document).ready(function(){
                    vmSlick();
                    vmzoomer();
                    jQuery(document).ajaxComplete(function() {        
                        vmSlick();
                        jQuery('.vmzoomer-additional-images').slick('reinit');
                        vmzoomer();
                    });   
                });";
                $doc->addScriptDeclaration($script); 
            }  
        }
	}
}
?>