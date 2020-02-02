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

defined('_JEXEC') or die();
jimport('joomla.application.component.model');

class VMInvoiceModelConfig extends JModelLegacy //after edit toolbar,open new form in form.php in (view vminvoice)
{
    function __construct()
    {
        parent::__construct();
        //$array = JRequest::getVar('cid', 0, '', 'array');
        $this->setId(1);    
        
        //define params which doesn't affect pdf appearance. It is important for pre-caching pdfs
        $this->notAppearanceParams = array('auto_manual','order_status','pre_def_time_h','pre_def_time','debug','cache_pdf', //General config
        	'default_vendor','default_currency','default_status', //Order editing
       		'allow_prefix_editing', 'start_number', 'starting_order', //Invoice config
        	'delivery_note', 'send_both', //Delivery note config
	        'use_conf','mail_send_to','admin_email','from_name','mail_subject','mail_message', //Mailing config
	        'download_id','version_checker'); //Registration config
    }

    function setId ($id)    
    {        
        $this->_id = $id;
        $this->_data = null;    
    }

    function &getData ()
    {        
        if (empty($this->_data)) {            
            $query = ' SELECT * FROM #__vminvoice_config WHERE id = 1';            
            $this->_db->setQuery($query);            
            $this->_data = $this->_db->loadObject();        
        }
        
        if (! $this->_data) {            
            $this->_data = new stdClass();            
            $this->_data->id = 0;              
            $this->_data->params = '';      
        }
        
        $this->_data->template_items = explode('_TEMPLATE_ITEMS_SEPARATOR_',$this->_data->template_items);
        $this->_data->template_dn_items = explode('_TEMPLATE_ITEMS_SEPARATOR_',$this->_data->template_dn_items);
        
        return $this->_data;    
    }

    /**
     * Checks tables if they have same number of columns
     * 
     * @param string $header
     * @param string $item
     */
    function checkItemsTemplate($header,$item)
    {
    	$colsHeader = InvoiceHelper::getNumCols($header);
		if (!is_numeric($colsHeader))
			return $colsHeader.' items header'.$header;
 
    	$colsItems = InvoiceHelper::getNumCols($item);
		if (!is_numeric($colsItems))
			return $colsItems.' item row'.$item;
			
		if ($colsHeader!==$colsItems)
			return 'Not same number of columns for header and row';
			
		return true;
    }
    
