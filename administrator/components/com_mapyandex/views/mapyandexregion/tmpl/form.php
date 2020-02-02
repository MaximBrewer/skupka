<?php 
/*
 * @package Joomla 3.0
 * @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @component Yandex Map Component
 * @copyright Copyright (C) Aleksandr Ermakov www.slyweb.ru
 */
defined('_JEXEC') or die('Restricted access'); 
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('jquery.framework',false);
JHtml::_('jquery.ui', array('core', 'sortable'));
$r 			=  new MapYandexRenderAdminView();
$app		= JFactory::getApplication();
$option 	= $app->input->get('option');
$OPT		= strtoupper($option);
echo '<form action="index.php" method="post" name="adminForm" id="adminForm">';
echo '<div class="row-fluid">';
echo '<div class="span10 form-horizontal">';
echo '<fieldset>';



$tabs = array (
'general' 		=> JText::_($OPT.'_GENERAL_OPTIONS'),
'publishing' 	=> JText::_($OPT.'_PUBLISHING_OPTIONS')
);

echo $r->navigation($tabs);

echo '<div class="tab-content">'. "\n";

echo '<div class="tab-pane active" id="general">'."\n";


?>

	
		<div class="span12 fltlft">
		
		<fieldset class="adminform">
			<legend><?php echo empty($this->item->id) ? JText::_('COM_MAPYANDEX_NAMECOMPONENT') : JText::sprintf('COM_MAPYANDEX_NAMECOMPONENT', $this->item->id); ?></legend>
	

<?php

$document = JFactory::getDocument();
$document->addScript('https://api-maps.yandex.ru/2.1/?lang=ru_RU&amp;apikey='.$this->params->get('key'));

?>

<?php
	$trafficControl = '';
	$geolocationControl = '';
	$sputnik = '';
	$search = '';
	$scale = '';
	$styleel = '';
	$stylecoo = '';
	$stylead = '';
	$stylead = '';
	$element = '';
	$border = '';
	
	if($this->map->bradius == 1) {
		$borderradius = 'border-radius: 6px 6px 6px 6px;';
	}
	if($this->map->yandexborder == 1) {
		$border = 'border: 1px solid #'.$this->map->color_map_yandex.';';
	}
	
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

$style = '.YMaps-b-balloon-wrap td {
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

	}
.YMaps-b-balloon-content {
width:'.$this->map->oblako_width_map_yandex.'px !important;
}	
.imginmap {
	margin:0 5px 0 0;
}';
$document->addStyleDeclaration($style);
?>

