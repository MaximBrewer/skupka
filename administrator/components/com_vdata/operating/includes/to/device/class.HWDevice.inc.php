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
    /**
     * name of the device
     *
     * @var String
     */
    private $_name = "";

    /**
     * capacity of the device, if not available it will be null
     *
     * @var Integer
     */
    private $_capacity = null;

    /**
     * count of the device
     *
     * @var Integer
     */
    private $_count = 1;

    /**
     * compare a given device with the internal one
     *
     * @param HWDevice $dev device that should be compared
     *
     * @return boolean
     */
    public function equals(HWDevice $dev)
    {
        if ($dev->getName() === $this->_name && $dev->getCapacity() === $this->_capacity) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns $_capacity.
     *
     * @see HWDevice::$_capacity
     *
     * @return Integer
     */
    public function getCapacity()
    {
        return $this->_capacity;
    }

    /**
     * Sets $_capacity.
     *
     * @param Integer $capacity device capacity
     *
     * @see HWDevice::$_capacity
     *
     * @return Void
     */
    public function setCapacity($capacity)
    {
        $this->_capacity = $capacity;
    }

    /**
     * Returns $_name.
     *
     * @see HWDevice::$_name
     *
     * @return String
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Sets $_name.
     *
     * @param String $name device name
     *
     * @see HWDevice::$_name
     *
     * @return Void
     */
    public function setName($name)
    {
        $this->_name = $name;
    }

    /**
     * Returns $_count.
     *
     * @see HWDevice::$_count
     *
     * @return Integer
     */
    public function getCount()
    {
        return $this->_count;
    }

    /**
     * Sets $_count.
     *
     * @param Integer $count device count
     *
     * @see HWDevice::$_count
     *
     * @return Void
     */
    public function setCount($count)
    {
        $this->_count = $count;
    }
}
