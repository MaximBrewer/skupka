(function ($) {
	"use strict";
	window.map_id = 0;
	window.complicated = false;
	window.defaultSettings = {
		iconLayout: 'default#imageWithContent',
		iconColor: '#4d98fa',
		preset: 'islands#blueIcon',
		strokeWidth: 3,
		strokeColor: '#FE110F',
		strokeOpacity: 1,
		fillOpacity: 0.8,
		fillColor: '#FC6057',
		iconImageSize: [32, 32]
	};
	var map, ymaps, mode, win,
		cache = {},
		exlude = null,
		bounds = null,
		hash = null;

	function loadObjects(offset, limit, exclude, $forseid) {
		offset = offset || 0;
		limit = limit || 500;
		bounds = map.getBounds();
		hash = JSON.stringify(bounds) + offset + '-' + limit + JSON.stringify(exlude) + map.id;
		if (cache[hash] === undefined || $forseid !== undefined) {
			jQuery.post(window.mapparent.data('base') + 'administrator/index.php?option=com_yandex_maps&task=load', {
				forse_id: $forseid || 0,
				map_id: window.map_id,
				bound: bounds,
				offset: offset,
				limit: limit
			}, function (data) {
				cache[hash] = data;
				if (data.count > offset + data.objects.length) {
					loadObjects(offset + limit, limit, exclude);
				} else {
					exlude = map.getBounds();
				}

				win.addObjectsToMap(data.objects);
			}, 'json');
		}
	}
	function initOutputEvents() {
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
			$('#jform_type').val(map.getType()).trigger("liszt:updated");
			if (window.map_id &&  parseInt($('#loadobjects').val(), 10)) {
				loadObjects();
			}
		});
		map.events.add(['click'], function (e) {
			if (mode && e.get('domEvent').get('which') === 1 && !window.complicated) {
				var coord = e.get('coords'), proj, x, y;
				switch (mode) {
				case 'polygon':
					win.addObject(coord, 0, 0, map, mode, [[coord]], {
						strokeWidth:	window.defaultSettings.strokeWidth,
						strokeColor:	window.defaultSettings.strokeColor,
						strokeOpacity:  window.defaultSettings.strokeOpacity,
						fillOpacity:	window.defaultSettings.fillOpacity,
						fillColor:		window.defaultSettings.fillColor,
						draggable:		true,
						visible:		true
					}, {
						metaType: "Polygon",
						balloonContent: ''
					});
					window.complicated = true;
					break;
				case 'polyline':
					win.addObject(coord, 0, 0, map, mode, [coord], {
						strokeColor:	window.defaultSettings.strokeColor,
						strokeOpacity:	window.defaultSettings.strokeOpacity,
						strokeWidth:	window.defaultSettings.strokeWidth,
						draggable:		true
					}, {
						metaType: "Polyline",
						balloonContent: ''
					});
					window.complicated = true;
					break;
				case 'circle':
					proj = map.options.get('projection');
					x = proj.fromGlobalPixels([300, 0], map.getZoom());
					y = proj.fromGlobalPixels([0, 0], map.getZoom());
					win.addObject(coord, 0, 0, map, mode, [coord, ymaps.coordSystem.geo.getDistance(x, y)], {
						fillColor:		window.defaultSettings.fillColor,
						fillOpacity:	window.defaultSettings.fillOpacity,
						strokeColor:	window.defaultSettings.strokeColor,
						strokeOpacity:	window.defaultSettings.strokeOpacity,
						strokeWidth:	window.defaultSettings.strokeWidth,
						visible:		true,
						draggable:		true
					}, {
						balloonContent: '',
						hintContent: '',
						metaType: 'Circle'
					});
					break;
				default:
					win.addObject(coord, 0, 0, map, mode, coord, {
						preset: window.defaultSettings.preset,
						iconColor: window.defaultSettings.iconColor,
						draggable: true,
						visible: true
					}, {
						iconContent: '',
						balloonContent: 'Point',
						metaType: 'Point'
					});
					break;
				}
				e.preventDefault();
			}
		});
	}
	function initInputEvents() {
		var timerUpdateControls, timerUpdateBehaviors, timer;
		// input Controls
		timerUpdateControls = 0;
		$('input.jform_controls').on('updateValue', function () {
			clearTimeout(timerUpdateControls);
			timerUpdateControls = setTimeout(function () {
				$('input.jform_controls').each(function () {
					var control = $(this).data('name');
					if (parseInt(this.value, 10)) {
						if (!map.controls.get(control)) {
							map.controls.add(control);
						}
					} else {
						if (map.controls.get(control)) {
							map.controls.remove(control);
						}
					}
				});
			}, 500);
		}).eq(0).trigger('updateValue');
		// input Behaviors
		timerUpdateBehaviors = 0;
		$('input.jform_behaviors').on('updateValue', function () {
			clearTimeout(timerUpdateBehaviors);
			timerUpdateBehaviors = setTimeout(function () {
				$('input.jform_behaviors').each(function () {
					var name = $(this).data('name');
					if (parseInt(this.value, 10)) {
						if (name !== 'default') {
							if (!map.behaviors.isEnabled(name)) {
								map.behaviors.enable(name);
							}
						} else {
							map.behaviors.enable(["drag", "dblClickZoom", "rightMouseButtonMagnifier"]);
						}
					} else {
						if (map.behaviors.isEnabled(name)) {
							map.behaviors.disable(name);
						}
					}
				});
			}, 500);
		}).eq(0).trigger('updateValue');

		// input Map parameters
		$('#jform_zoom,#jform_type,#jform_lat,#jform_lan').on('mousedown keydown change', function () {
			clearTimeout(timer);
			timer = setTimeout(function () {
				map.setZoom(parseInt($('#jform_zoom').val() || 10, 10));
				map.setType($('#jform_type').val() || 'yandex#map');
				map.setCenter([parseFloat($('#jform_lat').val() || 55), parseFloat($('#jform_lan').val() || 53)]);
			});
		});

		// button mode
		$('#xdsoft_button_s>span').on('click', function () {
			if (!$(this).hasClass('active')) {
				$('#xdsoft_button_s>span').removeClass('active');
				$(this).addClass('active');
				mode = $(this).attr('id').replace('button_', '');
			} else {
				$(this).removeClass('active');
				mode = null;
			}
		});
		$('.xdsoft_button_find').on('click', function () {
			win.setCenter($('.xdsoft_map_search').val());
		});
	}
	window.createMap = function (nmap, nymaps, nwin) {
		win = nwin;
		ymaps = nymaps;
		map  = nmap;
		map.setCenter([parseFloat($('#jform_lat').val() || 55), parseFloat($('#jform_lan').val() || 53)]);
		map.setZoom(parseInt($('#jform_zoom').val() || 10, 10));
		map.setType($('#jform_type').val() || 'yandex#map');
		window.map_id = parseInt($('[name=id]').val() || 0, 10);
		initInputEvents();
		initOutputEvents();
		if (window.map_id && parseInt($('#loadobjects').val(), 10)) {
			loadObjects();
		}
	};
	$(function () {
		window.mapparent = $('#map');
		var idocument, iwindow,
			iframe = $('<iframe  allowTransparency="true" scrolling="no" marginheight="0" marginwidth="0" frameborder="0" style="height:' + window.mapparent.height() + 'px"></iframe>');

		window.mapbox = $('<div style="height:' + window.mapparent.height() + 'px"></div>');

		window.mapparent.append(iframe);

		idocument = iframe[0].contentDocument;
		iwindow = iframe[0].contentWindow;
		if (iwindow.jInsertFieldValue === undefined) {
			iwindow.jInsertFieldValue = function (value, id) {
				var $1 = iwindow.jQuery.noConflict(),
					$elem = $1("#" + id),
					old_value = $elem.val();
				if (old_value !== value) {
					$elem.val(value);
					$elem.trigger("change");
					if (typeof ($elem.get(0).onchange) === "function") {
						$elem.get(0).onchange();
					}
					if (iwindow.jMediaRefreshPreview !== undefined) {
						iwindow.jMediaRefreshPreview(id);
					}
				}
			};
		}
		if (iwindow.jMediaRefreshPreview === undefined) {
			iwindow.jMediaRefreshPreview = function (id) {
				var $1 = iwindow.jQuery.noConflict(),
					value = $1("#" + id).val(),
					$img = $1("#" + id + "_preview");
				if ($img.length) {
					if (value) {
						$img.attr("src", "' + mapparent.data('base') + '" + value);
						$1("#" + id + "_preview_empty").hide();
						$("#" + id + "_preview_img").show();
					} else {
						$img.attr("src", "");
						$1("#" + id + "_preview_empty").show();
						$1("#" + id + "_preview_img").hide();
					}
				}
			};
		}
		if (iwindow.jMediaRefreshPreviewTip === undefined) {
			iwindow.jMediaRefreshPreviewTip = function (tip) {
				var $1 = iwindow.jQuery.noConflict(),
					$tip = $1(tip),
					$img = $tip.find("img.media-preview"),
					id = $img.attr("id");

				$tip.find("div.tip").css("max-width", "none");
				id = id.substring(0, id.length - 8);
				iwindow.jMediaRefreshPreview(id);
				$tip.show();
			};
		}
		idocument.open();
		idocument.write("<!DOCTYPE html>");
		idocument.write('<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru-ru" lang="ru-ru" dir="ltr">');
		idocument.write("<head>" +
			'<meta name="viewport" content="width=device-width, initial-scale=1.0">' +
			'<meta http-equiv="X-UA-Compatible" content="IE=edge" />' +
			'<meta http-equiv="content-type" content="text/html; charset=utf-8" />' +
			"<base href=\"" + window.mapparent.data('base') + "\">" +
			'<link rel="stylesheet" href="' + window.mapparent.data('base') + 'media/com_yandex_maps/js/colpick/colpick.css" type="text/css" />' +
			'<link rel="stylesheet" href="' + window.mapparent.data('base') + 'media/com_yandex_maps/js/rangeslider.css" type="text/css" />' +
			'<link rel="stylesheet" href="' + window.mapparent.data('base') + 'media/com_yandex_maps/css/iframe_map.css" type="text/css" />' +
			'<link rel="stylesheet" href="' + window.mapparent.data('base') + 'media/system/css/modal.css" type="text/css" />' +
			'<link rel="stylesheet" href="' + window.mapparent.data('base') + 'media/com_yandex_maps/js/wysiwyg/jquery.cleditor.css" type="text/css" />' +
			'<link rel="stylesheet" href="' + window.mapparent.data('base') + 'administrator/templates/isis/css/template.css" type="text/css" />' +
			'<script src="//api-maps.yandex.ru/2.1/?lang=' + window.mapparent.data('lang') + (window.YANDEX_MAPS_API_KEY ? '&apikey=' + encodeURIComponent(window.YANDEX_MAPS_API_KEY) : '') + '" type="text/javascript"></script>' +
			'<script src="' + window.mapparent.data('base') + 'media/com_yandex_maps/js/jquery.min.js" type="text/javascript"></script>' +
			'<script src="' + window.mapparent.data('base') + 'media/system/js/mootools-core.js" type="text/javascript"></script>' +
			'<script src="' + window.mapparent.data('base') + 'media/system/js/core.js" type="text/javascript"></script>' +
			'<script src="' + window.mapparent.data('base') + 'media/system/js/mootools-more.js" type="text/javascript"></script>' +
			'<script src="' + window.mapparent.data('base') + 'media/system/js/modal.js" type="text/javascript"></script>' +
			'<script src="' + window.mapparent.data('base') + 'media/com_yandex_maps/js/rangeslider.min.js" type="text/javascript"></script>' +
			'<script src="' + window.mapparent.data('base') + 'media/com_yandex_maps/js/colpick/colpick.js" type="text/javascript"></script>' +
			'<script src="' + window.mapparent.data('base') + 'media/com_yandex_maps/js/wysiwyg/jquery.cleditor.js" type="text/javascript"></script>' +
			'<script src="' + window.mapparent.data('base') + 'media/com_yandex_maps/js/xdsoft_sizerbox.js" type="text/javascript"></script>' +
			'<script src="' + window.mapparent.data('base') + 'media/com_yandex_maps/js/iframe_map.js" type="text/javascript"></script>' +
			'<script src="' + window.mapparent.data('base') + 'media/media/js/mediafield.min.js" type="text/javascript"></script>' +
			'<script src="' + window.mapparent.data('base') + 'media/system/js/modal-fields.js" type="text/javascript"></script>' +
			'<script >window.jModalClose = function () {\n' +
            '\t\t\tSqueezeBox.close();\n' +
            '\t\t};</script>' +
			"</head>"
			);
		idocument.write("<body><div id=\"map_object\" style=\"height:" + window.mapparent.height() + "px\"></div></body>");
		idocument.write('<script type="text/javascript">ymaps.ready(function() {' +
			'map = new ymaps.Map(document.getElementById("map_object"), {' +
				'center: [55,53],' +
				'zoom: 10' +
			'});' +
			'sizerBox = new SizerBox(map, ymaps);' +
			'window.parent.createMap(map, ymaps, this);' +
			'createBalloon();' +
			'});</script>');/*
		idocument.write('<script type="text/javascript">' + "\n" +
				'function jInsertFieldValue(value, id) { ' + "\n" +
				'	var $ = jQuery.noConflict(); ' + "\n" +
				'	var old_value = $("#" + id).val(); ' + "\n" +
				'	if (old_value != value) { ' + "\n" +
				'		var $elem = $("#" + id); ' + "\n" +
				'		$elem.val(value); ' + "\n" +
				'		$elem.trigger("change"); ' + "\n" +
				'		if (typeof($elem.get(0).onchange) === "function") { ' + "\n" +
				'			$elem.get(0).onchange(); ' + "\n" +
				'		}' + "\n" +
				'		jMediaRefreshPreview(id); ' + "\n" +
				'	}' + "\n" +
				'} ' + "\n" +
				'function jMediaRefreshPreview(id) {' + "\n" +
				'	var $ = jQuery.noConflict(); ' + "\n" +
				'	var value = $("#" + id).val(); ' + "\n" +
				'	var $img = $("#" + id + "_preview"); ' + "\n" +
				'	if ($img.length) { ' +
				'		if (value) { ' +
				'			$img.attr("src", "' + mapparent.data('base') + '" + value); ' + "\n" +
				'			$("#" + id + "_preview_empty").hide(); ' + "\n" +
				'			$("#" + id + "_preview_img").show(); ' + "\n" +
				'		} else { ' + "\n" +
				'			$img.attr("src", "") ' + "\n" +
				'			$("#" + id + "_preview_empty").show(); ' + "\n" +
				'			$("#" + id + "_preview_img").hide(); ' + "\n" +
				'		} ' + "\n" +
				'	} ' + "\n" +
				'} ' + "\n" +
				'function jMediaRefreshPreviewTip(tip) { ' + "\n" +
				'	var $ = jQuery.noConflict(); ' + "\n" +
				'	var $tip = $(tip); ' + "\n" +
				'	var $img = $tip.find("img.media-preview"); ' + "\n" +
				'	$tip.find("div.tip").css("max-width", "none"); ' + "\n" +
				'	var id = $img.attr("id"); ' + "\n" +
				'	id = id.substring(0, id.length - "_preview".length); ' + "\n" +
				'	jMediaRefreshPreview(id); ' + "\n" +
				'	$tip.show(); ' + "\n" +
				'}' + "\n" +
			'</script>');*/
		idocument.write("</html>");
		idocument.close();
	});
}(jQuery));