    function store ()    
    {
        $row =  $this->getTable();
        $data = JRequest::get('post',JREQUEST_ALLOWHTML);

        //TODO: remove widths from item row. or make it same as header. or make it one table?
        //explode template html to its parts
        if (isset($data['template_'])){
        	$template = explode('<hr class="system-pagebreak" />',$data['template_']);
        	if (count($template)!=3){
				JError::raiseWarning(0,'Template must have 2 pagebreaks to separate header, body and footer');
				return false;}
        	$data['template_header']=trim($template[0]);
        	$data['template_body']=trim($template[1]);
        	$data['template_footer']=trim($template[2]);
        }
        if (isset($data['template_dn_'])){
        	$template = explode('<hr class="system-pagebreak" />',$data['template_dn_']);
        	if (count($template)!=3){
				JError::raiseWarning(0,'Template must have 2 pagebreaks to separate header, body and footer');
				return false;}
        	$data['template_dn_header']=trim($template[0]);
        	$data['template_dn_body']=trim($template[1]);
        	$data['template_dn_footer']=trim($template[2]);
        }
        
		//check & implode items template
        if (isset($data['template_items']) && is_array($data['template_items']))
        {
        	$data['template_items'][0] = preg_replace('#^.*(<\s*table[^>]*>.*<\s*\/\s*table\s*>).*$#isU','$1',$data['template_items'][0]); //keep only table tag
        	$data['template_items'][1] = preg_replace('#^.*(<\s*table[^>]*>.*<\s*\/\s*table\s*>).*$#isU','$1',$data['template_items'][1]);
        	
        	$check = $this->checkItemsTemplate($data['template_items'][0],$data['template_items'][1]);
        	if ($check!==true){
				JError::raiseWarning(0,'Invoice items template error: '.$check);
				return false;}
				
			$data['template_items'] = implode('_TEMPLATE_ITEMS_SEPARATOR_',$data['template_items']);
        }

        if (isset($data['template_dn_items']) && is_array($data['template_dn_items']))
        {
        	$data['template_dn_items'][0] = preg_replace('#^.*(<\s*table[^>]*>.*<\s*\/\s*table\s*>).*$#isU','$1',$data['template_dn_items'][0]); //keep only table tag
        	$data['template_dn_items'][1] = preg_replace('#^.*(<\s*table[^>]*>.*<\s*\/\s*table\s*>).*$#isU','$1',$data['template_dn_items'][1]);
        	
        	$check = $this->checkItemsTemplate($data['template_dn_items'][0],$data['template_dn_items'][1]);
        	if ($check!==true){
				JError::raiseWarning(0,'Delivery note items template error: '.$check);
				return false;}
				
			$data['template_dn_items'] = implode('_TEMPLATE_ITEMS_SEPARATOR_',$data['template_dn_items']);
        }
        
        
        if (! $row->bind($data)) {
            $this->setError($row->getError());            
            return false;        
        }       	
        
        // Save params
        $params = JRequest::getVar( 'params', array(), 'post', 'array' );
        if (is_array( $params ) AND $params) {

        	
        	// Implode ordering of items footer
            if (isset($params['items_footer_ordering']))
            	$params['items_footer_ordering'] = implode(',',$params['items_footer_ordering']);
            if (isset($params['items_footer_dn_ordering']))
            	$params['items_footer_dn_ordering'] = implode(',',$params['items_footer_dn_ordering']);	
            
            
        	$originalData = $this->getData(); //load original db data for templates
        	
        	$dbParams = InvoiceHelper::getParams();
        	
           	$oldParams = $dbParams->getAllParams(); //get all original params in array
			
            $reg = new JRegistry();
            $params = array_merge($oldParams, $params); //overwrite old params by news
            $reg->loadArray($params);
            $row->params = $reg->toString(); //store all new params to table
            
	        //new parameters array
	        $newParams = $reg->toArray();

	        /* determine if update last_appearance_change */ 
	        
	        //determine if was changed some appearance parameter
	        foreach ($newParams as $key => $value)
	        	if (!in_array($key,$this->notAppearanceParams) AND ($value!=$oldParams[$key]))
	        		 $row->last_appearance_change=time();
	        		 
	       	//check also is was changed template
	        $template_vars = array('template_header','template_body','template_items','template_footer');
	        if (InvoiceHelper::getParams()->get('delivery_note')==1)
	        	$template_vars = array_merge($template_vars,array('template_dn_header','template_dn_body','template_dn_items','template_dn_footer'));

	        foreach ($template_vars as $template_var)
	        	if (isset($data[$template_var]) AND $originalData->$template_var!=$data[$template_var])
	        		$row->last_appearance_change=time();
        } 
        else{
            $this->setError('No params array in POST.');            
            return false;        
        }

        $fontFile = JRequest::getVar('new_font_file', '', 'files', 'array' );

        if (!empty($fontFile['tmp_name']))
        {
        	$fontsPath = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_vminvoice'.DS.'libraries'.DS.'tcpdf'.DS.'fonts'.DS;
        	$mainframe = JFactory::getApplication();
        	        	
        	if (!preg_match('#^(.+)\.(ttf|php|z)$#',$fontFile['name'], $matches))
        		JError::raiseWarning(0,JText::_('COM_VMINVOICE_ONLY_TTF_FONTS_ALLOWED'));
        	else {
        		$fontName = strtolower(preg_replace('#[^\w]#', '', $matches[1]).'.'.$matches[2]); //safe file name, only letters and numbers
	        	if (!is_writable($fontsPath) && !chmod($fontsPath, 0777))
	        		JError::raiseWarning(0,JText::sprintf('COM_VMINVOICE_DIRECTORY_WRITABLE',$fontsPath));
	        	elseif (!move_uploaded_file($fontFile['tmp_name'], $fontsPath.$fontName))
	        		JError::raiseWarning(0,JText::_('COM_VMINVOICE_CANNOT_UPLOAD_FONT'));
	        	else
	        		$mainframe->enqueueMessage(JText::sprintf('COM_VMINVOICE_FONT_UPLOADED',$fontName));
        	}
        }
        
        
        if (! $row->check()) {
            $this->setError($row->getErrorMsg());
            return false;
        }
        
        if (!$row->store()) {            
            JError::raiseError(500, $row->getError() ); //...
            $this->setError($row->getError());            
            return false;        
        }
        
        return true;
    }
    
    function restoreTemplate($dn='')
    {
    	//prepare array for db
		$templates = array();
		$templates['id']=1;
		
		//set template from restore column
		$query = ' SELECT template_'.$dn.'restore FROM #__vminvoice_config WHERE id = 1';     
        $this->_db->setQuery($query);            
		$restore = explode('_TEMPLATE_SEPARATOR_',$this->_db->loadResult());
        $templates['template_'.$dn.'header']=$restore[0];
        $templates['template_'.$dn.'body']=$restore[1];
        $templates['template_'.$dn.'items']=$restore[2];
        $templates['template_'.$dn.'footer']=$restore[3];
        
        //restore footer rows ordering
        
		$dbParams = InvoiceHelper::getParams();
        $oldParams = $dbParams->getAllParams(); //get all original params in array 	
		$reg = new JRegistry();
        $reg->loadArray($oldParams);
        $reg->set('items_footer_'.$dn.'ordering', implode(',',InvoiceHelper::getItemsFooterOrdering($dn=='dn_', true)));
        $templates['params'] = $reg->toString();
        
        
        //store to table
        $row =  $this->getTable();
        if (!$row->save($templates)) {
            $this->setError($row->getError());            
            return false;        
        }
        
        return true;
    }
    
    //just dev function
    function makeRestore($dn='')
    {  	
    	$query = ' SELECT template_'.$dn.'header,template_'.$dn.'body,template_'.$dn.'items,template_'.$dn.'footer FROM #__vminvoice_config WHERE id = 1';    
        $this->_db->setQuery($query);  
        $res = $this->_db->loadRow();          
  
		$templates = array('id' => 1);
		$templates['template_'.$dn.'restore']=implode('_TEMPLATE_SEPARATOR_',$res);
        
        $row =  $this->getTable();

        if (! $row->save($templates)) {
        	JError::raiseError(0, 'Cannot make template restore point. '.$row->getError() );               
            return false;        
        }
        return true;
    }
    
}
?>
