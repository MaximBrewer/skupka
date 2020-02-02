<?php
/*------------------------------------------------------------------------
# com_vdata - vData
# ------------------------------------------------------------------------
# author    Team WDMtech
# copyright Copyright (C) 2014 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support:  Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access');

require(JPATH_ADMINISTRATOR.'/components/com_vdata/classes/vendor_config.php');
require(JPATH_ADMINISTRATOR.'/components/com_vdata/classes/config.default.php');

class PMA_Config
{
   
	
    var $default_source = '';

   
    var $default = array();

    
    var $settings = array();

   
    var $source = '';

    
    var $source_mtime = 0;
    var $default_source_mtime = 0;
    var $set_mtime = 0;

    
    var $error_config_file = false;

    
    var $error_config_default_file = false;

    
    var $error_pma_uri = false;

    
    var $default_server = array();

    
    var $done = false;

    
    function __construct($source = null)
    {
        $this->settings = array();
        
        // functions need to refresh in case of config file changed goes in
        // PMA_Config::load()
        $this->load($source);

        // other settings, independent from config file, comes in
        $this->checkSystem();

        $this->checkIsHttps();
    }

   
    function checkSystem()
    {
        $this->set('PMA_VERSION', '3.4.10.1');
        
        $this->set('PMA_THEME_VERSION', 2);
        
        $this->set('PMA_THEME_GENERATION', 2);

        $this->checkPhpVersion();
        $this->checkWebServerOs();
        $this->checkWebServer();
        $this->checkGd2();
        $this->checkClient();
        $this->checkUpload();
        $this->checkUploadSize();
        $this->checkOutputCompression();
    }

   
    function checkOutputCompression()
    {
       
       
        if (@ini_get('zlib.output_compression')) {
            $this->set('OBGzip', false);
        }

       
        if (strtolower($this->get('OBGzip')) == 'auto') {
            if ($this->get('PMA_USR_BROWSER_AGENT') == 'IE'
              && $this->get('PMA_USR_BROWSER_VER') >= 6
              && $this->get('PMA_USR_BROWSER_VER') < 7) {
                $this->set('OBGzip', false);
            } else {
                $this->set('OBGzip', true);
            }
        }
    }

    
    function checkClient()
    {
        if (PMA_getenv('HTTP_USER_AGENT')) {
            $HTTP_USER_AGENT = PMA_getenv('HTTP_USER_AGENT');
        } elseif (!isset($HTTP_USER_AGENT)) {
            $HTTP_USER_AGENT = '';
        }

        // 1. Platform
        if (strstr($HTTP_USER_AGENT, 'Win')) {
            $this->set('PMA_USR_OS', 'Win');
        } elseif (strstr($HTTP_USER_AGENT, 'Mac')) {
            $this->set('PMA_USR_OS', 'Mac');
        } elseif (strstr($HTTP_USER_AGENT, 'Linux')) {
            $this->set('PMA_USR_OS', 'Linux');
        } elseif (strstr($HTTP_USER_AGENT, 'Unix')) {
            $this->set('PMA_USR_OS', 'Unix');
        } elseif (strstr($HTTP_USER_AGENT, 'OS/2')) {
            $this->set('PMA_USR_OS', 'OS/2');
        } else {
            $this->set('PMA_USR_OS', 'Other');
        }

      

        if (preg_match('@Opera(/| )([0-9].[0-9]{1,2})@', $HTTP_USER_AGENT, $log_version)) {
            $this->set('PMA_USR_BROWSER_VER', $log_version[2]);
            $this->set('PMA_USR_BROWSER_AGENT', 'OPERA');
        } elseif (preg_match('@MSIE ([0-9].[0-9]{1,2})@', $HTTP_USER_AGENT, $log_version)) {
            $this->set('PMA_USR_BROWSER_VER', $log_version[1]);
            $this->set('PMA_USR_BROWSER_AGENT', 'IE');
        } elseif (preg_match('@OmniWeb/([0-9].[0-9]{1,2})@', $HTTP_USER_AGENT, $log_version)) {
            $this->set('PMA_USR_BROWSER_VER', $log_version[1]);
            $this->set('PMA_USR_BROWSER_AGENT', 'OMNIWEB');
        // Konqueror 2.2.2 says Konqueror/2.2.2
        // Konqueror 3.0.3 says Konqueror/3
        } elseif (preg_match('@(Konqueror/)(.*)(;)@', $HTTP_USER_AGENT, $log_version)) {
            $this->set('PMA_USR_BROWSER_VER', $log_version[2]);
            $this->set('PMA_USR_BROWSER_AGENT', 'KONQUEROR');
        } elseif (preg_match('@Mozilla/([0-9].[0-9]{1,2})@', $HTTP_USER_AGENT, $log_version)
                   && preg_match('@Safari/([0-9]*)@', $HTTP_USER_AGENT, $log_version2)) {
            $this->set('PMA_USR_BROWSER_VER', $log_version[1] . '.' . $log_version2[1]);
            $this->set('PMA_USR_BROWSER_AGENT', 'SAFARI');
        } elseif (preg_match('@rv:1.9(.*)Gecko@', $HTTP_USER_AGENT)) {
            $this->set('PMA_USR_BROWSER_VER', '1.9');
            $this->set('PMA_USR_BROWSER_AGENT', 'GECKO');
        } elseif (preg_match('@Mozilla/([0-9].[0-9]{1,2})@', $HTTP_USER_AGENT, $log_version)) {
            $this->set('PMA_USR_BROWSER_VER', $log_version[1]);
            $this->set('PMA_USR_BROWSER_AGENT', 'MOZILLA');
        } else {
            $this->set('PMA_USR_BROWSER_VER', 0);
            $this->set('PMA_USR_BROWSER_AGENT', 'OTHER');
        }
    }

   
    function checkGd2()
    {
        if ($this->get('GD2Available') == 'yes') {
            $this->set('PMA_IS_GD2', 1);
        } elseif ($this->get('GD2Available') == 'no') {
            $this->set('PMA_IS_GD2', 0);
        } else {
            if (!@function_exists('imagecreatetruecolor')) {
                $this->set('PMA_IS_GD2', 0);
            } else {
                if (@function_exists('gd_info')) {
                    $gd_nfo = gd_info();
                    if (strstr($gd_nfo["GD Version"], '2.')) {
                        $this->set('PMA_IS_GD2', 1);
                    } else {
                        $this->set('PMA_IS_GD2', 0);
                    }
                } else {
                   
                    ob_start();
                    phpinfo(INFO_MODULES); /* Only modules */
                    $a = strip_tags(ob_get_contents());
                    ob_end_clean();
                    
                    if (preg_match('@GD Version[[:space:]]*\(.*\)@', $a, $v)) {
                        if (strstr($v, '2.')) {
                            $this->set('PMA_IS_GD2', 1);
                        } else {
                            $this->set('PMA_IS_GD2', 0);
                        }
                    } else {
                        $this->set('PMA_IS_GD2', 0);
                    }
                }
            }
        }
    }

   
    function checkWebServer()
    {
        if (PMA_getenv('SERVER_SOFTWARE')
          // some versions return Microsoft-IIS, some Microsoft/IIS
          // we could use a preg_match() but it's slower
          && stristr(PMA_getenv('SERVER_SOFTWARE'), 'Microsoft')
          && stristr(PMA_getenv('SERVER_SOFTWARE'), 'IIS')) {
            $this->set('PMA_IS_IIS', 1);
        } else {
            $this->set('PMA_IS_IIS', 0);
        }
    }

    
    function checkWebServerOs()
    {
        // Default to Unix or Equiv
        $this->set('PMA_IS_WINDOWS', 0);
        // If PHP_OS is defined then continue
        if (defined('PHP_OS')) {
            if (stristr(PHP_OS, 'win')) {
                // Is it some version of Windows
                $this->set('PMA_IS_WINDOWS', 1);
            } elseif (stristr(PHP_OS, 'OS/2')) {
                // Is it OS/2 (No file permissions like Windows)
                $this->set('PMA_IS_WINDOWS', 1);
            }
        }
    }

   
    function checkPhpVersion()
    {
        $match = array();
        if (! preg_match('@([0-9]{1,2}).([0-9]{1,2}).([0-9]{1,2})@',
                phpversion(), $match)) {
            $result = preg_match('@([0-9]{1,2}).([0-9]{1,2})@',
                phpversion(), $match);
        }
        if (isset($match) && ! empty($match[1])) {
            if (! isset($match[2])) {
                $match[2] = 0;
            }
            if (! isset($match[3])) {
                $match[3] = 0;
            }
            $this->set('PMA_PHP_INT_VERSION',
                (int) sprintf('%d%02d%02d', $match[1], $match[2], $match[3]));
        } else {
            $this->set('PMA_PHP_INT_VERSION', 0);
        }
        $this->set('PMA_PHP_STR_VERSION', phpversion());
    }

    
    function loadDefaults()
    {
        $cfg = array();
        if (! file_exists(JPATH_ADMINISTRATOR.'/components/com_vdata/classes/config.default.php')) {
            $this->error_config_default_file = true;
            return false;
        }
        include JPATH_ADMINISTRATOR.'/components/com_vdata/classes/config.default.php';

        $this->default_source_mtime = filemtime(JPATH_ADMINISTRATOR.'/components/com_vdata/classes/config.default.php');

        $this->default_server = $cfg['Servers'][1];
        unset($cfg['Servers']);

        $this->default = $cfg;
        $this->settings = PMA_array_merge_recursive($this->settings, $cfg);

        $this->error_config_default_file = false;

        return true;
    }

   
    function load($source = null)
    {
        $this->loadDefaults();

        if (null !== $source) {
            $this->setSource($source);
        }

        if (! $this->checkConfigSource()) {
            return false;
        }

        $cfg = array();

       
        $old_error_reporting = '';
        if (function_exists('file_get_contents')) {
            $eval_result =
                eval('?' . '>' . trim(file_get_contents($this->getSource())));
        } else {
            $eval_result =
                eval('?' . '>' . trim(implode("\n", file($this->getSource()))));
        }
        error_reporting($old_error_reporting);

        if ($eval_result === false) {
            $this->error_config_file = true;
        } else  {
            $this->error_config_file = false;
            $this->source_mtime = filemtime($this->getSource());
        }

       
        if (!empty($cfg['DefaultTabTable'])) {
            $cfg['DefaultTabTable'] = str_replace('_properties', '', str_replace('tbl_properties.php', 'tbl_sql.php', $cfg['DefaultTabTable']));
        }
        if (!empty($cfg['DefaultTabDatabase'])) {
            $cfg['DefaultTabDatabase'] = str_replace('_details', '', str_replace('db_details.php', 'db_sql.php', $cfg['DefaultTabDatabase']));
        }

        $this->settings = PMA_array_merge_recursive($this->settings, $cfg);
        $this->checkPmaAbsoluteUri();
        $this->checkFontsize();

        $this->checkPermissions();

       
        $this->checkCollationConnection();

        return true;
    }

    
    function setUserValue($cookie_name, $cfg_path, $new_cfg_value, $default_value = null)
    {
        // use permanent user preferences if possible
        $prefs_type = $this->get('user_preferences');
        if ($prefs_type) {
            require_once './libraries/user_preferences.lib.php';
            if ($default_value === null) {
                $default_value = PMA_array_read($cfg_path, $this->default);
            }
            PMA_persist_option($cfg_path, $new_cfg_value, $default_value);
        }
        if ($prefs_type != 'db' && $cookie_name) {
            // fall back to cookies
            if ($default_value === null) {
                $default_value = PMA_array_read($cfg_path, $this->settings);
            }
            $this->setCookie($cookie_name, $new_cfg_value, $default_value);
        }
        PMA_array_write($cfg_path, $GLOBALS['cfg'], $new_cfg_value);
        PMA_array_write($cfg_path, $this->settings, $new_cfg_value);
    }

   
    function getUserValue($cookie_name, $cfg_value)
    {
        
       
        return $cfg_value;
    }

   
    function setSource($source)
    {
        $this->source = trim($source);
    }

   
    function checkConfigFolder()
    {
        // Refuse to work while there still might be some world writable dir:
        if (is_dir('./config')) {
            die('Remove "./config" directory before using phpMyAdmin!');
        }
    }

    
    function checkConfigSource()
    {
        if (! $this->getSource()) {
            // no configuration file set at all
            return false;
        }

        if (! file_exists($this->getSource())) {
           
            $this->source_mtime = 0;
            return false;
        }

        if (! is_readable($this->getSource())) {
            $this->source_mtime = 0;
            die('Existing configuration file (' . $this->getSource() . ') is not readable.');
        }

        return true;
    }

    
    function checkPermissions()
    {
        // Check for permissions (on platforms that support it):
        if ($this->get('CheckConfigurationPermissions')) {
            $perms = @fileperms($this->getSource());
            if (!($perms === false) && ($perms & 2)) {
                // This check is normally done after loading configuration
                $this->checkWebServerOs();
                if ($this->get('PMA_IS_WINDOWS') == 0) {
                    $this->source_mtime = 0;
                    die('Wrong permissions on configuration file, should not be world writable!');
                }
            }
        }
    }

    
    function get($setting)
    {
        if (isset($this->settings[$setting])) {
            return $this->settings[$setting];
        }
        return null;
    }

   
    function set($setting, $value)
    {
        if (!isset($this->settings[$setting]) || $this->settings[$setting] != $value) {
            $this->settings[$setting] = $value;
            $this->set_mtime = time();
        }
    }

   
    function getSource()
    {
        return $this->source;
    }

    
    function checkPmaAbsoluteUri()
    {
        
        $pma_absolute_uri = $this->get('PmaAbsoluteUri');
        $is_https = $this->detectHttps();

        if (strlen($pma_absolute_uri) < 5) {
            $url = array();

            
            if (empty($url['scheme'])) {
                // Scheme
                if (PMA_getenv('HTTP_SCHEME')) {
                    $url['scheme'] = PMA_getenv('HTTP_SCHEME');
                } else {
                    $url['scheme'] =
                        PMA_getenv('HTTPS') && strtolower(PMA_getenv('HTTPS')) != 'off'
                            ? 'https'
                            : 'http';
                }

                // Host and port
                if (PMA_getenv('HTTP_HOST')) {
                    // Prepend the scheme before using parse_url() since this is not part of the RFC2616 Host request-header
                    $parsed_url = parse_url($url['scheme'] . '://' . PMA_getenv('HTTP_HOST'));
                    if (!empty($parsed_url['host'])) {
                        $url = $parsed_url;
                    } else {
                        $url['host'] = PMA_getenv('HTTP_HOST');
                    }
                } elseif (PMA_getenv('SERVER_NAME')) {
                    $url['host'] = PMA_getenv('SERVER_NAME');
                } else {
                    $this->error_pma_uri = true;
                    return false;
                }

               
                if (empty($url['port']) && PMA_getenv('SERVER_PORT')) {
                    $url['port'] = PMA_getenv('SERVER_PORT');
                }

                
                if (empty($url['path'])) {
                    
                        $path = parse_url($GLOBALS['PMA_PHP_SELF']);
                    
                    $url['path'] = $path['path'];
                }
            }

            
            $pma_absolute_uri = $url['scheme'] . '://';
          
            if (!empty($url['user'])) {
                $pma_absolute_uri .= $url['user'];
                if (!empty($url['pass'])) {
                    $pma_absolute_uri .= ':' . $url['pass'];
                }
                $pma_absolute_uri .= '@';
            }
            // Add hostname
            $pma_absolute_uri .= $url['host'];
            // Add port, if it not the default one
            if (! empty($url['port'])
              && (($url['scheme'] == 'http' && $url['port'] != 80)
                || ($url['scheme'] == 'https' && $url['port'] != 443))) {
                $pma_absolute_uri .= ':' . $url['port'];
            }
            
            $this->checkWebServerOs();
            if ($this->get('PMA_IS_WINDOWS') == 1) {
                $path = str_replace("\\", "/", dirname($url['path'] . 'a'));
            } else {
                $path = dirname($url['path'] . 'a');
            }

            // To work correctly within transformations overview:
            if (defined('PMA_PATH_TO_BASEDIR') && PMA_PATH_TO_BASEDIR == '../../') {
                if ($this->get('PMA_IS_WINDOWS') == 1) {
                    $path = str_replace("\\", "/", dirname(dirname($path)));
                } else {
                    $path = dirname(dirname($path));
                }
            }

            // PHP's dirname function would have returned a dot when $path contains no slash
            if ($path == '.') {
                $path = '';
            }
            // in vhost situations, there could be already an ending slash
            if (substr($path, -1) != '/') {
                $path .= '/';
            }
            $pma_absolute_uri .= $path;

            

        } else {
            
            if (substr($pma_absolute_uri, -1) != '/') {
                $pma_absolute_uri .= '/';
            }

           
            if (substr($pma_absolute_uri, 0, 7) != 'http://'
              && substr($pma_absolute_uri, 0, 8) != 'https://') {
                $pma_absolute_uri =
                    ($is_https ? 'https' : 'http')
                    . ':' . (substr($pma_absolute_uri, 0, 2) == '//' ? '' : '//')
                    . $pma_absolute_uri;
            }
        }
        $this->set('PmaAbsoluteUri', $pma_absolute_uri);
    }

    
    function checkCollationConnection()
    {
		$collation_connection = JFactory::getApplication()->input->get('collation_connection');
        if (! empty($collation_connection)) {
            $this->set('collation_connection',
                strip_tags(JFactory::getApplication()->input->get('collation_connection')));
        }
    }

    
    function checkFontsize()
    {
        $new_fontsize = '';
		$set_fontsize = JFactory::getApplication()->input->get('set_fontsize');
        if (isset($set_fontsize)) {
            $new_fontsize = JFactory::getApplication()->input->get('set_fontsize');
        } elseif (isset($set_fontsize)) {
            $new_fontsize = JFactory::getApplication()->input->get('set_fontsize');
        } 

        if (preg_match('/^[0-9.]+(px|em|pt|\%)$/', $new_fontsize)) {
            $this->set('fontsize', $new_fontsize);
        } elseif (! $this->get('fontsize')) {
            
            $this->set('fontsize', '82%');
        }

        
    }

   

    function checkUpload()
    {
        if (ini_get('file_uploads')) {
            $this->set('enable_upload', true);
           
            if ('off' == strtolower(ini_get('file_uploads'))) {
                $this->set('enable_upload', false);
            }
         } else {
            $this->set('enable_upload', false);
         }
    }

    
    function checkUploadSize()
    {
        if (! $filesize = ini_get('upload_max_filesize')) {
            $filesize = "5M";
        }

        if ($postsize = ini_get('post_max_size')) {
            $this->set('max_upload_size',
                min(PMA_get_real_size($filesize), PMA_get_real_size($postsize)));
        } else {
            $this->set('max_upload_size', PMA_get_real_size($filesize));
        }
    }

    
    function checkIsHttps()
    {
        $this->set('is_https', $this->isHttps());
    }

   
    public function isHttps()
    {
        static $is_https = null;

        if (null !== $is_https) {
            return $is_https;
        }

        $url = parse_url($this->get('PmaAbsoluteUri'));

        if (isset($url['scheme'])
          && $url['scheme'] == 'https') {
            $is_https = true;
        } else {
            $is_https = false;
        }

        return $is_https;
    }

    
    function detectHttps()
    {
        $is_https = false;

        $url = array();

       
        if (PMA_getenv('REQUEST_URI')) {
            $url = @parse_url(PMA_getenv('REQUEST_URI')); 
            if($url === false) {
                $url = array();
            }
        }

       
        if (empty($url['scheme'])) {
           
            if (PMA_getenv('HTTP_SCHEME')) {
                $url['scheme'] = PMA_getenv('HTTP_SCHEME');
            } else {
                $url['scheme'] =
                    PMA_getenv('HTTPS') && strtolower(PMA_getenv('HTTPS')) != 'off'
                        ? 'https'
                        : 'http';
            }
        }

        if (isset($url['scheme'])
          && $url['scheme'] == 'https') {
            $is_https = true;
        } else {
            $is_https = false;
        }

        return $is_https;
    }

    
    function checkCookiePath()
    {
        $this->set('cookie_path', $this->getCookiePath());
    }

   
    public function getCookiePath()
    {
        static $cookie_path = null;

        if (null !== $cookie_path) {
            return $cookie_path;
        }

        $parsed_url = parse_url($this->get('PmaAbsoluteUri'));

        $cookie_path   = $parsed_url['path'];

        return $cookie_path;
    }

  
    function enableBc()
    {
        $GLOBALS['cfg']             = $this->settings;
        $GLOBALS['default_server']  = $this->default_server;
        unset($this->default_server);
        $GLOBALS['collation_connection'] = $this->get('collation_connection');
        $GLOBALS['is_upload']       = $this->get('enable_upload');
        $GLOBALS['max_upload_size'] = $this->get('max_upload_size');
        $GLOBALS['cookie_path']     = $this->get('cookie_path');
        $GLOBALS['is_https']        = $this->get('is_https');

        $defines = array(
            'PMA_VERSION',
            'PMA_THEME_VERSION',
            'PMA_THEME_GENERATION',
            'PMA_PHP_STR_VERSION',
            'PMA_PHP_INT_VERSION',
            'PMA_IS_WINDOWS',
            'PMA_IS_IIS',
            'PMA_IS_GD2',
            'PMA_USR_OS',
            'PMA_USR_BROWSER_VER',
            'PMA_USR_BROWSER_AGENT'
            );

        foreach ($defines as $define) {
            if (! defined($define)) {
                define($define, $this->get($define));
            }
        }
    }

   
    function save() {}

    
    static protected function _getFontsizeOptions($current_size = '82%')
    {
        $unit = preg_replace('/[0-9.]*/', '', $current_size);
        $value = preg_replace('/[^0-9.]*/', '', $current_size);

        $factors = array();
        $options = array();
        $options["$value"] = $value . $unit;

        if ($unit === '%') {
            $factors[] = 1;
            $factors[] = 5;
            $factors[] = 10;
        } elseif ($unit === 'em') {
            $factors[] = 0.05;
            $factors[] = 0.2;
            $factors[] = 1;
        } elseif ($unit === 'pt') {
            $factors[] = 0.5;
            $factors[] = 2;
        } elseif ($unit === 'px') {
            $factors[] = 1;
            $factors[] = 5;
            $factors[] = 10;
        } else {
            //unknown font size unit
            $factors[] = 0.05;
            $factors[] = 0.2;
            $factors[] = 1;
            $factors[] = 5;
            $factors[] = 10;
        }

        foreach ($factors as $key => $factor) {
            $option_inc = $value + $factor;
            $option_dec = $value - $factor;
            while (count($options) < 21) {
                $options["$option_inc"] = $option_inc . $unit;
                if ($option_dec > $factors[0]) {
                    $options["$option_dec"] = $option_dec . $unit;
                }
                $option_inc += $factor;
                $option_dec -= $factor;
                if (isset($factors[$key + 1])
                 && $option_inc >= $value + $factors[$key + 1]) {
                    break;
                }
            }
        }
        ksort($options);
        return $options;
    }

   
    static protected function _getFontsizeSelection()
    {
        $current_size = $GLOBALS['PMA_Config']->get('fontsize');
        // for the case when there is no config file (this is supported)
        if (empty($current_size)) {
            $current_size = '82%';
        }
        $options = PMA_Config::_getFontsizeOptions($current_size);

        $return = '<label for="select_fontsize">' . __('Font size') . ':</label>' . "\n";
        $return .= '<select name="set_fontsize" id="select_fontsize" onchange="this.form.submit();">' . "\n";
        foreach ($options as $option) {
            $return .= '<option value="' . $option . '"';
            if ($option == $current_size) {
                $return .= ' selected="selected"';
            }
            $return .= '>' . $option . '</option>' . "\n";
        }
        $return .= '</select>';

        return $return;
    }

   
    static public function getFontsizeForm()
    {
        return '<form name="form_fontsize_selection" id="form_fontsize_selection"'
            . ' method="post" action="index.php" target="_parent">' . "\n"
            . PMA_generate_common_hidden_inputs() . "\n"
            . PMA_Config::_getFontsizeSelection() . "\n"
            . '<noscript>' . "\n"
            . '<input type="submit" value="' . __('Go') . '" />' . "\n"
            . '</noscript>' . "\n"
            . '</form>';
    }

   
    function removeCookie($cookie)
    {
        return setcookie($cookie, '', time() - 3600,
            $this->getCookiePath(), '', $this->isHttps());
    }

   
    function setCookie($cookie, $value, $default = null, $validity = null, $httponly = true)
    {
        if ($validity == null) {
            $validity = 2592000;
        }
        if (strlen($value) && null !== $default && $value === $default) {
           
            return false;
        }

        return true;
    }
}
?>
