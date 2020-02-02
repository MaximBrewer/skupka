<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Import library dependencies
jimport('joomla.event.plugin');
jimport('joomla.plugin.plugin');
jimport('joomla.application.component.helper');
jimport('joomla.filter.output');
jimport('joomla.html.parameter');


class plgContentMapYandex extends JPlugin
{

	var $_id;
	var $_db;
	var $num;
	var $map_type;
	var $userpath;
	var $comparams;
	var $here_jquery;

	public function __construct($subject, $config)
	{
		$view = JRequest::getWord('view');

		
		if($view != 'article' && $view != 'featured' && $view != 'html' && $view != 'item' && $view != 'contact' && $view != 'category')
		{
			return;
		}
		
		parent::__construct($subject, $config);
		$this->loadLanguage();
		$this->_id = JRequest::getInt('id');
		$this->_db = JFactory::getDBO();
		$document = JFactory::getDocument();
		$this->comparams = JComponentHelper::getParams( 'com_mapyandex' );
		
		$this->userpath = $this->comparams->get('userpathtoimg');
		$this->use_jquery = $this->comparams->get('use_jquery');
		$this->here_jquery = false;
		$document = JFactory::getDocument();
		$document->addScript('https://api-maps.yandex.ru/2.1/?lang=ru_RU&amp;apikey='.$this->comparams->get('key'));

		$this->loadLanguage('plg_mapyandex', JPATH_ADMINISTRATOR);
		

	}

	public function onContentPrepare($context, $row, $params, $page = 0)
	{
	
		$this->plgJSMarks($context, $row, $params, $page = 0);
	}
	
