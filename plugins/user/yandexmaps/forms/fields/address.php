<?php
defined('JPATH_BASE') or die;
jimport('joomla.form.helper');
class JFormFieldAddress extends JFormField{
	function getInput() {
		JHtml::addIncludePath(JPATH_ROOT.'/components/com_yandex_maps/helpers');
		jhtml::_('xdwork.autocomplete');
		jhtml::_('xdwork.yapi');
		$value = (object)(json_decode($this->value)?:array('address'=>'','lat'=>'','lan'=>'', 'zoom'=> ''));
		static $included = false;
		if (!$included) {
			$included = true;
			JFactory::getDocument()->addScriptDeclaration(';(function($){
				var map;
				$(function(){
					$("#'.$this->id.'_search")
						.on(\'selected.xdsoft keydown.xdsoft\',function(e, datum){
							var data, err;
							try {
								data = JSON.parse($("#'.$this->id.'").val());
							} catch(err) {
								data = {address:"", lat:"", lan: "", zoom: ""};
							}
							if ((e.keyCode===13 || e.type!==\'keydown\') && datum) {
								data.address = datum.GeoObject.metaDataProperty.GeocoderMetaData.text;
								data.lat = datum.GeoObject.Point.pos.split(\' \')[1];
								data.lan = datum.GeoObject.Point.pos.split(\' \')[0];
								if (map) {
									map.setCenter([parseFloat(data.lat), parseFloat(data.lan)]);
								}
							}
							$("#'.$this->id.'").val(JSON.stringify(data));
							if (e.keyCode===13) {
								return false;
							}
						})
					
				})
				ymaps.ready(function(){
					map = new ymaps.Map("map'.$this->id.'", {
						center: [parseFloat('.($value->lat?:0).') || 55.76, parseFloat('.($value->lan?:0).') || 37.64], 
						zoom: parseInt('.(@$value->zoom?:0).', 10) || 7
					});
					map.events.add([\'boundschange\'], function () {
						var center = map.getCenter(), zoom = map.getZoom();
						var data, e;
						try {
							data = JSON.parse($("#'.$this->id.'").val());
						} catch(e) {
							data = {address:"", lat:"", lan: "", zoom: ""};
						}
						data.lat = center[0];
						data.lan = center[1];
						data.zoom = zoom;
						$("#'.$this->id.'").val(JSON.stringify(data));
					});
				});
			}(window.XDjQuery || window.jQ || window.jQuery));');
		}
		

		return '<input type="hidden" name="'.$this->name.'" id="'.$this->id.'" value="'.htmlspecialchars(json_encode($value)).'"/>
			<div class="xdsoft_btn_group"><input type="text" style="" class="xdsoft_search_input" id="'.$this->id.'_search" value="'.htmlspecialchars($value->address).'"/><button onclick="jQuery(\'.xdsoft_search_input,#'.$this->id.'\').val(\'\')" class="btn" type="button"><span aria-hidden="true" class="icon-cancel"></span></button></div>
			<div class="xdsoft_mini_map" id="map'.$this->id.'"><div class="xdsoft_cursor_center"></div>
		';
	}
}
