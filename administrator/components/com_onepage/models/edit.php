<?php
/**
 * @version		$Id: cache.php 21518 2011-06-10 21:38:12Z chdemko $
 * @package		Joomla.Administrator
 * @subpackage	com_cache
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Cache Model
 *
 * @package		Joomla.Administrator
 * @subpackage	com_cache
 * @since		1.6
 */
class JModelEdit extends OPCModel
{
  function mergeA(&$priorityA, &$secondA)
  {
    foreach ($secondA as $k=>$a)
    {
      if (empty($priorityA[$k])) $priorityA[$k] = $a;
    }
    return $priorityA;
  }
  
  
  // returns true if at least some translation exists in DB
  function checkDB($component, $type, $lang)
  {
    $this->createtable(); 
    $db = JFactory::getDBO(); 
    $q = 'select lang from #__vmtranslator_translations where lang = "'.$db->escape($lang).'" and entity = "'.$db->escape($component).'" and type = "'.$db->escape($type).'"  limit 0,1'; 
    $db->setQuery($q); 
    $x = $db->loadResult(); 
    if (empty($x)) return false;
    else 
    {
    return true; 
    }
  }
  
  function insertSingle($val, $key, $component, $type='site', $lang, $user='')
  {
  
  if (empty($user))
  {
   $usero = JFactory::getUser(); 
   $user = $usero->username; 
  }
  
  if (empty($key) || (empty($component)) || (empty($type))) return; 
  
  
     $val = urlencode($val); 
  
     $db = JFactory::getDBO(); 
	 $q = "insert into #__vmtranslator_translations (`id`, `entity`, `type`, `var`, `translation`, `lang`, `user`) values (NULL, '".$db->escape($component)."', '".$db->escape($type)."', '".$db->escape($key)."', '".$db->escape($val)."', '".$db->escape($lang)."', '".$db->escape($user)."' ) ";
	 $db->setQuery($q); 
	 $db->query(); 
	 $err = $db->getErrorMsg(); 
	 if (!empty($err)) {var_dump($err); die('err'); }
	 
	 return $db->insertid();
  }
  
  function fillDB($component, $type, $lang, &$arr, $user)
  {
	$this->createtable(); 
    $db = JFactory::getDBO(); 
	// get rid of reference, copy the array
   	  $cp = unserialize(serialize($arr));
	  foreach ($cp as $k => $v)
	  {
	    
	    $key = urlencode($k); 
	    $val = urlencode($v);
	    if (strpos($key, '_translationid_')!==false)
		 {
		   die('123'); 
		 }
		 if (empty($key) || (empty($component)) || (empty($type))) return; 
		 
		 
	    $q = "insert into #__vmtranslator_translations (`id`, `entity`, `type`, `var`, `translation`, `lang`, `user`) values (NULL, '".$db->escape($component)."', '".$db->escape($type)."', '".$db->escape($key)."', '".$db->escape($val)."', '".$db->escape($lang)."', '".$db->escape($user)."' ) ";
	    $db->setQuery($q); 
	    $db->query(); 
	    $err = $db->getErrorMsg(); 
	    if (!empty($err)) {var_dump($err); die('err'); }
	    
	    $id = $db->insertid(); 
	    $arr[$k.'_translationid_'.$id] = $arr[$k]; 
		
	    unset($arr[$k]); 
	  }
	  unset($cp); 
  }
  function flushTable()
  {
   return; 
   $db = JFactory::getDBO(); 
   $q = 'delete from #__vmtranslator_translations where 1 limit 9999999'; 
   $db->setQuery($q); 
   $db->query(); 
   $err = $db->getErrorMsg(); 
   if (!empty($err)) { var_dump($err); die(); }
   
  }
  
