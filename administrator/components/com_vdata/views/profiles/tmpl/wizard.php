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
$context = 'com_vdata.profiles.wizard.';

$keyword = $app->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
$component = $app->getUserStateFromRequest( $context.'component', 'component', '', 'string' );
$iotype = $app->getUserStateFromRequest( $context.'iotype', 'iotype', -1, 'int' );
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
<div class="adminform_box vdata_wizard_boxes">
<form action="index.php?option=com_vdata&view=profiles&layout=wizard" method="post" name="adminForm" id="adminForm">
<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>
		<legend><?php echo JText::_('PROFILE_WIZARD'); ?> <span class="icon-info" id="icon_desc_toggle"></span>
			<div class="sub_desc" id="desc_toggle" style="display:none;"><a class="close" href="javascript:void(0);">×</a><?php echo JText::_('PROFILE_WIZARD_DESC_TT'); ?></div>
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
				$opt = array(JHTML::_('select.option', '', JText::_('SELECT_COMPONENT')) );
				// $opt = array(JHTML::_('select.option', 'com_joomla', JText::_('SELECT_COMPONENT')) );
				
				// array_push($opt, JHTML::_('select.option', 'com_joomla', JText::_('JOOMLA_DEFAULT')));
				
				foreach($this->components as $cmpnt){
					array_push($opt, JHTML::_('select.option', $cmpnt->element, ($cmpnt->element=='com_joomla')?JText::_('JOOMLA_DEFAULT'):JText::_($cmpnt->element)));
				}
				
				echo JHTML::_('select.genericlist', $opt, 'component', "class='inputbox' size='1' onchange='document.adminForm.submit();'", 'value', 'text', $component , -1);
			?>
			</div>
			<div class="btn-group pull-right">
				<?php
				$opt = array(JHTML::_('select.option', -1, JText::_('SELECT_TYPE')), JHTML::_('select.option', 0, JText::_('IMPORT')), JHTML::_('select.option', 1, JText::_('EXPORT')) );
				
				echo JHTML::_('select.genericlist', $opt, 'iotype', "class='inputbox' size='1' onchange='document.adminForm.submit();'", 'value', 'text', $iotype , -1);
				?>
			</div>
		</div>
		<div class="clearfix"> </div>
	
	<div class="vdata_custom_create hasTip" title="<?php echo JText::_('VDATA_CREATE_CUSTOM_TASK');?>">
		<a href="<?php echo JRoute::_('index.php?option=com_vdata&view=profiles&task=edit&profile=custom');?>">
			<div class="panel-icon" >
				<img src="<?php echo JUri::root().'/media/com_vdata/images/icon-48-wizard.png';?>">
			</div>
			<div class="texticon" >
				<?php echo JText::_('VDATA_CUSTOM'); ?>
			</div>
			<div class="desc">
				<?php echo JText::_('VDATA_CREATE_CUSTOM_TASK');?>
			</div>
		</a>
	</div>
	<hr>
	
	<div class="cpanel-left vdata-cpanel-left">
		
		<div id="cpanel">
			<?php if($this->profiles){foreach($this->profiles as $key=>$profile){?>
				<div class="icon hasTip" title="<?php echo $profile->desc?>">
					<a href="<?php echo JRoute::_('index.php?option=com_vdata&view=profiles&task=edit&origin=remote&cid[]='.$profile->id);?>">
					<div class="panel-icon" >
						<img src="<?php echo JUri::root().'/media/com_vdata/images/icon-48-wizard.png';?>">
					</div>
						<div class="texticon" >
						<?php echo $profile->title; ?>
						</div>
						<div class="desc">
							<?php echo JHTML::_('string.truncate', ($profile->desc), 40);?>
						</div>
                    </a>
				</div>
			<?php }}?>
		</div>
	</div>
	<div class="v_pagination">
		<div class="pull-left"><?php echo $this->pagination->getListFooter(); ?></div>
		<?php if($this->total>$this->conf_limit){?><div class="pull-right"><?php echo $this->pagination->getLimitBox();?></div><?php }?>
	</div>
	
	
	</div>
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_vdata" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="profiles" />

<input type="hidden" name="params" value="" />


</form>
</div>
