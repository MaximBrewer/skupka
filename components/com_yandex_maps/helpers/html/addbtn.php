<?php
$root = jURI::root();
jhtml::_('xdwork.registration');
return 
<<<HTML
<div class="xdsoft_btn_add_box">
<button type="button" onclick="map{$map->id}.addNewObject(this)" class="btn"><img src="{$root}media/com_yandex_maps/images/add.svg" alt="">Добавить объект</button>
</div>
HTML
;