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

<div class="sh404sef-popup" id="sh404sef-popup">

	<div class="shmodal-toolbar row-fluid wbl-theme-default" id="shmodal-toolbar">
		<div class="shmodal-toolbar-wrapper">
			<div class="shmodal-toolbar-text">
				<?php
				echo JText::_('COM_SH404SEF_NOT_FOUND_ENTER_REDIRECT_FOR') . ' '
					. ShlHtmlBs_Helper::label(Sh404sefHelperHtml::abridge($this->escape($this->url->oldurl), 'editurl'), 'info', $dismiss = false, 'label-large');
				?>
			</div>
			<div class="shmodal-toolbar-buttons" id="shmodal-toolbar-buttons">
				<?php
				$message = JText::_('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST');
				$message = addslashes($message);
				$onclick = "javascript:
 	 if(document.adminForm.boxchecked.value==0)
 	 {alert('$message');
}else{
 	 Joomla.submitform('selectnfredirect', document.adminForm);
}";
				?>
				<button class="btn btn-primary" type="button" onclick="<?php echo $onclick; ?>">
					<i class="icon-publish icon-white"> </i>
					<?php echo JText::_('COM_SH404SEF_NOT_FOUND_SELECT_REDIRECT'); ?></button>
				<button class="btn" type="button" onclick="<?php echo JRequest::getBool('refresh', 0)
					? 'window.parent.location.href=window.parent.location.href;' : '';
				?>  window.parent.shlBootstrap.closeModal();">
					<?php echo JText::_('JCANCEL'); ?>
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
						if (!$this->filterSimilarUrls)
						{
							echo ShlMvcLayout_Helper::render('com_sh404sef.filters.search_all', $this->options);
						}

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
							<table class="table table-striped table-bordered">
								<thead>
								<tr>
									<th class="shl-list-id">&nbsp;
									</th>
									<th class="shl-list-check">
										<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);"/>
									</th>
									<th class="shl-list-shurl">
										<?php echo JText::_('COM_SH404SEF_PAGE_ID'); ?>
									</th>
									<th class="shl-list-sef">
										<?php
										if ($this->options->filter_similar_urls == 0)
										{
											echo JHTML::_('grid.sort', JText::_('COM_SH404SEF_SEF_URL'), 'oldurl', $this->options->filter_order_Dir, $this->options->filter_order);
										}
										else
										{
											echo JText::_('COM_SH404SEF_SEF_URL');
										}
										?>
									</th>
									<th class="shl-list-icon">
										<?php echo JText::_('COM_SH404SEF_IS_CUSTOM'); ?>
									</th>
								</tr>
								</thead>

								<tbody>
								<?php
								$k = 0;
								for ($i = 0; $i < $this->itemCount; $i++)
								{

									$url = &$this->items[$i];
									$checked = JHtml::_('grid.id', $i, $url->id);
									$custom = !empty($url->newurl) && $url->dateadd != '0000-00-00' ? ShlHtmlBs_Helper::iconglyph('', 'wrench', JText::_('COM_SH404SEF_CUSTOM_URL_LINK_TITLE')) : '&nbsp;';
									$alt = JText::sprintf('COM_SH404SEF_NOT_FOUND_REDIRECT_TO', $this->escape($this->url->get('oldurl')), $this->escape($url->oldurl));
									?>
									<tr>
										<td class="shl-list-id">
											<?php echo $this->pagination->getRowOffset($i); ?>
										</td>
										<td class="shl-list-check">
											<?php echo $checked; ?>
										</td>
										<td class="shl-list-shurl">
											<?php
											echo empty($url->pageid) ? '' : $this->escape($url->pageid);
											?>
										</td>

										<td class="shl-list-sef">
											<span class="shl-list-sef"><?php echo $this->escape($url->oldurl); ?></span>
										</td>

										<td class="shl-list-icon">
											<?php echo $custom; ?>
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
							echo '<br /><div class="alert alert-info">' . JText::_('COM_SH404SEF_NO_URL') . '</div>';
						}
						?>

					</div>
					<input type="hidden" name="c" value="notfound"/>
					<input type="hidden" name="view" value="notfound"/>
					<input type="hidden" name="option" value="com_sh404sef"/>
					<input type="hidden" name="format" value="html"/>
					<input type="hidden" name="task" value=""/>
					<input type="hidden" name="boxchecked" value="0"/>
					<input type="hidden" name="hidemainmenu" value="0"/>
					<input type="hidden" name="filter_order" value="<?php echo $this->options->filter_order; ?>"/>
					<input type="hidden" name="filter_order_Dir"
					       value="<?php echo $this->options->filter_order_Dir; ?>"/>
					<input type="hidden" name="tmpl" value="component"/>
					<input type="hidden" name="notfound_url_id" value="<?php echo $this->url->id; ?>"/>
					<?php echo JHTML::_('form.token'); ?>
				</div>

			</div>
		</form>
	</div>
</div>
