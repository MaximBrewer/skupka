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
class SimpleXMLExtended
{
    
    private $_encoding = null;

    
    private $_SimpleXmlElement = null;

   
    private static $_CP437toUTF8Table = array(
        "\xC3\x87","\xC3\xBC","\xC3\xA9","\xC3\xA2",
        "\xC3\xA4","\xC3\xA0","\xC3\xA5","\xC3\xA7",
        "\xC3\xAA","\xC3\xAB","\xC3\xA8","\xC3\xAF",
        "\xC3\xAE","\xC3\xAC","\xC3\x84","\xC3\x85",
        "\xC3\x89","\xC3\xA6","\xC3\x86","\xC3\xB4",
        "\xC3\xB6","\xC3\xB2","\xC3\xBB","\xC3\xB9",
        "\xC3\xBF","\xC3\x96","\xC3\x9C","\xC3\xA2",
        "\xC2\xA3","\xC3\xA5","\xE2\x82\xA7","\xC6\x92",
        "\xC3\xA1","\xC3\xAD","\xC3\xB3","\xC3\xBA",
        "\xC3\xB1","\xC3\x91","\xC2\xAA","\xC2\xBA",
        "\xC2\xBF","\xE2\x8C\x90","\xC2\xAC","\xC2\xBD",
        "\xC2\xBC","\xC2\xA1","\xC2\xAB","\xC2\xBB",
        "\xE2\x96\x91","\xE2\x96\x92","\xE2\x96\x93","\xE2\x94\x82",
        "\xE2\x94\xA4","\xE2\x95\xA1","\xE2\x95\xA2","\xE2\x95\x96",
        "\xE2\x95\x95","\xE2\x95\xA3","\xE2\x95\x91","\xE2\x95\x97",
        "\xE2\x95\x9D","\xE2\x95\x9C","\xE2\x95\x9B","\xE2\x94\x90",
        "\xE2\x94\x94","\xE2\x94\xB4","\xE2\x94\xAC","\xE2\x94\x9C",
        "\xE2\x94\x80","\xE2\x94\xBC","\xE2\x95\x9E","\xE2\x95\x9F",
        "\xE2\x95\x9A","\xE2\x95\x94","\xE2\x95\xA9","\xE2\x95\xA6",
        "\xE2\x95\xA0","\xE2\x95\x90","\xE2\x95\xAC","\xE2\x95\xA7",
        "\xE2\x95\xA8","\xE2\x95\xA4","\xE2\x95\xA5","\xE2\x95\x99",
        "\xE2\x95\x98","\xE2\x95\x92","\xE2\x95\x93","\xE2\x95\xAB",
        "\xE2\x95\xAA","\xE2\x94\x98","\xE2\x94\x8C","\xE2\x96\x88",
        "\xE2\x96\x84","\xE2\x96\x8C","\xE2\x96\x90","\xE2\x96\x80",
        "\xCE\xB1","\xC3\x9F","\xCE\x93","\xCF\x80",
        "\xCE\xA3","\xCF\x83","\xC2\xB5","\xCF\x84",
        "\xCE\xA6","\xCE\x98","\xCE\xA9","\xCE\xB4",
        "\xE2\x88\x9E","\xCF\x86","\xCE\xB5","\xE2\x88\xA9",
        "\xE2\x89\xA1","\xC2\xB1","\xE2\x89\xA5","\xE2\x89\xA4",
        "\xE2\x8C\xA0","\xE2\x8C\xA1","\xC3\xB7","\xE2\x89\x88",
        "\xC2\xB0","\xE2\x88\x99","\xC2\xB7","\xE2\x88\x9A",
        "\xE2\x81\xBF","\xC2\xB2","\xE2\x96\xA0","\xC2\xA0");

    
    public function __construct($xml, $encoding = null)
    {
        if ($encoding != null) {
            $this->_encoding = $encoding;
        }
        $this->_SimpleXmlElement = $xml;
    }

   
    public function addChild($name, $value = null)
    {
        $nameUtf8 = $this->_toUTF8($name);
        if ($value == null) {
            return new SimpleXMLExtended($this->_SimpleXmlElement->addChild($nameUtf8), $this->_encoding);
        } else {
            $valueUtf8 = htmlspecialchars($this->_toUTF8($value));

            return new SimpleXMLExtended($this->_SimpleXmlElement->addChild($nameUtf8, $valueUtf8), $this->_encoding);
        }
    }

   
    public function addCData($name, $cdata)
    {
        $nameUtf8 = $this->_toUTF8($name);
        $node = $this->_SimpleXmlElement->addChild($nameUtf8);
        $domnode = dom_import_simplexml($node);
        $no = $domnode->ownerDocument;
        $domnode->appendChild($no->createCDATASection($cdata));

        return new SimpleXMLExtended($node, $this->_encoding);
    }

    
    public function addAttribute($name, $value)
    {
        $nameUtf8 = $this->_toUTF8($name);
        $valueUtf8 = htmlspecialchars($this->_toUTF8($value));
        $this->_SimpleXmlElement->addAttribute($nameUtf8, $valueUtf8);
    }

   
    public function combinexml(SimpleXMLElement $new_child)
    {
        $node1 = dom_import_simplexml($this->_SimpleXmlElement);
        $dom_sxe = dom_import_simplexml($new_child);
        $node2 = $node1->ownerDocument->importNode($dom_sxe, true);
        $node1->appendChild($node2);
    }

  
    private function _toUTF8($str)
    {
        if ($this->_encoding != null) {
            if (strcasecmp($this->_encoding, "UTF-8") == 0) {
                return trim($str);
            } elseif (strcasecmp($this->_encoding, "CP437") == 0) {
                $str = trim($str);
                $strr = "";
                if (($strl = strlen($str)) > 0) for ($i = 0; $i < $strl; $i++) {
                    $strc = substr($str, $i, 1);
                    if ($strc < 128) $strr.=$strc;
                        else $strr.=$_CP437toUTF8Table[$strc-128];
                }

                 return $strr;
            } else {
                $enclist = mb_list_encodings();
                if (in_array($this->_encoding, $enclist)) {
                    return mb_convert_encoding(trim($str), 'UTF-8', $this->_encoding);
                } elseif (function_exists("iconv")) {
                    return iconv($this->_encoding, 'UTF-8', trim($str));
                } else {
                    return mb_convert_encoding(trim($str), 'UTF-8');
                }
            }
        } else {
            return mb_convert_encoding(trim($str), 'UTF-8');
        }
    }

    
    public function getSimpleXmlElement()
    {
        return $this->_SimpleXmlElement;
    }
}
