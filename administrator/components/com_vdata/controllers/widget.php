<?php 
/*------------------------------------------------------------------------
# com_vdata - vData
# ------------------------------------------------------------------------
# author    Team WDMtech
# copyright Copyright (C) 2016 wwww.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech..com
# Technical Support:  Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


class  VdataControllerWidget extends VdataController
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct()
	{
		
		parent::__construct();
		
		JFactory::getApplication()->input->set( 'view', 'widget' );
		
		$this->model = $this->getModel('widget');
		$this->registerTask( 'add'  , 	'edit' );
	}
	
	function display($cachable = false, $urlparams = false){
		
		$user = JFactory::getUser();
		$canCreateWidget = $user->authorise('core.widget', 'com_vdata');
		
		if(!$canCreateWidget){
			$msg = JText::_( 'ALERT_AUTHORIZATION_ERROR' );
			$this->setRedirect( 'index.php?option=com_vdata', $msg , 'error');
		}
			
		parent::display();
	}
	
	/**
	 * display the edit form
	 * @return void
	 */
	function edit()
	{
		JFactory::getApplication()->input->set( 'view', 'widget' );
		JFactory::getApplication()->input->set('hidemainmenu', 1);

		parent::display();
	}
	

	//to publish/unpublish the items
	function series_column(){
		$obj = $this->model->series_column();
	    jexit(json_encode($obj));
	}
	function table_reference_options(){
		$obj = $this->model->table_reference_options();
	    jexit(json_encode($obj));
		
	}
	
	function data_for_query()
	{
	  $obj = $this->model->data_for_query();
	  jexit(json_encode($obj));
	}
	function publish()
	{	
		$model = $this->getModel('widget');
		
		$task = JFactory::getApplication()->input->getCmd('task', '');
		$text = $task=="publish"?'Published':'Unpublished';
		
		if(!$model->publish()) {
			JFactory::getApplication()->enqueueMessage($model->getError());
			$link = 'index.php?option=com_vdata&view=widget';
		    $this->setRedirect($link, $msg);
		} else {
			$msg = JText::sprintf( '%s successfully', $text );
			$link = 'index.php?option=com_vdata&view=widget';
		     $this->setRedirect($link, $msg);
		}
			
	}
	
	/**
	 * save a record (and redirect to main page)
	 * @return void
	 */
	function save()
	{
		$model = $this->getModel('widget');
		
		if(!$model->store()) {
			JFactory::getApplication()->enqueueMessage($model->getError());
			$this->setRedirect( 'index.php?option=com_vdata&view=widget');
		} else {
			$msg = JText::_( 'vchart Saved' );
			$this->setRedirect( 'index.php?option=com_vdata&view=widget', $msg );
		}

	}
	
	function apply()
	{ 
		$model = $this->getModel('widget');
		 
		$widgetid=$model->store();
		
		if($widgetid <= 0) {
			JFactory::getApplication()->enqueueMessage($model->getError());
			$this->setRedirect( 'index.php?option=com_vdata&view=widget&tmpl=component&task=edit&cid[]='.$widgetid);
		} else {
			$msg = JText::_( 'Widget Saved' );
		    $this->setRedirect( 'index.php?option=com_vdata&view=widget&tmpl=component&task=edit&cid[]='.$widgetid,$msg);
		}

	}
	

	/**
	 * remove record(s)
	 * @return void
	 */
	
	 function formating_section(){
		  $obj = $this->model->formating_section();
		  
	       jexit(json_encode($obj));
	 }
	 function predefined(){
		        $term = JFactory::getApplication()->input->get('term', '');
				$db     = JFactory::getDbo();
				$query  = $db->getQuery(true);
				$result = array();

				// Prepare the query.
				$query->select('m.id, m.title, m.alias, m.link, m.parent_id, m.img, e.element')
				->from('#__menu AS m');

				// Filter on the enabled states.
				$query->join('LEFT', '#__extensions AS e ON m.component_id = e.extension_id')
				->where('m.client_id = 1')
				->where('e.enabled = 1')
				->where('m.id > 1')
                ->where('m.parent_id = 1');
				// Order by lft.
				$query->order('m.lft');

				$db->setQuery($query);

				// Component list
				$components = $db->loadObjectList();
				$install_components ='"com_joomla"';
                foreach ($components as &$component)
		        {
				$install_components .= ',"'.$component->title.'"';	
				}
				
				$obj = new stdClass();
				$obj->result = 'error';
				// $url = 'http://www.joomlawings.com/index.php';
				$url = 'http://www.wdmtech.com/demo/index.php';
				
				$ch = curl_init(); 
				$session_setting = JFactory::getApplication()->input->getInt('session_setting', '');
				$postdata = array("term"=>$term, "option"=>"com_vdata", "task"=>"preWidgets", "token"=>JSession::getFormToken(), "scope"=>$install_components, "session_setting"=>$session_setting);
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

				$result = json_decode(curl_exec($ch)); 
				$items = $result->html;
			   // $items = json_decode(curl_exec($ch));
				//$items = (curl_exec($ch));print_r($items);
			  $resp = array();
				
				$result = $labels = $results = $charting = $listing = $single = $desc = array();
                for($i=0;$i<count($items);$i++){
				$item = $items[$i];	
				array_push($result, array("value"=>trim($item->label),"label"=>trim($item->label),"desc"=>trim($item->desc),"id"=>trim($item->id)));
				array_push($labels, trim($item->label));
				array_push($results, trim($item->value));
				array_push($charting, trim($item->charting));
				array_push($listing, trim($item->listing));
				array_push($single, trim($item->single));
				array_push($desc, trim($item->desc));
				
				} 
				$obj->html = $result;
				$obj->htmls = $results;
				$obj->labels = $labels;
				$obj->charting = $charting;
				$obj->listing = $listing;
				$obj->single = $single;
				$obj->desc = $desc;
				$obj->result = 'success';
				jexit(json_encode($obj));
	 }
	 function extra_conditions(){
		  $obj = $this->model->series_column();
	       jexit(json_encode($obj));
	 }
	function remove()
	{
		$model = $this->getModel('widget');
		
		if(!$model->delete()) {
			JFactory::getApplication()->enqueueMessage($model->getError());
			$this->setRedirect( 'index.php?option=com_vdata&view=widget');
		} else {
			$msg = JText::_( 'Record(s) Deleted' );
			$this->setRedirect( 'index.php?option=com_vdata&view=widget', $msg );
		}
		
	}
