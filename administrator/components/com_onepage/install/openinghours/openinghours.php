<?php

defined('_JEXEC') or die;
class PlgSystemOpeninghours extends JPlugin
{
	private $_o; 
	private $_o_stamp; 
	private $_z; 
	private $_z_stamp;
	private $_do; 
	private $_do_stamp;
	private $_dz;
	private $_dz_stamp;	
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		
		$dof = (int)JFactory::getDate()->format('w');
		$key = 'o'.$dof; 
		$this->_o = $this->_getTime($this->params->get($key, '')); 
		
		
	$config = JFactory::getConfig();
		$offset = $config->get('offset');	
	$zzone = new DateTimeZone($offset); //	
	$dtime = DateTime::createFromFormat("Y-m-d H:i", date("Y-m-d")." ".$this->params->get($key, ''),$zzone);
	$this->_o_stamp = $dtime->getTimestamp();

	
	$cTime = new JDate('now');
		 
	//	$sOfDay=startOfDay();
	$cTs=$cTime->toUnix();

	
		$key = 'z'.$dof; 
		$this->_z = $this->_getTime($this->params->get($key, '')); 

$dtime = DateTime::createFromFormat("Y-m-d H:i", date("Y-m-d")." ".$this->params->get($key, ''),$zzone);
	$this->_z_stamp = $dtime->getTimestamp();
 		
		
		$key = 'dz'.$dof; 
		$this->_dz = $this->_getTime($this->params->get($key, '')); 
		$dtime = DateTime::createFromFormat("Y-m-d H:i", date("Y-m-d")." ".$this->params->get($key, ''),$zzone);
		$this->_dz_stamp = $dtime->getTimestamp();
		
		$key = 'do'.$dof; 
		$this->_do = $this->_getTime($this->params->get($key, '')); 
		$dtime = DateTime::createFromFormat("Y-m-d H:i", date("Y-m-d")." ".$this->params->get($key, ''),$zzone);
		$this->_do_stamp = $dtime->getTimestamp();

		
	}
    private function _getTime($t)
	{
		$a = explode(':', $t); 
		if (count($a)>1) {
		$h = $a[0]; 
		if (isset($a[1]))
		$min = $a[1]; 
		else $min = 0; 
		}
		else
		{
			$h = $a; 
			$min = 0; 
			
		}
		$config = JFactory::getConfig();
		$offset = $config->get('offset');
		$zone = new DateTimeZone($offset); // Or your own definition of "here"
		$todayStart = new DateTime('today midnight', $zone);
		
		$timestamp = $todayStart->getTimestamp();
		$timestamp = $timestamp - 24*60*60; // we need morning midtnight
		
		$h = (int)$h; 
		$min = (int)$min; 
		$tr = $timestamp = $timestamp + ($h * 60 * 60) + ($min * 60); 
		
		return $tr;
		
	}
	
	private function _isClosed()
	{
	  $config = JFactory::getConfig();
		$offset = $config->get('offset');
		$zone = new DateTimeZone($offset); // Or your own definition of "here"
		$now = new DateTime('now', $zone);
		
		$todayStart = new DateTime('today midnight', $zone);
		
		
		$stamp = $now->getTimestamp(); 
		$cur_num_week_day = date('w',$stamp);
		$cur_hour = date('H',$stamp);
		$cur_min = date('i',$stamp);
		

		if ($this->_o == $this->_z) return true; 
		if (($stamp > $this->_o_stamp) and ($stamp < $this->_z_stamp)) return false; 
	   return true; 
	}
	
	
	private function _isDonaskaClosed()
	{
	  $config = JFactory::getConfig();
		$offset = $config->get('offset');
		$zone = new DateTimeZone($offset); // Or your own definition of "here"
		$now = new DateTime('now', $zone);
		
		$stamp = $now->getTimestamp(); 
		
		
		
		if ($this->_do == $this->_dz) return true; 
		if (($stamp > $this->_do_stamp) and ($stamp < $this->_dz_stamp)) return false; 
	   
	   return true; 
	}
	
	
	public function onAfterInitialise()
	{
		
	
	
	}
	public function onLoadJshopConfig(&$config)
	{
		if (JFactory::getApplication()->isAdmin()) return; 
		if (($this->_isClosed()) || ($this->_isDonaskaClosed())) {
		 $config->user_as_catalog = 1; 
		 
		 
		
		}
		
		
		
	}
	public function onAfterRender()
	{
		
		$doc = JFactory::getDocument(); 
		$html = JResponse::getBody();
		
		$c = $this->_isClosed(); 
		if ($c) 
		{
			$te = $this->params->get('text_zatvorene', ''); 
			
		}
		else
		{
			$te = $this->params->get('text_otvorene', ''); 
		}
		
		$html = str_replace('id="opening_hours">', 'id="opening_hours"><h3 style="text-align: center;"><span style="color: #000000;"><a href="index.php?option=com_content&amp;view=article&amp;id=74&amp;Itemid=519" style="color: #0B4EA2;">'.$te.'</a></span></h3>', $html); 
		JResponse::setBody($html); 
		
	}
}
