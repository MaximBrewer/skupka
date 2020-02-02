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
class HWDevice
{
    
    private $_name = "";

   
    private $_capacity = null;

    
    private $_count = 1;

    
    public function equals(HWDevice $dev)
    {
        if ($dev->getName() === $this->_name && $dev->getCapacity() === $this->_capacity) {
            return true;
        } else {
            return false;
        }
    }

   
    public function getCapacity()
    {
        return $this->_capacity;
    }

    
    public function setCapacity($capacity)
    {
        $this->_capacity = $capacity;
    }


    public function getName()
    {
        return $this->_name;
    }

    
    public function setName($name)
    {
        $this->_name = $name;
    }

    
    public function getCount()
    {
        return $this->_count;
    }

   
    public function setCount($count)
    {
        $this->_count = $count;
    }
}