function make_list($name, $current_value, &$items, $first = 0, $extra='')
{
	$html = "\n".'<select name="'.$name.'" id="'.$name.'" class="inputbox" size="1" '.$extra.'>';
	if ($items == null)
		return '';
	foreach ($items as $key => $value)
		{
		if (strncmp($key,"OPTGROUP_START",14) == 0)
			{
			$html .= "\n".'<optgroup label="'.$value.'">';
			continue;
			}
		if (strncmp($key,"OPTGROUP_END",12) == 0)
			{
			$html .= "\n".'</optgroup>';
			continue;
			}
		if ($key < $first)					// skip unwanted entries
			continue;
		$selected = '';
		if ($current_value == $key)
			$selected = ' selected="selected"';
		$html .= "\n".'<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
		}
	$html .= '</select>'."\n";
	return $html;
}
	/**
	 * cancel editing a record
	 * @return void
	 */
	
	
 function load_columns()
	{
		
		JSession::checkToken() or jexit( JText::_('INVALID_TOKEN') );
		
		$model = $this->getModel('widget');
		$obj = $model->getColumns();
		jexit(json_encode($obj));
		
	}
	
	function load_refer_columns()
	{
		$model = $this->getModel('widget');
		$obj = $model->getReferColumns();
		
		jexit(json_encode($obj));
		
	}
	function value_from_existing_database()
	{
		
		$model = $this->getModel('widget');
		$obj = $model->value_existing_database();
		jexit(json_encode($obj));
	}
	function value_from_existing_database_extra_condition()
	{
		
		$model = $this->getModel('widget');
		$obj = $model->value_from_existing_database_extra_condition();
		jexit(json_encode($obj));
	}
	function value_from_existing_database_sql_query()
	{
		
		$model = $this->getModel('widget');
		$obj = $model->value_from_existing_database_sql_query(); 
		jexit(json_encode($obj));
	}
	function series_data()
	{
	    $model = $this->getModel('widget');
		$obj = $model->series_data();
		jexit(json_encode($obj));
	}
	function getTableFromOtherDatabase(){
		
		$model = $this->getModel('widget');
		$obj = $model->getTableFromOtherDatabase();
		jexit(json_encode($obj));
		}
		
		function value_from_map_information(){
		
		$model = $this->getModel('widget');
		$obj = $model->value_from_map_information();
		jexit(json_encode($obj));
		
		}
	function series_color_object()
	{
		
		$query_name = JFactory::getApplication()->input->get('sql_query','', 'RAW');
		$chart_type = JFactory::getApplication()->input->get('chart_type','', 'RAW');
		$table = JFactory::getApplication()->input->get('table','', 'RAW');
		$extra = JFactory::getApplication()->input->get('extra','', 'RAW');
		$limit = JFactory::getApplication()->input->get('limit','');
		$orderby = JFactory::getApplication()->input->get('orderby','');
		$ordering = JFactory::getApplication()->input->get('ordering','');
		$column = JFactory::getApplication()->input->get('column','');
		$obj = new stdClass();
		$db = JFactory::getDbo();
		$obj->result = 'error';
		if(!empty($query_name))
		{
			try
			{
			$obj->result = 'success';	
			$data = $db->setQuery($query_name)->loadObjectList();
			$object_array = get_object_vars($data[0]);
		    $object_array = count($object_array)>0?array_keys($object_array):array();
			 if($chart_type=='Line Chart' || $chart_type=='Area Chart' || $chart_type=='Stepped AreaChart' || $chart_type=='Column Chart' || $chart_type=='Bar Chart'){
				 array_shift($object_array);
			 }
			 elseif($chart_type == 'Pie Chart' || $chart_type == 'Slice Pie Chart'){
				 $column = $object_array[0];
				 $columns =  array();
				 for($s=0;$s<count($data);$s++){
					array_push($columns,$data[$s]->$column); 
				 }
				 $object_array = $columns;
			 }
			$obj->data = implode(',',$object_array);
			}
			catch (RuntimeException $e)
			{
			$obj->result = 'error';
			$obj->error = $e->getMessage();	
			}
		}
		elseif(!empty($table) && ($chart_type == 'Pie Chart' || $chart_type == 'Slice Pie Chart')){
		try
			{
			$obj->result = 'success';
			    if(!empty($column) && !empty($table))
				$query = 'select `'.$column.'` from '.$table;
				if(!empty($extra))
				$query .= ' where '.$extra;
				if(!empty($orderby) && !empty($ordering))
				$query .= ' ORDER BY '.$orderby.' '.$ordering; 
				if(!empty($limit))
				$query .= ' LIMIT '.$limit;
			
			$data = $db->setQuery($query)->loadObjectList();
			$object_array = get_object_vars($data[0]);
		    $object_array = count($object_array)>0?array_keys($object_array):array();
			 
				 $column = $object_array[0];
				 $columns =  array();
				 for($s=0;$s<count($data);$s++){
					array_push($columns,$data[$s]->$column); 
				 }
				 $object_array = $columns;
			
			$obj->data = implode(',',$object_array);
			}
			catch (RuntimeException $e)
			{
			$obj->result = 'error';
			$obj->error = $e->getMessage();	
			}	
		}
		jexit(json_encode($obj));
		
	}
	
	function cancel()
	{
		$msg = JText::_( 'Operation Cancelled' );
		$this->setRedirect( 'index.php?option=com_vdata&view=vdata', $msg );
	
	}
}