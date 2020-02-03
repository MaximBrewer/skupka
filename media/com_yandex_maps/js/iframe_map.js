/*global window,ymaps, map, sizerBox, jQuery, document, SqueezeBox,setTimeout,Image*/
var currentObject;
function validateGeometry(geometry) {
	"use strict";
	var coords = geometry, r;
	if (jQuery.type(geometry) === 'string') {
		try {
			coords = JSON.parse(geometry);
		} catch (e) {
			return false;
		}
	}
	if (jQuery.type(coords) === 'array') {
		if (coords.length) {
			for (r in coords) {
				if (coords.hasOwnProperty(r)) {
					if (jQuery.type(coords[r]) === 'array') {
						if (!validateGeometry(coords[r])) {
							return false;
						}
					} else {
						if (isNaN(coords[r]) || coords[r] === null) {
							return false;
						}
					}
				}
			}
			return true;
		}
	}
	return true;
}

function serializeObject(object, string) {
	"use strict";
	if (!object) {
		return;
	}

	var object_type = object.properties.get('metaType') ? object.properties.get('metaType').toLowerCase() : 'polygon',
		geometry = {},
		optes = {properties: {}, options: {}},
		result,
		name,
		key;

	geometry = object_type !== 'circle' ? object.geometry.getCoordinates() : [object.geometry.getCoordinates(), object.geometry.getRadius()];

	if (geometry.length === 0 || !validateGeometry(geometry)) {
		return {};
	}

	result = [
		object.xdsoftData,
		geometry,
		jQuery.extend(true, {}, object.options.getAll()),
		jQuery.extend(true, {}, object.properties.getAll())
	];
	if (result[2].draggable !== undefined) {
		delete result[2].draggable;
	}
	return string ? JSON.stringify(result) : result;
}
function deleteObject(object) {
	"use strict";
	if (object) {
		if (!object.xdsoftData.id) {
			object.xdsoftMirror.remove();
		} else {
			object.xdsoftData.deleted = 1;
			object.xdsoftMirror.val(serializeObject(object, true));
		}
		map.geoObjects.remove(object);
		window.balloon.close();
		sizerBox.hide();
	}
}
function validateColor(color) {
	"use strict";
	if (!color) {
		return '';
	}

	if (/(black|silver|gray|whitesmoke|maroon|red|purple|fuchsia|green|lime|olivedrab|yellow|navy|blue|teal|aquamarine|orange|aliceblue|antiquewhite|aqua|azure|beige|bisque|blanchedalmond|blueviolet|brown|burlywood|cadetblue|chartreuse|chocolate|coral|cornflowerblue|cornsilk|crimson|darkblue|darkcyan|darkgoldenrod|darkgray|darkgreen|darkgrey|darkkhaki|darkmagenta|darkolivegreen|darkorange|darkorchid|darkred|darksalmon|darkseagreen|darkslateblue|darkslategray|darkslategrey|darkturquoise|darkviolet|deeppink|deepskyblue|dimgray|dimgrey|dodgerblue|firebrick|floralwhite|forestgreen|gainsboro|ghostwhite|goldenrod|gold|greenyellow|grey|honeydew|hotpink|indianred|indigo|ivory|khaki|lavenderblush|lavender|lawngreen|lemonchiffon|lightblue|lightcoral|lightcyan|lightgoldenrodyellow|lightgray|lightgreen|lightgrey|lightpink|lightsalmon|lightseagreen|lightskyblue|lightslategray|lightslategrey|lightsteelblue|lightyellow|limegreen|linen|mediumaquamarine|mediumblue|mediumorchid|mediumpurple|mediumseagreen|mediumslateblue|mediumspringgreen|mediumturquoise|mediumvioletred|midnightblue|mintcream|mistyrose|moccasin|navajowhite|oldlace|olive|orangered|orchid|palegoldenrod|palegreen|paleturquoise|palevioletred|papayawhip|peachpuff|peru|pink|plum|powderblue|rosybrown|royalblue|saddlebrown|salmon|sandybrown|seagreen|seashell|sienna|skyblue|slateblue|slategray|slategrey|snow|springgreen|steelblue|tan|thistle|tomato|turquoise|violet|wheat|white|yellowgreen|rebeccapurple)/i.test(color)) {
	    return color;
    }

	if (!/^#/.test(color)) {
		color = '#' + color;
	}

	if (!/^#([a-f0-9]{6}|[a-f0-9]{3})/i.test(color)) {
		color = '#ffffff';
	}

	return color;
}
function getImageSizes(url) {
	"use strict";
	var image = new Image();
	image.src = url;
	return [image.width, image.height];
}
function updateObject(object) {
	"use strict";
	if (!object) {
		return;
	}

	var
        object_type = object.properties.get('metaType') ?
            object.properties.get('metaType').toLowerCase() : 'polygon',
        icon,
        preset;

	object.properties.set('iconContent', jQuery('#iconContent').val());

	if (!object.xdsoftData.title) {
		object.xdsoftData.title = jQuery('#iconContent').val();
	}

	object.properties.set('balloonContent', jQuery('#balloonContent').val());
	object.xdsoftData.category_id = jQuery('#category_id').val();

	switch (object_type) {
	case 'polygon':
	case 'circle':
	case 'polyline':
		object.options.set('fillOpacity', (parseInt(jQuery('#fillOpacity').val(), 10) / 10) || window.parent.defaultSettings.fillOpacity);
		object.options.set('fillColor', validateColor(jQuery('#fillColor').val()) || window.parent.defaultSettings.fillColor);
		object.options.set('strokeWidth', parseInt(jQuery('#strokeWidth').val(), 10) || window.parent.defaultSettings.strokeWidth);
		object.options.set('strokeOpacity', (parseInt(jQuery('#strokeOpacity').val(), 10) / 10) || window.parent.defaultSettings.strokeOpacity);
		object.options.set('strokeColor', validateColor(jQuery('#strokeColor').val()) || window.parent.defaultSettings.strokeColor);

        if (jQuery('#coordinates').is(':visible')) {
            var coordinate = JSON.parse(jQuery('#coordinates').val());
            if (object_type !== 'circle') {
                object.geometry.setCoordinates(coordinate);
            } else {
                object.geometry.setCoordinates(coordinate[0])
                object.geometry.setRadius(coordinate[1])
            }
        }

		break;

	case 'point':
	case 'placemark':
		if (jQuery('#coordinates').is(':visible')) {
            var coordinate = jQuery('#coordinates').val().split(',').map(parseFloat);
            object.geometry.setCoordinates(coordinate);
        }

		icon = jQuery('#iconpickericon').val();
        icon = icon.replace(window.parent.mapparent.data('base'), '');
        preset = object.options.get('preset');

        switch (icon) {
		case 'media/com_yandex_maps/images/placemark/islands-darkBlueDotIcon.png':
            if (!preset || /stretch/i.test(preset.toString())) {
                object.options.unset('iconImageHref');
                object.options.unset('iconLayout');
                object.options.set('preset', 'islands#icon');
            }

            object.options.set('iconColor',  validateColor(jQuery('#strokeColor').val()) || window.parent.defaultSettings.iconColor);
            break;
		case '':
		case 'media/com_yandex_maps/images/placemark/islands-blueStretchyIcon.png':
            if (!preset || !/stretch/i.test(preset.toString())) {
                object.options.unset('iconColor');
                object.options.unset('iconLayout');

                preset = window.parent.defaultSettings.preset;
                if (!/stretch/i.test(preset.toString())) {
                    preset = 'islands#blueStretchyIcon';
                }

                object.options.set('preset', preset);
            }
            break;
		default:
			object.options.unset('iconColor');
			object.options.set('iconLayout', window.parent.defaultSettings.iconLayout);
			object.options.set('iconImageHref', icon);
			object.options.set('iconImageSize', window.parent.defaultSettings.iconImageSize);
			object.options.set('iconImageOffset', [-Math.round(window.parent.defaultSettings.iconImageSize[0] / 2), -Math.round(window.parent.defaultSettings.iconImageSize[1] / 2)]);
		}

		break;
	}

	object.xdsoftMirror.val(serializeObject(object, true));
	window.balloon.close();
}

