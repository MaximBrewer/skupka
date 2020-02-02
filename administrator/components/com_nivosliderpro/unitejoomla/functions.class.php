<?php

// file version: 1.0.3
//define dmp function
if (function_exists("dmp") == false) {

    function dmp($str) {
        echo "<pre>";
        print_r($str);
        echo "</pre>";
    }

}

class UniteFunctions {

    public static function isJoomla3() {

        if (defined("JVERSION")) {
            $version = JVERSION;
            $version = (int) $version;
            return($version == 3);
        }

        if (class_exists("JVersion")) {
            $jversion = new JVersion;
            $version = $jversion->getShortVersion();
            $version = (int) $version;
            return($version == 3);
        }

        return(!defined("DS"));
    }

    public static function throwError($message, $code = null) {
        if (!empty($code))
            throw new Exception($message, $code);
        else
            throw new Exception($message);
    }

    //--------------------------------------------------------------------------------
    //get date db string
    public static function getDateDBString($timestamp = null) {
        if ($timestamp == null)
            $timestamp = time();
        date_default_timezone_set('Asia/Jerusalem');
        $str = date("Y-m-d H:i:s");
        return($str);
    }

    //------------------------------------------------------------
    // get black value from rgb value
    public static function yiq($r, $g, $b) {
        return (($r * 0.299) + ($g * 0.587) + ($b * 0.114));
    }

