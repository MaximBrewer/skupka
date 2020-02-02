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
class CommonFunctions
{
    private static function _parse_log_file($string)
    {
        if (defined('PSI_LOG') && is_string(PSI_LOG) && (strlen(PSI_LOG)>0) && ((substr(PSI_LOG, 0, 1)=="-") || (substr(PSI_LOG, 0, 1)=="+"))) {
            $log_file = substr(PSI_LOG, 1);
            if (file_exists($log_file)) {
                $contents = @file_get_contents($log_file);
                if ($contents && preg_match("/^\-\-\-[^-\r\n]+\-\-\- ".preg_quote($string, '/')."\r?\n/m", $contents, $matches, PREG_OFFSET_CAPTURE)) {
                    $findIndex = $matches[0][1];
                    if (preg_match("/\r?\n/m", $contents, $matches, PREG_OFFSET_CAPTURE, $findIndex)) {
                        $startIndex = $matches[0][1]+1;
                        if (preg_match("/^\-\-\-[^-\r\n]+\-\-\- /m", $contents, $matches, PREG_OFFSET_CAPTURE, $startIndex)) {
                            $stopIndex = $matches[0][1];

                            return substr($contents, $startIndex, $stopIndex-$startIndex);
                        } else {
                            return substr($contents, $startIndex);
                        }
                    }
                }
            }
        }

        return false;
    }

    
    private static function _findProgram($strProgram)
    {
        $path_parts = pathinfo($strProgram);
        if (empty($path_parts['basename'])) {
            return;
        }
        $arrPath = array();
        if ((PSI_OS == 'WINNT') && empty($path_parts['extension'])) {
            $strProgram .= '.exe';
            $path_parts = pathinfo($strProgram);
        }
        if (empty($path_parts['dirname']) || ($path_parts['dirname'] == '.')) {
            if (PSI_OS == 'WINNT') {
                $arrPath = preg_split('/;/', getenv("Path"), -1, PREG_SPLIT_NO_EMPTY);
            } else {
                $arrPath = preg_split('/:/', getenv("PATH"), -1, PREG_SPLIT_NO_EMPTY);
            }
            if (defined('PSI_ADD_PATHS') && is_string(PSI_ADD_PATHS)) {
                if (preg_match(ARRAY_EXP, PSI_ADD_PATHS)) {
                    $arrPath = array_merge(eval(PSI_ADD_PATHS), $arrPath); // In this order so $addpaths is before $arrPath when looking for a program
                } else {
                    $arrPath = array_merge(array(PSI_ADD_PATHS), $arrPath); // In this order so $addpaths is before $arrPath when looking for a program
                }
            }
        } else {
            array_push($arrPath, $path_parts['dirname']);
            $strProgram = $path_parts['basename'];
        }

        //add some default paths if we still have no paths here
        if (empty($arrPath) && PSI_OS != 'WINNT') {
            if (PSI_OS == 'Android') {
                array_push($arrPath, '/system/bin');
            } else {
                array_push($arrPath, '/bin', '/sbin', '/usr/bin', '/usr/sbin', '/usr/local/bin', '/usr/local/sbin');
            }
        }

        $exceptPath = "";
        if ((PSI_OS == 'WINNT') && (($windir = getenv("WinDir")) !== false)) {
            $windir = strtolower($windir);
            foreach ($arrPath as $strPath) {
                if ((strtolower($strPath) == $windir."\\system32") && is_dir($windir."\\SysWOW64")) {
                    $exceptPath = $windir."\\sysnative";
                    array_push($arrPath, $exceptPath);
                    break;
                }
            }
        } else if (PSI_OS == 'Android') {
             $exceptPath = '/system/bin';
        }

        // If open_basedir defined, fill the $open_basedir array with authorized paths,. (Not tested when no open_basedir restriction)
        if ((bool) ini_get('open_basedir')) {
            if (PSI_OS == 'WINNT') {
                $open_basedir = preg_split('/;/', ini_get('open_basedir'), -1, PREG_SPLIT_NO_EMPTY);
            } else {
                $open_basedir = preg_split('/:/', ini_get('open_basedir'), -1, PREG_SPLIT_NO_EMPTY);
            }
        }
        foreach ($arrPath as $strPath) {
            // Path with trailing slash
            if (PSI_OS == 'WINNT') {
                $strPathS = rtrim($strPath, "\\")."\\";
            } else {
                $strPathS = rtrim($strPath, "/")."/";
            }
            // To avoid "open_basedir restriction in effect" error when testing paths if restriction is enabled
            if (isset($open_basedir)) {
                $inBaseDir = false;
                if (PSI_OS == 'WINNT') {
                    foreach ($open_basedir as $openbasedir) {
                        if (substr($openbasedir, -1)=="\\") {
                            $str_Path = $strPathS;
                        } else {
                            $str_Path = $strPath;
                        }
                        if (stripos($str_Path, $openbasedir) === 0) {
                            $inBaseDir = true;
                            break;
                        }
                    }
                } else {
                    foreach ($open_basedir as $openbasedir) {
                        if (substr($openbasedir, -1)=="/") {
                            $str_Path = $strPathS;
                        } else {
                            $str_Path = $strPath;
                        }
                        if (strpos($str_Path, $openbasedir) === 0) {
                            $inBaseDir = true;
                            break;
                        }
                    }
                }
                if ($inBaseDir == false) {
                    continue;
                }
            }
            if (($strPath !== $exceptPath) && !is_dir($strPath)) {
                continue;
            }
            if (PSI_OS == 'WINNT') {
                $strProgrammpath = rtrim($strPath, "\\")."\\".$strProgram;
            } else {
                $strProgrammpath = rtrim($strPath, "/")."/".$strProgram;
            }
            if (is_executable($strProgrammpath)) {
                return $strProgrammpath;
            }
        }
    }

    
    public static function executeProgram($strProgramname, $strArgs, &$strBuffer, $booErrorRep = true)
    {
        if (defined('PSI_LOG') && is_string(PSI_LOG) && (strlen(PSI_LOG)>0) && ((substr(PSI_LOG, 0, 1)=="-") || (substr(PSI_LOG, 0, 1)=="+"))) {
            $out = self::_parse_log_file("Executing: ".trim($strProgramname.' '.$strArgs));
            if ($out == false) {
                if (substr(PSI_LOG, 0, 1)=="-") {
                    $strBuffer = '';

                    return false;
                }
            } else {
                $strBuffer = $out;

                return true;
            }
        }

        $strBuffer = '';
        $strError = '';
        $pipes = array();
        $strProgram = self::_findProgram($strProgramname);
        
        if (!$strProgram) {
            if ($booErrorRep) {
                $error->addError('find_program('.$strProgramname.')', 'program not found on the machine');
            }

            return false;
        }
        // see if we've gotten a |, if we have we need to do path checking on the cmd
        if ($strArgs) {
            $arrArgs = preg_split('/ /', $strArgs, -1, PREG_SPLIT_NO_EMPTY);
            for ($i = 0, $cnt_args = count($arrArgs); $i < $cnt_args; $i++) {
                if ($arrArgs[$i] == '|') {
                    $strCmd = $arrArgs[$i + 1];
                    $strNewcmd = self::_findProgram($strCmd);
                    $strArgs = preg_replace("/\| ".$strCmd.'/', '| "'.$strNewcmd.'"', $strArgs);
                }
            }
        }
        $descriptorspec = array(0=>array("pipe", "r"), 1=>array("pipe", "w"), 2=>array("pipe", "w"));
        if (defined("PSI_MODE_POPEN") && PSI_MODE_POPEN === true) {
            if (PSI_OS == 'WINNT') {
                $process = $pipes[1] = popen('"'.$strProgram.'" '.$strArgs." 2>nul", "r");
            } else {
                $process = $pipes[1] = popen('"'.$strProgram.'" '.$strArgs." 2>/dev/null", "r");
            }
        } else {
			if(function_exists('proc_open')){
				$process = proc_open('"'.$strProgram.'" '.$strArgs, $descriptorspec, $pipes);
			}
        }
        if (is_resource($process)) {
            self::_timeoutfgets($pipes, $strBuffer, $strError);
            if (defined("PSI_MODE_POPEN") && PSI_MODE_POPEN === true) {
                $return_value = pclose($pipes[1]);
            } else {
                fclose($pipes[0]);
                fclose($pipes[1]);
                fclose($pipes[2]);
                // It is important that you close any pipes before calling
                // proc_close in order to avoid a deadlock
                $return_value = proc_close($process);
            }
        } else {
            if ($booErrorRep) {
                $error->addError($strProgram, "\nOpen process error");
            }

            return false;
        }
        $strError = trim($strError);
        $strBuffer = trim($strBuffer);
       
        if (! empty($strError)) {
            if ($booErrorRep) {
                $error->addError($strProgram, $strError."\nReturn value: ".$return_value);
            }

            return $return_value == 0;
        }

        return true;
    }

   
    public static function rfts($strFileName, &$strRet, $intLines = 0, $intBytes = 4096, $booErrorRep = true)
    {
        if (defined('PSI_LOG') && is_string(PSI_LOG) && (strlen(PSI_LOG)>0) && ((substr(PSI_LOG, 0, 1)=="-") || (substr(PSI_LOG, 0, 1)=="+"))) {
            $out = self::_parse_log_file("Reading: ".$strFileName);
            if ($out == false) {
                if (substr(PSI_LOG, 0, 1)=="-") {
                    $strRet = '';

                    return false;
                }
            } else {
                $strRet = $out;

                return true;
            }
        }

        $strFile = "";
        $intCurLine = 1;
        $error = Errorss::singleton();
        if (file_exists($strFileName)) {
            if (is_readable($strFileName)) {
                if ($fd = fopen($strFileName, 'r')) {
                    while (!feof($fd)) {
                        $strFile .= fgets($fd, $intBytes);
                        if ($intLines <= $intCurLine && $intLines != 0) {
                            break;
                        } else {
                            $intCurLine++;
                        }
                    }
                    fclose($fd);
                    $strRet = $strFile;
                    if (defined('PSI_LOG') && is_string(PSI_LOG) && (strlen(PSI_LOG)>0) && (substr(PSI_LOG, 0, 1)!="-") && (substr(PSI_LOG, 0, 1)!="+")) {
                        
                    }
                } else {
                    if ($booErrorRep) {
                         $error->addError('fopen('.$strFileName.')', 'file can not read by phpsysinfo');
                    }

                    return false;
                }
            } else {
                    if ($booErrorRep) {
                         $error->addError('fopen('.$strFileName.')', 'file permission error');
                    }

                    return false;
            }
        } else {
            if ($booErrorRep) {
                $error->addError('file_exists('.$strFileName.')', 'the file does not exist on your machine');
            }

            return false;
        }

        return true;
    }

   
    public static function fileexists($strFileName)
    {
        if (defined('PSI_LOG') && is_string(PSI_LOG) && (strlen(PSI_LOG)>0) && ((substr(PSI_LOG, 0, 1)=="-") || (substr(PSI_LOG, 0, 1)=="+"))) {
            $log_file = substr(PSI_LOG, 1);
            if (file_exists($log_file)
                && ($contents = @file_get_contents($log_file))
                && preg_match("/^\-\-\-[^-\n]+\-\-\- ".preg_quote("Reading: ".$strFileName, '/')."\n/m", $contents)) {
                return true;
            } else {
                if (substr(PSI_LOG, 0, 1)=="-") {
                    return false;
                }
            }
        }

        return file_exists($strFileName);
    }

   
    public static function gdc($strPath, $booErrorRep = true)
    {
        $arrDirectoryContent = array();
        $error = Errorss::singleton();
        if (is_dir($strPath)) {
            if ($handle = opendir($strPath)) {
                while (($strFile = readdir($handle)) !== false) {
                    if ($strFile != "." && $strFile != "..") {
                        $arrDirectoryContent[] = $strFile;
                    }
                }
                closedir($handle);
            } else {
                if ($booErrorRep) {
                    $error->addError('opendir('.$strPath.')', 'directory can not be read by phpsysinfo');
                }
            }
        } else {
            if ($booErrorRep) {
                $error->addError('is_dir('.$strPath.')', 'directory does not exist on your machine');
            }
        }

        return $arrDirectoryContent;
    }

    
    public static function checkForExtensions($arrExt = array())
    {
        if ((strcasecmp(PSI_SYSTEM_CODEPAGE, "UTF-8") == 0) || (strcasecmp(PSI_SYSTEM_CODEPAGE, "CP437") == 0))
            $arrReq = array('simplexml', 'pcre', 'xml', 'dom');
        elseif (PSI_OS == "WINNT")
            $arrReq = array('simplexml', 'pcre', 'xml', 'mbstring', 'dom', 'com_dotnet');
        else
            $arrReq = array('simplexml', 'pcre', 'xml', 'mbstring', 'dom');
        $extensions = array_merge($arrExt, $arrReq);
        $text = "";
        $error = false;
        $text .= "<?xml version='1.0'?>\n";
        $text .= "<phpsysinfo>\n";
        $text .= "  <Error>\n";
        foreach ($extensions as $extension) {
            if (!extension_loaded($extension)) {
                $text .= "    <Function>checkForExtensions</Function>\n";
                $text .= "    <Message>phpSysInfo requires the ".$extension." extension to php in order to work properly.</Message>\n";
                $error = true;
            }
        }
        $text .= "  </Error>\n";
        $text .= "</phpsysinfo>";
        if ($error) {
            header("Content-Type: text/xml\n\n");
            echo $text;
            die();
        }
    }

    
    private static function _timeoutfgets($pipes, &$out, &$err, $timeout = 30)
    {
        $w = null;
        $e = null;

        if (defined("PSI_MODE_POPEN") && PSI_MODE_POPEN === true) {
            $pipe2 = false;
        } else {
            $pipe2 = true;
        }
        while (!(feof($pipes[1]) && (!$pipe2 || feof($pipes[2])))) {
            if ($pipe2) {
                $read = array($pipes[1], $pipes[2]);
            } else {
                $read = array($pipes[1]);
            }

            $n = stream_select($read, $w, $e, $timeout);



            foreach ($read as $r) {
                if ($r == $pipes[1]) {
                    $out .= fread($r, 4096);
                } else if (feof($pipes[1]) && $pipe2 && ($r == $pipes[2])) {//read STDERR after STDOUT
                    $err .= fread($r, 4096);
                }
            }
        }
    }

    
    public static function getWMI($wmi, $strClass, $strValue = array())
    {
        $arrData = array();
        if ($wmi) {
            $value = "";
            try {
                $objWEBM = $wmi->Get($strClass);
                $arrProp = $objWEBM->Properties_;
                $arrWEBMCol = $objWEBM->Instances_();
                foreach ($arrWEBMCol as $objItem) {
                    if (is_array($arrProp)) {
                        reset($arrProp);
                    }
                    $arrInstance = array();
                    foreach ($arrProp as $propItem) {
                        $value = $objItem->{$propItem->Name}; //instead exploitable eval("\$value = \$objItem->".$propItem->Name.";");
                        if (empty($strValue)) {
                            if (is_string($value)) $arrInstance[$propItem->Name] = trim($value);
                            else $arrInstance[$propItem->Name] = $value;
                        } else {
                            if (in_array($propItem->Name, $strValue)) {
                                if (is_string($value)) $arrInstance[$propItem->Name] = trim($value);
                                else $arrInstance[$propItem->Name] = $value;
                            }
                        }
                    }
                    $arrData[] = $arrInstance;
                }
            } catch (Exception $e) {
                if (PSI_DEBUG) {
                    $error = Errorss::singleton();
                    $error->addError($e->getCode(), $e->getMessage());
                }
            }
        }

        return $arrData;
    }

   
    public static function getPlugins()
    {
        if (defined('PSI_PLUGINS') && is_string(PSI_PLUGINS)) {
            if (preg_match(ARRAY_EXP, PSI_PLUGINS)) {
                return eval(strtolower(PSI_PLUGINS));
            } else {
                return array(strtolower(PSI_PLUGINS));
            }
        } else {
            return array();
        }
    }
}
