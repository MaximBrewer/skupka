<?php
/**
 * @package 	customfilters
 * @author		Sakis Terz
 * @link		http://breakdesigns.net
 * @copyright	Copyright (c) 2012-2018 breakdesigns.net. All rights reserved.
 * @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *				customfilters is free software. This version may have been modified
 *				pursuant to the GNU General Public License, and as distributed
 *				it includes or is derivative of works licensed under the GNU
 *				General Public License or other free or open source software
 *				licenses.
 * @since		1.0
 */

// no direct access
defined('_JEXEC') or die;
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('behavior.modal');


//advanced settings
$display_types_advanced=array(5,6,'5,6');
$script='var display_types_advanced=new Array("5","6","5,6","8");';
$this->document->addScriptDeclaration($script);

$model=$this->getModel();

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$saveOrder	= $listOrder == 'ordering';
$published_opt=array(array('value'=>1 ,'text' => JText::_('Published')),array('value'=>0 ,'text' => JText::_('Unpublished')));
$boolean_options=array(JHTML::_('select.option',1,JText::_('JYES')),JHTML::_('select.option',0,JText::_('JNO')));
?>

<?php if (version_compare(JVERSION, '3.5.0', 'lt')): ?>
<div class="alert alert-info">
<?php echo JText::_('COM_CUSTOMFILTERS_UPDATE_JOOMLA_VERSION'); ?>
</div>
<?php endif; ?>

<?php if($this->needsdlid):?>
<div class="alert">
    <?php echo JText::sprintf('COM_CUSTOMFILTERS_NEEDS_DLD','https://breakdesigns.net/custom-filters-manual-pro/49-using-the-live-update-49'); ?>
</div>
<?php endif; ?>

