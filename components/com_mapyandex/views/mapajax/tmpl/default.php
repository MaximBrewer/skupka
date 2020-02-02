<?php
/*
 * @package Joomla 1.5
 * @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @component Yandex Map Component
 * @copyright Copyright (C) Aleksandr Ermakov www.slyweb.ru
 */
 defined('_JEXEC') or die('Restricted access'); 



?>
	<?php $document =& JFactory::getDocument();?>


	<?php $document->addScript('http://api-maps.yandex.ru/2.0.10/?load=package.full&mode=debug&lang=ru-RU');?>


<?php
	
	if($this->foobar->bradius == 1) {
		$borderradius = 'border-radius: 6px 6px 6px 6px;';
	}
	if($this->foobar->yandexborder == 1) {
		$border = 'border: 1px solid #'.$this->foobar->color_map_yandex.';';
	}
	if($this->foobar->center_map_yandex == 1) {
		$margin = 'margin:0 auto;';
	}
$style = '.YMaps-b-balloon-wrap td {
padding:0!important;
}
#YMapsID {
	margin:0; 
	box-shadow: 4px 4px 4px #'.$this->foobar->color_map_yandex.';  
	background: -moz-linear-gradient(center top , #'.$this->foobar->color_map_yandex.', #F1F1F1) repeat scroll 0 0 #F1F1F1;
    color: #333333;
    font-weight: bold;
	'.$borderradius.'
    '.$border.'
	'.$margin.'

	}
	.YMaps-b-balloon-content {
width:'.$this->foobar->oblako_width_map_yandex.'px !important;
}	
	
	';
$document->addStyleDeclaration($style);
$metka = '';



foreach($this->metka as $val) {

if($this->foobar->params->get('draggable_placemark')) {
		$draggable_placemark = 'draggable: true, // Метку можно перетаскивать, зажав левую кнопку мыши.';
}
	
if($this->foobar->params->get('new_placemark')) {
    $op = 'options = { balloonCloseButton: true, 
				'.$draggable_placemark.'
               preset: \'twirl#'.$val->deficon.'\'
                },';
} else {
    $op = 'options = { balloonCloseButton: true, 
					'.$draggable_placemark.'
                    iconImageHref: \''.JURI::base(true).'/administrator/components/com_mapyandex/assets/images/deficon/'.$val->deficon.'.png\', // картинка иконки
					iconImageSize: [27, 26], // размеры картинки
                    iconImageOffset: [-3, -26] // смещение картинки
                },';
}


	if($val->yandexcoord == 1) {

		$metka .= '
					/* После того, как поиск вернул результат, вызывается*/
    ymaps.geocode(\''.$val->city_map_yandex.', '.$val->street_map_yandex.'\', {results: 100}).then(function (res) {
                    
	var point = res.geoObjects.get(0).geometry.getCoordinates();

	var properties = {
        balloonContent: "'.addslashes($val->misil).'",
        hintContent: "<div>'.addslashes($val->misilonclick).'</div>",

    },
	'.$op.'
    placemark = new ymaps.Placemark(point, properties, options);

	map.geoObjects.add(placemark);
			
		});	';
		
	} else {

		$lng = json_decode($val->lng);
		
		$metka .= '
	var properties = {
        balloonContent: "'.addslashes($val->misil).'",
        hintContent: "<div>'.addslashes($val->misilonclick).'</div>",

    },
	'.$op.'
    placemark = new ymaps.Placemark(['.$lng->latitude_map_yandex.', '.$lng->longitude_map_yandex.'], properties, options);

	map.geoObjects.add(placemark);
			
			';
	}
	
}


$textarray = '';
$textarrayonput = ''; 
$route = json_decode($this->foobar->route_map_yandex);
$ymaproute = '';


$ymaproute = $this->addRouteToMap($route);

