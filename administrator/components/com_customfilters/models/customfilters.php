<?php
/**
 * The Customfilters model file
 *
 * @package 	customfilters
 * @author		Sakis Terz
 * @link		http://breakdesigns.net
 * @copyright	Copyright (c) 2012-2018 breakdesigns.net. All rights reserved.
 * @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @version $Id: customfilters.php 2014-06-03 18:34 sakis $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;


// Load the model framework
jimport('joomla.application.component.modellist');

/**
 * The basic model class
 *
 * @author	Sakis Terz
 * @since	1.0
 */
class CustomfiltersModelCustomfilters extends JModelList{
	/**
	 * @var string Model context string
	 */
	var $extension='com_customfilters';
	var $name='Custom Filters';

	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.0
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
			'filter_id',
			'alias',
			'ordering',
			'data_type',
			'custom_title',
			'field_type',
			'type_id',
			'published',
            'custom_id');
		}
		parent::__construct($config);

	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 * @since	1.0
	 */
	protected function populateState($ordering = null, $direction = null){
		// Initialise variables.

		$app = JFactory::getApplication('administrator');

		// Adjust the context to support modal layouts.
		if ($layout = $app->input->get('layout', 'default')) {
			$this->context .= '.'.$layout;
		}

		// Load the filter published.
		$this->setState('filter.published', $app->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', ''));

		// Load the filter search.
		$this->setState('filter.search', $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search', ''));

		// Load the filter type_id
		$type_id=$app->getUserStateFromRequest($this->context.'.filter.type_id', 'filter_type_id', '');
		preg_match('/[0-9]+([,]{1}[0-9]+)?/', $type_id,$matches);
		if(!empty($matches[0]))$this->setState('filter.type_id', $matches[0]);

		parent::populateState('ordering','ASC');
	}


	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 * @author	Sakis Terz
	 * @param	string		$id	A prefix for the store id.
	 * @return	string		A store id.
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.published');
		$id	.= ':'.$this->getState('filter.type_id');
		return parent::getStoreId($id);
	}



	/**
	 * Build an SQL query to load the list data.
	 *
	 * @author	Sakis Terz
	 * @return	JDatabaseQuery
	 * @param	boolean $use_filters
	 * @since	1.0
	 */
	protected function getListQuery($use_filters=true)
	{
		$db=JFactory::getDbo();
		$query=$db->getQuery(true);

		//table cf_customfields
		$query->select('cf.id AS id');
		$query->select('cf.ordering AS ordering');
		$query->select('cf.vm_custom_id AS vm_custom_id');
		$query->select('cf.alias AS alias');
		$query->select('cf.published AS published');
		$query->select('cf.type_id AS type_id');
		$query->select('cf.data_type AS data_type');
		$query->select('cf.order_by AS order_by');
		$query->select('cf.order_dir AS order_dir');
		$query->select('cf.params AS params');
		$query->from('#__cf_customfields AS cf');

		//table vituemart_customfields
		$query->select('vmc.virtuemart_custom_id AS custom_id');
		$query->select('vmc.custom_title AS custom_title');
		$query->select('vmc.field_type AS field_type');
		$query->select('vmc.custom_element AS custom_element');
		$query->select('vmc.custom_desc AS custom_descr');

		//joins
		$query->join('INNER','#__virtuemart_customs AS vmc ON cf.vm_custom_id=vmc.virtuemart_custom_id');

		//set the wheres
		if($use_filters){
			$where=array();
			$where_q='';

			//display type filter
			$disp_type=$this->getState('filter.type_id');

			if (!empty($disp_type)) {
				$where[] = 'cf.type_id = ' .$db->quote($disp_type);
			}
			//published filter
			$published=$this->getState('filter.published');

			if (is_numeric($published)) {
				$where[] = 'cf.published = ' . (int) $published;
			} else if ($published === '') {
				$where[] = '(cf.published = 0 OR cf.published = 1)';
			}

			//search filter
			$search=$this->getState('filter.search');
			if (!empty($search)) {
				if (stripos($search, 'id:') === 0) {
					$where[]='cf.filter_id = '.(int) substr($search, 3);
				} else {
					$search = $db->quote('%'.$db->escape($search, true).'%');
					$where[]='(vmc.custom_title LIKE '.$search.' || vmc.custom_desc LIKE '.$search.')';
				}
			}

			if(count($where)>0) $where_q=implode(' AND ',$where);
			if($where_q)$query->where($where_q);
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering','cf.ordering');
		$orderDirn	= $this->state->get('list.direction','ASC');
		if($orderCol=='ordering')$orderCol='cf.ordering';
		$query->order($db->escape($orderCol.' '.$orderDirn));

		return $query;
	}


	/**
	 * Method to get a list of custom fields.
	 * Overridden to add a check for access levels.
	 *
	 * @return	mixed	An array of data items on success, false on failure.
	 * @since	1.0
	 */
	public function getItems()
	{
		$items	= parent::getItems();
		$customFieldsTypes=$this->getField_types();
		foreach($items as &$item){
			$params=new JRegistry;
			$params->loadString($item->params);
			$item->smart_search=$params->get('smart_search',0);
			$item->expanded=$params->get('expanded',1);
			$item->scrollbar_after=$params->get('scrollbar_after','');
			$item->slider_min_value=$params->get('slider_min_value',0);
			$item->slider_max_value=$params->get('slider_max_value',300);
			$item->filter_category_ids=$params->get('filter_category_ids',array());
			$item->field_type_string=$customFieldsTypes[$item->field_type];
			if($item->field_type=='E'){
				$custom=$this->getCustomfield($item->custom_id);
				//write also which custom element/plugin it is
				$item->field_type_string.=' ('.$custom->custom_element.')';
			}
		}
		return $items;
	}

	public function getTable($type = 'Customfilter', $prefix = 'Customfilters', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to insert the existing records to the table cf_customfields.
	 *
	 * It checks the virtuemart_customs table for new or deleted custom fields and updates the cf filters table accordingly
	 * @author	Sakis Terz
	 * @return	mixed	true on success, JError on failure
	 * @since	1.0
	 */
	public function createFilters(){
		$db=JFactory::getDbo();
		JPluginHelper::importPlugin ('vmcustom');

		//the accepted custom fields
		$params  = JComponentHelper::getParams('com_customfilters');
		$field_types_ar=$params->get('used_cf');
		if(!isset($field_types_ar)) {
			$this->insertCfTypes();
			$field_types_ar=array("S","I","B","D","T","V","E");
		}
		//get the existing custom fields from the vm table
		$custom_fields=$this->getCustomfields('*',$field_types_ar);
		//the existing custom filters
		$cf_customfilters=$this->getCustomFilters();
		$slugs=array();


		//if there are no filters
		if(empty($cf_customfilters)){
			$inserted_q=array();
			$counter=1;

			foreach($custom_fields as $vm_c){

				$query2='';
				$slug=JFilterOutput::stringURLUnicodeSlug($vm_c->custom_title);
				//check if the slug exists and format it accordingly
				while(in_array($slug, $slugs)){
					$slug=$slug.$vm_c->virtuemart_custom_id;
					$slug_counter++;
				}
				$slugs[]=$slug;
				//if not plugin
				if($vm_c->field_type!='E'){
					if($vm_c->field_type=='I')$data_type='int';
					else if ($vm_c->field_type=='D')$data_type='date';
					else $data_type='string';
					$query2="INSERT INTO #__cf_customfields (vm_custom_id,alias,ordering,published,data_type) VALUES ($vm_c->virtuemart_custom_id,".$db->quote($slug).",".$counter.",1,".$db->quote($data_type).")";
				}

				//if its plugin call the plugin hook
				else{
					$dispatcher = JDispatcher::getInstance ();
					$data_type='string';
					$name=$vm_c->custom_element;
					$virtuemart_custom_id=$vm_c->virtuemart_custom_id;
					$ret = $dispatcher->trigger ('onGenerateCustomfilters', array($name,$virtuemart_custom_id,&$data_type));
					if($ret==true && !empty($data_type)){
						$query2="INSERT INTO #__cf_customfields (vm_custom_id,alias,ordering,published,data_type)";
						$query2.=" VALUES (".$vm_c->virtuemart_custom_id.",".$db->quote($slug).",".$counter.",1,".$db->quote($data_type).")";
					}
					else continue;
				}
				$db->setQuery($query2);
				if(!$db->query()){
					$this->setError($db->getErrorMsg());
					return;
				}
				$counter++;
			}

		}else{
			//filter custom ids
			$cf_customfilters_ids=array_keys($cf_customfilters);
			//custom fields custom ids
			$vm_customfield_ids=array_keys($custom_fields);


			//new
			$tobeAdded=array_diff($vm_customfield_ids, $cf_customfilters_ids);

			if(count($tobeAdded)){
				foreach($tobeAdded as $tba){
					$query='';
					//get the title
					$current_custom=$custom_fields[$tba];
					$title=$current_custom->custom_title;
					$slug=$db->quote(JFilterOutput::stringURLUnicodeSlug($title));
					if($this->isSlugExists($slug))$slug=$slug.$tba; //check if slug exists

					if($current_custom->field_type!='E'){
						if($current_custom->field_type=='I')$data_type='int';
						else if ($current_custom->field_type=='D')$data_type='date';
						else $data_type='string';
						$query="INSERT INTO #__cf_customfields (vm_custom_id,alias,ordering,data_type) VALUES ($tba,$slug,(SELECT MAX(cf.ordering) FROM #__cf_customfields AS cf)+1,".$db->quote($data_type).")";
					}
					//plugin
					else{
						$dispatcher = JDispatcher::getInstance ();
						$data_type='string';
						$name=$current_custom->custom_element;
						$virtuemart_custom_id=$current_custom->virtuemart_custom_id;
						$ret = $dispatcher->trigger ('onGenerateCustomfilters', array($name,$virtuemart_custom_id,&$data_type));
						if($ret==true && !empty($data_type)){
							$query="INSERT INTO #__cf_customfields (vm_custom_id,alias,ordering,data_type)";
							$query.=" VALUES (".$current_custom->virtuemart_custom_id.",".$slug.",(SELECT MAX(cf.ordering) FROM #__cf_customfields AS cf)+1,".$db->quote($data_type).")";
						}
						else continue;
					}

					$db->setQuery($query);
					if(!$db->query()){
						$this->setError($db->getErrorMsg());
						return;
					}
				}
			}
			//delete or update
			$tobeDeleted=array_diff($cf_customfilters_ids, $vm_customfield_ids);
			foreach ($cf_customfilters as $cflt){
				$vm_custom_id=$cflt->vm_custom_id;

				//check if delete
				if(in_array($vm_custom_id, $tobeDeleted)){
					$query="DELETE FROM #__cf_customfields WHERE vm_custom_id =$vm_custom_id";
					$db->setQuery($query);
					if(!$db->query()){
						$this->setError($db->getErrorMsg());
						return;
					}
				}else{
					//check if we should update the data_type
					if($cflt->field_type=='E'){
						$dispatcher = JDispatcher::getInstance ();
						$data_type='string';
						$name=$cflt->custom_element;
						$ret = $dispatcher->trigger ('onGenerateCustomfilters', array($name,$vm_custom_id,&$data_type));
					} else {
						if($cflt->field_type=='I')$data_type='int';
						else if ($cflt->field_type=='D')$data_type='date';
						else $data_type='string';
					}

					if($data_type!=$cflt->data_type){//update
						$query="UPDATE `#__cf_customfields` SET `data_type`=".$db->quote($data_type)." WHERE id=$cflt->id";
						$db->setQuery($query);
						if(!$db->query()){
							$this->setError($db->getErrorMsg());
							return;
						}
					}

				}

			}

		}

		return true;
	}

	/**
	 * Get the existing filters
	 *
	 * @since 1.9.0
	 * @return	$array the filters Array
	 *
	 */
	public function getCustomFilters(){
		$query=$this->getListQuery($use_filters=false);

		$filters=$this->_getList($query);
		if(empty($filters) || !is_array($filters))return false;

		//create an assoc array with the vm_custom_id as key
		$new_array=array();
		foreach($filters as $flt){
			$new_array[$flt->vm_custom_id]=$flt;
		}
		return $new_array;
	}

	/**
	 * Get the existing custom fields
	 *
	 * @since	1.9.0
	 * @param 	string 	$fields the fields to load from the database
	 * @param	string	$custom_types a string containing the custom types
	 */
	public function getCustomfields($fields='*',$custom_types_ar){
		if(empty($custom_types_ar))return;
		$db=JFactory::getDbo();
		$new_ft_ar=array();
		if($custom_types_ar){
			$new_ft_ar=array();
			foreach($custom_types_ar as $fta){
				//if($fta!='E')
				$new_ft_ar[]=$db->quote($fta);//not plugins
				//else $load_plugin=true;
			}
		}
		if(isset($new_ft_ar))$field_types=implode(',',$new_ft_ar);

		$query=$db->getQuery(true);
		$query->select($fields);
		$query->from('#__virtuemart_customs');
		$query->where("field_type IN ($field_types)");
		$query->order("ordering");
		$db->setQuery($query);
		if(strpos($fields, '*')!==false|| strpos($fields, ',')!==false){
			$results=$db->loadObjectList();
		}
		//single field
		else $results=$db->loadColumn();
		//create an assoc array using as key the virtuemart_custom_id
		$new_results=array();
		foreach ($results as $res){
			$new_results[$res->virtuemart_custom_id]=$res;
		}
		return $new_results;
	}

	/**
	 * Checks if the slug already exists in the db
	 * @since	1.9.0
	 * @param 	string $slug
	 */
	public function isSlugExists($slug){
		$db=JFactory::getDbo();
		$query=$db->getQuery(true);
		$query->select('1');
		$query->from('#__cf_customfields');
		$query->where('alias='.$db->quote($slug));
		$db->setQuery($query);
		$result=$db->loadResult();
		return $result;
	}

	/**
	 * Get a specific custom field
	 *
	 * @since	1.9.0
	 * @param 	int $custom_id the fields to load from the database
	 *
	 */
	public function getCustomfield($custom_id){
		if(empty($custom_id))return;
		$db=JFactory::getDbo();
		$query=$db->getQuery(true);
		$query->select('*');
		$query->from('#__virtuemart_customs');
		$query->where("virtuemart_custom_id=".(int)$custom_id);
		$db->setQuery($query);
		$result=$db->loadObject();
		return $result;
	}

	/**
	 * Method to get the available filter display types.
	 * 1.Select, 2.Radios, 3.Checkboxes, 4.Links
	 *
	 * @return	array
	 * @since	1.0
	 */
	public function getAllDisplayTypes(){
		$joptions=$this->getDisplayTypes('');
		return $joptions;
	}


	/**
	 * Method to get the available filter display types.
	 * 1.Select, 2.Radios, 3.Checkboxes, 4.Links
	 *
	 * @return	array
	 * @since	1.6.1
	 */
	public function getDisplayTypes($type){
		$options=array(
		array('id' => '1','type' => 'select'),
		array('id' => '2','type' => 'radio'),
		array('id' => '3','type' => 'checkbox'),
		array('id' => '4','type' => 'link'),
		array('id' => '5','type' => 'range_inputs'),
		array('id' => '6','type' => 'range_slider'),
		array('id' => '5,6','type' => 'range_input_slider'),
		array('id' => '8','type' => 'range_calendars'),
		array('id' => '9','type' => 'color_btn_sinlge'),
		array('id' => '10','type' => 'color_btn_multi'),
		array('id' => '11','type' => 'button_single'),
		array('id' => '12','type' => 'button_multi')
		);

		$joptions=array();
		foreach($options as $opt){
			$opt=(object)$opt;
			if(!empty($type)){
				if(($type!='int' && $type!='float') && ($opt->type=='range_inputs' || $opt->type=='range_slider' || $opt->type=='range_input_slider')){}
				else if($type!='date' && ($opt->type=='range_calendars')){}
				else if(($type!='color_hex' && $type!='color_name') && ($opt->type=='color_btn_sinlge' || $opt->type=='color_btn_multi')){}
				else $joptions[]=JHTML::_('select.option',$opt->id,$opt->type);
			}else{
				$joptions[]=JHTML::_('select.option',$opt->id,$opt->type);
			}
		}
		return $joptions;
	}

	/**
	 * @return 	array	autorized Types of data
	 * @author	Sakis Terz
	 * @since	1.0
	 */
	function getField_types(){
		return array(
			'S'=>JText::_('CF_STRING'),
			'I'=>JText::_('CF_INTEGER'),
			'P'=>JText::_('PARENT'),
			'B'=>JText::_('CF_BOOLEAN'),
			'D'=>JText::_('CF_DATE'),
			'T'=>JText::_('CF_TIME'),
			'M'=>JText::_('IMAGE'),
			'V'=>JText::_('CF_CART_VARIANT'),
			'E'=>JText::_('CF_PLUGIN')
		);
	}

	/**
	 * Inserts the allowed custom field types in the extensions table as params
	 * @since 1.0
	 * @author Sakis Terz
	 */
	function insertCfTypes(){
		$db=JFactory::getDbo();
		$q='UPDATE `#__extensions`  SET `params`=\'{"used_cf":["S","I","B","D","T","V","E"]}\' WHERE `element`="com_customfilters"';
		$db->setQuery($q);
		if(!$db->query()){
			JError::raiseWarning('200', $db->getErrorMsg());
		}
	}

	/**
	 * Function that returns version info in JSON format
	 * @return string
	 * @since 1.3.1
	 */
	function getVersionInfo($updateFrequency=2){
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'update.php');
		$version_info=array();
		$html='';
		$html_current='';
		$html_outdated='';
		$pathToXML=JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'customfilters.xml';
		$installData=JApplicationHelper::parseXMLInstallFile($pathToXML);

		$updateHelper=extensionUpdateHelper::getInstance($extension='com_customfilters_pro',$targetFile='assets/lastversion.ini',$updateFrequency=2);
		$updateRegistry=$updateHelper->getData();

		if($installData['version']){
			if(is_object($updateRegistry) && $updateRegistry!==false){
				$isoutdated_code=version_compare($installData['version'], $updateRegistry->version);
				if($isoutdated_code<0){
					$html_current='<div class="cfversion">
					<span class="pbversion_label">'.JText::_('COM_CUSTOMFILTERS_LATEST_VERSION') .' : v. </span>
					<span class="cfversion_no">'.$updateRegistry->version.'</span><span> ('.$updateRegistry->date.')</span>
					</div>';
				}

				if($isoutdated_code<0)$html_outdated=' <span id="cfoutdated">!Outdated</span>';
				else $html_outdated=' <span id="cfupdated">Updated</span>';
			}

			$html.='<div class="cfversion">
			<span class="pbversion_label">'.JText::_('COM_CUSTOMFILTERS_CURRENT_VERSION') .' : v. </span>
			<span class="cfversion_no">'.$installData['version'].'</span><span> ('.$installData['creationDate'].')</span>'.$html_outdated.
			'</div>';

		}
		$html.=$html_current;
		$version_info['html']=$html;
		$version_info['status_code']=$isoutdated_code;
		return $version_info;
	}


	/**
	 * Does the user need to enter a Download ID in the component's Options page?
	 *
	 * @return bool
	 */
	public function needsDownloadID()
	{
		// Do I need a Download ID?
		$ret = true;

		JLoader::import('joomla.application.component.helper');
		$dlid = cfHelper::getValue('update_dlid', '');

		if(preg_match('/^([0-9]{1,}:)?[0-9a-f]{32}$/i', $dlid))
		{
			$ret = false;
		}

		return $ret;
	}

	/**
	 * Refreshes the Joomla! update sites for this extension as needed
	 *
	 * @return  void
	 */
	public function refreshUpdateSite()
	{
		JLoader::import('joomla.application.component.helper');
		$dlid = cfHelper::getValue('update_dlid', '');
		$extra_query = null;


		// If I have a valid Download ID I will need to use a non-blank extra_query in Joomla! 3.2+
		if (preg_match('/^([0-9]{1,}:)?[0-9a-f]{32}$/i', $dlid))
		{
			$extra_query = 'dlid=' . $dlid;
		}

		// Create the update site definition we want to store to the database
		$update_site = array(
			'name'		=> $this->name,
			'type'		=> 'extension',
			'location'	=> 'https://breakdesigns.net/index.php?option=com_ars&view=update&task=stream&format=xml&id=2',
			'enabled'	=> 1,
			'last_check_timestamp'	=> 0,
			'extra_query'	=> $extra_query
		);

		$extension_id=$this->getExtensionId();

		if (empty($extension_id))
		{
			return;
		}

		$db = $this->getDbo();
		// Get the update sites for our extension
		$query = $db->getQuery(true)
		->select($db->quoteName('update_site_id'))
		->from($db->quoteName('#__update_sites_extensions'))
		->where($db->quoteName('extension_id') . ' = ' . $db->quote($extension_id));
		$db->setQuery($query);

		$updateSiteIDs = $db->loadColumn(0);


		if (!count($updateSiteIDs))
		{
			// No update sites defined. Create a new one.
			$newSite = (object)$update_site;
			$db->insertObject('#__update_sites', $newSite);

			$id = $db->insertid();

			$updateSiteExtension = (object)array(
				'update_site_id'	=> $id,
				'extension_id'		=> $extension_id,
			);
			$db->insertObject('#__update_sites_extensions', $updateSiteExtension);
		}
		else
		{
			// Loop through all update sites
			foreach ($updateSiteIDs as $id)
			{
				$query = $db->getQuery(true)
				->select('*')
				->from($db->quoteName('#__update_sites'))
				->where($db->quoteName('update_site_id') . ' = ' . $db->quote($id));
				$db->setQuery($query);
				$aSite = $db->loadObject();

				// Does the name and location match?
				if (($aSite->name == $update_site['name']) && ($aSite->location == $update_site['location']))
				{

					// Do we have the extra_query property (J 3.2+) and does it match?
					if (property_exists($aSite, 'extra_query'))
					{
						if ($aSite->extra_query == $update_site['extra_query'])
						{
							continue;
						}
					}
					else
					{
						// Joomla! 3.1 or earlier. Updates may or may not work.
						continue;
					}
				}

				$update_site['update_site_id'] = $id;
				$newSite = (object)$update_site;
				$db->updateObject('#__update_sites', $newSite, 'update_site_id', true);
			}
		}
	}

	/**
	 * Get the update id from the updates table
	 *
	 * @param string $extension
	 * @param string $type
	 * @since 2.1.0
	 */
	public function getUpdateId($extension='com_customfilters', $type='component'){
		// Get the update ID to ourselves
		$db=$this->getDbo();
		$query = $db->getQuery(true);
		$query
		->select($db->quoteName('update_id'))
		->from($db->quoteName('#__updates'))
		->where($db->quoteName('type') . ' = ' . $db->quote($type))
		->where($db->quoteName('element') . ' = ' . $db->quote($extension));
		$db->setQuery($query);

		$update_id = $db->loadResult();

		if (empty($update_id))
		{
			return false;
		}
		return $update_id;
	}

	/**
	 * Get the update id from the updates table
	 *
	 * @param string $extension
	 * @param string $type
	 * @since 2.1.0
	 */
	public function getExtensionId($extension='com_customfilters', $type='component'){
		// Get the extension ID to ourselves
		$db=$this->getDbo();
		$query = $db->getQuery(true)
		->select($db->quoteName('extension_id'))
		->from($db->quoteName('#__extensions'))
		->where($db->quoteName('type') . ' = ' . $db->quote($type))
		->where($db->quoteName('element') . ' = ' . $db->quote($extension));
		$db->setQuery($query);

		$extension_id = $db->loadResult();

		if (empty($extension_id))
		{
			return;
		}
		return $extension_id;
	}

	/**
	 * Checks if the download ID provisioning plugin (used in j2.5) for the updates of this extension is published. If not, it will try
	 * to publish it automatically. It reports the status of the plugin as a boolean.
	 *
	 * @return  bool
	 */
	public function isUpdatePluginEnabled()
	{
		// We can't be bothered about the plugin in Joomla! 3.x
		if (version_compare(JVERSION, '3.0.0', 'gt'))
		{
			return true;
		}
	}

}
