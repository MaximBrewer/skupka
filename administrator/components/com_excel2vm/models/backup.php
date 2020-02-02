<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');
jimport('joomla.filesystem.file');
require_once (dirname(__FILE__).DS."updateTable.php");

class Excel2vmModelBackup extends JModelLegacy {
	public $pagination;

	function __construct() {
		parent :: __construct();
        $params = JComponentHelper :: getParams("com_excel2vm");
		$params->get('db_debug',0);
		$this->table = new updateTable("#__excel2vm_backups", "backup_id");
        $this->core = new core();
		$this->id=JRequest::getVar('id', '', '', 'int');
		$this->config=$this->core->getConfig();
        $this->active_fields =$this->core->active_fields;
        $this->profile =$this->core->profile;
	}

//Список всех пользователей



	function getBackups() {
		$query = "SELECT  *, DATE_FORMAT(date, '%d.%m.%Y %H:%i:%s') as date2 FROM #__excel2vm_backups ORDER BY date DESC";
		return $this->_getList($query);
	}

	function getCategories(){
		$this->_db->debug(0);
		$this->_db->setQuery("SELECT virtuemart_category_id, category_name FROM #__virtuemart_categories_".$this->config->sufix);
        return $this->_db->loadObjectList();
	}

    function new_backup(){
        $time_start=$this->getmicrotime();
        $resp = new stdClass();
        $tables=array("#__virtuemart_categories",
					  "#__virtuemart_categories_".$this->config->sufix,
					  "#__virtuemart_category_categories",
					  "#__virtuemart_category_medias",
					  "#__virtuemart_product_manufacturers",
					  "#__virtuemart_manufacturers",
					  "#__virtuemart_manufacturers_".$this->config->sufix,
					  "#__virtuemart_products",
					  "#__virtuemart_products_".$this->config->sufix,
					  "#__virtuemart_product_medias",
					  "#__virtuemart_product_prices",
					  "#__virtuemart_product_customfields",
					  "#__virtuemart_customs",
					  "#__virtuemart_product_categories",
					  "#__virtuemart_medias"
					  );

		array_walk($tables,create_function('&$val','$val = str_replace("#__","'.$this->_db->getPrefix().'",$val);'));
        if(!$this->config->backup_type){//Обычный SQL

			$backup_filename="virtuemart_backup_".date("d.m.Y_H_i_s").".sql";
			$fp = fopen(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'backup'.DS.$backup_filename , "a" );
            if(!$fp){
                $resp->status="error";
                if(!is_writeable(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'backup'.DS)){
                    $resp->html= JText::_('ERROR_OCCURED_DURING_BACKUP_CHECK_IS_THE_FOLDER_WRIGHTABLE').JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'backup'.DS;
                }
                else{
                    $resp->html= "Возникла ошибка. Проверьте, достаточно ли дискового пространства на хостинге";
                }
                echo json_encode($resp);
				exit();
            }

	    	foreach($tables as $table){
	           $table = str_replace('#__',$this->_db->getPrefix(),$table);

               $fields_list_array=$this->_db->getTableColumns($table);
               $fields_list=array();
               foreach($fields_list_array as $key=> $field){
                   $fields_list[]=$key;
               }
               $this->_db->setQuery("SELECT COUNT(*) FROM `{$table}`");
	           $total=$this->_db->loadResult();
			   fwrite($fp,"TRUNCATE TABLE `{$table}`;\n");
			   $i=0;
	           for(;;){
                   if($i>=$total)break;
				   $this->_db->setQuery("SELECT * FROM `{$table}`",$i,200);
		           $data=$this->_db->loadAssocList();
				   $i+=200;
				   if(!$data)break;
                   if(count($fields_list)){
                       fwrite($fp,"INSERT INTO `{$table}` (`".implode("`,`",$fields_list)."`) VALUES\n");
                   }
                   else{
                       fwrite($fp,"INSERT INTO `{$table}` VALUES\n");
                   }


				   $rows=array();

				   foreach($data as $key=> $row ){
				   	   $fields=array();
		               foreach($row as $field){
		                   $field=str_replace(";\n",";",$field);
		                   $fields[]=$this->_db->Quote($field);
		               }
					   $rows[]="(".implode(",",$fields).")";
				   }
				   if(count($rows)){
					   fwrite($fp,implode(",\n",$rows).";\n\n");
				   }
				   else
				       fwrite($fp,";\n\n");

	           }
	    	}

            fclose($fp);
			$size=filesize(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'backup'.DS.$backup_filename);
			if($size){
				$this->_db->setQuery("INSERT INTO #__excel2vm_backups SET file_name = '$backup_filename',size='$size'");
				$this->_db->Query();
			}
			else{
                $resp->status="error";
                if(!is_writeable(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'backup'.DS)){
                    $resp->html= JText::_('ERROR_OCCURED_DURING_BACKUP_CHECK_IS_THE_FOLDER_WRIGHTABLE').JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'backup'.DS;
                }
                else{
                    $resp->html= "Возникла ошибка. Проверьте, достаточно ли дискового пространства на хостинге";
                }
                echo json_encode($resp);
				exit();
			}
        }
        else{
	        $backup_filename="virtuemart_backup_".date("d.m.Y_H_i_s").".gz";
			$mainframe = JFactory::getApplication();
			$command = "mysqldump -h".$mainframe->getCfg('host')." -u".$mainframe->getCfg('user')." -p".$mainframe->getCfg('password')." ".$mainframe->getCfg('db')." ".implode(" ",$tables)." | gzip -9> ".JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'backup'.DS.$backup_filename;


			system($command,$output);

			if($output===0){
				$size=filesize(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'backup'.DS.$backup_filename);
				$this->_db->setQuery("INSERT INTO #__excel2vm_backups SET file_name = '$backup_filename',size='$size'");
				$this->_db->Query();
			}
			else{
			    $resp->status="error";
                $resp->html= JText::_('ERROR_OCCURED_DURING_BACKUP_TRY_SQL_BACKUP_METHOD');
                echo json_encode($resp);
				exit();
			}
        }

        $id = $this->_db->insertid();
        $this->table->load($id);
		$this->table->dateFormat('date','d.m.Y H:i:s');
		$link="components/com_excel2vm/backup/".$backup_filename;
        $time_end=$this->getmicrotime();
        $execution_time=round($time_end-$time_start,3);

        $resp->status="ok";
        $resp->time=$execution_time;
        $resp->html= "<tr id='$id' style='display:none'>
				<td>$id</td>
				<td><a href='$link' target='_blank'>$backup_filename</a></td>
				<td>".$this->getSize($size)."</td>
				<td>{$this->table->date}</td>
				<td><li style='display: inline-block' class='ui-state-default ui-corner-all'><span title='Удалить' rel='$id' class='ui-icon ui-icon-circle-close'></span></li></td>
				<td><li style='display: inline-block' class='ui-state-default ui-corner-all'><span title='". JText::_('RECOVER')."' rel='$id' class='ui-icon ui-icon-arrowreturnthick-1-w'></span></li></td>

			 </tr>";
             echo json_encode($resp);
        exit();
    }