($this->foobar->map_baloon_autopan) ? $autopan = 'true' : $autopan = 'false';  
($this->foobar->map_centering) ? $map_centering = 'true' : $map_centering = 'false'; 
if(!$this->foobar->map_baloon_or_placemark) {

	$balloonorplacemark = 'startPlacemark = new ymaps.Placemark(point, {
							// Свойства
							// Текст метки
							iconContent: \''.addslashes($this->foobar->misil).'\',
							hintContent: "<div>'.addslashes($this->foobar->misilonclick).'</div>",
							balloonContentHeader: "<div>'.addslashes($this->foobar->misilonclick).'</div>"
             
						}, {
							// Опции
							// Иконка метки будет растягиваться под ее контент
							preset: \'twirl#blueStretchyIcon\'
						});
						map.geoObjects.add(startPlacemark);';

} else {

	$balloonorplacemark = 'map.balloon.open(
						// Координаты балуна
						point, {
							/* Свойства балуна:
							   - контент балуна */
							content: \''.addslashes($this->foobar->misil).'\'
						}, {
							/* Опции балуна:
							   - балун имеет копку закрытия */ 
							closeButton: true,
							minWidth: '.$this->foobar->map_baloon_minwidth.',
							minHeight: '.$this->foobar->map_baloon_minheight.',
							autoPan: '.$map_centering.',
							autoPanDuration: '.$this->foobar->map_baloon_autopanduration.'
						}
					);';

}
?>

<?php
if ($this->foobar->where_text == 2) {
	echo $this->foobar->text_map_yandex;
}

if($this->foobar->yandexcoord == 1) {
	$stylecoo='style="display:none;"';
	$valone = 'var valone = "'.$this->foobar->city_map_yandex.', '.$this->foobar->street_map_yandex.'"';
} else {
	$stylead = 'style="display:none;"';
	$parsejson = json_decode($this->foobar->lng);
	$lang = $parsejson->longitude_map_yandex;
	$lat = $parsejson->latitude_map_yandex;
	$valone = 'var valone = "'.$lat.', '.$lang.'"';
}
$autozoom = 'var autozoom = '.$this->foobar->autozoom.';';
if($this->foobar->autozoom) {
	$autozoomflag = 10;
} else {
	$autozoomflag = $this->foobar->yandexzoom;
}
	$el = json_decode($this->foobar->yandexel);
	if($el) {
	
			if(in_array(1,$el)) {
			$lineika = '.add("mapTools")';
		}
			if(in_array(2,$el)) {
			$minimap = '.add("miniMap")';
		}
			if(in_array(3,$el)) {
			$sputnik = '.add("typeSelector")';
		}
			if(in_array(4,$el)) {
			$search = '.add("searchControl")';
		}
			if(in_array(5,$el)) {
			$scale = '.add("zoomControl")';
		}
	} else {

		$lineika = '.add("mapTools")';
		$minimap = '.add("miniMap")';
		$sputnik = '.add("typeSelector")';
		$search	 = '.add("searchControl")';
		$scale 	 = '.add("zoomControl")';
	}
	
if($this->foobar->yandexbutton == 1){
$element = "
			// Добавление элементов управления
			".$sputnik."
            ".$lineika."
            
			".$minimap." 
			".$scale."
			".$search."";
			}


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
			
			
				map = new ymaps.Map("YMapsID", {
					// Центр карты
					center: res.geoObjects.get(0).geometry.getCoordinates(),
					// Коэффициент масштабирования
					zoom: '.$autozoomflag.'
					
				});
			
				if(autozoom) {

					map.setCenter(point, '.$this->foobar->yandexzoom.', {
						checkZoomRange: true,
						duration: 1000
					});
				} else {

	
				map.zoomRange.get(
					/* Координаты точки, в которой определяются 
					   значения коэффициентов масштабирования */ 
					point)
					.then(function (zoomRange, err) {
					
					var userzoom = '.$this->foobar->yandexzoom.';
						if (!err) {
						
							// zoomRange[0] - минимальный масштаб
							// zoomRange[1] - максимальный масштаб
							if(userzoom > zoomRange[1]) {
								userzoom = zoomRange[1];
																
									map.setCenter(point, userzoom,{duration:500});
								
							}
						
							
						}
					}
				)	
			}

		

				
			'.$balloonorplacemark.'

      
           // Добавление стандартного набора кнопок
			map.controls
			'.$element.'
			'.$metka.'
			

			
			'.$ymaproute.'
            });
		
			
});
';
	
$document->addScriptDeclaration($script);

