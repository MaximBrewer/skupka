<?php
/*------------------------------------------------------------------------
# com_vdata - vData
# ------------------------------------------------------------------------
# author    Team WDMtech
# copyright Copyright (C) 2016 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support:  Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access');
class SensorDevice
{
   
    private $_name = "";

    
    private $_value = 0;

    
    private $_max = null;

    
    private $_min = null;

    
    private $_event = "";

    
    public function getMax()
    {
        return $this->_max;
    }

    
    public function setMax($max)
    {
        $this->_max = $max;
    }

    
    public function getMin()
    {
        return $this->_min;
    }

  
    public function setMin($min)
    {
        $this->_min = $min;
    }

    public function getName()
    {
        return $this->_name;
    }

    
    public function setName($name)
    {
        $this->_name = $name;
    }

    
    public function getValue()
    {
        return $this->_value;
    }

    
    public function setValue($value)
    {
        $this->_value = $value;
    }

   
    public function getEvent()
    {
        return $this->_event;
    }

   
    public function setEvent($event)
    {
        $this->_event = $event;
    }
}
