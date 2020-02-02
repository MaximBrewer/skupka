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
<?php echo $this->menuhtml; ?>
<?php if ($canEdit && JFactory::getApplication()->input->get('menutype')) { ?>
<form class="row-fluid" action="<?php echo JRoute::_('index.php?option=com_maximenuck&view=migration');?>" method="post" name="adminForm" id="adminForm">
<div>
	<span class="hasTooltip btn btn-small btn-danger" style="display:block;margin:5px;" onclick="delete_all_values()" title="<?php echo JText::_('COM_MAXIMENUCK_DELETE_ALL_SETTINGS_DESC'); ?>"><?php echo JText::_('COM_MAXIMENUCK_DELETE_ALL_SETTINGS'); ?></span>
</div>
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
					// $orderkey   = array_search($item->id, $this->ordering[$item->parent_id]);
					$canCreate  = $user->authorise('core.create',     'com_maximenuck');
					// $canEdit    = $user->authorise('core.edit',       'com_maximenuck');
					$canCheckin = $user->authorise('core.manage',     'com_checkin') || $item->checked_out == $user->get('id')|| $item->checked_out == 0;
					$canChange  = $user->authorise('core.edit.state', 'com_maximenuck') && $canCheckin;
					?>
				<li class="clearfix" data-alias="<?php echo $item->alias ?>" data-level="<?php echo $item->level ?>" data-id="<?php echo (int) $item->id; ?>" data-parent="<?php echo (int) $item->parent_id; ?>" data-newcol="<?php echo $item->params->get('maximenu_createcolumn', 0) ?>" data-colwidth="<?php echo $item->params->get('maximenu_colwidth', '180') ?>" data-home="<?php echo $item->home ?>">
					<div class="row itemwrapper">
						<div class="itemtitle"><?php echo str_repeat('-', $item->level) . ' ' . $this->escape($item->title); ?></div>
						<div>
							<span class="itemparamstate"><?php echo JText::_('COM_MAXIMENUCK_NORMAL_STATE'); ?></span>
							<span class="btn-group waitrow">
								<span class="hasTooltip btn btn-small primary"><?php echo JText::_('COM_MAXIMENUCK_MARGINS'); ?></span>
								<span class="hasTooltip btn btn-small disabled valueck itemnormalstylesmargintop"><?php echo $item->params->get('itemnormalstylesmargintop', '&nbsp;'); ?></span>
								<span class="hasTooltip btn btn-small disabled valueck itemnormalstylesmarginright"><?php echo $item->params->get('itemnormalstylesmarginright', '&nbsp;'); ?></span>
								<span class="hasTooltip btn btn-small disabled valueck itemnormalstylesmarginbottom"><?php echo $item->params->get('itemnormalstylesmarginbottom', '&nbsp;'); ?></span>
								<span class="hasTooltip btn btn-small disabled valueck itemnormalstylesmarginleft"><?php echo $item->params->get('itemnormalstylesmarginleft', '&nbsp;'); ?></span>
								<span class="hasTooltip btn btn-small primary"><?php echo JText::_('COM_MAXIMENUCK_PADDINGS'); ?></span>
								<span class="hasTooltip btn btn-small disabled valueck itemnormalstylespaddingtop"><?php echo $item->params->get('itemnormalstylespaddingtop', '&nbsp;'); ?></span>
								<span class="hasTooltip btn btn-small disabled valueck itemnormalstylespaddingright"><?php echo $item->params->get('itemnormalstylespaddingright', '&nbsp;'); ?></span>
								<span class="hasTooltip btn btn-small disabled valueck itemnormalstylespaddingbottom"><?php echo $item->params->get('itemnormalstylespaddingbottom', '&nbsp;'); ?></span>
								<span class="hasTooltip btn btn-small disabled valueck itemnormalstylespaddingleft"><?php echo $item->params->get('itemnormalstylespaddingleft', '&nbsp;'); ?></span>
								<span class="hasTooltip btn btn-small primary"><?php echo JText::_('COM_MAXIMENUCK_BORDERRADIUS'); ?></span>
								<span class="hasTooltip btn btn-small disabled valueck itemnormalstylesroundedcornerstl"><?php echo $item->params->get('itemnormalstylesroundedcornerstl', '&nbsp;'); ?></span>
								<span class="hasTooltip btn btn-small disabled valueck itemnormalstylesroundedcornerstr"><?php echo $item->params->get('itemnormalstylesroundedcornerstr', '&nbsp;'); ?></span>
								<span class="hasTooltip btn btn-small disabled valueck itemnormalstylesroundedcornersbr"><?php echo $item->params->get('itemnormalstylesroundedcornersbr', '&nbsp;'); ?></span>
								<span class="hasTooltip btn btn-small disabled valueck itemnormalstylesroundedcornersbl"><?php echo $item->params->get('itemnormalstylesroundedcornersbl', '&nbsp;'); ?></span>
								<span class="hasTooltip btn btn-small btn-danger normalstate" onclick="delete_values_normal_state(this)" title="<?php echo JText::_('COM_MAXIMENUCK_DELETE_SETTINGS_DESC'); ?>"><?php echo JText::_('COM_MAXIMENUCK_DELETE_SETTINGS'); ?></span>
							</span>
						</div>
						<div>
							<span class="itemparamstate"><?php echo JText::_('COM_MAXIMENUCK_HOVER_STATE'); ?></span>
							<span class="btn-group waitrow">
								<span class="hasTooltip btn btn-small primary"><?php echo JText::_('COM_MAXIMENUCK_MARGINS'); ?></span>
								<span class="hasTooltip btn btn-small disabled valueck itemhoverstylesmargintop"><?php echo $item->params->get('itemhoverstylesmargintop', '&nbsp;'); ?></span>
								<span class="hasTooltip btn btn-small disabled valueck itemhoverstylesmarginright"><?php echo $item->params->get('itemhoverstylesmarginright', '&nbsp;'); ?></span>
								<span class="hasTooltip btn btn-small disabled valueck itemhoverstylesmarginbottom"><?php echo $item->params->get('itemhoverstylesmarginbottom', '&nbsp;'); ?></span>
								<span class="hasTooltip btn btn-small disabled valueck itemhoverstylesmarginleft"><?php echo $item->params->get('itemhoverstylesmarginleft', '&nbsp;'); ?></span>
								<span class="hasTooltip btn btn-small primary"><?php echo JText::_('COM_MAXIMENUCK_PADDINGS'); ?></span>
								<span class="hasTooltip btn btn-small disabled valueck itemhoverstylespaddingtop"><?php echo $item->params->get('itemhoverstylespaddingtop', '&nbsp;'); ?></span>
								<span class="hasTooltip btn btn-small disabled valueck itemhoverstylespaddingright"><?php echo $item->params->get('itemhoverstylespaddingright', '&nbsp;'); ?></span>
								<span class="hasTooltip btn btn-small disabled valueck itemhoverstylespaddingbottom"><?php echo $item->params->get('itemhoverstylespaddingbottom', '&nbsp;'); ?></span>
								<span class="hasTooltip btn btn-small disabled valueck itemhoverstylespaddingleft"><?php echo $item->params->get('itemhoverstylespaddingleft', '&nbsp;'); ?></span>
								<span class="hasTooltip btn btn-small primary"><?php echo JText::_('COM_MAXIMENUCK_BORDERRADIUS'); ?></span>
								<span class="hasTooltip btn btn-small disabled valueck itemhoverstylesroundedcornerstl"><?php echo $item->params->get('itemhoverstylesroundedcornerstl', '&nbsp;'); ?></span>
								<span class="hasTooltip btn btn-small disabled valueck itemhoverstylesroundedcornerstr"><?php echo $item->params->get('itemhoverstylesroundedcornerstr', '&nbsp;'); ?></span>
								<span class="hasTooltip btn btn-small disabled valueck itemhoverstylesroundedcornersbr"><?php echo $item->params->get('itemhoverstylesroundedcornersbr', '&nbsp;'); ?></span>
								<span class="hasTooltip btn btn-small disabled valueck itemhoverstylesroundedcornersbl"><?php echo $item->params->get('itemhoverstylesroundedcornersbl', '&nbsp;'); ?></span>
								<span class="hasTooltip btn btn-small btn-danger hoverstate" onclick="delete_values_hover_state(this)" title="<?php echo JText::_('COM_MAXIMENUCK_DELETE_SETTINGS_DESC'); ?>"><?php echo JText::_('COM_MAXIMENUCK_DELETE_SETTINGS'); ?></span>
							</span>
						</div>
						<div>
							<span class="itemparamstate"><?php echo JText::_('COM_MAXIMENUCK_ACTIVE_STATE'); ?></span>
							<span class="btn-group waitrow">
								<span class="hasTooltip btn btn-small primary"><?php echo JText::_('COM_MAXIMENUCK_MARGINS'); ?></span>
								<span class="hasTooltip btn btn-small disabled valueck itemactivestylesmargintop"><?php echo $item->params->get('itemactivestylesmargintop', '&nbsp;'); ?></span>
								<span class="hasTooltip btn btn-small disabled valueck itemactivestylesmarginright"><?php echo $item->params->get('itemactivestylesmarginright', '&nbsp;'); ?></span>
								<span class="hasTooltip btn btn-small disabled valueck itemactivestylesmarginbottom"><?php echo $item->params->get('itemactivestylesmarginbottom', '&nbsp;'); ?></span>
								<span class="hasTooltip btn btn-small disabled valueck itemactivestylesmarginleft"><?php echo $item->params->get('itemactivestylesmarginleft', '&nbsp;'); ?></span>
								<span class="hasTooltip btn btn-small primary"><?php echo JText::_('COM_MAXIMENUCK_PADDINGS'); ?></span>
								<span class="hasTooltip btn btn-small disabled valueck itemactivestylespaddingtop"><?php echo $item->params->get('itemactivestylespaddingtop', '&nbsp;'); ?></span>
								<span class="hasTooltip btn btn-small disabled valueck itemactivestylespaddingright"><?php echo $item->params->get('itemactivestylespaddingright', '&nbsp;'); ?></span>
								<span class="hasTooltip btn btn-small disabled valueck itemactivestylespaddingbottom"><?php echo $item->params->get('itemactivestylespaddingbottom', '&nbsp;'); ?></span>
								<span class="hasTooltip btn btn-small disabled valueck itemactivestylespaddingleft"><?php echo $item->params->get('itemactivestylespaddingleft', '&nbsp;'); ?></span>
								<span class="hasTooltip btn btn-small primary"><?php echo JText::_('COM_MAXIMENUCK_BORDERRADIUS'); ?></span>
								<span class="hasTooltip btn btn-small disabled valueck itemactivestylesroundedcornerstl"><?php echo $item->params->get('itemactivestylesroundedcornerstl', '&nbsp;'); ?></span>
								<span class="hasTooltip btn btn-small disabled valueck itemactivestylesroundedcornerstr"><?php echo $item->params->get('itemactivestylesroundedcornerstr', '&nbsp;'); ?></span>
								<span class="hasTooltip btn btn-small disabled valueck itemactivestylesroundedcornersbr"><?php echo $item->params->get('itemactivestylesroundedcornersbr', '&nbsp;'); ?></span>
								<span class="hasTooltip btn btn-small disabled valueck itemactivestylesroundedcornersbl"><?php echo $item->params->get('itemactivestylesroundedcornersbl', '&nbsp;'); ?></span>
								<span class="hasTooltip btn btn-small btn-danger activestate" onclick="delete_values_active_state(this)" title="<?php echo JText::_('COM_MAXIMENUCK_DELETE_SETTINGS_DESC'); ?>"><?php echo JText::_('COM_MAXIMENUCK_DELETE_SETTINGS'); ?></span>
							</span>
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
	if (!$canEdit) echo JText::_('COM_MENUMANAGERCK_NORIGHTS_TO_EDIT');
} ?>
</div>
</body>
<?php
/*public 'itemnormalstylesfontsize' => string '' (length=0)
      public 'itemnormalstylesfontcolor' => string '' (length=0)
      public 'itemnormalstylesfontweight' => string 'normal' (length=6)
      public 'itemnormalstylesdescfontsize' => string '' (length=0)
      public 'itemnormalstylesdescfontcolor' => string '' (length=0)
      public 'itemnormalstylesmargintop' => string '' (length=0)
      public 'itemnormalstylesmarginright' => string '' (length=0)
      public 'itemnormalstylesmarginbottom' => string '' (length=0)
      public 'itemnormalstylesmarginleft' => string '' (length=0)
      public 'itemnormalstylespaddingtop' => string '' (length=0)
      public 'itemnormalstylespaddingright' => string '' (length=0)
      public 'itemnormalstylespaddingbottom' => string '' (length=0)
      public 'itemnormalstylespaddingleft' => string '' (length=0)
      public 'itemnormalstylesbgcolor1' => string '' (length=0)
      public 'itemnormalstylesbgopacity' => string '' (length=0)
      public 'itemnormalstylesbgimage' => string '' (length=0)
      public 'itemnormalstylesbgpositionx' => string 'left' (length=4)
      public 'itemnormalstylesbgpositiony' => string 'top' (length=3)
      public 'itemnormalstylesbgimagerepeat' => string 'repeat' (length=6)
      public 'itemnormalstylesbgcolor2' => string '' (length=0)
      public 'itemnormalstylesroundedcornerstl' => string '5' (length=1)
      public 'itemnormalstylesroundedcornerstr' => string '5' (length=1)
      public 'itemnormalstylesroundedcornersbr' => string '5' (length=1)
      public 'itemnormalstylesroundedcornersbl' => string '5' (length=1)
      public 'itemnormalstylesshadowcolor' => string '' (length=0)
      public 'itemnormalstylesshadowblur' => string '3' (length=1)
      public 'itemnormalstylesshadowspread' => string '0' (length=1)
      public 'itemnormalstylesshadowoffsetx' => string '0' (length=1)
      public 'itemnormalstylesshadowoffsety' => string '0' (length=1)
      public 'itemnormalstylesshadowinset' => string '0' (length=1)
      public 'itemnormalstylesbordercolor' => string '' (length=0)
      public 'itemnormalstylesbordertopwidth' => string '' (length=0)
      public 'itemnormalstylesborderrightwidth' => string '' (length=0)
      public 'itemnormalstylesborderbottomwidth' => string '' (length=0)
      public 'itemnormalstylesborderleftwidth' => string '' (length=0)
      public 'itemnormalstylesparentarrowcolor' => string '' (length=0)
      public 'itemhoverstylesparentarrowcolor' => string '' (length=0)
      public 'itemnormalstylesparentarrowmargintop' => string '' (length=0)
      public 'itemnormalstylesparentarrowmarginright' => string '' (length=0)
      public 'itemnormalstylesparentarrowmarginbottom' => string '' (length=0)
      public 'itemnormalstylesparentarrowmarginleft' => string '' (length=0)*/