// "generate" a new JUser Object
$user = JFactory::getUser(); // it's important to set the "0" otherwise your admin user information will be loaded
if($user->id > 0) {
$document =& JFactory::getDocument();

$document->addScript('https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js');

?>
<script type="text/javascript">
$j = jQuery.noConflict();

$j(function(){
	$j('#ajaxmarkerform').submit(function(e){
		var data = $j('#ajaxmarkerform').serialize();
		e.preventDefault();
		$j.ajax({
				//data:data,
				url: 'index.php?option=com_mapyandex&format=raw',
				success: function(data) {
				   //$('#quote p').html(data);
				},
				error:function(){
			
				}
		
		});
	
	});
});
</script>
  <table width="100%">
    <tr>
     <td width="90%" valign="top"><div class="col50">
  	<form action="index.php" method="post" name="ajaxmarkerform" id="ajaxmarkerform">	


<?php



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
});
		<!--
		function changeDisplayImage() {
			if (document.adminForm.imageurl.value !="") {
				document.adminForm.imagelib.src="../images/banners/" + document.adminForm.imageurl.value;
			} else {
				document.adminForm.imagelib.src="images/blank.png";
			}
		}
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == "cancel") {
				submitform( pressbutton );
				return;
			}
			// do field validation
			if (form.name_marker.value == "") {
				alert( "'.JText::_( "ERROR_NAME_MAP", true ).'" );
			}
			else if (form.city_map_yandex.value == "") {
				alert( "'.JText::_( "ERROR_CITY", true ).'" );
			
			} else if (form.street_map_yandex.value == "") {
				alert( "'.JText::_( "ВERROR_STREET", true ).'" );
			}
	
			else {
				submitform( pressbutton );
			}
		}
		//-->';
	
$document->addScriptDeclaration($script);
?>

<div class="col100">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'COM_MAPYANDEX_NEWYMAR' ); ?></legend>
				

		<table class="admintable">

		
			<tr>
					<td width="100" align="left" class="key">
				<label for="foobar">
					<?php echo JText::_( 'COM_MAPYANDEX_NAMEMARKER' ); ?>:
				</label>
			</td>
								<td width="100" align="left">
			 <input type="text" name="name_marker" id="keyword" value="" />
			</td>
			</tr>
			<tr>
					<td width="100" align="left" class="key">
				<label for="editmarker">
					<?php echo JText::_( 'COM_MAPYANDEX_NAMEMAP' ); ?>:
				</label>
			</td>
								<td width="100" align="left">
			 <?php echo $this->allmaps;?>
			</td>
			</tr>


						<tr>
			<td width="100" align="left" class="key">
				<label for="foobar">
					<?php echo JText::_( 'COM_MAPYANDEX_SEACRHMETHOD' ); ?>:
				</label>
			</td>
			<td align="left">

			<?php 
			
				$statecoord[] = JHTML::_('select.option','1', JText::_( 'По адресу' ) );
				$statecoord[] = JHTML::_('select.option','2', JText::_( 'По координатам' ) );
				echo JHTML::_('select.genericlist',  $statecoord, $name = 'yandexcoord', $attribs = null, $key = 'value', $text = 'text', $selected = 1, $idtag = false, $translate = false );
			?>
			</td>
		</tr>

		<tr class="dispadres" <?php echo $stylead;?>>
			<td width="100" align="left" class="key">
				<label for="foobar">
					<?php echo JText::_( 'COM_MAPYANDEX_CITY' ); ?>:
				</label>
			</td>
								<td width="100" align="left">
				 <input type="text" name="city_map_yandex" value="">
			</td>
		</tr>
			
			
		<tr class="dispadres" <?php echo $stylead;?>>
			<td width="100" align="left" class="key">
				<label for="foobar">
					<?php echo JText::_( 'COM_MAPYANDEX_STREET' ); ?>:
				</label>
			</td>
								<td width="100" align="left">
				 <input type="text" name="street_map_yandex" value="">
			</td>
		</tr>
		
			<tr class="dispcoords" <?php echo $stylecoo;?>>
					<td width="100" align="left" class="key">
				<label for="foobar">
					<?php echo JText::_( 'COM_MAPYANDEX_COORD' ); ?>:
				</label>
			</td>
								<td width="100" align="left">
				<?php
					// define modal options
					$modalOptions = array (
					'size' => array('x' => 500, 'y' => 500)
					
					);
					// load modal JavaScript
					JHTML::_('behavior.modal', 'a.modal', $modalOptions);
				?>
				<div style="display:inline" class="button2-left"><div class="image"><a href="<?php echo 'index.php?option=com_mapyandex&view=mapyandexajax&tmpl=component&cid[]='.$this->foobar->id; ?>" class="modal"
				rel = "{
						handler: 'iframe'
						
						}">
						<?php echo JText::_( 'COM_MAPYANDEX_OPENMODAL' ); ?>
						</a></div></div>
				
			</td>
			</tr>
			
			

			<tr class="dispcoords" <?php echo $stylecoo;?>>
					<td width="100" align="left" class="key">
				<label for="foobar">
					<?php echo JText::_( 'COM_MAPYANDEX_LNG' ); ?>:
				</label>
			</td>
								<td width="100" align="left">
				 <input type="text" id="latitude" name="latitude_map_yandex" value="">
				 <input type="text" id="longitude" name="longitude_map_yandex" value="">
			</td>
			</tr>
				<tr>
			<td width="100" align="left" class="key">
				<label for="foobar">
					<?php echo JText::_( 'COM_MAPYANDEX_TEXTMAR' ); ?>:
				</label>
			</td>
			<td align="left">

			<textarea name="misil" rows="10" cols="50"></textarea>

			</td>
		</tr>
				<tr>
			<td width="100" align="left" class="key">
				<label for="foobar">
					<?php echo JText::_( 'COM_MAPYANDEX_TEXTMARONCLICK' ); ?>:
				</label>
			</td>
			<td align="left">

			<textarea name="misilonclick" rows="10" cols="50"></textarea>

			</td>
		</tr>
		
			</table>
	</fieldset>

