<?php
/*
 * @package Joomla 3.9.1
 * @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @component Yandex Map Component
 * @copyright Copyright (C) Aleksandr Ermakov www.slyweb.ru
 */
 defined('_JEXEC') or die('Restricted access'); 


$document = JFactory::getDocument();

if($this->params->get('use_jquery')) {
	$jv = '1.7.2';
	$document->addScript('https://ajax.googleapis.com/ajax/libs/jquery/'.$jv.'/jquery.min.js');
	$this->here_jquery = true;
}
$document->addScript('https://api-maps.yandex.ru/2.1/?lang=ru_RU&amp;apikey='.$this->params->get('key'));
	
if(strpos($this->map->width_map_yandex,'%') == false) {
	$this->map->width_map_yandex = $this->map->width_map_yandex.'px';
	preg_match('@\d+@si',$this->map->width_map_yandex,$m);
	$this->map->width_map_yandex = $m[0].'px';
}
if(strpos($this->map->height_map_yandex,'%') == false) {
	$this->map->height_map_yandex = $this->map->height_map_yandex.'px';
	preg_match('@\d+@si',$this->map->height_map_yandex,$m);
	$this->map->height_map_yandex = $m[0].'px';
}

if($this->map->bradius == 1) {
	$borderradius = 'border-radius: 6px 6px 6px 6px;';
} else {
	$borderradius = '';
}

if($this->map->yandexborder == 1) {
	$border = 'border: 1px solid #'.$this->map->color_map_yandex.';';
}else {
	$border = '';
}
if($this->map->center_map_yandex == 1) {
	$margin = 'margin:0 auto;';
} else {
	$margin = '';
}
	
$style = '
.YMaps-b-balloon-wrap td {
padding:0!important;
}
#YMapsID {
	margin:0; 
	box-shadow: 4px 4px 4px #'.$this->map->color_map_yandex.';  
	background: -moz-linear-gradient(center top , #'.$this->map->color_map_yandex.', #F1F1F1) repeat scroll 0 0 #F1F1F1;
    color: #333333;
    font-weight: bold;
	'.$borderradius.'
    '.$border.'
	'.$margin.'

	}
.YMaps-b-balloon-content {
width:'.$this->map->oblako_width_map_yandex.'px !important;
}	
.imginmap {
	margin:0 5px 0 0;
}
';

$document->addStyleDeclaration($style);
$metka = '';
$metka .= 'myGeoObjects = [];';
$userpath = $this->params->get('userpathtoimg');
		
if(empty($userpath)) {

	$userpath = '/images/mapyandex/yandexmarkerimg/';
}
$i = -1;

foreach($this->metka as $val) {
++$i;
	if(!empty($val->userimg)) {
		$imgarr = json_decode($val->userimg);
		$startfile = '<img align="left" class="imginmap" src="'.JURI::root(true).$userpath.$imgarr->startfile.'">';
		$smallfile = '<img align="left" class="imginmap" src="'.JURI::root(true).$userpath.$imgarr->smallfile.'">';
	} else {
		$startfile = '';
		$smallfile = '';
	}
	

if($this->params->get('draggable_placemark')) {
		$draggable_placemark = 'draggable: true, // Метку можно перетаскивать, зажав левую кнопку мыши.';
}
	
if($this->params->get('new_placemark')) {
	
	if(preg_match('@Old@s',$val->deficon,$m)) {
		$val->deficon = str_replace('Old','',$val->deficon);
		$op = '
			options = { 
			iconLayout: \'default#image\',
			iconImageHref: \''.JURI::root(true).'/administrator/components/com_mapyandex/assets/images/deficon/'.$val->deficon.'.png\',
			iconImageSize: [27, 26],
			iconImageOffset: [-3, -42]
			}';
	} else {
		$op = 'options =  { 
			balloonCloseButton: true, 
			'.$draggable_placemark.'
		    preset: \'islands#'.$val->deficon.'\'
			}';
	}
} else {
	
	if(!preg_match('@Stretchy@s',$val->deficon,$m) && !preg_match('@Old@s',$val->deficon,$m)) {
		if(!$this->comparams->get('new_placemark')) {
				$smallfile = '';
		}
			$op = '
				options = {
				balloonCloseButton: true, 
				'.$draggable_placemark.'
				iconImageHref: "'.JURI::base(true).'/administrator/components/com_mapyandex/assets/images/deficon/'.$val->deficon.'.png", // картинка иконки
				iconImageSize: [27, 26], // размеры картинки
				iconImageOffset: [-3, -26] // смещение картинки
			}';
	} else if(preg_match('@Old@s',$val->deficon,$m)) {
		$val->deficon = str_replace('Old','',$val->deficon);
		
		$op = '
			options = {
		    iconLayout: \'default#image\',
			iconImageHref: \'administrator/components/com_mapyandex/assets/images/deficon/'.$val->deficon.'.png\',
			iconImageSize: [30, 42],
			iconImageOffset: [-3, -42]
			}';
	} else {
		$op = '
			options = {
			opacity: 0.5, 
			balloonCloseButton: true, 
			'.$draggable_placemark.'
			preset: \'islands#'.$val->deficon.'\'
			}';
	}
}
	//size of marker
	$wih = json_decode($val->wih);
	if(count($wih) == 0 || !$wih) {
			$wih[0] = 250;
			$wih[1] = 250;
	}
	if(preg_match('@Stretchy@s',$val->deficon,$m)) {
		$forstrchmarkertext = addslashes($val->misilonclick);		
	} else {
		$forstrchmarkertext = '';
	}
	if(preg_match('@Old@s',$val->deficon,$m)) $startfile = '';
	
	$before_metka = '
	var properties = {
        balloonContent: "'.addslashes($startfile).JHtmlString::truncate(addslashes($val->misil),15).'",
		iconContent: "<div>'.JHtmlString::truncate(addslashes($val->misil),25).'</div>",
        hintContent: "<div>'.JHtmlString::truncate(addslashes($val->misilonclick),25).'</div>",
    },
	'.$op.'';
	
	if($val->yandexcoord == 1) {
		$metka .= '
		ymaps.geocode(\''.$val->city_map_yandex.', '.$val->street_map_yandex.'\', {results: 100}).then(function (res) {
        /* После того, как поиск вернул результат, вызывается*/       
		var point = res.geoObjects.get(0).geometry.getCoordinates();
		'.$before_metka.'
		placemark = new ymaps.Placemark(point, properties, options);
		map.geoObjects.add(placemark);
		});	';
		
	} else {

		$lng = json_decode($val->lng);
		$metka .= '
		'.$before_metka.'
		placemark = new ymaps.Placemark(['.$lng->latitude_map_yandex.', '.$lng->longitude_map_yandex.'], properties, options);
		map.geoObjects.add(placemark);';
	}
}

