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
class Coretemp extends Sensors
{
    
    private function _temperature()
    {
        $smp = 1;
        CommonFunctions::executeProgram('sysctl', '-n kern.smp.cpus', $smp);
        for ($i = 0; $i < $smp; $i++) {
            $temp = 0;
            if (CommonFunctions::executeProgram('sysctl', '-n dev.cpu.'.$i.'.temperature', $temp)) {
                $temp = preg_replace('/C/', '', $temp);
                $dev = new SensorDevice();
                $dev->setName("CPU ".($i + 1));
                $dev->setValue($temp);
                $dev->setMax(70);
                $this->mbinfo->setMbTemp($dev);
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
