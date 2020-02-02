<?php
/** 
 * @version		$Id: opc.php$
 * @copyright	Copyright (C) 2005 - 2014 RuposTel.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');
jimport( 'joomla.session.session' );



//require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'language.php'); 
class plgSystemSystemlogo extends JPlugin
{
	var $url = ''; 
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->url = '//'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; 
		
	}
	
	public function onAfterRender()
	{


		


		
		
		 $buffer = JResponse::getBody();
		
		for ($i=1; $i<=50; $i++) { 
		  $z = $this->params->get('testurl'.$i, ''); 
		  $b = $this->params->get('logourl'.$i, ''); 
		  if (empty($b)) continue; 
		  
		  $class = $this->params->get('logoclass', ''); 
		  $z = str_replace('http://', '//', $z); 
		  $z = str_replace('https://', '//', $z); 
		  $b = str_replace('http://', '//', $b); 
		  $b = str_replace('https://', '//', $b); 
		  
		  if ((!empty($z)) && (!empty($b))) { 

		
		  
		   $u1 = urldecode($this->url); 
		      if ((stripos($this->url, $z) !== false) || ((stripos($u1, $z) !== false))) { 
			  
				$css = '<style type="text/css"> .'.$class.' { background-image: url('.$b.') !important; } </style>'; 
				$zx = stripos($buffer, '</head'); 
				
				if ($zx !== false) {
				   $buffer2 = substr($buffer, 0, $zx).$css.substr($buffer, $zx); 
				   JResponse::setBody($buffer2);
				   return true; 
				   
				   
				}
			 }
		  }
		}
		 
		
			
		
		
		

		
		
		
	}
	
}
	