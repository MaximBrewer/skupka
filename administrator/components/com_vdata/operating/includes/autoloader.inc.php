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
function __autoload($class_name)
{
    //$class_name = str_replace('-', '', $class_name);

    /* case-insensitive folders */
    $dirs = array('/plugins/'.strtolower($class_name).'/', '/includes/mb/', '/includes/ups/');
   
    foreach ($dirs as $dir) {
        if (file_exists(APP_ROOT.$dir.'class.'.strtolower($class_name).'.inc.php')) {
            include_once APP_ROOT.$dir.'class.'.strtolower($class_name).'.inc.php';

            return;
        }
    }

    /* case-sensitive folders */
    $dirs = array('/includes/', '/includes/interface/', '/includes/to/', '/includes/to/device/', '/includes/os/', '/includes/plugin/', '/includes/xml/', '/includes/web/', '/includes/error/', '/includes/js/', '/includes/output/');

    foreach ($dirs as $dir) {
        if (file_exists(APP_ROOT.$dir.'class.'.$class_name.'.inc.php')) {
            include_once APP_ROOT.$dir.'class.'.$class_name.'.inc.php';

            return;
        }
    }

    $error = Errorss::singleton();

    $error->addError("_autoload(\"".$class_name."\")", "autoloading of class file (class.".$class_name.".inc.php) failed!");
    $error->errorsAsXML();
}

/**
 * sets a user-defined error handler function
 *
 * @param integer $level   contains the level of the error raised, as an integer.
 * @param string  $message contains the error message, as a string.
 * @param string  $file    which contains the filename that the error was raised in, as a string.
 * @param integer $line    which contains the line number the error was raised at, as an integer.
 *
 * @return void
 */
function errorHandlerPsi($level, $message, $file, $line)
{
    $error = Errorss::singleton();
    $error->addPhpError("errorHandlerPsi : ", "Level : ".$level." Message : ".$message." File : ".$file." Line : ".$line);
}

set_error_handler('errorHandlerPsi');
