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
JHtml::_('bootstrap.framework');
JHtml::_('formbehavior.chosen', 'select');
$liveSite = Sh404sefFactory::getPageInfo()->getDefaultFrontLiveSite();
?>

<div class="sh404sef-popup" id="sh404sef-popup">

	<div class="shmodal-toolbar row-fluid wbl-theme-default" id="shmodal-toolbar">
		<div class="shmodal-toolbar-wrapper">
			<div class="shmodal-toolbar-text">
				<?php
				echo JText::_('COM_SH404SEF_SRC_DETAILS_FOR')
					. ' '
					. ShlHtmlBs_Helper::label(Sh404sefHelperHtml::abridge($this->escape($this->url->oldurl), 'editurl'), 'info', $dismiss = false, 'label-large')
					. ' <small title="' . $this->escape($this->url->newurl) . '">(' . $this->escape(Sh404sefHelperHtml::abridge($this->url->newurl, 'editurl')) . ')</small>';
				?>
			</div>
			<div class="shmodal-toolbar-buttons" id="shmodal-toolbar-buttons">
				<?php
				if ($this->itemCount > 0) :
					$message = JText::_('COM_SH404SEF_CONFIRM_PURGE_HITS_DETAILS', true);
					$onclick = "javascript:
 	 if(confirm('$message')) {
 	 Joomla.submitform('purgedetails', document.adminForm);
}";
					?>
					<button class="btn btn-primary" type="button" onclick="<?php echo $onclick; ?>">
						<i class="icon-publish icon-white"> </i>
						<?php echo JText::_('COM_SH404SEF_HIT_DETAILS_PURGE'); ?></button>
				<?php endif; ?>
				<button class="btn" type="button" onclick="<?php echo JRequest::getBool('refresh', 0)
					? 'window.parent.location.href=window.parent.location.href;' : '';
				?>  window.parent.shlBootstrap.closeModal();">
					<?php echo JText::_('JTOOLBAR_CLOSE'); ?>
				</button>
			</div>
		</div>
	</div>

	<div class="shmodal-content wbl-theme-default" id="shmodal-content">

		<form method="post" action="index.php" name="adminForm" id="adminForm" class="form-validate form-horizontal">

			<div class="row-fluid">

				<div class="shl-fixed shl-modal-searchbar-wrapper shl-left-separator">

					<div class="span2 hidden-phone shl-hidden-low-width"></div>
					<div id="shl-modal-searchbar-right-block" class="span10">
						<?php
						echo ShlMvcLayout_Helper::render('com_sh404sef.filters.search_all', $this->options);
						echo ShlMvcLayout_Helper::render('com_sh404sef.filters.limit_box', $this->pagination);
						echo '<div class="pull-right hidden-phone">' . $this->pagination->getListFooter() . '</div>';
						?>
					</div>
				</div>

				<div id="shl-sidebar-container" class="shl-fixed span2">
					<?php
					echo ShlMvcLayout_Helper::render('com_sh404sef.submenus.filters', JHtml::_('sidebar.getFilters'));
					?>
				</div>

				<div class="shl-modal-list-wrapper shl-left-separator">

					<div class="span2 hidden-phone"></div>
					<div class="span10">

						<?php

						// possible errors and success messages
						echo ShlMvcLayout_Helper::render('com_sh404sef.general.message_block', $this);

						if (!empty($this->alertMsg))
						{
							echo ShlHtmlBs_Helper::alert($this->alertMsg, 'info', $dismiss = false, 'alert-centered');
						}

						?>

						<div id="sh-message-box"></div>

						<?php
						if ($this->itemCount > 0)
						{
							?>
							<table class="table table-striped shl-hit-details table-bordered">
								<thead>
								<tr>
									<th class="shl-list-id">&nbsp;
									</th>
									<th class="shl-list-hits"><?php echo JText::_('COM_SH404SEF_SRC_DETAILS_DUPLICATE'); ?>
									</th>

									<th class="">
										<?php
										echo JHTML::_('grid.sort', JText::_('COM_SH404SEF_SRC_DETAILS_SOURCE_ROUTED_URL'), 'source_routed_url', $this->options->filter_order_Dir, $this->options->filter_order);
										?>
									</th>

									<th class="center">
										<?php
										echo JHTML::_('grid.sort', JText::_('JDATE'), 'datetime', $this->options->filter_order_Dir, $this->options->filter_order);
										?>
									</th>

								</tr>
								</thead>

								<tbody>
								<?php
								$k = 0;
								for ($i = 0; $i < $this->itemCount; $i++)
								{
									$record = $this->items[$i];
									$isDuplicate = $this->items[$i]->url != $this->url->newurl
									?>
									<tr>
										<td class="shl-list-id">
											<?php echo $this->pagination->getRowOffset($i); ?>
										</td>

										<td class="shl-list-hit-type">
											<?php
											$txt = $isDuplicate ? ShlHtmlBs_Helper::iconglyph('', 'thumbs-down') : '';
											echo empty($txt) ? '&nbsp;' : ShlHtmlBs_Helper::badge($txt, 'warning', JText::_('COM_SH404SEF_SRC_DETAILS_DUPLICATE_TITLE'));
											?>
										</td>

										<td class="shl-list-referrer">
		                                    <span class="">
			                                    <?php echo $this->escape($this->items[$i]->source_routed_url); ?>
		                                    </span>
										</td>

										<td class="shl-list-datetime">
											<?php echo $this->items[$i]->datetime; ?>
										</td>
									</tr>

									<tr>
										<td colspan="4">

											<?php if ($isDuplicate) : ?>
												<div class="muted shl-hit-details-subrecord">
													<small>
														<span
															class="shl-hit-details-sub-title"><?php echo JText::_('COM_SH404SEF_SRC_DETAILS_ACTUAL_NON_SEF'); ?>
															&nbsp;</span>
				                                    <span class="shl-hit-details-sub-data">
					                                    <?php echo $this->escape($this->items[$i]->url); ?>
					                                </span>
													</small>
												</div>
											<?php endif; ?>

											<div class="muted shl-hit-details-subrecord">
												<small>
													<span
														class="shl-hit-details-sub-title"><?php echo JText::_('COM_SH404SEF_SRC_DETAILS_SOURCE_URL'); ?>
														&nbsp;</span>
				                                    <span class="shl-hit-details-sub-data">
					                                    <?php echo $this->escape($this->items[$i]->source_url); ?>
					                                </span>
													<button type="button" class="btn btn-mini pull-right"
													        data-toggle="collapse"
													        data-target="#shl-src-details-sub-data-<?php echo $i; ?>">
														<?php echo JText::_('COM_SH404SEF_SRC_DETAILS_TRACE'); ?>
													</button>
												</small>
											</div>

											<div class="muted shl-hit-details-subrecord">
												<small>
													<div id="shl-src-details-sub-data-<?php echo $i; ?>"
													     class="collapse">
														<?php
														$trace = str_replace("\n", '<br />', $this->items[$i]->trace);
														echo $trace;
														?>
													</div>
												</small>
											</div>

										</td>
									</tr>

									<?php
									$k = 1 - $k;
								}

								?>
								</tbody>
							</table>

							<?php
						}
						else
						{
							echo '<br /><div class="alert alert-info">' . JText::_('COM_SH404SEF_NO_DETAILS_RECORDED') . '</div>';
						}
						?>

					</div>
					<input type="hidden" name="c" value="srcdetails"/>
					<input type="hidden" name="view" value="srcdetails"/>
					<input type="hidden" name="option" value="com_sh404sef"/>
					<input type="hidden" name="format" value="html"/>
					<input type="hidden" name="task" value=""/>
					<input type="hidden" name="boxchecked" value="0"/>
					<input type="hidden" name="hidemainmenu" value="0"/>
					<input type="hidden" name="filter_order" value="<?php echo $this->options->filter_order; ?>"/>
					<input type="hidden" name="filter_order_Dir"
					       value="<?php echo $this->options->filter_order_Dir; ?>"/>
					<input type="hidden" name="tmpl" value="component"/>
					<input type="hidden" name="url_id" value="<?php echo $this->url->id; ?>"/>
					<?php echo JHTML::_('form.token'); ?>
				</div>

			</div>
		</form>
	</div>
</div>
