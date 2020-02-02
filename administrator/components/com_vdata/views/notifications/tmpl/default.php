<?php
/*------------------------------------------------------------------------
# com_vdata - vData
# ------------------------------------------------------------------------
# author    Team WDMtech
# copyright Copyright (C) 2014 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support:  Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
$user = JFactory::getUser();

$app = JFactory::getApplication();
$context = 'com_vdata.notifications.list.';

$keyword = $app->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
$filter_type = $app->getUserStateFromRequest( $context.'filter_type', 'filter_type', -1, 'int' );
?>
<script type="text/javascript">
$hd(function(){
	jQuery('select').chosen({"disable_search_threshold":0,"search_contains": true, "allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
	jQuery("#icon_desc_toggle").mouseover(function(){
		jQuery( "#desc_toggle" ).show( 'slide', 800);
	});	
	jQuery( ".sub_desc .close" ).on( "click", function() {
		jQuery( "#desc_toggle" ).hide( 'slide', 800);
	});
})
</script>

<div class="adminform_box vdata_notifications">
<form action="index.php?option=com_vdata&view=notifications" method="post" name="adminForm" id="adminForm">
<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>
		<legend><?php echo JText::_('VDATA_NOTIFICATION'); ?> <span class="icon-info" id="icon_desc_toggle"></span>
			<div class="sub_desc" id="desc_toggle" style="display:none;"><a class="close" href="javascript:void(0);">×</a><?php echo JText::_('VDATA_NOTIFICATION_DESC_TT'); ?></div>
		</legend>
<div class="btn-toolbar" id="filter-bar">
	<div class="filter-search btn-group pull-left">
		<input type="text" id="filter_search" name="search" class="hasTip" title="<?php echo JText::_('SEARCH_TIP');?>" placeholder="<?php echo JText::_('SEARCH_PLACEHOLDER');?>" value="<?php if(!empty($keyword)) echo $keyword;?>"/>
	</div>
	<div class="btn-group">
		<button type="submit" class="btn hasTip" title="<?php echo JText::_('SEARCH');?>"><i class="icon-search"><span><?php echo JText::_('SEARCH');?></span></i></button>
		<button onclick="document.getElementById('filter_search').value='';this.form.submit();" class="btn hasTip" title="<?php echo JText::_('CLEAR');?>"><i class="icon-remove"><span><?php echo JText::_('CLEAR');?></span></i></button>
	</div>
	<div class="pull-right"><?php echo $this->pagination->getLimitBox();?></div>
</div>
 <div id="filter-bar">
	<b><?php echo JText::_('NOTIFICATION_CRON_URL')." : ";?></b>
	<?php echo "curl ".Juri::root()."index.php?option=com_vdata&task=get_notification";?>
 </div>
<div class="clearfix"> </div>
<div id="editcell">
    <table class="adminlist table">
    <thead>
			<tr>
				<th width="10" class="center">
					<?php echo JText::_( 'NUM' ); ?>
				</th>
				<th width="3%" class="center">
					<?php //echo JHTML::_('grid.checkall'); ?>
					<input type="checkbox" onclick="Joomla.checkAll(this)" title="<?php echo JText::_( 'CHECK_ALL' );?>" value="" name="checkall-toggle"><!---->
					<!--<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php //echo count( $this->items ); ?>);" />-->
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort', 'NOTIFICATION_TITLE', 'n.title', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
                
				<th width="100" nowrap="nowrap" class="center">
					<?php echo JHTML::_('grid.sort', 'STATE', 'n.state', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
				<th width="10" nowrap="nowrap" class="center">
					<?php echo JHTML::_('grid.sort', 'ID', 'n.id', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
			</tr>
		</thead>

	<tfoot>
    <tr>
      <td colspan="5"><?php echo $this->pagination->getListFooter(); ?></td>
    </tr>
  	</tfoot>

    <?php
    $k = 0;
	
	$lang = JFactory::getLanguage();
	
    for ($i=0, $n=count( $this->items ); $i < $n; $i++)
    {
        $row = $this->items[$i];
		$checked    = JHTML::_( 'grid.id', $i, $row->id );
		
		$canEdit	= $user->authorise('core.edit', 'com_vdata');
		$canEditOwn = $user->authorise('core.edit.own','com_vdata');
		$canEditState	= $user->authorise('core.edit.state', 'com_vdata');
        ?>
        <tr class="<?php echo "row$k"; ?>">
            <td class="center"><?php echo $this->pagination->getRowOffset($i); ?></td>
			
			<td class="center"><?php echo $checked; ?></td>

            <td>
			<?php if($canEdit || $canEditOwn){?>
			<a href="index.php?option=com_vdata&view=notifications&task=edit&cid[]=<?php echo $row->id; ?>"><?php	echo $row->title;?></a>
			<?php }else{echo $row->title;}?>
			</td>
			<td class="center publish_unpublish"><?php echo JHtml::_('jgrid.published', $row->state, $i, '', $canEditState, 'cb'); ?></td>
            <td class="center"><?php echo $row->id; ?></td>
        </tr>
        <?php
        $k = 1 - $k;
    }
    ?>
    </table>
</div></div>
 <?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_vdata" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="view" value="notifications" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />

</form>
</div>
