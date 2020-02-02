<?php
/*
 * @package Joomla 3.3.9
 * @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @component Yandex Map Component
 * @copyright Copyright (C) Aleksandr Ermakov www.slyweb.ru
 */
defined('_JEXEC') or die();
jimport( 'joomla.application.component.view' );
jimport( 'joomla.filesystem.file' );

class MapYandexViewMapyandexajaxdelete extends JViewLegacy
{
	/**
	 * Hellos view display method
	 * @return void
	 **/
	protected $success;
	protected $info;
	
	function display($tpl = null)
	{


		$params = JComponentHelper::getParams( 'com_mapyandex' );
		$mid = JRequest::getVar( 'mid', array(0), 'post', 'array' );
		//$foobar = $this->get('Delete');
		$marker = $this->get('Marker');
		$userimg = json_decode($marker[0]->userimg);
		
		
		$userimgarr = (array)$userimg;
		array_push($userimgarr,str_replace('_s','',$userimgarr['smallfile']));
		if(count((array)$userimg)>1) {
					$this->success = array();
					$this->info = array();
			$i = 0;		
			foreach($userimgarr as $file) {
				++$i;	
				$userpath = $params->get('userpathtoimg');
				
				if(empty($userpath)) {

					$userpath = '/images/mapyandex/yandexmarkerimg/';
				}

				if(JFile::exists($_SERVER['DOCUMENT_ROOT'].JURI::root(true).$userpath.$file)) {
					
					$jp = JPath::getPermissions($_SERVER['DOCUMENT_ROOT'].JURI::root(true).$userpath.$file);
					$ch = JPath::canChmod($_SERVER['DOCUMENT_ROOT'].JURI::root(true).$userpath.$file);
					
					$jp = str_split($jp, 3);

					
					if(strpos($jp[0],'w')) {
						
						if(JFile::delete($_SERVER['DOCUMENT_ROOT'].JURI::root(true).$userpath.$file)) {
							//1 ... 3 params json - success file delete 
							$this->success[] = 1;
							$this->info[] = $file;
						} else {
							//1 ... 3 params json - NOT success file delete 
							$this->success[] = 403;
							$this->info[] = $file;
						}
						
					} else {
						//2 you no perms
						$this->success[] = 2;
						$this->info[] = $file;
						break; 
					}
				} else {
					//1 ... 3 params json -  file NOT exsist 
					$this->success[] = 404;
					$this->info[] = $file;
					break; 
				
				}
				//4 params json - success sql query
				if($i == 3) {
					if($this->get('Delete')) {
						$this->success[] = 1;
					} else {
						$this->success[] = 0;
					}
					

					}
				}
			}
			
		
		//$this->assignRef('foobar', $foobar);
		
		parent::display($tpl);
	}
}
?>
