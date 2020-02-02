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

$sticky = Sh404sefHelperHtml::setFixedTemplate();

if ($sticky) :?>
	<div class="shl-fixed-top-hidden<?php echo Sh404sefHelperHtml::getFixedHeaderClass(); ?>">&nbsp;</div>

<?php endif; ?>

<div class="shl-main-content wbl-theme-default">

	<form method="post" name="adminForm" id="adminForm" class="shl-no-margin">

		<?php if ($sticky) : ?>
			<div class="shl-fixed span12 shl-main-searchbar-wrapper">
				<div class="span2 shl-left-separator shl-hidden-low-width">&nbsp;</div>
				<div id="shl-main-searchbar-right-block" class="span10">
					<?php
					echo ShlMvcLayout_Helper::render('com_sh404sef.filters.search_all', $this->options);

					if (!$this->slowServer)
					{
						echo ShlMvcLayout_Helper::render('com_sh404sef.filters.search_shurl', $this->options);
					}

					echo ShlMvcLayout_Helper::render('com_sh404sef.filters.limit_box', $this->pagination);

					echo '<div id="shl-top-pagination-container" class="pull-right"></div>';

					if ($this->slowServer) : ?>
						<input type="hidden" value="" name="search_pageid"/>
						<input type="hidden" value="0" name="filter_duplicate"/>
						<input type="hidden" value="0" name="filter_aliases"/>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>

		<div id="shl-sidebar-container" class="<?php echo $sticky ? 'shl-fixed' : ''; ?> span2 shl-no-margin">
			<?php echo $this->sidebar; ?>
		</div>

		<?php if (!$sticky): ?>
		<div class="span10">
			<?php endif; ?>

			<?php if (!$sticky): ?>
				<div class="span12 shl-main-searchbar-wrapper">
					<?php
					echo ShlMvcLayout_Helper::render('com_sh404sef.filters.search_all', $this->options);

					if (!$this->slowServer)
					{
						echo ShlMvcLayout_Helper::render('com_sh404sef.filters.search_shurl', $this->options);
					}

					echo ShlMvcLayout_Helper::render('com_sh404sef.filters.limit_box', $this->pagination);

					if ($this->slowServer) : ?>
						<input type="hidden" value="" name="search_pageid"/>
						<input type="hidden" value="0" name="filter_duplicate"/>
						<input type="hidden" value="0" name="filter_aliases"/>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<div class="shl-main-list-wrapper span12 <?php if ($sticky)
			{
				echo ' shl-main-list-wrapper-padding';
			} ?>">

				<?php if ($sticky): ?>
				<div class="span2  shl-hidden-low-width"></div>
				<div class="span10 shl-no-margin-left">
					<?php endif;

					if ($this->slowServer)
					{
						echo '<div class="alert">' . JText::_('COM_SH404SEF_SLOW_SERVER_MODE_ON') . '</div>';
					}

					?>

					<div id="sh-message-box"></div>
					<table class="table table-striped table-bordered shl-main-list-wrapper">
						<thead>
						<tr>
							<th class="shl-list-id">&nbsp;
							</th>
							<th class="shl-list-check">
								<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);"/>
							</th>
							<th class="shl-list-hits">
								<?php echo JHTML::_('grid.sort', JText::_('COM_SH404SEF_HITS'), 'cpt', $this->options->filter_order_Dir, $this->options->filter_order); ?>
							</th>
							<th class="shl-list-shurl">
								<?php echo JText::_('COM_SH404SEF_PAGE_ID'); ?>
							</th>
							<th class="shl-list-sef">
								<?php echo JHTML::_('grid.sort', JText::_('COM_SH404SEF_SEF_URL'), 'oldurl', $this->options->filter_order_Dir,
									$this->options->filter_order);
								?>
							</th>

							<?php if ($this->slowServer) : ?>
								<th class="center shl-list-icon">
									<?php echo JText::_('COM_SH404SEF_HAS_METAS'); ?>
								</th>
								<th class="center shl-list-icon">
									<?php echo JText::_('COM_SH404SEF_HAS_DUPLICATE'); ?>
								</th>
								<th class="center shl-list-icon">
									<?php echo JText::_('COM_SH404SEF_ALIASES'); ?>
								</th>
								<?php
							else :
								?>
								<th class="shl-list-icon">
									<?php echo JHTML::_('grid.sort', JText::_('COM_SH404SEF_HAS_METAS'), 'metas', $this->options->filter_order_Dir,
										$this->options->filter_order);
									?>
								</th>
								<th class="shl-list-icon">
									<?php echo JHTML::_('grid.sort', JText::_('COM_SH404SEF_HAS_DUPLICATE'), 'duplicates', $this->options->filter_order_Dir,
										$this->options->filter_order);
									?>
								</th>
								<th class="shl-list-icon">
									<?php echo JHTML::_('grid.sort', JText::_('COM_SH404SEF_ALIASES'), 'aliases', $this->options->filter_order_Dir,
										$this->options->filter_order);
									?>
								</th>
							<?php endif; ?>

							<th class="shl-list-icon">
								<?php echo JText::_('COM_SH404SEF_IS_CUSTOM'); ?>
							</th>

							<th class="shl-list-hits"><?php echo JText::_('COM_SH404SEF_SRC_COLUMN_TITLE'); ?>
							</th>
						</tr>
						</thead>
						<tfoot>
						<tr>
							<td colspan="10">
								<?php echo '<div id="shl-bottom-pagination-container">' . $this->pagination->getListFooter() . '</div>'; ?>
							</td>
						</tr>
						</tfoot>
						<tbody>
						<?php
						$k = 0;
						if ($this->itemCount > 0)
						{
							$sizes = Sh404sefFactory::getPConfig()->windowSizes;
							for ($i = 0; $i < $this->itemCount; $i++)
							{
								$url = &$this->items[$i];
								$checked = JHtml::_('grid.id', $i, $url->id);
								$custom = !empty($url->newurl) && $url->dateadd != '0000-00-00'
									? ShlHtmlBs_Helper::iconglyph('', 'wrench', JText::_('COM_SH404SEF_CUSTOM_URL_LINK_TITLE')) : '&nbsp;';
								?>

								<tr class="shl-line-wrap350">
									<td class="shl-list-id">
										<?php echo $this->pagination->getRowOffset($i); ?>
									</td>
									<td class="shl-list-check">
										<?php echo $checked; ?>
									</td>
									<td class="shl-list-hits">
										<?php
										echo empty($url->cpt) ? '&nbsp;' : ShlSystem_Strings::formatIntForTitle($url->cpt);
										?>
									</td>
									<td class="shl-list-shurl">
										<?php
										echo $this->slowServer ? '' : $this->escape($url->pageid);
										?>
									</td>
									<td class="shl-list-sef">
										<?php
										$params = array();
										$linkData = array('c' => 'editurl', 'task' => 'edit', 'cid[]' => $url->id, 'tmpl' => 'component');
										$targetUrl = Sh404sefHelperUrl::buildUrl($linkData);
										$params['linkTitle'] = JText::_('COM_SH404SEF_MODIFY_LINK_TITLE') . ' ' . $this->escape($url->oldurl);
										$modalTitle = '';
										$params['linkClass'] = 'shl-list-sef';
										$params['linkType'] = 'a';

										$name = '-editurl-' . $url->id;
										echo ShlHtmlModal_helper::modalLink($name, $this->escape($url->oldurl), $targetUrl, $sizes['editurl']['x'],
											$sizes['editurl']['y'], $top = 0, $left = 0, $onClose = '', $modalTitle, $params);
										// small preview icon
										$sefConfig = Sh404sefFactory::getConfig();
										$link = JURI::root() . ltrim($sefConfig->shRewriteStrings[$sefConfig->shRewriteMode], '/') . $url->oldurl;
										echo '&nbsp;<a href="' . $this->escape($link) . '" target="_blank" title="' . JText::_('COM_SH404SEF_PREVIEW') . ' ' . $this->escape($url->oldurl) . '">';
										echo '<img src=\'components/com_sh404sef/assets/images/external-black.png\' border=\'0\' alt=\'' . JText::_('COM_SH404SEF_PREVIEW') . '\' />';
										echo '</a>';
										?>
									</td>

									<td class="center">
										<?php
										if (empty($url->metas))
										{
											echo '&nbsp;';
										}
										else
										{
											$params = array();
											$linkData = array('c' => 'editurl', 'task' => 'edit', 'cid[]' => $url->id, 'tmpl' => 'component', 'startOffset' => 1);
											$targetUrl = Sh404sefHelperUrl::buildUrl($linkData);
											$params['linkTitle'] = JText::_('COM_SH404SEF_HAS_META_LINK_TITLE');
											$modalTitle = '';
											$name = '-editurlmeta-' . $url->id;
											echo ShlHtmlModal_helper::modalLink($name, ShlHtmlBs_Helper::iconglyph('', 'tags'), $targetUrl,
												$sizes['editurl']['x'], $sizes['editurl']['y'], $top = 0,
												$left = 0, $onClose = '', $modalTitle, $params);
										}
										?>
									</td>
									<td class="center">
										<?php
										if ($this->slowServer)
										{
											$params = array();
											$linkData = array('c' => 'duplicates', 'cid[]' => $url->id, 'tmpl' => 'component');
											$targetUrl = Sh404sefHelperUrl::buildUrl($linkData);
											$modalTitle = '';
											$params['linkTitle'] = $url->rank == 0 ? JText::_('COM_SH404SEF_IS_A_MAIN_URL') : JText::_('COM_SH404SEF_IS_DUPLICATE');
											$params['linkType'] = 'button';
											$params['linkClass'] = 'btn btn-important';
											$name = '-editurlduplicates-' . $url->id;
											$anchor = $url->rank == 0 ? '[<strong>+++</strong>]' : '++';
											echo ShlHtmlModal_helper::modalLink($name, $anchor, $targetUrl, $sizes['duplicates']['x'],
												$sizes['duplicates']['y'], $top = 0, $left = 0, $onClose = '', $modalTitle, $params);
										}
										if (empty($url->duplicates))
										{
											echo '&nbsp;';
										}
										else
										{
											$params = array();
											$linkData = array('c' => 'duplicates', 'cid[]' => $url->id, 'tmpl' => 'component');
											$targetUrl = Sh404sefHelperUrl::buildUrl($linkData);
											$modalTitle = '';
											$params['linkTitle'] = JText::sprintf('COM_SH404SEF_HAS_DUPLICATES_LINK_TITLE', $url->duplicates);
											$params['linkType'] = 'button';
											$params['linkClass'] = 'btn btn-important';
											$name = '-editurlduplicates-' . $url->id;
											$anchor = $url->duplicates;
											echo ShlHtmlModal_helper::modalLink($name, $anchor, $targetUrl, $sizes['duplicates']['x'],
												$sizes['duplicates']['y'], $top = 0, $left = 0, $onClose = '', $modalTitle, $params);
										}
										?>
									</td>
									<td class="center">
										<?php
										if (empty($url->aliases))
										{
											echo '&nbsp;';
										}
										else
										{
											$params = array();
											$linkData = array('c' => 'editurl', 'task' => 'edit', 'cid[]' => $url->id, 'tmpl' => 'component', 'startOffset' => 2);
											$targetUrl = Sh404sefHelperUrl::buildUrl($linkData);
											$modalTitle = '';
											$params['linkTitle'] = 'Has ' . $url->aliases . ' alias(es)';
											$params['linkType'] = 'button';
											$params['linkClass'] = 'btn btn-success';
											$name = '-editurlaliases-' . $url->id;
											$anchor = $url->aliases;
											echo ShlHtmlModal_helper::modalLink($name, $anchor, $targetUrl, $sizes['editurl']['x'],
												$sizes['editurl']['y'], $top = 0, $left = 0, $onClose = '', $modalTitle, $params);
										}
										?>
									</td>
									<td class="center">
										<?php echo $custom; ?>
									</td>

									<td class="center">
										<?php
										$params = array();
										$linkData = array('c' => 'srcdetails', 'url_id' => $url->id, 'tmpl' => 'component');
										$targetUrl = Sh404sefHelperUrl::buildUrl($linkData);
										$modalTitle = '';
										$params['linkTitle'] = JText::_('COM_SH404SEF_SRC_TITLE');
										$params['linkClass'] = 'btn  shl-src-link';
										$name = '-viewsrcdetails-' . $url->id;
										echo ShlHtmlModal_helper::modalLink($name, '+', $targetUrl, $sizes['selectredirect']['x'], $sizes['selectredirect']['y'], $top = 0, $left = 0, $onClose = '', $modalTitle, $params);
										?>
									</td>

								</tr>

								<tr>
									<td colspan="10">

										<div class="muted shl-hit-details-subrecord">
											<small>
												<span class="shl-hit-details-sub-title">
													<strong>
														<?php echo JText::_('COM_SH404SEF_NON_SEF_URL'); ?>&nbsp;
													</strong>
												</span>
					                            <span class="shl-hit-details-sub-data">
										            <?php echo $this->escape($url->newurl) ?>
					                            </span>
											</small>
										</div>

									</td>
								</tr>

								<?php
								$k = 1 - $k;
							}
						}
						else
						{
							?>
							<tr>
								<td class="center shl-middle" colspan="10">
									<?php echo JText::_('COM_SH404SEF_NO_URL'); ?>
								</td>
							</tr>
							<?php
						}
						?>
						</tbody>
					</table>

					<?php if ($sticky): ?>
				</div>
			<?php endif; ?>
			</div>

			<?php if (!$sticky): ?>
		</div>
	<?php endif; ?>

		<input type="hidden" name="c" value="urls"/>
		<input type="hidden" name="view" value="urls"/>
		<input type="hidden" name="option" value="com_sh404sef"/>
		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="boxchecked" value="0"/>
		<input type="hidden" name="hidemainmenu" value="0"/>
		<input type="hidden" name="filter_order" value="<?php echo $this->options->filter_order; ?>"/>
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->options->filter_order_Dir; ?>"/>
		<?php echo JHTML::_('form.token'); ?>
	</form>
</div>


<div class="sh404sef-footer-container">
	<?php echo $this->footerText; ?>
</div>
