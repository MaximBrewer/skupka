<?php
/**
 * @package 	formfields
 * @version		1.0
 * @created		Apr 2012
 * @author		BowThemes
 * @email		support@bowthems.com
 * @website		http://bowthemes.com
 * @support		Forum - http://bowthemes.com/forum/
 * @copyright	Copyright (C) 2012 Bowthemes. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 */
// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.form.formfield');
class JFormFieldDeleteImages extends JFormField{
    protected $type = 'deleteimages';
    protected function  getInput() {
        $html  = '<button id="btnDeleteAll">'.JText::_("MOD_BTBGSLIDESHOW_BUTTON_DELETEALL").'</button>';
        return $html;
    }

}