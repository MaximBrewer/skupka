<?php

if(!defined('DS')){
	define('DS',DIRECTORY_SEPARATOR);
}
class updateTable
{
	function __construct($table,$key,$keyVal=NULL){

		$this->params=JComponentHelper::getParams('com_excel2vm');

		$debug=$this->params->get('db_debug',0);
		$this->conf=new stdClass;
		$this->conf->sql_protect=$this->params->get('sql_protect',0);
		/*if($debug){
			require_once (dirname(__FILE__) . DS . "db_debug.php");
			$full_debug=JRequest::getVar('full_debug', 'cookie', 0, 'int');
			$this->db=new JDatabaseMySQLbak($full_debug,$full_debug);
		}
		else{*/
			@$this->db= JFactory::getDBO();
				$this->db->debug($debug);

		$this->conf->table=$table;
		$this->conf->key=$key;
		$this->db->setQuery("REPAIR TABLE `{$table}`");
		$this->db->query();
		$fields=$this->db->getTableColumns($table);
		foreach($fields as $name=>$type)
			$this->$name=$name!=$this->conf->key?NULL:$keyVal;
	}
	function __destruct(){
		unset($this->db);
	}

	function show(){
	   $obj=new stdClass();
	   $array=get_object_vars($this);
	   foreach ($array as $name =>$val){
			if($name!='conf' AND $name!='db'){
				$obj->$name= $val;
			}
	   }
	   echo '<pre>';
	   print_r($obj);
	   echo '</pre>';
	}

	
	
	function bind($array){

		$params=JComponentHelper::getParams('com_excel2vm');
		$custom_clear = $params->get('custom_clear','-');
		$array=is_object($array)?get_object_vars($array):$array;
		if(!is_array($array))return;
		foreach ($array as $name =>$val){
			if($this->conf->sql_protect){
				$this->check_sql_injection($val);
			}

			if(property_exists($this,$name)){
				if(trim($val)==$custom_clear)
					$val='';
				$this->$name=trim($val);
			}
		}


	}
				function update($updateNull=false){
		$this->db->updateObject( $this->conf->table, $this, $this->conf->key, $updateNull);
		if($this->db->getErrorMsg()) echo $this->db->getErrorMsg()."<br><br>";
		return $this->db->getAffectedRows();
	}
			function insert(){
		$fields = array();
		$values = array();

				$statement = 'INSERT INTO `'.$this->conf->table.'` (%s) VALUES (%s)';

				foreach (get_object_vars($this) as $k => $v)
		{
						if (is_array($v) or is_object($v) or $v === null) {
				continue;
			}

						if ($k[0] == '_') {
				continue;
			}

						$fields[] = '`'.$k.'`';
			$values[] = $this->db->quote($v);
		}

				$this->db->setQuery(sprintf($statement, implode(',', $fields),  implode(',', $values)));
		if (!$this->db->query()) {
			if($this->db->getErrorNum()==126 OR $this->db->getErrorNum()==145){
			   $this->db->setQuery("REPAIR TABLE `{$this->conf->table}`");
			   $this->db->query();
			   $this->db->setQuery(sprintf($statement, implode(',', $fields),  implode(',', $values)));
			   if(!$this->db->query())return false;
			}

		}

				$id = $this->db->insertid();
		if (@$key && @$id) {
			$this->{$this->conf->key} = $id;
		}
		if($this->db->getErrorMsg()) echo $this->db->getErrorMsg()."<br><br>";
		return $id;
	}

	function save(){
		if($this->{$this->conf->key}){
			$this->db->setQuery( "SELECT {$this->conf->key} FROM {$this->conf->table} WHERE {$this->conf->key} = '{$this->{$this->conf->key}}'");
			if($this->db->loadResult())
				return $this->update();
			else
				return $this->insert();
		}
		else
			return $this->insert();
	}

	function delete($cid=NULL,$key=NULL){
		$cid = $cid?(array)$cid:JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$k=$key?$key:$this->conf->key;
		$cid=implode(',',(array)$cid);
		$this->db->setQuery("DELETE FROM {$this->conf->table} WHERE $k IN($cid)");
		if(!$this->db->Query())
			JError::raiseError(500, $this->db->getErrorMsg());
		return "Удаленo: ".$this->db->getAffectedRows();
	}

