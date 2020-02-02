<?php
/*------------------------------------
* -Netbase- Advanced Virtuemart Invoices for Virtuemart
* Author    CMSMart Team
* Copyright (C) 2012 http://cmsmart.net. All Rights Reserved.
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* Websites: http://cmsmart.net
* Email: team@cmsmart.net
* Technical Support:  Forum - http://bloorum.com/forums
-----------------------------------------------------*/

class InvoiceConfig {

	function __construct($xml,$data)
	{
		if (COM_NETBASEVM_EXTEND_ISJ16)
		{
			//TODO: check if initial values are got from XML in J1.6
			//edit xml file to J!1.6 format 
			$xml = file_get_contents($xml);
			$xml = str_replace('<config>','<form>',$xml);
			$xml = str_replace('</config>','</form>',$xml);
			$xml = str_replace('<params','<fieldset',$xml);
			$xml = str_replace('</params>','</fieldset>',$xml);
			$xml = str_replace('<param ','<field ',$xml);
			$xml = str_replace('</param>','</field>',$xml);
			jimport('joomla.form.form');
			$this->params = JForm::getInstance('com_vminvoice.config', $xml);
			$this->params->bind(json_decode($data));
		}
		else
		{
			$this->params = new JParameter($data, $xml);
			 
			//assign default values in J!1.5
            foreach ($this->params->_xml as $group) {
            	foreach ($group->_children as $child){
            		$key = $child->_attributes['name'];
            		if (is_null($this->params->get($key,null))) //not defined, set default from xml
            			if (isset($child->_attributes['default']))
            				$this->params->def($key,$child->_attributes['default']);
            	}
            }			
		}
	}
	
	/**
	 * Get one param
	 * @param unknown_type $param
	 * @param unknown_type $default
	 */
	function get($param,$default=null)
	{
		static $params; //cache
		
		if (!isset($params[$param.'.'.$default])) 
		{
			if (COM_NETBASEVM_EXTEND_ISJ16){
				if (is_null($val = $this->params->getValue($param,null,null))){
					$val = $this->params->getFieldAttribute($param,'default',$default); //get default value from xml
					if (strpos($val,';')!==false)
						$val = explode(';',$val);
				}
				$params[$param.'.'.$default] = $val;
			}
			else
				$params[$param.'.'.$default] = $this->params->get($param,$default);
		}
		return $params[$param.'.'.$default];
	}
	
	/**
     * Get all current params as asociative array
     */
    function getAllParams(){
    	
    	$allParams = array();
    	
    	if (COM_NETBASEVM_EXTEND_ISJ16)
    	{
    		$fields = $this->params->getFieldset();
    		
    		foreach ($fields as $key => $val)
    			$allParams[$key] = $this->get($key);
    	}
    	else
    	{
    		foreach ($this->params->_xml as $group) {
	        	foreach ($group->_children as $child){
	            	$key = $child->_attributes['name'];
	            	$allParams[$key] = $this->params->get($key);
	            }
	       	}
    	}
    	return $allParams;
    }
	
}

?>