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
class Template
{
    
    private $_vars;

    
    private $_file;

    
    public function __construct($file=null)
    {
        $this->_file = $file;
        $this->_vars = array();
    }

    /**
     * Set a template variable.
     *
     * @param string variable name
     * @param string variable value
     */
    public function set($name, $value)
    {
        $this->_vars[$name] = is_object($value) ? $value->fetch() : $value;
    }

    /**
     * Open, parse, and return the template file.
     *
     * @param string $file
     *
     * @return string
     */
    public function fetch($file=null)
    {
        if (!$file) {
            $file = $this->_file;
        }

        // Extract the vars to local namespace
        extract($this->_vars);

        // Start output buffering
        ob_start();

        include(APP_ROOT.$file);

        // Get the contents of the buffer
        $contents = ob_get_contents();

        // End buffering and discard
        ob_end_clean();

        return $contents;
    }
}
