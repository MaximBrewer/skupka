window.object = null;
var sizerBox;
$get = function (selector) {
	return parent.jQuery(selector);
}
getColor = function (val, defaultValue) {
	return 
}
attachEventsToMap = function (map) {
	sizerBox = new SizerBox(map, ymaps);
	map.events.add('dblclick',function(e){
		if( e.get('domEvent').get('which')===1 ){
			var object_type = $get('#jform_type').val(),
				cur = e.get('coords');

			switch( object_type ){
				case 'polygon':
					addObject(map, object_type,[[cur]],{
						strokeWidth: 	parseInt($get('#strokeWidth').val(), 10) || 3,
						strokeColor:	$get('#strokeColor').val() || "990066",
						strokeOpacity: 	parseInt($get('#strokeOpacity').val(), 10)/10 || 1,
						fillOpacity: 	parseInt($get('#fillOpacity').val(), 10)/10 || 1,
						fillColor: 		$get('#fillColor').val() || "DB7093",
						draggable: 		true,
						visible:		true
					},
					{metaType:"Polygon","balloonContent":$get('#balloonContent').val()});
				break;
				case 'polyline':
					addObject(map, object_type,[cur],{
						strokeColor:	$get('#strokeColor').val() || "990066",
						strokeOpacity: 	parseInt($get('#strokeOpacity').val(), 10)/10 || 1,
						strokeWidth: 	parseInt($get('#strokeWidth').val(), 10) || 3,
						draggable:		true
					},
					{metaType:"Polyline","balloonContent":""});
				break;
				case 'circle':
					var proj = map.options.get('projection');
					var x = proj.fromGlobalPixels ([300,0], map.getZoom()), y = proj.fromGlobalPixels ([0,0], map.getZoom());
					addObject(map, object_type,[cur,ymaps.coordSystem.geo.getDistance(x, y)],{
						fillColor: 		$get('#fillColor').val() || "DB7093",
						fillOpacity: 	parseInt($get('#fillOpacity').val(), 10)/10 || 1,
						strokeColor:	$get('#strokeColor').val() || "990066",
						strokeOpacity:	parseInt($get('#fillOpacity').val(), 10)/10 || 0.8,
						strokeWidth:	parseInt($get('#strokeWidth').val(), 10) || 3,
						visible:		true,
						draggable:		true
					},{
						"balloonContent":$get('#balloonContent').val(),
						"hintContent":"",
						"metaType":"Circle"
					});
				break;

				case 'placemark':
					var val, tmp_object = addObject(map, object_type, cur, {
						preset: $get('#preset').val(),
						draggable: true,
						visible:true
					},{
						iconContent: $get('#iconContent').val() || $get('#jform_title').val() || 'Точка ',
						balloonContent:$get('#balloonContent').val(),
						metaType:"Point"
					});
					
					if ($get('#iconImageHref').val()) {
						tmp_object.options.set('iconLayout', parseInt($get('#object_show_title_with_image').val() || 0, 10) ? 'default#imageWithContent' : 'default#image');
						tmp_object.options.set('iconImageHref', $get('#iconImageHref').val());
						tmp_object.options.set('iconImageSize', validateArray($get('#iconImageSize').val()));
						tmp_object.options.set('iconImageOffset', validateArray($get('#iconImageOffset').val()));
					}
					
				break;
			}
		}
		e.preventDefault();
	});

	$get('#option_and_properties').find('.properties,.options,#balloonContent').on('change changex blur keypress keydown', function () {
		if (!object || !this.getAttribute('id')) {
			return;
		}
		var mode = top.jQuery(this).hasClass('options')?'options':'properties',
			val = this.getAttribute('type')=='range'?this.value/10:top.jQuery(this).val();	
		switch (this.getAttribute('id')) {
			case 'visible':
				val = !!parseInt(val);
			break;
			case 'preset':
				if (val) {
					object.options.unset('iconLayout');
					object.options.unset('iconImageHref');
					object.options.unset('iconImageSize');
					object.options.unset('iconImageOffset');
					object.options.unset('iconColor');
				}
			break;
			case 'iconColor':
				val = validateColor(val);

				if (val && $get('#preset').val().match(/Stretchy|geolocation/)) {
					$get('#preset').val('islands#icon').trigger('change');
				}
			break;
			case 'iconImageOffset':
			case 'iconImageSize':
				val = validateArray(val);
			break;
			case 'iconImageHref':
				object[mode].set('iconLayout', parseInt($get('#object_show_title_with_image').val() || 0, 10) ? 'default#imageWithContent' : 'default#image');
				loadImage(val, function() {
					$get('#iconImageSize').val(this.width+','+this.height).trigger('update').trigger('change');
					$get('#iconImageOffset').val(-Math.round(this.width/2)+',-'+Math.round(this.height/2)).trigger('update').trigger('change');
				});
			break;
		}
		object[mode].set(this.getAttribute('id'), val);
	});
};

var _opt = {
		options:['strokeColor','strokeOpacity','strokeWidth','fillColor','fillOpacity','preset','iconColor','iconImageHref', 'iconImageSize', 'iconImageOffset','visible'],
		properties:['metaType','iconContent','balloonContent']
	};