    function getSize($bytes){
	   if($bytes<1024)
	   	  return $bytes." B<br>";
	   elseif($bytes<1024*1024)
	   	  return round($bytes/1024)." KB<br>";
	   else
	   	  return round($bytes/(1024*1024),2)." MB<br>";
	}

    function clear(){
        $query=array();
        $inputCookie  = JFactory::getApplication()->input->cookie;


    	$products=JRequest::getVar('products', '', '', 'cmd');
    	$cats=JRequest::getVar('cats', '', '', 'cmd');
    	$images=JRequest::getVar('images', '', '', 'cmd');
    	$manufacturers=JRequest::getVar('manufacturers', '', '', 'cmd');
    	$customs=JRequest::getVar('customs', '', '', 'cmd');
    	$customs_profile=JRequest::getVar('customs_profile', '', '', 'cmd');
    	$empty_profile=JRequest::getVar('empty_profile', '', '', 'cmd');
    	$backups=JRequest::getVar('backups', '', '', 'cmd');
    	$loaded=JRequest::getVar('loaded', '', '', 'cmd');
    	$exported=JRequest::getVar('exported', '', '', 'cmd');

        if($products=='true' OR $cats=='true'){
            $msg[]="Товары удалены";
            $inputCookie->set('b_products',1, time()+(365*24*3600));
            $query[]= "TRUNCATE TABLE `#__virtuemart_products`;";
        	$query[]= "TRUNCATE TABLE `#__virtuemart_products_".$this->config->sufix."`;";
        	$query[]= "TRUNCATE TABLE `#__virtuemart_product_medias`;";
        	$query[]= "TRUNCATE TABLE `#__virtuemart_product_prices`;";
        	$query[]= "TRUNCATE TABLE `#__virtuemart_product_customfields`;";
        	$query[]= "TRUNCATE TABLE `#__virtuemart_product_categories`;";
            $query[]= "TRUNCATE TABLE `#__virtuemart_product_manufacturers`;";
        	$query[]= "DELETE FROM #__virtuemart_medias WHERE file_type='product';";

            $this->_db->setQuery("SHOW TABLES LIKE '".$this->_db->getPrefix()."fastseller_product_product_type_xref'");
            if($this->_db->loadResult()){
                $query[]= "TRUNCATE TABLE `#__fastseller_product_product_type_xref`;";
                $this->_db->setQuery("SELECT product_type_id FROM #__fastseller_product_type");
                $pr_types=$this->_db->loadColumn();
                foreach(@$pr_types as $pr_type){
                    $query[]= "TRUNCATE TABLE `#__fastseller_product_type_{$pr_type}`;";
                }
            }

            $this->_db->setQuery("SHOW TABLES LIKE '".$this->_db->getPrefix()."vm_product_product_type_xref'");
            if($this->_db->loadResult()){
                $query[]= "TRUNCATE TABLE `#__vm_product_product_type_xref`;";
                $this->_db->setQuery("SELECT product_type_id FROM #__vm_product_type");
                $pr_types=$this->_db->loadColumn();
                foreach(@$pr_types as $pr_type){
                    $query[]= "TRUNCATE TABLE `#__vm_product_type_{$pr_type}`;";
                }
            }
        }
        else{
            $inputCookie->set('b_products',0, time()+(365*24*3600));
        }

        if($cats=='true'){
            $msg[]="Категории удалены";
            $inputCookie->set('b_cats',1, time()+(365*24*3600));
            $query[]= "TRUNCATE TABLE `#__virtuemart_categories`;";
            $query[]= "TRUNCATE TABLE `#__virtuemart_categories_".$this->config->sufix."`;";

    		$query[]= "TRUNCATE TABLE `#__virtuemart_category_categories`;";
            $query[]= "TRUNCATE TABLE `#__virtuemart_category_medias`;";
            $query[]= "TRUNCATE TABLE `#__virtuemart_medias`;";
        }
        else{
            $inputCookie->set('b_cats',0, time()+(365*24*3600));
        }

        if($manufacturers=='true'){
            $msg[]="Производители удалены";
            $inputCookie->set('b_manufacturers',1, time()+(365*24*3600));
            $query[]= "TRUNCATE TABLE `#__virtuemart_manufacturers`";
            $query[]= "TRUNCATE TABLE `#__virtuemart_manufacturers_".$this->config->sufix."`;";
        }
        else{
            $inputCookie->set('b_manufacturers',0, time()+(365*24*3600));
        }

        if($customs_profile=='true'){
            $msg[]="Произвольные поля удалены из профиля";
            $inputCookie->set('b_customs_profile',1, time()+(365*24*3600));
            $query[]="DELETE FROM #__excel2vm_fields WHERE type = 'custom'";
        }
        else{
            $inputCookie->set('b_customs_profile',0, time()+(365*24*3600));
        }


        if($empty_profile=='true'){
            $msg[]="Пустые поля удалены из профиля";
            $inputCookie->set('b_empty_profile',1, time()+(365*24*3600));
            $query[]="DELETE FROM #__excel2vm_fields WHERE type = 'empty'";
        }
        else{
            $inputCookie->set('b_empty_profile',0, time()+(365*24*3600));
        }


        if($customs=='true'){
            $msg[]="Настраиваемые поля удалены";
            $inputCookie->set('b_customs',1, time()+(365*24*3600));
            $xml=JFactory::getXML(JPATH_ADMINISTRATOR .DS.'components'.DS.'com_virtuemart'.DS.'virtuemart.xml');
            $this->vm_version=(string)$xml->version;
            $this->is_vm_version_3=(substr($this->vm_version,0,1)==3 OR substr($this->vm_version,0,3)=='2.9')?true:false;
            $query[]= "TRUNCATE TABLE `#__virtuemart_customs`";
            $query[]= "INSERT INTO `#__virtuemart_customs` (`show_title`, `virtuemart_custom_id`, `custom_parent_id`, `virtuemart_vendor_id`, `custom_jplugin_id`, `custom_element`, `admin_only`, `custom_title`, `custom_tip`, `custom_value`, `".($this->is_vm_version_3?'custom_desc':'custom_field_desc')."`, `field_type`, `is_list`, `is_hidden`, `is_cart_attribute`, `layout_pos`, `custom_params`, `shared`, `published`, `created_on`, `created_by`, `ordering`, `modified_on`, `modified_by`, `locked_on`, `locked_by`) VALUES
(1, 1, 0, 1, 0, '', 0, 'COM_VIRTUEMART_RELATED_PRODUCTS', 'COM_VIRTUEMART_RELATED_PRODUCTS_TIP', '', 'COM_VIRTUEMART_RELATED_PRODUCTS_DESC', 'R', 0, 0, 0, NULL, NULL, 0, 1, '0000-00-00 00:00:00', 62, 0, '2011-05-25 21:52:43', 62, '0000-00-00 00:00:00', 0),
(1, 2, 0, 1, 0, '', 0, 'COM_VIRTUEMART_RELATED_CATEGORIES', 'COM_VIRTUEMART_RELATED_CATEGORIES_TIP', NULL, 'COM_VIRTUEMART_RELATED_CATEGORIES_DESC', 'Z', 0, 0, 0, NULL, NULL, 0, 1, '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0)";
            $query[]="DELETE FROM #__excel2vm_fields WHERE type IN ('extra','extra-price','extra-cart','cherry');";
        }
        else{
            $inputCookie->set('b_customs',0, time()+(365*24*3600));
        }

        foreach($query as $q){
            $this->_db->setQuery($q);
            $this->_db->Query();
        }

        if($images=='true'){
            $msg[]="Файлы изображений удалены из папки ".DS.'images'.DS.'stories'.DS.'virtuemart'.DS.'product'.DS;
            $inputCookie->set('b_images',1, time()+(365*24*3600));
            $this->delete_files(JPATH_ROOT.DS.'images'.DS.'stories'.DS.'virtuemart'.DS.'product'.DS,array('index.html'));
            $this->delete_files(JPATH_ROOT.DS.'images'.DS.'stories'.DS.'virtuemart'.DS.'product'.DS.'resized'.DS,array('index.html'));

        }
        else{
            $inputCookie->set('b_images',0, time()+(365*24*3600));
        }

        if($backups=='true'){
            $msg[]="Резервные копии удалены";
            $inputCookie->set('b_backups',1, time()+(365*24*3600));
            $this->delete_files(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'backup',array('index.html'));
            $this->_db->setQuery("TRUNCATE TABLE #__excel2vm_backups");
            $this->_db->Query();
        }
        else{
            $inputCookie->set('b_backups',0, time()+(365*24*3600));
        }

        if($loaded=='true'){
            $msg[]="Импортированные файлы удалены";
            $inputCookie->set('b_loaded',1, time()+(365*24*3600));
            $this->delete_files(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'xls',array('index.html','example4j17.xls'));

        }
        else{
            $inputCookie->set('b_loaded',0, time()+(365*24*3600));
        }
        if($exported=='true'){
            $msg[]="Экспортированные файлы удалены";
            $inputCookie->set('b_exported',1, time()+(365*24*3600));
            $this->delete_files(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'export',array('index.html'));

        }
        else{
            $inputCookie->set('b_exported',0, time()+(365*24*3600));
        }
    	echo "<ul><li>".implode("</li><li>",$msg)."</li></ul>";
		exit();
    }

	function restore() {
        $this->table->load($this->id);
        $this->table->dateFormat('date','d.m.Y H:i:s');
		if(substr($this->table->file_name,-3)=='sql'){
		   if(!JFile::exists(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'backup'.DS.$this->table->file_name)){
                echo '<b><font color="#FF0000">'. JText::_('DATA_WAS_NOT_RESTORED_FILE_REMOVED') .'</font></b><br />'.$this->_db->ErrorMsg();
		        exit();
		   }
           $query='';
           $success=0;
           $counter=0;
           $file_handler=fopen(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'backup'.DS.$this->table->file_name,"r");
           while (!feof($file_handler)) {
                  $counter++;
                  $query.=fgets($file_handler,16192);
                  if(substr(trim($query),-1)==";"){
                      $this->_db->setQuery($query);
                      if($this->_db->Query()){
                          $success++;
                          $query='';
                      }
                  }


           }

		   if($success)
		   		echo JText::_('DATA_SUCCESSFULLY_RECOVERED_AT_THE_TIME_OF').$this->table->date.". <br>Количество запросов - $success";
		   else
	            echo '<b><font color="#FF0000">'. JText::_('DATA_WAS_NOT_RESTORED') .'</font></b><br />'.$this->_db->ErrorMsg();

		   exit();
		}
		else{
	        $mainframe = JFactory::getApplication();
			$command = "gunzip < ".JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'backup'.DS.$this->table->file_name." | mysql -h".$mainframe->getCfg('host')." -u".$mainframe->getCfg('user')." -p".$mainframe->getCfg('password')." ".$mainframe->getCfg('db');

			system($command,$output);
			if($output===0)
				echo JText::_('DATA_SUCCESSFULLY_RECOVERED_AT_THE_TIME_OF').$this->table->date;
			else
	            echo '<b><font color="#FF0000">'. JText::_('DATA_WAS_NOT_RESTORED') .'</font></b><br />'.$this->_db->ErrorMsg();
			exit();
		}

	}

	function delete_backup(){
        $this->table->load($this->id);
		if(!JFile::delete(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'backup'.DS.$this->table->file_name)){
			if(JFile::exists(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'backup'.DS.$this->table->file_name)){
            	echo '<b><font color="#FF0000">'.sprintf(JText::_('FILE_S_CAN_NOT_BE_REMOVED'),$this->table->file_name).'</font></b>';
				exit();
			}

		}
        if($this->table->delete($this->id))
			echo sprintf(JText::_('FILE_S_REMOVED'),$this->table->file_name);
		else
            echo sprintf(JText::_('ROW_S_NOT_REMOVED'),$this->table->backup_id);
		exit();
	}



    function getmicrotime(){
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    function delete_files($source,$exclude=array()){
       $dh = opendir($source);

       while (($file = readdir($dh)) !== false) {
          if(filetype($source .DIRECTORY_SEPARATOR. $file)=='file' AND !in_array($file,$exclude)){

          	unlink($source .DIRECTORY_SEPARATOR. $file);
          }
       }
    }
}

?>