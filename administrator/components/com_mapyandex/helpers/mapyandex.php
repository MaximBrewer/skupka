<?php
/*
 * @package		Joomla.Framework
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
defined('_JEXEC') or die;
jimport('joomla.client.ftp');

class MapYandexHelper
{
	

	public static function getActions()
	{
		$user	= JFactory::getUser();
		$result	= new JObject;

		$assetName = 'com_mapyandex';

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action) {
			$result->set($action,	$user->authorise($action, $assetName));
		}

		return $result;
	}

	public static function canWrite()
	{
		$user	= JFactory::getUser();
		$result['perms'] = 0;
		$params = JComponentHelper::getParams( 'com_mapyandex' );	
				$userpath = $params->get('userpathtoimg');
				
				if(empty($userpath)) {

					$userpath = '/images/mapyandex/yandexmarkerimg/';
				}
			
				
				if(is_dir($_SERVER['DOCUMENT_ROOT'].JURI::root(true).$userpath)) {


	
					//var_dump($ftp->mkdir($config->get('ftp_root').$userpath.'555'));


					
					$jp = JPath::getPermissions($_SERVER['DOCUMENT_ROOT'].JURI::root(true).$userpath);
					$ch = JPath::canChmod($_SERVER['DOCUMENT_ROOT'].JURI::root(true).$userpath);
					$jp = str_split($jp, 3);
			
				
					if(!is_writable($_SERVER['DOCUMENT_ROOT'].JURI::root(true).$userpath)) {
						$result['notperms'] = JText::sprintf('COM_MAPYANDEX_NO_PERMS',$_SERVER['DOCUMENT_ROOT'].JURI::root(true).$userpath);
					}
				
					if(!$jp) {
						$result['notperms'] = JText::sprintf('COM_MAPYANDEX_NO_PERMS',$_SERVER['DOCUMENT_ROOT'].JURI::root(true).$userpath);
					}
					
					
					if(strpos($jp[0],'w') && is_writable($_SERVER['DOCUMENT_ROOT'].JURI::root(true).$userpath)) {
					
						$result['perms'] = 1;
						
						
					} else {
						
						$result['notperms'] = JText::sprintf('COM_MAPYANDEX_NO_PERMS',$_SERVER['DOCUMENT_ROOT'].JURI::root(true).$userpath);
						
					}
				} else {
					
					$result['notperms'] = JText::sprintf('COM_MAPYANDEX_NO_PERMS',$_SERVER['DOCUMENT_ROOT'].JURI::root(true).$userpath);
				
				}

				if(JRequest::getVar('task') == 'add') {
					if(!empty($result['notperms'])) {
						$config = JFactory::getConfig($_SERVER['DOCUMENT_ROOT'].JURI::root(true).'configuration.php');
						$ftp = new JFtp(array());
						$ftp->connect($config->get('ftp_host'),$config->get('ftp_port'));
						
						$l = $ftp->login($config->get('ftp_user'),$config->get('ftp_pass'));
						if($l && $ftp->isConnected()) {
							$result['isconnectedftp'] = $ftp->isConnected();
						} else {
							$result['isconnectedftp']= false;	
							$result['noftp'] = JText::sprintf('COM_MAPYANDEX_NO_FTP',$_SERVER['DOCUMENT_ROOT'].JURI::root(true).$userpath);
						}
						
						$ftp->quit();

					}
				}

		return $result;
	}
	
		
		
	function addRegionsToMap($map)
	{
			
		$region = json_decode($map->region_map_yandex);
		$map_region_style = array();
		$map_region_style[1] = '#0000FF';
		$region_border_color = '#FFFF00';
		$map_region_style = json_decode($map->map_region_style);
		if(!is_array($map_region_style) || empty($map_region_style)) {
		$styleoption = '											
				strokeWidth: 6,
				strokeColor: \''.$map_region_style[1].'\', // синий
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
		if($region) {

			$gi = -1;
			$length = count($region)-1;
			$ymapregion = '
			myGeoobject = [];';
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
									myGeoobject['.($gi+1).'].events.add(\'geometrychange\', function (event) {
										printGeometry(myGeoobject['.($gi+1).'].geometry.getCoordinates(),'.($gi+1).');
									});

									// Размещаем геообъект на карте
									map.geoObjects.add(myGeoobject['.($gi+1).']);
									myGeoobject['.($gi+1).'].editor.startEditing();

								// Выводит массив координат геообъекта в <div id="geometry">
							
							';
			}
			$ymapregion .= '
				function printGeometry (coords,getinput) {
				   $j(\'#geometry\').html(\'Координаты: \' + stringify(coords));

					
					without = stringify(coords).replace(/\\]|\s/ig,"");
					without = without.replace(/\\[/ig,"");
					
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
				$j(".imgdeleteroute").live("click", function(){

					$tr = $j(this).parent();
					var idmap = $j(this).attr("data-region");
					var id = $j(this).attr("rel");
					
					map.geoObjects.remove(myGeoobject[id]);
			
					
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
				
				/*подсветка выбранных регионов*/
				$j("ul.ui-sortable .ui-state-default").mouseenter( function(e){
					e.preventDefault();
					var idmap = $j(this).find(".imgdeleteroute").attr("data-region");
					var id = $j(this).find(".imgdeleteroute").attr("rel");
					
					this.fillColor = myGeoobject[id].options.get("fillColor");
					myGeoobject[id].options.set("fillColor","#ccc");
					
					
				} ).mouseleave( function(e){
				
					e.preventDefault();
					var id = $j(this).find(".imgdeleteroute").attr("rel");
					myGeoobject[id].options.set("fillColor",this.fillColor);	
					

				} );
				


				$j(".adminformlist select").change(function(){
					var val = $j(this).val();

						myGeoobject.each(function (geoObject) {

						});

					

					

				});
				
				$j(\'input[name="color_map_region"]\').change(function(){
					var val = $j(this).val();

						myGeoobject.each(function (geoObject) {


						});

					

					

				});
			
