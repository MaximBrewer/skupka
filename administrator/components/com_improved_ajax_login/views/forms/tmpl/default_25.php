<?php
/*-------------------------------------------------------------------------
# com_improved_ajax_login - com_improved_ajax_login
# -------------------------------------------------------------------------
# @ author    Balint Polgarfi
# @ copyright Copyright (C) 2013 Offlajn.com  All Rights Reserved.
# @ license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @ website   http://www.offlajn.com
-------------------------------------------------------------------------*/
?><?php
// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHTML::_('script','system/multiselect.js',false,true);
// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_improved_ajax_login/assets/css/improved_ajax_login.css');
$document->addScript('https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js');

$user	= JFactory::getUser();
$userId	= $user->get('id');
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$canOrder	= $user->authorise('core.edit.state', 'com_improved_ajax_login');
$saveOrder	= $listOrder == 'a.ordering';
?>
<style>
.icon-48-forms {
  background: url(<?php echo JURI::root()
    ?>administrator/components/com_improved_ajax_login/assets/images/l_forms.png);
  height: 47px;
}
</style>
<script>
(window.jq183||jQuery)(function($) {
  $(document.body).on("change", "select.ial-lang", function() {
    var val = $(this).val();
    location.href = val;
  });
});
</script>
<form action="<?php echo JRoute::_('index.php?option=com_improved_ajax_login&view=forms'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('Search'); ?>" />
			<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>


		<div class='filter-select fltrt'>
			<select name="filter_published" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true);?>
			</select>
		</div>


	</fieldset>
	<div class="clr"> </div>

	<table class="adminlist">
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" />
				</th>

				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_IMPROVED_AJAX_LOGIN_FORMS_TITLE', 'a.title', $listDirn, $listOrder); ?>
				</th>
<!--
                <?php if (isset($this->items[0]->state)) { ?>
				<th width="5%">
					<?php echo JHtml::_('grid.sort',  'JPUBLISHED', 'a.state', $listDirn, $listOrder); ?>
				</th>
                <?php } ?>
                <?php if (isset($this->items[0]->ordering)) { ?>
				<th width="10%">
					<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ORDERING', 'a.ordering', $listDirn, $listOrder); ?>
					<?php if ($canOrder && $saveOrder) :?>
						<?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'forms.saveorder'); ?>
					<?php endif; ?>
				</th>
                <?php } ?>
-->
				<!--th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_IMPROVED_AJAX_LOGIN_FORMS_TYPE', 'a.type', $listDirn, $listOrder); ?>
				</th-->
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'JFIELD_LANGUAGE_LABEL', 'a.language', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JText::_('Translate'); ?>
				</th>
                <?php if (isset($this->items[0]->id)) { ?>
                <th width="1%" class="nowrap">
                    <?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                </th>
                <?php } ?>
			</tr>
		</thead>
		<tfoot>
			<?php
                if(isset($this->items[0])){
                    $colspan = count(get_object_vars($this->items[0]));
                }
                else{
                    $colspan = 10;
                }
            ?>
			<tr>
				<td colspan="<?php echo $colspan ?>">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->items as $i => $item) :
			$ordering	= ($listOrder == 'a.ordering');
			$canCreate	= $user->authorise('core.create',		'com_improved_ajax_login');
			$canEdit	= $user->authorise('core.edit',			'com_improved_ajax_login');
			$canCheckin	= $user->authorise('core.manage',		'com_improved_ajax_login');
			$canChange	= $user->authorise('core.edit.state',	'com_improved_ajax_login');
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>
				<td>
				<?php if (isset($item->checked_out) && $item->checked_out) : ?>
					<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'forms.', $canCheckin); ?>
				<?php endif; ?>
				<?php if ($canEdit) : ?>
					<a href="<?php echo JRoute::_('index.php?option=com_improved_ajax_login&task=form.edit&id='.(int) $item->id); ?>">
					<?php echo $this->escape($item->title); ?></a>
				<?php else : ?>
					<?php echo $this->escape($item->title); ?>
				<?php endif; ?>
				</td>
<!--
                <?php if (isset($this->items[0]->state)) { ?>
				    <td class="center">
					    <?php echo JHtml::_('jgrid.published', $item->state, $i, 'forms.', $canChange, 'cb'); ?>
				    </td>
                <?php } ?>
                <?php if (isset($this->items[0]->ordering)) { ?>
				    <td class="order">
					    <?php if ($canChange) : ?>
						    <?php if ($saveOrder) :?>
							    <?php if ($listDirn == 'asc') : ?>
								    <span><?php echo $this->pagination->orderUpIcon($i, true, 'forms.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								    <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'forms.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							    <?php elseif ($listDirn == 'desc') : ?>
								    <span><?php echo $this->pagination->orderUpIcon($i, true, 'forms.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								    <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'forms.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							    <?php endif; ?>
						    <?php endif; ?>
						    <?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
						    <input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text-area-order" />
					    <?php else : ?>
						    <?php echo $item->ordering; ?>
					    <?php endif; ?>
				    </td>
                <?php } ?>
-->
				<td>
					<?php echo $item->language ? $item->language : JText::_('JALL') ?>
				</td>
				<td>
          <?php if ($item->id < 100): ?>
          <select class="ial-lang">
            <option><?php echo JText::_('- Translate Form -') ?></option>
          <?php foreach($this->lang as $lang): ?>
          <?php if ($lang->code != $item->language): ?>
            <option value="<?php echo JRoute::_('index.php?option=com_improved_ajax_login&view=form&layout=edit&orig='.(int)$item->id.'&lang='.$lang->code); ?>"><?php echo $lang->title ?></option>
          <?php endif ?>
          <?php endforeach ?>
          </select>
          <?php else: ?>
          <a title="Delete" onclick="return confirm('<?php echo JText::_('Are you sure you want to delete this Form?') ?>')" href="<?php echo JRoute::_('index.php?option=com_improved_ajax_login&task=form.delete&id='.$item->id) ?>" class="btn btn-micro hasTooltip">
            [ <?php echo JText::_('JACTION_DELETE') ?> ]
          </a>
          <?php endif ?>
				</td>
                <?php if (isset($this->items[0]->id)) { ?>
				<td class="center">
					<?php echo (int) $item->id; ?>
				</td>
                <?php } ?>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>