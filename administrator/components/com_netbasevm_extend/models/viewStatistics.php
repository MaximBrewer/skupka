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

require_once(JPATH_ADMINISTRATOR.DS."components".DS. "com_netbasevm_extend".DS."helpers/statistics".DS."config1.php");

class viewListRead
{
	private static $_instance = null; 
	private $classList = array();
	private $displayClasses = array();
	
	protected function __construct()
	{
	}
	
	public static function getInstance()
	{
		if(is_null(self::$_instance))
		{ 	
			self::$_instance = new self;
			//echo 'new instance';	
		}
			
			
		return self::$_instance;
	}
		
	private function readClassLists()
	{
		//UnSerialize the list of classes to display 
		$cfg2Path = JPATH_ADMINISTRATOR.DS."components".DS."com_netbasevm_extend".DS."helpers/statistics".DS."config2.php";
		$serializedClassed = file_get_contents($cfg2Path);
  		$this->displayClasses = unserialize($serializedClassed);

		//Read the classes from the dashboard directory. If class is not on the display classes
		//list, add it to the general class list
		//echo "reading...<br/>";
		$path = JPATH_ADMINISTRATOR.DS."components".DS."com_netbasevm_extend".DS."tables".DS."statistics".DS;
		$fileList = scandir($path);

		foreach($fileList as $file => $v)
		{
			//skip directories
			if(is_file($path.$fileList[$file]))
			{
				$path_parts = pathinfo($fileList[$file]);
				$className = $path_parts['filename'];
				require_once($path.$fileList[$file]);
				
				try
				{
					$class = new ReflectionClass($className);
					$parent = new ReflectionClass('gvviewclass');
					
					if($className != 'gvviewclass' && $class->isSubclassOf($parent) && $class->isInstantiable() && $class->hasMethod('getHandleFunctions'))
					{
						//if class name is not part of classes to display, add it to the general class list
						if(!isset($this->displayClasses[$className]))
						{
							$viewObj = new $className(FEEDDIR, $className);
							$titleName = $viewObj->getTitleName1();
							$viewObj = null;
							$this->classList[$className] = 	$titleName;
						}
						//$this->displayClasses[$className] = $titleName;
					}
				}
				catch (ReflectionException $e)
				{
					
				}
			}
		}
		/*$s = serialize($this->displayClasses);
		file_put_contents($cfg2Path, $s);*/
		
	}
	
	public function &getClassList($force = false)
	{
		if(!$force)
		{	
			if(empty($this->classList))
			{
				$this->readClassLists();
			}
		}
		else
			$this->readClassLists();
			
		return $this->classList;
	}
	
	public function &getDisplayClasses($force = false)
	{
		if(!$force)
		{	
			if(empty($this->displayClasses))
			{
				$this->readClassLists();
			}
		}
		else
			$this->readClassLists();
			
		return $this->displayClasses;
	}
	
	public function setClassList(&$availDateViews, &$availGenViews)
	{
		$availDateViews = str_replace(':','&',$availDateViews);
		$availDateViews = str_replace(',','=',$availDateViews);
		$availGenViews = str_replace(':','&',$availGenViews);
		$availGenViews = str_replace(',','=',$availGenViews);
		
		$tmpArray1 = array();
		$tmpArray2 = array();
		parse_str($availDateViews, $tmpArray1);
		parse_str($availGenViews, $tmpArray2);
		$this->classList = array_merge($tmpArray1, $tmpArray2);
		
		return true;
	}
	
	public function setDisplayClasses(&$dispDateViews, &$dispGenViews)
	{
		$bRes = true;
		
		$dispDateViews = str_replace(':','&',$dispDateViews);
		$dispDateViews = str_replace(',','=',$dispDateViews);
		$dispGenViews = str_replace(':','&',$dispGenViews);
		$dispGenViews = str_replace(',','=',$dispGenViews);
		
		$tmpArray1 = array();
		$tmpArray2 = array();
		parse_str($dispDateViews, $tmpArray1);
		parse_str($dispGenViews, $tmpArray2);
		$this->displayClasses = array_merge($tmpArray1, $tmpArray2);
		
		//Serialize the list of classes to display 
		$cfg2Path = JPATH_ADMINISTRATOR.DS."components".DS."com_netbasevm_extend".DS."helpers/statistics".DS."config2.php";
		$serializedClassed = serialize($this->displayClasses);
		echo "----" . $serializedClassed;
		if(!file_put_contents($cfg2Path,$serializedClassed,LOCK_EX))
			$bRes = false; 

		return $bRes;
	}
	
}

$dummy = viewListRead::getInstance();