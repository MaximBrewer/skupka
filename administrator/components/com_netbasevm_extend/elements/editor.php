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

class JElementEditor extends JElement
{
    
    var $_name = 'Editor';

    function fetchElement ($name, $value, &$node, $control_name)
    {
        $editor = JFactory::getEditor();
        /* @var $editor JEditor */

        $cols = $node->attributes( 'cols' )>0 ? $node->attributes( 'cols' ) : 1;
        $rows = $node->attributes( 'rows' )>0 ? $node->attributes( 'rows' ) : 1;
        $width = $node->attributes( 'width' )>0 ? $node->attributes( 'width' ) : 800;
        $height = $node->attributes( 'height' )>0 ? $node->attributes( 'height' ) : 500;
        
        $code = $editor->display($control_name . '[' . $name . ']', $value, $width, $height, $cols, $rows);
        return $code;
    }
}
?>