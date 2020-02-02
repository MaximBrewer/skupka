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
class IPMIutil extends Sensors
{
    
    private $_lines = array();

  
    public function __construct()
    {
        parent::__construct();
        switch (strtolower(PSI_SENSOR_ACCESS)) {
        case 'command':
            CommonFunctions::executeProgram('ipmiutil', 'sensor -stw', $lines);
            $this->_lines = preg_split("/\r?\n/", $lines, -1, PREG_SPLIT_NO_EMPTY);
            break;
        case 'file':
            if (CommonFunctions::rfts(APP_ROOT.'/data/ipmiutil.txt', $lines)) {
                $this->_lines = preg_split("/\r?\n/", $lines, -1, PREG_SPLIT_NO_EMPTY);
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
            if (isset($buffer[2]) && $buffer[2] == "Temperature" && $buffer[1] == "Full" && isset($buffer[6]) && preg_match("/^(\S+)\sC$/", $buffer[6], $value)) {
                $dev = new SensorDevice();
                $dev->setName($buffer[4]);
                $dev->setValue($value[1]);
                if (isset($buffer[7]) && $buffer[7] == "Thresholds") {
                    if ((isset($buffer[8]) && preg_match("/^hi-crit\s(\S+)\s*$/", $buffer[8], $limits))
                        ||(isset($buffer[9]) && preg_match("/^hi-crit\s(\S+)\s*$/", $buffer[9], $limits))
                        ||(isset($buffer[10]) && preg_match("/^hi-crit\s(\S+)\s*$/", $buffer[10], $limits))
                        ||(isset($buffer[11]) && preg_match("/^hi-crit\s(\S+)\s*$/", $buffer[11], $limits))) {
                        $dev->setMax($limits[1]);
                    }
                }
                if ($buffer[5] != "OK") $dev->setEvent($buffer[5]);
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
            if (isset($buffer[2]) && $buffer[2] == "Voltage" && $buffer[1] == "Full" && isset($buffer[6]) && preg_match("/^(\S+)\sV$/", $buffer[6], $value)) {
                $dev = new SensorDevice();
                $dev->setName($buffer[4]);
                $dev->setValue($value[1]);
                if (isset($buffer[7]) && $buffer[7] == "Thresholds") {
                    if ((isset($buffer[8]) && preg_match("/^lo-crit\s(\S+)\s*$/", $buffer[8], $limits))
                        ||(isset($buffer[9]) && preg_match("/^lo-crit\s(\S+)\s*$/", $buffer[9], $limits))
                        ||(isset($buffer[10]) && preg_match("/^lo-crit\s(\S+)\s*$/", $buffer[10], $limits))
                        ||(isset($buffer[11]) && preg_match("/^lo-crit\s(\S+)\s*$/", $buffer[11], $limits))) {
                        $dev->setMin($limits[1]);
                    }
                    if ((isset($buffer[8]) && preg_match("/^hi-crit\s(\S+)\s*$/", $buffer[8], $limits))
                        ||(isset($buffer[9]) && preg_match("/^hi-crit\s(\S+)\s*$/", $buffer[9], $limits))
                        ||(isset($buffer[10]) && preg_match("/^hi-crit\s(\S+)\s*$/", $buffer[10], $limits))
                        ||(isset($buffer[11]) && preg_match("/^hi-crit\s(\S+)\s*$/", $buffer[11], $limits))) {
                        $dev->setMax($limits[1]);
                    }
                }
                if ($buffer[5] != "OK") $dev->setEvent($buffer[5]);
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
            if (isset($buffer[2]) && $buffer[2] == "Fan" && $buffer[1] == "Full" && isset($buffer[6]) && preg_match("/^(\S+)\sRPM$/", $buffer[6], $value)) {
                $dev = new SensorDevice();
                $dev->setName($buffer[4]);
                $dev->setValue($value[1]);
                if (isset($buffer[7]) && $buffer[7] == "Thresholds") {
                    if ((isset($buffer[8]) && preg_match("/^lo-crit\s(\S+)\s*$/", $buffer[8], $limits))
                        ||(isset($buffer[9]) && preg_match("/^lo-crit\s(\S+)\s*$/", $buffer[9], $limits))
                        ||(isset($buffer[10]) && preg_match("/^lo-crit\s(\S+)\s*$/", $buffer[10], $limits))
                        ||(isset($buffer[11]) && preg_match("/^lo-crit\s(\S+)\s*$/", $buffer[11], $limits))) {
                        $dev->setMin($limits[1]);
                    } elseif ((isset($buffer[8]) && preg_match("/^hi-crit\s(\S+)\s*$/", $buffer[8], $limits))
                        ||(isset($buffer[9]) && preg_match("/^hi-crit\s(\S+)\s*$/", $buffer[9], $limits))
                        ||(isset($buffer[10]) && preg_match("/^hi-crit\s(\S+)\s*$/", $buffer[10], $limits))
                        ||(isset($buffer[11]) && preg_match("/^hi-crit\s(\S+)\s*$/", $buffer[11], $limits))) {
                        if ($limits[1]<$value[1]) {//max instead min issue
                            $dev->setMin($limits[1]);
                        }
                    }
                }
                if ($buffer[5] != "OK") $dev->setEvent($buffer[5]);
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
            if (isset($buffer[2]) && $buffer[2] == "Current" && $buffer[1] == "Full" && isset($buffer[6]) && preg_match("/^(\S+)\sW$/", $buffer[6], $value)) {
                $dev = new SensorDevice();
                $dev->setName($buffer[4]);
                $dev->setValue($value[1]);
                if (isset($buffer[7]) && $buffer[7] == "Thresholds") {
                    if ((isset($buffer[8]) && preg_match("/^hi-crit\s(\S+)\s*$/", $buffer[8], $limits))
                        ||(isset($buffer[9]) && preg_match("/^hi-crit\s(\S+)\s*$/", $buffer[9], $limits))
                        ||(isset($buffer[10]) && preg_match("/^hi-crit\s(\S+)\s*$/", $buffer[10], $limits))
                        ||(isset($buffer[11]) && preg_match("/^hi-crit\s(\S+)\s*$/", $buffer[11], $limits))) {
                        $dev->setMax($limits[1]);
                    }
                }
                if ($buffer[5] != "OK") $dev->setEvent($buffer[5]);
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
            if (isset($buffer[2]) && $buffer[2] == "Current" && $buffer[1] == "Full" && isset($buffer[6]) && preg_match("/^(\S+)\sA$/", $buffer[6], $value)) {
                $dev = new SensorDevice();
                $dev->setName($buffer[4]);
                $dev->setValue($value[1]);
                if (isset($buffer[7]) && $buffer[7] == "Thresholds") {
                    if ((isset($buffer[8]) && preg_match("/^hi-crit\s(\S+)\s*$/", $buffer[8], $limits))
                        ||(isset($buffer[9]) && preg_match("/^hi-crit\s(\S+)\s*$/", $buffer[9], $limits))
                        ||(isset($buffer[10]) && preg_match("/^hi-crit\s(\S+)\s*$/", $buffer[10], $limits))
                        ||(isset($buffer[11]) && preg_match("/^hi-crit\s(\S+)\s*$/", $buffer[11], $limits))) {
                        $dev->setMax($limits[1]);
                    }
                }
                if ($buffer[5] != "OK") $dev->setEvent($buffer[5]);
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
