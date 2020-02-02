<?php
/**
 * @name		Maximenu CK params
 * @package		com_maximenuck
 * @copyright	Copyright (C) 2014. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - http://www.template-creator.com - http://www.joomlack.fr
 */

defined('_JEXEC') or die;
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
$user		= JFactory::getUser();
$app		= JFactory::getApplication();

$assoc		= isset($app->item_associations) ? $app->item_associations : 0;
$canEdit    = $user->authorise('core.edit',       'com_maximenuck');
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr-fr" lang="fr-fr" dir="ltr">
<head>
<?php echo $this->loadTemplate('head'); ?>
</head>
<body>
<?php echo $this->loadTemplate('mainmenu'); ?>
<div id="mainck" class="container-fluid">

<?php if ($canEdit) { ?>
<form class="row-fluid" action="<?php echo JRoute::_('index.php?option=com_maximenuck&view=items');?>" method="post" name="adminForm" id="adminForm">
<div class="span12">
	<div class="row-fluid">
		<div>
			<ol class="sortable" id="sortable">
				<?php
				$originalOrders = array();
				foreach ($this->items as $i => $item) :
					$item->params = new JRegistry($item->params);
					$newcol = $item->params->get('maximenu_createcolumn', 0);
					$newrow = $item->params->get('maximenu_createnewrow', 0);
					$orderkey   = array_search($item->id, $this->ordering[$item->parent_id]);
					$canCreate  = $user->authorise('core.create',     'com_maximenuck');
					// $canEdit    = $user->authorise('core.edit',       'com_maximenuck');
					$canCheckin = $user->authorise('core.manage',     'com_checkin') || $item->checked_out == $user->get('id')|| $item->checked_out == 0;
					$canChange  = $user->authorise('core.edit.state', 'com_maximenuck') && $canCheckin;
					// Get the parents of item for sorting
					if ($item->level > 1)
					{
						$parentsStr = "";
						$_currentParentId = $item->parent_id;
						$parentsStr = " ".$_currentParentId;
						for ($j = 0; $j < $item->level; $j++)
						{
							foreach ($this->ordering as $k => $v)
							{
								$v = implode("-", $v);
								$v = "-" . $v . "-";
								if (strpos($v, "-" . $_currentParentId . "-") !== false)
								{
									$parentsStr .= " " . $k;
									$_currentParentId = $k;
									break;
								}
							}
						}
					}
					else
					{
						$parentsStr = "";
					}

					// Parse the link arguments.
					$link = htmlspecialchars_decode($item->link);
					$args = array();
					parse_str(parse_url(htmlspecialchars_decode($link), PHP_URL_QUERY), $args);
					if (! isset($args['id']) ) $args['id'] = 0;
					if (! isset($args['option']) ) $args['option'] = '';
					if (! isset($args['view']) ) $args['view'] = '';
				?>
				<li class="clearfix" data-alias="<?php echo $item->alias ?>" data-level="<?php echo $item->level ?>" data-id="<?php echo (int) $item->id; ?>" data-parent="<?php echo (int) $item->parent_id; ?>" data-newcol="<?php echo $item->params->get('maximenu_createcolumn', 0) ?>" data-colwidth="<?php echo $item->params->get('maximenu_colwidth', '180') ?>" data-home="<?php echo $item->home ?>">
					<div class="row itemwrapper">
						
						<span class="disclose"><span></span></span>
						<div>
							<span class="icon-move"></span>
							<span class="btn-group">
								<?php if ($canEdit) : ?>
								<a id="publish<?php echo $item->id ?>" class="hasTooltip btn btn-small active" rel="" title="<?php echo JText::_('JSTATUS') ?>" data-state="<?php echo $item->apublished ?>" data-id="<?php echo $item->id ?>" onclick="ajaxPublish(this,$ck(this).attr('data-id'), $ck(this).attr('data-state'))" href="javascript:void(0);">
									<i class="icon-<?php echo ($item->apublished ? '' : 'un') ?>publish"></i>
								</a>
								<?php else : ?>
									<a id="publish<?php echo $item->id ?>" class="disabled hasTooltip btn btn-small active" rel="" title="<?php echo JText::_('JSTATUS') ?>" data-state="<?php echo $item->apublished ?>" data-id="<?php echo $item->id ?>" href="javascript:void(0);">
										<i class="icon-<?php echo ($item->apublished ? '' : 'un') ?>publish"></i>
									</a>
								<?php endif; ?>
								<!--
								<a class="hasTooltip btn btn-small modal" title="<?php //echo JText::_('COM_MAXIMENUCK_EDIT_ITEM_MODAL') ?>" data-id="<?php //echo $item->id ?>" rel="{handler: 'iframe', size: {x: 900, y: 500}}" href="index.php?option=com_maximenuck&task=itemedition.edit&tmpl=component&id=<?php //echo $item->id ?>&<?php //echo JSession::getFormToken(); ?>=1">
									<i class="icon-options"></i>
								</a>
								-->
								<?php //echo str_repeat('<span class="gi">|&mdash;</span>', $item->level - 1) ?>
								<?php if ($item->checked_out && $canEdit) : ?>
									
									<?php
									$text = $item->editor . '<br />' . JHtml::_('date', $item->checked_out_time, JText::_('DATE_FORMAT_LC')) . '<br />' . JHtml::_('date', $item->checked_out_time, 'H:i');
									$active_title = JText::_('JLIB_HTML_CHECKIN') . ' ' . $text;
									//$inactive_title = JHtml::tooltipText(JText::_('JLIB_HTML_CHECKED_OUT'), $text, 0);
									?>
									<a class="hasTooltip btn btn-small checkedouticon" onclick="ajaxCheckin(this,<?php echo $item->id ?>)" href="javascript:void(0);" title="<?php echo $active_title; ?>">
										<i class="icon-checkedout"></i>
									</a>
								<?php endif; ?>
								<?php if ($canEdit) : ?>
								<a class="hasTooltip btn btn-small edititem" title="<?php echo JText::_('COM_MAXIMENUCK_EDIT_ITEM'); ?>" onclick="editItem(this)"><i class="icon-out-2"></i></a>
								<a class="hasTooltip btn btn-small edittitle" title="<?php echo JText::_('COM_MAXIMENUCK_EDIT_TITLE'); ?>" onclick="editTitle(this)"><i class="icon-pencil-2"></i></a>
								<?php endif; ?>
							</span>
								<?php if ($canEdit) : ?>
									<span contentEditable="true" class="hasTooltip cktitle" style="display:inline-block;min-width:200px;" ondblclick="editTitle(this)" href="javascript:void(0)" title="<?php echo JText::_('COM_MAXIMENUCK_DBLCLICK_TO_EDIT'); ?>" data-id="<?php echo (int) $item->id; ?>"><?php echo $this->escape($item->title); ?></span>
									<span class="btn-group">
										<i class="hasTooltip btn btn-small icon-save exittitle" title="<?php echo JText::_('COM_MAXIMENUCK_SAVE'); ?>" onclick="saveTitle($ck('.cktitle', $ck(this).parent().parent()))" style="display:none;"></i>
										<i class="hasTooltip btn btn-small icon-cancel exittitle" title="<?php echo JText::_('COM_MAXIMENUCK_CANCEL'); ?>" onclick="exitTitle($ck('.cktitle', $ck(this).parent().parent()))" style="display:none;"></i>
									</span>
								<?php else : ?>
									<?php echo $this->escape($item->title); ?>
								<?php endif; ?>
									
								<?php if ($canEdit) : ?>
									<div class="" style="display:inline-block;">
										<input class="input-small hasTooltip" type="text" onblur="saveDescription(this)" title="<?php echo JText::_('COM_MAXIMENUCK_ITEM_DESCRIPTION'); ?>" data-id="<?php echo (int) $item->id; ?>" text-origin="<?php echo $this->escape($item->params->get('maximenu_desc', '')); ?>" value="<?php echo $this->escape($item->params->get('maximenu_desc', '')); ?>" />
									</div>
								<?php else : ?>
									<?php echo $this->escape($item->params->get('maximenu_desc', '')); ?>
								<?php endif; ?>
								<?php //if ($item->level > 1 && $hasmaximenumodule) { ?>
								<span class="btn-group columnbuttons<?php if ($newcol) echo ' active'; ?>">
									<a class="hasTooltip btn btn-small createcolumn<?php if ($newcol) echo ' active btn-primary'; ?>" title="<?php echo JText::_('COM_MAXIMENUCK_CREATE_COLUMN'); ?>" onclick="togglecolumn(this)">
										<i class="icon-tree-2"></i>
									</a>
									<a class="hasTooltip btn btn-small colwidth btnvalue" title="<?php echo JText::_('COM_MAXIMENUCK_COLUMN_WIDTH'); ?>" onclick="changecolwidth(this)"><?php echo $item->params->get('maximenu_colwidth', '180'); ?></a>
									
									<a class="hasTooltip btn btn-small createnewrow<?php if ($newrow) echo ' active btn-primary'; ?>" title="<?php echo JText::_('COM_MAXIMENUCK_CREATE_ROW'); ?>" onclick="createnewrow(this)"><i class="icon-reply"></i></a>
								</span>
								<?php //} ?>
								<span class="btn-group submenuwidthbuttons">
									<a class="hasTooltip btn btn-small submenuwidth btnvalue" title="<?php echo JText::_('COM_MAXIMENUCK_SUBMENUWIDTH'); ?>" onclick="changesubmenuwidth(this)"><i class="icon-menu-2"></i><span class="valuetxt"><?php echo $item->params->get('maximenu_submenucontainerwidth', ''); ?></span></a>
									<a class="hasTooltip btn btn-small submenuheight btnvalue" title="<?php echo JText::_('COM_MAXIMENUCK_SUBMENUHEIGHT'); ?>" onclick="changesubmenuheight(this)"><i class="icon-menu-2"></i><span class="valuetxt"><?php echo $item->params->get('maximenu_submenucontainerheight', ''); ?></span></a>
									<a class="hasTooltip btn btn-small submenuleftmargin btnvalue" title="<?php echo JText::_('COM_MAXIMENUCK_SUBMENULEFTMARGIN'); ?>" onclick="submenuleftmargin(this)"><i class="icon-arrow-right-4"></i><span class="valuetxt"><?php echo $item->params->get('maximenu_leftmargin', ''); ?></span></a>
									<a class="hasTooltip btn btn-small submenutopmargin btnvalue" title="<?php echo JText::_('COM_MAXIMENUCK_SUBMENUTOPMARGIN'); ?>" onclick="submenutopmargin(this)"><i class="icon-arrow-down-4"></i><span class="valuetxt"><?php echo $item->params->get('maximenu_topmargin', ''); ?></span></a>
									<a class="hasTooltip btn btn-small fullwidth<?php if (stristr($item->params->get('maximenu_liclass', ''), 'fullwidth')) echo ' active btn-primary'; ?>" title="<?php echo JText::_('COM_MAXIMENUCK_FULLWIDTH'); ?>" onclick="changefullwidthclass(this)"><i class="icon-expand"></i></a>
								</span>
								<span class="togglemobileoptions btn-group">
									<span class="togglescreen btn hasTooltip <?php echo ($item->params->get('maximenu_disabledesktop', '0') == '1' ? 'disable' : '') ?>" title="<?php echo JText::_('COM_MAXIMENUCK_ENABLE_DESKTOP'); ?>" onclick="toggledesktopstate(this)"><i class="icon-delete"></i><i class="icon-screen"></i></span>
									<span class="togglemobile btn hasTooltip <?php echo ($item->params->get('maximenu_disablemobile', '0') == '1' ? 'disable' : '') ?>" title="<?php echo JText::_('COM_MAXIMENUCK_ENABLE_MOBILE'); ?>" onclick="togglemobilestate(this)"><i class="icon-delete"></i><i class="icon-mobile"></i></span>
								</span>

								<?php
								$imagepreview = $item->params->get('menu_image','') ? '<br /><img src=&quot;' . (JUri::root(true) . '/' . $item->params->get('menu_image','')) . '&quot; style=&quot;max-width:200px;max-height:200px;&quot; />' : '';
								?>
								<span class="imageoptions btn-group">
									<span id="imageselect<?php echo $item->id ?>" class="imageselect btn hasTooltip <?php echo ($item->params->get('menu_image','') ? 'active' : '') ?>" style="padding: 4px 7px;" title="<?php echo JText::_('COM_MAXIMENUCK_ITEM_IMAGE') . $imagepreview ; ?>" onclick="call_image_popup(this.id)"><i class="icon-picture"></i></span>
									<span id="imageremove" class="btn hasTooltip icon-delete" style="padding: 4px 7px;font-size:10px;height:18px;" title="<?php echo JText::_('COM_MAXIMENUCK_ITEM_IMAGE_REMOVE'); ?>" onclick="remove_image(this);"></span>
								</span>
								<span class="iconoptions btn-group">
									<span id="iconselect<?php echo $item->id ?>" class="iconselect btn hasTooltip <?php echo ($item->params->get('maximenu_icon','') ? 'active' : '') ?>" style="padding: 4px 7px;" title="<?php echo JText::_('COM_MAXIMENUCK_ITEM_ICON_DESC'); ?>" onclick="call_icons_popup(this.id)"><?php echo JText::_('COM_MAXIMENUCK_ITEM_ICON'); ?>&nbsp;<i class="<?php echo $item->params->get('maximenu_icon','') ?>"></i></span>
									<span id="iconremove" class="btn hasTooltip icon-delete" style="padding: 4px 7px;font-size:10px;height:18px;" title="<?php echo JText::_('COM_MAXIMENUCK_ITEM_ICON_REMOVE'); ?>" onclick="remove_fa_icon(this);"></span>
								</span>
								<?php
								$moduleid = $item->params->get('maximenu_module','');
								$modulebtnclass = $item->params->get('maximenu_insertmodule','') ? 'active btn-info' : '';
								$moduletitle = isset($this->modules[$moduleid]) ? $this->modules[$moduleid]->title : '';
								$modulename = $item->params->get('maximenu_insertmodule','') ? '<span class="moduleid label label-inverse">' . $item->params->get('maximenu_module','') . '</span>&nbsp;' . $moduletitle : '';
								?>
								<span class="moduleoptions btn-group">
									<span id="moduleselect<?php echo $item->id ?>" class="btn hasTooltip <?php echo $modulebtnclass ?>" style="padding: 4px 7px;height:18px;" title="<?php echo JText::_('COM_MAXIMENUCK_ITEM_MODULE'); ?>" onclick="call_modules_popup(this.id)"><span class="modulename"><?php echo $modulename; ?></span>&nbsp;<i class="icon-database"></i></span>
									<span id="moduleremove" class="btn hasTooltip icon-delete" style="padding: 4px 7px;font-size:10px;height:18px;" title="<?php echo JText::_('COM_MAXIMENUCK_ITEM_MODULE_REMOVE'); ?>" onclick="remove_module_icon(this);"></span>
								</span>
								<span class="label pull-right hasTooltip" title="<?php echo JText::_('CK_ID') ?>" style="margin:5px 0;"><?php echo $item->id ?></span>
						</div>
					</div>
					<?php
					// The next item is deeper.
					if ($item->deeper)
					{
						echo '<ol>';
					}
					// The next item is shallower.
					elseif ($item->shallower)
					{
						echo '</li>';
						echo str_repeat('</ol></li>', $item->level_diff);
					}
					// The next item is on the same level.
					else {
						echo '</li>';
					}
					endforeach; ?>
				</ol>
				<input type="hidden" name="menutype" id="menutype" value="<?php echo JFactory::getApplication()->input->get('menutype'); ?>" />
				<input type="hidden" name="task" value="" />
				<?php echo JHtml::_('form.token'); ?>
			</div>
		</div>
	</div>
</form>
<?php } else {
	if (!$canEdit) echo JText::_('COM_MAXIMENUCK_NORIGHTS_TO_EDIT');
} ?>
</div>
<div id="maximenuckModalIcons" tabindex="-1" class="maximenuckModal modal hide fade">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3><?php echo JText::_('CK_EDIT') ?></h3>
	</div>
	<div class="modal-body">
	</div>
	<div class="modal-footer">
		<input id="maximenuckModalIconsFieldid" type="hidden" onchange="set_fa_icon(this.value, jQuery('#maximenuckModalIcons').attr('data-fieldid'));" />
		<button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('JCANCEL') ?></button>
	</div>