  function getTranlations($component, $type, $lang, &$arrr, $arr1o=array())
  {
    $this->createtable(); 
    $db = JFactory::getDBO(); 
    $arr = array(); 
	echo JText::sprintf('COM_ONEPAGE_FETCHING_TRANSLATIONS', $component, $type, $lang).' <br />'; 
    $q = "select * from #__vmtranslator_translations where entity = '".$db->escape($component)."' and type = '".$db->escape($type)."' and lang = '".$db->escape($lang)."' order by id asc ";  
    $db->setQuery($q); 
    $ret = $db->loadAssocList(); 
	
    $err = $db->getErrorMsg(); 
    if (!empty($err)) { var_dump($err); die('err 2 models\edit.php'); }
	
    foreach ($ret as $k=>$v)
    {
	  // don't fetch translationid variables: 
	  if (strpos($v['var'], '_translationid_')!==false) continue; 
      $key = $v['var']; //urldecode($v['var']).'_translationid_'.$v['id']; 
	  $purekey = $v['var']; 
      $val = urldecode($v['translation']);
      //$arr[$key] = $val; 
      //if (!isset($arr[$purekey.'_defaulttrans']))
	  if (!isset($arr[$key]))
      {
       $arr[$key] = array(); 
       $arr[$key]['id'] = $v['id']; 
       $arr[$key]['var'] = urldecode($v['var']); 
       $arr[$key]['translation'] = $val;
       $arr[$key]['other'] = array(); 
      }
      else
      $arr[$key]['other'][] = $v['id']; 
      
      /*
      if (empty($arr[$key])) $arr[$key] = $val; 
      else 
      {
        for ($i = 0; $i<100; $i++)
        {
          if (empty($arr[$key.'_trvariants_'.$i]))
          {
            $arr[$key.'_trvariants_'.$i] = $val; 
            break;
          }
        }
      }
      */
    }
	
			      $usero = JFactory::getUser(); 
			  $user = $usero->username; 


	foreach ($arrr as $k2=>$v2)
	 {
	   
	   if (!isset($arr[$k2]) && (strpos($k2, '_translationid_')===false))
	    {
		 //function insertSingle($val, $component, $type='site', $lang, $user='')
		
		  $id = $this->insertSingle($v2, $k2, $component, $type, $lang); 
		  
		  
		  $arr[$k2] = array(); 
		  $arr[$k2]['id'] = $id; 
          $arr[$k2]['var'] = $k2; 
          $arr[$k2]['translation'] = $arrr[$k2];
          $arr[$k2]['other'] = array(); 
		  
		}
		else
		{
		  
		   // if db['text'] == from['text']
		   // but toini['text'] != db['text']
		   
		  if (!empty($arr1o[$k2]))
		  if ($arrr[$k2] != $arr[$k2])
		  if ($arr[$k2]['translation'] == $arr1o[$k2])
		   {
		   $id = $arr[$k2]['id']; 
		   //function insertUpdate($component, $type, $lang, $user, $id, $var, $value)
		   //$id = $this->insertUpdate($v2, $k2, $component, $type, $lang); 
			
		   // $this->insertUpdate($component, $type, $lang, $user, $id, $k2, $v2); 
		  $arr[$k2] = array(); 
		  $arr[$k2]['id'] = $id; 
          $arr[$k2]['var'] = $k2; 
          $arr[$k2]['translation'] = $arrr[$k2];
          $arr[$k2]['other'] = array(); 
	
		
	
		   }
		}
	 }
	 /*
	foreach ($arr as $k=>$v)
	 {
	   $arrr[$k] = $v; 
	 }
	 */
	 
	$arrr = $arr;
	
	/*
    if (!empty($arr)) $arrr = $arr;
    else
    {
      $cp = $arrr; 
      foreach ($cp as $k => $v2)
      {
        $arrr[$k.'_translationid_0'] = $arrr[$k]; 
        unset($arrr[$k]); 
      }
    }
	
	var_dump($arrr); die();
	*/
  }
  
  function getKeys(&$arr)
  {
  
  }
  
