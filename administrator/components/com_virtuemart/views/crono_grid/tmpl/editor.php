<?php 
/**
* @version: 2.2.0 (2013.12.03)
* @author: Vahrushev Konstantin
* @copyright: Copyright (C) 2012 crono.ru
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
* http://crono.ru
**/
defined('_JEXEC') or die('Restricted access');

JToolBarHelper::apply("save");
JToolBarHelper::cancel("cancel");
$toolbar = JToolBar::getInstance('toolbar');
$toolbarhtml = str_ireplace('Joomla.submitbutton', 'CRG.FrameEditor.save', $toolbar->render());
?>
<script>
function CRG_FrameEditor_save(task){
    CRG = parent.CRG;
    //CRG.CustomRestore();
    form = jQuery('#editor');
    //если wysiwyg, то вытягиваем значение редактора
    if(typeof(window["CRG_getWysiwygEditorText"])=="function"){
        form[0][form[0]._cellname.value].value = CRG_getWysiwygEditorText();
    }
    if(task=='save'){
        // отправляем аякс запрос
        form.ajaxSubmit({
            dataType:  'json', 
            success:function(saveresult){
                if(saveresult.saved){
                    tmp = {};
                    tmp[CRG.iName] = saveresult.data.displayvalue;
                    CRG.grid.jqGrid('setRowData',CRG.iRow, tmp); 
                }
                else alert(data.message);
            }
        }); 
    }
    //parent.jQuery('#modalbox').dialog('close');
}
</script>
<div id="toolbar-box">
    <div class="m">
        <?php echo $toolbarhtml?>
    </div>
</div>
<div class="m">
    <form action="" method="POST" name="exteditor" id="editor">
        <div>
            <?php echo $this->editor;?>
        </div>
        <input type='hidden' name="_rowid" value="<?php echo $this->product_id?>"> 
        <input type='hidden' name="_cellname" value="<?php echo $this->field?>"> 
        <input type='hidden' name="<?php echo $this->field?>" value=""> 
        <input type='hidden' name="option" value="com_virtuemart"/> 
        <input type='hidden' name="task" value="saveData"/> 
        <input type='hidden' name="format" value="json"/> 
    </form>
</div>