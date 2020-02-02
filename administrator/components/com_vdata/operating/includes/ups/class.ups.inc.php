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
abstract class UPS implements PSI_Interface_UPS
{
    
    protected $error;

    
    protected $upsinfo;

    
    public function __construct()
    {
        $this->error = Errorss::singleton();
        $this->upsinfo = new UPSInfo();
    }

    
    final public function getUPSInfo()
    {
        $this->build();

        return $this->upsinfo;
    }
}
