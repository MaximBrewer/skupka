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
class NetDevice
{
    
    private $_name = "";

  
    private $_txBytes = 0;

    
    private $_rxBytes = 0;

    
    private $_errors = 0;

   
    private $_drops = 0;

   
    private $_info = null;

   
    public function getDrops()
    {
        return $this->_drops;
    }

    
    public function setDrops($drops)
    {
        $this->_drops = $drops;
    }

    
    public function getErrors()
    {
        return $this->_errors;
    }

    
    public function setErrors($errors)
    {
        $this->_errors = $errors;
    }

    
    public function getName()
    {
        return $this->_name;
    }

   
    public function setName($name)
    {
        $this->_name = $name;
    }

   
    public function getRxBytes()
    {
        return $this->_rxBytes;
    }

   
    public function setRxBytes($rxBytes)
    {
        $this->_rxBytes = $rxBytes;
    }

    
    public function getTxBytes()
    {
        return $this->_txBytes;
    }

    
    public function setTxBytes($txBytes)
    {
        $this->_txBytes = $txBytes;
    }

   
    public function getInfo()
    {
        return $this->_info;
    }

    
    public function setInfo($info)
    {
        $this->_info = $info;
    }
}