<?php 
//всё шиворот на выворот
if($this->map->yandexcoord == 1) {

	$stylecoo='style="display:none;"';
	$valone = 'var valone = "'.$this->map->city_map_yandex.', '.$this->map->street_map_yandex.'"';
	$address = $this->map->city_map_yandex.', '.$this->map->street_map_yandex;
} else {
	$stylead = 'style="display:none;"';
	$parsejson = json_decode($this->map->lng);
	$longitude = $parsejson->longitude_map_yandex;
	$latitude = $parsejson->latitude_map_yandex;
	$valone = 'var valone = "'.$latitude.', '.$longitude.'"';

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
if($this->map->yandexbutton == 1){
$element = "
			// Добавление элементов управления
            ".$trafficControl."
            ".$geolocationControl ."
			".$sputnik." 
			".$scale."
			".$search."";
			}


$metka = '';

$userpath = $this->params->get('userpathtoimg');
		
if(empty($userpath)) {

	$userpath = '/images/mapyandex/yandexmarkerimg/';
}

foreach($this->metka as $val) {


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
		if(!$this->params->get('new_placemark')) {
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
        balloonContent: "'.addslashes($startfile).addslashes($val->misil).'",
		iconContent: "<div>'.JHtmlString::truncate(addslashes($val->misilonclick),5).'</div>",
        hintContent: "<div>'.JHtmlString::truncate(addslashes($val->misilonclick),10).'</div>",
       /*iconCaption : "Описаниие",*/

    },
	'.$op.'';
	
	//JLog::add('my error message', JLog::ERROR);
	//whatever debugging code you want to run, eg
    //JLog::add('my debug message', JLog::DEBUG, 'my-debug-category');


	
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




if($this->regions) {
	$regions = $this->regions;
	$this->textarrayoutput = $this->textarrayoutput;
} 


if($this->route) {
	$ymaproute = $this->route;
} 


$script ='	
$.noConflict();

	$j = jQuery;
	$j(function(){
	
	$j(\'[name="yandexcoord"]\').change(function(){
		v = $j(this).children("option:selected").val()
		if(v == 2) {
			$j(".dispcoords").fadeIn("slow",function(){
				$j(".dispadres").fadeOut("slow");
			});
			
		}
		else {
			$j(".dispadres").fadeIn("slow",function(){
				$j(".dispcoords").fadeOut("slow");
			});
		}
	});
		$j(\'[name="yandexbutton"]\').change(function(){
		
		v = $j(this).children("option:selected").val()
		if(v == 2) {

			$j(".elyandex").fadeOut("slow");
			
		}
		else {
			$j(".elyandex").fadeIn("slow");
		}
	});

	
	ymaps.ready(function () { 
	
			var map;

	  
          
			'.$valone.'

			if(valone == "") {
				valone = "Москва, ул. Ленина, 50";
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
					zoom: '.$this->map->yandexzoom.',
					controls: ['.$element.']
		
				}


				);	
			
				
				myCollection = new ymaps.GeoObjectCollection();
				startPlacemark = new ymaps.Placemark(point, {
							// Свойства
							// Текст метки
							iconContent: \''.JHtmlString::truncate(addslashes($this->map->misil),15).'\',
							hintContent: "<div>'.JHtmlString::truncate(addslashes($this->map->misilonclick),15).'</div>",
							balloonContentHeader: "<div>'.addslashes($this->map->misilonclick).'</div>"
             
						}, {
							// Опции
							// Иконка метки будет растягиваться под ее контент
							preset: \'islands#blueStretchyIcon\'
						});
				
				
				map.geoObjects.add(startPlacemark);

				
				$j(\'.addnewroute\').click(function(e){

				

				
						myGeoobjectdinamic = [];
						e.preventDefault()
						varnr = $j(\'.newroute\').length;
						found = [];
						var gc = map.getCenter();
						
						lang = gc[0].toString();
						lat = gc[1].toString();
						re = /[0-9\.]{5}/i
		
						found[0] = parseFloat(lang.substring(0,5))-0.015;
						found[1] = parseFloat(lat.substring(0,5))-0.015;
						
						found[2] = found[0] +0.015;
						found[3] = found[1] +0;
						found[4] = found[0] +0.015;
						found[5] = found[1] +0.045;
						found[6] = found[0] +0;
						found[7] = found[1] +0.045;

		
						myGeometry = {
							type: \'Polygon\',
							coordinates: [
								[
									[found[0],found[1]],
									[found[2], found[3]],
									[found[4], found[5]],
									[found[6], found[7]],

								]
							]
						},
						myOptions = {
							strokeWidth: 6,
							strokeColor: \'#0000FF\', // синий
							opacity: \'0.5\', // синий
							fillColor: \'#FFFF00\', // желтый
							draggable: true      // объект можно перемещать, зажав левую кнопку мыши
						};

					// Создаем геообъект с определенной (в switch) геометрией.
					myGeoobjectdinamic[varnr] = new ymaps.GeoObject({geometry: myGeometry}, myOptions);

					// При визуальном редактировании геообъекта изменяется его геометрия.
					// Тип геометрии измениться не может, однако меняются координаты.
					// При изменении геометрии геообъекта будем выводить массив его координат.
					myGeoobjectdinamic[varnr].events.add(\'geometrychange\', function (event) {
						changeGeometry(myGeoobjectdinamic[varnr].geometry.getCoordinates(),varnr);
					});

					// Размещаем геообъект на карте
					map.geoObjects.add(myGeoobjectdinamic[varnr]);
					// ... и выводим его координаты.
					changeGeometry(myGeoobjectdinamic[varnr].geometry.getCoordinates());
					// Подключаем к геообъекту редактор, позволяющий
					// визуально добавлять/удалять/перемещать его вершины.
					myGeoobjectdinamic[varnr].editor.startEditing();
				// Выводит массив координат геообъекта в <div id="geometry">

				var text = "Регион №"+varnr;
				
				var appnewroute = \'<li class="ui-state-default"><div width="100" align="left" class="key"><label for="foobar">\'+text+\':</label></div><div width="100" align="left"><input type="text" name="name_region_yandex[]" class="acpro_inp_\'+(varnr+1)+\' newroute" size="100" value="\'+myGeoobjectdinamic[varnr].geometry.getCoordinates()+\'" /></div><div class="imgdeleterouteactive" rel="\'+(varnr+1)+\'" data-region="'.$this->map->id.'">'.JHTML::_( 'image', 'administrator/components/com_mapyandex/assets/images/iconfalse.png', JText::_( 'COM_MAPYANDEX_DELETE_ROUTE_ITEM' )).'</div><div>'.JHTML::_( 'image', 'administrator/components/com_mapyandex/assets/images/icon-loading2.gif', JText::_( 'COM_MAPYANDEX_DELETE_ROUTE_ITEM' ),array('class'=>'imgyandexmaploader')).'</div><span class="ui-icon ui-icon-arrowthick-2-n-s"></span></li>\';
				
				if(varnr == 0)  { 
					$j(".sort").append(appnewroute);
				} else {
					$j(".sort li:last").after(appnewroute);
				}
				
				
					$j(".imgdeleterouteactive").live("click", function(){

					$tr = $j(this).parent();
					var idmap = $j(this).attr("data-region");
					var id = $j(this).attr("rel");
	
					idc = id-1;
	
					map.geoObjects.remove(myGeoobjectdinamic[idc]);
		
					
					$j.ajax({
					url: "index.php?option=com_mapyandex&view=mapyandexajaxtask",
					data: {id:id,idmapforregion:idmap},
					success: function(data){
						$j(\'.imgyandexmaploader\',$tr).hide();
						$tr.fadeOut("slow",function(){
							$j(this).remove();
						});
					},
					beforeSend: function(data){
						$j(\'.imgyandexmaploader\',$tr).show();
					}
					});
				});
				
				});
				
				function changeGeometry(coords,getinput) {
				   $j(\'#geometry\').html(\'Координаты: \' + stringify(coords));
					without = stringify(coords).replace(/\\]|\s/ig,"");
					without = without.replace(/\\[/ig,"");
		
					getinput = getinput+1;

					$j("input.acpro_inp_"+getinput).val(without);
					
					function stringify (coords) {
						var res = \'\';
						if ($j.isArray(coords)) {
							res = \'[ \';
							for (var i = 0, l = coords.length; i < l; i++) {
								if (i > 0) {
									res += \', \';
								}
								res += stringify(coords[i]);
							}
							res += \' ]\';
						} else if (typeof coords == \'number\') {
							res = coords.toPrecision(6);
						} else if (coords.toString) {
							res = coords.toString();
						}

						return res;
					}
				}
				
           // Добавление стандартного набора кнопок
			map.controls
			'.$metka.'
			'.$regions.'
			'.$ymaproute.'
			
			
							map.geoObjects.each(function (geoObject) {
					
					console.log(geoObject.properties);
					if (geoObject.properties.get(\'id\') == \'some id\') {
						
						return false;
					}
				});
				
                });
			


        });  

		
		
			<!--
	Joomla.submitbutton = function(task)
	{
			if (task == "mapyandexrregion.cancel") {
				submitform( task );
				return;
			}
			var serialize = $j(\'.newroute\').serialize();
		
			$j("#adminForm").append(\'<input type="hidden" name="allroutemap">\');
			$j(\'#adminForm input[name="allroutemap"]\').val(serialize);
			
			submitform( task );
	}
		//-->	

$j( ".sort" ).sortable({  items: ".ui-state-default", stop: function(event, ui) {


// 
 le = $j(this).find("li").length - 1;
 $j.each($j(this).find("li"),function(i,e){

 				if(i == 0) {
					var text = "Начало";
				} else if(i == le) {
					var text = "Конец пути";
				} else {
					var text = "Пункт "+i;
				}
				$j(this).find(".key label").text(text);
 
 });
 
 
 
 },handle:\'.ui-icon\'});
	
});';
	
$document->addScriptDeclaration($script);
?>
<div style="width:100%;">


				

		<table style="width:100%;" class="admintable">

					<tr>
					<td style="width:20%;" align="left" class="key">
				<label for="foobar">
					<?php echo JText::_( 'COM_MAPYANDEX_NAMEMAP' ); ?>:
				</label>
			</td>
								<td style="width:80%;" align="left">
			 <input type="text" readonly="true" name="name_map_yandex" id="keyword" value="<?php echo $this->map->name_map_yandex;?>" />
			</td>
			</tr>
			
		<tr>
					<td style="width:20%;" align="left" class="key">
				<label for="foobar">
					<?php echo JText::_( 'COM_MAPYANDEX_EXMAP' ); ?>:
				</label>
			</td>
			<td style="width:80%;" align="left">
<div>







    <div id="YMapsID" style="height:<?php echo $this->map->height_map_yandex;?>; width:<?php echo $this->map->width_map_yandex;?>;"></div>
	
    <div id="info"></div>

	


</div>
			</td></tr>
			

		
	</table>
	<div style="clear:both;">
	<ul class="sort" >
	<?php echo $this->textarrayoutput;?>
	</ul>
	</div>
	<div id="geometry"></div>
	<button class="addnewroute btn btn-small btn-success"><?php echo JText::_('COM_MAPYANDEX_ROUTE_PROCESS'); ?></button>


<div class="clr"></div>
		</fieldset>
</div>
</div>

		
<?php

echo '<div class="tab-pane" id="publishing">'."\n";

?>

<div class="width-30 fltrt">
          <fieldset class="adminform">
          <legend><?php echo JText::_('COM_MAPYANDEX_MARKERSETTINGS'); ?></legend>

		<fieldset class="adminform">
	
			<?php
			$map_region_style = json_decode($this->map->map_region_style);
			foreach($this->form->getFieldset('map_region_settings') as $field) {
				echo '<div class="control-group">';
				if (!$field->hidden) {
					echo '<div class="control-label">'.$field->label.'</div>';
				}
				echo '<div class="controls">'.$field->input.'</div>';
				echo '</div>';
			} 
			

			?>

		
			<div class="control-group">
				<div class="control-label">
				<?php echo JText::_('COM_MAPYANDEX_COLOR_MAP_REGION'); ?>
				</div>
				<div class="controls">

				<?php echo $this->form->getInput('color_map_region'); ?>
			
				
				
				</div>
			</div>
	
		</fieldset>
		
</div>
</div>

          </fieldset>
        </div>

<div class="clr"></div>

<input type="hidden" name="option" value="com_mapyandex" />
<input type="hidden" name="id" value="<?php echo $this->map->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="mapyandexregion" />
<input type="hidden" name="controller" value="mapyandexregion" />
</form>