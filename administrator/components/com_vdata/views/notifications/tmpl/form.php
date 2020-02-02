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

$lang = JFactory::getLanguage();

?>

<script type="text/javascript">
	
	Joomla.submitbutton = function(task) {
		if (task == 'cancel') {
			Joomla.submitform(task, document.getElementById('adminForm'));
		} 
		else {
			
			var form = document.adminForm;
		
			/* if(form.title.value == "")	{
				alert("<?php echo JText::_('PLZ_ENTER_TITLE'); ?>");
				return false;
			} */
			
			Joomla.submitform(task, document.getElementById('adminForm'));
			
		}
	}
	
	$hd(function(){
		$hd('select').chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
		
		$hd(document).on('change', 'select[name="params[custom][table]"]', function(e){
			var table = $hd(this).val();
			if(table==''){
				$hd('select[name="params[custom][orderby]"], select[name="params[custom][columns][]"]').html('<option value=""><?php echo JText::_('SELECT_COLUMN');?></option>').trigger('liszt:updated');
				
			}
			else{
				$hd.ajax({
					url:'index.php',
					type:'POST',
					dataType:'json',
					data:{'option':'com_vdata', 'view':'notifications', 'task':'getTableColumns',  'component':'com_vdata', 'table':table, "<?php echo JSession::getFormToken(); ?>":1},
					 beforeSend: function()	{
						$hd(".loading").show();
					},
					complete: function()	{
						$hd(".loading").hide();
					},
					success: function(res){
						
						if(res.result = "success") {
							$hd('select[name="params[custom][orderby]"], select[name="params[custom][columns][]"]').html(res.html).trigger('liszt:updated');
							$hd('select[name="params[filters][column][]"], select[name="params[filters][column][]"]').html(res.html).trigger('liszt:updated');
						}
						else{
							alert(res.error);
						}
					},
					error: function(jqXHR, textStatus, errorThrown)	{
						alert(textStatus);				  
					}
				});
			}
			$hd('div#filter_condition').html('');
		});
		
		$hd(document).on('click', 'label.notify_column', function(event){
			if($hd(this).attr('for')=='column_y'){
				var table = $hd('select[name="params[custom][table]"]').val();
				var query = $hd('textarea#condition_query').val();
				var columns = $hd('select[name="params[custom][columns][]"]').val() || [];
				
				if(table=='' && query==''){
					alert('<?php echo JText::_('SELECT_TABLE_OR_WRITE_QUERY');?>');
					$hd('div#recipient_column_value').html('');
					
					return false;
				}
				
				$hd.ajax({
					url:'index.php',
					type:'POST',
					dataType:'json',
					data:{'option':'com_vdata', 'view':'notifications', 'task':'getNotificationColumn',  'component':'com_vdata', 'query':query, 'table':table,'table_columns':columns, "<?php echo JSession::getFormToken(); ?>":1},
					 beforeSend: function()	{
						$hd(".loading").show();
					},
					complete: function()	{
						$hd(".loading").hide();
					},
					success: function(res){
						
						if(res.result = "success") {
							$hd('div#recipient_column_value').html('<select name="notification_tmpl[recipient][column][value]">'+res.html+'</select>');
							$hd('select[name="notification_tmpl[recipient][column][value]"]').chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
						}
						else{
							alert(res.error);
						}
					},
					error: function(jqXHR, textStatus, errorThrown)	{
						alert(textStatus);				  
					}
				});
			}
			else{
				$hd('div#recipient_column_value').html('');
			}
			
		});
		
		
		$hd(document).off('click.add_filter').on('click.add_filter', '.add_filter', function(event){
			
			if($hd('select[name="params[custom][table]"]').val()==''){
				alert('<?php echo JText::_('SELECT_TABLE'); ?>');
				return;
			}
			var html = '<div class="vdata_filter_block">';
			
			html += '<span class="filter_block"><select name="params[filters][column][]">';
			
			html += $hd($hd('select[name="params[custom][orderby]"]').clone().find(':selected').removeAttr("selected").end()).html();
			
			html += '</select></span>';
			
			html += '<span class="op_block"><select name="params[filters][cond][]" class="oplist" ><option value="=">=</option><option value="<>">!=</option><option value="<">&lt;</option><option value=">">&gt;</option><option value="in">IN</option><option value="notin">NOT IN</option><option value="between">BETWEEN</option><option value="notbetween">NOT BETWEEN</option><option value="like">LIKE</option><option value="notlike">NOT LIKE</option><option value="regexp">REGEXP</option></select></span>';
			
			html += '<span class="value_block"><input id="paramsfiltersvalue" class="inputbox filterval" type="text" size="50" value="" name="params[filters][value][]"></span>';			
			html += '<div class="remove_filter btn"><span class="icon-cancel-circle"></span></div>';
			//html += ' <div class="remove_filter btn btn-success"><?php echo JText::_('REMOVE'); ?></div>';
			html += '</div>';
			
			$hd('div#additional').before(html);
			
			$hd('select').chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"}); 
		
		});
		
		$hd(document).off('click.remove_filter').on('click.remove_filter', '.remove_filter', function(event){
			$hd(this).parent().remove();
		});
		
		//filter combo box
		var options = {
			source: ["@vdSql:NOW()", "@vdSql:CURDATE()", "@vdSql:DATE_SUB(NOW(), INTERVAL 10 DAY)", "@vdPhp:date(\"Y-m-d H:i:s\")"],
			minLength: 0
		};
		$hd(document).on('focus', '.filterval', function(){
			 $hd(this).autocomplete(options).focus(function(){
				$hd(this).autocomplete("search",$hd(this).val());
			});
		});
		
		var extra_condition = {
			source: ["DAYNAME(now())='sunday'", "CURDATE()=LAST_DAY(now())"],
			minLength: 0
		};
		$hd(document).on('focus', '#extra_condition', function(){
			 $hd(this).autocomplete(extra_condition).focus(function(){
				$hd(this).autocomplete("search",$hd(this).val());
			});
		});
		
	});
	
	

