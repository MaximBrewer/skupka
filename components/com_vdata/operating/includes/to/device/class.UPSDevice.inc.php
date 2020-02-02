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
class UPSDevice
{
    
    private $_name = "";

    
    private $_model = "";

    
    private $_mode = "";

    
    private $_startTime = "";

   
    private $_status = "";

    
    private $_temperatur = null;

   
    private $_outages = null;

    
    private $_lastOutage = null;

    private $_lastOutageFinish = null;

    
    private $_lineVoltage = null;

    
    private $_lineFrequency = null;

    
    private $_load = null;

   
    private $_batteryDate = null;

    private $_batteryVoltage = null;

    
    private $_batterCharge = null;

   
    private $_timeLeft = null;

    
    public function getBatterCharge()
    {
        return $this->_batterCharge;
    }

    public function setBatterCharge($batterCharge)
    {
        $this->_batterCharge = $batterCharge;
    }

    
    public function getBatteryDate()
    {
        return $this->_batteryDate;
    }

    
    public function setBatteryDate($batteryDate)
    {
        $this->_batteryDate = $batteryDate;
    }

    
    public function getBatteryVoltage()
    {
        return $this->_batteryVoltage;
    }

    
    public function setBatteryVoltage($batteryVoltage)
    {
        $this->_batteryVoltage = $batteryVoltage;
    }

    
    public function getLastOutage()
    {
        return $this->_lastOutage;
    }

    
    public function setLastOutage($lastOutage)
    {
        $this->_lastOutage = $lastOutage;
    }

   
    public function getLastOutageFinish()
    {
        return $this->_lastOutageFinish;
    }

    
    public function setLastOutageFinish($lastOutageFinish)
    {
        $this->_lastOutageFinish = $lastOutageFinish;
    }

   
    public function getLineVoltage()
    {
        return $this->_lineVoltage;
    }

    
    public function setLineVoltage($lineVoltage)
    {
        $this->_lineVoltage = $lineVoltage;
    }

    
    public function getLineFrequency()
    {
        return $this->_lineFrequency;
    }

  
    public function setLineFrequency($lineFrequency)
    {
        $this->_lineFrequency = $lineFrequency;
    }

    
    public function getLoad()
    {
        return $this->_load;
    }

   
    public function setLoad($load)
    {
        $this->_load = $load;
    }

    
    public function getMode()
    {
        return $this->_mode;
    }

    
    public function setMode($mode)
    {
        $this->_mode = $mode;
    }

   
    public function getModel()
    {
        return $this->_model;
    }

    
    public function setModel($model)
    {
        $this->_model = $model;
    }

   
    public function getName()
    {
        return $this->_name;
    }

    
    public function setName($name)
    {
        $this->_name = $name;
    }

    
    public function getOutages()
    {
        return $this->_outages;
    }

    
    public function setOutages($outages)
    {
        $this->_outages = $outages;
    }

    
    public function getStartTime()
    {
        return $this->_startTime;
    }

    
    public function setStartTime($startTime)
    {
        $this->_startTime = $startTime;
    }

    
    public function getStatus()
    {
        return $this->_status;
    }

   
    public function setStatus($status)
    {
        $this->_status = $status;
    }

    
    public function getTemperatur()
    {
        return $this->_temperatur;
    }

    
    public function setTemperatur($temperatur)
    {
        $this->_temperatur = $temperatur;
    }

   
    public function getTimeLeft()
    {
        return $this->_timeLeft;
    }

    
    public function setTimeLeft($timeLeft)
    {
        $this->_timeLeft = $timeLeft;
    }
}
