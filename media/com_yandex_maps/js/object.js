/*global window,jQuery*/
var win1;
(function ($) {
	"use strict";
	var map, sizerBox, SizerBox, ymaps;
	function getMapOptions(id) {
		var option = $('#jform_' + id + '_id option:selected'),
			opt = {
				center: [parseFloat(option.data('lat')), parseFloat(option.data('lan'))],
				zoom: option.data('zoom') ? parseInt(option.data('zoom'), 10) : 10,
				type: option.data('type') || $('#jform_map_id option:selected').data('type') || 'yandex#map'
			};
		return opt;
	}

	function onChangeAttachEvents() {
		var timer;

		map.events.add(['boundschange', 'typechange', 'balloonclose'], function () {
			if (!$('#jform_lat').is(':focus')) {
				$('#jform_lat').val(map.getCenter()[0]);
			}
			if (!$('#jform_lan').is(':focus')) {
				$('#jform_lan').val(map.getCenter()[1]);
			}
			if (!$('#jform_zoom').is(':focus')) {
				$('#jform_zoom').val(map.getZoom());
			}
		});

		map.events.add('click', function (e) {
			var geo = ymaps.geocode(e.get('coords'));
			geo.then(
				function (res) {
					var geodata = res.geoObjects.get(0);
					$('#status_stroke').html('<a href="javascript:void(0);" onclick="jQuery(\'#jform_title\').val(this.innerText)">' + geodata.properties.get('name') + '</a>');
				},
				function (err) {}
			);
		});

		$('#jform_category_id')
			.on('change.xdsoft', function () {
				var options = getMapOptions('category');
				map.setZoom(options.zoom);
				map.setType(options.type);
				map.setCenter(options.center);
			});

		if (!$('[name="jform[id]"]').val()) {
			$('#jform_category_id').trigger('change.xdsoft');
		}

		$('#jform_type').on('change', function () {
			$('.control-group.placemark,.control-group.polygon,.control-group.polyline,.control-group.circle').hide();
			$('.control-group.' + $(this).val()).show();
		}).trigger('change');
	}

	window.createMap = function (nmap, nymaps, win) {
		ymaps = nymaps;
		map  = nmap;
		win1 = win;
		var options = getMapOptions('category'), init = true, coordinates, properties;

		map.setCenter([parseFloat($('#jform_lat').val()) || options.center[0] || 55.745501257, parseFloat($('#jform_lan').val()) || options.center[1] || 37.680073435]);
		map.setZoom($('#jform_zoom').val() || options.zoom || 10);
		map.setType(options.type);
		$('input[type=range]').rangeslider({
			polyfill: false,
			onSlide: function (position, value) {
				if (!init) {
					this.$element.val(value).trigger('changex');
				}
			}
		});

		$('.color').colpick({
			layout: 'hex',
			submit: 0,
			onChange: function (hsb, hex, rgb, el, bySetColor) {
				$(el).css('background', '#' + hex);
				if (!init) {
					$(el).val(hex).trigger('changex');
				}
			}
		}).on('change update', function () {
			init = true;
			$(this).colpickSetColor(this.value);
			init = false;
		}).trigger('update');

		init = false;

		$("#preset").chosenImage({
			disable_search_threshold: 10,
			disable_search: true
		});

		onChangeAttachEvents();

		if ($('#jform_coordinates').val()) {
			coordinates = JSON.parse($('#jform_coordinates').val());
			properties = $('#jform_properties').val() ? JSON.parse($('#jform_properties').val()) : {};
			options = $('#jform_options').val() ? JSON.parse($('#jform_options').val()) : {};

			if (coordinates) {
				win.addObject(map, $('#jform_type').val(), coordinates, options, properties, true);
			}
		}
	};

	$(function () {
		var idocument,
			mapbox,
			mapparent = $('#map'),
			iframe = $('<iframe  allowTransparency="true" scrolling="no" marginheight="0" marginwidth="0" frameborder="0" style="height:' + mapparent.height() + 'px"></iframe>');

		mapbox = $('<div style="height:' + mapparent.height() + 'px"></div>');

		mapparent.append(iframe);

		idocument = iframe[0].contentDocument;
		idocument.open();
		idocument.write("<!DOCTYPE html>");
		idocument.write('<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru-ru" lang="ru-ru" dir="ltr">');
		idocument.write("<head>" +
			'<meta name="viewport" content="width=device-width, initial-scale=1.0">' +
			'<meta http-equiv="X-UA-Compatible" content="IE=edge" />' +
			'<meta http-equiv="content-type" content="text/html; charset=utf-8" />' +
			"<base href=\"" + window.juri_root + "\">" +
			'<link rel="stylesheet" href="' + window.juri_root + 'media/com_yandex_maps/css/iframe.css" type="text/css" />' +
			'<script src="//api-maps.yandex.ru/2.1/?lang=' + mapparent.data('lang') + (window.YANDEX_MAPS_API_KEY ? '&apikey=' + encodeURIComponent(window.YANDEX_MAPS_API_KEY) : '') + '" type="text/javascript"></script>' +
			'<script src="' + window.juri_root + 'media/com_yandex_maps/js/xdsoft_sizerbox.js" type="text/javascript"></script>' +
			'<script src="' + window.juri_root + 'media/com_yandex_maps/js/xdsoft_sizerbox.js" type="text/javascript"></script>' +
			'<script src="' + window.juri_root + 'media/com_yandex_maps/js/iframe.js" type="text/javascript"></script>' +
			"</head>");
		idocument.write("<body><div id=\"map_object\" style=\"height:" + mapparent.height() + "px\"></div></body>");
		idocument.write('<script type="text/javascript">ymaps.ready(function() {' +
			'map = new ymaps.Map(document.getElementById("map_object"), {' +
				'center: [55,53],' +
				'zoom: 10' +
			'});' +
			'attachEventsToMap(map);' +
			'window.parent.createMap(map,ymaps,this)' +
			'});</script>');
		idocument.write("</html>");
		idocument.close();
	});
}(jQuery));

