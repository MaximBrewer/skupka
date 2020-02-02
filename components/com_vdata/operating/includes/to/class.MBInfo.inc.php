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
class MBInfo
{
    
    private $_mbTemp = array();

    
    private $_mbFan = array();

    
    private $_mbVolt = array();

    
    private $_mbPower = array();

    
    private $_mbCurrent = array();

    public function getMbFan()
    {
        return $this->_mbFan;
    }

    
    public function setMbFan($mbFan)
    {
        array_push($this->_mbFan, $mbFan);
    }

    
    public function getMbTemp()
    {
        return $this->_mbTemp;
    }

    
    public function setMbTemp($mbTemp)
    {
        array_push($this->_mbTemp, $mbTemp);
    }

    
    public function getMbVolt()
    {
        return $this->_mbVolt;
    }

   
    public function setMbVolt($mbVolt)
    {
        array_push($this->_mbVolt, $mbVolt);
    }

   
    public function getMbPower()
    {
        return $this->_mbPower;
    }

    
    public function setMbPower($mbPower)
    {
        array_push($this->_mbPower, $mbPower);
    }
   
    public function getMbCurrent()
    {
        return $this->_mbCurrent;
    }

   
    public function setMbCurrent($mbCurrent)
    {
        array_push($this->_mbCurrent, $mbCurrent);
    }
}
