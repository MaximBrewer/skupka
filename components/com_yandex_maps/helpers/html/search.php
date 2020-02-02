<?php
JHtml::addIncludePath(JPATH_ROOT.'/components/com_yandex_maps/helpers');
jhtml::_('xdwork.autocomplete');
JFactory::getDocument()->addScriptDeclaration(';(function($){
	$(function(){
		$("#xdsoft_search_on_map_input'.$map->id.'")
			.on(\'selected.xdsoft\',function(e,datum){
				map'.$map->id.'.setCenter(this.value);
			})
			.on(\'keydown.xdsoft\',function(e){
				if (e.keyCode==13) {
					map'.$map->id.'.setCenter(this.value);
				}
			})
	})
}(window.XDjQuery || window.jQ || window.jQuery));');
return 
<<<HTML
<input type="text" data-mapid="{$map->id}" class="xdsoft_search_on_map_input xdsoft_search_input" id="xdsoft_search_on_map_input{$map->id}" placeholder="Поиск по карте"/>
HTML
;