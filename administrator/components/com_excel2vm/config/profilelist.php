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


class JFormFieldProfilelist extends JFormField{
	    protected     $type = 'profilelist';

        protected function getInput(){
				$db = JFactory::getDBO();
				$key = 'id';
				$val = 'profile';

				//$db->debug(1);
				$db->setQuery("	SELECT $key,$val
								FROM #__excel2vm
								ORDER BY id");
				$rows = $db->loadAssocList();
                
				if (count($rows)>0)
				foreach ($rows as $row)
					$options[]=array($key=>$row[$key],$val=>$row[$val]);

                if($options){
                        return JHTML::_('select.genericlist',$options, $this->name, ' size="1"', $key, $val,$this->value);
                }
        }
}