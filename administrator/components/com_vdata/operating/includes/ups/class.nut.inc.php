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
class Nut extends UPS
{
    
    private $_output = array();

    
    public function __construct()
    {
        parent::__construct();
        if (defined('PSI_UPS_NUT_LIST') && is_string(PSI_UPS_NUT_LIST)) {
            if (preg_match(ARRAY_EXP, PSI_UPS_NUT_LIST)) {
                $upses = eval(PSI_UPS_NUT_LIST);
            } else {
                $upses = array(PSI_UPS_NUT_LIST);
            }
            foreach ($upses as $ups) {
                CommonFunctions::executeProgram('upsc', '-l '.trim($ups), $output);
                $ups_names = preg_split("/\n/", $output, -1, PREG_SPLIT_NO_EMPTY);
                foreach ($ups_names as $ups_name) {
                    CommonFunctions::executeProgram('upsc', trim($ups_name).'@'.trim($ups), $temp);
                    if (! empty($temp)) {
                        $this->_output[trim($ups_name).'@'.trim($ups)] = $temp;
                    }
                }
            }
        } else { //use default if address and port not defined
            CommonFunctions::executeProgram('upsc', '-l', $output);
            $ups_names = preg_split("/\n/", $output, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($ups_names as $ups_name) {
                CommonFunctions::executeProgram('upsc', trim($ups_name), $temp);
                if (! empty($temp)) {
                    $this->_output[trim($ups_name)] = $temp;
                }
            }
        }
    }

   
    private function _info()
    {
        if (! empty($this->_output)) {
            foreach ($this->_output as $name=>$value) {
                $temp = preg_split("/\n/", $value, -1, PREG_SPLIT_NO_EMPTY);
                $ups_data = array();
                foreach ($temp as $value) {
                    $line = preg_split('/: /', $value, 2);
                    $ups_data[$line[0]] = isset($line[1]) ? trim($line[1]) : '';
                }
                $dev = new UPSDevice();
                //General
                $dev->setName($name);
                if (isset($ups_data['ups.model'])) {
                    $dev->setModel($ups_data['ups.model']);
                }
                if (isset($ups_data['driver.name'])) {
                    $dev->setMode($ups_data['driver.name']);
                }
                if (isset($ups_data['ups.status'])) {
                    $dev->setStatus($ups_data['ups.status']);
                }

                //Line
                if (isset($ups_data['input.voltage'])) {
                    $dev->setLineVoltage($ups_data['input.voltage']);
                }
                if (isset($ups_data['input.frequency'])) {
                    $dev->setLineFrequency($ups_data['input.frequency']);
                }
                if (isset($ups_data['ups.load'])) {
                    $dev->setLoad($ups_data['ups.load']);
                }

                //Battery
                if (isset($ups_data['battery.voltage'])) {
                    $dev->setBatteryVoltage($ups_data['battery.voltage']);
                }
                if (isset($ups_data['battery.charge'])) {
                    $dev->setBatterCharge($ups_data['battery.charge']);
                }
                if (isset($ups_data['battery.runtime'])) {
                    $dev->setTimeLeft(round($ups_data['battery.runtime']/60, 2));
                }

                //Temperature
                if (isset($ups_data['ups.temperature'])) {
                    $dev->setTemperatur($ups_data['ups.temperature']);
                }

                $this->upsinfo->setUpsDevices($dev);
            }
        }
    }

   
    public function build()
    {
        $this->_info();
    }
}
