<?php
/*------------------------------------
* -Netbase- Advanced Virtuemart Invoices for Virtuemart
* Author    CMSMart Team
* Copyright (C) 2012 http://cmsmart.net. All Rights Reserved.
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* Websites: http://cmsmart.net
* Email: team@cmsmart.net
* Technical Support:  Forum - http://bloorum.com/forums
-----------------------------------------------------*/

defined('_JEXEC') or die('Restricted access');

class JElementImage extends JElement
{
    
    var $_name = 'Image';

    function fetchElement ($name, $value, &$node, $control_name)
    {

    	//NOTE: it is necessary to add this scripts to template:
    	/*
			//function override to write img src from com_media to text field
			function jInsertEditorText(img,editor)
			{
				pattern =/src=\"([^\"]+)\"/i;
				matches = img.match(pattern);
					
				$(editor).value = matches[1];
			}
    	*/
    	$code = '<input style="float:left" type="text" name="'.$control_name .'['.$name .']'.'" value="'.$value.'" id="'.$control_name.$name.'" size="50">';
        
        $link = 'index.php?option=com_media&amp;view=images&amp;layout=default&amp;tmpl=component&amp;e_name='.$control_name.$name;

		JHTML::_('behavior.modal');

		$code.='
			<div class="button2-left">
			<div class="image">
			<a class="modal-button" title="Image" href="'.$link.'" onclick="IeCursorFix(); return false;" 
				rel="{handler: \'iframe\', size: {x: 570, y: 400}}">'.JText::_('COM_VMINVOICE_SELECT').'</a>
			</div>
			</div>';

        return $code;
    }
}
?>