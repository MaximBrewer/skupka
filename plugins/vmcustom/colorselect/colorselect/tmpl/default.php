<?php
	defined('_JEXEC') or die();
$product = $viewData[0];
$params = $viewData[1];
$name = 'customProductData['.$product->virtuemart_product_id.']['.$params->virtuemart_custom_id.']['.$params->virtuemart_customfield_id .']';
$class='vmcustom-colorselect'.$params->virtuemart_customfield_id;
?>

<?php
$doc = JFactory::getDocument();
$view = JRequest::getVar('view');
// подключаем скрипты и стили
if(!empty($params->path_select)){
    $doc->addStyleSheet(JURI::base().'plugins/vmcustom/colorselect/colorselect/assets/colorselect.css');
    $doc->addScript(JURI::base().'plugins/vmcustom/colorselect/colorselect/assets/colorselect.js');
}
?>

<?php 
// получаем параметры из настроек
$textarea = $params->textarea;
$path_select = $params->path_select; // путь к папке с изображениями для select

// каждую строку помещаем в массив
if(strpos($textarea, '&#13;&#10;')){
   $textRows = explode("&#13;&#10;",$textarea); 
} else {
   $textRows = explode("\r\n",$textarea); 
}

?>

<?php $currency = CurrencyDisplay::getInstance(); 

?>
<select name="<?php echo $name; ?>" id="colorimage<?php echo $params->virtuemart_customfield_id; ?>" class="<?php echo $class; ?>">
    <?php
    // выводим пустой параметр
    if($params->empty_option){ ?>
        <option class="empty-option" value="<?php echo '0'; ?>"><?php echo JTEXT::_('VMCUSTOM_COLORSELECT_EMPTY_OPTION_SITE'); ?></option>
    <?php
    }
    ?>
    
    <?php foreach($textRows as $textRow): ?>
    <?php
    // получаем массив параметров
    $option = explode('}{',$textRow);

    //удаляем скобки у первого и последнего параметра
    $option[0] = str_replace('{', '', $option[0]);
    $option[3] = str_replace('}', '', $option[3]);

    //имя
    $optionName = $option[0];
    
    //цена
    if(isset($option[3]) && !empty($option[3])){
       $optionPrice = ' ('.$option[3].$currency->getSymbol().')'; 
    } else{
       $optionPrice = ''; 
    }
    
    //картинка цвета
    if(!empty($option[1]) && !empty($params->path_select)){
        $data_img = 'data-img-src="'.$path_select.$option[1].'"';
    } else {
        $data_img = '';
    }
    
    //картинка для замены
    if(!empty($option[2])){
        if(!empty($params->path_img)){
            $data_sub = 'data-img-sub="'.$params->path_img.$option[2].'"';
        } else{
            $data_sub = 'data-img-sub="/images/stories/virtuemart/product/'.$option[2].'"';
        }
    } else {
        $data_sub = '';
    }
    ?>
    <option value="<?php echo $optionName; ?>" <?php echo $data_img; ?> <?php echo $data_sub; ?>><?php echo $optionName.$optionPrice; ?></option>
    <?php endforeach; ?>
</select>

<?php if(!empty($params->path_select)){?>
<script>
   jQuery("<?php echo '.'.$class; ?>").chosen({
       disable_search_threshold: 10,
       width: "100%"
   });
 </script>
<?php } ?> 

<?php 
// классы основного и дополнительных изображений
$classImg = $params->class_img;
$classAddImg = $params->class_add_img;

// триггер для дополнительных изображений
$trigger = $params->trigger;

$js = $style = '';
?>

<?php if($view == 'productdetails'): ?>
   <?php if($trigger){ 
    $js .= "$('.{$class}').change(function(){  
        var imgSub = $('.{$class} option:selected').data('imgSub');
        if(typeof imgSub !== 'undefined'){
            $('{$classAddImg} a').each(function() {
                var imgUrl = $(this).attr('href').split('/'); // массив из url изображения
                var imgName = imgUrl[imgUrl.length - 1]; // имя изображения

                var imgSubUrl = $('.{$class} option:selected').data('imgSub').split('/'); // массив из url изображения для замены
                var imgSubName = imgSubUrl[imgSubUrl.length - 1]; // имя изображения для замены

                if(imgName == imgSubName){
                    $(this).siblings('img').trigger('click');
                }
            });
        }     
    });";
    } else { 
        $js .= "$('.{$class}').change(function(){  
            var imgSub = $('.{$class} option:selected').data('imgSub');
            if(typeof imgSub !== 'undefined'){
                $('{$classImg} img').attr('src', imgSub);
                $('.vmzoomer-image a').attr('href', imgSub);
            }     
        });";
    } ?>

    <?php 
    // при выборе пустого параметра выбирается главное изображение
    if($params->empty_option){
       $js .= "var mainImg = $('{$classImg} img').attr('src');
        $('.empty-option').attr('data-img-sub', mainImg);";
    } 
    ?>

    <?php
    if($params->hidden_add_img){
        $style .= "$classAddImg{display: none !important}";
    }
    ?>

    <?php 
    //добавление css и js
    $script = "jQuery(document).ready(function($) { $js });";
    $doc->addScriptDeclaration($script);
    $doc->addStyleDeclaration($style);
    ?>
<?php endif; ?>