  function generatefile()
  {
   
    jimport( 'joomla.filesystem.file' );
    jimport( 'joomla.filesystem.folder' );
	
    $lang = JRequest::getVar('tlang', ''); 
	$tr_from = JRequest::getVar('tr_from', 'en-GB'); 
	
    $user = JRequest::getVar('nickname', ''); 
    $component = JRequest::getVar('tcomponent', ''); 
	$type = JRequest::getVar('ttype', 'site');     
    $path = JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'translations';
    
    
    $relpath = JURI::root().'components/com_onepage/translations';
    
	if ($type == 'site')
    $path = JPATH_ROOT.DIRECTORY_SEPARATOR.'language';
	else $path = JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'language';
    
    // basic security
    if (strpos($lang, '..')!==false) die('edit.php: hacking attempt'); 
    if (strpos($component, '..')!==false) die('edit.php: hacking attempt'); 
    if (strpos($user, '..')!==false) die('edit.php: hacking attempt'); 
    if (strpos($type, '..')!==false) die('edit.php: hacking attempt'); 
    
    $lang = JFile::makeSafe($lang); 
    $user = JFile::makeSafe($user); 
    $component = JFile::makeSafe($component); 
	$type = JFile::makeSafe($type); 
	$tr_from = JFile::makeSafe($tr_from); 
	
	 if (!file_exists($path)) 
	if (JFolder::create($path) === false)
	echo JText::sprinf('COM_ONEPAGE_CANNOT_CREATE_DIRECTORY', $path); 
	// sk-SK
    $path .= DS.$lang; 
    $relpath .= '/'.$lang;
    
    if (!file_exists($path)) 
	if (JFolder::create($path) === false)
	echo JText::sprinf('COM_ONEPAGE_CANNOT_CREATE_DIRECTORY', $path); 
	
	/*
    // sk-SK/site
    $path .= DS.$type; 
    $relpath .= '/'.$type;
    if (!file_exists($path)) 
	if (JFolder::create($path) === false)
	echo 'Cannot create directory: '.$path; 
    
	
    $path .= DS.$user; 
    $relpath .= '/'.$user;
    if (!file_exists($path)) 
	if (JFolder::create($path) === false)
	echo 'Cannot create directory: '.$path; 
    */
	
    $filename = $path.DIRECTORY_SEPARATOR.$lang.'.'.$component.'.ini';
	
	if (file_exists($filename))
	 {
	   $x = rand(100000, 999999); 
	   // will create a random filename
	   $filename2 = $path.DIRECTORY_SEPARATOR.$lang.'.'.$component.'_bck_opc'.$x.'.ini';
	   if (JFile::copy($filename, $filename2) === false)
		echo JText::sprintf('COM_ONEPAGE_CANNOT_CREATE_BACKUP', $filename).'<br />';
	    //echo 'Cannot create a backup of '.$filename.'<br />'; 
	 }
	
    $relpath .= '/'.$lang.'.'.$component.'.ini';
    	
	$this->createtable(); 
	
    $arr1 = $this->getIni($tr_from, $type, $component); 
	if (empty($arr1)) 
	 {
	   echo '<b style="color: red;">'.JText::_('COM_ONEPAGE_CANNOT_SAVE_LANG').'</b><br />'; 
	   return; 
	 }
	$arr_orig = $arr1; 
	$arr2 = $this->getIni($lang, $type, $component); 
	
	foreach ($arr1 as $key => $v)
	 {
	   if (!empty($arr2[$key])) $arr1[$key] = $arr2[$key]; 
	 }
	
    $db = JFactory::getDBO(); 
	echo JText::sprintf('COM_ONEPAGE_FETCHING_TRANSLATIONS_PERUSER', $user, $component, $type, $lang).'<br />'; 
    foreach ($arr1 as $key => $val)
     {
       $translation = $val; 
	   
       $q = "select * from #__vmtranslator_translations where user = '".$db->escape($user)."' and var = '".$db->escape(urlencode($key))."' and entity = '".$db->escape($component)."' and lang = '".$db->escape($lang)."' and type = '".$db->escape($type)."' order by id asc limit 0, 1"; 
       $db->setQuery($q); 
       $res = $db->loadAssoc(); 
       $err = $db->getErrorMsg(); 
       if (!empty($err)) { var_dump($err); die(); }

       if (!empty($res))
       {
         $translation = urldecode($res['translation']); 
       }
       else
       {
        // if user has no entry, get the latest id
        $q = "select * from #__vmtranslator_translations where var = '".$db->escape(urlencode($key))."' and lang = '".$db->escape($lang)."'  and entity = '".$db->escape($component)."' and type = '".$db->escape($type)."' order by id asc limit 0, 1"; 
        $db->setQuery($q); 
        $res = $db->loadAssoc(); 
        $translation = urldecode($res['translation']); 
        $err = $db->getErrorMsg(); 
        if (!empty($err)) { var_dump($err); die(); }
       }
	   
	   if (($arr_orig[$key] != $translation) || (empty($arr2[$key])))
       $arr1[$key] = $translation;
     }
	 
	 // vm2.0.22+ new lang files: 
    if (stripos($component, 'com_virtuemart')!==false)
   {
	   $arr3 = $this->getIni($lang, $type, 'com_virtuemart'); 
	
   }
   /*
  foreach ($arr1o as $k=>$a2)
		{
			if (empty($arr3)) break; 
			//var_dump($arr1o); 
			//var_dump($arr3); die();
			var_dump($arr1[$k]); 
			die('here'); 
			if (empty($arr1[$k]) || ($arr1[$k] == $arr_orig[$key]))
			 if (!empty($arr3[$k])) 
			{
				
				$arr2[$k] = $arr3[$k]; 
				//$arr1[$k] = $arr3[$k]; 
			}
		}
	*/
    $this->write_ini_file($filename, $arr1); 
    return $relpath; 
    
  }

function tableExists($table)
{

 $dbj = JFactory::getDBO();
 $prefix = $dbj->getPrefix();
 $table = str_replace('#__', '', $table); 
 $table = str_replace($prefix, '', $table); 
 
  $q = "SHOW TABLES LIKE '".$dbj->getPrefix().$table."'";
	   $dbj->setQuery($q);
	   $r = $dbj->loadResult();
	   if (!empty($r)) return true;
 return false;
}
  