$textarray = '';
$textarrayonput = ''; 
$route = json_decode($this->map->route_map_yandex);
$ymaproute = '';


$ymaproute = $this->addRouteToMap($route);

($this->map->map_baloon_autopan) ? $autopan = 'true' : $autopan = 'false';  
($this->map->map_centering) ? $map_centering = 'true' : $map_centering = 'false'; 
if(!$this->map->map_baloon_or_placemark) {

	$balloonorplacemark = 'startPlacemark = new ymaps.Placemark(point, {
							// Свойства
							// Текст метки
							iconContent: \''.addslashes($this->map->misil).'\',
							hintContent: "<div>'.addslashes($this->map->misilonclick).'</div>",
							balloonContentHeader: "<div>'.addslashes($this->map->misilonclick).'</div>"
             
						}, {
							// Опции
							// Иконка метки будет растягиваться под ее контент
							preset: \'islands#blueStretchyIcon\'
						});
						map.geoObjects.add(startPlacemark);';

} else {

	$balloonorplacemark = 'map.balloon.open(
						// Координаты балуна
						point, {
							/* Свойства балуна:
							   - контент балуна */
							content: \''.addslashes($this->map->misil).'\'
						}, {
							/* Опции балуна:
							   - балун имеет копку закрытия */ 
							closeButton: true,
							minWidth: '.$this->map->map_baloon_minwidth.',
							minHeight: '.$this->map->map_baloon_minheight.',
							autoPan: '.$map_centering.',
							autoPanDuration: '.$this->map->map_baloon_autopanduration.'
						}
					);';

}

if ($this->map->where_text == 2) {
	echo $this->map->text_map_yandex;
}


if($this->map->yandexcoord == 1) {
	$stylecoo='style="display:none;"';
	$valone = 'var valone = "'.$this->map->city_map_yandex.', '.$this->map->street_map_yandex.'"';
} else {
	$stylead = 'style="display:none;"';
	$parsejson = json_decode($this->map->lng);
	$lang = $parsejson->longitude_map_yandex;
	$lat = $parsejson->latitude_map_yandex;
	$valone = 'var valone = "'.$lat.', '.$lang.'"';
}
$autozoom = 'var autozoom = '.$this->map->autozoom.';';
if($this->map->autozoom) {
	$autozoomflag = 10;
} else {
	$autozoomflag = $this->map->yandexzoom;
}
	$el = json_decode($this->map->yandexel);


	if($el) {
	
			if(in_array(1,$el)) {
			$trafficControl = '"trafficControl",';
		}
			if(in_array(2,$el)) {
			$geolocationControl = '"geolocationControl",';
		}
			if(in_array(3,$el)) {
			$sputnik = '"typeSelector",';
		}
			if(in_array(4,$el)) {
			$search = '"searchControl",';
		}
			if(in_array(5,$el)) {
			$scale = '"zoomControl",';
		}
	} else {

		$trafficControl = '"trafficControl",';
		$geolocationControl = '"geolocationControl",';
		$sputnik = '"typeSelector",';
		$search	 = '"searchControl",';
		$scale 	 = '"zoomControl",';
	}
	
	if($this->map->yandexbutton == 1 || $this->map->map_type =='calculator'){
	$element = "
			// Добавление элементов управления
            ".$trafficControl."
            ".$geolocationControl ."
			".$sputnik." 
			".$scale."
			".$search."";
	}
	
(!empty($this->map->map_type) && $this->map->map_type == 'calculator') ? $this->map->map_type = 'calculator' : $this->map->map_type = 'map';
			

$textarray = '';
$textarrayonput = ''; 
$region = json_decode($this->map->region_map_yandex);

