<?php 
/**
* @version: 2.2.0 (2013.12.03)
* @author: Vahrushev Konstantin
* @copyright: Copyright (C) 2012 crono.ru
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
* http://crono.ru
**/
defined('_JEXEC') or die('Restricted access'); 
/** @var vmCronoGrid_Grid */
$grid = $this->grid;
?>
<?php $this->loadAssetsFiles(false);?>
<script type="text/javascript">
    /* Переопределяем кнопки панели инструментов */
    jQuery('button.btn').removeAttr('onclick');
    jQuery('button.btn').click(function(){
        var index = jQuery(this).parent().index()+1;
        if(index==1){
            CRG.ProductAdd();
        }
        if(index==2){
            CRG.grid.jqGrid('saveCell',CRG.CurrentCell.iRow,CRG.CurrentCell.iCol);
        }
        if(index==3){
            CRG.grid.trigger("reloadGrid");
        }
        if(index==4){
            clone_id = CRG.grid.getGridParam('selarrrow')[0];
            clone_id?CRG.ProductAdd(clone_id):alert(<?php echo JText::_('CRONO_VMGRID_NO_PRODUCT_DELECTED')?>);
        }
        if(index==5){
            CRG.ProductDelete();
        }
        if(index==7){
            CRG.view.SaveColumnCfg();
            var view_id = parseInt(jQuery('#view_id').val());
            jQuery('#filter').submit();
        }
        if(index==6){
            var view_id = parseInt(jQuery('#view_id').val());
            var url = 'index.php?option=com_virtuemart&view=crono_grid&task=viewForm&view_id='+view_id;
            document.location = url;
        }
    });
</script>

<div style='padding:5px;'>
    <form id='filter' action="" method="get">
            <div>
                <label><?php echo JText::_('CRONO_VMGRID_VIEW')?>: </label>
                <select id="view_id" name="view_id">
                    <?php foreach($this->views as $id=>$view){?>
                    <option value="<?php echo $id?>" <?php echo $id==$this->view_id?"selected='1'":''?>><?php echo $view->name?></option>
                    <?php }?>
                </select>
            </div>
            <div>
                <?php echo $this->filter_form?>
            </div>
            <input type="hidden" name="option" value="com_virtuemart">
            <input type="hidden" name="view" value="crono_grid">
            <input type="hidden" name="task" value=""> 
            <input type="submit" value="Применить"> 
    </form>
    <div style="padding-right:30px; overflow: visible;">
        <!-- контейнер основной таблицы -->
        <table id="crg-list"></table>
        <!-- контейнер пагинации -->
        <div id="pager"></div>
    </div>

    <form name="adminForm" action="index.php" method="get">
        <input type="hidden" name="option" value="com_virtuemart">
        <input type="hidden" name="controller" value="crono_grid">
        <input type="hidden" name="task" value="">
    </form>
</div>
<?php //AdminUIHelper::endAdminArea(); ?>