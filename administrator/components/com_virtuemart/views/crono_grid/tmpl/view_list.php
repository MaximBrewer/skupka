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
<?php $this->loadAssetsFiles(true);?>
<script type="text/javascript">
    jQuery('#toolbar-delete a').removeAttr('onclick');
    jQuery('#toolbar-delete a').click(function(){
        var checked = jQuery('#editcell input:checkbox[checked]');
        if(checked.length){
            var views = CRG.view.GetCookie();
            jQuery.each(checked,function(){
                var id = jQuery(this).val();
                if(id){
                    views[id] = undefined;
                }
            });
            CRG.view.SetCookie(views);
            jQuery('#adminForm').submit();
        }
    });
</script>
<form action="index.php" method="get" name="adminForm" id="adminForm">
    <div id="editcell">
    <table class="adminlist" cellspacing="0" cellpadding="0">
        <thead>
        <tr>
            <th width="10">
                <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->views); ?>);" />
            </th>
            <th >
                <?php echo  JText::_('CRONO_VMGRID_VIEW') ; ?>
            </th> 
            <th >
                <?php echo JText::_('COM_VIRTUEMART_DESCRIPTION') ; ?>
            </th> 
        </tr>
        </thead>
        <?php $i=0;?>
        <?php foreach($this->views as $id=>$row) {?>
            <?php
            $i++;
            $checked  = JHtml::_('grid.id', $i, $id);
            $editlink = JRoute::_('index.php?option=com_virtuemart&view=crono_grid&task=viewForm&view_id=' . $id);
            ?>
            <tr class="row<?php echo $i%2; ?>">
                <td align="center">
                    <?php echo $checked; ?>
                </td>
                <td align="left">
                    <a href="<?php echo $editlink; ?>"><?php echo $row->name; ?></a>
                </td>
                <td align="left">
                    <?php echo $row->desc; ?>
                </td>
            </tr>
        <?php }?>
    </table>
    </div>

    <input type="hidden" name="option" value="com_virtuemart" />
    <input type="hidden" name="controller" value="crono_grid" />
    <input type="hidden" name="task" value="viewList" />
    <input type="hidden" name="boxchecked" value="0" />
    <?php echo JHTML::_( 'form.token' ); ?>
</form>
<?php AdminUIHelper::endAdminArea(); ?>