$map_region_style = array();
$map_region_style[1] = '#0000FF';
$region_border_color = '#FFFF00';
$map_region_style = json_decode($this->map->map_region_style);
if(!is_array($map_region_style) || empty($map_region_style)) {
$styleoption = '											
		strokeWidth: 6,
		strokeColor: \'#'.$map_region_style[1].'\', // синий
		opacity: \'0.5\', // синий
		fillColor: \''.$region_border_color.'\', // желтый
		draggable: true      // объект можно перемещать, зажав левую кнопку мыши';
} else {
$styleoption = '											
		strokeWidth: 6,
		strokeColor: \'#'.$map_region_style[1].'\', // синий
		opacity: \''.$map_region_style[0].'\', // синий
		fillColor: \''.$region_border_color.'\', // желтый
		draggable: true      // объект можно перемещать, зажав левую кнопку мыши';
}
$ymapregion = '';
if($region) {

	$gi = -1;
	$length = count($region)-1;
$ymapregion = '
myGeoobject = [];';
	foreach($region as $val) {
	$gi++;
				$textbefore = 'Регион № '.$gi;
				  
				  $textarrayonput  .= '<li class="ui-state-default"><div width="100" align="left" class="key"><label for="foobar">'.$textbefore.'</label></div><div width="100" align="left"><input type="text" class="acpro_inp_'.($gi+1).' newroute" name="name_region_yandex[]" size="100" value="'.$val.'" /></div><div class="imgdeleteroute" rel="'.($gi+1).'" data-region="'.$this->map->id.'">'.JHTML::_( 'image', 'administrator/components/com_mapyandex/assets/images/iconfalse.png', JText::_( 'COM_MAPYANDEX_DELETE_ROUTE_ITEM' )).'</div><div>'.JHTML::_( 'image', 'administrator/components/com_mapyandex/assets/images/icon-loading2.gif', JText::_( 'COM_MAPYANDEX_DELETE_ROUTE_ITEM' ),array('class'=>'imgyandexmaploader')).'</div><span class="ui-icon ui-icon-arrowthick-2-n-s"></span></li>';
					
					if($textarray != '') {
						$textarray = substr($textarray, 0, -1);
					}
		
					$c = 0;
					$jsarr = '';
					foreach(explode(',',$val) as $item) {

						if($c%2==0) {
							$jsarr .= '[';
						}
						if($c%2!==0) {
							$jsarr .= $item.'],';
						} else {
							$jsarr .= $item.',';
						}
						++$c;
					}
					//last comma delete ie8
					$jsarr = rtrim($jsarr, ",");
				
					$ymapregion .= '
					myGeometry = {
											type: \'Polygon\',
											coordinates: [
												[
												
													'.$jsarr.'

												]
											]
										},
										myOptions = {
											'.$styleoption.'
										};

									// Создаем геообъект с определенной (в switch) геометрией.
									myGeoobject['.($gi+1).'] = new ymaps.GeoObject({geometry: myGeometry}, myOptions);

									// При визуальном редактировании геообъекта изменяется его геометрия.
									// Тип геометрии измениться не может, однако меняются координаты.
									// При изменении геометрии геообъекта будем выводить массив его координат.
									//myGeoobject['.($gi+1).'].events.add(\'geometrychange\', function (event) {
									//	printGeometry(myGeoobject['.($gi+1).'].geometry.getCoordinates(),'.($gi+1).');
									//});

									// Размещаем геообъект на карте
									map.geoObjects.add(myGeoobject['.($gi+1).']);
									//myGeoobject['.($gi+1).'].editor.startEditing();

								// Выводит массив координат геообъекта в <div id="geometry">
					
					';
	}	
	
}	

