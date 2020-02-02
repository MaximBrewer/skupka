<?php
/*
 * @package Joomla 1.5
 * @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @component Yandex Map Component
 * @copyright Copyright (C) Aleksandr Ermakov www.slyweb.ru
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
// import the JView class
jimport('joomla.application.component.view');
/**
* Foobar View
*/
class mapyandexViewmapajax extends JViewLegacy
{
	/**
	 * Hellos view display method
	 * @return void
	 **/
	function display($tpl = null)
	{
		$app = JFactory::getApplication(); 

		
		$tmpl = array();
		
		$this->params = $app->getParams();
		

		$this->foobar = $this->get('Foobar');


		if ($foobar[0]->id_map_yandex !== '') {
			$foobar[0]->id_map_yandex = $foobar[0]->id_map_yandex;
		} else  {
			$foobar[0]->id_map_yandex = $tmpl['apikey'];
		}

		
		// interrogate the model

	
		$this->metka = $this->get('Metka');
	
		parent::display($tpl);
	}
	
	
	function addRouteToMap($route)
	{
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
			($this->foobar->map_centering) ? $map_centering = 'true' : $map_centering = 'false'; 
			$ymaproute = 'ymaps.route([
							'.$textarray.'
						], {
							// Опции маршрутизатора
							mapStateAutoApply: '.$map_centering.' // автоматически позиционировать карту
						}).then(function (route) {
							map.geoObjects.add(route);
							
							   var points = route.getWayPoints();  
							// Задаем стиль метки - иконки будут красного цвета, и
							// их изображения будут растягиваться под контент
							
							points.options.set(\'preset\', \'twirl#redStretchyIcon\');
							route.options.set({ strokeColor: \''.$this->foobar->color_map_route.'\', opacity: '.$this->foobar->map_route_opacity.' });
							points.get(0).properties.set(\'iconContent\', \'Точка отправления\');
							for(i = 0; i <='.($length).';i++) {
								if(i == '.($length).') {		
									points.get('.($length).').properties.set(\'iconContent\', \'Точка прибытия\');
								}
							}
						}, function (error) {
							alert("Возникла ошибка: " + error.message);
						});';
		} 
		return $ymaproute;
	}
}

?>
