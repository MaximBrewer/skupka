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
    /**
     * name of the device
     *
     * @var String
     */
    private $_name = "";

    /**
     * transmitted bytes
     *
     * @var Integer
     */
    private $_txBytes = 0;

    /**
     * received bytes
     *
     * @var Integer
     */
    private $_rxBytes = 0;

    /**
     * counted error packages
     *
     * @var Integer
     */
    private $_errors = 0;

    /**
     * counted droped packages
     *
     * @var Integer
     */
    private $_drops = 0;

    /**
     * string with info
     *
     * @var String
     */
    private $_info = null;

    /**
     * Returns $_drops.
     *
     * @see NetDevice::$_drops
     *
     * @return Integer
     */
    public function getDrops()
    {
        return $this->_drops;
    }

    /**
     * Sets $_drops.
     *
     * @param Integer $drops dropped packages
     *
     * @see NetDevice::$_drops
     *
     * @return Void
     */
    public function setDrops($drops)
    {
        $this->_drops = $drops;
    }

    /**
     * Returns $_errors.
     *
     * @see NetDevice::$_errors
     *
     * @return Integer
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * Sets $_errors.
     *
     * @param Integer $errors error packages
     *
     * @see NetDevice::$_errors
     *
     * @return Void
     */
    public function setErrors($errors)
    {
        $this->_errors = $errors;
    }

    /**
     * Returns $_name.
     *
     * @see NetDevice::$_name
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
     * @see NetDevice::$_name
     *
     * @return Void
     */
    public function setName($name)
    {
        $this->_name = $name;
    }

    /**
     * Returns $_rxBytes.
     *
     * @see NetDevice::$_rxBytes
     *
     * @return Integer
     */
    public function getRxBytes()
    {
        return $this->_rxBytes;
    }

    /**
     * Sets $_rxBytes.
     *
     * @param Integer $rxBytes received bytes
     *
     * @see NetDevice::$_rxBytes
     *
     * @return Void
     */
    public function setRxBytes($rxBytes)
    {
        $this->_rxBytes = $rxBytes;
    }

    /**
     * Returns $_txBytes.
     *
     * @see NetDevice::$_txBytes
     *
     * @return Integer
     */
    public function getTxBytes()
    {
        return $this->_txBytes;
    }

    /**
     * Sets $_txBytes.
     *
     * @param Integer $txBytes transmitted bytes
     *
     * @see NetDevice::$_txBytes
     *
     * @return Void
     */
    public function setTxBytes($txBytes)
    {
        $this->_txBytes = $txBytes;
    }

    /**
     * Returns $_info.
     *
     * @see NetDevice::$_info
     *
     * @return String
     */
    public function getInfo()
    {
        return $this->_info;
    }

    /**
     * Sets $_info.
     *
     * @param String $info info string
     *
     * @see NetDevice::$_info
     *
     * @return Void
     */
    public function setInfo($info)
    {
        $this->_info = $info;
    }
}
