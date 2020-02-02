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
class HDDTemp extends Sensors
{
    
    private function _temperature()
    {
        $ar_buf = array();
        switch (strtolower(PSI_HDD_TEMP)) {
        case "tcp":
            $lines = '';
            // Timo van Roermund: connect to the hddtemp daemon, use a 5 second timeout.
            $fp = @fsockopen('localhost', 7634, $errno, $errstr, 5);
            // if connected, read the output of the hddtemp daemon
            if ($fp) {
                while (!feof($fp)) {
                    $lines .= fread($fp, 1024);
                }
                fclose($fp);
            } else {
                $this->error->addError("HDDTemp error", $errno.", ".$errstr);
            }
            $lines = str_replace("||", "|\n|", $lines);
            $ar_buf = preg_split("/\n/", $lines, -1, PREG_SPLIT_NO_EMPTY);
            break;
        case "command":
            $strDrives = "";
            $strContent = "";
            $hddtemp_value = "";
            if (CommonFunctions::rfts("/proc/diskstats", $strContent, 0, 4096, false)) {
                $arrContent = preg_split("/\n/", $strContent, -1, PREG_SPLIT_NO_EMPTY);
                foreach ($arrContent as $strLine) {
                    preg_match("/^\s(.*)\s([a-z]*)\s(.*)/", $strLine, $arrSplit);
                    if (! empty($arrSplit[2])) {
                        $strDrive = '/dev/'.$arrSplit[2];
                        if (file_exists($strDrive)) {
                            $strDrives = $strDrives.$strDrive.' ';
                        }
                    }
                }
            } else {
                if (CommonFunctions::rfts("/proc/partitions", $strContent, 0, 4096, false)) {
                    $arrContent = preg_split("/\n/", $strContent, -1, PREG_SPLIT_NO_EMPTY);
                    foreach ($arrContent as $strLine) {
                        if (!preg_match("/^\s(.*)\s([\/a-z0-9]*(\/disc))\s(.*)/", $strLine, $arrSplit)) {
                            preg_match("/^\s(.*)\s([a-z]*)\s(.*)/", $strLine, $arrSplit);
                        }
                        if (! empty($arrSplit[2])) {
                            $strDrive = '/dev/'.$arrSplit[2];
                            if (file_exists($strDrive)) {
                                $strDrives = $strDrives.$strDrive.' ';
                            }
                        }
                    }
                }
            }
            if (trim($strDrives) == "") {
                break;
            }
            if (CommonFunctions::executeProgram("hddtemp", $strDrives, $hddtemp_value, PSI_DEBUG)) {
                $hddtemp_value = preg_split("/\n/", $hddtemp_value, -1, PREG_SPLIT_NO_EMPTY);
                foreach ($hddtemp_value as $line) {
                    $temp = preg_split("/:\s/", $line, 3);
                    if (count($temp) == 3 && preg_match("/^[0-9]/", $temp[2])) {
                        preg_match("/^([0-9]*)(.*)/", $temp[2], $ar_temp);
                        $temp[2] = trim($ar_temp[1]);
                        $temp[3] = trim($ar_temp[2]);
                        array_push($ar_buf, "|".implode("|", $temp)."|");
                    }
                }
            }
            break;
        default:
            $this->error->addConfigError("temperature()", "PSI_HDD_TEMP");
            break;
        }
        // Timo van Roermund: parse the info from the hddtemp daemon.
        foreach ($ar_buf as $line) {
            $data = array();
            if (preg_match("/\|(.*)\|(.*)\|(.*)\|(.*)\|/", $line, $data)) {
                if (trim($data[3]) != "ERR") {
                    // get the info we need
                    $dev = new SensorDevice();
                    $dev->setName($data[1] . ' (' . (strpos($data[2], "  ")?substr($data[2], 0, strpos($data[2], "  ")):$data[2]) . ')');
                    if (is_numeric($data[3])) {
                        $dev->setValue($data[3]);
                    }
                    $dev->setMax(60);
                    $this->mbinfo->setMbTemp($dev);
                }
            }
        }
    }

    /**
     * get the information
     *
     * @see PSI_Interface_Sensor::build()
     *
     * @return Void
     */
    public function build()
    {
        $this->_temperature();
    }
}