	function mapyandexdisp( $article, $params, $limitstart,$id,$idcount )
	{
		$this->_id = $id; 

		$view = JRequest::getCmd('view');
		
		if ( ($view != 'article' && $view != 'featured' && $view != 'html' && $view != 'contact' && $view != 'item' && $view != 'category')
		|| JRequest::getBool('fullview')
		|| JRequest::getVar('print'))
			{
					return null; 
					exit;
			} 
	
		
		/* The url adress of page */
		$currurl = JURI::current();
		$content='';

		
		$baseurl = JURI::base();
		$document = JFactory::getDocument();
		$title = $document->getTitle();
		
		$paramsc = $this->comparams;
		/* customs params  */
		$pretext = $paramsc->get( 'pretext');
		$h = $paramsc->get( 'size');
		$b = '#FDFFBC';
		$b = $paramsc->get( 'background');
		
	
		$metka = '';
		
		if($paramsc->get('draggable_placemark')) {
			$draggable_placemark = 'draggable: true, // Метку можно перетаскивать, зажав левую кнопку мыши.';
		}
		
		if (!JComponentHelper::isEnabled('com_mapyandex', true)) {

			echo JText::_('COM_MAPYANDEX_ERROR_MAPS_PLUGIN_REQUIRES_MAP_YANDEX_COMPONENT');
			
		}
		
		
		
		$map = $this->getFoobar($id);
		$getmetka = $this->getMetka($id);
	

		if($map) {
			(!empty($map->map_type) && $map->map_type == 'calculator') ? $map_type = 'calculator' : $map_type = 'map';
	
			


			
			if($map->bradius == 1) {
				$borderradius = 'border-radius: 6px 6px 6px 6px;';
			} else {
				$borderradius = '';
			}
			if($map->yandexborder == 1) {
				$border = 'border: 1px solid '.$map->color_map_yandex.';';
			} else {
				$border = '';
			}
			if($map->center_map_yandex == 1) {
				$margin = 'margin:0 auto;';
			} else {
				$margin = '';
			}
				
			$style = '.YMaps-b-balloon-wrap td {
					padding:0!important;
			}
			#YMapsID-'.$idcount.' {
			margin:0; 
			box-shadow: 4px 4px 4px '.$map->color_map_yandex.';  
			background: -moz-linear-gradient(center top , #'.$map->color_map_yandex.', #F1F1F1) repeat scroll 0 0 #F1F1F1;
			color: #333333;
			font-weight: bold;
			'.$borderradius.'
			'.$border.'
			'.$margin.'

			}
			.YMaps-b-balloon-content {
		width:'.$map->oblako_width_map_yandex.'px !important;
		}	
		.imginmap {
			margin:0 5px 0 0;
		}
			';
		$document->addStyleDeclaration($style);
		
$userpath = $this->userpath;

if(empty($userpath)) {

	$userpath = '/images/mapyandex/yandexmarkerimg/';
}

	foreach($getmetka as $val) {
			$startfile = '';
			$smallfile = '';
		if(!empty($val->userimg)) {
			
			$imgarr = json_decode($val->userimg);
			if(is_file(JURI::root(true).$userpath.$imgarr->startfile)) {
				$startfile = '<img align="left" class="imginmap" src="'.JURI::root(true).$userpath.$imgarr->startfile.'">';
				$smallfile = '<img align="left" class="imginmap" src="'.JURI::root(true).$userpath.$imgarr->smallfile.'">';
			}
		} 
		
		if($this->comparams->get('draggable_placemark')) {
		$draggable_placemark = 'draggable: true, // Метку можно перетаскивать, зажав левую кнопку мыши.';
		}
	
		if($this->comparams->get('new_placemark')) {

						
				if(preg_match('@Old@s',$val->deficon,$m)) {
					$val->deficon = str_replace('Old','',$val->deficon);
					
					$op = '
						options = { 
						iconLayout: \'default#image\',
						iconImageHref: \''.JURI::root(true).'/administrator/components/com_mapyandex/assets/images/deficon/'.$val->deficon.'.png\',
						iconImageSize: [27, 26],
						iconImageOffset: [-3, -42]
						}';
					//echo JHTML::_( 'image', 'administrator/components/com_mapyandex/assets/images/deficon/'.$val->deficon.'.png',JText::_( 'COM_MAPYANDEX_DELETE_ROUTE_ITEM' ));
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
			$wih = (!empty($val->wih)) ?json_decode($val->wih) : array();
			if(count($wih) == 0 || !$wih) {
					$wih[0] = 250;
					$wih[1] = 250;
			}
			if(preg_match('@Old@s',$val->deficon,$m)) $startfile = '';
				$before_metka = '
				var properties = {
					balloonContent: "'.addslashes($startfile).'<br />'.addslashes($val->misil).'",
					iconContent: "<div>'.JHtmlString::truncate(addslashes($val->misilonclick),5).'</div>",
					hintContent: "<div>'.JHtmlString::truncate(addslashes($val->misilonclick),10).'</div>",
					iconCaption : "Описаниие",

				},
				'.$op.'';
			if(preg_match('@Stretchy@s',$val->deficon,$m)) {
				$forstrchmarkertext = addslashes($val->misilonclick);		
			} else {
				$forstrchmarkertext = '';
			}
			
			if($val->yandexcoord == 1) {
				
				$metka .= '
				
				ymaps.geocode(\''.$val->city_map_yandex.', '.$val->street_map_yandex.'\', {results: 100}).then(function (res) {
				/* После того, как поиск вернул результат, вызывается*/       
				var point = res.geoObjects.get(0).geometry.getCoordinates();
				'.$before_metka.'
				
				placemark = new ymaps.Placemark(point, properties, options);
				map'.$idcount.'.geoObjects.add(placemark);
				});	';
				
			} else {
				
				$lng = json_decode($val->lng);
				$metka .= '
				'.$before_metka.'
				
				placemark = new ymaps.Placemark(['.$lng->latitude_map_yandex.', '.$lng->longitude_map_yandex.'], properties, options);
				map'.$idcount.'.geoObjects.add(placemark);';
			}
			
	}

		
$textarray = '';
$textarrayonput = ''; 
$route = json_decode($map->route_map_yandex);
$ymaproute = '';


$ymaproute = $this->addRouteToMap($route,$map,$id,$idcount);

($map->map_baloon_autopan) ? $autopan = 'true' : $autopan = 'false';  
($map->map_centering) ? $map_centering = 'true' : $map_centering = 'false'; 
if(!$map->map_baloon_or_placemark) {

	$balloonorplacemark = 'startPlacemark = new ymaps.Placemark(point_'.$idcount.', {
							// Свойства
							// Текст метки
							iconContent: \''.addslashes($map->misil).'\',
							hintContent: "<div>'.addslashes($map->misilonclick).'</div>",
							balloonContentHeader: "<div>'.addslashes($map->misilonclick).'</div>"
             
						}, {
							// Опции
							// Иконка метки будет растягиваться под ее контент
							preset: \'islands#blueStretchyIcon\'
						});
						map'.$idcount.'.geoObjects.add(startPlacemark);';

} else {

	$balloonorplacemark = 'map'.$idcount.'.balloon.open(
						// Координаты балуна
						point_'.$idcount.', {
							/* Свойства балуна:
							   - контент балуна */
							content: \''.addslashes($map->misil).'\'
						}, {
							/* Опции балуна:
							   - балун имеет копку закрытия */ 
							closeButton: true,
							minWidth: '.$map->map_baloon_minwidth.',
							minHeight: '.$map->map_baloon_minheight.',
							autoPan: '.$map_centering.',
							autoPanDuration: '.$map->map_baloon_autopanduration.'
						}
					);';

}

		if(strpos($map->width_map_yandex,'%') == false) {
			$map->width_map_yandex = $map->width_map_yandex.'px';
			preg_match('@\d+@si',$map->width_map_yandex,$m);
			$map->width_map_yandex = $m[0].'px';
		}
		if(strpos($map->height_map_yandex,'%') == false) {
			$map->height_map_yandex = $map->height_map_yandex.'px';
			preg_match('@\d+@si',$map->height_map_yandex,$m);
			$map->height_map_yandex = $m[0].'px';
		}
		
		if ($map->where_text == 2) {
			echo $map->text_map_yandex;
		}

		if($map->yandexcoord == 1) {
			$stylecoo='style="display:none;"';
			$valone = 'var valone_'.$idcount.' = "'.$map->city_map_yandex.', '.$map->street_map_yandex.'"';
		} else {
			$stylead = 'style="display:none;"';
			$parsejson = json_decode($map->lng);
			$lang = $parsejson->longitude_map_yandex;
			$lat = $parsejson->latitude_map_yandex;
			$valone = 'var valone_'.$idcount.' = "'.$lat.', '.$lang.'"';
		}
		
		$autozoom = 'var autozoom = '.$map->autozoom.';';
		if($map->autozoom) {
			$autozoomflag = 10;
		} else {
			$autozoomflag = $map->yandexzoom;
		}
		
	$el = json_decode($map->yandexel);


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
	if($map->yandexbutton == 1){
	$element = "
				// Добавление элементов управления
				".$trafficControl."
				".$geolocationControl ."
				".$sputnik." 
				".$scale."
				".$search."";
	}
				
	$settings = (!empty($map->map_calculator_settings)) ? json_decode($map->map_calculator_settings) : array();
	$defaultmap = (!empty($map->defaultmap)) ? $map->defaultmap : 'publicMap';

