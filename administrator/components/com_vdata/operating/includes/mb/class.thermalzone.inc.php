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
class ThermalZone extends Sensors
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
        if (PSI_OS == 'WINNT') {
            $_wmi = null;
            // don't set this params for local connection, it will not work
            $strHostname = '';
            $strUser = '';
            $strPassword = '';
            try {
                // initialize the wmi object
                $objLocator = new COM('WbemScripting.SWbemLocator');
                if ($strHostname == "") {
                    $_wmi = $objLocator->ConnectServer($strHostname, 'root\WMI');

                } else {
                    $_wmi = $objLocator->ConnectServer($strHostname, 'root\WMI', $strHostname.'\\'.$strUser, $strPassword);
                }
            } catch (Exception $e) {
                $this->error->addError("WMI connect error", "PhpSysInfo can not connect to the WMI interface for ThermalZone data.");
            }
            if ($_wmi) {
                $this->_buf = CommonFunctions::getWMI($_wmi, 'MSAcpi_ThermalZoneTemperature', array('InstanceName', 'CriticalTripPoint', 'CurrentTemperature'));
            }
        }
    }

    /**
     * get temperature information
     *
     * @return void
     */
    private function _temperature()
    {
        if (PSI_OS == 'WINNT') {
            if ($this->_buf) foreach ($this->_buf as $buffer) {
                if (isset($buffer['CurrentTemperature']) && (($value = ($buffer['CurrentTemperature'] - 2732)/10) > -100)) {
                    $dev = new SensorDevice();
                    if (isset($buffer['InstanceName']) && preg_match("/([^\\\\ ]+)$/", $buffer['InstanceName'], $outbuf)) {
                        $dev->setName('ThermalZone '.$outbuf[1]);
                    } else {
                        $dev->setName('ThermalZone THM0_0');
                    }
                    $dev->setValue($value);
                    if (isset($buffer['CriticalTripPoint']) && (($maxvalue = ($buffer['CriticalTripPoint'] - 2732)/10) > 0)) {
                        $dev->setMax($maxvalue);
                    }
                    $this->mbinfo->setMbTemp($dev);
                }
            }
        } else {
            foreach (glob('/sys/class/thermal/thermal_zone*/') as $thermalzone) {
                $thermalzonetemp = $thermalzone.'temp';
                $temp = null;
                if (CommonFunctions::rfts($thermalzonetemp, $temp, 0, 4096, false) && !is_null($temp) && (trim($temp) != "")) {
                    if ($temp >= 1000) {
                        $temp = $temp / 1000;
                    }
                    
                    if ($temp > -40) {
                        $dev = new SensorDevice();
                        $dev->setValue($temp);

                        $temp_type = null;
                        if (CommonFunctions::rfts($thermalzone.'type', $temp_type, 0, 4096, false) && !is_null($temp_type) && (trim($temp_type) != "")) {
                            $dev->setName($temp_type);
                        }

                        $temp_max = null;
                        if (CommonFunctions::rfts($thermalzone.'trip_point_0_temp', $temp_max, 0, 4096, false) && !is_null($temp_max) && (trim($temp_max) != "") && ($temp_max > 0)) {
                            if ($temp_max >= 1000) {
                                $temp_max = $temp_max / 1000;
                            }
                            $dev->setMax($temp_max);
                        }

                        $this->mbinfo->setMbTemp($dev);
                    }
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
