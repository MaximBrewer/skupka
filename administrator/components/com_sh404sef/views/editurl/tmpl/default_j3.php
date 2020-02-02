<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author       Yannick Gaultier
 * @copyright    (c) Yannick Gaultier - Weeblr llc - 2016
 * @package      sh404SEF
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version      4.7.3.3292
 * @date        2016-03-15
 */

// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC'))
{
	die('Direct Access to this location is not allowed.');
}

jimport('joomla.html.html.bootstrap');
JHtml::_('formbehavior.chosen', 'select');

?>

<div class="shmodal-toolbar row-fluid wbl-theme-default" id="shmodal-toolbar">
	<div class="shmodal-toolbar-wrapper">
		<div class="shmodal-toolbar-text">
			<?php
			if (empty($this->url->id))
			{
				echo ShlHtmlBs_Helper::label(Sh404sefHelperHtml::abridge(JText::_('COM_SH404SEF_ADD_URL_TITLE'), 'editurl'), 'info', $dismiss = false, 'label-large');
			}
			else
			{
				$title = JText::_('COM_SH404SEF_MODIFY_LINK_TITLE') . ' ';
				echo $title . ShlHtmlBs_Helper::label(Sh404sefHelperHtml::abridge($this->escape($this->url->oldurl), 'editurl'), 'info', $dismiss = false, 'label-large');
			}

			?>
		</div>
		<div class="shmodal-toolbar-buttons" id="shmodal-toolbar-buttons">
			<button class="btn btn-primary" type="button" onclick="Joomla.submitform('save',document.adminForm);">
				<i class="icon-apply icon-white"> </i>
				<?php echo JText::_('JSAVE'); ?>
			</button>
			<button class="btn" type="button" onclick="<?php echo JRequest::getBool('refresh', 0)
				? 'window.parent.location.href=window.parent.location.href;' : '';
			?>  window.parent.shlBootstrap.closeModal();">
				<?php echo JText::_('JCANCEL'); ?>
			</button>
		</div>
	</div>
</div>

<div class="shmodal-content wbl-theme-default" id="shmodal-content">

	<?php
	echo ShlMvcLayout_Helper::render('com_sh404sef.general.message_block', $this);
	?>
	<div id="editurl-container">

		<form action="index.php" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">

			<div class="row-fluid">

				<ul class="nav nav-tabs" id="editurl">
					<?php if (!$this->home) : ?>
						<li<?php echo $this->activePanel == 'editurl' ? ' class="active"' : ''; ?>><a data-toggle="tab"
						                                                                              href="#panelediturl"><?php echo JText::_('COM_SH404SEF_EDIT_URL'); ?></a>
						</li>
					<?php endif; ?>

					<li<?php echo $this->activePanel == 'seo' ? ' class="active"' : ''; ?>><a data-toggle="tab"
					                                                                          href="#panelseo"><?php echo JText::_('COM_SH404SEF_EDIT_META'); ?></a>
					</li>
					<li<?php echo $this->activePanel == 'aliases' ? ' class="active"' : ''; ?>><a data-toggle="tab"
					                                                                              href="#panelaliases"><?php echo JText::_('COM_SH404SEF_ALIASES'); ?></a>
					</li>
					<li<?php echo $this->activePanel == 'social_seo' ? ' class="active"' : ''; ?>><a data-toggle="tab"
					                                                                                 href="#panelsocial_seo"><?php echo JText::_('COM_SH404SEF_OG_CONFIG'); ?></a>
					</li>
				</ul>

				<?php
				// start pane
				echo JHtml::_('bootstrap.startPane', 'sh404SEFEditurl', array('active' => 'panel' . $this->activePanel));

				// don't display url edit panel for home page, as user can't change the url
				if (!$this->home)
				{
					echo JHtml::_('bootstrap.addPanel', 'sh404SEFEditurl', 'panelediturl');
					echo $this->loadTemplate($this->joomlaVersionPrefix . '_url');
					echo JHtml::_('bootstrap.endPanel');
				}

				echo JHtml::_('bootstrap.addPanel', 'sh404SEFEditurl', 'panelseo');
				echo $this->loadTemplate($this->joomlaVersionPrefix . '_seo');
				echo JHtml::_('bootstrap.endPanel');

				echo JHtml::_('bootstrap.addPanel', 'sh404SEFEditurl', 'panelaliases');
				echo $this->loadTemplate($this->joomlaVersionPrefix . '_aliases');
				echo JHtml::_('bootstrap.endPanel');

				echo JHtml::_('bootstrap.addPanel', 'sh404SEFEditurl', 'panelsocial_seo');
				echo $this->loadTemplate($this->joomlaVersionPrefix . '_social_seo');
				echo JHtml::_('bootstrap.endPanel');

				echo JHtml::_('bootstrap.endPane');

				// if automatic url, some items are not editable, we pass them as hidden fields
				if (!$this->canEditNewUrl) : ?>
					<input type="hidden" name="newurl" value="<?php echo $this->escape($this->url->get('newurl')); ?>"/>
				<?php endif;

				// if can edit the newurl, then the old is fixed (404 pages for instances)
				$oldUrl = $this->url->get('oldurl');
				if ($this->canEditNewUrl && !empty ($oldUrl)) : ?>
					<input type="hidden" name="oldurl" value="<?php echo $this->escape($this->url->get('oldurl')); ?>"/>
				<?php endif; ?>


				<div>
					<input type="hidden" name="id" value="<?php echo $this->url->get('id'); ?>"/>

					<input type="hidden" name="c" value="editurl"/>
					<input type="hidden" name="view" value="editurl"/>
					<input type="hidden" name="option" value="com_sh404sef"/>
					<input type="hidden" name="task" value=""/>
					<input type="hidden" name="tmpl" value="component"/>
					<input type="hidden" name="previousSefUrl"
					       value="<?php echo $this->escape($this->url->get('oldurl')); ?>"/>
					<input type="hidden" name="previousNonSefUrl"
					       value="<?php echo $this->escape($this->url->get('newurl')); ?>"/>
					<input type="hidden" name="meta_id" value="<?php echo $this->meta->id; ?>"/>
					<?php if ($this->home || $this->noUrlEditing) : ?>
						<input type="hidden" name="oldurl"
						       value="<?php echo $this->escape($this->url->get('oldurl')); ?>"/>
						<input type="hidden" name="newurl"
						       value="<?php echo $this->escape($this->url->get('newurl')); ?>"/>
						<input type="hidden" name="pageid" value=""/>
					<?php endif; ?>
					<?php echo JHTML::_('form.token'); ?>
				</div>
			</div>
		</form>
	</div>
</div>
