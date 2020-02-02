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
$context = 'com_vdata.profiles.list.';

$keyword = $app->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
$filter_type = $app->getUserStateFromRequest( $context.'filter_type', 'filter_type', -1, 'int' );

?>
<script type="text/javascript">
Joomla.submitbutton = function(task) {
	if (task == 'saveAsCopy') {
		
		if (document.adminForm.boxchecked.value==0){
			alert('<?php echo JText::_('COM_VDATA_NO_PROFILE_SELECTED');?>');
			return false;
		}
		// Joomla.submitbutton('saveAsCopy');
		Joomla.submitform(task, document.getElementById('adminForm'));
	} 
	else {
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
}

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

<div class="adminform_box vdata_profiles">
<form action="index.php?option=com_vdata&view=profiles" method="post" name="adminForm" id="adminForm">
<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>
		<legend><?php echo JText::_('PROFILE'); ?> <span class="icon-info" id="icon_desc_toggle"></span>
			<div class="sub_desc" id="desc_toggle" style="display:none;"><a class="close" href="javascript:void(0);">×</a><?php echo JText::_('PROFILE_DESC_TT'); ?></div>
		</legend>
<div class="btn-toolbar" id="filter-bar">
	<div class="filter-search btn-group pull-left">
		<input type="text" id="filter_search" name="search" class="hasTip" title="<?php echo JText::_('SEARCH_TIP');?>" placeholder="<?php echo JText::_('SEARCH_PLACEHOLDER');?>" value="<?php if(!empty($keyword)) echo $keyword;?>"/>
	</div>
	<div class="btn-group">
		<button type="submit" class="btn hasTip" title="<?php echo JText::_('SEARCH');?>"><i class="icon-search"><span><?php echo JText::_('SEARCH');?></span></i></button>
		<button onclick="document.getElementById('filter_search').value='';this.form.submit();" class="btn hasTip" title="<?php echo JText::_('CLEAR');?>"><i class="icon-remove"><span><?php echo JText::_('CLEAR');?></span></i></button>
	</div>
	<div class="btn-group pull-right">
		<?php
		$opt = array(JHTML::_('select.option', -1, JText::_('SELECT_TYPE')), JHTML::_('select.option', 0, JText::_('IMPORT')), JHTML::_('select.option', 1, JText::_('EXPORT')) );
		
		echo JHTML::_('select.genericlist', $opt, 'filter_type', "class='inputbox' size='1' onchange='document.adminForm.submit();'", 'value', 'text', $filter_type , -1);
		?>
		<?php echo $this->pagination->getLimitBox();?>
	</div>
</div>
<div class="clearfix"> </div>
<div id="editcell">
    <table class="adminlist table">
    <thead>
			<tr>
				<th width="10" class="center">
					<?php echo JText::_( 'NUM' ); ?>
				</th>
				<th width="3%">
					<?php //echo JHTML::_('grid.checkall'); ?>
					<input type="checkbox" onclick="Joomla.checkAll(this)" title="<?php echo JText::_( 'CHECK_ALL' );?>" value="" name="checkall-toggle"><!---->
					<!--<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php //echo count( $this->items ); ?>);" />-->
				</th>
				<th class="title">
					<?php echo JText::_('PROFILE');//JHTML::_('grid.sort', 'PROFILE', 'i.title', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
                <?php /*?><th class="title" width="200">
					<?php echo JHTML::_('grid.sort', 'PLUGIN', 'e.name', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th><?php */?>
				<?php if($this->config->logging){?>
				<th class="title center" width="200">
					<?php echo JText::_('VDATA_LOGS');//JHTML::_('grid.sort', 'VDATA_LOGS', 'e.logs', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
				<?php }?>
				
                <th width="100" nowrap="nowrap" class="center">
					<?php echo JText::_('ACTIONS'); ?>
				</th>
				<th width="10" nowrap="nowrap">
					<?php echo JText::_('ID');//JHTML::_('grid.sort', 'ID', 'i.id', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
			</tr>
		</thead>

	<tfoot>
    <tr>
      <td colspan="6"><?php echo $this->pagination->getListFooter(); ?></td>
    </tr>
  	</tfoot>

    <?php
    $k = 0;
	
	$lang = JFactory::getLanguage();
	
    for ($i=0, $n=count( $this->items ); $i < $n; $i++)
    {
        $row = $this->items[$i];
		$checked    = JHTML::_( 'grid.id', $i, $row->id );
		
		$lang->load($this->items[$i]->extension . '.sys', JPATH_SITE.'/plugins/hexdata/'.$this->items[$i]->element, null, false, false);
		
		$canEdit	= $user->authorise('core.edit', 'com_vdata');
		$canEditOwn = $user->authorise('core.edit.own','com_vdata');
		$canEditState	= $user->authorise('core.edit.state', 'com_vdata');
        ?>
        <tr class="<?php echo "row$k"; ?>">
            <td><?php echo $this->pagination->getRowOffset($i); ?></td>
			
			<td class="center"><?php echo $checked; ?></td>

            <td>
			<?php if($canEdit || $canEditOwn){?>
			<a href="index.php?option=com_vdata&view=profiles&task=edit&cid[]=<?php echo $row->id; ?>"><?php	echo $row->title;?></a>
			<?php }else{echo $row->title;}?>
			</td>
			
			<?php /*?><td align="center"><?php echo JText::_($row->plugin); ?></td><?php */?>
			<?php if($this->config->logging){?>
				<td class="center"><a href="<?php echo JRoute::_('index.php?option=com_vdata&view=logs&filter_profile=').$row->id; ?>"><?php echo $row->logs;?></a></td>
            <?php }?>
			
			<?php if($row->iotype) : ?>
            <td  class="center"><a class="btn btn-small btn-export" href="index.php?option=com_vdata&view=export&profileid=<?php echo $row->id; ?>"><?php echo JText::_('EXPORT'); ?></a></td>
            <?php else : ?>
            <td class="center"><a class="btn btn-small btn-import" href="index.php?option=com_vdata&view=import&profileid=<?php echo $row->id; ?>"><?php echo JText::_('IMPORT'); ?></a></td>
            <?php endif; ?>
            <td><?php echo $row->id; ?></td>
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
<input type="hidden" name="view" value="profiles" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />

</form>
</div>
