(function($){
	map = null;
	jQuery(function(){
		ymaps.ready(function () {
			map = new ymaps.Map("map", {
				center: [parseFloat($('#jform_lat').val()) || 55.745501257 , parseFloat($('#jform_lan').val()) || 37.680073435],
				zoom: parseInt($('#jform_zoom').val()) || 10
			});
			map.setType('yandex#map');
			
			onChangeAttachEvents();
		});
	});
	function getMapOptions() {
		var option = $('#jform_map_id option:selected'),
			opt = {
				center: [parseFloat(option.data('lat')), parseFloat(option.data('lan'))], 
				zoom: option.data('zoom') ? parseInt(option.data('zoom')) : 10, 
				type: option.data('type')
			};
		return opt;
	}
	function onChangeAttachEvents() {
		$('#jform_map_id').on('change', function() {
			var options = getMapOptions();
			map.setZoom(options.zoom);
			map.setType(options.type);
			map.setCenter(options.center);
		});
		map.events.add(['boundschange', 'typechange', 'balloonclose'], function(){
			if (!$('#jform_lat').is(':focus')) {
				$('#jform_lat').val(map.getCenter()[0]);
			}
			if (!$('#jform_lan').is(':focus')) {
				$('#jform_lan').val(map.getCenter()[1]);
			}
			if (!$('#jform_zoom').is(':focus')) {
				$('#jform_zoom').val(map.getZoom());
			}
			$('#jform_type').val(map.getType()).trigger("liszt:updated");
		});
		map.events.add('click', function (e) {
			var geo = ymaps.geocode(e.get('coords'));
			geo.then(
				function (res) {
					 var geodata = res.geoObjects.get(0);
					 jQuery('#status_stroke').html('<a href="javascript:void(0);" onclick="jQuery(\'#jform_title\').val(this.innerText)">'+geodata.properties.get('name')+'</a>');
				},
				function (err) {}
			);
		});
		var timer;
		$('#jform_zoom,#jform_type,#jform_lat,#jform_lan').on('mousedown keydown change', function() {
			clearTimeout(timer);
			timer = setTimeout(function() {
				map.setZoom(parseInt($('#jform_zoom').val()))
				map.setType($('#jform_type').val())
				map.setCenter([parseFloat($('#jform_lat').val()), parseFloat($('#jform_lan').val())])
			});
		});
	}
}(jQuery));
