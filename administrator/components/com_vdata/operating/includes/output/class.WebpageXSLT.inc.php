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
class WebpageXSLT extends WebpageXML implements PSI_Interface_Output
{
    /**
     * call the parent constructor
     */
    public function __construct()
    {
        parent::__construct(false, null);
    }

    /**
     * generate the static page
     *
     * @return void
     */
    public function run()
    {
        CommonFunctions::checkForExtensions(array('xsl'));
        $xmlfile = $this->getXMLString();
        $xslfile = "phpsysinfo.xslt";
        $domxml = new DOMDocument();
        $domxml->loadXML($xmlfile);
        $domxsl = new DOMDocument();
        $domxsl->load($xslfile);
        $xsltproc = new XSLTProcessor;
        $xsltproc->importStyleSheet($domxsl);
        echo $xsltproc->transformToXML($domxml);
    }
}
