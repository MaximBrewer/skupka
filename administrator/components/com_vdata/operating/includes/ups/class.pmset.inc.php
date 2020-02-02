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
class Pmset extends UPS
{
    
    private $_output = array();

   
    public function __construct()
    {
        parent::__construct();
        CommonFunctions::executeProgram('pmset', '-g batt', $temp);
        if (! empty($temp)) {
            $this->_output[] = $temp;
        }
    }

    
   private function _info()
    {
        $model = array();
        $percCharge = array();
        $lines = explode(PHP_EOL, implode($this->_output));
        $dev = new UPSDevice();
        $model = explode('FW:',  $lines[1]);
        if (strpos($model[0], 'InternalBattery') === false) {
            $percCharge = explode(';',  $lines[1]);
            $dev->setName('UPS');
            if ($model !== false) {
                $dev->setModel(substr(trim($model[0]), 1));
            }
            if ($percCharge !== false) {
                $dev->setBatterCharge(trim(substr($percCharge[0], -4, 3)));
                $dev->setStatus(trim($percCharge[1]));
                if (isset($percCharge[2])) {
                    $time = explode(':', $percCharge[2]);
                    $hours = $time[0];
                    $minutes = $hours*60+substr($time[1], 0, 2);
                    $dev->setTimeLeft($minutes);
                }
            }
            $this->upsinfo->setUpsDevices($dev);
        }
    }

   
    public function build()
    {
        $this->_info();
    }
}
