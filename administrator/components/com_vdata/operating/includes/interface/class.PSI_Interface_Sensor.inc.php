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
interface PSI_Interface_Sensor
{
    /**
     * build the mbinfo information
     *
     * @return void
     */
    public function build();

    /**
     * get the filled or unfilled (with default values) MBInfo object
     *
     * @return MBInfo
     */
    public function getMBInfo();
}