$textarray = '';
$textarrayonput = ''; 
$region = !(empty($map->region_map_yandex)) ? json_decode($map->region_map_yandex) : array();
$map_region_style = array();
$map_region_style[1] = '#0000FF';
$region_border_color = '#FFFF00';



$map_region_style = ( !empty($map->map_region_style) ) ? json_decode($map->map_region_style) : array();

if(!is_array($map_region_style) || empty($map_region_style)) {
$styleoption = '											
		strokeWidth: 6,
		strokeColor: \''.$map_region_style_default.'\', // синий
		opacity: \'0.5\', // синий
		fillColor: \''.$region_border_color.'\', // желтый
		draggable: true      // объект можно перемещать, зажав левую кнопку мыши';
} else {
$styleoption = '											
		strokeWidth: 6,
		strokeColor: \''.$map_region_style[1].'\', // синий
		opacity: \''.$map_region_style[0].'\', // синий
		fillColor: \''.$region_border_color.'\', // желтый
		draggable: true      // объект можно перемещать, зажав левую кнопку мыши';
		
}
$ymapregion = '';
if($region) {

	$gi = -1;
	$length = count($region)-1;
	$ymapregion = '
	myPolygon = [];';
	foreach($region as $val) {
	$gi++;
				$textbefore = 'Регион № '.$gi;
				  
				  $textarrayonput  .= '<li class="ui-state-default"><div width="100" align="left" class="key"><label for="foobar">'.$textbefore.'</label></div><div width="100" align="left"><input type="text" class="acpro_inp_'.($gi+1).' newroute" name="name_region_yandex[]" size="100" value="'.$val.'" /></div><div class="imgdeleteroute" rel="'.($gi+1).'" data-region="'.$map->id.'">'.JHTML::_( 'image', 'administrator/components/com_mapyandex/assets/images/iconfalse.png', JText::_( 'COM_MAPYANDEX_DELETE_ROUTE_ITEM' )).'</div><div>'.JHTML::_( 'image', 'administrator/components/com_mapyandex/assets/images/icon-loading2.gif', JText::_( 'COM_MAPYANDEX_DELETE_ROUTE_ITEM' ),array('class'=>'imgyandexmaploader')).'</div><span class="ui-icon ui-icon-arrowthick-2-n-s"></span></li>';
					
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
					//delete comma ie8 - error
					$jsarr = rtrim($jsarr,',');
					
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
									myPolygon['.($gi+1).'] = new ymaps.GeoObject({geometry: myGeometry}, myOptions);

									// При визуальном редактировании геообъекта изменяется его геометрия.
									// Тип геометрии измениться не может, однако меняются координаты.
									// При изменении геометрии геообъекта будем выводить массив его координат.
									//myPolygon['.($gi+1).'].events.add(\'geometrychange\', function (event) {
									//	printGeometry(myPolygon['.($gi+1).'].geometry.getCoordinates(),'.($gi+1).');
									//});

									// Размещаем геообъект на карте
									map'.$idcount.'.geoObjects.add(myPolygon['.($gi+1).']);
									//myPolygon['.($gi+1).'].editor.startEditing();

								// Выводит массив координат геообъекта в <div id="geometry">
					
					';
	}	
	
}	


	
if($map_type =='map'){

	$script ='
	
	ymaps.ready(function () { 
	
			var map'.$idcount.';
			'.$autozoom.'
			'.$valone.'

			if(valone_'.$idcount.' == "") {
				valone_'.$idcount.' = "Москва, ул. Ленина, 50";
			}

						/* После того, как поиск вернул результат, вызывается*/
              ymaps.geocode(valone_'.$idcount.', {results: 100}).then(function (res) {
                    
					var point_'.$idcount.' = res.geoObjects.get(0).geometry.getCoordinates();
			// Добавление полученного элемента на карту
			
			
				map'.$idcount.' = new ymaps.Map("YMapsID-'.$idcount.'", {
					// Центр карты
					center: res.geoObjects.get(0).geometry.getCoordinates(),
					// Коэффициент масштабирования
					zoom: '.$autozoomflag.',
					type: "yandex#'.$defaultmap.'",
					controls: ['.$element.']
				});
			
				if(autozoom) {

					map'.$idcount.'.setCenter(point_'.$idcount.', '.$map->yandexzoom.', {
						checkZoomRange: true,
						duration: 1000,
						callback:function(){
							'.$metka.'
						}
					});
				} else {
	
	
				map'.$idcount.'.zoomRange.get(
					/* Координаты точки, в которой определяются 
					   значения коэффициентов масштабирования */ 
					point_'.$idcount.')
					.then(function (zoomRange, err) {
					
					var userzoom = '.$map->yandexzoom.';
						if (!err) {
						
							// zoomRange[0] - минимальный масштаб
							// zoomRange[1] - максимальный масштаб
							if(userzoom > zoomRange[1]) {
								userzoom = zoomRange[1];
																
									map'.$idcount.'.setCenter(point_'.$idcount.', userzoom,{duration:500,callback:function(){
										'.$metka.'
									}});
								
							} else {
								'.$metka.'
							}
						
							
						}
					}
				)	
			}
			'.$balloonorplacemark.'

      
           // Добавление стандартного набора кнопок
			map'.$idcount.'.controls
			
				'.$ymaproute.'
				'.$ymapregion.'
                });
		
			
});';
} else {
	if($this->use_jquery) {
		if(!$this->here_jquery) {
			$jv = '1.7.2';
			$document->addScript('https://ajax.googleapis.com/ajax/libs/jquery/'.$jv.'/jquery.min.js');
			$this->here_jquery = true;
		}
	}
	$script ='
	ymaps.ready(function () { 
	
			var map'.$idcount.';
			'.$valone.'

			if(!valone_'.$idcount.') {
				valone_'.$idcount.' = "Санкт-Петербург, пр. Невский, 100";
			}

			/* После того, как поиск вернул результат, вызывается*/
              ymaps.geocode(valone_'.$idcount.', {results: 100}).then(function (res) {
                    
					var point = res.geoObjects.get(0).geometry.getCoordinates();
					// Добавление полученного элемента на карту
		
					// Создание экземпляра карты и привязка его к контейнеру div
					map'.$idcount.' = new ymaps.Map("YMapsID-'.$idcount.'", {
					// Центр карты
					center: res.geoObjects.get(0).geometry.getCoordinates(),
					// Коэффициент масштабирования
					zoom: '.$autozoomflag.',
					type: "yandex#'.$defaultmap.'",
					controls: ['.$element.']
		
				}
				);	
				$j = jQuery.noConflict();
				calculator = new DeliveryCalculator(map'.$idcount.', point);
				
 


      
           // Добавление стандартного набора кнопок
			map'.$idcount.'.controls
			
			
                });
			
			
        });  
		
			var DELIVERY_TARIF = 15,
			MINIMUM_COST = 100;

			 
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
									textcuur = \'рублей\';
				
									
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
	$document->addScriptDeclaration($script);

	$content .= ' <div id="YMapsID-'.$idcount.'" style="height:'.$map->height_map_yandex.'; width:'.$map->width_map_yandex.';"></div>

		 <div style="width:'.$map->width_map_yandex.';'.$margin.';text-align:right;margin-top:5px;clear:both; font-size:10px;"><a href="http://slyweb.ru/developer/yandexmap/" title="Яндекс карты для Joomla">Яндекс карты для Joomla</a></div>

		 <div style="height:50px;" class="clear"></div>';


	if ($map->where_text == 3) {
		$content .= $map->text_map_yandex;
	}
	
	} else {
		$content .= JText::_("COM_MAPYANDEX_ERROR_PLUGIN_YANDEX_MAP");

	}

		
	return $content;
	}


	/**
	 * Returns the query
	 * @return string The query to be used to retrieve the rows from the database
	 */
	 
	function buildQuery($id)
	{

		$query = ' SELECT * '
			. ' FROM #__map_yandex WHERE id ='.$id;

		return $query;
	}


	function hit($id)
	{

		$db = JFactory::getDBO();
		$db->setQuery('UPDATE #__map_yandex
		SET hits = hits + 1 WHERE id ='.$id);
		$db->query();
	}
	
	protected function populateState() 
	{
			$app = JFactory::getApplication();
			// Get the message id
			$id = JRequest::getInt('id');
			$this->setState('message.id', $id);
	 
			// Load the parameters.
			$params = $app->getParams();
			$this->setState('params', $params);
			parent::populateState();
	}
	
	/**
	 * Returns the query
	 * @return string The query to be used to retrieve the rows from the database
	 */
	function _buildQuery()
	{

		$query = ' SELECT * '
			. ' FROM #__map_yandex WHERE id ="'.(int)$this->_id.'"';

		return $query;
	}
	/**
	 * Возвращаем данные
	 * @return array Возврату подлежит массив объектов
	 */
	function getFoobar()
	{

		
			$this->hit($this->_id);
			$query = $this->_buildQuery();
			$this->_db->setQuery($query);
			
			if (!$this->_foobar = $this->_db->loadObject()) 
			{
				$this->setError($this->_db->getError());
			}
			else
			{
				// Load the JSON string
				$params = new JRegistry;
                // loadJSON is @deprecated    12.1  Use loadString passing JSON as the format instead.
				$params->loadString($this->_foobar->comparams, 'JSON');
				//$params->loadJSON($this->_foobar->comparams);
				$this->_foobar->comparams = $params;
 
	
			}			
		

		return $this->_foobar;
	}
	
	/**
	 * Добавление маршрута на карту
	 * @return array Возврату подлежит массив объектов
	 */
	 
	function addRouteToMap($route,$map,$id,$idcount)
	{
	$textarray = '';
	$textarrayonput = '';
		if($route) {
			$i = -1;
			$length = count($route)-1;
			foreach($route as $val) {
			$i++;
						  $textarray  .= '\''.$val.'\',';
						  if($i == 0) {
							$textbefore = 'Начало пути';
						  } else if($i == $length) {
							$textbefore = 'Конец пути';
						  }	
						  else {
							$textbefore = 'Пункт '.$i;
						  }
						  
						  $textarrayonput  .= '<li class="ui-state-default"><div width="100" align="left" class="key"><label for="foobar">'.$textbefore.'</label></div><div width="100" align="left"><input type="text" name="name_route_yandex[]" class="newroute" size="100" value="'.$val.'" /></div><div class="imgdeleteroute" rel="'.$i.'">'.JHTML::_( 'image', 'administrator/components/com_mapyandex/assets/images/iconfalse.png', JText::_( 'COM_MAPYANDEX_DELETE_ROUTE_ITEM' )).'</div><div>'.JHTML::_( 'image', 'administrator/components/com_mapyandex/assets/images/icon-loading2.gif', JText::_( 'COM_MAPYANDEX_DELETE_ROUTE_ITEM' ),array('class'=>'imgyandexmaploader')).'</div><span class="ui-icon ui-icon-arrowthick-2-n-s"></span></li>';
							

			}
			
			if($textarray != '') {
				$textarray = substr($textarray, 0, -1);
			}
			($map->map_centering) ? $map_centering = 'true' : $map_centering = 'false'; 
			$ymaproute = 'ymaps.route([
							'.$textarray.'
						], {
							// Опции маршрутизатора
							mapStateAutoApply: '.$map_centering.' // автоматически позиционировать карту
						}).then(function (route) {
							map'.$idcount.'.geoObjects.add(route);
							
							   var points = route.getWayPoints();  
							// Задаем стиль метки - иконки будут красного цвета, и
							// их изображения будут растягиваться под контент
							
							points.options.set(\'preset\', \'islands#redStretchyIcon\');
							route.options.set({ strokeColor: \''.$map->color_map_route.'\', opacity: '.$map->map_route_opacity.' });
							points.get(0).properties.set(\'iconContent\', \'Точка отправления\');
							for(i = 0; i <='.($length).';i++) {
								if(i == '.($length).') {		
									points.get('.($length).').properties.set(\'iconContent\', \'Точка прибытия\');
								}
							}
						}, function (error) {
							alert("Возникла ошибка: " + error.message);
						});';
		} else {
			$ymaproute = '';
		}		
		return $ymaproute;
	}
	
	/**
	 * Возвращаем метки
	 * @return array Возврату подлежит массив объектов
	 */
	function getMetka($id)
	{
	
			$db = JFactory::getDbo();
			$query = "SELECT * FROM #__map_yandex_metki WHERE id_map =".$db->Quote($id);
			$db->setQuery($query);
			$metka = $db->loadObjectList();
	
		// return the foobar data
		return $metka;
	
	}
	function number($matches)
	{
		$c = preg_replace('@[^A-Za-z0-9 ]@si','',$this->context);
		if (version_compare(PHP_VERSION, '7.0.0') >= 0) {
			$this->idfind[] = $c.random_int(100, 999);	
		} else {
			$this->idfind[] = str_replace('.','',$c.uniqid('sl', TRUE));	
		}
		//
		
		return '{mapyandex_id='.end($this->idfind).'}';
	
	}
	function plgJSMarks( $context, $article, $params, $limitstart )
	{
		


			if(preg_match('@{mapyandex_id=(.*?)|mapyandex_calculator_id=(.*?)}@si',$article->text))
			{
				//$plugin =& JPluginHelper::getPlugin('content', 'mapyandex');
				$pluginParams = $params;
					
				preg_match_all('@{(mapyandex_id|mapyandex_calculator_id)=(.*?)}@si',$article->text,$id);
	
				//замена одинаковых карт
				//замена одинаковых карт
				$this->idfind = array();
				$this->context = $context;
				$article->text = preg_replace_callback('@{mapyandex_id=.*?}|{mapyandex_calculator_id=.*?}@si',array(&$this,'number'),$article->text);
				//замена разметки на html
				$idcount = -1;
				//var_dump($id[2]);
				foreach($id[2] as $map) {
					$idcount++;	

					$content = $this->mapyandexdisp( $article, $pluginParams, $limitstart, $map,$this->idfind[$idcount]);
					$article->text = preg_replace('@{mapyandex_id='.$this->idfind[$idcount].'}|{mapyandex_calculator_id='.$this->idfind[$idcount].'}@si',$content,$article->text);

				}
			}

		return true;
	} 
}





?>

