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
class MBM5 extends Sensors
{
    /**
     * array with the names of the labels
     *
     * @var array
     */
    private $_buf_label = array();

    /**
     * array withe the values
     *
     * @var array
     */
    private $_buf_value = array();

    /**
     * read the MBM5.csv file and fill the private arrays
     */
    public function __construct()
    {
        parent::__construct();
        switch (strtolower(PSI_SENSOR_ACCESS)) {
        case 'file':
            $delim = "/;/";
            CommonFunctions::rfts(APP_ROOT."/data/MBM5.csv", $buffer);
            if (strpos($buffer, ";") === false) {
                $delim = "/,/";
            }
            $buffer = preg_split("/\n/", $buffer, -1, PREG_SPLIT_NO_EMPTY);
            $this->_buf_label = preg_split($delim, substr($buffer[0], 0, -2), -1, PREG_SPLIT_NO_EMPTY);
            $this->_buf_value = preg_split($delim, substr($buffer[1], 0, -2), -1, PREG_SPLIT_NO_EMPTY);
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
        for ($intPosi = 3; $intPosi < 6; $intPosi++) {
            if ($this->_buf_value[$intPosi] == 0) {
                continue;
            }
            preg_match("/([0-9\.])*/", str_replace(",", ".", $this->_buf_value[$intPosi]), $hits);
            $dev = new SensorDevice();
            $dev->setName($this->_buf_label[$intPosi]);
            $dev->setValue($hits[0]);
            $dev->setMax(70);
            $this->mbinfo->setMbTemp($dev);
        }
    }

    /**
     * get fan information
     *
     * @return void
     */
    private function _fans()
    {
        for ($intPosi = 13; $intPosi < 16; $intPosi++) {
            if (!isset($this->_buf_value[$intPosi])) {
                continue;
            }
            preg_match("/([0-9\.])*/", str_replace(",", ".", $this->_buf_value[$intPosi]), $hits);
            $dev = new SensorDevice();
            $dev->setName($this->_buf_label[$intPosi]);
            $dev->setValue($hits[0]);
            $dev->setMin(3000);
            $this->mbinfo->setMbFan($dev);
        }
    }

    /**
     * get voltage information
     *
     * @return void
     */
    private function _voltage()
    {
        for ($intPosi = 6; $intPosi < 13; $intPosi++) {
            if ($this->_buf_value[$intPosi] == 0) {
                continue;
            }
            preg_match("/([0-9\.])*/", str_replace(",", ".", $this->_buf_value[$intPosi]), $hits);
            $dev = new SensorDevice();
            $dev->setName($this->_buf_label[$intPosi]);
            $dev->setValue($hits[0]);
            $this->mbinfo->setMbVolt($dev);
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
        $this->_fans();
        $this->_temperature();
        $this->_voltage();
    }
}
