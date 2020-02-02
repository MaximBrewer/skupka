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
class K8Temp extends Sensors
{
    /**
     * content to parse
     *
     * @var array
     */
    private $_lines = array();

    /**
     * fill the private array
     */
    public function __construct()
    {
        parent::__construct();
        switch (strtolower(PSI_SENSOR_ACCESS)) {
        case 'command':
            $lines = "";
            CommonFunctions::executeProgram('k8temp', '', $lines);
            $this->_lines = preg_split("/\n/", $lines, -1, PREG_SPLIT_NO_EMPTY);
            break;
        case 'file':
            if (CommonFunctions::rfts(APP_ROOT.'/data/k8temp.txt', $lines)) {
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
            if (preg_match('/(.*):\s*(\d*)/', $line, $data)) {
                if ($data[2] > 0) {
                    $dev = new SensorDevice();
                    $dev->setName($data[1]);
                    $dev->setMax('70.0');
                    if ($data[2] < 250) {
                        $dev->setValue($data[2]);
                    }
                    $this->mbinfo->setMbTemp($dev);
                }
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
    }
}
