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

JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');
/*echo '<pre>';
$ip = '162.251.80.27';
		//top -b -n 1 |grep init 
		//$temp = tempnam(sys_get_temp_dir(), 'php'); proc stat
$data_S = shell_exec("mysqladmin");print_r($data_S); 

//$data_S = shell_exec("top -b -n 1");print_r($data_S);


jexit('hj');*/
//free, ps 
//$conf = new JConfig(); print_r($conf); jexit(); 
$db = JFactory::getDbo();
$config = JFactory::getConfig();
 /*echo '<pre>';
echo $config->get( 'db' );
$info = $db->setQuery('select * from #__vd_widget_query')->loadObjectList();
print_r($info);
jexit(); */ 

$app = JFactory::getApplication();
$context = 'com_vdata.logs.list.';

$filter_type 	= $app->getUserStateFromRequest( $context.'filter_type', 'filter_type', -1, 'int' );
$filter_time 	= $app->getUserStateFromRequest( $context.'filter_time', 'filter_time', '', 'string' );
$filter_profile = $app->getUserStateFromRequest( $context.'filter_profile', 'filter_profile', '', 'string' );
$filter_loc 	= $app->getUserStateFromRequest( $context.'filter_loc', 'filter_loc', '', 'string' );
$filter_result 	= $app->getUserStateFromRequest( $context.'filter_result', 'filter_result', '', 'string' );

?>
<script type="text/javascript">
window.SqueezeBox.initialize({
         onOpen:function(){ 
		
jQuery("html, body").animate({scrollTop : 0}, "slow");
		 }
	});
	
$hd(function(){
	$hd('button.fclear').on('click', function(e){
		$hd('select[name="filter_type"]').val(-1);
		$hd('select[name="filter_profile"], select[name="filter_time"], select[name="filter_loc"], select[name="filter_result"]').val('');
		$hd('select').trigger('liszt:updated');
	});
	
	$hd('select').chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
	
	$hd("#icon_desc_toggle").mouseover(function(){
		$hd( "#desc_toggle" ).show( 'slide', 800);
	});	
	$hd( ".sub_desc .close" ).on( "click", function() {
		$hd( "#desc_toggle" ).hide( 'slide', 800);
	});	
})

</script>

<div class="adminform_box vdata_logs">
<form action="index.php?option=com_vdata&view=logs" method="post" name="adminForm" id="adminForm">
<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>
		<legend><?php echo JText::_('LOGS'); ?> <span class="icon-info" id="icon_desc_toggle"></span>
			<div class="sub_desc" id="desc_toggle" style="display:none;"><a class="close" href="javascript:void(0);">×</a><?php echo JText::_('LOGS_DESC_TT'); ?></div>
		</legend>
<div class="btn-toolbar logs_bar" id="filter-bar">

	<div class="btn-group pull-left">
	<?php
		
		echo '<div class="btn-wrapper"><button class="btn fclear btn-small">'.JText::_('CLEAR').'</button></div>';
		
		$iotypes = array(JHTML::_('select.option', -1, JText::_('SELECT_IOTYPE')), JHTML::_('select.option', 0, JText::_('IMPORT')), JHTML::_('select.option', 1, JText::_('EXPORT')) );
		
		echo JHTML::_('select.genericlist', $iotypes, 'filter_type', "class='inputbox' onchange='document.adminForm.submit();'", 'value', 'text', $filter_type , -1);
		
		array_unshift($this->profiles, array('id'=>'', 'title'=>JText::_('SELECT_PROFILE')));
		echo JHTML::_('select.genericlist', $this->profiles, 'filter_profile', "class='inputbox' onchange='document.adminForm.submit();'", 'id', 'title', $filter_profile , '');
		
		$time_opts = array(JHTML::_('select.option', '', JText::_('SELECT_TIME')), JHTML::_('select.option', 'today', JText::_('TODAY')),JHTML::_('select.option', 'yesterday', JText::_('YESTERDAY')), JHTML::_('select.option', 'week', JText::_('LAST_WEEK')), JHTML::_('select.option', 'month', JText::_('LAST_MONTH')), JHTML::_('select.option', 'year', JText::_('LAST_YEAR')) );
		
		echo JHTML::_('select.genericlist', $time_opts, 'filter_time', "class='inputbox' onchange='document.adminForm.submit();'", 'value', 'text', $filter_time , '');
		
		$loc_opt = array( JHTML::_('select.option', '', JText::_('SELECT_LOCATION')), JHTML::_('select.option', 'administrator', JText::_('ADMIN')), JHTML::_('select.option', 'site', JText::_('SITE')), JHTML::_('select.option', 'cron', JText::_('CRON')) );
		
		echo JHTML::_('select.genericlist', $loc_opt, 'filter_loc', "class='inputbox'  onchange='document.adminForm.submit();'", 'value', 'text', $filter_loc , '');
		
		$result_opt = array( JHTML::_('select.option', '', JText::_('SELECT_RESULT')), JHTML::_('select.option', 'success', JText::_('SUCCESS')), JHTML::_('select.option', 'error', JText::_('ERROR')), JHTML::_('select.option', 'abort', JText::_('ABORT')) );
		
		echo JHTML::_('select.genericlist', $result_opt, 'filter_result', "class='inputbox' onchange='document.adminForm.submit();'", 'value', 'text', $filter_result , '');
	?>
	<?php echo $this->pagination->getLimitBox();?>
	</div>

