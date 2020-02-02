<?php 
/**
* @version: 2.2.0 (2013.12.03)
* @author: Vahrushev Konstantin
* @copyright: Copyright (C) 2012 crono.ru
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
* http://crono.ru
**/
defined('_JEXEC') or die('Restricted access'); ?>
<script type="">
jQuery(document).ready(function(){
    CRG.CatGridInit();
    
});
function CRG_FrameEditor_getvalue(){
    selected = jQuery("#grid-category-select :checked:enabled");
    ids = new Array();
    names = new Array();
    for(i=0; i<selected.length;i++){
        row = selected.eq(i).parents('tr');
        ids[i]=row[0].id;
        names[i]=row.eq(0).find('td').eq(2).text();
    }
    return jQuery('#mode').attr('value')+':'+ids.join(',');
}
function CRG_changemode(){
    jQuery("#grid-category-select :checked:enabled").attr('checked', false);
}
</script>
<div>
Режим: 
<select id="mode" onchange="CRG_changemode();">
    <option value="replace">Удалить существующие, установить выбранные</option>
    <option value="add">Добавить в выбранные</option>
    <option value="delete">Удалить из выюранных</option>
</select>
<div style="padding: 10;">
    <table id="grid-category-select"></table>
</div>
</div>