<form action="<?php echo JRoute::_('index.php?option=com_customfilters&view=customfilters'); ?>"
	method="post" name="adminForm" id="adminForm">
	<div id="totals">
	<?php echo $this->pagination->getResultsCounter() ;?>
	</div>
	<br clear="all" />
	<div id="j-main-container">
		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible cfhide"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?>
				</label> <input type="text" name="filter_search" id="filter_search"
					placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>"
					value="<?php echo $this->escape($this->state->get('filter.search')); ?>" />
			</div>

			<div class="btn-group pull-left">
				<button type="submit" class="btn hasTooltip">
				    <i class="icon-search"></i>
				</button>
				<button type="button" class="btn hasTooltip"
					onclick="document.id('filter_search').value='';this.form.submit();">
					   <i class="icon-remove"></i>
				</button>
			</div>


			<div class="btn-group pull-right hidden-phone">
				<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?>
				</label>
				<?php echo $this->pagination->getLimitBox(); ?>
			</div>


			<div class="filter-select fltrt btn-group pull-right">
				<select name="filter_published" class="inputbox"
					onchange="this.form.submit()">
					<option value="">
					<?php echo JText::_('JOPTION_SELECT_PUBLISHED');?>
					</option>
					<?php echo JHtml::_('select.options', $published_opt, 'value', 'text', $this->state->get('filter.published'), true);?>
				</select>
			</div>
			<div class="filter-select fltrt btn-group pull-right">
				<select name="filter_type_id" class="inputbox"
					onchange="this.form.submit()">
					<option value="">
					<?php echo JText::_('COM_CUSTOMFILTERS_SELECT_DISPLAY_TYPE');?>
					</option>
					<?php echo JHtml::_('select.options', $this->displayTypes, 'value', 'text', $this->state->get('filter.type_id'), true);?>
				</select>
			</div>
		</div>
		<div class="clr"></div>

		<table class="adminlist table table-striped">
			<thead>
				<tr>
					<th width="1%"><input type="checkbox" name="checkall-toggle"
						value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>"
						onclick="Joomla.checkAll(this)" />
					</th>

					<th><?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'custom_title', $listDirn, $listOrder); ?>
					</th>

					<th width="1%" style="min-width: 55px" class="nowrap center"><?php echo JHtml::_('grid.sort', 'JSTATUS', 'published', $listDirn, $listOrder); ?>
					</th>

					<th width="255"><?php echo JHtml::_('grid.sort', 'JFIELD_ALIAS_LABEL', 'alias', $listDirn, $listOrder); ?>
					</th>

					<th><?php echo JText::_('CUSTOM_FIELD_DESCRIPTION'); ?>
					</th>

					<th><?php echo JHtml::_('grid.sort', 'CUSTOM_FIELD_TYPE', 'field_type', $listDirn, $listOrder); ?>
					</th>

					<th width="8%"><?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ORDERING', 'ordering', $listDirn, $listOrder); ?>
					<?php if ($saveOrder) :?> <?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'customfilters.saveorder'); ?>
					<?php endif; ?>
					</th>
					<th><?php echo JHtml::_('grid.sort', 'COM_CUSTOMFILTERS_DISPLAY_TYPE', 'type_id', $listDirn, $listOrder); ?>
					</th>

					<th><?php echo JHtml::_('grid.sort', 'COM_CUSTOMFILTERS_SMART_SEARCH', 'smart_search', $listDirn, $listOrder); ?>
					</th>
					<th><?php echo JHtml::_('grid.sort', 'COM_CUSTOMFILTERS_EXPANDED', 'expanded', $listDirn, $listOrder); ?>
					</th>
					<th><?php echo JHtml::_('grid.sort', 'COM_CUSTOMFILTERS_SCROLLBARAFTER', 'scrollbar_after', $listDirn, $listOrder); ?>
					</th>
					<th><?php echo JHtml::_('grid.sort', 'COM_CUSTOMFILTERS_CUSTOM_ID', 'custom_id', $listDirn, $listOrder); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="15"><?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
			<?php
			foreach($this->items as $i => $item){
				$displayTypes=$model->getDisplayTypes($item->data_type);?>
				<tr class="row<?php echo $i % 2; ?>">
					<td class="center"><?php echo JHtml::_('grid.id', $i, $item->id); ?>
					</td>

					<td class="left"><?php echo $item->custom_title ?>
					</td>

					<td class="center"><?php echo JHtml::_('jgrid.published', $item->published, $i,'customfilters.'); ?>
					</td>

					<td class="center"><input type="text"
						name="alias[<?php echo $item->id?>]" class="cf_alias_input"
						id="cf_alias_<?php echo $item->id?>" disabled="disabled"
						class="inputbox" size="45" value="<?php echo $item->alias ?>" /> <?php echo JHtml::link('javascript:void(0)','&nbsp;',array('class'=>'cf_edit_btn','onclick'=>"document.id('cf_alias_".$item->id."').disabled=''")) ?>
					</td>
					<td class="left"><?php echo $item->custom_descr ?>
					</td>

					<td class="left"><?php echo $item->field_type_string ?>
					</td>

					<td class="order"><?php if ($saveOrder) :?> <?php if ($listDirn == 'asc') : ?>
						<span><?php echo $this->pagination->orderUpIcon($i, ($item->ordering > @$this->items[$i-1]->ordering), 'customfilters.orderup', 'JLIB_HTML_MOVE_UP', $saveOrder); ?>
					</span> <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, ($item->ordering < @$this->items[$i+1]->ordering), 'customfilters.orderdown', 'JLIB_HTML_MOVE_DOWN', $saveOrder); ?>
					</span> <?php elseif ($listDirn == 'desc') : ?> <span><?php echo $this->pagination->orderUpIcon($i, ($item->ordering < @$this->items[$i-1]->ordering), 'customfilters.orderdown', 'JLIB_HTML_MOVE_UP', $saveOrder); ?>
					</span> <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, ($item->ordering > @$this->items[$i+1]->ordering), 'customfilters.orderup', 'JLIB_HTML_MOVE_DOWN', $saveOrder); ?>
					</span> <?php endif; ?> <?php endif; ?> <?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
						<input type="text" name="order[]" size="5"
						value="<?php echo $item->ordering;?>" <?php echo $disabled ?>
						class="text-area-order" />
					</td>

					<td class="center"><?php echo  JHTML::_('select.genericlist',$displayTypes,"type_id[$item->id]",'class="inputbox cfDisplayTypes" size="1"', 'value', 'text',$item->type_id);

					$class='';
					if(!in_array($item->type_id, $display_types_advanced))$class='cfhide';
					?> <a href="#box<?php echo $item->id?>"
						class="btn-small cf_adv_settings <?php echo $class?>"
						id="show_popup<?php echo $item->id?>"> <span aria-hidden="true"
							data-icon="&#xe002"></span> <span><?php echo JText::_('COM_CUSTOMFILTERS_ADV_SETTINGS');?>
						</span> </a> <?php //load the settings popup
					require(dirname(__FILE__).DIRECTORY_SEPARATOR.'default_settings.php');?>
					</td>
					<td class="center"><?php echo  JHTML::_('select.genericlist',$boolean_options,"smart_search[$item->id]",'class="inputbox" size="1"', 'value', 'text',$item->smart_search);?>
					</td>
					<td class="center"><?php echo  JHTML::_('select.genericlist',$boolean_options,"expanded[$item->id]",'class="inputbox" size="1"', 'value', 'text',$item->expanded);?>
					</td>
					<td class="center"><input type="text"
						name="scrollbar_after[<?php echo $item->id?>]"
						value="<?php echo $item->scrollbar_after ?>" class="inputbox"
						size="10" maxlength="8" />
					</td>
					<td class="center"><?php echo $item->custom_id ?>
					</td>
				</tr>
				<?php }	?>
			</tbody>
		</table>
	</div>
	<input type="hidden" name="option" value="com_customfilters" /> <input
		type="hidden" name="task" value="" /> <input type="hidden"
		name="boxchecked" value="0" /> <input type="hidden"
		name="filter_order" value="<?php echo $listOrder; ?>" /> <input
		type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
</form>
		<?php echo $this->loadTemplate('update'); ?>