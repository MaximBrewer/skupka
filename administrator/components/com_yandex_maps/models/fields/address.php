<?php
defined('JPATH_BASE') or die;
jimport('joomla.form.helper');
class JFormFieldAddress extends JFormField{
	public function attr($attr_name, $default = null){
		if (isset($this->element[$attr_name])) {
			return $this->element[$attr_name];
		} else {
			return $default;
		}
	}
	function getInput() {
		JHtml::addIncludePath(JPATH_ROOT.'/components/com_yandex_maps/helpers');
		jhtml::_('xdwork.autocomplete');
		jhtml::_('xdwork.yapi');
        if (!$this->attr('composite', 0)) {            
            $value = (object)(json_decode($this->value)?:array('address'=>'','lat'=>'','lan'=>'', 'zoom'=> ''));
        } else {
            $value = array('address'=>'','lat'=>'','lan'=>'', 'zoom'=> '');
        }
		
		JFactory::getDocument()->addScriptDeclaration(';function init'.$this->id.'map() {
			var map, $ = window.XDjQuery || window.jQ || window.jQuery || window.$, location;
			'.(((int)$this->attr('autocomplete', 1) && (int)$this->attr('autoinit', 1)) ? '
            $(function(){
				$(".'.$this->id.'_search")
					.off(\'selected.xdsoft keydown.xdsoft\')
					.on(\'selected.xdsoft keydown.xdsoft\',function(e, datum){
						var data = JSON.parse($(".'.$this->id.'").val());
						if ((e.keyCode === 13 || e.type!==\'keydown\') && datum) {
							data.address = datum.GeoObject.metaDataProperty.GeocoderMetaData.text;
							data.lat = datum.GeoObject.Point.pos.split(\' \')[1];
							data.lan = datum.GeoObject.Point.pos.split(\' \')[0];
							if (map) {
								map.setCenter([parseFloat(data.lat), parseFloat(data.lan)]);
							}
						}
						$(".'.$this->id.'").val(JSON.stringify(data));
					});
			}); ' : '' ).'
            var getValue = function () {
                    '.(!$this->attr('composite', 0) ? '
                    return JSON.parse($(".'.$this->id.'").val() || \'{}\');
                    ' : '
                    return {
                        lat: $("jform_'.$this->attr('lat_field', 'lat').'").val(),
                        lan: $("jform_'.$this->attr('lan_field', 'lan').'").val(),
                        zoom: $("jform_'.$this->attr('zoom_field', 'zoom').'").val(),
                    };
                    ').'
                },
                setValue = function (data) {
                     '.(!$this->attr('composite', 0) ? '
                    $(".'.$this->id.'").val(JSON.stringify(data));
                    ' : '
                    $("jform_'.$this->attr('lat_field', 'lat').'").val(data.lat);
                    $("jform_'.$this->attr('lan_field', 'lan').'").val(data.lan);
                    $("jform_'.$this->attr('zoom_field', 'zoom').'").val(data.zoom);
                    ').'
                };
            location = getValue();
			ymaps.ready(function(){
				map = new ymaps.Map("map'.$this->id.'", {
					center: [parseFloat(location.lat || '.($value->lat?:0).') || 55.76, parseFloat(location.lan || '.($value->lan?:0).') || 37.64],
					controls: ["zoomControl", "searchControl"],
					zoom: parseInt(location.zoom || '.(@$value->zoom?:0).', 10) || 7
				});
				map.events.add([\'boundschange\'], function () {
					var center = map.getCenter(), zoom = map.getZoom();
					var data = JSON.parse($(".'.$this->id.'").val());
					data.lat = center[0];
					data.lan = center[1];
					data.zoom = zoom;
					setValue(data);
				});
            });
        };');

        if ((int)$this->attr('autoinit', 1)) {
            JFactory::getDocument()->addScriptDeclaration(';(function(){
                init'.$this->id.'map();
            }());');
        }

		ob_start();
		?>
		<div style="display:inline-block;">
			<input type="hidden" name="<?php echo $this->name;?>" class="<?php echo $this->id;?>" value="<?php echo htmlspecialchars(json_encode($value));?>"/>
			<?php if((int)$this->attr('autocomplete', 1)) { ?>
			<div class="input-append" style="overflow:visible;">
				<input type="text" style="" class="span12 xdsoft_search_input <?php echo $this->id;?>_search"  value="<?php echo htmlspecialchars($value->address);?>"/>
				<button onclick="jQuery('.<?php echo $this->id;?>_search,.<?php echo $this->id?>').val('')" class="btn" type="button">
					<span aria-hidden="true" class="icon-cancel"></span>
				</button>
			</div>
			<?php } ?>
			<div style="position:relative;">
				<div id="map<?php echo $this->id;?>" style="margin-top:10px;border:1px solid #ccc;width:<?php echo $this->attr('width', 300)?>px;height:<?php echo $this->attr('height', 300)?>px;position:relative;">
					<div class="xdsoft_cursor_center"></div>
				</div>
			</div>
		</div>
		<style>
		.xdsoft_cursor_center,
		.xdsoft_cursor_center:after,
		.xdsoft_cursor_center:before{
			border:1px solid #000;
		}
		.xdsoft_cursor_center{
			z-index:100;
			position:absolute;
			width:1px;
			height:30px;
			border-width:0px 1px 0px 0px;
			left:50%;
			top:50%;
			margin-top:-15px;
		}
		.xdsoft_cursor_center:after{
			content:"";
			position:absolute;
			top:50%;
			left:0;
			width:15px;
			border-width:1px 0px 0px 0px;
		}
		.xdsoft_cursor_center:before{
			content:"";
			position:absolute;
			top:50%;
			right:0;
			width:15px;
			border-width:1px 0px 0px 0px;
		}
		.xdsoft_autocomplete .xdsoft_autocomplete_dropdown > div{
			font-size:14px;
		}
		</style>
		<?php
		return ob_get_clean();
	}
}
