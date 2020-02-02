<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/*
 * This trick allows us to extend the correct class, based on whether it's Joomla! 1.5 or 1.6
 */
if(!class_exists('JFakeElementBase')) {
        if(version_compare(JVERSION,'1.6.0','ge')) {
                class JFakeElementBase extends JFormField {
                        // This line is required to keep Joomla! 1.6/1.7 from complaining
                        public function getInput() {}
                }               
        } else {
                class JFakeElementBase extends JElement {}
        }
}

class JFakeElementDisp extends JFakeElementBase
{

	function fetchElement()
	{
		jimport( 'joomla.plugin.helper' );
		$enabled = JPluginHelper::isEnabled('system', 'benchmark');
		
		define('BENCHDIR', JPATH_SITE . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR . 'benchmark' . DIRECTORY_SEPARATOR . 'benchmark' . DIRECTORY_SEPARATOR . 'j35'); 
		
		$s = $data = ' define(\'BROOT\', __DIR__); include(__DIR__.DIRECTORY_SEPARATOR.\'plugins\'.DIRECTORY_SEPARATOR.\'system\'.DIRECTORY_SEPARATOR.\'benchmark\'.DIRECTORY_SEPARATOR.\'benchmark\'.DIRECTORY_SEPARATOR.\'j35\'.DIRECTORY_SEPARATOR.\'defines_include.php\');
		
		'; 
		
		if ($enabled) {
		jimport( 'joomla.filesystem.file' );
		
		
		
		
		
		
		if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'defines.php'))
		{
			if (JFile::copy(BENCHDIR.DIRECTORY_SEPARATOR.'defines.php', JPATH_SITE.DIRECTORY_SEPARATOR.'defines.php')!==false) {
			  $ok = true; 
			}
			else
			{
				$ok = false; 
			}
			
		}
		else
		{
			$x = file_get_contents(JPATH_SITE.DIRECTORY_SEPARATOR.'defines.php'); 
			$x = trim($x); 
			
			if (stripos($x, 'defines_include.php')===false) {
			$x = trim($x); 
			if (empty($x)) { $x = urldecode('%3C%3Fphp'); }
			else {
			$x2 = strrev($x); 
			if ((strlen($x2)>6)) 
			if (substr($x2, 0,2)==='>?')
			{
				
				$x = substr($x, 0, -2); 
			}
			}
			$x .= "\n"; 
			$x .= $data; 
			if (JFile::write(JPATH_SITE.DIRECTORY_SEPARATOR.'defines.php', $x)!==false) {
			 $ok = true; 
			}
			else
			{
				$ok = false; 
			}
			}
			else
			{
				$ok = true; 
			}
			
			
		}
		if ($ok) {
		  $html = 'defines.php are now installed';
		}
		else
		{
			$html = ' error installing defines.php ! '; 
		}
		
		}
		else{
			$x = file_get_contents(JPATH_SITE.DIRECTORY_SEPARATOR.'defines.php'); 
			$x2 = file_get_contents(BENCHDIR.DIRECTORY_SEPARATOR.'defines.php', JPATH_SITE.DIRECTORY_SEPARATOR.'defines.php'); 
			
			if ($x === $x2) {
			  if (JFile::delete(JPATH_SITE.DIRECTORY_SEPARATOR.'defines.php') !== false) {
			   $ok = true; 
			  }
			}
			else
			{
				
				$x = str_replace($s, '', $x); 
				if (JFile::write(JPATH_SITE.DIRECTORY_SEPARATOR.'defines.php', $x)!==false) {
					$ok = true; 
				}
			}
			
			if (!empty($ok)) {
			 $html = ' benchmark code removed (or not found) from defines.php'; 
			}
			else
			{
				$html = ' please remove benchmark code from defines.php manually !'; 
			}
			
		}
		
	
	return $html;
	}
	// Joomla! 1.6
	function getInput()
	{
	   return $this->fetchElement(); 
	}
}

/*
 * Part two of our trick; we define the proper element name, depending on whether it's Joomla! 1.5 or 1.6
 */
if(version_compare(JVERSION,'1.6.0','ge')) {
        class JFormFieldDisp extends JFakeElementDisp {}
} else {
        class JElementDisp extends JFakeElementDisp {}                
}