';
		}
		return $ymapregion;
	}	
	
	
	public function textarrayForRegions($map)
	{
		$region = json_decode($map->region_map_yandex);
	
		
		if($region) {

			$gi = -1;
			$length = count($region)-1;
			$ymapregion = '
			myGeoobject = [];';
			foreach($region as $val) {
			$gi++;
						$textbefore = 'Регион № '.$gi;
						  
						  $textarrayonput  .= '<li class="ui-state-default"><div width="100" align="left" class="key"><label for="foobar">'.$textbefore.'</label></div><div width="100" align="left"><input type="text" class="acpro_inp_'.($gi+1).' newroute" name="name_region_yandex[]" size="100" value="'.$val.'" /></div><div class="imgdeleteroute" rel="'.($gi+1).'" data-region="'.$map->id.'">'.JHTML::_( 'image', 'administrator/components/com_mapyandex/assets/images/iconfalse.png', JText::_( 'COM_MAPYANDEX_DELETE_ROUTE_ITEM' )).'</div><div>'.JHTML::_( 'image', 'administrator/components/com_mapyandex/assets/images/icon-loading2.gif', JText::_( 'COM_MAPYANDEX_DELETE_ROUTE_ITEM' ),array('class'=>'imgyandexmaploader')).'</div><span class="ui-icon ui-icon-arrowthick-2-n-s"></span></li>';
			}		
			
			return $textarrayonput;
		}
	}

	public function textarrayoutput($map)
	{
		$textarrayonput	= '';
		$route = json_decode($map->route_map_yandex);
		if($route) {
			$i = -1;
			$length = count($route)-1;
			foreach($route as $val) {
			$i++;
				
						
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
			return $textarrayonput;
		}
	}

	
	public function addRouteToMap($map)
	{
		
		$textarray = '';
		$textarrayonput = ''; 
		$route = json_decode($map->route_map_yandex);

		
		$cr = count($route);
		if($route) {
			$i = -1;
			$length = count($route)-1;
			foreach($route as $val) {
			$i++;
						$textarray  .= '\''.$val.'\',';
						
	
							

			}
						if($textarray != '') {
							$textarray = substr($textarray, 0, -1);
						}
 
 
			$ymaproute = 'ymaps.route([
							'.$textarray.'
						], {
							// Опции маршрутизатора
		
						}).then(function (route) {
							map.geoObjects.add(route);
							
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
}