</script>

<div id="vdatapanel">

<form action="index.php?option=com_vdata&view=notifications" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<div class="col100">
<fieldset class="adminform">
		<?php $pf = ($this->item->id>0) ? JText::_('EDIT_NOTIFICATION') : JText::_('NEW_NOTIFICATION'); ?>
		<legend><?php echo JText::_('NOTIFICATION').' : '.$pf; ?></legend>
	
	<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'settings')); ?>
	
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'settings', JText::_('COM_VDATA_NOTIFICATION_GENERAL', true)); ?>
		<table class="adminform table table-striped">
		<tr>
			<td width="200"><label class="required hasTip" title="<?php echo JText::_('NOTIFICATION_TITLE_DESC');?>"><?php echo JText::_('TITLE'); ?></label></td>
			<td>
			<input type="text" name="title" id="title" class="inputbox required" value="<?php echo $this->item->title; ?>" size="50" />
			</td>
		</tr>
		<tr>
			<td width="200">
				<label class="required hasTip" title="<?php echo JText::_('NOTIFICATION_CONDITION_QUERY_DESC');?>"><?php echo JText::_('NOTIFICATION_CONDITION_QUERY'); ?></label>
			</td>
			<td>
				<div class="vdata_notification_condition">
				<textarea id="condition_query" name="params[query]" cols="10" rows="5" ><?php if(isset($this->item->params->query)){echo $this->item->params->query;}?></textarea>
				</div>
				<div class="vdata_noti_or"><?php echo JText::_('OR');?></div>
				<div class="vdata_select_table">
					<select name="params[custom][table]">
						<option value=""><?php echo JText::_('SELECT_TABLE');?></option>
						<?php foreach($this->tables as $table){?>
							<option value="<?php echo $table;?>" <?php if(isset($this->item->params->custom->table) && ($this->item->params->custom->table==$table)){echo 'selected="selected"';}?>><?php echo $table;?></option>
						<?php }?>
					</select>
					<span><?php echo JText::_('SELECT_COLUMNS');?></span>
					<select name="params[custom][columns][]" multiple="multiple">
						<option value=""><?php echo JText::_('SELECT_COLUMN');?></option>
						<?php foreach($this->tableColumns as $col){?>
							<option value="<?php echo $col->Field;?>" <?php if(in_array($col->Field, $this->item->params->custom->columns)){echo ' selected="selected"';}?>><?php echo $col->Field;?></option>
						<?php }?>
					</select>
					
					<div id="add">
						<select name="params[custom][clause]">
							<option value="and" <?php if(isset($this->item->params->custom->clause) && ($this->item->params->custom->clause=='and')){echo 'selected="selected"';}?>><?php echo JText::_('AND');?></option>
							<option value="or" <?php if(isset($this->item->params->custom->clause) && ($this->item->params->custom->clause=='or')){echo 'selected="selected"';}?>><?php echo JText::_('OR');?></option>
						</select>
						<div class="btn add_filter btn-success"><span class="icon-plus"></span></div>
					</div>
					<div id="filter_condition">
						<?php if(isset($this->item->params->filters->column)){
							foreach($this->item->params->filters->column as $idx=>$column){
						?>
							<div class="vdata_filter_block">
								<span class="filter_block">
									<select name="params[filters][column][]">
										<?php foreach($this->tableColumns as $col){?>
										<option value="<?php echo $col->Field;?>" <?php if($col->Field==$this->item->params->filters->column[$idx]){echo ' selected="selected"';}?>><?php echo $col->Field;?></option>
										<?php }?>
									</select>
								</span>
								<span class="op_block">
									<select class="oplist" name="params[filters][cond][]">
										<option value="=" <?php if($this->item->params->filters->cond[$idx]=="="){echo ' selected="selected"';}?>><?php echo JText::_("=");?></option>
										<option value="<>" <?php if($this->item->params->filters->cond[$idx]=="<>"){echo ' selected="selected"';}?>><?php echo JText::_("!=");?></option>
										<option value="<" <?php if($this->item->params->filters->cond[$idx]=="<"){echo ' selected="selected"';}?>><?php echo JText::_("<");?></option>
										<option value=">" <?php if($this->item->params->filters->cond[$idx]==">"){echo ' selected="selected"';}?>><?php echo JText::_(">");?></option>
										<option value="in" <?php if($this->item->params->filters->cond[$idx]=="in"){echo ' selected="selected"';}?>><?php echo JText::_("IN");?></option>
										<option value="notin" <?php if($this->item->params->filters->cond[$idx]=="notin"){echo ' selected="selected"';}?>><?php echo JText::_("NOT IN");?></option>
										<option value="between" <?php if($this->item->params->filters->cond[$idx]=="between"){echo ' selected="selected"';}?>><?php echo JText::_("BETWEEN");?></option>
										<option value="notbetween" <?php if($this->item->params->filters->cond[$idx]=="notbetween"){echo ' selected="selected"';}?>><?php echo JText::_("NOT BETWEEN");?></option>
										<option value="like" <?php if($this->item->params->filters->cond[$idx]=="like"){echo ' selected="selected"';}?>><?php echo JText::_("LIKE");?></option>
										<option value="notlike" <?php if($this->item->params->filters->cond[$idx]=="notlike"){echo ' selected="selected"';}?>><?php echo JText::_("NOT LIKE");?></option>
										<option value="regexp" <?php if($this->item->params->filters->cond[$idx]=="regexp"){echo ' selected="selected"';}?>><?php echo JText::_("REGEXP");?></option>
									</select>
								</span>
								<span class="value_block">
									<input id="paramsfiltersvalue" class="inputbox filterval" type="text" name="params[filters][value][]" value="<?php echo $this->item->params->filters->value[$idx]?>" size="50">
								</span>
								<span class="remove_filter btn">
									<span class="icon-cancel-circle"></span>
								</span>
							</div>
						<?php }}?>
					</div>
					<div id="additional">
					<?php echo JText::_('ADDITIONAL_CONDITION');?>
						<input id="extra_condition" type="text" name="params[filters][additional]" value="<?php if(isset($this->item->params->filters->additional)){echo $this->item->params->filters->additional;}?>" />
					</div>
					<div id="orderby">
						<div class="vdata_select_column">
							<?php echo JText::_('ORDER_BY');?>
							<select name="params[custom][orderby]">
								<option value=""><?php echo JText::_('SELECT_COLUMN');?></option>
								<?php foreach($this->tableColumns as $col){?>
								<option value="<?php echo $col->Field;?>" <?php if($col->Field==$this->item->params->custom->orderby){echo ' selected="selected"';}?>><?php echo $col->Field;?></option>
								<?php }?>
							</select>
							<select name="params[custom][orderdir]">
								<option value="asc" <?php if(isset($this->item->params->custom->orderdir) && ($this->item->params->custom->orderdir=='asc')){echo 'selected="selected"';}?>><?php echo JText::_('ASC');?></option>
								<option value="desc" <?php if(isset($this->item->params->custom->orderdir) && ($this->item->params->custom->orderdir=='desc')){echo 'selected="selected"';}?>><?php echo JText::_('DESC');?></option>
							</select>
						</div>
					</div>
				</div>
			</td>
		</tr>
		<?php /*?><tr id="feed_access">
			<td width="200"><label class="hasTip required" title="<?php echo JText::_('ACCESS_DESC');?>"><?php echo JText::_('ACCESS'); ?></label></td>
			<td>
				<?php echo JHtmlAccess::level('access', $this->item->access, '', false);?>
			</td>
		</tr><?php */?>
		<tr>
			<td width="200"><label class="hasTip required" title="<?php echo JText::_('STATUS_DESC');?>"><?php echo JText::_('STATUS');?></label></td>
			<td>
				<select name="state">
					<option value="0" <?php if($this->item->state==0) echo 'selected="selected"';?>><?php echo JText::_('DISABLE');?></option>
					<option value="1" <?php if($this->item->state==1) echo 'selected="selected"';?>><?php echo JText::_('ENABLE');?></option>
				</select>
			</td>
		</tr>
		</table>
	
	<?php echo JHtml::_('bootstrap.endTab'); ?>
	<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'notification', JText::_('COM_VDATA_NOTIFICATION_TMPL', true)); ?>
		<table class="adminform table table-striped">
			<tr>
			<td width="200"><label class="required hasTip" title="<?php echo JText::_('NOTIFICATION_SUBJECT_DESC');?>"><?php echo JText::_('NOTIFICATION_SUBJECT'); ?></label></td>
			<td>
				<input type="text" name="notification_tmpl[subject]" id="title" class="inputbox required" value="<?php if(isset($this->item->notification_tmpl->subject)){echo $this->item->notification_tmpl->subject;} ?>" size="50" />
			</td>
			</tr>
			<tr>
				<td>
				<label class="required hasTip" title="<?php echo JText::_('NOTIFICATION_RECIPIENTS_DESC');?>"><?php echo JText::_('NOTIFICATION_RECIPIENTS'); ?></label>
				</td>
				<td>
					<table>
						<tr>
							<td>
							<span class="hasTip" title="<?php echo JText::_('NOTIFICATION_RECIPIENTS_ADMIN_DESC');?>"><?php echo JText::_('NOTIFICATION_RECIPIENTS_ADMIN');?></span>
							</td>
							<td>
								<select name="notification_tmpl[recipient][group]">
									<option value=""><?php echo JText::_('SELECT_USER_GROUP');?></option>
								<?php
									$selected = isset($this->item->notification_tmpl->recipient->group)?$this->item->notification_tmpl->recipient->group:'';
									foreach($this->usergroups as $group){
								?>
									<option value="<?php echo $group->value;?>" <?php if($group->value==$selected){echo ' selected="selected"';}?>><?php echo $group->text;?></option>
								<?php
									}
								?>
								</select>
							</td>
						</tr>
						<tr>
							<td>
							<span class="hasTip" title="<?php echo JText::_('NOTIFICATION_RECIPIENTS_SENDMAIL_DESC');?>"><?php echo JText::_('NOTIFICATION_RECIPIENTS_SENDMAIL');?></span>
							</td>
							<td>
							<div class="radio btn-group">
								<label for="sendmail_n" class="radio"><?php echo JText::_( 'COM_VDATA_OPTION_DISABLE' ); ?></label>
								<input type="radio" class="btn" id="sendmail_n" name="notification_tmpl[recipient][sendmail]" value="0" <?php if( (isset($this->item->notification_tmpl->recipient->sendmail) && ($this->item->notification_tmpl->recipient->sendmail==0)) || !isset($this->item->notification_tmpl->recipient->sendmail) ){echo ' checked="checked"';}?>/>
								<label for="sendmail_y" class="radio"><?php echo JText::_( 'COM_DATA_OPTION_DISABLE' ); ?></label>
								<input type="radio" class="btn" id="sendmail_y" name="notification_tmpl[recipient][sendmail]" value="1" <?php if( isset($this->item->notification_tmpl->recipient->sendmail) && ($this->item->notification_tmpl->recipient->sendmail==1) ){echo ' checked="checked"';}?>/>
							</div>
							</td>
						</tr>
						<tr>
							<td>
							<span class="hasTip" title="<?php echo JText::_('NOTIFICATION_RECIPIENTS_CUSTOM_DESC');?>"><?php echo JText::_('NOTIFICATION_RECIPIENTS_CUSTOM');?></span>
							</td>
							<td>
							<input type="text" name="notification_tmpl[recipient][custom]" value="<?php if(isset($this->item->notification_tmpl->recipient->custom)){echo $this->item->notification_tmpl->recipient->custom;}?>" placeholder="<?php echo JText::_('NOTIFICATION_RECIPIENTS_CUSTOM_PLACEHOLDER');?>"/>
							</td>
						</tr>
						<tr>
							<td>
							<span class="hasTip" title="<?php echo JText::_('NOTIFICATION_RECIPIENTS_TABLE_COLUMN_DESC');?>"><?php echo JText::_('NOTIFICATION_RECIPIENTS_TABLE_COLUMN');?></span>
							</td>
							<td>
							<div class="radio btn-group">
								<label for="column_n" class="radio notify_column"><?php echo JText::_( 'COM_VDATA_OPTION_DISABLE' ); ?></label>
								<input type="radio" class="btn" id="column_n" name="notification_tmpl[recipient][column]" value="0" <?php if( (isset($this->item->notification_tmpl->recipient->column) && ($this->item->notification_tmpl->recipient->column==0)) || !isset($this->item->notification_tmpl->recipient->column) ){echo ' checked="checked"';}?>/>
								<label for="column_y" class="radio notify_column"><?php echo JText::_( 'COM_DATA_OPTION_DISABLE' ); ?></label>
								<input type="radio" class="btn" id="column_y" name="notification_tmpl[recipient][column]" value="1" <?php if( isset($this->item->notification_tmpl->recipient->column) && ($this->item->notification_tmpl->recipient->column==1) ){echo ' checked="checked"';}?>/>
							</div>
							<div id="recipient_column_value">
								<?php if(isset($this->item->notification_tmpl->recipient->column->value)){?>
									<select name="notification_tmpl[recipient][column][value]">
										<?php echo $this->notificationColumns;?>
									</select>
								<?php }?>
							</div>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
			<td width="200"><label class="required hasTip" title="<?php echo JText::_('NOTIFICATION_TMPL_DESC');?>"><?php echo JText::_('NOTIFICATION_TMPL'); ?></label></td>
			<td>
			<?php jimport( 'joomla.html.editor' );
				$config = JFactory::getConfig();
				$userEditor = JFactory::getUser()->getParam("editor");
				$editorType = !empty($userEditor)?$userEditor:$config->get( 'editor' );
				$editor = JEditor::getInstance($editorType);
				$editor = JEditor::getInstance($editorType);
				$tmpl = isset($this->item->notification_tmpl->tmpl)?$this->item->notification_tmpl->tmpl:'';
				echo $editor->display('notification_tmpl[tmpl]', $tmpl, '550', '400', '60', '20', false);
			?>
			</td>
			</tr>
		</table>
	<?php echo JHtml::_('bootstrap.endTab'); ?>
		
	<?php echo JHtml::_('bootstrap.endTabSet'); ?>
	

</fieldset>
</div>
<div class="clr"></div>
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_vdata" />
<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="notifications" />
</form>

</div>