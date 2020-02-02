<?php
/*------------------------------------
* -Netbase- Advanced Virtuemart Invoices for Virtuemart
* Author    CMSMart Team
* Copyright (C) 2012 http://cmsmart.net. All Rights Reserved.
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* Websites: http://cmsmart.net
* Email: team@cmsmart.net
* Technical Support:  Forum - http://bloorum.com/forums
-----------------------------------------------------*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.model');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.installer.helper');

class VMInvoiceModelUpgrade extends JModelLegacy
{
    
    function getNewVMIVersion ()
    {
        if (! isset($this->_newVMIVersion)) {
            $this->_loadVersions();
        }
        
        return $this->_newVMIVersion;
    }

    function getRegisteredInfo ($downloadId = '')
    {
        if (! isset($this->_regInfo)) {
            $regInfo = new stdClass();
            
            //$vmiConfig =  VMIConfig::getConfig();
            if (strlen(trim($downloadId)) == 32) {
                // Send the request to ARTIO server to check registration
                $data = array('download_id' => trim($downloadId));
                $response = VMInvoiceModelUpgrade::PostRequest('http://www.artio.net/license-check', null, $data);
                
                if (($response === false) || ($response->code != 200)) {
                    JError::raiseNotice(100, JText::_('COM_VMINVOICE_ERROR_REG_CHECK_FAIL').' Error no. 1.');
                    return null;
                } else {
                    // Parse the response - get individual lines
                    $lines = explode("\n", $response->content);
                    
                    // Get the code
                    $pos = strpos($lines[0], ' ');
                    if ($pos === false) {
                        JError::raiseNotice(100, JText::_('COM_VMINVOICE_ERROR_REG_CHECK_FAIL').' Error no. 2.');
                        return null;
                    }
                    $regInfo->code = intval(substr($lines[0], 0, $pos));
                    
                    if (($regInfo->code == 10) || ($regInfo->code == 20)) {
                        // Download id found
                        if (count($lines) < 3) {
                            // Wrong response
                            JError::raiseNotice(100, JText::_('COM_VMINVOICE_ERROR_REG_CHECK_FAIL').' Error no. 3.');
                            return null;
                        }
                        
                        // Parse the date
                        $date =  JFactory::getDate(str_replace('.', '/', trim($lines[1])));
                        $regInfo->date = $date->Format('DATE_FORMAT_LC3');
                        
                        // Parse the name
                        $regInfo->name = trim($lines[2]);
                        
                        // Parse the company
                        $regInfo->company = isset($lines[3]) ? trim($lines[3]) : '';
                        
                        // Is upgrade expired?
                        if ($regInfo->code == 20) {
                            JError::raiseNotice(100, JText::_('COM_VMINVOICE_INFO_UPGRADE_LICENSE_EXPIRED') . ' ' . JText::_('COM_VMINVOICE_INFO_YOU_CAN_NOT_UPGRADE'));
                        }
                        // Is upgrade inactive
                        if ($regInfo->code == 30) {
                            JError::raiseNotice(100, JText::_('COM_VMINVOICE_INFO_UPGRADE_NOT_ACTIVE') . ' ' . JText::_('COM_VMINVOICE_INFO_YOU_CAN_NOT_UPGRADE'));
                            $regInfo->date = JText::_('COM_VMINVOICE_NOT_ACTIVATED_YET');
                        }
                    } else 
                        if ($regInfo->code == 90) {
                            // Download id not found, do nothing
                            JError::raiseNotice(100, 
                            JText::_('COM_VMINVOICE_ERROR_DOWNLOAD_ID_NOT_FOUND') . ' ' . JText::_('COM_VMINVOICE_INFO_YOU_CAN_NOT_UPGRADE'));
                        } else {
                            // Wrong response
                            JError::raiseNotice(100, JText::_('COM_VMINVOICE_ERROR_REG_CHECK_FAIL').' Error no. 4.');
                            return null;
                        }
                }
            } elseif (trim($downloadId) != '') {
                // Download ID has short length - incorrect
                JError::raiseNotice(100, JText::_('COM_VMINVOICE_DOWNLOAD_ID_INCORRECT_LENGTH') . ' ' . JText::_('COM_VMINVOICE_INFO_YOU_CAN_NOT_UPGRADE'));
                return null;
            } else {
                // Download ID not set
                JError::raiseNotice(100, JText::_('COM_VMINVOICE_DOWNLOAD_ID_NOT_SET') . ' ' . JText::_('COM_VMINVOICE_INFO_YOU_CAN_NOT_UPGRADE'));
                return null;
            }
            
            $this->_regInfo = $regInfo;
        }
        
        return $this->_regInfo;
    }

    function _loadVersions ()
    {
        if (! isset($this->_newVMIVersion)) {
            
            // Get the response from server
            $response = self::PostRequest("http://www.artio.cz/updates/joomla/vminvoice/version");
            
            // Check the response
            if (($response === false) || ($response->code != 200)) {
                JError::raiseNotice(100, JText::_('COM_VMINVOICE_ERROR_NO_VERSION_INFO'));
                $versions = '?.?.?';
            } else {
                $versions = $response->content;
            }
            
            $versions = explode("\n", trim($versions));
            $this->_newVMIVersion = trim(array_shift($versions));
            
            $this->_extVersions = array();
            if (count($versions) > 0) {
                foreach ($versions as $version) {
                    $parts = preg_split('/[\s]+/', $version);
                    
                    $ext = new stdClass();
                    $ext->name = $parts[0];
                    $ext->version = trim($parts[1]);
                    if (isset($parts[2])) {
                        $ext->link = trim($parts[2]);
                        $ext->type = 'Paid';
                    } else {
                        $ext->link = '';
                        $ext->type = 'Free';
                    }
                    
                    $this->_extVersions[$ext->name] = $ext;
                }
            }
        }
    }

    function &getVersions ()
    {
        $this->_loadVersions();
        
        return $this->_extVersions;
    }

    function getIsPaidVersion ()
    {
        /*if( !isset($this->_isPaidVersion) ) {
            $check = VMITools::GetVMIGlobalMeta();
            $ctrl = md5(implode(file(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_vminvoice'.DS.'vmi.xml')));
            
            $this->_isPaidVersion = ($check == $ctrl);
        }*/
        
        return true; //$this->_isPaidVersion;
    }

    function upgrade ()
    {
        //$extDir = JPATH_ROOT.DS.'components'.DS.'com_vminvoice'.DS.'sef_ext';
        

        $fromServer = JRequest::getVar('fromserver');
        //$extension = JRequest::getVar('ext');
        

        if (is_null($fromServer)) {
            $this->setState('message', JText::_('COM_VMINVOICE_UPGRADE_SOURCE_NOT_GIVEN'));
            return false;
        }
        
        if ($fromServer == 1) {
            $package = $this->_getPackageFromServer();
        } else {
            $package = $this->_getPackageFromUpload();
        }
        
        // was the package unpacked?
        if (! $package) {
            $this->setState('message', 'Unable to find install package.');
            return false;
        }
        
        // get current version
        $curVersion = $this->getVMIVersion();
        
        if (empty($curVersion)) {
            $this->setState('message', JText::_('COM_VMINVOICE_COULD_NOT_FIND_CURRENT_VERSION'));
            JFolder::delete($package['dir']);
            return false;
        }
        
        // create an array of upgrade files
        $upgradeDir = $package['dir'] . DS . 'upgrade';
        $upgradeFiles = JFolder::files($upgradeDir, '.php$');
        
        if (empty($upgradeFiles)) {
            $this->setState('message', JText::_('COM_VMINVOICE_THIS_PACKAGE_DOES_NOT_CONTAIN_ANY_UPGRADE_INFORMATIONS'));
            JFolder::delete($package['dir']);
            return false;
        }
        
        // check if current version is upgradeable with downloaded package
        $reinstall = false;
        if (! in_array($curVersion . '.php', $upgradeFiles)) {
            
            // check if current version is being manually reinstalled with the same version package
            $xmlFile = $package['dir'] . DS . 'vminvoice.xml';
            $packVersion = $this->_getXmlText($xmlFile, 'version');
            if (($packVersion == $curVersion) && JFile::exists($upgradeDir . DS . 'reinstall.php')) {
                // initiate the reinstall
                $reinstall = true;
                $mainframe = JFactory::getApplication();
                $mainframe->enqueueMessage(JText::_('COM_VMINVOICE_INFO_VMI_REINSTALL'));
            }
            
            if (! $reinstall) {
                $this->setState('message', JText::_('COM_VMINVOICE_ERROR_CANT_UPGRADE'));
                JFolder::delete($package['dir']);
                return false;
            }
        }
        
        natcasesort($upgradeFiles);
        
        // prepare arrays of upgrade operations and functions to manipulate them
        $this->_fileError = false;
        $this->_fileList = array();
        $this->_sqlList = array();
        $this->_scriptList = array();
        
        if (! $reinstall) {
            // load each upgrade file starting with current version in ascending order
            foreach ($upgradeFiles as $uFile) {
                if (! preg_match("/^[0-9]+\.[0-9]+\.[0-9]+\.php$/i", $uFile)) {
                    continue;
                }
                if (strnatcasecmp($uFile, $curVersion . ".php") >= 0) {
                    require_once ($upgradeDir . DS . $uFile);
                }
            }
        } else {
            // create list of all files to upgrade
            require_once ($upgradeDir . DS . 'reinstall.php');
        }
        
        if ($this->_fileError == false) {
            // set errors variable
            $errors = false;
            
            // first of all check if all the files are writeable
            // ONLY IF FTP IS DISABLED
            jimport('joomla.client.helper');
            $ftpOptions = JClientHelper::getCredentials('ftp');
            
            if ($ftpOptions['enabled'] != 1) {
                foreach ($this->_fileList as $dest => $op) {
                    $file = JPath::clean(JPATH_ROOT . DS . $dest);
                    
                    // check if source file is present in upgrade package
                    if ($op->operation == 'upgrade') {
                        $from = JPath::clean($package['dir'] . DS . $op->packagePath);
                        if (! JFile::exists($from)) {
                            JError::raiseWarning(100, JText::_('COM_VMINVOICE_FILE_DOES_NOT_EXIST_IN_UPGRADE_PACKAGE') . ': ' . $op->packagePath);
                            unset($this->_fileList[$dest]); //remove from upgrade list
                            continue; //continue in upgrade. can be just some old file that was removed long ago.
                            //$errors = true;
                        }
                    }
                    
                    if ((($op->operation == 'delete') && (JFile::exists($file))) || (($op->operation == 'upgrade') && (! JFile::exists($file)))) {
                        
                        // if the file is to be deleted or created, the file's directory must be writable
                        $dir = dirname($file);
                        if (! JFolder::exists($dir)) {
                            // we need to create the directory where the file is to be created
                            if (! JFolder::create($dir)) {
                                JError::raiseWarning(100, JText::_('COM_VMINVOICE_DIRECTORY_COULD_NOT_BE_CREATED') . ': ' . $dir);
                                $errors = true;
                            }
                        }
                        
                        if (! is_writable($dir)) {
                            if (! JPath::setPermissions($dir, '0755', '0777')) {
                                JError::raiseWarning(100, JText::_('COM_VMINVOICE_DIRECTORY_NOT_WRITEABLE') . ': ' . $dir);
                                $errors = true;
                            }
                        }
                    } elseif ($op->operation == 'upgrade') {
                        
                        // the file itself must be writeable
                        if (! is_writable($file)) {
                            if (! JPath::setPermissions($file, '0755', '0777')) {
                                JError::raiseWarning(100, JText::_('COM_VMINVOICE_FILE_NOT_WRITEABLE') . ': ' . $file);
                                $errors = true;
                            }
                        }
                    }
                }
            }
            
            // If there are no errors, let's upgrade
            if (! $errors) {
                $db =  JFactory::getDBO();
                
                // execute SQL queries
                foreach ($this->_sqlList as $sql) {
                    $db->setQuery($sql);
                    if (! $db->execute()) {
                        JError::raiseWarning(100, JText::_('COM_VMINVOICE_UNABLE_TO_EXECUTE_SQL_QUERY') . ': ' . $sql.'. '.$db->getErrorMsg());
                        $errors = true;
                    }
                }
                
                // perform file operations
                foreach ($this->_fileList as $dest => $op) {
                    if ($op->operation == 'delete') {
                        $file = JPath::clean(JPATH_ROOT . DS . $dest);
                        // test if this is folder
                        if (JFolder::exists($file)) {
                            $success = JFolder::delete($file);
                            if (! $success) {
                                JError::raiseWarning(100, JText::_('COM_VMINVOICE_COULD_NOT_DELETE_FOLDER._PLEASE,_CHECK_THE_WRITE_PERMISSIONS_ON') . ' ' . $dest);
                                $errors = true;
                            }
                        }
                        // test if it is file
                        else if (JFile::exists($file)) {
                            $success = JFile::delete($file);
                            if (! $success) {
                                JError::raiseWarning(100, JText::_('COM_VMINVOICE_COULD_NOT_DELETE_FILE_PLEASE_CHECK_THE_WRITE_PERMISSIONS_ON') . ' ' . $dest);
                                $errors = true;
                            }
                        }
                    } elseif ($op->operation == 'upgrade') {
                        $from = JPath::clean($package['dir'] . DS . $op->packagePath);
                        $to = JPath::clean(JPATH_ROOT . DS . $dest);
                        $destDir = dirname($to);
                        
                        // create the destination directory if needed
                        if (! JFolder::exists($destDir)) {
                            JFolder::create($destDir);
                        }
                        
                        $success = JFile::copy($from, $to);
                        if (! $success) {
                            JError::raiseWarning(100, JText::_('COM_VMINVOICE_COULD_NOT_REWRITE_FILE_PLEASE_CHECK_THE_WRITE_PERMISSIONS_ON') . ' ' . $dest);
                            $errors = true;
                        }
                    }
                }
                
                // run scripts
                foreach ($this->_scriptList as $script) {
                    $file = JPath::clean($package['dir'] . DS . $script);
                    if (! JFile::exists($file)) {
                        JError::raiseWarning(100, JText::_('COM_VMINVOICE_COULD_NOT_FIND_SCRIPT_FILE') . ': ' . $script);
                        $errors = true;
                    } else {
                        include ($file);
                    }
                }
            }
            
            if (! $errors) {
                $what = JText::_('COM_VMINVOICE');
                $this->setState('message', $what . ' ' . JText::_('COM_VMINVOICE_SUCCESSFULLY_UPGRADED'));
            } else {
                $this->setState('message', JText::_('COM_VMINVOICE_ERROR_UPGRADE_PROBLEM'));
            }
        }
        
        JFolder::delete($package['dir']);
        return true;
    }

    // Adds a file operation to $fileList
    // $joomlaPath - destination file path (e.g. '/administrator/components/com_sef/admin.sef.php')
    // $operation - can be 'delete' or 'upgrade'
    // $packagePath - source file path in upgrade package if $operation is 'upgrade' (e.g. '/admin.sef.php')
    function _addFileOp ($joomlaPath, $operation, $packagePath = '')
    {
        if (! in_array($operation, array('upgrade', 'delete'))) {
            $this->fileError = true;
            JError::raiseWarning(100, JText::_('COM_VMINVOICE_INVALID_UPGRADE_OPERATION') . ': ' . $operation);
            return false;
        }
        
        // Do not check if file in package exists - it may be deleted in some future version during upgrade
        // It will be checked before running file operations
        $file = new stdClass();
        $file->operation = $operation;
        $file->packagePath = $packagePath;
        
        $this->_fileList[$joomlaPath] = $file;
    }

    function _addSQL ($sql)
    {
        $this->_sqlList[] = $sql;
    }

    function _addScript ($script)
    {
        $this->_scriptList[] = $script;
    }

    function _getPackageFromUpload ()
    {
        // Get the uploaded file information
        $userfile = JRequest::getVar('install_package', null, 'files', 'array');
        
        // Make sure that file uploads are enabled in php
        if (! (bool) ini_get('file_uploads')) {
            JError::raiseWarning(100, JText::_('COM_VMINVOICE_WARN_INSTALL_FILE'));
            return false;
        }
        
        // Make sure that zlib is loaded so that the package can be unpacked
        if (! extension_loaded('zlib')) {
            JError::raiseWarning(100, JText::_('COM_VMINVOICE_WARN_INSTALL_ZLIB'));
            return false;
        }
        
        // If there is no uploaded file, we have a problem...
        if (! is_array($userfile)) {
            JError::raiseWarning(100, JText::_('COM_VMINVOICE_NO_FILE_SELECTED'));
            return false;
        }
        
        // Check if there was a problem uploading the file.
        if ($userfile['error'] || $userfile['size'] < 1) {
            JError::raiseWarning(100, JText::_('COM_VMINVOICE_WARN_INSTALL_UPLOAD_ERROR'));
            return false;
        }
        
        // Build the appropriate paths
        $config =  JFactory::getConfig();
        $tmp_dest = $config->get('tmp_path') . DS . $userfile['name'];
        $tmp_src = $userfile['tmp_name'];
        
        // Move uploaded file
        jimport('joomla.filesystem.file');
        $uploaded = JFile::upload($tmp_src, $tmp_dest);
        
        // Unpack the downloaded package file
        $package = JInstallerHelper::unpack($tmp_dest);
        
        // Delete the package file
        JFile::delete($tmp_dest);
        
        return $package;
    }

    function _getPackageFromServer ($extension = '')
    {
        // Make sure that zlib is loaded so that the package can be unpacked
        if (! extension_loaded('zlib')) {
            JError::raiseWarning(100, JText::_('COM_VMINVOICE_WARN_INSTALL_ZLIB'));
            return false;
        }
        
        // build the appropriate paths
        //$vmiConfig =  VMIConfig::getConfig();
        $config =  JFactory::getConfig();
        if (empty($extension)) {
            $tmp_dest = $config->get('tmp_path') . DS . 'vminvoice.zip';
        } else {
            $tmp_dest = $config->get('tmp_path') . DS . $extension . '.zip';
        }
        
        // Validate the upgrade on server
        $data = array();
        $data['username'] = '';
        $data['password'] = '';
        
        //get download ID
        $params = InvoiceHelper::getParams();        
        $downloadId = $params->get('download_id');
        
        $data['download_id'] = $downloadId;
        $data['file'] = 'com_vminvoice';

        $uri = parse_url(JURI::root());
        $data['site'] = trim($uri['host'] . $uri['path'], '/');

        $lang = JFactory::getLanguage();
        $data['lang'] = $lang->getTag();
        $data['cat'] = 'vminvoice';
        
        // Get the server response
        $response = $this->PostRequest('http://www.artio.net/joomla-auto-upgrade', JURI::root(), $data);
        
        // Check the response
        if (($response === false) || ($response->code != 200)) {
            JError::raiseWarning(100, JText::_('COM_VMINVOICE_CONNECTION_TO_SERVER_COULD_NOT_BE_ESTABLISHED'));
            return false;
        }
        
        // Response OK, check what we got
        if (strpos($response->header, 'Content-Type: application/zip') === false) {
            JError::raiseWarning(100, $response->content);
            return false;
        }
        
        // Seems we got the ZIP installation package, let's save it to disk
        if (! JFile::write($tmp_dest, $response->content)) {
            JError::raiseWarning(100, JText::_('COM_VMINVOICE_UNABLE_TO_SAVE_INSTALLATION_FILE_IN_TEMP_DIRECTORY'));
            return false;
        }
        
        // Unpack the downloaded package file
        $package = JInstallerHelper::unpack($tmp_dest);
        
        // Delete the package file
        JFile::delete($tmp_dest);
        
        return $package;
    }

    function _getXmlText ($file, $variable)
    {
        // try to find variable
        $value = null;
        if (JFile::exists($file)) {
            $xml =  JFactory::getXML('Simple');
            
            if ($xml->loadFile($file)) {
                $root =  $xml->document;
                $element =  $root->getElementByPath($variable);
                $value = $element ? $element->data() : '';
            }
        }
        
        return $value;
    }

    /**
     * Sends the POST request
     *
     * @param string $url
     * @param string $referer
     * @param array $_data
     * @return object
     */
    function PostRequest ($url, $referer = null, $_data = null, $method = 'post', $userAgent = null)
    {
        
        // convert variables array to string:
        $data = '';
        if (is_array($_data) && count($_data) > 0) {
            // format --> test1=a&test2=b etc.
            $data = array();
            while (list ($n, $v) = each($_data)) {
                $data[] = "$n=$v";
            }
            $data = implode('&', $data);
            $contentType = "Content-type: application/x-www-form-urlencoded\r\n";
        } else {
            $data = $_data;
            $contentType = "Content-type: text/xml\r\n";
        }
        
        if (is_null($referer)) {
            $referer = JURI::root();
        }
        
        // parse the given URL
        $url = parse_url($url);
        if (! isset($url['scheme']) || ($url['scheme'] != 'http')) {
            return false;
        }
        
        // extract host and path:
        $host = $url['host'];
        $path = isset($url['path']) ? $url['path'] : '/';
        
        // open a socket connection on port 80
        $errno = null;
        $errstr = null;
        $fp = @fsockopen($host, 80, $errno, $errstr, 5);
        if ($fp === false) {
            return false;
        }
        
        if (! is_null($userAgent)) {
            $userAgent = "User-Agent: " . $userAgent . "\r\n";
        }
        
        // send the request
        if ($method == 'post') {
            fputs($fp, "POST $path HTTP/1.1\r\n");
            if (! is_null($userAgent)) {
                fputs($fp, $userAgent);
            }
            fputs($fp, "Host: $host\r\n");
            fputs($fp, "Referer: $referer\r\n");
            fputs($fp, $contentType);
            fputs($fp, "Content-length: " . strlen($data) . "\r\n");
            fputs($fp, "Connection: close\r\n\r\n");
            fputs($fp, $data);
        } elseif ($method == 'get') {
            $query = '';
            if (isset($url['query'])) {
                $query = '?' . $url['query'];
            }
            fputs($fp, "GET {$path}{$query} HTTP/1.1\r\n");
            if (! is_null($userAgent)) {
                fputs($fp, $userAgent);
            }
            fputs($fp, "Host: $host\r\n");
            fputs($fp, "Connection: close\r\n\r\n");
        }
        
        $result = '';
        while (! feof($fp)) {
            // receive the results of the request
            $result .= fgets($fp, 128);
        }
        
        // close the socket connection:
        fclose($fp);
        
        // split the result header from the content
        $result = explode("\r\n\r\n", $result, 2);
        
        $header = isset($result[0]) ? $result[0] : '';
        $content = isset($result[1]) ? $result[1] : '';
        
        $response = new stdClass();
        $response->header = $header;
        $response->content = $content;
        
        // Handle chunked transfer if needed
        if (strpos(strtolower($response->header), 'transfer-encoding: chunked') !== false) {
            $parsed = '';
            $left = $response->content;
            
            while (true) {
                $pos = strpos($left, "\r\n");
                if ($pos === false) {
                    return $response;
                }
                
                $chunksize = substr($left, 0, $pos);
                $pos += strlen("\r\n");
                $left = substr($left, $pos);
                
                $pos = strpos($chunksize, ';');
                if ($pos !== false) {
                    $chunksize = substr($chunksize, 0, $pos);
                }
                $chunksize = hexdec($chunksize);
                
                if ($chunksize == 0) {
                    break;
                }
                
                $parsed .= substr($left, 0, $chunksize);
                $left = substr($left, $chunksize + strlen("\r\n"));
            }
            
            $response->content = $parsed;
        }
        
        // Get the response code from header
        $headerLines = explode("\n", $response->header);
        $header1 = explode(' ', trim($headerLines[0]));
        $code = intval($header1[1]);
        $response->code = $code;
        
        return $response;
    }

    function getVMIInfo ()
    {
        static $info;
        
        if (! isset($info)) {
            $info = array();
            
            $xml = JFactory::getXML('Simple');
            
            $xmlFile = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_vminvoice' . DS . 'vminvoice.xml';
            
            if (JFile::exists($xmlFile)) {
                if ($xml->loadFile($xmlFile)) {
                    $root =  $xml->document;
                    
                    $element =  $root->getElementByPath('version');
                    $info['version'] = $element ? $element->data() : '';
                    
                    $element =  $root->getElementByPath('creationdate');
                    $info['creationDate'] = $element ? $element->data() : '';
                    
                    $element =  $root->getElementByPath('author');
                    $info['author'] = $element ? $element->data() : '';
                    
                    $element =  $root->getElementByPath('authoremail');
                    $info['authorEmail'] = $element ? $element->data() : '';
                    
                    $element =  $root->getElementByPath('authorurl');
                    $info['authorUrl'] = $element ? $element->data() : '';
                    
                    $element =  $root->getElementByPath('copyright');
                    $info['copyright'] = $element ? $element->data() : '';
                    
                    $element =  $root->getElementByPath('license');
                    $info['license'] = $element ? $element->data() : '';
                    
                    $element =  $root->getElementByPath('description');
                    $info['description'] = $element ? $element->data() : '';
                }
            }
        }
        
        return $info;
    }

    function getVMIVersion ()
    {
        static $version;
        
        if (! isset($version)) {
            $xml = JFactory::getXML('Simple');
            
            $xmlFile = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_vminvoice' . DS . 'vminvoice.xml';
            
            if (JFile::exists($xmlFile)) {
                if ($xml->loadFile($xmlFile)) {
                    $root =  $xml->document;
                    $element =  $root->getElementByPath('version');
                    $version = $element ? $element->data() : '';
                }
            }
        }
        
        return $version;
    }

}
?>