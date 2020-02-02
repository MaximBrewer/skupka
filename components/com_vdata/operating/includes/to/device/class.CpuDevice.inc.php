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
class CpuDevice
{
    
    private $_model = "";

    
    private $_cpuSpeed = 0;

    
    private $_cpuSpeedMax = 0;

   
    private $_cpuSpeedMin = 0;

    
    private $_cache = null;

    
    private $_virt = null;

    
    private $_busSpeed = null;

    
    private $_temp = null;

    
    private $_bogomips = null;

    
    private $_load = null;

    
    public function getBogomips()
    {
        return $this->_bogomips;
    }

    
    public function setBogomips($bogomips)
    {
        $this->_bogomips = $bogomips;
    }

   
    public function getBusSpeed()
    {
        return $this->_busSpeed;
    }

    
    public function setBusSpeed($busSpeed)
    {
        $this->_busSpeed = $busSpeed;
    }

    
    public function getCache()
    {
        return $this->_cache;
    }

    
    public function setCache($cache)
    {
        $this->_cache = $cache;
    }

    
    public function getVirt()
    {
        return $this->_virt;
    }

    
    public function setVirt($virt)
    {
        $this->_virt = $virt;
    }

    
    public function getCpuSpeed()
    {
        return $this->_cpuSpeed;
    }

    
    public function getCpuSpeedMax()
    {
        return $this->_cpuSpeedMax;
    }

    
    public function getCpuSpeedMin()
    {
        return $this->_cpuSpeedMin;
    }

    
    public function setCpuSpeed($cpuSpeed)
    {
        $this->_cpuSpeed = $cpuSpeed;
    }

    
    public function setCpuSpeedMax($cpuSpeedMax)
    {
        $this->_cpuSpeedMax = $cpuSpeedMax;
    }

    
    public function setCpuSpeedMin($cpuSpeedMin)
    {
        $this->_cpuSpeedMin = $cpuSpeedMin;
    }

   
    public function getModel()
    {
        return $this->_model;
    }

    
    public function setModel($model)
    {
        $this->_model = $model;
    }

    
    public function getTemp()
    {
        return $this->_temp;
    }

    
    public function setTemp($temp)
    {
        $this->_temp = $temp;
    }

    
    public function getLoad()
    {
        return $this->_load;
    }

    
    public function setLoad($load)
    {
        $this->_load = $load;
    }
}
