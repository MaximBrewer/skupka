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
class SensorDevice
{
    /**
     * name of the sensor
     *
     * @var String
     */
    private $_name = "";

    /**
     * current value of the sensor
     *
     * @var Integer
     */
    private $_value = 0;

    /**
     * maximum value of the sensor
     *
     * @var Integer
     */
    private $_max = null;

    /**
     * minimum value of the sensor
     *
     * @var Integer
     */
    private $_min = null;

    /**
     * event of the sensor
     *
     * @var String
     */
    private $_event = "";

    /**
     * Returns $_max.
     *
     * @see Sensor::$_max
     *
     * @return Integer
     */
    public function getMax()
    {
        return $this->_max;
    }

    /**
     * Sets $_max.
     *
     * @param Integer $max maximum value
     *
     * @see Sensor::$_max
     *
     * @return Void
     */
    public function setMax($max)
    {
        $this->_max = $max;
    }

    /**
     * Returns $_min.
     *
     * @see Sensor::$_min
     *
     * @return Integer
     */
    public function getMin()
    {
        return $this->_min;
    }

    /**
     * Sets $_min.
     *
     * @param Integer $min minimum value
     *
     * @see Sensor::$_min
     *
     * @return Void
     */
    public function setMin($min)
    {
        $this->_min = $min;
    }

    /**
     * Returns $_name.
     *
     * @see Sensor::$_name
     *
     * @return String
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Sets $_name.
     *
     * @param String $name sensor name
     *
     * @see Sensor::$_name
     *
     * @return Void
     */
    public function setName($name)
    {
        $this->_name = $name;
    }

    /**
     * Returns $_value.
     *
     * @see Sensor::$_value
     *
     * @return Integer
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * Sets $_value.
     *
     * @param Integer $value current value
     *
     * @see Sensor::$_value
     *
     * @return Void
     */
    public function setValue($value)
    {
        $this->_value = $value;
    }

    /**
     * Returns $_event.
     *
     * @see Sensor::$_event
     *
     * @return String
     */
    public function getEvent()
    {
        return $this->_event;
    }

    /**
     * Sets $_event.
     *
     * @param String $event sensor event
     *
     * @see Sensor::$_event
     *
     * @return Void
     */
    public function setEvent($event)
    {
        $this->_event = $event;
    }
}
