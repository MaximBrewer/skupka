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
class Webpage extends Output implements PSI_Interface_Output
{
    
    private $_language;

   
    private $_template;

    
    private $_templates = array();

    private $_languages = array();

   
    public function __construct()
    {
        parent::__construct();
        $this->_getTemplateList();
        $this->_getLanguageList();
    }

    /**
     * checking phpsysinfo.ini setting for template, if not supportet set phpsysinfo.css as default
     * checking phpsysinfo.ini setting for language, if not supported set en as default
     *
     * @return void
     */
    private function _checkTemplateLanguage()
    {
        $this->_template = trim(strtolower(PSI_DEFAULT_TEMPLATE));
        if (!file_exists(APP_ROOT.'/templates/'.$this->_template.".css")) {
            $this->_template = 'phpsysinfo';
        }

        $this->_language = trim(strtolower(PSI_DEFAULT_LANG));
        if (!file_exists(APP_ROOT.'/language/'.$this->_language.".xml")) {
            $this->_language = 'en';
        }
    }

    /**
     * get all available tamplates and store them in internal array
     *
     * @return void
     */
    private function _getTemplateList()
    {
        $dirlist = CommonFunctions::gdc(APP_ROOT.'/templates/');
        sort($dirlist);
        foreach ($dirlist as $file) {
            $tpl_ext = substr($file, strlen($file) - 4);
            $tpl_name = substr($file, 0, strlen($file) - 4);
            if (($tpl_ext === ".css") && ($tpl_name !== "phpsysinfo_bootstrap")) {
                array_push($this->_templates, $tpl_name);
            }
        }
    }

    /**
     * get all available translations and store them in internal array
     *
     * @return void
     */
    private function _getLanguageList()
    {
        $dirlist = CommonFunctions::gdc(APP_ROOT.'/language/');
        sort($dirlist);
        foreach ($dirlist as $file) {
            $lang_ext = substr($file, strlen($file) - 4);
            $lang_name = substr($file, 0, strlen($file) - 4);
            if ($lang_ext == ".xml") {
                array_push($this->_languages, $lang_name);
            }
        }
    }

    /**
     * render the page
     *
     * @return void
     */
    public function run()
    {
        $this->_checkTemplateLanguage();

        $tpl = new Template("/templates/html/index_dynamic.html");

        $tpl->set("template", $this->_template);
        $tpl->set("templates", $this->_templates);
        $tpl->set("language", $this->_language);
        $tpl->set("languages", $this->_languages);

        echo $tpl->fetch();
    }
}