		function load($keyVal=NULL,$key=NULL){
		$key=($key!==NULL)?$key:$this->conf->key;
		$keyVal=($keyVal!==NULL)?$keyVal:$this->$key;

		if ($keyVal === null)
			return false;

		$this->db->setQuery( "SELECT * FROM {$this->conf->table} WHERE {$key} = '{$keyVal}'");

		if ($result = $this->db->loadAssoc())
			return $this->bind($result);
		else
		{
			JError::raiseError(500, $this->db->getErrorMsg() );
			return false;
		}
	}


		function reset($all=0){
		foreach (get_object_vars($this) as $name => $value)
		{
			if($name != $this->conf->key AND $name !='db' AND $name !='conf' OR ($name == $this->conf->key AND $all))
			{
				$this->$name	= NULL;
			}
		}
	}

	function dateFormat($property_name,$format){
		if(!$property_name or !$format)
			return false;
		@$time = strtotime($this->$property_name);
		if($format=='mysql')
			$this->$property_name=date("Y-m-d H:i:s",$time);
		else
			$this->$property_name=date($format,$time);
	}

	function check_sql_injection($data){
		$data=strtolower($data);
		$data=str_replace("/*","",$data);
		$data=str_replace("*/","",$data);
		$restricted=array('union','extractvalue','information_schema','database','substring','between','ascii');
		foreach($restricted as $word){
			if(strstr($data,$word)){
				echo "Зафиксирована попытка sql-инъекции:<br>$data";
				file_put_contents(JPATH_ROOT.DS.'components'.DS.'com_excel2vm'.DS.'error.txt',date("Y-m-d H:i:s",time())." - Зафиксирована попытка sql-инъекции - $data",FILE_APPEND);
				exit();
			}
		}
	}
}

class core{
   function __construct($cron=false){
	 $this->_db = JFactory::getDBO();
	 $this->config_table = new updateTable("#__excel2vm", "id", 1);
	 $this->cron=$cron;
	 $this->params=JComponentHelper::getParams('com_excel2vm');
   }

