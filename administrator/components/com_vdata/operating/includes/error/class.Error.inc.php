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
class Errorss
{
   
    private static $_instance;

    
    private $_arrErrorList = array();

    private $_errors = 0;

   
    private function __construct()
    {
        $this->_errors = 0;
        $this->_arrErrorList = array();
    }

    
    public static function singleton()
    {
        if (!isset(self::$_instance)) {
            $c = __CLASS__;
            self::$_instance = new $c;
        }

        return self::$_instance;
    }

  
    public function __clone()
    {
        trigger_error("Can't be cloned", E_USER_ERROR);
    }

    
    public function addError($strCommand, $strMessage)
    {
        $this->_addError($strCommand, $this->_trace($strMessage));
    }

    
    private function _addError($strCommand, $strMessage)
    {
        $index = count($this->_arrErrorList) + 1;
        $this->_arrErrorList[$index]['command'] = $strCommand;
        $this->_arrErrorList[$index]['message'] = $strMessage;
        $this->_errors++;
    }

    
    public function addConfigError($strCommand, $strMessage)
    {
        $this->_addError($strCommand, "Wrong Value in phpsysinfo.ini for ".$strMessage);
    }

    
    public function addPhpError($strCommand, $strMessage)
    {
        $this->_addError($strCommand, "PHP throws a error\n".$strMessage);
    }

    
    public function addWarning($strMessage)
    {
        $index = count($this->_arrErrorList) + 1;
        $this->_arrErrorList[$index]['command'] = "WARN";
        $this->_arrErrorList[$index]['message'] = $strMessage;
    }

   
    public function errorsAsXML()
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $root = $dom->createElement("phpsysinfo");
        $dom->appendChild($root);
        $xml = new SimpleXMLExtended(simplexml_import_dom($dom), 'UTF-8');
        $generation = $xml->addChild('Generation');
        $generation->addAttribute('version', PSI_VERSION_STRING);
        $generation->addAttribute('timestamp', time());
        $xmlerr = $xml->addChild("Errors");
        foreach ($this->_arrErrorList as $arrLine) {
//            $error = $xmlerr->addCData('Error', $arrLine['message']);
            $error = $xmlerr->addChild('Error');
            $error->addAttribute('Message', $arrLine['message']);
            $error->addAttribute('Function', $arrLine['command']);
        }
        header("Cache-Control: no-cache, must-revalidate\n");
        header("Content-Type: text/xml\n\n");
        echo $xml->getSimpleXmlElement()->asXML();
        exit();
    }
   
    public function errorsAddToXML($encoding)
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $root = $dom->createElement("Errors");
        $dom->appendChild($root);
        $xml = simplexml_import_dom($dom);
        $xmlerr = new SimpleXMLExtended($xml, $encoding);
        foreach ($this->_arrErrorList as $arrLine) {
//            $error = $xmlerr->addCData('Error', $arrLine['message']);
            $error = $xmlerr->addChild('Error');
            $error->addAttribute('Message', $arrLine['message']);
            $error->addAttribute('Function', $arrLine['command']);
        }

        return $xmlerr->getSimpleXmlElement();
    }
    
    public function errorsExist()
    {
        if ($this->_errors > 0) {
            return true;
        } else {
            return false;
        }
    }
    
    private function _trace($strMessage)
    {
        $arrTrace = array_reverse(debug_backtrace());
        $strFunc = '';
        $strBacktrace = htmlspecialchars($strMessage)."\n\n";
        foreach ($arrTrace as $val) {
            // avoid the last line, which says the error is from the error class
            if ($val == $arrTrace[count($arrTrace) - 1]) {
                break;
            }
            $strBacktrace .= str_replace(APP_ROOT, ".", $val['file']).' on line '.$val['line'];
            if ($strFunc) {
                $strBacktrace .= ' in function '.$strFunc;
            }
            if ($val['function'] == 'include' || $val['function'] == 'require' || $val['function'] == 'include_once' || $val['function'] == 'require_once') {
                $strFunc = '';
            } else {
                $strFunc = $val['function'].'(';
                if (isset($val['args'][0])) {
                    $strFunc .= ' ';
                    $strComma = '';
                    foreach ($val['args'] as $val) {
                        $strFunc .= $strComma.$this->_printVar($val);
                        $strComma = ', ';
                    }
                    $strFunc .= ' ';
                }
                $strFunc .= ')';
            }
            $strBacktrace .= "\n";
        }

        return $strBacktrace;
    }
   
    private function _printVar($var)
    {
        if (is_string($var)) {
            $search = array("\x00", "\x0a", "\x0d", "\x1a", "\x09");
            $replace = array('\0', '\n', '\r', '\Z', '\t');

            return ('"'.str_replace($search, $replace, $var).'"');
        } elseif (is_bool($var)) {
            if ($var) {
                return ('true');
            } else {
                return ('false');
            }
        } elseif (is_array($var)) {
            $strResult = 'array( ';
            $strComma = '';
            foreach ($var as $key=>$val) {
                $strResult .= $strComma.$this->_printVar($key).' => '.$this->_printVar($val);
                $strComma = ', ';
            }
            $strResult .= ' )';

            return ($strResult);
        }
        
        return (var_export($var, true));
    }
}
