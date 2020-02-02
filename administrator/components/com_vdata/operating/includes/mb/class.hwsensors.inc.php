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
class HWSensors extends Sensors
{
    /**
     * content to parse
     *
     * @var array
     */
    private $_lines = array();

    /**
     * fill the private content var through tcp or file access
     */
    public function __construct()
    {
        parent::__construct();
        $lines = "";
//        CommonFunctions::executeProgram('sysctl', '-w hw.sensors', $lines);
        CommonFunctions::executeProgram('sysctl', 'hw.sensors', $lines);
        $this->_lines = preg_split("/\n/", $lines, -1, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * get temperature information
     *
     * @return void
     */
    private function _temperature()
    {
        foreach ($this->_lines as $line) {
            if (preg_match('/^hw\.sensors\.[0-9]+=[^\s,]+,\s+([^,]+),\s+temp,\s+([0-9\.]+)\s+degC.*$/', $line, $ar_buf)) {
                $dev = new SensorDevice();
                $dev->setName($ar_buf[1]);
                $dev->setValue($ar_buf[2]);
                $this->mbinfo->setMbTemp($dev);
            } elseif (preg_match('/^hw\.sensors\.[0-9]+=[^\s,]+,\s+([^,]+),\s+([0-9\.]+)\s+degC$/', $line, $ar_buf)) {
                $dev = new SensorDevice();
                $dev->setName($ar_buf[1]);
                $dev->setValue($ar_buf[2]);
                $this->mbinfo->setMbTemp($dev);
            } elseif (preg_match('/^hw\.sensors\.[^\.]+\.(.*)=([0-9\.]+)\s+degC\s+\((.*)\)$/', $line, $ar_buf)) {
                $dev = new SensorDevice();
                $dev->setName($ar_buf[3]);
                $dev->setValue($ar_buf[2]);
                $this->mbinfo->setMbTemp($dev);
            } elseif (preg_match('/^hw\.sensors\.[^\.]+\.(.*)=([0-9\.]+)\s+degC$/', $line, $ar_buf)) {
                $dev = new SensorDevice();
                $dev->setName($ar_buf[1]);
                $dev->setValue($ar_buf[2]);
                $this->mbinfo->setMbTemp($dev);
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
            if (preg_match('/^hw\.sensors\.[0-9]+=[^\s,]+,\s+([^,]+),\s+fanrpm,\s+([0-9\.]+)\s+RPM.*$/', $line, $ar_buf)) {
                $dev = new SensorDevice();
                $dev->setName($ar_buf[1]);
                $dev->setValue($ar_buf[2]);
                $this->mbinfo->setMbFan($dev);
            } elseif (preg_match('/^hw\.sensors\.[0-9]+=[^\s,]+,\s+([^,]+),\s+([0-9\.]+)\s+RPM$/', $line, $ar_buf)) {
                $dev = new SensorDevice();
                $dev->setName($ar_buf[1]);
                $dev->setValue($ar_buf[2]);
                $this->mbinfo->setMbFan($dev);
            } elseif (preg_match('/^hw\.sensors\.[^\.]+\.(.*)=([0-9\.]+)\s+RPM\s+\((.*)\)$/', $line, $ar_buf)) {
                $dev = new SensorDevice();
                $dev->setName($ar_buf[3]);
                $dev->setValue($ar_buf[2]);
                $this->mbinfo->setMbFan($dev);
            } elseif (preg_match('/^hw\.sensors\.[^\.]+\.(.*)=([0-9\.]+)\s+RPM$/', $line, $ar_buf)) {
                $dev = new SensorDevice();
                $dev->setName($ar_buf[1]);
                $dev->setValue($ar_buf[2]);
                $this->mbinfo->setMbFan($dev);
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
            if (preg_match('/^hw\.sensors\.[0-9]+=[^\s,]+,\s+([^,]+),\s+volts_dc,\s+([0-9\.]+)\s+V.*$/', $line, $ar_buf)) {
                $dev = new SensorDevice();
                $dev->setName($ar_buf[1]);
                $dev->setValue($ar_buf[2]);
                $this->mbinfo->setMbVolt($dev);
            } elseif (preg_match('/^hw\.sensors\.[0-9]+=[^\s,]+,\s+([^,]+),\s+([0-9\.]+)\s+V\sDC$/', $line, $ar_buf)) {
                $dev = new SensorDevice();
                $dev->setName($ar_buf[1]);
                $dev->setValue($ar_buf[2]);
                $this->mbinfo->setMbVolt($dev);
            } elseif (preg_match('/^hw\.sensors\.[^\.]+\.(.*)=([0-9\.]+)\s+VDC\s+\((.*)\)$/', $line, $ar_buf)) {
                $dev = new SensorDevice();
                $dev->setName($ar_buf[3]);
                $dev->setValue($ar_buf[2]);
                $this->mbinfo->setMbVolt($dev);
            } elseif (preg_match('/^hw\.sensors\.[^\.]+\.(.*)=([0-9\.]+)\s+VDC$/', $line, $ar_buf)) {
                $dev = new SensorDevice();
                $dev->setName($ar_buf[1]);
                $dev->setValue($ar_buf[2]);
                $this->mbinfo->setMbVolt($dev);
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
    }
}
