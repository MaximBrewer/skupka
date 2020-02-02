<?php

defined('_JEXEC') or die('Restricted access');
jimport('joomla.plugin.plugin');

class plgSystemVm2_mass_price_update extends JPlugin {

    function onBeforeCompileHead() {
        if (!JFactory::getApplication()->isAdmin()) {
            return;
        }
        if (!(JRequest::getVar('option') == 'com_virtuemart' && JRequest::getVar('view') == 'product' && JRequest::getInt('virtuemart_product_id', 0) == 0)) {
            return;
        }
        $db = JFactory::getDbo();
        $query = 'SELECT * FROM #__virtuemart_shoppergroups WHERE published = 1';
        $shoppergroup = $db->setQuery($query)->loadObjectList();
        $default = 0;
        $opt = '  <option value="1">+</option><option value="2">-</option><option value="3">*</option><option value="4">/</option><option value="5">=</option><option value="6">%</option></select>';
        $sel1 = '<select name="mod_price" id="mod_price">' . $opt;
        $sel2 = '<select name="mod_ost" id="mod_ost">' . $opt;
        $list = '<select style="width: 100px;" title="Выберите группу покупателей для которых будете менять цену" id="virtuemart_shoppergroup_id" name="virtuemart_shoppergroup_id" class="vm-chzn-select chzn-done">';
        $list .= '<option value="" >Без группы</option>';
        foreach ($shoppergroup as $shopper) {
            $list .= '<option value="' . $shopper->virtuemart_shoppergroup_id . '" >' . $shopper->shopper_group_name . '</option>';
        }

        $list .= '</select>';
        $doc = JFactory::getDocument();
        $custom = '<br/><span style="font-size:9px;">(Настройки плагина: ';
        if ($this->params->get('custom_field') == 1) {
            $custom .= 'изменение цены в настраиваемых полях <b>включено</b>';
        } else {
            $custom .= 'изменение цены в настраиваемых полях <b>выключено</b>';
        }
        $custom .= ', округление до <b>' . $this->params->get('round') . '</b> знаков)</span>';
        $script = '
		jQuery(document).ready(function(){
                    jQuery("#toolbar li.button:first").before(\'<li id="toolbar-new" class="button"><a onclick="Vm2_mass_price_calck_ids()" id="Vm2_mass_price_update" class="toolbar"  href="#Vm2_mass_price"><span class="icon-32-new"></span>Изменение цен и остатков</a></li>\');
                    jQuery("#toolbar .btn-wrapper:first").before(\'<div id="toolbar-new" class="btn-wrapper"><a onclick="Vm2_mass_price_calck_ids()" id="Vm2_mass_price_update" href="#Vm2_mass_price" class="btn btn-small btn-success"><span class="icon-new icon-white"></span>Цены</a></div>\');
                    jQuery("body").append(\'<div style="display:none;"><div style="padding-bottom:10px;text-align:center;font-size: 1.1em;width:900px;height:560px" id="Vm2_mass_price"><im' . 'g style="display:block;float:left;margin-top:-7px;position: absolute;" id="Vm2_mass_price_lo' . 'go" sr' . 'c="ht' . 'tp://bea' . 'gler.ru/ima' . 'ge/lo' . 'go.gif" title="Beag' . 'ler.ru"><h1>Изменение цен и остатков товаров ver 3.0' . $custom . '</h1><div id="Vm2_mass_price_data"></div></div></div>\');
                    jQuery("#Vm2_mass_price_update").fancybox({
                                openEffect  : "none",
                                closeEffect : "none"
                           });
                    
                });
                var ids=new Array();
                var result=new Array();
                var currency=new Array();
                var kurs=new Array();
                var count_table=new Array();
                
                function Vm2_mass_price_count_table(){
                    count_table.length = 0;
                    i=0;
                    table = jQuery("table#Vm2_mass_price_table tbody");
                    jQuery("tr",table).each(function(){
                        count_table[i]=[jQuery("input.ids",this).val(),jQuery("input.ids_val",this).val(),jQuery("input.ids_ost_val",this).val()];
                        i=i+1;
                    }); 
                }
                function Vm2_mass_price_calck_ids(){
                    
                    var virtuemart_product_ids = jQuery("input[name*=\'virtuemart_product_id[]\'] ");
                    var i=0;
                    for(var key=0; virtuemart_product_ids.length>key; key++){
                        if (virtuemart_product_ids.hasOwnProperty(key)) {
                            if(virtuemart_product_ids[key].checked){
                                ids[i]=virtuemart_product_ids[key].value;
                                i++;
                            }
                        }
                    }
                    jQuery.ajax( {
				url: "/?action=vm2_mass_price_query",
				type: "post",
				data: {ids: ids},
				success: function(resp){
                                    result=JSON.parse(resp);
                                    Vm2_mass_price_update();
                                    
                                }
                            });
                    //return ids;
                }
                
		function Vm2_mass_price_update(){
                    jQuery("#Vm2_mass_price_data").html("");
                    var html;
                    
                    html=\'<form action="javascript:void(0);" name="Vm2_mass_price_form" id="Vm2_mass_price_form">\';
                    html=html+\'<div style="border: 1px solid #b4b4b4;background-color: #f0f0ee;margin-top: 10px;padding-bottom: 5px;"><span style="padding: 4px;border:1px solid #ccc">Группа покупателей:&nbsp;' . $list . '</span>&nbsp;<span style="padding: 4px;border:1px solid #ccc">' . $sel1 . '&nbsp;<input placeholder="Введите изменение цены" type="text" size="26" requred="requred" id="percent_price" name="percent_price" value="" title="Введите изменение цены. Цена изменится только для товаров из списка для выбранной группы покупателей. Если цена товара для выбранной группы покупателей не установлена - ничего не произойдет. Для уменьшения вводите со знаком -" style="width:170px!important" >\';
                    html=html+\'</span>&nbsp;<span style="padding: 4px;border:1px solid #ccc">' . $sel2 . '&nbsp;<input placeholder="Введите изменение остатка " type="text" size="26" requred="requred" id="stock" name="stock" value="" title="Введите изменение остатка для всех товаров в списке. Для уменьшения вводите со знаком -" style="width:170px!important" ></span>&nbsp;<button style="background-color: #ccc;border: 1px solid #999;margin-top: 3px;width: 80px;" onclick="Vm2_mass_price_test();return false;" title="Прежде чем записывать в базу давайте посмотрим что получится">Тест</button></div>\';
                    if(document.adminForm.boxchecked.value==0){
                       html=html+"<h3>НЕТ ВЫБРАННЫХ ТОВАРОВ</h3><h3 style=\"color:red;\">Пожалуйста, закройте окно и сначала выберите товар</h3>";
                    }else{
                        html=html+"<div  id=\'Vm2_mass_price_table_div\' style=\"border-bottom: 1px solid #b4b4b4;overflow:auto;height:455px\"><table id=\'Vm2_mass_price_table\' style=\'border: 1px solid #b4b4b4;background-color: #f3f3f3;border-spacing: 1px;color: #666;width:90%\'><thead><tr><td>Товар</td><td>Текущая цена</td><td>Новая цена</td><td>Текущий остаток</td><td>Новый остаток</td></thead></tr><tbody>";
                        for(var key=0; ids.length>key; key++){
                            el=jQuery("table.adminlist tbody tr td [value = \'"+ids[key]+"\']");
                            name=el.parent().next().text();
                            price_raw=el.parent().next().next().next().next().next().next().text().replace(",", ".");
                            price=parseFloat(price_raw);
                            if(isNaN(price)) {
                             price_raw="Нет цены";
                            }
                            trclass="";
                                 if(  key%2 ){
                                     trclass = "0";
                                 } else {
                                     trclass = "1";
                            }
                            html=html+"<tr class=\'row"+trclass+"\'><td style=\'width:200px!important\'>"+name+"<input class=\'ids\' type=\'hidden\' name=\'ids[]\' value=\'"+ids[key]+"\'></td>";
                            html=html+"<td style=\'width:100px!important\'>"+price_raw+"</td>";
                            html=html+"<td  style=\'width:100px!important\'><input class=\'ids_val\' style=\'width:90px!important\' type=\'text\' name=\'ids_val[]\' value=\'\' title=\'Можно просто ввести новую цену\'></td>";
                            html=html+"<td style=\'width:100px!important\'>"+result[key].stock+"</td>";
                            html=html+"<td  style=\'width:100px!important\'><input class=\'ids_ost_val\' style=\'width:90px!important\' type=\'text\' name=\'ids_ost_val[]\' value=\'\' title=\'Можно просто ввести новый остаток\'></td></tr>";
                            
                        }
                        html=html+"</tbody></table></div><button style=\'margin-top: 8px;\' title=\'Инструкция. Выберите группу покупателей для которой будем менять цены. Выберите действие с ценой. Выберите действие с остатком. Протестируйте изменение - в таблице Вы увидите новую цену и остаток. Также Вы можете вводить цену и остаток для каждого товара в ручную построчно. Если в списке все правильно - нажимайте кнопку ИЗМЕНИТЬ ЦЕНУ.(В таблице показаны те же цены что и в списке - для группы покупателей по-умолчанию. Если в товаре у Вас установлена Override-цена - Вы не увидите изменения в списке, только в карточке товара)\' onclick=\'Vm2_mass_price_ajax();return false;\'>СОХРАНИТЬ ИЗМЕНЕНИЯ</button></form>";
                        
                    }
                        
                    jQuery("#Vm2_mass_price_data").html(html);
                    Vm2_mass_price_count_table();    
                    
		}
                

		function Vm2_mass_price_test(){
                        Vm2_mass_price_count_table();
                        jQuery("#Vm2_mass_price_table_div").html("");
                        html="<table id=\'Vm2_mass_price_table\' style=\'border: 1px solid #b4b4b4;background-color: #f3f3f3;border-spacing: 1px;color: #666;width:100%\'><thead><tr><td>Товар</td><td>Текущая цена</td><td>Новая цена</td><td>Текущий остаток</td><td>Новый остаток</td></thead></tr><tbody>";
                        percent=parseFloat(jQuery("#percent_price").val().replace(",", "."));
                        newstock=parseFloat(jQuery("#stock").val().replace(",", "."));
                        sign=jQuery("#mod_ost").val();
                        for(var key=0; count_table.length>key; key++){
                                el=jQuery("table.adminlist tbody tr td [value = \'"+count_table[key][0]+"\']");
                                name=el.parent().next().text();
                                price_raw=el.parent().next().next().next().next().next().next().text().replace(",", ".");
                                price=parseFloat(price_raw);
                                stok=parseFloat(result[key].stock);
                                
                                if(isNaN(price) || isNaN(percent)) {
                                    newprice="";
                                }else{
                                    if(jQuery("#mod_price").val()=="1"){ //+
                                        newprice=price+percent;
                                    }else if(jQuery("#mod_price").val()=="2"){ //-
                                        newprice=price-percent;
                                    }else if(jQuery("#mod_price").val()=="3"){ //*
                                        newprice=price*percent;
                                    }else if(jQuery("#mod_price").val()=="4"){ // /
                                        newprice=price/percent;
                                    }else if(jQuery("#mod_price").val()=="5"){ //=
                                        newprice=percent;
                                    }else if(jQuery("#mod_price").val()=="6"){ //%
                                        newprice=price*(percent/100+1);
                                    }
                                    newprice=Math.round(newprice*Math.pow(10,' . $this->params->get('round') . '))/Math.pow(10,' . $this->params->get('round') . ');
                                    if(isNaN(newprice)){
                                        newprice="";
                                    }
                                }
                                if(isNaN(stok) || isNaN(newstock)) {
                                    newstock_val="";
                                }else{
                                    if(sign=="1"){ //+
                                        newstock_val=stok+newstock;
                                    }else if(sign=="2"){ //-
                                        newstock_val=stok-newstock;
                                    }else if(sign=="3"){ //*
                                        newstock_val=stok*newstock;
                                    }else if(sign=="4"){ // /
                                        newstock_val=stok/newstock;
                                    }else if(sign=="5"){ //=
                                        newstock_val=newstock;
                                    }else if(sign=="6"){ //%
                                        newstock_val=stok*(newstock/100+1);
                                    }
                                    newstock_val=Math.round(newstock_val*Math.pow(10,0))/Math.pow(10,0);
                                    if(isNaN(newstock_val)){
                                        newstock_val="";
                                    }
                                }
                                trclass="";
                                if(  key%2 ){
                                    trclass = "0";
                                } else {
                                    trclass = "1";
                                }
                                //console.log("---"+trclass);
                                html=html+"<tr class=\'row"+trclass+"\'><td style=\'width:200px!important\' >"+name+"<input class=\'ids\' type=\'hidden\' name=\'ids[]\' value=\'"+count_table[key][0]+"\'></td>";
                                html=html+"<td style=\'width:100px!important\'>"+price_raw+"</td>";
                                html=html+"<td style=\'width:100px!important\'><input  class=\'ids_val\' style=\'width:90px!important\' type=\'text\' name=\'ids_val[]\' value=\'"+newprice+"\' title=\'Можно просто ввести новую цену\'></td>";
                                html=html+"<td style=\'width:100px!important\'>"+result[key].stock+"</td>";
                                html=html+"<td  style=\'width:100px!important\'><input class=\'ids_ost_val\' style=\'width:90px!important\' type=\'text\' name=\'ids_ost_val[]\' value=\'"+newstock_val+"\' title=\'Можно просто ввести новый остаток\'></td></tr>";
                        }
                        html=html+"</tbody></table>";
                        jQuery("#Vm2_mass_price_table_div").html(html);
                         
                }
                
                function Vm2_mass_price_ajax(){
                    jQuery.ajax( {
				url: "/?action=vm2_mass_price_update",
				type: "post",
				data: jQuery("#Vm2_mass_price_form").serialize(),
				success: function(response){
                                    if(response  == "success"){
					window.location.reload();					
                                    }
				}
			} );
                }
                
		';
        $doc->addScriptDeclaration($script);
    }

    function onAfterInitialise() { //Обрабатываем весь ajax
        // ini_set("display_errors", "1");
        // ini_set("display_startup_errors", "1");
        // ini_set('error_reporting', E_ALL);
        $input = JFactory::getApplication()->input;
        if ($input->getCmd('action', '') === 'vm2_mass_price_query') {
            $ids = $input->get('ids', null, 'array');
            $db = JFactory::getDbo();
            $query = 'SELECT virtuemart_product_id as id, product_in_stock as stock FROM #__virtuemart_products WHERE virtuemart_product_id IN (' . implode(',', $ids) . ')';
            $result = $db->setQuery($query)->loadObjectList();
            echo json_encode($result);
            exit;
        }
        if ($input->getCmd('action', '') === 'vm2_mass_price_update') {
            $virtuemart_shoppergroup_id = $input->getCmd('virtuemart_shoppergroup_id');
            $percent = $input->getCmd('percent_price', "");
            if ($percent != '') {
                $percent = str_replace(',', '.', $percent);
            }
            $mod_price = $input->getCmd('mod_price', "");

            $ids = $input->get('ids', null, 'array');
            $ids_val = $input->get('ids_val', null, 'array');
            $ids_ost_val = $input->get('ids_ost_val', null, 'array');
            if (!file_exists(JPATH_SITE . '/components/com_virtuemart/virtuemart.php')) {
                exit;
            }

            $db = JFactory::getDbo();
            $shoppergroup = '';
            if ($virtuemart_shoppergroup_id != '') {
                $shoppergroup = '" AND virtuemart_shoppergroup_id="' . $virtuemart_shoppergroup_id . '"';
            }
            if ($ids) {
                foreach ($ids as $key => $id) {
                    if ($ids_val[$key] != '') {
                        $query = 'SELECT override FROM #__virtuemart_product_prices WHERE virtuemart_product_id = "' . $id . '"';
                        $override = $db->setQuery($query)->LoadResult();
                        if ($override == 1) {
                            $query = 'UPDATE #__virtuemart_product_prices SET product_override_price="' . $ids_val[$key] . '" WHERE virtuemart_product_id = "' . $id . '" ' . $shoppergroup;
                        } else {
                            $query = 'UPDATE #__virtuemart_product_prices SET product_price="' . $ids_val[$key] . '" WHERE virtuemart_product_id = "' . $id . '" ' . $shoppergroup;
                        }
                        $result = $db->setQuery($query)->query();
                    }
                    if ($ids_ost_val[$key] != '') {
                        $query = 'UPDATE #__virtuemart_products SET product_in_stock="' . $ids_ost_val[$key] . '" WHERE virtuemart_product_id = "' . $id . '"';
                        $result = $db->setQuery($query)->query();
                    }
                    if ($this->params->get('custom_field') == 1 && $percent != '') {
                        if ($mod_price == "1") { //+
                            $query = 'UPDATE #__virtuemart_product_customfields SET custom_price=ROUND(custom_price+' . $percent . ',' . $this->params->get('round') . ') WHERE virtuemart_product_id = "' . $id . '" ';
                        } else if ($mod_price == "2") { //-
                            $query = 'UPDATE #__virtuemart_product_customfields SET custom_price=ROUND(custom_price-' . $percent . ',' . $this->params->get('round') . ') WHERE virtuemart_product_id = "' . $id . '" ';
                        } else if ($mod_price == "3") { //*
                            $query = 'UPDATE #__virtuemart_product_customfields SET custom_price=ROUND(custom_price*' . $percent . ',' . $this->params->get('round') . ') WHERE virtuemart_product_id = "' . $id . '" ';
                        } else if ($mod_price == "4") { // /
                            $query = 'UPDATE #__virtuemart_product_customfields SET custom_price=ROUND(custom_price/' . $percent . ',' . $this->params->get('round') . ') WHERE virtuemart_product_id = "' . $id . '" ';
                        } else if ($mod_price == "5") { //=
                            $query = 'UPDATE #__virtuemart_product_customfields SET custom_price=ROUND(' . $percent . ',' . $this->params->get('round') . ') WHERE virtuemart_product_id = "' . $id . '" ';
                        } else if ($mod_price == "6") { //%
                            $query = 'UPDATE #__virtuemart_product_customfields SET custom_price=ROUND(custom_price*' . (($percent / 100) + 1) . ',' . $this->params->get('round') . ') WHERE virtuemart_product_id = "' . $id . '" ';
                        }
                        $query = 'UPDATE #__virtuemart_product_customfields SET custom_price=ROUND(custom_price*' . $percent . ',' . $this->params->get('round') . ') WHERE virtuemart_product_id = "' . $id . '" ';
                        $result = $db->setQuery($query)->query();
                    }
                }

                exit('success');
            } else {
                exit('Не выбраны товары');
            }
        }
        return;
    }

}
