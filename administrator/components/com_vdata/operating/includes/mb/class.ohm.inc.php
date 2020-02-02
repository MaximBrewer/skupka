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
class OHM extends Sensors
{
/**
     * holds the COM object that we pull all the WMI data from
     *
     * @var Object
     */
    private $_buf = array();

    /**
     * fill the private content var
     */
    public function __construct()
    {
        parent::__construct();
        $_wmi = null;
        // don't set this params for local connection, it will not work
        $strHostname = '';
        $strUser = '';
        $strPassword = '';
        try {
            // initialize the wmi object
            $objLocator = new COM('WbemScripting.SWbemLocator');
            if ($strHostname == "") {
                $_wmi = $objLocator->ConnectServer($strHostname, 'root\OpenHardwareMonitor');

            } else {
                $_wmi = $objLocator->ConnectServer($strHostname, 'root\OpenHardwareMonitor', $strHostname.'\\'.$strUser, $strPassword);
            }
        } catch (Exception $e) {
            $this->error->addError("WMI connect error", "PhpSysInfo can not connect to the WMI interface for OpenHardwareMonitor data.");
        }
        if ($_wmi) {
            $this->_buf = CommonFunctions::getWMI($_wmi, 'Sensor', array('Parent', 'Name', 'SensorType', 'Value'));
        }
    }

    /**
     * get temperature information
     *
     * @return void
     */
    private function _temperature()
    {
        if ($this->_buf) foreach ($this->_buf as $buffer) {
            if ($buffer['SensorType'] == "Temperature") {
                $dev = new SensorDevice();
                $dev->setName($buffer['Parent'].' '.$buffer['Name']);
                $dev->setValue($buffer['Value']);
                $this->mbinfo->setMbTemp($dev);
            }
        }
    }

    /**
     * get voltage information
     *
     * @return void
     */
    private function _voltage()
    {
        if ($this->_buf) foreach ($this->_buf as $buffer) {
            if ($buffer['SensorType'] == "Voltage") {
                $dev = new SensorDevice();
                $dev->setName($buffer['Parent'].' '.$buffer['Name']);
                $dev->setValue($buffer['Value']);
                $this->mbinfo->setMbVolt($dev);
            }
        }
    }

    /**
     * get fan information
     *
     * @return void
     */
    private function _fans()
    {
        if ($this->_buf) foreach ($this->_buf as $buffer) {
            if ($buffer['SensorType'] == "Fan") {
                $dev = new SensorDevice();
                $dev->setName($buffer['Parent'].' '.$buffer['Name']);
                $dev->setValue($buffer['Value']);
                $this->mbinfo->setMbFan($dev);
            }
        }
    }

    /**
     * get power information
     *
     * @return void
     */
    private function _power()
    {
        if ($this->_buf) foreach ($this->_buf as $buffer) {
            if ($buffer['SensorType'] == "Power") {
                $dev = new SensorDevice();
                $dev->setName($buffer['Parent'].' '.$buffer['Name']);
                $dev->setValue($buffer['Value']);
                $this->mbinfo->setMbPower($dev);
            }
        }
    }

    /**
     * get the information
     *
     * @see PSI_Interface_Sensor::build()
     *
     * @return Void
     */
    public function build()
    {
      $this->_temperature();
      $this->_voltage();
      $this->_fans();
      $this->_power();
    }
}