function hideIconPicker($, doc) {
	"use strict";
	$('.iconpickervariants', doc)
		.addClass('xdsoft_i_closed');
	setTimeout(function () {
		$('.iconpickervariants', doc)
			.removeClass('xdsoft_i_closed');
	}, 100);
}

function initIconPicker($, doc) {
	"use strict";
	$('#iconpickericon', doc).on('change', function () {
		if (!this.value) {
			$('#iconpickermirror', doc).attr('src', $('#iconpickermirror', doc).data('default'));
			return;
		}
		$('#iconpickermirror', doc).attr('src', this.value);
	});
	$('.iconpicker .iconpickersuggestions a', doc).on('click', function () {
		if ($(this).find('img').attr('src')) {
			$('#iconpickericon', doc).val($(this).find('img').attr('src')).trigger('change');
			$('.iconpickerfile', doc).hide();
		} else {
			$('#iconpickericon', doc).val('').trigger('change');
			$('.iconpickerfile', doc).show();
		}
		hideIconPicker($, doc);
		return false;
	});
	$('#iconImageHref').on('change', function () {
		$('#iconpickericon', doc).val(this.value).trigger('change');
		if (this.value) {
			$('.iconpickerfile', doc).hide();
		} else {
			$('#iconpickericon', doc).val('').trigger('change');
		}
	});
}
function initObjectForm() {
	"use strict";
	jQuery('input[type=range]', document).rangeslider({
		polyfill: false,
		onSlide: function (position, value) {
			//value
		}
	});
	var init = false, elm;
	jQuery('.color', document).colpick({
		layout: 'hex',
		submit: 0,
		onChange: function (hsb, hex, rgb, el, bySetColor) {
			jQuery(el).css('background', '#' + hex);
			if (!init) {
				jQuery(el).val(hex).trigger('changex');
			}
		}
	}).on('change update', function () {
		init = true;
		jQuery(this).css('background', '#' + this.value);
		jQuery(this).colpickSetColor(this.value);
		init = false;
	}).trigger('update');

	SqueezeBox.initialize({});

	elm  = jQuery('a.modal');

	SqueezeBox.assign(elm.attr('href', window.parent.mapparent.data('base') + 'administrator/' + elm.attr('href')).get(), {
		parse: 'rel'
	});

	setTimeout(function () {
		if (jQuery.fn.cleditor) {
			jQuery.cleditor.defaultOptions.docExternalHead = '<base href=\"' +  window.parent.mapparent.data('base') + '\">';
			var editor = jQuery('#balloonContent', document).cleditor({
				controls: "source | bold italic underline | font size " +
					"style | color highlight removeformat | bullets numbering | alignleft center alignright justify " +
					"rule image link unlink",
				height: 150
			});
			editor.change(function () {
				this.updateTextArea();
				this.$area.trigger('change');
			});
			jQuery('#balloonContent', document).on('update', function () {
				editor[0].updateFrame();
			});
		}
	}, 200);

	jQuery('#deleteObject').on('click', function () {
		deleteObject(currentObject);
		return false;
	});

	jQuery('#updateObject').on('click', function () {
		updateObject(currentObject);
		return false;
	});
}
function fillObjectForm(object) {
	"use strict";
	if (!object) {
		return;
	}
	var options = serializeObject(object),
		object_type = object.properties.get('metaType') ? object.properties.get('metaType').toLowerCase() : 'polygon';

	jQuery('#xdsoft_object_form_parent').addClass(object_type);
	jQuery('#iconContent').val(options[3].iconContent);
	jQuery('#balloonContent').val(options[3].balloonContent).trigger('update');
	jQuery('#category_id').val(object.xdsoftData.category_id);
	switch (object_type) {
	case 'polygon':
	case 'polyline':
	case 'circle':
		jQuery('#strokeWidth').val(options[2].strokeWidth).trigger('change');
		jQuery('#strokeOpacity').val(Math.round(options[2].strokeOpacity * 10)).trigger('change');
		jQuery('#fillOpacity').val(Math.round(options[2].fillOpacity * 10)).trigger('change');
		jQuery('#strokeColor').val(options[2].strokeColor).trigger('change');
		jQuery('#fillColor').val(options[2].fillColor).trigger('change');
        jQuery('#coordinates').val(JSON.stringify(options[1], null, 2));
		break;
	case 'point':
	case 'placemark':
		if (!options[2].iconImageHref) {
			setTimeout(function () {
				if (/Stretchy/i.test(options[2].preset)) {
					jQuery('#stretchy').click();
				} else {
					jQuery('#point').click();
				}
			}, 200);
		} else {
			jQuery('#iconpickericon').val(options[2].iconImageHref).trigger('change');
		}

		if (options[2].iconColor) {
			jQuery('#strokeColor').val(options[2].iconColor).trigger('change');
		}

		jQuery('#coordinates').val(options[1].toString());
		break;
	}
}
function createBalloon() {
	"use strict";
	window.balloon = new ymaps.Balloon(map, '', {
		maxWidth: 600,
        minHeight: 750
	});

	window.balloon.options.setParent(map.options);
	var form = window.parent.jQuery('#dialogObjectCreate');
	form.find('select').show();
	form.find('.chzn-container').remove();
	window.balloon.setData({content: form.html()});
	window.balloon.options.set('minHeight', 450);
	form.remove();
	window.balloon.events.add('open', function () {
		initIconPicker(jQuery, document);
		fillObjectForm(currentObject);
		initObjectForm();
	});
}
function getFirstCoordinate(object) {
	"use strict";
	var coord = object.geometry.getCoordinates();
	while (jQuery.type(coord[0]) === 'array') {
		coord = coord[0];
	}
	return coord;
}
function addObject(coord, id, category_id, map, object_type, coordinates, noptions, nproperties, created, sourceObject) {
	"use strict";
	var object, mirror = window.parent.jQuery('<input type="hidden" name="jform[fastobjects][]"/>'),
		properties = jQuery.extend(true, {}, nproperties),
		options = jQuery.extend(true, {}, noptions);
	if (!coordinates || !coordinates.length) {
		return;
	}
	if (options) {
		options.draggable = true;
	}

	switch (object_type) {
	case 'polygon':
	case 'polyline':
		object = new ymaps[object_type === 'polygon' ? 'Polygon' : 'Polyline'](coordinates, properties, options);
		currentObject = object;
		map.geoObjects.add(object);
		object.editor[!created ? 'startDrawing' : 'startEditing']();
		object.events.add('editorstatechange', function () {
			window.parent.complicated = object.editor.state.get('drawing');
			if (!window.parent.complicated) {
				if (window.balloon.isOpen()) {
					window.balloon.close(true);
				}
				window.balloon.open(getFirstCoordinate(object));
			}
		});
		break;
	case 'placemark':
	case 'circle':
		if (object_type === 'placemark' && options.iconColor && !/^islands#/.test(options.preset)) {
			options.preset = 'islands#icon';
		}

		if (options.iconColor !== undefined && !options.iconColor) {
			delete options.iconColor;
		}

		object = new ymaps[object_type === 'circle' ? 'Circle' : 'Placemark'](coordinates, properties, options);
		currentObject = object;
		map.geoObjects.add(object);

		if (!created) {
			if (window.balloon.isOpen()) {
				window.balloon.close(true);
			}
			window.balloon.open(getFirstCoordinate(object));
		}

		break;
	}

	object.events.add('dblclick', function () {
		return false;
	});

	object.events.add('click', function (e) {
		if (!window.parent.complicated) {
			if (window.balloon) {
				if (window.balloon.isOpen()) {
					window.balloon.close(true);
				}
				window.balloon.open(getFirstCoordinate(object));
			}
			currentObject = object;
			sizerBox.init(object);
			e.stopPropagation();
		}
	});

	if (!created) {
		sizerBox.init(object);
	}
	object.events
		.add(['geometrychange', 'optionschange', 'propertieschange'], function () {
			var data = serializeObject(object);
			if (!data || !data[1] || data[1].length === undefined || data[1].length === 0 || !validateGeometry(data[1])) {
				deleteObject(object);
				return;
			}
			coord = getFirstCoordinate(object);
			object.xdsoftData.lat = coord[0];
			object.xdsoftData.lan = coord[1];
			object.xdsoftData.zoom =  map.getZoom();
			mirror.val(JSON.stringify(data));
		});

	object.xdsoftMirror = mirror;
	object.xdsoftData = {
		id: id,
		category_id: category_id || window.parent.defaultSettings.defaultCategoryId,
		title: jQuery.trim(object.title || String(object.properties.get('iconContent'))),
		type: object_type,
		zoom: map.getZoom(),
		lat: coord[0],
		lan: coord[1]
	};

	if (sourceObject !== undefined) {
		object.xdsoftData.title = jQuery.trim(sourceObject.title);
		if (!jQuery.trim(object.properties.get('iconContent')) && sourceObject.title) {
			if (window.parent.ymOptions.use_title_how_icon_content) {
				if (window.parent.ymOptions.use_title_how_icon_content === 1 || !object.properties.get('iconContent')) {
					object.properties.set('iconContent', object.xdsoftData.title);
				}
			}	
		}
		if (window.parent.ymOptions.use_description_how_balloon_content && sourceObject.description) {
			if (window.parent.ymOptions.use_description_how_balloon_content === 1 || !object.properties.get('balloonContent')) {
				object.properties.set('balloonContent', jQuery.trim(sourceObject.description));
			}
		}
	}

	mirror.val(serializeObject(object, true));

	window.parent.jQuery('#objectStore').append(mirror);

	return object;
}
function setCenter(value) {
	"use strict";
	ymaps.geocode(value, {results: 1}).then(function (res) {
		var firstGeoObject = res.geoObjects.get(0), coord;
		if (firstGeoObject) {
			coord = firstGeoObject.geometry.getCoordinates();
			map.setCenter(coord);
		}
	}, function (err) {});
}
function eachMix(haystack, callback) {
	"use strict";
	var i;
	if (jQuery.isArray(haystack) || jQuery.isPlainObject(haystack)) {
		for (i in haystack) {
			if (haystack.hasOwnProperty(i)) {
				callback.call(window, i, haystack[i]);
			}
		}
	}
}
var globalObjects = {};
function addObjectsToMap(objects) {
	"use strict";
	eachMix(objects, function (i) {
		if (!isNaN(objects[i].id) && !globalObjects[objects[i].id] && objects[i].coordinates) {
			globalObjects[objects[i].id] = true;
			addObject([objects[i].lat, objects[i].lan], objects[i].id, objects[i].category_id, map, objects[i].type, JSON.parse(objects[i].coordinates), JSON.parse(objects[i].options), JSON.parse(objects[i].properties), true, objects[i]);
		}
	});
}
ymaps.ready(function () {
	"use strict";
	ymaps.control.storage.add('addUserPoint', function () {
		var btn =  new ymaps.control.Button({
			data: {
				image: '/media/com_yandex_maps/images/add.svg',
				content: 'Добавить объект',
				title: 'Нажмите чтобы добавить новую точку'
			},
			options: {
				selectOnClick: false,
				maxWidth: [30, 100, 150]
			}
		});
		return btn;
	});
	ymaps.control.storage.add('categorySelector', function () {
		var control = new ymaps.control.ListBox({
				data: {
					content: 'Выбрать категорию'
				},
				items: [
					new ymaps.control.ListBoxItem('Москва')
				]
			});
		control.get(0).events.add('click', function () {
			map.setCenter([55.752736, 37.606815]);
		});
		return control;
	});
});