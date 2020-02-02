<?php
if(!defined("DS")){
    define("DS",DIRECTORY_SEPARATOR);
}
jimport( 'joomla.application.component.model');
defined('_JEXEC') or die('Restricted access');


	class com_excel2vmInstallerScript
	{


	    public function install($parent){
            $this->_db = JFactory::getDBO();

            $this->_db->debug(0);
            $tables=$this->_db->setQuery("SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = '".$this->_db->getPrefix()."virtuemart_products_ru_ru'");
            $lang=count($this->_db->loadResult())?"ru-RU":"en-GB";

            $this->_db->setQuery('INSERT IGNORE INTO `#__excel2vm` (`id`, `profile`, `active`, `config`, `default_profile`) VALUES
(1, \'По умолчанию\', \'1,2,3,5,6,8\', \'O:8:"stdClass":35:{s:14:"price_template";s:1:"1";s:6:"simbol";s:1:" ";s:14:"extra_category";s:25:"Прочие товары";s:8:"languege";s:5:"'.$lang.'";s:8:"currency";s:3:"131";s:13:"currency_rate";s:1:"1";s:14:"alias_template";s:1:"2";s:5:"first";s:1:"2";s:4:"last";s:6:"все";s:7:"cat_col";s:1:"1";s:11:"auto_backup";s:1:"1";s:11:"backup_type";s:1:"0";s:6:"create";s:1:"1";s:23:"create_without_category";s:1:"0";s:18:"create_without_sku";s:1:"1";s:9:"is_update";s:1:"1";s:15:"multicategories";s:1:"1";s:15:"change_category";s:1:"1";s:18:"update_without_sku";s:1:"1";s:9:"unpublish";s:1:"0";s:20:"unpublish_categories";a:1:{i:0;s:1:"0";}s:9:"published";s:1:"1";s:13:"published_old";s:2:"-1";s:15:"unpublish_image";s:1:"0";s:11:"reset_stock";s:1:"0";s:16:"reset_categories";a:1:{i:0;s:1:"0";}s:14:"delete_related";s:1:"0";s:16:"spec_price_clear";s:1:"0";s:18:"extra_fields_clear";s:1:"0";s:20:"images_import_method";s:1:"0";s:4:"path";s:34:"images/stories/virtuemart/product/";s:10:"thumb_path";s:42:"images/stories/virtuemart/product/resized/";s:10:"price_hint";s:1:"1";s:16:"new_profile_name";s:0:"";s:16:"profile_id_value";s:1:"1";}\',1)');
            $this->_db->Query();

            $this->_db->setQuery("SELECT id FROM #__excel2vm_yml WHERE id=1");
            if(!$this->_db->loadResult()){
                $this->_db->setQuery("INSERT INTO #__excel2vm_yml SET id = 1, yml_export_path =".$this->_db->Quote(JPATH_ROOT.DS."ymarket.xml").", params=".$this->_db->Quote('{"is_update":1,"is_create":1,"identity":"product_id"}')."");
                $this->_db->Query();
            }
	    }

	    public	function update($parent)
	    {
	    	$this->_db = JFactory::getDBO();
            $sqls=file_get_contents(dirname(__FILE__).DS."admin".DS."install.sql");


			$tables=$this->_db->getTableColumns("#__excel2vm_backups");

			if(!isset($tables['size'])){
		        $this->_db->setQuery("ALTER TABLE `#__excel2vm_backups`
								ADD size int( 20 ) NOT NULL;");
		        $this->_db->Query();
			}

            $tables=$this->_db->getTableColumns("#__excel2vm_yml");

			if(!isset($tables['export_params'])){
		        $this->_db->setQuery("ALTER TABLE `#__excel2vm_yml`
								ADD export_params text NOT NULL;");
		        $this->_db->Query();
			}

            $tables=$this->_db->getTableColumns("#__excel2vm_fields");

           if(@$tables['extra_id']->Type!='varchar(256)'){
		        $this->_db->setQuery("ALTER TABLE `#__excel2vm_fields` CHANGE `extra_id` `extra_id` VARCHAR( 256 ) NULL DEFAULT NULL");
		        $this->_db->Query();

		   }

           $this->_db->setQuery("ALTER TABLE `#__excel2vm` CHANGE `profile` `profile` TEXT NOT NULL ");
		   $this->_db->Query();

           $sqls= $this->_db->splitSql($sqls);
            foreach($sqls as $sql){
            	if(empty($sql))continue;
                if(!trim($sql))continue;
                $this->_db->setQuery($sql);
				$this->_db->query();
            }

            $this->_db->setQuery("SELECT id FROM #__excel2vm_yml WHERE id=1");
            if(!$this->_db->loadResult()){
                $this->_db->setQuery("INSERT INTO #__excel2vm_yml SET id = 1, yml_export_path =".$this->_db->Quote(JPATH_ROOT.DS."ymarket.xml").", params=".$this->_db->Quote('{"is_update":1,"is_create":1,"identity":0,"images_mode":1}')."");
                $this->_db->Query();
            }

	        echo "Компонент Обновлен до версии ".$parent->get('manifest')->version;
	    }

}
?>