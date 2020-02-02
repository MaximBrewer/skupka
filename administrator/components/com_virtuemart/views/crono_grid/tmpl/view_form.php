<?php defined('_JEXEC') or die('Restricted access'); 
/**
* 
* @version: 2.2.0 (2013.12.03)
* @author: Vahrushev Konstantin
* @copyright: Copyright (C) 2012 crono.ru
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
* http://crono.ru
**/
?>
<?php
$doc = JFactory::getDocument();
$doc->addScriptDeclaration('jQuery(document).ready(function(){CRG.FieldSelector.Init();})');
?>
<script type="text/javascript">
    jQuery('button.btn').removeAttr('onclick');
    jQuery('button.btn').click(function(){
        index = jQuery(this).parent().index()+1;
        if(index==1){
            jQuery('#view_task').val('viewList');
            if(CRG.view.Save())jQuery('#adminForm').submit();
        }
        if(index==2){
            CRG.view.Save();
            if(CRG.view.Save())jQuery('#adminForm').submit();
        }
        if(index==3){
            history.back();
        }
    });
</script>
<?php $this->loadAssetsFiles(false);?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
    <?php echo JText::_('COM_VIRTUEMART_NAME')?> <input type="text" name="name" value="<?php echo @$this->view->name?>" id="view_name">
    <?php echo JText::_('COM_VIRTUEMART_DESCRIPTION')?>
    <textarea cols="" rows="" name="description"  id="view_desc"><?php echo @$this->view->desc?></textarea>
    
    <!-- Контейнер таблицы выбора полей --> 
    <div id='fields-selector' style="width: 96%; margin-left: 10px;"><table id="treefieldselector"></table></div>
    <input type="hidden" name="option" value="com_virtuemart" />
    <input type="hidden" name="controller" value="crono_grid" />
    <input type="hidden" name="task"value="" id='view_task'/>
    <input type="hidden" name="view_id" id="view_id" value="<?php echo $this->view_id?>" />
    <?php echo JHTML::_( 'form.token' ); ?>
</form>
<?php //AdminUIHelper::endAdminArea(); ?>
