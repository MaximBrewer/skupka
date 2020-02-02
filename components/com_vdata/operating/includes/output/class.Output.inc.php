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
abstract class Output
{
    /**
     * error object for logging errors
     *
     * @var Error
     */
    protected $error;

    /**
     * call the parent constructor and check for needed extensions
     */
    public function __construct()
    {
       
        
        CommonFunctions::checkForExtensions();
//        $this->error = Errorss::singleton();
//        $this->_checkConfig();
    }

    /**
     * read the config file and check for existence
     *
     * @return void
     */
    private function _checkConfig()
    {
        include_once APP_ROOT.'/read_config.php';

        if ($this->error->errorsExist()) {
            $this->error->errorsAsXML();
        }
    }
}