</div>
<div class="clearfix"> </div>

<div id="editcell">
<table class="adminlist table table-striped table-hover">
<thead>

		<tr>
			<th width="5" class="center">
				<?php echo JText::_( 'Num' ); ?>
			</th>
			<?php /*?><th width="20">
	        <input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
			</th><?php */?>
			<th >
                <?php echo JHTML::_('grid.sort', 'MESSAGE', 'i.message', $this->sortDirection, $this->sortColumn ); ?>
			</th>
			<th class="center">
				<?php echo JHTML::_('grid.sort', 'IOTYPE', 'i.iotype', $this->sortDirection, $this->sortColumn );?>
			</th>
			<th>
				<?php echo JHTML::_('grid.sort', 'PROFILE', 'p.title', $this->sortDirection, $this->sortColumn ); ?>
			</th>
			<th>
				<?php echo JHTML::_('grid.sort', 'SIDE', 'i.side', $this->sortDirection, $this->sortColumn ); ?>
			</th>
            <th >
                 <?php echo JHTML::_('grid.sort', 'Date', 'i.op_start', $this->sortDirection, $this->sortColumn ); ?>
			</th>
			<th class="center">
                <?php echo JHTML::_('grid.sort', 'Status', 'i.status', $this->sortDirection, $this->sortColumn ); ?>
			</th>
            <th width="32" class="center">
				<?php echo JHTML::_('grid.sort', 'ID', 'i.id', $this->sortDirection, $this->sortColumn ); ?>
            </th>
		</tr>
	</thead>
	<tfoot>
        <tr>
        <td colspan="8"><?php echo $this->pagination->getListFooter(); ?></td>
        </tr>
    </tfoot>
		<?php

	$k = 0;

	for ($i=0, $n=count( $this->logs ); $i < $n; $i++)	{
		$row = &$this->logs[$i];
		$checked 	= JHTML::_('grid.id',   $i, $row->id );
 
		
		$link 		= JRoute::_( 'index.php?option=com_vdata&view=logs&task=edit&cid[]='. $row->id );
		?>
		<tr class="<?php echo "row$k"; ?>">
		<td class="center"><?php echo $this->pagination->getRowOffset($i); ?></td>
			<?php /*?><td>
				<?php echo $checked; ?>
			</td><?php */?>
			
			<td>
				<a rel="{handler: \'iframe\', size: {x: \'90%\', y: \'90%\'}}" class="modal" href="<?php echo $link; ?>"><?php echo substr($row->message,0,50); ?></a>
			</td>
			<td class="center">
				<?php if($row->iotype==0){echo JText::_('IMPORT');} elseif($row->iotype==1){echo JText::_('EXPORT');}elseif($row->iotype==2){echo JText::_('FEED');} ?>
			</td>
			<td>
				<?php 
					$plink = JRoute::_( 'index.php?option=com_vdata&view=profiles&task=edit&cid[]='. $row->profileid );
					echo '<a href="'.$plink.'">'.$row->title.'</a>';
				?>
			</td>
			<td>
				<?php echo $row->side;?>
			</td>
			<td>
				<?php echo $row->op_start; ?>
			</td>
			<td class="center">
				<span class="<?php echo 'vdata_'.$row->status;?> vdata_btn"><?php echo $row->status;?></span>
			</td>
			<td class="center">
				<?php echo $row->id; ?>
			</td>
		</tr>
		
		<?php 
		
	$k = 1 - $k;

	}?>
	</table></div>
<div class="clr"></div></div>
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_vdata" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="id" value="" />
<input type="hidden" name="view" value="logs" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="filter_order" value="<?php echo $this->sortColumn; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->sortDirection; ?>" />
</form>
</div>