  function createtable()
  {
  
 $dbj = JFactory::getDBO();
 $prefix = $dbj->getPrefix();

   if (!$this->tableExists('vmtranslator_translations'))
   {
 $q = "CREATE TABLE IF NOT EXISTS ".$prefix."vmtranslator_translations (
  id int(12) NOT NULL auto_increment,
  entity varchar(50) NOT NULL,
  `type` enum('administrator','site') NOT NULL default 'site',
  var varchar(200) NOT NULL,
  translation varchar(5000) NOT NULL,
  lang varchar(6) NOT NULL,
  `user` varchar(10) NOT NULL,
  PRIMARY KEY  (id),
  UNIQUE KEY `user` (`user`,var,lang,entity,`type`),
  UNIQUE KEY id (id,var),
  KEY lang (lang,entity,`type`),
  KEY var (var,lang,entity,`type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; "; 
  
  $dbj->setQuery($q); 
  $dbj->query(); 
  }
   
   $q = 'ALTER TABLE  `#__vmtranslator_translations` CHANGE  `translation`  `translation` VARCHAR( 5000 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL'; 
   $dbj->setQuery($q); 
   $dbj->query(); 

  }
  
   function write_ini_file($file, array $options){
   
	jimport( 'joomla.filesystem.file' );
	
	//$fh = fopen($file, 'w') or die("can't write file");
	$fi = pathinfo($file); 
	$filename = $fi['basename']; 
	if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) 
	$line = '; '.$filename."\n";
	else
	$line = '# '.$filename."\n";
	$date = JFactory::getDate();
	$date = $date->toRFC822(); 
	if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) 
	$line .= '; generated '.$date."\n";
	else
	$line .= '# generated '.$date."\n";
	//fwrite($fh, $line);
	  
	foreach ($options as $key => $val)
	{
	
	 // http://www.fastw3b.net/latest-news/262-language-files-specifications-for-joomla-16x.html
	  $key = str_replace('}', '', $key); 
	  $key = str_replace('{', '', $key); 
	  $key = str_replace('|', '', $key); 
	  $key = str_replace('&', '', $key); 
	  $key = str_replace('~', '', $key); 
	  $key = str_replace('!', '', $key); 
	  $key = str_replace('[', '', $key); 
	  $key = str_replace('(', '', $key); 
	  $key = str_replace(')', '', $key); 
	  $key = str_replace('^', '', $key); 
	  $key = str_replace('"', '', $key); 
	  
	  if ($key == false) $key = ''; 
	  elseif (empty($key)) $key = ''; 
	  elseif (strtolower($key) == 'false') $key = ''; 
	  elseif (strtolower($key) == 'null') $key = ''; 
	  elseif (strtolower($key) == 'yes') $key = ''; 
	  elseif (strtolower($key) == 'no') $key = ''; 
	  elseif (strtolower($key) == 'true') $key = ''; 
	  elseif (strtolower($key) == 'on') $key = ''; 
	  elseif (strtolower($key) == 'off') $key = ''; 
	  elseif (strtolower($key) == 'none') $key = ''; 
	  else
	  {
	  $key = str_replace(' ', '_', $key);
	  $val = str_replace('"', '&quot;', $val); 
	  $val .= "\n";
	  // ^(null|yes|no|true|false|on|off|none)=(.+)\R and replace with nothing.
	  if (strpos($val, "\n")===(strlen($val)-1)) $val = substr($val, 0, strlen($val)-1);
	  if (strpos($val, "\r")===(strlen($val)-1)) $val = substr($val, 0, strlen($val)-1);
	  
	  $line .= $key.'="'.$val.'"'."\n"; 
	  if(function_exists('parse_ini_string'))
	  {
	  
	    if (parse_ini_string($line)===false)
		if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) 
		$line .= ';'.$line;
		else
	    $line .= '#'.$line;
	  }
	  
	  //fwrite($fh, $line);
	  }
	}

