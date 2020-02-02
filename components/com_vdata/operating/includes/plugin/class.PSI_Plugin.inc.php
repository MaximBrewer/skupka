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
abstract class PSI_Plugin implements PSI_Interface_Plugin
{
    
    private $_plugin_name = "";

  
    private $_plugin_base = "";

    
    protected $global_error = "";

   
    protected $xml;

    
    public function __construct($plugin_name, $enc)
    {
        $this->global_error = Errorss::Singleton();
        if (trim($plugin_name) != "") {
            $this->_plugin_name = $plugin_name;
            $this->_plugin_base = APP_ROOT."/plugins/".strtolower($this->_plugin_name)."/";
            $this->_checkfiles();
            $this->_getconfig();
        } else {
            $this->global_error->addError("__construct()", "Parent constructor called without Plugin-Name!");
        }
        $this->_createXml($enc);
    }

    
    private function _getconfig()
    {
        if ((!defined('PSI_PLUGIN_'.strtoupper($this->_plugin_name).'_ACCESS')) &&
             (!defined('PSI_PLUGIN_'.strtoupper($this->_plugin_name).'_FILE'))) {
                $this->global_error->addError("config.ini", "Config for plugin ".$this->_plugin_name." not exist!");
        }
    }

    
    private function _checkfiles()
    {
        if (!file_exists($this->_plugin_base."js/".strtolower($this->_plugin_name).".js")) {
            $this->global_error->addError("file_exists(".$this->_plugin_base."js/".strtolower($this->_plugin_name).".js)", "JS-File for Plugin '".$this->_plugin_name."' is missing!");
        } else {
            if (!is_readable($this->_plugin_base."js/".strtolower($this->_plugin_name).".js")) {
                $this->global_error->addError("is_readable(".$this->_plugin_base."js/".strtolower($this->_plugin_name).".js)", "JS-File for Plugin '".$this->_plugin_name."' is not readable but present!");
            }
        }
        if (!file_exists($this->_plugin_base."lang/en.xml")) {
            $this->global_error->addError("file_exists(".$this->_plugin_base."lang/en.xml)", "At least an english translation must exist for the plugin!");
        } else {
            if (!is_readable($this->_plugin_base."lang/en.xml")) {
                $this->global_error->addError("is_readable(".$this->_plugin_base."js/".$this->_plugin_name.".js)", "The english translation can't be read but is present!");
            }
        }
    }

    
    private function _createXml($enc)
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $root = $dom->createElement("Plugin_".$this->_plugin_name);
        $dom->appendChild($root);
        $this->xml = new SimpleXMLExtended(simplexml_import_dom($dom), $enc);
    }
}
