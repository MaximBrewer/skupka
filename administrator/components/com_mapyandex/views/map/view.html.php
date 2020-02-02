<?php
/*
 * @package Joomla 3.x
 * @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @component Yandex Map Component
 * @copyright Copyright (C) Aleksandr Ermakov www.slyweb.ru
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );

/**
 * MapYandexViewMap View
 *
 * @package    Joomla.Tutorials
 * @subpackage Components
 */
class MapYandexViewMap extends JViewLegacy
{

	protected $state;
	protected $item;
	protected $form;
	protected $tmpl;

	function untextnl($text)
	{
		$text = str_replace("<br />","\r\n",$text);
		return $text;
	}
	
	function display($tpl = null)
	{
		//get the hello
		$foobar		= $this->get('Foobar');
		$isNew		= ($foobar->id < 1);

		$this->form		= $this->get('Form');
		
		$tmpl = array();
		$this->params = JComponentHelper::getParams( 'com_mapyandex' );
		JHtml::stylesheet( 'administrator/components/com_mapyandex/assets/mapyandex.css' );
		// prepare the cSS
			$css = '.icon-48-mapyandexe {
				background: url("'.JURI::root(true).'/media/com_mapyandex/colorpicker/images/icon-48-mapyandexe.png") 0 0 no-repeat;
			}';
			
			// add the CSS to the document
		$doc = JFactory::getDocument();
		$doc->addStyleDeclaration($css);


		JToolBarHelper::title(   JText::_( 'COM_MAPYANDEX_NAMECOMPONENT' ), 'mapyandexe' );
		JToolBarHelper::apply('apply', 'JTOOLBAR_APPLY');
		JToolBarHelper::save('save', 'JTOOLBAR_SAVE');
		if ($isNew)  {
			JToolBarHelper::cancel();
		} else {
			// for existing items the button is renamed `close`
			JToolBarHelper::cancel( 'cancel', 'Close' );
		}

		$this->map = $this->get('Foobar');
		
			$this->route = MapYandexHelper::addRouteToMap($this->map);
			$this->regions = MapYandexHelper::addRegionsToMap($this->map);
		$this->form->bind($this->map);

		$this->assignRef( 'tmpl', $tmpl );
		
		$this->metka = $this->get('Metka');
		
		
		parent::display($tpl);
	}
	
	
		
	function addRegionsToMap($region)
	{
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
		return $ymapregion;
	}	
	

}