initForm = function (object_type,object){
	var options = getObjectData(object),
		option_and_properties = top.jQuery.extend(true, {}, options[1], options[2]);

	$get('#option_and_properties').find('input:not(.xy),select,textarea').each(function () {
		if (option_and_properties[top.jQuery(this).attr('id')] !== undefined) {
			var val = option_and_properties[top.jQuery(this).attr('id')];
			switch (top.jQuery(this).attr('id')) {
				case 'visible':
					val = val ? 1 : 0;
				break;
			}
			top.jQuery(this).val(this.getAttribute('type') === 'range' ? val * 10 : val);
			top.jQuery(this).trigger('update');
		}
	});
};

getObjectData = function (object){
	if (!object)
		return;

	var object_type = object.properties.get('metaType')?object.properties.get('metaType').toLowerCase():'polygon',
		geometry = {},
		optes = {properties:{}, options:{}},
		_name,
		_key;
		
	geometry = object_type!='circle'?object.geometry.getCoordinates():[object.geometry.getCoordinates(),object.geometry.getRadius()];

	if( geometry.length==0 || !validateGeometry(geometry)){
		deleteObject(object);
		return;
	}
		
	for (_name in _opt) {
		if (_opt.hasOwnProperty(_name)) {
			for (_key = 0;_key < _opt[_name].length;_key = _key + 1) {
				if (object[_name].get(_opt[_name][_key]) !== undefined) {
					switch (_opt[_name][_key]) {
						case 'iconImageHref':
							optes[_name]['iconLayout'] = parseInt($get('#object_show_title_with_image').val() || 0, 10) ? 'default#imageWithContent' : 'default#image';
						break;
					}
					optes[_name][_opt[_name][_key]] = object[_name].get(_opt[_name][_key]);
				}
			}
		}
	}
	return [
		geometry,
		optes['options'],
		optes['properties'],
	]
};

var timeoutupdate;

objectWasUpdated = function  (object) {
	clearTimeout(timeoutupdate);
	timeoutupdate = setTimeout(function(){
		var opt = getObjectData(object);
		$get('#jform_coordinates').val(JSON.stringify(opt[0]));
		$get('#jform_options').val(JSON.stringify(opt[1]));
		$get('#jform_properties').val(JSON.stringify(opt[2]));
	}, 100);
};

loadImage = function (fimage, callback) {
    var loaded = false;
    function loadHandler() {
        if (loaded) {
            return;
        }
        loaded = true;
		callback&&callback.call(img);
    }
    var img = new Image();
    img.onload = loadHandler;
    img.src = fimage;
    if ( img.complete || img.clientHeight>0 ) {
        loadHandler();
    }
};

validateArray = function (value) {
	var i = 0;
	value = value.split(',');
	for (; i < value.length; i = i + 1) {
		value[i] = parseInt(value[i]);
	}
	return value;
};

validateColor = function (color) {
	if (!color) {
		return '';
	}
	if (!/^#/.test(color)) {
		color = '#' + color;
	}
	if (!/^#([a-f0-9]{6}|[a-f0-9]{3})/i.test(color)) {
		color = '#ffffff';
	}
	return color.toUpperCase();
};

deleteObject = function (map, object){
	map.geoObjects.remove(object);
	sizerBox.hide();
	delete object;
};

addObject = function (map, object_type, coordinates, options, properties, created){
	properties = top.jQuery.extend(true, {}, properties);
	options = top.jQuery.extend(true, {}, options);
	if (object) {
		deleteObject(map, object, sizerBox);
	}

	if (!coordinates||!coordinates.length){
        return;
    }

	if (options) {
        options['draggable'] = true;
    }

	switch( object_type ){
		case 'polygon':case 'polyline':
			object = new ymaps[object_type=='polygon'?'Polygon':'Polyline']( coordinates,properties,options)
			map.geoObjects.add(object);
			object.editor[!created ? 'startDrawing' : 'startEditing']();
		break;
		case 'placemark':case 'circle':
			object = new ymaps[object_type=='circle'?'Circle':'Placemark'](coordinates,properties,options);
			map.geoObjects.add(object);
		break;
	}
	
	object.events.add('dblclick',function(e){
		return false;
	});

	object.events.add('click',function(e){
	});
	
	sizerBox.init(object);
	
	
	!created && objectWasUpdated(object);
	initForm(object_type, object);
	object.events
		.add(['geometrychange','optionschange','propertieschange'],function(){
			objectWasUpdated(object);
		});

	return object;
};

validateGeometry = function (geometry){
	var coords = geometry;
	if (top.jQuery.type(geometry) == 'string') {
		try{
			coords = JSON.parse(geometry);
		} catch (e) {
			return false;
		}
	}
	if (top.jQuery.type(coords) == 'array') {
		if (coords.length) {
			for (var r in coords) {
				if (coords.hasOwnProperty(r)) {
					if (top.jQuery.type(coords[r]) == 'array') {
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
	return false;
};
getPosition = function (coord){
	return (function (crd){
		if(top.jQuery.isArray(crd[0])){
			return arguments.callee(crd[0]);
		};
		return crd;
	})(coord);
};

createObjectAndCenterMap = function(map, type, coordinates, options, properties) {
	addObject(map, type, coordinates, options, properties, true)
	map.setCenter(getPosition(coordinates));
	ymaps.geoQuery(object).applyBoundsToMap(map, {
		checkZoomRange: true
	});
};