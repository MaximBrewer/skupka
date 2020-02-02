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
interface PSI_Interface_OS
{
    /**
     * get a special encoding from os where phpsysinfo is running
     *
     * @return string
     */
    public function getEncoding();

    /**
     * build the os information
     *
     * @return void
     */
    public function build();

    /**
     * get the filled or unfilled (with default values) system object
     *
     * @return System
     */
    public function getSys();
}
