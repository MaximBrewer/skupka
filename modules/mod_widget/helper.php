<?php  
/*------------------------------------------------------------------------
# mod_widget - vData Widgets 
# ------------------------------------------------------------------------
# author    Team WDM Tech
# copyright Copyright (C) 2016 wwww.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech..com
# Technical Support:  Forum - http://www.wdmtech..com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access');

class modWidgetHelper {
	
	
	static function getWidgets($params) {
	        $widget = $params->get('widget');
			
			$db = JFactory::getDbo();
			$sql = 'SELECT p.* FROM  `#__vd_widget` AS p where p.id ='.$widget;
          
			$sql .= ' order by p.ordering asc';
			$db->setQuery($sql);
			return $db->loadObjectList();
	}  
	static function getConfiguration()
	{
	    $db = JFactory::getDbo();
		$query = 'select * from '.$db->quoteName('#__vd_config').' where id=1';
		$db->setQuery( $query );
		return $db->loadObject();
      
	}
	static function getProfiles(){
		$db = JFactory::getDbo();
		$query = 'select i.*, e.name as plugin, e.element, concat("plg_", e.folder, "_", e.element) as extension FROM #__vd_profiles as i left join #__extensions as e on (e.extension_id=i.pluginid and e.enabled=1) where 1=1 group by i.id order by id desc';
		$db->setQuery($query);
		return $db->loadObjectList();
		
	}
	static function getPlugins()
	{
		$db = JFactory::getDbo();
		$query = 'select extension_id, name from #__extensions  where type = "plugin" and folder = "vdata" and enabled = 1';
		$db->setQuery( $query );
		return $db->loadObjectList();
		
	}
	static function log_information($q,$response_data){
	  $query = '';
	  $opton_value = json_decode($q->detail);
	  $db = JFactory::getDbo();
	  for($v=0;$v<count($response_data);$v++){
		  $response_datas = $response_data[$v];
		  if($opton_value->existing_database_table==$response_datas->label)
			  $query =$response_datas->value;
		  
	  }
	  if(!empty($query)){
		        preg_match_all('/{tablename\s(.*?)}/i', $query, $matches);
                preg_match_all('/{as\s(.*?)}/i', $query, $match);
				$matches_s = $matches[1];
				$text = $query;
				for($r=0;$r<count($matches_s);$r++){
				$text = preg_replace('{tablename '.$matches_s[$r].'}', $matches_s[$r], $text, 1);	
				}
				 $query = str_replace('{','',str_replace('}','',$text)); 
	   
	   $sql = $query;
	   $db->setQuery($sql);
	   $datas = $db->loadObjectList();
	   $html = '<table class="adminlist table table-hover" width="100%"><th>No</th><th>Table Name</th><th>Message</th>';
	   for($v=0;$v<count($datas)&&$v<6;$v++){
		   $data = $datas[$v];
		  $html .= '<tr><td>'.$data->id.'</td><td>'.$data->table.'</td><td>'.$data->message.'</td><tr>'; 
	   }
	   $html .= '</table>';
	   
	   return $html;
	   }
	  
  }
}