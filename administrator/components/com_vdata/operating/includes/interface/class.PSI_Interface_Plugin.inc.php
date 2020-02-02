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
interface PSI_Interface_Plugin
{
    /**
     * doing all tasks before the xml can be build
     *
     * @return void
     */
    public function execute();

    /**
     * build the xml
     *
     * @return SimpleXMLObject entire XML content for the plugin which than can be appended to the main XML
     */
    public function xml();
}
