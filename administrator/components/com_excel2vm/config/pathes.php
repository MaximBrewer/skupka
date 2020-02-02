<?php
  /**
 * @package jlvklike
 * @author Anton Voynov (anton@joomline.net)
 * @version 1.2
 * @copyright (C) 2010 by Anton Voynov(http://www.joomline.net)
 * @license JoomLine: http://joomline.net/licenzija-joomline.html
 *
*/

defined('JPATH_BASE') or die();
jimport('joomla.form.formfield');
/**
 * Renders a multiple item select element
 * using SQL result and explicitly specified params
 *
 */
if(!defined('DS')){
    define('DS',DIRECTORY_SEPARATOR);
}

class JFormFieldPathes extends JFormField{
	    protected     $type = 'pathes';

        protected function getInput(){


                $base=constant($this->element['constant']);
                if(substr($base,-1)!=DS){
                   $base.=DS;
                }
                $default=$this->element['link'];
                if(substr($default,0,1)==DS){
                    $default=substr($default,1);
                }
                $default=$base.$default;
                $value=$this->value?$this->value:$default;
                $size=@$this->element['size']?$this->element['size']:20;
                return '<input type="text" size="'.$size.'" value="'.$value.'" id="'.$this->name.'" name="'.$this->name.'" class="" aria-invalid="false">';
        }
}