    //------------------------------------------------------------	
    // convert colors to rgb
    public static function html2rgb($color) {
        if ($color[0] == '#')
            $color = substr($color, 1);
        if (strlen($color) == 6)
            list($r, $g, $b) = array($color[0] . $color[1],
                $color[2] . $color[3],
                $color[4] . $color[5]);
        elseif (strlen($color) == 3)
            list($r, $g, $b) = array($color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]);
        else
            return false;
        $r = hexdec($r);
        $g = hexdec($g);
        $b = hexdec($b);
        return array($r, $g, $b);
    }

    //---------------------------------------------------------------------------------------------------
    // convert timestamp to time string
    public static function timestamp2Time($stamp) {
        $strTime = date("H:i", $stamp);
        return($strTime);
    }

    //---------------------------------------------------------------------------------------------------
    // convert timestamp to date and time string
    public static function timestamp2DateTime($stamp) {
        $strDateTime = date("d M Y, H:i", $stamp);
        return($strDateTime);
    }

    //---------------------------------------------------------------------------------------------------
    // convert timestamp to date string
    public static function timestamp2Date($stamp) {
        $strDate = date("d M Y", $stamp); //27 Jun 2009
        return($strDate);
    }

    /**
     * get value from array. if not - return alternative
     */
    public static function getVal($arr, $key, $altVal = "") {
        if (isset($arr[$key]))
            return($arr[$key]);
        return($altVal);
    }

    //------------------------------------------------------------

    public static function toString($obj) {
        return(trim((string) $obj));
    }

    //------------------------------------------------------------
    // get variable from post or from get. get wins.
    public static function getPostGetVariable($name, $initVar = "") {
        $var = $initVar;
        if (isset($_POST[$name]))
            $var = $_POST[$name];
        else if (isset($_GET[$name]))
            $var = $_GET[$name];
        return($var);
    }

    //------------------------------------------------------------

    public static function getPostVariable($name, $initVar = "") {
        $var = $initVar;
        if (isset($_POST[$name]))
            $var = $_POST[$name];
        return($var);
    }

    //------------------------------------------------------------


    public static function getGetVar($name, $initVar = "") {
        $var = $initVar;
        if (isset($_GET[$name]))
            $var = $_GET[$name];
        return($var);
    }

    /**
     * 
     * validate that some file exists, if not - throw error
     */
    public static function validateFilepath($filepath, $errorPrefix = null) {
        if (file_exists($filepath) == true)
            return(false);
        if ($errorPrefix == null)
            $errorPrefix = "File";
        $message = $errorPrefix . " $filepath not exists!";
        self::throwError($message);
    }

    /**
     * 
     * validate that some array field (or fields) exists.
     * could be coma saparaeted fields.
     */
    public static function validateArrayFieldExists($arr, $fields, $messagePrefix = "Field not found") {

        if (is_array($arr) == false)
            self::throwError("the array given is not array!!!");

        if (empty($fields))
            self::throwError("Please give some fields to this function.");

        $arrFields = explode(",", $fields);
        foreach ($arrFields as $field) {
            if (array_key_exists($field, $arr) == false)
                self::throwError($messagePrefix . ": <b>" . $field . "</b>");
        }
    }

    /**
     * 
     * validate that some value is numeric
     */
    public static function validateNumeric($val, $fieldName = "") {
        self::validateNotEmpty($val, $fieldName);

        if (empty($fieldName))
            $fieldName = "Field";

        if (!is_numeric($val))
            self::throwError("$fieldName should be numeric ");
    }

    /**
     * 
     * validate that some variable not empty
     */
    public static function validateNotEmpty($val, $fieldName = "") {

        if (empty($fieldName))
            $fieldName = "Field";

        if (empty($val) && is_numeric($val) == false)
            self::throwError("Field <b>$fieldName</b> should not be empty");
    }

    /**
     * 
     * if directory not exists - create it
     * @param $dir
     */
    public static function checkCreateDir($dir) {
        if (!is_dir($dir))
            mkdir($dir);
    }

    /**
     * 
     * get some directory cotnents including files and folders, filter the "." and the ".." on the way
     * type can be all, file, dir
     */
    public static function getFolderContents($path, $type = "all") {
        if (!is_dir($path))
            self::throwError("path not found: $path");

        $path = realpath($path) . "/";

        $arrDirContents = scandir($path);
        $arrNew = array();
        foreach ($arrDirContents as $key => $file) {
            if ($file == "." || $file == "..")
                continue;

            $filepath = $path . $file;
            $skip = false;
            switch ($type) {
                case "file":
                    if (is_dir($filepath))
                        $skip = true;
                    break;
                case "dir":
                    if (is_file($filepath))
                        $skip = true;
                    break;
            }

            if ($skip == true)
                continue;

            $arrNew[] = $file;
        }

        return($arrNew);
    }

    /**
     * 
     * get only child directories from some folder
     * 
     */
    public static function getFolderDirs($path) {
        $arrContents = self::getFolderContents($path, "dir");
        return($arrContents);
    }

    /**
     * 
     * get only child directories from some folder
     * 
     */
    public static function getFolderFiles($path) {
        $arrContents = self::getFolderContents($path, "file");
        return($arrContents);
    }

    /**
     * filter array, leaving only needed fields - also array
     */
    public static function filterArrFields($arr, $fields) {
        $arrNew = array();
        foreach ($fields as $field) {
            if (isset($arr[$field]))
                $arrNew[$field] = $arr[$field];
        }
        return($arrNew);
    }

    //------------------------------------------------------------
    //get path info of certain path with all needed fields
    public static function getPathInfo($filepath) {
        $info = pathinfo($filepath);

        //fix the filename problem
        if (!isset($info["filename"])) {
            $filename = $info["basename"];
            if (isset($info["extension"]))
                $filename = substr($info["basename"], 0, (-strlen($info["extension"]) - 1));
            $info["filename"] = $filename;
        }

        return($info);
    }

    /**
     * Convert std class to array, with all sons
     * @param unknown_type $arr
     */
    public static function convertStdClassToArray($arr) {

        if (getType($arr) != "array" && getType($arr) != "object")
            return($arr);

        $arr = (array) $arr;
        foreach ($arr as $key => $item) {
            if (getType($item) == "array" || getType($item) == "object")
                $item = self::convertStdClassToArray($item);
            $arr[$key] = $item;
        }
        return($arr);
    }

    //------------------------------------------------------------
    //save some file to the filesystem with some text
    public static function writeFile($str, $filepath) {
        $fp = fopen($filepath, "w+");
        fwrite($fp, $str);
        fclose($fp);
    }

    //------------------------------------------------------------
    //save some file to the filesystem with some text
    public static function writeDebug($str, $filepath = "debug.txt", $showInputs = true) {
        $post = print_r($_POST, true);
        $server = print_r($_SERVER, true);

        if (getType($str) == "array")
            $str = print_r($str, true);

        if ($showInputs == true) {
            $output = "--------------------" . "\n";
            $output .= self::getDateDBString() . "\n";
            $output .= $str . "\n";
            $output .= "Post: " . $post . "\n";
        } else {
            $output = "---" . "\n";
            $output .= $str . "\n";
        }

        if (!empty($_GET)) {
            $get = print_r($_GET, true);
            $output .= "Get: " . $get . "\n";
        }

        //$output .= "Server: ".$server."\n";

        $fp = fopen($filepath, "a+");
        fwrite($fp, $output);
        fclose($fp);
    }

    /**
     * 
     * clear debug file
     */
    public static function clearDebug($filepath = "debug.txt") {

        if (file_exists($filepath))
            unlink($filepath);
    }

    /**
     * 
     * save to filesystem the error
     */
    public static function writeDebugError(Exception $e, $filepath = "debug.txt") {
        $message = $e->getMessage();
        $trace = $e->getTraceAsString();

        $output = $message . "\n";
        $output .= $trace . "\n";

        $fp = fopen($filepath, "a+");
        fwrite($fp, $output);
        fclose($fp);
    }

    //------------------------------------------------------------
    //save some file to the filesystem with some text
    public static function addToFile($str, $filepath) {
        $fp = fopen($filepath, "a+");
        fwrite($fp, "---------------------\n");
        fwrite($fp, $str . "\n");
        fclose($fp);
    }

    //--------------------------------------------------------------
    //check the php version. throw exception if the version beneath 5
    private static function checkPHPVersion() {
        $strVersion = phpversion();
        $version = (float) $strVersion;
        if ($version < 5)
            throw new Exception("You must have php5 and higher in order to run the application. Your php version is: $version");
    }

    //--------------------------------------------------------------
    // valiadte if gd exists. if not - throw exception
    private static function validateGD() {
        if (function_exists('gd_info') == false)
            throw new Exception("You need GD library to be available in order to run this application. Please turn it on in php.ini");
    }

    //--------------------------------------------------------------
    //return if the json library is activated
    public static function isJsonActivated() {
        return(function_exists('json_encode'));
    }

    /**
     * 
     * encode array into json for client side
     */
    public static function jsonEncodeForClientSide($arr) {
        $json = "";
        if (!empty($arr)) {
            $json = json_encode($arr);
            $json = addslashes($json);
        }

        $json = "'" . $json . "'";

        return($json);
    }

    //--------------------------------------------------------------
    //validate if some directory is writable, if not - throw a exception
    private static function validateWritable($name, $path, $strList, $validateExists = true) {

        if ($validateExists == true) {
            //if the file/directory doesn't exists - throw an error.
            if (file_exists($path) == false)
                throw new Exception("$name doesn't exists");
        }
        else {
            //if the file not exists - don't check. it will be created.
            if (file_exists($path) == false)
                return(false);
        }

        if (is_writable($path) == false) {
            chmod($path, 0755);  //try to change the permissions
            if (is_writable($path) == false) {
                $strType = "Folder";
                if (is_file($path))
                    $strType = "File";
                $message = "$strType $name is doesn't have a write permissions. Those folders/files must have a write permissions in order that this application will work properly: $strList";
                throw new Exception($message);
            }
        }
    }

    //--------------------------------------------------------------
    //validate presets for identical keys
    public static function validatePresets() {
        global $g_presets;
        if (empty($g_presets))
            return(false);
        //check for duplicates
        $assoc = array();
        foreach ($g_presets as $preset) {
            $id = $preset["id"];
            if (isset($assoc[$id]))
                throw new Exception("Double preset ID detected: $id");
            $assoc[$id] = true;
        }
    }

    //--------------------------------------------------------------
    //Get url of image for output
    public static function getImageOutputUrl($filename, $width = 0, $height = 0, $exact = false) {
        //exact validation:
        if ($exact == "true" && (empty($width) || empty($height) ))
            self::throwError("Exact must have both - width and height");

        $url = CMGlobals::$URL_GALLERY . "?img=" . $filename;
        if (!empty($width))
            $url .= "&w=" . $width;

        if (!empty($height))
            $url .= "&h=" . $height;

        if ($exact == true)
            $url .= "&t=exact";

        return($url);
    }

    /**
     * 
     * get list of all files in the directory
     */
    public static function getFileList($path) {
        $dir = scandir($path);
        $arrFiles = array();
        foreach ($dir as $file) {
            if ($file == "." || $file == "..")
                continue;
            $filepath = $path . "/" . $file;
            if (is_file($filepath))
                $arrFiles[] = $file;
        }
        return($arrFiles);
    }

    /**
     * 
     * do "trim" operation on all array items.
     */
    public static function trimArrayItems($arr) {
        if (gettype($arr) != "array")
            UniteFunctions::throwError("trimArrayItems error: The type must be array");

        foreach ($arr as $key => $item)
            $arr[$key] = trim($item);

        return($arr);
    }

    /**
     * 
     * get url contents
     */
    public static function getUrlContents($url, $arrPost = array(), $method = "post", $debug = false) {
        $ch = curl_init();
        $timeout = 0;

        $strPost = '';
        foreach ($arrPost as $key => $value) {
            if (!empty($strPost))
                $strPost .= "&";
            $value = urlencode($value);
            $strPost .= "$key=$value";
        }


        //set curl options
        if (strtolower($method) == "post") {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $strPost);
        }
        else //get
            $url .= "?" . $strPost;

        //remove me
        //Functions::addToLogFile(SERVICE_LOG_SERVICE, "url", $url);				

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

        $headers = array();
        $headers[] = "User-Agent:Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.2.8) Gecko/20100722 Firefox/3.6.8";
        $headers[] = "Accept-Charset:utf-8;q=0.7,*;q=0.7";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);

        if ($debug == true) {
            dmp($url);
            dmp($response);
            exit();
        }

        if ($response == false)
            throw new Exception("getUrlContents Request failed");

        curl_close($ch);
        return($response);
    }

    /**
     * 
     * get link html
     */
    public static function getHtmlLink($link, $text, $id = "", $class = "") {

        if (!empty($class))
            $class = " class='$class'";

        if (!empty($id))
            $id = " id='$id'";

        $html = "<a href=\"$link\"" . $id . $class . ">$text</a>";
        return($html);
    }

    /**
     * 
     * get select from array
     */
    public static function getHTMLSelect($arr, $default = "", $htmlParams = "", $assoc = false) {

        $html = "<select $htmlParams>";
        foreach ($arr as $key => $item) {
            $selected = "";

            if ($assoc == false) {
                if ($item == $default)
                    $selected = " selected ";
            }
            else {
                if (trim($key) == trim($default))
                    $selected = " selected ";
            }


            if ($assoc == true)
                $html .= "<option $selected value='$key'>$item</option>";
            else
                $html .= "<option $selected value='$item'>$item</option>";
        }
        $html.= "</select>";
        return($html);
    }

    /**
     * 
     * convert array to assoc array by some field
     */
    public static function arrayToAssoc($arr, $field) {
        $arrAssoc = array();
        foreach ($arr as $item)
            $arrAssoc[$item[$field]] = $item;

        return($arrAssoc);
    }

    /**
     *
     * convert assoc array to array
     */
    public static function assocToArray($assoc) {
        $arr = array();
        foreach ($assoc as $item)
            $arr[] = $item;

        return($arr);
    }

    /**
     * 
     * strip slashes from textarea content after ajax request to server
     */
    public static function normalizeTextareaContent($content) {
        if (empty($content))
            return($content);
        $content = stripslashes($content);
        $content = trim($content);
        return($content);
    }

    /**
     * 
     * echo json array, and exit.
     */
    public static function jsonResponse($data) {
        $json = json_encode($data);
        echo $json;
        exit();
    }

    /**
     * 
     * Enter description here ...
     * @param unknown_type $response
     */
    public static function jsonErrorResponse($message, $action = "") {
        $data = array();
        $data["success"] = false;
        $data["message"] = $message;
        $data["aciton"] = $action;
        self::jsonResponse($data);
    }

    /**
     * 
     * get all defined constants list with values.
     */
    public static function getDefinedConstants() {
        $arr = get_defined_constants(true);
        $arrUser = $arr["user"];
        return($arrUser);
    }

    /**
     * 
     * parse settings (name=value) file content
     */
    public static function parseSettingsFile($content) {
        $arrSettings = array();
        $lines = explode("\n", $content);

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line))
                continue;
            $arr = explode("=", $line);
            if (count($arr) != 2)
                continue;

            $key = trim($arr[0]);
            $value = trim($arr[1]);

            $arrSettings[$key] = $value;
        }

        return($arrSettings);
    }

    /**
     * 
     * output error message
     */
    public static function errorHtmlOutput($message) {
        header("Status: 404 Not Found");
        echo $message;
        exit();
    }

    /**
     * Generate random string
     * @param $len
     */
    public static function generateRandomString($len) {
        $strHash = "";
        for ($i = 0; $i < 3; $i++) {
            $randomNum = 99999 * rand();
            $hash = base64_encode($randomNum);
            $hash = str_replace("=", "", $hash);
            $strHash .= $hash;
        }
        $strHash = strtolower($strHash);
        $output = substr($strHash, 0, $len);
        return($output);
    }

    /**
     * 
     * redirect to some url
     */
    public static function redirectToUrl($url) {
        header("location: $url");
        exit();
    }

    /**
     * return true/false if the email is valid
     */
    public static function isEmailValid($email) {

        return preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/', $email);
    }

    //------------------------------------------------------------------------------------------
    // output file for downloading
    public static function downloadFile($filepath, $filename, $mimeType = "") {
        $contents = file_get_contents($filepath);
        $filesize = strlen($contents);

        if ($mimeType == "") {
            $info = pathinfo($filepath);
            $ext = $info["extension"];
            switch ($ext) {
                case "zip":
                    $mimeType = "application/$ext";
                    break;
                default:
                    $mimeType = "image/$ext";
                    break;
            }
        }

        header("Content-Type: $mimeType");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Length: $filesize");
        echo $contents;
        exit();
    }

}

?>