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

class JFormFieldLinks extends JFormField{
	    protected     $type = 'links';

        protected function getInput(){
			$default=$this->element['default'];
            if(substr($default,0,1)==DS){
                $default=substr($default,1);
            }
            $href=JURI::root().$default;
            $desc=str_replace(array('<br>','<br />','<br/>'),"\n",JText::_($this->element['description']));
            return  "<a style='float:left;margin-bottom: 5px; margin-top: 10px;' title='".$desc."' href='$href' target='_blank'>$href</a>";
        }
}