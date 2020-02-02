<?php
class CfOutput
{
    //the variable which will be escaped
    protected $variable;

    //indicates if the array of variables is per filter
    public $perfilter;

    // all the outputs are stored here
    protected static $cfOutputs = array();

    //cache for the encoded inputs
    protected static $encodedInputs=array();

    /**
     * Gets the output (cached)
     *
     * @param mixed $variable
     *
     * @since 2.3.0
     * @author Sakis Terz
     */
    public static function getOutput($variable, $escape=true, $perfilter=false)
    {
        $hash = md5(json_encode($variable).$escape.$perfilter);
        if (! isset(self::$cfOutputs[$hash])) {
            $cfOutput = new CfOutput();
            $cfOutput->setVariable($variable);
            $cfOutput->setPerfilter($perfilter);
            $cfOutput->setEscape($escape);
            self::$cfOutputs[$hash] = $cfOutput->prepareVariables();
        }

        return self::$cfOutputs[$hash];
    }

    /**
     * Sets the variable inthe class
     *
     * @param mixed $variable
     */
    public function setVariable($variable)
    {
        $this->variable = $variable;
    }

    /**
     * Sets the perfilter inthe class
     *
     * @param mixed $variable
     */
    public function setPerfilter($perfilter)
    {
        $this->perfilter = $perfilter;
    }

    public function setEscape($escape){
        $this->escape = $escape;
    }

    /**
     * Escapes variables for output
     *
     * @return array
     *
     * @since 2.3.0
     */
    public function prepareVariables()
    {
        if (empty($this->variable)) return $this->variable;
        if (! is_array($this->variable)) return $this->escape?htmlspecialchars($this->variable, ENT_COMPAT, 'UTF-8'):$this->variable;

        $new_array = array();
        foreach ($this->variable as $var_name => $var) {
            if (is_array($var)) {
                $new_array[$var_name] = array();

                // is custom
                if (strpos($var_name, 'custom_f_') !== false && $this->perfilter==false) {
                    $new_array[$var_name] = $this->encodeVar($var, $var_name);
                } else {
                    foreach ($var as $key => $var2) {
                        if (! empty($var2) && ! is_array($var2))
                            $new_array[$var_name][$key] =$this->escape?htmlspecialchars($var2, ENT_COMPAT, 'UTF-8'):$var2;

                         // multi-dimensional array possibly holding the filters per perfilter
                        else {
                            $new_array[$var_name][$key] = array();

                            // is custom
                            if (strpos($key, 'custom_f_') !== false) {
                                $new_array[$var_name][$key] = $this->encodeVar($var2, $key);
                            }

                            else if(is_array($var2)){
                                foreach ($var2 as $key2 => $var3) {
                                    $new_array[$var_name][$key][$key2] =$this->escape?htmlspecialchars($var3, ENT_COMPAT, 'UTF-8'):$var3;
                                }
                            }
                        }
                    }
                }
            }
            // scalar var
            else $new_array[$var_name] = htmlspecialchars($var, ENT_COMPAT, 'UTF-8');
        }
        return $new_array;
    }

    /**
     * Some vars such as the custom filters values needs to be encoded
     * This function is mainly called by the module that needs the values as output to check for the selected values
     *
     * @param array $array
     *            inputs
     * @param boolean $perfilter
     *            case each key (filter name) contains a multidimensional array containing all the variables of other filters
     * @return array output
     * @since 2.2.0
     * @author Sakis Terz
     */
    public function encodeVar($array, $var_name)
    {
        $store = md5(json_encode($array).$var_name);

        if (! isset(self::$encodedInputs[$store])) {
            $published_cf = cftools::getCustomFilters();
            $newarray=array();

            if (isset($published_cf)) {

                // find the id of the custom
                preg_match('/[0-9]+/', $var_name, $mathcess);
                if (! empty($mathcess[0])) {
                    $cf = $published_cf[$mathcess[0]];

                    // if not number range or date, encode it
                    if ($cf->disp_type != 5 && $cf->disp_type != 6 && $cf->disp_type != 8) {
                        $newarray= cftools::bin2hexArray($array);
                    }
                    else $newarray=$array;
                }
            }
            self::$encodedInputs[$store] = $newarray;
        }
        return self::$encodedInputs[$store];
    }
}