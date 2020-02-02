<?php
/*
 * @package Joomla 1.5
 * @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @component Yandex Map Component
 * @copyright Copyright (C) Aleksandr Ermakov www.slyweb.ru
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );

class MapYandexViewmapyandexajaxtask extends JViewLegacy
{

	function display($tpl = null) {
			// Set up the data to be sent in the response.
			$data = array('some data');
			 
			$id = JRequest::getVar('id');
			$idregion = JRequest::getVar('idmapforregion');
			if($idregion) {
				$res = $this->get('Idregion');
				$allroute = $res->region_map_yandex;
			} else {
				$res = $this->get('Foobar');
				$allroute = $res->route_map_yandex;
			}
			
			
			
			$item = $this->foobar = json_decode($allroute);
			// Output the JSON data.
			if($id) {
				unset($item[$id]);
			}
			var_dump($item);
			$newroute = array();
			
			foreach($item as $val) {
				$newroute[] = $val;
			}
			
			$model = $this->getModel();
			if($idregion) {
				$greeting = $model->store($newroute,'region_map_yandex');
			} else {
				$greeting = $model->store($newroute,'route_map_yandex');
			
			}
			
			jexit();

	 }

}