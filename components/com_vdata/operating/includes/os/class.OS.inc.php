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
abstract class OS implements PSI_Interface_OS
{
    
    protected $error;

   
    protected $sys;

   
    public function __construct()
    {
        
        $this->sys = new System();
    }

    /**
     * get os specific encoding
     *
     * @see PSI_Interface_OS::getEncoding()
     *
     * @return string
     */
    public function getEncoding()
    {
        return PSI_SYSTEM_CODEPAGE;
    }
    /**
     * get os specific language
     *
     * @see PSI_Interface_OS::getLanguage()
     *
     * @return string
     */
    public function getLanguage()
    {
        return PSI_SYSTEM_LANG;
    }

    /**
     * get the filled or unfilled (with default values) System object
     *
     * @see PSI_Interface_OS::getSys()
     *
     * @return System
     */
    final public function getSys()
    {
        $this->build();

        return $this->sys;
    }
}
