<?php

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');
jimport( 'joomla.filesystem.file' );
jimport( 'joomla.filesystem.folder' );


class Excel2vmModelSupport extends JModelLegacy {
	public $pagination;

	function __construct() {
		parent :: __construct();
		$this->_db->debug(0);
        $this->order_id=$this->getOrderId();
	}

/*
    function getChangeList(){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://php-programmist.ru/excel2vm/versions.php?id=7');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 2);
        curl_setopt($ch, CURLOPT_REFERER, "http://google.com/");
        curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
        $file_data=curl_exec($ch);
        $error=curl_error($ch);
        curl_close($ch);
        if($file_data){
            return $file_data;
        }
        else{
            return $error;
        }
    }
*/
	function getMyVersion(){
        $xml=JFactory::getXML(JPATH_ADMINISTRATOR .DS.'components'.DS.'com_excel2vm'.DS.'excel2vm.xml');
        $version=(string)$xml->version;
		return $version;
	}
    function getOrderId(){
		$xml=JFactory::getXML(JPATH_ADMINISTRATOR .DS.'components'.DS.'com_excel2vm'.DS.'excel2vm.xml');
        $version=(string)$xml->updateservers->server;
        $version=substr($version,64,-4);
        if($version=='{update_data}' AND strstr($_SERVER['SERVER_NAME'],'demo-zone.ru')){
            return 191;
        }
        $temp=explode(":",$version);
        $version=@$temp[0];
		return $version;
	}
/*
    function getData(){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://php-programmist.ru/excel2vm/check.php?id='.$this->order_id.'&domain='.$_SERVER['SERVER_NAME']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 2);
        curl_setopt($ch, CURLOPT_REFERER, "http://google.com/");
        curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
        $json=curl_exec($ch);
        $error=curl_error($ch);
        curl_close($ch);
        if(!$json){
            @$json->support="Невозможно получить данные";
            @$json->version="Невозможно получить данные";
            echo $error;
        }
        else{
            return json_decode($json);
        }
    }

    function send_message(){
        JRequest::checkToken() or jexit('Invalid Token');
        $message=JRequest::getVar('message', '', '', 'string');
        if(!$message){
          JError::raiseWarning('',"Вы не указали текст сообщения");
          return false;
        }
        $out='';
        $t = explode('/', JURI :: root());
        $d = $t[2];
		if(substr($d,0,4)=='www.')$d=substr($d,4);

        $data=array();
        $data['message']=$message;
        $data['domain']=$d;
        $data['id']=$this->getOrderId();
        $data['max_execution_time']=ini_get('max_execution_time');
        $data['memory_limit']=ini_get('memory_limit');
        $data['post_max_size']=ini_get('post_max_size');
        $data['upload_max_filesize']=ini_get('upload_max_filesize');
        $data['display_errors']=ini_get('display_errors');
        $data['joomla_version']=JVERSION;
        $data['component_version']=$this->getMyVersion();


        $xml=JFactory::getXML(JPATH_ADMINISTRATOR .DS.'components'.DS.'com_virtuemart'.DS.'virtuemart.xml');
        $data['vm_version']=(string)$xml->version;;
        $data['log']=JURI::root()."components/com_excel2vm/error.txt";
        $data['price']=JURI::root()."administrator/components/com_excel2vm/xls/".$this->getLastFile();
        if($this->export_profile()){
             $data['profile']=JURI::root()."components/com_excel2vm/profile.txt";
        }

        if( $curl = curl_init() ) {
          curl_setopt($curl, CURLOPT_URL, 'http://php-programmist.ru/excel2vm/support.php');
          curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
          curl_setopt($curl, CURLOPT_POST, true);
          curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
          $out = curl_exec($curl);
          curl_close($curl);
        }
        else{
            JError::raiseWarning('',"Для отправки сообщения на Вашем сайте должен быть включен cURL. Обратитесь в тех. поддержку хостинга, чтобы они активировали cURL");
        }

        return $out;
    }

    function update(){
      JRequest::checkToken() or jexit('Invalid Token');
      jimport('joomla.updater.update');

        $xml=JFactory::getXML(JPATH_ADMINISTRATOR .DS.'components'.DS.'com_excel2vm'.DS.'excel2vm.xml');
        $data=(string)$xml->updateservers->server;
        $data=substr($data,64,-4);

        $url=sprintf("http://php-programmist.ru/updates/%s:from-excel-to-virtuemart2.zip",$data);

        $p_file = JInstallerHelper::downloadPackage($url);

        if (!$p_file) {
			JError::raiseWarning('',"Невозможно скачать обновление. Возможно, необходимо продлить срок подписки");
			return false;
		}

        $config		= JFactory::getConfig();
		$tmp_dest	= $config->get('tmp_path');

				$package	= JInstallerHelper::unpack($tmp_dest . '/' . $p_file);

				$installer	= JInstaller::getInstance();

        if (!$installer->update($package['dir'])) {
			$msg = "Возникла ошибка во время установки обновления. Возможно, необходимо продлить срок подписки";
            JError::raiseWarning('',$msg);
			$result = false;
		} else {
			$msg = "Обновление успешно установлено";
			$result = true;
		}
        if (!is_file($package['packagefile'])) {
			$config = JFactory::getConfig();
			$package['packagefile'] = $config->get('tmp_path') . '/' . $package['packagefile'];
		}

		JInstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);


        if($result)
              return $msg;
    }
*/

    function getLastFile(){

        $path=JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'xls'.DS;
        $files=JFolder::files($path);
        $new_array=array();
        foreach($files as $file){
           $ext=pathinfo($file, PATHINFO_EXTENSION);
           if(!in_array($ext,array('xls','xlsx','csv'))){
                continue;
           }
           $time=filemtime($path.$file);
           $new_array[$time]=$file;
        }
        krsort($new_array);
        return array_shift($new_array);
    }

    function export_profile(){
		require(dirname(__FILE__).DS.'config.php');
        $model=new Excel2vmModelConfig();
        return $model->export_profile(true);
	}
}
