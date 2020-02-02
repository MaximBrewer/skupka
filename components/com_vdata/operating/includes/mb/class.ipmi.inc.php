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
class IPMI extends Sensors
{
    
    private $_lines = array();

    
    public function __construct()
    {
        parent::__construct();
        switch (strtolower(PSI_SENSOR_ACCESS)) {
        case 'command':
            CommonFunctions::executeProgram('ipmitool', 'sensor', $lines);
            $this->_lines = preg_split("/\n/", $lines, -1, PREG_SPLIT_NO_EMPTY);
            break;
        case 'file':
            if (CommonFunctions::rfts(APP_ROOT.'/data/ipmi.txt', $lines)) {
                $this->_lines = preg_split("/\n/", $lines, -1, PREG_SPLIT_NO_EMPTY);
            }
            break;
        default:
            $this->error->addConfigError('__construct()', 'PSI_SENSOR_ACCESS');
            break;
        }
    }

    /**
     * get temperature information
     *
     * @return void
     */
    private function _temperature()
    {
        foreach ($this->_lines as $line) {
            $buffer = preg_split("/\s*\|\s*/", $line);
            if ($buffer[2] == "degrees C" && $buffer[3] != "na") {
                $dev = new SensorDevice();
                $dev->setName($buffer[0]);
                $dev->setValue($buffer[1]);
                if ($buffer[8] != "na") $dev->setMax($buffer[8]);
                switch ($buffer[3]) {
                    case "nr": $dev->setEvent("Non-Recoverable"); break;
                    case "cr": $dev->setEvent("Critical"); break;
                    case "nc": $dev->setEvent("Non-Critical"); break;
                }
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
        foreach ($this->_lines as $line) {
            $buffer = preg_split("/\s*\|\s*/", $line);
            if ($buffer[2] == "Volts" && $buffer[3] != "na") {
                $dev = new SensorDevice();
                $dev->setName($buffer[0]);
                $dev->setValue($buffer[1]);
                if ($buffer[5] != "na") $dev->setMin($buffer[5]);
                if ($buffer[8] != "na") $dev->setMax($buffer[8]);
                switch ($buffer[3]) {
                    case "nr": $dev->setEvent("Non-Recoverable"); break;
                    case "cr": $dev->setEvent("Critical"); break;
                    case "nc": $dev->setEvent("Non-Critical"); break;
                }
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
        foreach ($this->_lines as $line) {
            $buffer = preg_split("/\s*\|\s*/", $line);
            if ($buffer[2] == "RPM" && $buffer[3] != "na") {
                $dev = new SensorDevice();
                $dev->setName($buffer[0]);
                $dev->setValue($buffer[1]);
                if ($buffer[8] != "na") {
                    $dev->setMin($buffer[8]);
                } elseif (($buffer[5] != "na") && ($buffer[5]<$buffer[1])) { //max instead min issue
                    $dev->setMin($buffer[5]);
                }
                switch ($buffer[3]) {
                    case "nr": $dev->setEvent("Non-Recoverable"); break;
                    case "cr": $dev->setEvent("Critical"); break;
                    case "nc": $dev->setEvent("Non-Critical"); break;
                }
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
        foreach ($this->_lines as $line) {
            $buffer = preg_split("/\s*\|\s*/", $line);
            if ($buffer[2] == "Watts" && $buffer[3] != "na") {
                $dev = new SensorDevice();
                $dev->setName($buffer[0]);
                $dev->setValue($buffer[1]);
                if ($buffer[8] != "na") $dev->setMax($buffer[8]);
                switch ($buffer[3]) {
                    case "nr": $dev->setEvent("Non-Recoverable"); break;
                    case "cr": $dev->setEvent("Critical"); break;
                    case "nc": $dev->setEvent("Non-Critical"); break;
                }
                $this->mbinfo->setMbPower($dev);
            }
        }
    }

    /**
     * get current information
     *
     * @return void
     */
    private function _current()
    {
        foreach ($this->_lines as $line) {
            $buffer = preg_split("/\s*\|\s*/", $line);
            if ($buffer[2] == "Amps" && $buffer[3] != "na") {
                $dev = new SensorDevice();
                $dev->setName($buffer[0]);
                $dev->setValue($buffer[1]);
                if ($buffer[8] != "na") $dev->setMax($buffer[8]);
                switch ($buffer[3]) {
                    case "nr": $dev->setEvent("Non-Recoverable"); break;
                    case "cr": $dev->setEvent("Critical"); break;
                    case "nc": $dev->setEvent("Non-Critical"); break;
                }
                $this->mbinfo->setMbCurrent($dev);
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
        $this->_current();
    }
}
