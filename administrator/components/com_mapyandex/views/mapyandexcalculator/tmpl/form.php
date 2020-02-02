<?php 
/*
 * @package Joomla 3.x
 * @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @component Yandex Map Component
 * @copyright Copyright (C) Aleksandr Ermakov www.slyweb.ru
 */
defined('_JEXEC') or die('Restricted access'); 
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
echo '<form action="index.php" method="post" name="adminForm" id="adminForm">';
echo '<div class="row-fluid">';
echo '<div class="span12 form-horizontal">';

$document = JFactory::getDocument();
$document->addScript('https://api-maps.yandex.ru/2.1/?lang=ru_RU&amp;apikey='.$this->params->get('key'));

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
$borderradius = '';

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
}
if($this->map->yandexborder == 1) {
	$border = 'border: 1px solid #'.$this->map->color_map_yandex.';';
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
	';
$document->addStyleDeclaration($style);

if(!$this->map->height_map_yandex) $this->map->height_map_yandex = '500';
if(!$this->map->width_map_yandex) $this->map->width_map_yandex = '500';

//всё шиворот на выворот
$valone = 'var valone = false;';



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
	

	 $element = "
				".$trafficControl."
				".$geolocationControl ."
				".$sputnik." 
				".$scale."
				".$search."";


$script ='	
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

			if(!valone) {
				valone = "Санкт-Петербург, пр. Невский, 100";
				$j("#city_map_yandex").val(valone);
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
					controls: ['.$element.']
				}
				);	
				calculator = new DeliveryCalculator(map, point);
				
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
									var delimetr = $j(".adminformlist #jform_map_calculator_delimetr").val();
									if(delimetr == 1) { 
										var distance = Math.round(router.getLength() / 1000);
										var d = \'км\';
									}	
									else {
										var distance = Math.round(router.getLength());
										var d = \'м\';
									}
									var cleardistance = Math.round(router.getLength());
									var currency = $j(".adminformlist #jform_map_calculator_currency").val();
									textcuur = $j(".adminformlist #jform_map_calculator_currency option[value="+currency+"]").text();
				
									
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
					pr = $j(".adminformlist #jform_map_price").val();
					min = $j(".adminformlist #jform_map_min_price").val();

					if(!pr) pr = 0;
					var cost = len * pr;
			 
					return cost < min && min || cost;
				};
			 
				$j("body").on("click",".imgdeleteroute", function(){
				$tr = $j(this).parent();
					var idmap = $j(this).attr("data-idmap");
					var id = $j(this).attr("rel");
			
					$j.ajax({
					url: "index.php?option=com_mapyandex&view=mapyandexajaxtask",
					data: {id:id,idmap:idmap},
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
		
				$j("body").on("click",".addnewroute", function(e){ 
				e.preventDefault();
				
				varnr = $j(\'.newroute\').length;
				if(varnr == 0) {
					var text = "Начало";
				} else if(varnr == 1) {
					var text = "Пункт "+varnr;
				} else {
					$j(".sort li:last").find("div:first").html("<label for=\"foobar\">Пункт "+(varnr-1)+"</label>");
					var text = "Конец пути";
				}
				
				var appnewroute = \'<li class="ui-state-default"><div width="100" align="left" ><label for="foobar">\'+text+\':</label></div><div width="100" align="left"><input type="text" name="name_route_yandex[]" class="newroute" size="100" value="" /></div><div class="imgdeleteroute">'.JHTML::_( 'image', 'administrator/components/com_mapyandex/assets/images/iconfalse.png', JText::_( 'COM_MAPYANDEX_DELETE_ROUTE_ITEM' )).'</div><div>'.JHTML::_( 'image', 'administrator/components/com_mapyandex/assets/images/icon-loading2.gif', JText::_( 'COM_MAPYANDEX_DELETE_ROUTE_ITEM' ),array('class'=>'imgyandexmaploader')).'</div><span class="ui-icon ui-icon-arrowthick-2-n-s"></span></li>\';
				
				if(varnr == 0)  { 
					$j(".sort").append(appnewroute);
				} else {
					$j(".sort li:last").after(appnewroute);
				}
				
			});		
			<!--
	Joomla.submitbutton = function(task)
	{
			if (task == "mapyandexroute.cancel") {
				submitform( task );
				return;
			}
			var serialize = $j(\'.newroute\').serialize();
		
			$j("#adminForm").append(\'<input type="hidden" name="allroutemap">\');
			$j(\'#adminForm input[name="allroutemap"]\').val(serialize);
			
			submitform( task );
	}
		//-->	

	
});';
	
$document->addScriptDeclaration($script);
?>
<div class="span12">

<div class="span8 fltlft">
		<fieldset class="adminform">
			<legend><?php echo empty($this->item->id) ? JText::_('COM_MAPYANDEX_CALC_SETTINGS') : JText::sprintf('COM_MAPYANDEX_CALC', $this->item->id); ?></legend>

<p><?php JFactory::getApplication()->enqueueMessage(JText::_('COM_MAPYANDEX_CALC_DEFINFO'));?></p>
<div class="span5 fltlft">
		<table class="admintable" style="width: 100%;">

					<tr>
					<td align="left">
				<label for="foobar">
					<?php echo JText::_( 'COM_MAPYANDEX_NAMEMAP' ); ?>:
				</label>

			 <input type="text" name="name_map_yandex" id="keyword" value="<?php echo $this->map->name_map_yandex;?>" />
			</td>
			</tr>
		
							<tr>
					<td align="left">
				<label for="foobar">
					<?php echo JText::_( 'COM_MAPYANDEX_YMZOOM' ); ?>:
				</label>

			<?php 
				for($i=0;$i<18;$i++) {
					$statez[] = JHTML::_('select.option',$i, JText::_( $i ) );
				
				}
				echo JHTML::_('select.genericlist',  $statez, $name = 'yandexzoom', $attribs = null, $key = 'value', $text = 'text', $selected = $this->map->yandexzoom, $idtag = false, $translate = false );
			?>
			</tr>
			<tr class="elyandex">
				<td align="left">
					<label for="foobar">
						<?php echo JText::_( 'COM_MAPYANDEX_WHATEL' ); ?>:
					</label>
	
				<?php 
					$el = array(1,2,3,4,5);
					$attribs	= 'multiple="multiple"';
					$statem[] = JHTML::_('select.option','1', JText::_( 'COM_MAPYANDEX_SCALE' ) );
					$statem[] = JHTML::_('select.option','2', JText::_( 'COM_MAPYANDEX_MINIMAP' ) );
					$statem[] = JHTML::_('select.option','3', JText::_( 'COM_MAPYANDEX_SPUTNIC' ) );
					$statem[] = JHTML::_('select.option','4', JText::_( 'COM_MAPYANDEX_SEARCH' ) );
					$statem[] = JHTML::_('select.option','5', JText::_( 'COM_MAPYANDEX_ZOOM' ) );
					
				
					
					echo JHTML::_('select.genericlist',  $statem, $name = 'yandexel[]', $attribs, $key = 'value', $text = 'text', $selected = $el, $idtag = false, $translate = false );
				?>
				</td>
			</tr>
														<tr>
																	
					<td align="left" >
				<label for="foobar">
					<?php echo JText::_( 'COM_MAPYANDEX_WIDTHMAP' ); ?>:
				</label>
	
				 <input type="text" name="width_map_yandex" value="<?php echo $this->map->width_map_yandex;?>">
			</td>
			</tr>
																	<tr>
					<td align="left" >
				<label for="foobar">
					<?php echo JText::_( 'COM_MAPYANDEX_HEIGHTMAP' ); ?>:
				</label>

				 <input type="text" name="height_map_yandex" value="<?php echo $this->map->height_map_yandex;?>">
			</td>
			</tr>
			</table>
			</div>
			<div class="span5 fltlft">
					<table class="admintable" style="width:100%;">
		

			
			
					<tr class="dispadres" <?php echo $stylead;?>>
			<td align="left" >
				<label for="foobar">
					<?php echo JText::_( 'COM_MAPYANDEX_ADDRESS' ); ?>:
				</label>
	
				 <input type="text" size="50" id="city_map_yandex" name="city_map_yandex" value="<?php echo $this->map->city_map_yandex;?>">
			</td>
		</tr>
		
		<tr class="elyandex">
				<td align="left">
					<label for="foobar">
						<?php echo JText::_( 'COM_MAPYANDEX_DEFAULT_TYPE_MAP' ); ?>:
					</label>
	
				<?php 
					$attribs	= '';
					$defmap = array();
					$defmap[] = JHTML::_('select.option','1', JText::_( 'COM_MAPYANDEX_DEFAULT_TYPE_SCHEME' ) );
					$defmap[] = JHTML::_('select.option','2', JText::_( 'COM_MAPYANDEX_SPUTNIC' ) );
					$defmap[] = JHTML::_('select.option','3', JText::_( 'COM_MAPYANDEX_DEFAULT_TYPE_GYBRID' ) );
					$defmap[] = JHTML::_('select.option','4', JText::_( 'COM_MAPYANDEX_DEFAULT_TYPE_PEOPLE' ) );
					$defmap[] = JHTML::_('select.option','5', JText::_( 'COM_MAPYANDEX_DEFAULT_TYPE_SPUTNIC_PEOPLE' ) );

				
					
					echo JHTML::_('select.genericlist',  $defmap, $name = 'defaultmap[]', $attribs, $key = 'value', $text = 'text', $selected = $this->map->defaultmap, $idtag = false, $translate = false );
				?>
				</td>
		</tr>
	</table>
</div>

				</fieldset>
</div>

	
<div class="span3 fltlft">
          <fieldset class="adminform">
          <legend><?php echo JText::_('COM_MAPYANDEX_TARIF_SETTINGS'); ?></legend>


		<ul class="adminformlist">
			<?php 

			foreach($this->form->getFieldset('map_calculator_settings') as $field) {
				echo '<li>';
				if (!$field->hidden) {
					echo $field->label;
				}
				echo $field->input;
				echo '</li>';
			} ?>

			</ul>

          </fieldset>
        </div>


	
	<div class="clr" style="clear:both;"></div>
<div class="span12">
          <fieldset class="adminform">
          <legend><?php echo JText::_('COM_MAPYANDEX_CALCULATOR_LEGEND'); ?></legend>
	

		<div id="YMapsID" style="height:500px; width:100%;"></div>
	
		<div id="info"></div>

				</fieldset>
</div>
		
<div class="clr"></div>

<input type="hidden" name="option" value="com_mapyandex" />
<input type="hidden" name="id" value="<?php echo $this->map->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="map_type" value="calculator" />
<input type="hidden" name="controller" value="mapyandexcalculator" />
<input type="hidden" name="view" value="mapyandexcalculator" />
</form>