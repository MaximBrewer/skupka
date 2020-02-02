<?php
/*
 * @package Joomla 1.5
 * @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @component Yandex Map Component
 * @copyright Copyright (C) Aleksandr Ermakov www.slyweb.ru
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );
jimport( 'joomla.html.pagination' );
mapyandeximport('mapyandex.wideimage.wideimage');
jimport('joomla.client.ftp');
class MapYandexModelloadimg extends JModelLegacy
{
	
	var $_foobar;
	var $_editmarker;
	
function __construct()
{

	parent::__construct();
	// get the cid array from the default request hash
	$id = JRequest::getVar('id',1);
	


	$this->setId($id);
}


	/**
	* ќбновление ID и данных
	*
	* @param int foobar ID
	*/

	function setId($id)
	{
		$this->_id = $id;
		$this->_foobar = null;
	}

	

	
	function getEditmarker()
	{
	
	global $option, $mainframe;
	$db =& JFactory::getDBO();
		$cid = JRequest::getVar('cid');

		$query = ' SELECT * '
			. ' FROM #__map_yandex_metki WHERE id='.$this->_id;
			$query = $db->setQuery( $query);
		$this->_editmarker = $db->loadObjectList();

		return $this->_editmarker;
	}

	/**
	 * ¬ывод мудрых мыслей с пагинацией
	 * @возвращает список строк в файл view.html.php
	 */
	function getFoobar()
	{
	
	global $option, $mainframe;
	$app = JFactory::getApplication(); 
	
	$db =& JFactory::getDBO();
	//вставл€ем насройки последней карты в новую...
	$data['task'] = JRequest::getVar('task');
	if($data['task'] !== 'add') {

	$limit = JRequest::getVar('limit',
		$app->getCfg('list_limit'));
		$limitstart = JRequest::getVar('limitstart', 0);
		$query = "SELECT * FROM #__map_yandex_metki";
		$query = $db->setQuery( $query, $limitstart, $limit );
		$this->_foobar = $db->loadObjectList();
		
	}
	else {
	
		$query = ' SELECT * '
			. ' FROM #__map_yandex_metki ORDER BY ID DESC';
			$query = $db->setQuery( $query);
		$this->_foobar = $db->loadObjectList();
		}
		return $this->_foobar;
	}

	/**
	 * ¬ывод мудрых мыслей с пагинацией
	 * @возвращает список строк в файл view.html.php
	 */

	
	function getReviews()
	{
		global $option, $mainframe;
		$app = JFactory::getApplication(); 
		$limit = JRequest::getVar('limit',
		$app->getCfg('list_limit'));
		
		$limitstart = JRequest::getVar('limitstart', 0);
		$db =& JFactory::getDBO();
		$query = "SELECT count(*) FROM #__map_yandex_metki";
		$db->setQuery( $query );
		$total = $db->loadResult();
		$query = "SELECT * FROM #__map_yandex_metki";
		$db->setQuery( $query, $limitstart, $limit );
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}

		return $this->pageNav = new JPagination($total, $limitstart, $limit);
		
	}
	
	
	function textnl($text)
	{
		$text = str_replace("\r\n","<br />",$text);
		$text = str_replace("\r","<br />",$text);
		$text = str_replace("\n\n", '<p>',$text);
		$text = str_replace("\n", '<br />',$text); 
		return addslashes($text);
	}



    /**
     * Proxy convenience method
     *
     * @return bool
     */
	 
    public function getLoadFile()
    {
	

		$firstfilenm = $_FILES['userVideo']['name'];
		$albumhidden = $_POST['sid'];
		

		
		$uniqid = uniqid();
		

	

		/* create item */
		$scriptProperties['active'] = !empty($scriptProperties['active']) ? 1 : 0;

		$arr = explode('.',$firstfilenm);


		$filenm = $firstfilenm;

		/* Upload */
	
		$params = JComponentHelper::getParams( 'com_mapyandex' );
		
		$userpath = $params->get('userpathtoimg');
		
		if(!$userpath) {
			$userpath = '/media/com_mapyandex/yandexmarkerimg/';
		}
		
		$targetDir = $_SERVER['DOCUMENT_ROOT'].JURI::root(true).$userpath;
		
		
		/* if directory doesnt exist, create it */
		if (!file_exists($targetDir) || !is_dir($targetDir)) {
			
			mkdir($targetDir);
			
		}
		
		/* make sure directory is readable/writable */
		if (!is_readable($targetDir) || !is_writable($targetDir)) {

				
		
		}

		/* upload the file */
		$extension = end(explode('.', $filenm));
		$filename = $filenm;

		$absolutePath = $targetDir.$filename;

		if (@file_exists($absolutePath)) {
			@unlink($absolutePath);
		}
		
		if (!empty($_FILES['userVideo'])) {
			$explode = explode('/',$_FILES['userVideo']['type']);
		
			
			switch ($explode[1]) {
				case 'jpeg':
					break;
				case 'jpg':
					break;
				case 'gif':
					break;
				case 'png':
					break;
				default:
				echo '
				<script type="text/javascript">
				window.top.document.getElementById("file_type_error").style.display="block";
				window.top.document.getElementById("#avatar-buttons").style.display="block";</script>';
				die();
				
			}

			// если больше размером выводим сообщение
			if(filesize($_FILES['userVideo']['tmp_name'])/1024 > 10000) {
				echo '<script type="text/javascript">window.top.document.getElementById("file_size_error").style.display="block";</script>';
				die();
			}

			
			
			
			if (!is_writable($targetDir)) {
			
			

					
					$config = JFactory::getConfig($_SERVER['DOCUMENT_ROOT'].JURI::root(true).'configuration.php');
					$ftp = new JFtp();
					$ftp->connect($config->get('ftp_host'),$config->get('ftp_port'));
					
					if(!$ftp->login($config->get('ftp_user'),$config->get('ftp_pass'))) {
						echo '<script type="text/javascript">
							window.top.document.getElementById("derectorynoperms").style.display="block";
						</script>';
						return false;
						} else {
						if($ftp->isConnected()) {
							$ftp->chmod($config->get('ftp_root').$userpath,'0777');
							$ftp->store($_FILES['userVideo']['tmp_name'],$config->get('ftp_root').$userpath.$uniqid.'.'.$extension);
							$sf = $uniqid.'_s.'.$extension;
							$nf = $uniqid.'_n.'.$extension;
							
							if($extension == 'png') {
								$compress = 6;
							} else {
								$compress = 90;
							}

							mapyandexWideImage::load($targetDir.$uniqid.'.'.$extension)->resize(200,200,'inside')->crop('center', 'center', 100,100)->saveToFile($targetDir.$sf,$compress);
							mapyandexWideImage::load($targetDir.$uniqid.'.'.$extension)->resize(500,500,'inside')->crop('center', 'center', 500,500)->saveToFile($targetDir.$nf,$compress);
						}
					}
					$ftp->chmod($config->get('ftp_root').$userpath,'0755');
					$ftp->quit();
					
						
				
			} else {
					if(move_uploaded_file($_FILES['userVideo']['tmp_name'],$targetDir.$uniqid.'.'.$extension)) {
						$sf = $uniqid.'_s.'.$extension;
						$nf = $uniqid.'_n.'.$extension;
						
						if($extension == 'png') {
							$compress = 6;
						} else {
							$compress = 90;
						}

						mapyandexWideImage::load($targetDir.$uniqid.'.'.$extension)->resize(200,200,'inside')->crop('center', 'center', 100,100)->saveToFile($targetDir.$sf,$compress);
						mapyandexWideImage::load($targetDir.$uniqid.'.'.$extension)->resize(500,500,'inside')->crop('center', 'center', 500,500)->saveToFile($targetDir.$nf,$compress);
					}

				
			}
			
			
		} else {

		}

	

		$this->_img = array('startfile' => $nf,'smallfile' => $sf);
		
		return $this->_img;
	
	
    }

	function delete()
	{
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$db		=& JFactory::getDBO();
		if (count( $cids )) {
			foreach($cids as $cid) {
			$query = 'DELETE FROM #__map_yandex_metki'
			. ' WHERE id = ' .$cid;
				$db->setQuery( $query );
				if (!$db->query()) {
					JError::raiseWarning( 500, $db->getError() );
				}
			}
		}
		return true;
	}

}