   function getConfig(){
		$app=JFactory::getApplication();
		try{
			$this->_db->setQuery("SELECT config FROM #__virtuemart_configs",0,1);
			$VmConfigTemp=explode(';|',$this->_db->loadResult());
			foreach($VmConfigTemp as $param){
					$temp=explode("=",$param);
					$key=$temp[0];
					$val=@unserialize($temp[1]);
					$VmConfig[$key]=$val;
			}
			$this->sef=$app->getCfg('sef');
			$this->sef_rewrite=$app->getCfg('sef_rewrite');
			$this->sef_suffix=@$VmConfig['seo_sufix'];
			$fields=$this->_db->getTableColumns('#__menu');
			if(array_key_exists('path',$fields))
				$this->_db->setQuery("SELECT `path`,id FROM #__menu WHERE link = 'index.php?option=com_virtuemart&view=virtuemart'");
			else
				$this->_db->setQuery("SELECT alias as path,id FROM #__menu WHERE link = 'index.php?option=com_virtuemart'");
			$this->item_id=$this->_db->loadObject();
		}
		catch(Exception $e){
		   JError::raiseWarning('',"Таблица #__virtuemart_configs не найдена");
		}


		$id=0;
		if($this->cron){
			$id=$this->params->get($this->cron);
			$custom_profile=JRequest::getVar('profile', '', 'get', 'string');
			if($custom_profile){
				$this->_db->setQuery("SELECT id FROM #__excel2vm WHERE profile=".$this->_db->Quote(urldecode($custom_profile) )."");
				$id=$this->_db->loadResult();
				if(!$id){
				   $fp = fopen( dirname(__FILE__).DS."cron_log.txt" , "a" );
				   fwrite($fp, date("Y-m-d H:i:s")." - Профиль '$custom_profile' не найден. Убедитесь,&nbsp;что название профиля указано верно. Желательно использовать название на английском и без спец. символов\r\n");
				   fclose($fp);
				}
			}
		}
		if(@$this->cron_yml){
			switch($this->cron_yml) {
				  case 'import':
					   $id=$this->params->get('cron_yml_import');
				  break;
				  case 'export':
					   $id=$this->params->get('cron_yml_export');
				  break;
				}

		}
		if(!$id){
			$this->_db->setQuery("SELECT id FROM #__excel2vm WHERE default_profile = 1");
			$id=$this->_db->loadResult();
		}

		if(!$id){
			$this->_db->setQuery("UPDATE #__excel2vm SET default_profile = 1 LIMIT 1");
			$this->_db->Query();
			$this->config_table->load(1,'default_profile');
		}
		else
			$this->config_table->load($id);

		$this->profile=$id;
		$this->active_fields=$this->config_table->active;
		$config=unserialize($this->config_table->config);
		$config->profile_name=$this->config_table->profile;
		$config->profile_id=$this->config_table->id;
		$config->sufix=str_replace("-","_",strtolower($config->languege));
		if(!$config->sufix){
		   $config->sufix="ru_ru";
		}

		$config->currency_rate=(float)str_replace(",",".",@$config->currency_rate);
		if(!$config->currency_rate){
			$config->currency_rate=1;
		}
		if(!isset($config->is_update)){
			$config->is_update=1;
		}
		if(!@$config->level_delimiter){
			$config->level_delimiter="\\";
		}
		if(!@$config->category_delimiter){
			$config->category_delimiter="|";
		}

		$this->_db->setQuery("SELECT count(*) FROM information_schema.tables WHERE TABLE_NAME = '".$this->_db->getPrefix()."virtuemart_categories_{$config->sufix}'");
		if(!$this->_db->loadResult()){
			 $this->_db->setQuery("SELECT TABLE_NAME FROM information_schema.tables WHERE TABLE_NAME LIKE '".$this->_db->getPrefix()."virtuemart_categories_%'",0,1);
			 $new_table=$this->_db->loadResult();
			 $new_sufix=substr($new_table,-5);
			 $temp=explode("_",$new_sufix);
			 $temp[1]=ucfirst($temp[1]);
			 $config->languege=implode("-",$temp);
			 JError::raiseWarning('',"Таблица ".$this->_db->getPrefix()."virtuemart_categories_{$config->sufix} не найдена. Язык в настройках переключен на - $config->languege");
			 $config->sufix=$new_sufix;
			 $this->config_table->config= serialize($config);
			 $this->config_table->update();
		}

		$config->cat_id_col_original=@$config->cat_id_col;
		return $config;
	}

	function get_last_version(){
		$xml=JFactory::getXML(JPATH_ADMINISTRATOR .DS.'components'.DS.'com_excel2vm'.DS.'excel2vm.xml');
		return json_encode(array('new_version' => (string)$xml->version, 'my_version' => (string)$xml->version, 'description' => ''));//29.12.2016 20:21 denver
		

		$url=(string)$xml->updateservers->server;
		$cache =  JFactory::getCache();
		$cache->setLifeTime(60);
		$cache->setCaching(1);
		$data=$cache->call( array( 'core', 'get_update_xml' ),$url);

		@$data->my_version=(string)$xml->version;

		return $data;
	}
/*
	static function get_update_xml($url){
		if(!function_exists("curl_init")){
			echo "Внимание! На вашем сервере отключена библиотека cURL, необходимая для работы компонента. Вам необходимо обратиться в тех. поддержку хостинга с просьбой включить библиотеку cURL.";
		   exit();
		}

		$ch=curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_REFERER, "http://google.com/");
		curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
		$file_data=curl_exec($ch);
		$error=curl_error($ch);
		curl_close($ch);
		if(!$file_data){
			return false;
		}
		$update_xml=JFactory::getXML($file_data,false);
		@$data->new_version=(string)$update_xml->update[0]->version;
		@$data->description=(string)$update_xml->update[0]->description;
		return $data;
	}
*/
}
