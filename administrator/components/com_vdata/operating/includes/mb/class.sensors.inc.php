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
abstract class Sensors implements PSI_Interface_Sensor
{
    /**
     * object for error handling
     *
     * @var Error
     */
    protected $error;

    /**
     * object for the information
     *
     * @var MBInfo
     */
    protected $mbinfo;

    /**
     * build the global Error object
     */
    public function __construct()
    {
        $this->error = Errorss::singleton();
        $this->mbinfo = new MBInfo();
    }

    /**
     * get the filled or unfilled (with default values) MBInfo object
     *
     * @see PSI_Interface_Sensor::getMBInfo()
     *
     * @return MBInfo
     */
    final public function getMBInfo()
    {
        $this->build();

        return $this->mbinfo;
    }
}