$settings = json_decode($this->map->map_calculator_settings);
$defaultmap = ($this->map->defaultmap) ? $this->map->defaultmap : 'publicMap';

	
if($this->map->map_type =='map'){
$script ='	
ymaps.ready(function () { 
	
			var map;
			'.$autozoom.'
			'.$valone.'

			if(valone == "") {
				valone = "Москва, ул. Ленина, 50";
			}

						/* После того, как поиск вернул результат, вызывается*/
              ymaps.geocode(valone, {results: 100}).then(function (res) {
                    
					var point = res.geoObjects.get(0).geometry.getCoordinates();
			// Добавление полученного элемента на карту
			
			
				var map = new ymaps.Map("YMapsID", {
					// Центр карты
					center: res.geoObjects.get(0).geometry.getCoordinates(),
					// Коэффициент масштабирования
					zoom: '.$autozoomflag.',
					type: "yandex#'.$defaultmap.'",
					controls: ['.$element.']
				});
			
				if(autozoom) {

					map.setCenter(point, '.$this->map->yandexzoom.', {
						checkZoomRange: true,
						duration: 1000,
						callback: function(){
							'.$metka.'
						}
					});
				} else {

	
				map.zoomRange.get(
					/* Координаты точки, в которой определяются 
					   значения коэффициентов масштабирования */ 
					point)
					.then(function (zoomRange, err) {
					
					var userzoom = '.$this->map->yandexzoom.';
						if (!err) {
						
							// zoomRange[0] - минимальный масштаб
							// zoomRange[1] - максимальный масштаб
							if(userzoom > zoomRange[1]) {
								userzoom = zoomRange[1];
																
									map.setCenter(point, userzoom,{duration:500,callback:function(){
										'.$metka.'
									}});
								
							} else {
								'.$metka.'
							}
						
							
						}
					}
				)	
			}

		$j = jQuery.noConflict();

		$j(function(){
			$j(".submitb").click(function(){
				$j(\'#ajaxmarkerform\').submit();	
			});
			
			$j(\'#ajaxmarkerform\').submit(function(e){

				$j(".jerror").remove();
				
				var data = $j(\'#ajaxmarkerform\').serialize();
				var city = $j(\'#ajaxmarkerform input[name="city_map_yandex"]\').val();
				var street = $j(\'#ajaxmarkerform input[name="street_map_yandex"]\').val();
				var icon = $j(\'input:checked\').val();
				var smallfile = $j(\'#ajaxmarkerform #smallfile\').val();
				var startfile = $j(\'#ajaxmarkerform #startfile\').val();
				var lang = $j(\'#ajaxmarkerform #longitude\').val();
				var lant = $j(\'#ajaxmarkerform #latitude\').val();
				
					if(city == "" && (lant =="" && lang =="")) {
					
						$j(\'input[name="city_map_yandex"]\').after(\'<span class="jerror">Не указано!</span>\');
						$j(\'.submitb\').after(\'<span class="jerror">Вы не указали обязательное поле!</span>\');

						return false;
					
					}
					if(!$j(\'input:checked\').hasClass(\'deficon\') && smallfile !=="") {
						alert("Вы загрузили изображение - "+smallfile+", но выбранная вами метка не подходит для изображения, выберите другую!");
						$j(".loader_img_for_input").hide();
						return false;
					}
					$j(".loader_img_for_input").show();
				if(!icon) icon = "blueStretchyIcon";
				
				var address = city+", "+street;
				if(lant !=="" && lang !=="") address = [lant,lang];

				e.preventDefault();
				$j.ajax({
						data:data,
						url: \'index.php?option=com_mapyandex&task=ajaxit&format=row\',
						success: function(data) {
							$j(".loader_img_for_input").hide();
							ymaps.geocode(address, {results: 100}).then(function (res) {
						
								var point = res.geoObjects.get(0).geometry.getCoordinates();
								myPlacemark = new ymaps.Placemark(point, {
									// Свойства
									// Текст метки
									iconContent: "<div><img src=\"'.juri::root(true).$userpath.'"+smallfile+"\" alt=\"0\"></div>",
									hintContent: "<div><img src=\"'.juri::root(true).$userpath.'"+startfile+"\" alt=\"0\"></div>",
									balloonContentHeader: "<div>"+city+"</div>",
									balloonContent: "<div><img src=\"'.juri::root(true).$userpath.'"+startfile+"\" alt=\"0\"></div>"
					 
								}, {
									// Опции
									// Иконка метки будет растягиваться под ее контент
									preset: "islands#"+icon
								});
								map.geoObjects.add(myPlacemark);
							});

						},
						error:function(){
					
						},
						type:\'POST\'
				
				});
			
			});
		});

				
			'.$balloonorplacemark.'

      
           // Добавление стандартного набора кнопок
			map.controls
		
			'.$ymaproute.'
			'.$ymapregion.'
            });
				
});
';
} else {

	switch ($settings[3]) {
		case 1:
		$cuurentcytext = 'рублей';
		break;
		case 2:
		$cuurentcytext = 'евро';
		break;
		case 3:
		$cuurentcytext = 'доллары';
		break;
		case 4:
		$cuurentcytext = 'гривны';
		break;
	}
	
	$script ='	
	ymaps.ready(function () {
	
			var map;
			'.$valone.'

			if(!valone) {
				valone = "Санкт-Петербург, пр. Невский, 100";
			}

			/* После того, как поиск вернул результат, вызывается*/
              ymaps.geocode(valone, {results: 100}).then(function (res) {
                    
					var point = res.geoObjects.get(0).geometry.getCoordinates();
					// Добавление полученного элемента на карту
		
					// Создание экземпляра карты и привязка его к контейнеру div
					map = new ymaps.Map("YMapsID", {
					// Центр карты
					center: res.geoObjects.get(0).geometry.getCoordinates(),
					// Коэффициент масштабирования
					zoom: 12,
					type: "yandex#'.$defaultmap.'"
		
				}
				);	
				$j = jQuery.noConflict();
				calculator = new DeliveryCalculator(map, point);
				
                });
			
			
        });  
			 
				function DeliveryCalculator(map, finish) {
					this._map = map;
					this._start = null;
					this._finish = null;
					this._route = null;
					map.events.add(\'click\', this._onClick, this);
				}
			 
				var ptp = DeliveryCalculator.prototype;
			 
				ptp._onClick = function (e) {
					var position = e.get(\'coords\');
					$j(\'#deletemenu\').remove();
					
					if(!this._start) {
						this._start = new ymaps.Placemark(position, { iconContent: "А" }, { draggable : true });
						this._start.events.add(\'dragend\', this._onClick, this);
						this._map.geoObjects.add(this._start);
						this._start.events.add(\'contextmenu\',this._onContextmenu, this);

							
					} else if (!this._finish) {
						this._finish = new ymaps.Placemark(position, { iconContent: "Б" }, { draggable : true });
						this._finish.events.add(\'dragend\', this._onClick, this);
						this._map.geoObjects.add(this._finish);
						this._finish.events.add(\'contextmenu\',this._onContextmenu, this);
					} else {
						/*this._map.geoObjects.remove(this._start);
						this._start = null;
						this._map.geoObjects.remove(this._finish);
						this._finish = null;
						this._map.geoObjects.remove(this._route);

						this._route = null;*/
					}
					this.getDirections();
				};
			 
				ptp._onContextmenu = function(e) {
			
						// Отключаем стандартное контекстное меню браузера
						e.get(\'domEvent\').callMethod(\'preventDefault\');
						// Если меню метки уже отображено, то убираем его при повторном нажатии правой кнопкой мыши 
						if ($j(\'#deletemenu\').css(\'display\') == \'block\') {
							$j(\'#deletemenu\').remove();
						} else {
							// HTML-содержимое контекстного меню.
							var menuContent =
								\'<div id="deletemenu">\
									 <ul id="menu_list">\
										 <li><a class="delroute" href="#">Удалить маршрут!</a></li>\
									 </ul>\
								 </div>\';
							// Размещаем контекстное меню на странице
							$j(\'body\').append(menuContent);

							// ... и задаем его стилевое оформление.
							$j(\'#deletemenu\').css({
								position: \'absolute\',
								left: e.get(\'position\')[0],
								top: e.get(\'position\')[1],
								background: \'#ffffff\',
								border: \'1px solid #cccccc\',
								\'border-radius\': \'12px\',
								width: \'150px\',
								\'z-index\': 2
							});

							$j(\'#deletemenu ul\').css({
								\'list-style-type\': \'none\',
								padding: \'20px\',
								margin: 0
							});

							// Заполняем поля контекстного меню текущими значениями свойств метки.
							$j(\'#deletemenu input[name="icon_text"]\').val(this._start.properties.get(\'iconContent\'));
							$j(\'#deletemenu input[name="hint_text"]\').val(this._start.properties.get(\'hintContent\'));
							$j(\'#deletemenu input[name="balloon_text"]\').val(this._start.properties.get(\'balloonContent\'));
							var tojQuery = this;
							
							// При нажатии на кнопку "Сохранить" изменяем свойства метки
							// значениями, введенными в форме контекстного меню.
							$j(\'.delroute\').click(function (e) {
								e.preventDefault();
									tojQuery._map.geoObjects.remove(tojQuery._start);
									tojQuery._start = null;
									tojQuery._map.geoObjects.remove(tojQuery._finish);
									tojQuery._finish = null;
									tojQuery._map.geoObjects.remove(tojQuery._route);
									tojQuery._route = null;
								$j(\'#deletemenu\').remove();
						});
					}
				}

				ptp.getDirections = function () {
					var self = this,
						start = this._start.geometry.getCoordinates();
						if(!this._finish) {
							return;
						} 
						finish = this._finish.geometry.getCoordinates();
					this._route && this._map.geoObjects.remove(this._route);
					ymaps.geocode(finish)
						.then(function (geocode) {
							var address = geocode.geoObjects.get(0) && geocode.geoObjects.get(0).properties.get("balloonContentBody") || "";
			 
							ymaps.route([start, finish])
								.then(function (router) {
									var delimetr = 1;
									if(delimetr == 1) { 
										var distance = Math.round(router.getLength() / 1000);
										var d = \'км\';
									}	
									else {
										var distance = Math.round(router.getLength());
										var d = \'м\';
									}
									var cleardistance = Math.round(router.getLength());
									textcuur = \''.$cuurentcytext.'\';
				
									
									message = \'<span>Расстояние: \' + distance + \' \' + d +\'.</span><br/>\' +
												  \'<span style="font-weight: bold; font-style: italic">Стоимость доставки: %s (\' + textcuur + \'). </span>\';
			 						
									self._route = router.getPaths();
									self._route.options.set({ strokeWidth: 5, strokeColor: \'0000ffff\', opacity: 0.5 });
									self._map.geoObjects.add(self._route);
									self._route.events.add(\'contextmenu\',self._onContextmenu, self);
									self._finish.properties.set("balloonContentBody", address + message.replace(\'%s\', self.calculate(distance)));
									self._finish.balloon.open();
								});
						});
				};
			 
				ptp.calculate = function (len) {
					pr = '.$settings[0].';
					min = '.$settings[1].';

					if(!pr) pr = 0;
					var cost = len * pr;
			 
					return cost < min && min || cost;
				};
';
}

?>
 <div id="YMapsID" style="height:<?php echo $this->map->height_map_yandex;?>; width:<?php echo $this->map->width_map_yandex;?>;"></div>

 <div style="width:<?php echo $this->map->width_map_yandex;?>;<?php echo $margin;?>;text-align:right;margin-top:5px;clear:both; font-size:10px;"><a title="Яндекс карты для Joomla" href="http://slyweb.ru/yandexmap/">Яндекс карты для Joomla</a></div>
 
<?php	
$document->addScriptDeclaration($script);
$user = JFactory::getUser(); // it's important to set the "0" otherwise your admin user information will be loaded
$document->addStyleSheet(JURI::root(true).'/administrator/components/com_mapyandex/assets/mapyandex.css');
if(($user->id > 0 && $this->map->map_settings_user_all == 2) || ($this->map->map_settings_user_all == 1)) {
?>
<div class="mapconteiner">
<style>
.mapconteiner table,.mapconteiner tr td,.mapconteiner tr{
border:none !important;
}

</style>
  <table width="100%">
    <tr>
     <td width="90%" valign="top"><div class="col50">
  	<form action="index.php" method="post" name="ajaxmarkerform" id="ajaxmarkerform">	


<?php



$script ='	
	$j = jQuery.noConflict();;
	$j(function(){
	$j(\'[name="yandexcoord"]\').change(function(){
		v = $j(this).children("option:selected").val()
		if(v == 2) {
			$j(".dispcoords").fadeIn(200,function(){
				$j(".dispadres").fadeOut("slow");
			});
			
		}
		else {
			$j(".dispadres").fadeIn(200,function(){
				$j(".dispcoords").fadeOut("slow");
			});
		}
	});
			$j(\'[name="yandexbutton"]\').change(function(){
		
		v = $j(this).children("option:selected").val()
		if(v == 2) {

			$j(".elyandex").fadeOut(200);
			
		}
		else {
			$j(".elyandex").fadeIn(200);
		}
	});
});

function addfiletolist(startfile,smallfile) {
	$j = jQuery.noConflict();

	$j(function(){
		$j(\'#smallfile,#startfile\').remove();

		
		var addimgsm = $j("<input>");
		var addimgst = $j("<input>");
		addimgsm.attr({
				name:"smallfile",
				id:"smallfile",
				type:"hidden",
				value:smallfile
		});
		addimgst.attr({
				name:"startfile",
				id:"startfile",
				type:"hidden",
				value:startfile
		});

			
		$j("#ajaxmarkerform").append(addimgsm);
		$j("#ajaxmarkerform").append(addimgst);
		
	});
}


function getElementsByClassName(node,classname) {
  if (node.getElementsByClassName) { // use native implementation if available
    return node.getElementsByClassName(classname);
  } else {
    return (function getElementsByClass(searchClass,node) {
        if ( node == null )
          node = document;
        var classElements = [],
            els = document.getElementsByTagName("*"),
            elsLen = els.length,
            pattern = new RegExp("(^|\\s)"+searchClass+"(\\s|$)"), i, j;

        for (i = 0, j = 0; i < elsLen; i++) {
          if ( pattern.test(els[i].className) ) {
              classElements[j] = els[i];
              j++;
          }
        }
        return classElements;
    })(classname, node);
  }
}


$j = jQuery.noConflict();

$j(function(){
// для файлов к предложению 
	
	var loaderfilemessage;
	var videoFileMessage;

	// загружаем файл по событию submit
		videoFileMessage = document.getElementById("uploadfile");
		if(videoFileMessage) {
			videoFileMessage.onchange = function () {
				$j(\'.dialogsend .file_name_error\').hide();
				$j(\'.dialogsend .myfile\').show().text(\'(\'+this.value+\')\');
			}
		}
		
	$j("#uploadMessageFile").submit(function(e){

		
		if (videoFileMessage) {

		if($j(\'.dialogsend #videoname\').val() == \'\') {
			$j(\'.dialogsend #videoname_error\').show();
			return false;
		}
		else if($j(\'textarea[name="videodescription"]\').val() == \'\') {
			$j(\'#videodescription_error\').show();
			return false;
		}
			if(!$j.browser.msie) {
				var fileToUpload = videoFileMessage.files[0]; 
				
				if(!fileToUpload) {

					$j(\'.dialogsend .file_name_error\').show();
					return false;
				}
				$j(\'.loader_img,.desc_load\').show();
				$j(\'.loader_img_success,.submitb\').hide();
				var fs = fileToUpload.size/(1024 * 1024);
				if(fn == \'\') {
					$j(\'.dialogsend #file_name_error\').show();
					return false;
				}
				
				type = fileToUpload.type.split("/");
				/*type2 = fileToUpload.name.split(\'.\').pop();
				console.log(fileToUpload.name);
				console.log(type2);
				console.log(type[1]);*/
				switch(type[1]) {
						case "jpeg":
						break;
						case "jpg":
						break;
						case "png":
						break;
						case "gif":
						default:
						$j(\'.dialogsend #file_type_error\').show();
						return false;
						break;
				}

				if(fs > 10) {
					$j(\'.dialogsend #file_size_error\').show();
					return false;
				}
				var fn = fileToUpload.name;

			}
		} 
		
		$j(\'.dialogsend #file_size_error,.dialogsend #file_name_error,.dialogsend #videodescription_error,.dialogsend #videoname_error\').hide();
		
		var randIDS = Math.random();
		var p = $j(this);

		p.attr(\'target\',\'filemessage\');
		// в этот же файл(index.php) загружаем наш файл
		p.attr(\'action\',\'index.php?option=com_mapyandex&task=loadimg&format=row\');
		id_mess = $j(\'.zend_form\').attr(\'data-idmess\');
		
		$j(\'#chernovikupdate\').val(id_mess);
		
		// если первый раз, то создаём нужные элементы 
		if($j("#filemessage").length == 0){
	

			
			// if chernovik update file
			var id_mess_hidden = $j("<input>");
			id_mess_hidden.attr({
				name:"chernovikupdate",
				id:"chernovikupdate",
				type:"hidden",
				value:id_mess
			});
			$j("#uploadMessageFile").prepend(id_mess_hidden);
			
			u = $j(\'#tabs\').attr(\'data-sid\');
			
			var sid = $j("<input>");
			sid.attr({
				name:"sid",
				id:"sid",
				type:"hidden",
				value:u
			});
			$j("#uploadMessageFile").prepend(sid);
			
			var frame = $j("<iframe>");
			frame.attr({
			name:\'filemessage\',
			id:\'filemessage\',
			action:\'about:blank\',
			border:0
			}).css(\'display\',\'none\');
			p.after(frame);
		} else {

			
		
		}
		
		$j(\'.dialogsend .avatar-buttons\').hide(\'slow\');
		$j(\'.dialogsend .myvideo\').show().text(fn);
		

	});

});

';
	
$document->addScriptDeclaration($script);
?>

<div class="clrline"></div>	
 <div class="col100">
	<fieldset class="adminform">
		<h3><?php echo JText::_( 'COM_MAPYANDEX_NEWYMAR' ); ?></h3>
		<dl class="zend_form">		

			<dt><label for="foobar">
						<?php echo JText::_( 'COM_MAPYANDEX_NAMEMARKER' ); ?>:
					</label></dt>
					<dd><input type="text" name="name_marker" id="keyword" value="" /></dd>

				<dt><label for="foobar">
					<?php echo JText::_( 'COM_MAPYANDEX_SEACRHMETHOD' ); ?>:
				</label></dt>
				<dd>
			<?php 
			
				$statecoord[] = JHTML::_('select.option','1', JText::_( 'По адресу' ) );
				$statecoord[] = JHTML::_('select.option','2', JText::_( 'По координатам' ) );
				echo JHTML::_('select.genericlist',  $statecoord, $name = 'yandexcoord', $attribs = null, $key = 'value', $text = 'text', $selected = 1, $idtag = false, $translate = false );
			?></dd>

			<div class="dispadres">
				<div style="width:50%;">
					<dt class="dispadres" <?php echo $stylead;?> style="float:left;">
						<label for="foobar">
							<?php echo JText::_( 'COM_MAPYANDEX_CITY' ); ?>:
						</label>
					</dt>

					<dd><input type="text" name="city_map_yandex" value=""></dd>
				</div>
				<div style="width:50%;float:left;">
					<dt style="width:auto;float:left;" <?php echo $stylead;?>>
						<label for="foobar">
							<?php echo JText::_( 'COM_MAPYANDEX_STREET' ); ?>:
						</label>
					</dt>
					<dd> <input type="text" name="street_map_yandex" value=""></dd>
				</div>
			</div>
			



		
			<div class="dispcoords" style="display:none;">
				<dt><label for="foobar">
					<?php echo JText::_( 'COM_MAPYANDEX_COORD' ); ?>:
				</label></dt>
					<dd>			<?php
					// define modal options
					$modalOptions = array (
					'size' => array('x' => 700, 'y' => 650)
					
					);
					// load modal JavaScript
					JHTML::_('behavior.modal', 'a.modal', $modalOptions);
				?>
				<div style="display:inline" class="button2-left"><div class="image"><a href="<?php echo JURI::root(true).'/index.php?option=com_mapyandex&view=dialogxajax&tmpl=component';?>" class="modal"
				rel = "{
						handler: 'iframe'
						
						}">
						<?php echo JText::_( 'COM_MAPYANDEX_OPENMODAL' ); ?>
						</a></div></div></dd>
		
			
			


				<dt><label for="foobar">
					<?php echo JText::_( 'COM_MAPYANDEX_LNG' ); ?>:
				</label></dt>
				<dd>
				<input type="text" id="latitude" name="latitude_map_yandex" value="">
				<input type="text" id="longitude" name="longitude_map_yandex" value="">
				</dd>
				</div>

				<dt><label for="foobar">
					<?php echo JText::_( 'COM_MAPYANDEX_TEXTMAR' ); ?>:
				</label></dt>
				<dd><textarea name="misil" rows="3" cols="50"></textarea></dd>

			

				<dt><label for="foobar">
					<?php echo JText::_( 'COM_MAPYANDEX_TEXTMARONCLICK' ); ?>:
				</label></dt>
				<dd><textarea name="misilonclick" rows="3" cols="50"></textarea></dd>

		
			</dl>
	</fieldset>

<div class="clr"></div>

 </div>

<div class="clrline"></div>	
<h3><?php echo JText::_('COM_MAPYANDEX_MARKERSETTINGS'); ?></h3>
<?php


		echo '<table cellspacing="3" cellpadding="0" border="0" style="background-color:white" class="table"><tbody><tr valign="top">
      </tr>';
$option = array(
		0 => 'lightblueSmallPoint', 1 => 'whiteSmallPoint', 2 => 'greenSmallPoint', 3 => 'redSmallPoint', 4 => 'yellowSmallPoint', 
		5 => 'darkblueSmallPoint', 6 => 'nightSmallPoint', 7 => 'greySmallPoint', 8 => 'blueSmallPoint', 9 => 'orangeSmallPoint',
		10 => 'darkorangeSmallPoint', 11 => 'pinkSmallPoint', 12 => 'violetSmallPoint', 13 => 'airplaneIcon', 14 => 'arrowDownRightIcon',
		15 => 'arrowUpIcon', 16 => 'bankIcon', 17 => 'bicycleIcon', 18 => 'busIcon', 19 => 'carIcon', 20 => 'downhillSkiingIcon',
		21 => 'electricTrainIcon', 22 => 'gasStationIcon', 23 => 'houseIcon', 24 => 'metroKievIcon', 25 => 'metroYekaterinburgIcon',
		26 => 'phoneIcon', 27 => 'restaurauntIcon', 28 => 'skatingIcon', 29 => 'stadiumIcon', 30 => 'tailorShopIcon',
		31 => 'tireIcon', 32 => 'trolleybusIcon', 33 => 'turnRightIcon', 34 => 'workshopIcon', 
		35 => 'anchorIcon', 36 => 'arrowLeftIcon', 37 => 'attentionIcon', 38 => 'barIcon', 39 => 'bowlingIcon', 
		40 => 'cafeIcon', 41 => 'cellularIcon', 42 => 'dpsIcon', 43 => 'factoryIcon', 44 => 'gymIcon', 45 => 'keyMasterIcon', 
		46 => 'metroMoscowIcon', 47 => 'motobikeIcon', 48 => 'photographerIcon', 49 => 'shipIcon', 50 => 'skiingIcon',
		51 => 'storehouseIcon', 52 => 'theaterIcon', 53 => 'trainIcon', 54 => 'truckIcon', 55 => 'wifiIcon', 56 => 'arrowDownLeftIcon', 
		57 => 'arrowRightIcon', 58 => 'badmintonIcon', 59 => 'barberShopIcon', 60 => 'buildingsIcon', 61 => 'campingIcon', 
		62 => 'cinemaIcon', 63 => 'dryCleanerIcon', 64 => 'fishingIcon', 65 => 'hospitalIcon', 66 => 'mailPostIcon', 
		67 => 'metroStPetersburgIcon', 68 => 'mushroomIcon', 69 => 'pingPongIcon', 70 => 'shopIcon', 71 => 'smartphoneIcon', 
		72 => 'swimmingIcon', 73 => 'tennisIcon', 74 => 'tramwayIcon', 75 => 'turnLeftIcon', 76 => 'wifiLogoIcon' );



for($i=0; $i<count($option); $i++) {

if(($i % 15)==0) {
	echo '<tr valign="top">';
	}
    echo '<td align="center" width="" colname="col1">';
	echo JHTML::_('image', 'administrator/components/com_mapyandex/assets/images/deficon/'.$option[$i].'.png','','style="width:19px; height:20px; margin-bottom: 3px;"');
	
	echo '</td>';


	echo '<td align="center" width="" colname="col2"><input type="radio" value="'.$option[$i].'" id="deficon0" name="deficon" class="text_area"></td>'; 

  


	}

		

		
echo '</tr><tr valign="top">
        <td align="center" width="" colname="col1"></td>
        <td align="center" width="" colname="col2"></td>
        <td width="" colname="col3"></td>
        <td width="" colname="col4"></td>
        <td width="" colname="col5"></td>
        <td width="" colname="col6"></td>
        <td></td>
        <td></td>
        <td width="" colname="col3"></td>
        <td width="" colname="col4"></td>
        <td width="" colname="col5"></td>
        <td width="" colname="col6"></td>
      </tr></tbody></table>';
?>	
<div class="clrline"></div>	
<h3>Значки для меток с текстом и изображениями</h3>
<?php

echo '<table cellspacing="3" cellpadding="0" border="0" style="background-color:white" class="table"><tbody><tr valign="top">
      </tr>';
$option = array(
		0 => 'blackStretchyIcon', 1 => 'brownStretchyIcon', 2 => 'yellowStretchyIcon', 3 => 'yellowStretchyIcon', 4 => 'whiteStretchyIcon', 
		5 => 'violetStretchyIcon', 6 => 'redStretchyIcon', 7 => 'pinkStretchyIcon', 8 => 'orangeStretchyIcon', 9 => 'nightStretchyIcon',
		10 => 'lightblueStretchyIcon', 11 => 'greyStretchyIcon', 12 => 'greenStretchyIcon', 13 => 'darkorangeStretchyIcon', 14 => 'darkgreenStretchyIcon' , 15 => 'darkblueStretchyIcon', 16 => 'blueStretchyIcon' );



for($i=0; $i<count($option); $i++) {

if(($i % 6)==0) {
	echo '<tr valign="top">';
	}
    echo '<td align="center" width="" colname="col1">';
	echo JHTML::_('image', 'administrator/components/com_mapyandex/assets/images/deficon/'.$option[$i].'.png','','style="width:78px; height:40px; margin-bottom: 3px;"');
	
	echo '</td>';

	echo '<td align="center" width="" colname="col2"><input type="radio" value="'.$option[$i].'" class="deficon" name="deficon"></td>'; 

	}

		

		
echo '</tr><tr valign="top">
        <td align="center" width="" colname="col1">
         
        </td>
        <td align="center" width="" colname="col2"></td>
        <td width="" colname="col3"></td>
        <td width="" colname="col4"></td>
        <td width="" colname="col5"></td>
        <td width="" colname="col6"></td>
        <td align="center" width="" colname="col1">
         
        </td>
        <td align="center" width="" colname="col2"></td>
        <td width="" colname="col3"></td>
        <td width="" colname="col4"></td>
        <td width="" colname="col5"></td>
        <td width="" colname="col6"></td>
      </tr></tbody></table>';
?>
	</fieldset>
</div>
<input type="hidden" name="id" value="<?php echo $this->map->id;?>" />

</form>
 </td>
 </tr>
	</table>
<div class="clrline"></div>	
	<h3>Значёк метки может содержать изображение</h3>

	<div class="loader_img"></div>
	<div class="loader_img_success">Загрузка успешно завершена!</div>
	<div class="desc_load">Идёт загрузка... Дождитесь загрузки файла!</div>


	<form action="/user/loadfile"  enctype="multipart/form-data" method="post" id="uploadMessageFile" target="filemessage">
		


			<div class="dialogsend">
				<div class="myfile"></div>
				<div class="file_name_error" style="display:none;">Вы не выбрали файл!</div>
				<div id="file_type_error" style="display:none;">Не допустимый тип файла, допускается png,jpg (jpeg),gif!</div>
				<div id="derectorynoperms" style="display:none;">Директория не имеет прав на запись!</div>


					<div class="upload">
						Выбрать изобпажение 

						<input autocomplete="off" type="file" id="uploadfile" name="userVideo" value="Выберите файл" />
						
						</div>
					<input type="hidden" id="chernovikupdate" name="chernovikupdate" value="1" />
					<div class="upload">
						Загрузить...
						<input type="submit" id="uploadfilewithmessage" class="upload" name="upload-btn" value="Загрузить..." />
					</div>

				
			</div>
	  
	</form>


<div class="clrline"></div>	
<div class="loader_img_for_input"></div>

<div class="submitb" style="margin:10px 0 0 0;">Создать метку</div>


</div>
<?
}

$data = array(); // array for all user settings
?>

<?php


if ($this->map->where_text == 3) {
	echo $this->map->text_map_yandex;
}
?>