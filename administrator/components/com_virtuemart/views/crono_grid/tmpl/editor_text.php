<?php 
/**
* @version: 2.2.0 (2013.12.03)
* @author: Vahrushev Konstantin
* @copyright: Copyright (C) 2012 crono.ru
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
* http://crono.ru
**/
defined('_JEXEC') or die('Restricted access'); ?>
<?
    $editor = JFactory::getEditor();
    $field = $this->field;
    echo $editor->display( 'code',  $this->text, '100%', '400', '55', '30') ; 
    $doc = JFactory::getDocument();
    $script = 'function CRG_FrameEditor_getvalue() {'."\n return ".$editor->getContent('code')."\n}\n";
    $doc->addScriptDeclaration($script);
?>
</div>