</div>
<div id="maximenuckModalImagemanager" tabindex="-1" class="maximenuckModal modal hide fade">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3><?php echo JText::_('CK_EDIT') ?></h3>
	</div>
	<div class="modal-body">
	</div>
	<div class="modal-footer">
		<input id="maximenuckModalImagemanagerfieldid" type="hidden" onchange="set_image(this.value, jQuery('#maximenuckModalImagemanager').attr('data-fieldid'));" />
		<button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('JCANCEL') ?></button>
	</div>
</div>
<div id="maximenuckModalModules" tabindex="-1" class="maximenuckModal modal hide fade">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3><?php echo JText::_('CK_EDIT') ?></h3>
	</div>
	<div class="modal-body">
	</div>
	<div class="modal-footer">
		<input id="maximenuckModalModulesfieldid" type="hidden" onchange="set_module(this.value, jQuery('#maximenuckModalModules').attr('data-fieldid'));" />
		<button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('JCANCEL') ?></button>
	</div>
</div>
<script>
jQuery('#maximenuckModalIcons').on('show', function() {
	jQuery('body').addClass('modal-open');
	var modalBody = jQuery(this).find('.modal-body');
	if (! modalBody.find('iframe').length) {
		modalBody.prepend('<iframe class="iframe" src="index.php?option=com_maximenuck&amp;view=icons&amp;tmpl=component" height="400px" width="100%"></iframe>');
	}
}).on('hidden', function () {
	jQuery('body').removeClass('modal-open');
});

jQuery('#maximenuckModalImagemanager').on('show', function() {
	jQuery('body').addClass('modal-open');
	var modalBody = jQuery(this).find('.modal-body');
	if (! modalBody.find('iframe').length) {
		modalBody.prepend('<iframe class="iframe" src="<?php echo JUri::root(true) ?>/administrator/index.php?option=com_media&view=images&tmpl=component&fieldid=maximenuckModalImagemanagerfieldid" height="400px" width="100%"></iframe>');
	}
}).on('hidden', function () {
	jQuery('body').removeClass('modal-open');
});

jQuery('#maximenuckModalModules').on('show', function() {
	jQuery('body').addClass('modal-open');
	var modalBody = jQuery(this).find('.modal-body');
	if (! modalBody.find('iframe').length) {
		modalBody.prepend('<iframe class="iframe" src="<?php echo JUri::root(true) ?>/administrator/index.php?option=com_maximenuck&amp;view=moduleselect&amp;tmpl=component" height="400px" width="100%"></iframe>');
	}
}).on('hidden', function () {
	jQuery('body').removeClass('modal-open');
});
</script>
</body>
</html>