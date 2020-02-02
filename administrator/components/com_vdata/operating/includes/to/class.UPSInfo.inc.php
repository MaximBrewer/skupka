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
class UPSInfo
{
    /**
     * array with upsdivices
     *
     * @see UPSDevice
     *
     * @var Array
     */
    private $_upsDevices = array();

    /**
     * Returns $_upsDevices.
     *
     * @see UPSInfo::$_upsDevices
     *
     * @return Array
     */
    public function getUpsDevices()
    {
        return $this->_upsDevices;
    }

    /**
     * Sets $_upsDevices.
     *
     * @param UPSDevice $upsDevices upsdevice
     *
     * @see UPSInfo::$_upsDevices
     *
     * @return Void
     */
    public function setUpsDevices($upsDevices)
    {
        array_push($this->_upsDevices, $upsDevices);
    }
}
