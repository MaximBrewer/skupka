<?php
/**
 * @copyright	Copyright (C) 2005 - 2013 RuposTel.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;


class plgSystemSimplecdn extends JPlugin
{

	/**
	 * Constructor
	 *
	 */
	function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);

		
	}
	
	
	function onAfterRender()
	{
		
		
		$domain = $this->params->get('domainname', ''); 
		if (empty($domain)) return; 
		
		$domain2 = ''; 
		if ($domain != $_SERVER['HTTP_HOST']) {
		 $domain2 = $_SERVER['HTTP_HOST']; 
		}
		
		$buffer = JResponse::getBody();
		$folder = $this->params->get('directory', '/'); 
		if (empty($folder)) $folder = '/'; 
		if (substr($folder, -1) !== '/') $folder .= '/'; 
		
		
		 $arrs = array(
		 'src="http://'.$domain.$folder, 
		 'src="https://'.$domain.$folder, 
		 'src="'.$folder.'templates', 
		 'src="'.$folder.'images', 
		 'src="'.$folder.'plugins', 
		 'src="'.$folder.'media', 
		 'src="'.$folder.'components', 
		 'src="'.$folder.'t3-assets', 
		 'src="'.$folder.'cache', 
		 'src="'.$folder.'libraries', 
		 
		 
		 'rel="stylesheet" href="https://'.$domain.$folder, 
		 'rel="stylesheet" href="http://'.$domain.$folder, 
		 'rel="stylesheet" href="//'.$domain.$folder, 
		 'rel="stylesheet" href="'.$folder.'templates', 
		 'rel="stylesheet" href="'.$folder.'images', 
		 'rel="stylesheet" href="'.$folder.'plugins', 
		 'rel="stylesheet" href="'.$folder.'media', 
		 'rel="stylesheet" href="'.$folder.'components', 
		 'rel="stylesheet" href="'.$folder.'t3-assets', 
		 
		 
		 'type="text/css" href="http://'.$domain.$folder, 
		 'type="text/css" href="https://'.$domain.$folder, 
		 'type="text/css" href="//'.$domain.$folder, 
		 'type="text/css" href="'.$folder.'templates', 
		 'type="text/css" href="'.$folder.'images', 
		 'type="text/css" href="'.$folder.'plugins', 
		 'type="text/css" href="'.$folder.'media', 
		 'type="text/css" href="'.$folder.'components', 
		 'type="text/css" href="'.$folder.'t3-assets', 
		 
		 ); 
		 
		 
		 if (!empty($domain2)) {
			 $arrs[] =  'src="http://'.$domain.$folder;
		     $arrs[] = 'src="https://'.$domain.$folder;
			 $arrs[] = 'rel="stylesheet" href="https://'.$domain.$folder;
			 $arrs[] = 'rel="stylesheet" href="http://'.$domain.$folder;
			 $arrs[] = 'rel="stylesheet" href="//'.$domain.$folder;
			 
			 $arrs[] =  'type="text/css" href="http://'.$domain.$folder;
			 $arrs[] =  'type="text/css" href="https://'.$domain.$folder;
			 $arrs[] =  'type="text/css" href="//'.$domain.$folder;
			 
		 }
		 
		 //
		 $cdn = $this->params->get('cdndomainname', $domain); 
		 $cdndirectory = $this->params->get('cdndirectory', '/'); 
		
		 if (empty($cdndirectory)) $cdndirectory = '/'; 
		 if (substr($cdndirectory, -1) !== '/') $cdndirectory .= '/'; 
		 
		 $arrr = array(
		 'src="//'.$cdn.$cdndirectory, 
		 'src="//'.$cdn.$cdndirectory, 
		 'src="//'.$cdn.$cdndirectory.'templates', 
		 'src="//'.$cdn.$cdndirectory.'images', 
		 'src="//'.$cdn.$cdndirectory.'plugins', 
		 'src="//'.$cdn.$cdndirectory.'media', 
		 'src="//'.$cdn.$cdndirectory.'components',	
		 'src="//'.$cdn.$cdndirectory.'t3-assets',	
		 'src="//'.$cdn.$cdndirectory.'cache',	
		 'src="//'.$cdn.$cdndirectory.'libraries',	
		 
		 
		 
		 'rel="stylesheet" href="//'.$cdn.$cdndirectory, 
		 'rel="stylesheet" href="//'.$cdn.$cdndirectory, 
		 'rel="stylesheet" href="//'.$cdn.$cdndirectory, 
		 'rel="stylesheet" href="//'.$cdn.$cdndirectory.'templates', 
		 'rel="stylesheet" href="//'.$cdn.$cdndirectory.'images', 
		 'rel="stylesheet" href="//'.$cdn.$cdndirectory.'plugins', 
		 'rel="stylesheet" href="//'.$cdn.$cdndirectory.'media', 
		 'rel="stylesheet" href="//'.$cdn.$cdndirectory.'components', 
		 'rel="stylesheet" href="//'.$cdn.$cdndirectory.'t3-assets', 
		 
		 
		 'type="text/css" href="//'.$cdn.$cdndirectory, 
		 'type="text/css" href="//'.$cdn.$cdndirectory, 
		 'type="text/css" href="//'.$cdn.$cdndirectory, 
		 'type="text/css" href="//'.$cdn.$cdndirectory.'templates', 
		 'type="text/css" href="//'.$cdn.$cdndirectory.'images', 
		 'type="text/css" href="//'.$cdn.$cdndirectory.'plugins', 
		 'type="text/css" href="//'.$cdn.$cdndirectory.'media', 
	     'type="text/css" href="//'.$cdn.$cdndirectory.'components', 		 
		 'type="text/css" href="//'.$cdn.$cdndirectory.'t3-assets', 		 
		 
		 
		 ); 
		 
		  if (!empty($domain2)) {
		    $arrs[] =  'src="//'.$cdn.$cdndirectory;
		     $arrs[] = 'src="//'.$cdn.$cdndirectory;
			 $arrs[] = 'rel="stylesheet" href="//'.$cdn.$cdndirectory;
			 $arrs[] = 'rel="stylesheet" href="//'.$cdn.$cdndirectory;
			 $arrs[] = 'rel="stylesheet" href="//'.$cdn.$cdndirectory;
			 
			 $arrs[] =  'type="text/css" href="//'.$cdn.$cdndirectory;
			 $arrs[] =  'type="text/css" href="//'.$cdn.$cdndirectory;
			 $arrs[] =  'type="text/css" href="//'.$cdn.$cdndirectory;
		  }
		 
		 $buffer = str_replace($arrs, $arrr, $buffer); 
		 JResponse::setBody($buffer);
		
	}
}