<div class="clr"></div>

 </div>
 </td>
 <td style="width:100%;" valign="top"><div class="col50" style="width:100%;">
          <fieldset class="adminform">
          <legend><?php echo JText::_('COM_MAPYANDEX_MARKERSETTINGS'); ?></legend>
<?php



		echo '<table cellspacing="3" cellpadding="0" border="0" style="background-color:white" class="table"><tbody><tr valign="top">
        <td align="center" width="" colname="col1">
          <b>Вид значка</b>
        </td>
        <td align="center" width="" colname="col2">
        
        </td>
        <td align="center" width="" colname="col3">
          <b>Вид значка</b>
        </td>
        <td align="center" width="" colname="col4">
       
        </td>
        <td align="center" width="" colname="col5">
          <b>Вид значка</b>
        </td>
        <td align="center" width="" colname="col6">
      
        </td>
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

if(($i % 3)==0) {
	echo '<tr valign="top">';
	}
    echo '<td align="center" width="" colname="col1">';
	echo JHTML::_('image', 'administrator/components/com_mapyandex/assets/images/deficon/'.$option[$i].'.png','','style="width:19px; height:20px; margin-bottom: 3px;"');
	
	echo '</td>';
if($option[$i]==$this->editmarker->deficon) {
	echo '<td align="center" width="" colname="col2"><input type="radio" checked value="'.$option[$i].'" id="deficon0" name="deficon" class="text_area"></td>'; 
}
else {
	echo '<td align="center" width="" colname="col2"><input type="radio" value="'.$option[$i].'" id="deficon0" name="deficon" class="text_area"></td>'; 
}
  


	}

		

		
echo '</tr><tr valign="top">
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
        </div></td>
 </tr>
	</table>
	</fieldset>
</div>
<div class="clr"></div>
<input type="submit" value="Создать метку">
<input type="hidden" name="option" value="com_mapyandex" />
<input type="hidden" name="id" value="" />
<input type="hidden" name="task" value="savenew" />
<input type="hidden" name="controller" value="mapyandexmetki" />
</form>



<?
}

$data = array(); // array for all user settings
?>

 <div id="YMapsID" style="height:<?php echo $this->foobar->height_map_yandex;?>px; width:<?php echo $this->foobar->width_map_yandex;?>px;"></div>

 <div style="width:<?php echo $this->foobar->width_map_yandex;?>px;<?php echo $margin;?>;text-align:right;margin-top:5px;clear:both; font-size:10px;"><a title="Карты от Яндекс" href="http://slyweb.ru/yandexmap/">Карты от Яндекс</a></div>

 <div style="height:50px;" class="clear"></div>
<?php


if ($this->foobar->where_text == 3) {
	echo $this->foobar->text_map_yandex;
}
?>