	//fclose($fh);
	if (JFile::write($file, $line) === false) echo '<span style="color: red;">'.JText::_('COM_ONEPAGE_CANNOT_WRITE_FILE').' '.$file.'</span>';
	 else echo '<b style="color: green;">'.JText::_('COM_ONEPAGE_FILE_CREATED_IN').' '.$file.'</b>';
 }
	
	function getzip()
	{
	 die('ok'); 
	}
   function updateT()
  {
    $lang = JRequest::getVar('tlang', ''); 
    $var = JRequest::getVar('translation_var', ''); 
    $translation = JRequest::getVar('translation', '', 'post', 'STRING', JREQUEST_ALLOWRAW ); 
    
	$user = JRequest::getVar('nickname', ''); 
    $component = JRequest::getVar('tcomponent', ''); 
    
    if (empty($lang) || (empty($var)) || (empty($translation))) return false;
    
    $a = explode('_', $var); 
    $type = $a[0]; 
    if ($type != 'site')
    if ($type != 'administrator') 
     {
    	return false;
     }
    if ($a[1] !== 'lang') 
     {
       var_dump($a[1]);die();
       return false;
     }
    $lang = $a[2]; 
    $lvar = str_replace($a[0].'_'.$a[1].'_'.$a[2].'_', '', $var);
    $id = $a[count($a)-1]; 
    if (!is_numeric($id)) 
    {
    echo $id; 
    return false;
    }
    $lvar = str_replace('_translationid_'.$id, '', $lvar); 
    return $this->insertUpdate($component, $type, $lang, $user, $id, $var, $translation);
  }
  
  function insertUpdate($component, $type, $lang, $user, $id, $var, $value)
  {
    $key = urlencode($var);
    
	$this->createtable(); 
   
    $db = JFactory::getDBO(); 
    $q = "select * from #__vmtranslator_translations where id = '".$db->escape($id)."' limit 0, 1"; 
    $db->setQuery($q); 
    $res = $db->loadAssoc(); 
    $err = $db->getErrorMsg();  if (!empty($err)) { var_dump($err); die(); }
    $translation = urlencode($value); 
	if (!empty($res))
	{
	  if ($res['user'] == $user)
	  {
	    $q = "update #__vmtranslator_translations set translation = '".$db->escape($translation)."' where id = '".$id."' ";
	    $db->setQuery($q); 
	    $db->query($q); 
	    $err = $db->getErrorMsg();  if (!empty($err)) { var_dump($err); die(); }
	  }
	  else
	  {
	     
	    $val = $translation;
	    $user = urlencode($user); 
		if (empty($key) || (empty($component)) || (empty($type))) return; 
		
	    $q = "insert into #__vmtranslator_translations (`id`, `entity`, `type`, `var`, `translation`, `lang`, `user`) values (NULL, '".$db->escape($component)."', '".$db->escape($type)."', '".$db->escape($key)."', '".$db->escape($val)."', '".$db->escape($lang)."', '".$db->escape($user)."' ) ";
	    $db->setQuery($q); 
	    $db->query(); 
	    $err = $db->getErrorMsg();  if (!empty($err)) { var_dump($err); die(); }
		
		return $db->insertid();
	  }
	}
	else
	{
	  echo $id.' not found! key: '.$key; die();
	}
	
	return true;

  }
  
  function getIni($lang, $type, $component)
  {
    
    if ($type == 'site') $path = JPATH_SITE.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR;
    else
    if ($type == 'administrator') $path = JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR;
    else die('Invalid type'); 
    
    $path .= $lang.DIRECTORY_SEPARATOR.$lang.'.'.$component.'.ini';

	
	$path = (string)$path; 
	
	
	
	if (!file_exists($path))
	{

	
	echo JText::_('COM_ONEPAGE_FILE_DOESNOT_EXISTS').' '.$path.'<br />'; 
	return array();
	}
	else
	{
	echo JText::_('COM_ONEPAGE_FETCHING').' '.$path.'<br />'; 
	
	$ret = array(); 
	if (function_exists('parse_ini_string'))
	{
	$handle = @fopen($path, "r");
	
	if ($handle) {
    while (($buffer = fgets($handle, 40096)) !== false) {
        //echo $buffer.'<br />'; 
		
		$x = @parse_ini_string($buffer);
		
	    if (!empty($x))
		  {
		    foreach ($x as $key=>$val)
		    $ret[$key] = $val; 
		  }
		 if ($x === false)
		  {
		    // error
			//var_dump($buffer); 
			//die('err'); 
		  }
	 
    }
	
    if (!feof($handle)) {
        echo JText::_('COM_ONEPAGE_ERROR_FGET')."<br />\n";
    }
    fclose($handle);

     
	}
	}
	else
    $ret =  parse_ini_file($path, false); 
	
	if ($ret === false)
	  {
	    die(JText::_('COM_ONEPAGE_CANNOT_PARSE_INI')); 
	  }
	//var_dump($ret); die(); 
	return $ret; 
	}
    
  }
  
  function getVM2en()
  {
    $this->flushTable(); 
   
	$tr_from = JRequest::getVar('tr_fromlang', 'en-GB'); 
	$to = JRequest::getVar('tr_tolang', 'en-GB'); 
	$tr_type = JRequest::getVar('tr_type', 'site'); 
	$xt = JRequest::getVar('tr_ext', ''); 
	//echo $xt; 
	//die('x:'.rand());  
	if (empty($xt)) 
	{
	JRequest::setVar('format', 'html');
	return;
	}
	$xt = str_replace('.ini', '', $xt); 
	
    
    jimport( 'joomla.filesystem.folder' );
    jimport( 'joomla.filesystem.file' );
   
   $tr_type = JFile::makesafe($tr_type); 
   $xt = JFile::makesafe($xt); 
   $to = JFile::makesafe($to); 
   $tr_from = JFile::makesafe($tr_from); 
	
   $arr1 = $this->getIni($tr_from, $tr_type, $xt); 
   $arr2 = $this->getIni($to, $tr_type, $xt); 
   
  
   
   $arr2o = unserialize(serialize($arr2)); 
   // get rid of the reference
   $arr1o = unserialize(serialize($arr1)); 
	
	if (!empty($arr2o))
	{
	foreach ($arr2o as $k=>$a2)
    {
	  // if sk['text'] en['text'] = sk['text']
	  if (!empty($arr2[$k])) $arr1[$k] = $arr2[$k]; 
	  
	  if (!empty($arr3[$k])) 
	  {
		  
	   $arr2[$k] = $arr3[$k]; 
	   $arr1[$k] = $arr3[$k]; 
	  }
	  
	}
	  }
	else
	{
		// translat to file does not exists
		/*
		foreach ($arr1o as $k=>$a2)
		{
			//var_dump($arr1o); 
			//var_dump($arr3); die(); 
			 if (!empty($arr3[$k])) 
			{
		  
				$arr2[$k] = $arr3[$k]; 
				//$arr1[$k] = $arr3[$k]; 
			}
		}
		*/
		//die(); 
	}
	
  
   
   $user = JFactory::getUser(); 
   $username = $user->username; 


   if (!$this->checkDB($xt, $tr_type, $tr_from))
   {
     $this->fillDB($xt, $tr_type, $tr_from, $arr1, $username); 
	   
     $this->getTranlations($xt, $tr_type, $tr_from, $arr1);
	} 
   else
   {
   	 $this->getTranlations($xt, $tr_type, $tr_from, $arr1);
   }
  
   
   
   $ret[$tr_type][$tr_from] = $arr1; 
   
   
   
   
   $arr2 = $this->getIni($to, $tr_type, $xt); 
   
   
   
   // if absolutely no language file exists for target language
   if (empty($arr2)) $arr2 = $this->getIni($tr_from, $tr_type, $xt); 
   
   
   
   
   
   // we need to check if it contains at least the same fields as the original language
   foreach ($arr1o as $kk=>$vv)
    {
	  if (!is_array($vv))
	  if (!isset($arr2[$kk])) $arr2[$kk] = $vv; 
	}
	
	
	// vm2.0.22+ new lang files: 
    if (stripos($xt, 'com_virtuemart')!==false)
   {
	   $arr3 = $this->getIni($to, $tr_type, 'com_virtuemart'); 
	
   }
  foreach ($arr1o as $k=>$a2)
		{
			//var_dump($arr1o); 
			//var_dump($arr3); die(); 
			 if (!empty($arr3[$k])) 
			{
				$arr2[$k] = $arr3[$k]; 
				//$arr1[$k] = $arr3[$k]; 
			}
		}
	
   unset($arr1); 
   
   if (!$this->checkDB($xt, $tr_type, $to))
   {
   
     $this->fillDB($xt, $tr_type, $to, $arr2, $username); 
	 
     $this->getTranlations($xt, $tr_type, $to, $arr2, $arr1o);
	unset($arr1o);
   }
   else
   {
  
  
   	  $this->getTranlations($xt, $tr_type, $to, $arr2, $arr1o);
	  unset($arr1o);
	  
   }
  
   
   // ret['site']['to_language'] = ... 
   $ret[$tr_type][$to] = $arr2; 
   unset($arr2); 
   //var_dump($ret); die(); 
   return $ret;

  }
  
  function getVm1key(&$vm1, &$line)
  {
    foreach ($vm1 as $key => $value)
    {
      if ($value == $line) 
       {
         
        return $key; 
       }
    }
    return ''; 
  }
  
  function listDirs($lang_name)
  {
    return array();
    $codes = array(); 
    $path = JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'languages';;
    
    {
    //echo "Directory handle: $handle\n";
    //echo "Files:\n";
	$cc = scandir($path); 
    /* This is the correct way to loop over the directory. */
    foreach ($cc as $file) {
        if (is_dir($path.DIRECTORY_SEPARATOR.$file))
        {
		$ff = pathinfo($file);         
		if ($ff['basename'] !== '.' && $ff['basename'] != '..' && $ff['basename'] != 'overrides')
		{
		if (file_exists($path.DIRECTORY_SEPARATOR.$file.DIRECTORY_SEPARATOR.$lang_name.'.php'))
         $codes[] =  $path.DIRECTORY_SEPARATOR.$file.DIRECTORY_SEPARATOR.$lang_name.'.php';
        }
        }
    }

    
	}
	return $codes; 
    
  }

}
class vmLang
{
  public static $lang_vars = array();


  // please add your decoding function here
  // the output array must be in UTF-8
  function convert(&$vars)
  {
    if (strtolower($vars['CHARSET']) == 'iso-8859-1')
    {
      foreach ($vars as $k=>$v)
      {
        $vars[$k] = utf8_encode($v); 
      }
    }
    return $vars;
  }

  function initModule($type, &$vars)
  {
    if (empty(vmLang::$lang_vars))
     vmLang::$lang_vars = $this->convert($vars);
    else
    {
      vmLang::$lang_vars = array_merge(vmLang::$lang_vars, $this->convert($vars));
    }
  }
}
