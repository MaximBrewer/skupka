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

defined('_JEXEC') or die('Restrict Access');
jimport('joomla.application.component.view');

class VMInvoiceViewConfig extends JViewLegacy
{

	function display($tpl = null)
	{ 			
		$this->type = JRequest::getVar('type','');
		
		$title='COM_VMINVOICE_CONFIGURATION';
		$active=null;
		
		if ($this->type=='general'){
			$title='COM_VMINVOICE_GLOBAL_CONFIGURATION';
			$active=1;}
		elseif ($this->type=='invoice'){
			$title='COM_VMINVOICE_INVOICE_CONFIGURATION';
			$active=2;}
		elseif ($this->type=='dn'){
			$title='COM_VMINVOICE_DELIVERY_NOTE_CONFIGURATION';
			$active=3;}

		InvoiceHelper::setSubmenu($active);
			
		JToolBarHelper::title('VM Invoice: ' . JText::_($title), 'config');
		JToolBarHelper::save('save', 'COM_VMINVOICE_SAVE');
		JToolBarHelper::cancel('cancel', 'COM_VMINVOICE_CLOSE');
		
		$config  = $this->get('Data');
                $this->vminvoice_config=$config;

		$this->installNewTCPDFFonts();
		$this->availableFonts = $this->getTCPDFFonts();
			
    	parent::display($tpl);
	}
	
    /**
     * Check for new font files uploaded to TCPDF fonts folder and install them.
     */
    function installNewTCPDFFonts()
    {
    	
    	//find all *.ttf files in fonts folder
    	$fontsPath = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_vminvoice'.DS.'libraries'.DS.'tcpdf'.DS.'fonts'.DS;
    	
    	if (function_exists('glob'))
    	{
    		$ttfFiles = glob($fontsPath.'*.ttf');
    		if ($ttfFiles && count($ttfFiles))
    			foreach ($ttfFiles as $key => $file)
    				$ttfFiles[$key] = pathinfo($file, PATHINFO_FILENAME);
    	}
    	else
    	{
    		$ttfFiles = array();
    		if (!($dir = opendir($fontsPath)))
    			JError::raiseWarning(0,'Opendir: Cannot search fonts folder for new fonts');
    		else
		    	while (false !== ($file = readdir($dir)))
		        	if (preg_match('#^(.+)\.ttf$#i',$file, $match))
		        		$ttfFiles[] = $match[1];
    	}

    	if ($ttfFiles && count($ttfFiles)) foreach ($ttfFiles as $file)
    	{
			$checkFile = strtolower(str_replace('-','',$file)); //how TCPDF shorten uploaded file name
    		
    		//not all tcpdf files presented, install
    		if (!file_exists($fontsPath.$checkFile.'.z') || !file_exists($fontsPath.$checkFile.'.ctg.z') || !file_exists($fontsPath.$checkFile.'.php'))
    		{
    			
    			if (!is_writable($fontsPath) && !chmod($fontsPath, 0777)){
    				JError::raiseWarning(0, 'Cannot install new fonts: Folder '.$fontsPath.' is not writable');
    				break;}
    				
    			if (!isset($tcpdf)){
    				require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_vminvoice'.DS.'helpers'.DS.'invoicetcpdf.php');
    				$tcpdf = new TCPDF();
    			}
    			
    			$mainframe = JFactory::getApplication();
    			if ($tcpdf->addTTFfont($fontsPath.$file.'.ttf'))
    				$mainframe->enqueueMessage(JText::sprintf('COM_VMINVOICE_FONT_INSTALLED', ucfirst($file)));
    			else
    				JError::raiseWarning(0, JText::sprintf('COM_VMINVOICE_FONT_NOT_INSTALLED', ucfirst($file)));
    		}
    	}
    }
    
    /**
     * Get available fonts for TCPDF
     */
    function getTCPDFFonts()
    {
    	
    	
        //find all *.php files in fonts folder
    	$fontsPath = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_vminvoice'.DS.'libraries'.DS.'tcpdf'.DS.'fonts'.DS;
    	$phpFiles = array();
    	
    	if (function_exists('glob'))
    	{
    		if (($files = glob($fontsPath.'*.php'))===false)
    			JError::raiseWarning(0,'GLOB: Cannot search fonts folder for available fonts');
    		else
    			foreach ($files as $file){
    				$filename =  pathinfo($file, PATHINFO_FILENAME);
    				$phpFiles[] = $filename;
    			}
    	}
    	else
    	{
    		if (!($dir = opendir($fontsPath)))
    			JError::raiseWarning(0,'Opendir: Cannot search fonts folder for available fonts');
    		else
		    	while (false !== ($file = readdir($dir)))
		        	if (preg_match('#^(.+)\.php$#i',$file, $match))
		        		$phpFiles[] = $match[1];
    	}
    	
    	asort($phpFiles); //important: sort, because b/i files must come later than original
    	
    	$fonts = array();
    	if (count($phpFiles)) foreach ($phpFiles as $key => $file)
    	{
    		//check if it has .z and .ctg.z neigbours (edit: not neccessary because of core fonts, they have only php files)
    		/*
    		if (!file_exists($fontsPath.$file.'.z') || !file_exists($fontsPath.$file.'.ctg.z'))
    			continue;
    		*/
    		$fonts[$file] = ucfirst(str_replace(array('-','_'),' ',$file));
    	}
    	
    	foreach ($fonts as $key => $val)
    	{
    		//if it is only b/i variantion of existing font, delete from list
    		if (preg_match('#^(.+)bi$#i', $key, $match) || preg_match('#^(.+)(b|i)$#i', $key, $match)) 
    			if (isset($fonts[$match[1]]))
    				unset($fonts[$key]);
    	}
    	
    	return $fonts;
    }
    
    function getMaxUploadSize()
    {
    	$sizes = array();
    	foreach (array('post_max_size','upload_max_filesize','memory_limit') as $iniParam)
    		if (($val = ini_get($iniParam))>0)
    			$sizes[] = $val;
    	
    	if (!$sizes)
    		return false;
    	
    	if (!($bytes = $this->return_bytes(min($sizes))))
    		return false;
    	
    	return $this->convertSize($bytes);
    }
    
    //http://php.net/manual/en/function.ini-get.php#106518
    private function return_bytes ($val)
    {
    	if(empty($val))return 0;
    
    	$val = trim($val);
    
    	preg_match('#([0-9]+)[\s]*([a-z]+)#i', $val, $matches);
    
    	$last = '';
    	if(isset($matches[2])){
    		$last = $matches[2];
    	}
    
    	if(isset($matches[1])){
    		$val = (int) $matches[1];
    	}
    
    	switch (strtolower($last))
    	{
    		case 'g':
    		case 'gb':
    			$val *= 1024;
    		case 'm':
    		case 'mb':
    			$val *= 1024;
    		case 'k':
    		case 'kb':
    			$val *= 1024;
    	}
    
    	return (int) $val;
    }

    private function convertSize($size)
    {
    	$unit=array('b','kb','mb','gb','tb','pb');
    	return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
    }
    
}
?>