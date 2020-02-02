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
		<?php

		if ($sticky) : ?>
			<div class="shl-fixed span12 shl-main-searchbar-wrapper">
				<div class="span2 shl-left-separator shl-hidden-low-width">&nbsp;</div>
				<div id="shl-main-searchbar-right-block" class="span10">
					<?php
					echo ShlMvcLayout_Helper::render('com_sh404sef.filters.search_all', $this->options);
					echo ShlMvcLayout_Helper::render('com_sh404sef.filters.search_shurl', $this->options);
					echo ShlMvcLayout_Helper::render('com_sh404sef.filters.limit_box', $this->pagination);
					echo '<div id="shl-top-pagination-container" class="pull-right"></div>';
					?>
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
				<div class="span2 shl-hidden-low-width"></div>
				<div class="span10 shl-no-margin-left">
					<?php
					endif;
					echo ShlMvcLayout_Helper::render('shlib.msg.collapsed_message', array('content' => $this->helpMessage, 'close' => true, 'collapse' => true), SHLIB_LAYOUTS_PATH);
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
								<?php echo JHTML::_('grid.sort', JText::_('COM_SH404SEF_HITS'), 'hits', $this->options->filter_order_Dir, $this->options->filter_order); ?>
							</th>

							<th class="shl-list-shurl">
								<?php echo JHTML::_('grid.sort', JText::_('COM_SH404SEF_PAGE_ID'), 'pageid', $this->options->filter_order_Dir, $this->options->filter_order); ?>
							</th>

							<th>
								<?php echo JHTML::_('grid.sort', JText::_('COM_SH404SEF_URL'), 'oldurl', $this->options->filter_order_Dir, $this->options->filter_order); ?>
							</th>

							<th class="shl-list-hits"><?php echo JText::_('COM_SH404SEF_HIT_DETAILS'); ?>
							</th>

							<th class="shl-list-icon">
								<?php echo JText::_('COM_SH404SEF_IS_CUSTOM'); ?>
							</th>
						</tr>
						</thead>
						<tfoot>
						<tr>
							<td colspan="7">
								<?php echo '<div id="shl-bottom-pagination-container">' . $this->pagination->getListFooter() . '</div>'; ?>
							</td>
						</tr>
						</tfoot>
						<tbody>
						<?php
						$k = 0;
						$sizes = Sh404sefFactory::getPConfig()->windowSizes;
						$sefConfig = Sh404sefFactory::getConfig();
						if ($this->itemCount > 0)
						{
							for ($i = 0; $i < $this->itemCount; $i++)
							{

								$url = &$this->items[$i];
								$checked = JHtml::_('grid.id', $i, $url->pageidid);
								$custom = !empty($url->newurl) && $url->dateadd != '0000-00-00' ? ShlHtmlBs_Helper::iconglyph('', 'wrench', JText::_('COM_SH404SEF_CUSTOM_URL_LINK_TITLE')) : '&nbsp;';
								?>

								<tr>

									<td class="shl-list-id">
										<?php echo $this->pagination->getRowOffset($i); ?>
									</td>

									<td class="shl-list-check">
										<?php echo $checked; ?>
									</td>

									<td class="shl-list-hits">
										<?php echo empty($url->pageidhits) ? '&nbsp;' : ShlSystem_Strings::formatIntForTitle($url->pageidhits); ?>
									</td>

									<td class="shl-list-shurl">
										<?php echo empty($url->pageid) ? '' : $this->escape($url->pageid); ?>
									</td>

									<td class="shl-list-sef">
										<?php
										echo '<input type="hidden" name="metaid[' . $url->id . ']" value="' . (empty($url->metaid) ? 0 : $url->metaid) . '" />';
										echo '<input type="hidden" name="newurls[' . $url->id . ']" value="' . (empty($url->nonsefurl) ? '' : $this->escape($url->nonsefurl)) . '" />';
										// link to full meta edit
										$anchor = empty($url->oldurl) ? '(-)' : $this->escape($url->oldurl);
										$params = array();
										$linkData = array('c' => 'editurl', 'task' => 'edit', 'view' => 'editurl', 'startOffset' => '1', 'cid[]' => $url->id, 'tmpl' => 'component');
										$targetUrl = Sh404sefHelperUrl::buildUrl($linkData);
										$displayedUrl = empty($url->oldurl) ? $url->nonsefurl : $url->oldurl;
										$params['linkTitle'] = JText::_('COM_SH404SEF_MODIFY_META_TITLE') . ' ' . $this->escape($displayedUrl);
										$modalTitle = '';
										$params['linkClass'] = 'shl-list-sef';
										$params['linkType'] = 'a';
										$name = '-editurl-' . $url->id;
										echo ShlHtmlModal_helper::modalLink($name, $anchor, $targetUrl, $sizes['editurl']['x'], $sizes['editurl']['y'], $top = 0, $left = 0, $onClose = '', $modalTitle, $params);
										?>
									</td>

									<td class="center">
										<?php
										$params = array();
										$linkData = array('c' => 'hitdetails', 'url_id' => $url->pageidid, 'tmpl' => 'component', 'request_type' => 'shurls');
										$targetUrl = Sh404sefHelperUrl::buildUrl($linkData);
										$modalTitle = '';
										$params['linkTitle'] = JText::_('COM_SH404SEF_HIT_VIEW_DETAILS_TITLE');
										$params['linkClass'] = 'btn';
										$name = '-viewhitdetails-' . $url->pageidid;
										echo ShlHtmlModal_helper::modalLink($name, '+', $targetUrl, $sizes['selectredirect']['x'], $sizes['selectredirect']['y'], $top = 0, $left = 0, $onClose = '', $modalTitle, $params);
										?>
									</td>

									<td class="shl-list-icon">
										<?php echo $custom; ?>
									</td>

								</tr>
								<tr>
									<td colspan="7">

										<div class="muted shl-hit-details-subrecord">
											<small>
												<span class="shl-hit-details-sub-title">
													<strong><?php echo JText::_('COM_SH404SEF_URL'); ?> &nbsp;</strong>
												</span>
					                            <span class="shl-hit-details-sub-data">
										            <?php
										            // small preview icon
										            $link = JURI::root() . ltrim($sefConfig->shRewriteStrings[$sefConfig->shRewriteMode], '/') . (empty($url->oldurl) ? $url->nonsefurl : $url->oldurl);
										            echo $this->escape($url->nonsefurl)
										            ?>
						                            &nbsp;
						                            <a href="<?php echo $this->escape($link); ?>" target="_blank"
						                               title="<?php echo JText::_('COM_SH404SEF_PREVIEW') . ' ' . $this->escape($url->oldurl); ?>">
							                            <img
								                            src="components/com_sh404sef/assets/images/external-black.png"
								                            border="0"
								                            alt="<?php echo JText::_('COM_SH404SEF_PREVIEW'); ?>"/>
						                            </a>
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
								<td class="center shl-middle" colspan="7">
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
		<input type="hidden" name="c" value="pageids"/>
		<input type="hidden" name="view" value="pageids"/>
		<input type="hidden" name="option" value="com_sh404sef"/>
		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="boxchecked" value="0"/>
		<input type="hidden" name="hidemainmenu" value="0"/>
		<input type="hidden" name="filter_order" value="<?php echo $this->options->filter_order; ?>"/>
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->options->filter_order_Dir; ?>"/>
		<input type="hidden" name="format" value="html"/>
		<input type="hidden" name="shajax" value="0"/>
		<?php echo JHTML::_('form.token'); ?>
	</form>
</div>

<div class="sh404sef-footer-container">
	<?php echo $this->footerText; ?>
</div>
