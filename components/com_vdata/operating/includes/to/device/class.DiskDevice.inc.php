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
class DiskDevice
{
    
    private $_name = "";

   
    private $_fsType = "";

    
    private $_free = 0;

    
    private $_used = 0;

    
    private $_total = 0;

   
    private $_mountPoint = null;

    
    private $_options = null;

    
    private $_percentInodesUsed = null;

    
    public function getPercentUsed()
    {
        if ($this->_total > 0) {
            return round($this->_used / $this->_total * 100);
        } else {
            return 0;
        }
    }

   
    public function getPercentInodesUsed()
    {
        return $this->_percentInodesUsed;
    }

    
    public function setPercentInodesUsed($percentInodesUsed)
    {
        $this->_percentInodesUsed = $percentInodesUsed;
    }

    
    public function getFree()
    {
        return $this->_free;
    }

   
    public function setFree($free)
    {
        $this->_free = $free;
    }

    
    public function getFsType()
    {
        return $this->_fsType;
    }

    
    public function setFsType($fsType)
    {
        $this->_fsType = $fsType;
    }

    
    public function getMountPoint()
    {
        return $this->_mountPoint;
    }

    
    public function setMountPoint($mountPoint)
    {
        $this->_mountPoint = $mountPoint;
    }

    
    public function getName()
    {
        return $this->_name;
    }

    
    public function setName($name)
    {
        $this->_name = $name;
    }

    
    public function getOptions()
    {
        return $this->_options;
    }

   
    public function setOptions($options)
    {
        $this->_options = $options;
    }

   
    public function getTotal()
    {
        return $this->_total;
    }

    
    public function setTotal($total)
    {
        $this->_total = $total;
    }

    
    public function getUsed()
    {
        return $this->_used;
    }

    
    public function setUsed($used)
    {
        $this->_used = $used;
    }
}
