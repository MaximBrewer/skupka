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

?>

<div class="sh404sef-updates"
     id="sh404sef-updates">
	<!-- start updates panel markup -->

	<table class="table">
		<?php if (!$this->updates->status) : ?>
			<thead>
			<tr>
				<td>
					<?php echo JText::_('COM_SH404SEF_ERROR_CHECKING_NEW_VERSION'); ?>
				</td>
			</tr>
			<tr>
				<td class="center">
					<?php
					$button = ShlHtmlBs_Helper::button(JText::_('COM_SH404SEF_CHECK_UPDATES'));
					echo '<a href="javascript: void(0);" onclick="javascript: shSetupUpdates(\'forced\');" >' . $button . '</a>';
					?>
				</td>
			</tr>
			</thead>

		<?php else : ?>
			<thead>
			<?php if (!$this->updates->shouldUpdate) : ?>
				<tr>
					<td class="center">
						<?php
						echo ShlHtmlBs_Helper::label(JText::_('COM_SH404SEF_YOU_ARE_UP_TO_DATE'), 'success');
						?>
					</td>
				</tr>
			<?php endif; ?>
			<tr>
				<td class="center">
					<?php
					$button = ShlHtmlBs_Helper::button(JText::_('COM_SH404SEF_CHECK_UPDATES'));
					echo '<a href="javascript: void(0);" onclick="javascript: shSetupUpdates(\'forced\');" >' . $button . '</a>';
					if ($this->updates->shouldUpdate)
					{
						echo ' ' . ShlMvcLayout_Helper::render('com_sh404sef.updates.update_' . Sh404sefConfigurationEdition::$id . '_j3');
					}
					?>
				</td>
			</tr>

			</thead>
			<?php if ($this->updates->shouldUpdate) : ?>
				<tr>
					<td class="center">
						<?php
						if (!empty($this->updates->current))
						{
							echo ShlHtmlBs_Helper::label($this->updates->statusMessage, $this->updates->shouldUpdate ? 'important' : 'success');
							echo ' ';
							echo ShlHtmlBs_Helper::label($this->updates->current, 'success');
						}
						?>
					</td>
				</tr>

				<tr>
					<td class="center">
						<?php
						if (!empty($this->updates->current))
						{
							echo ' <div><small>['
								. '<a target="_blank" href="' . $this->escape($this->updates->changelogLink) . '" >'
								. JText::_('COM_SH404SEF_VIEW_CHANGELOG')
								. '</a>]'
								. '&nbsp['
								. '<a target="_blank" href="' . $this->escape($this->updates->downloadLink) . '" >'
								. JText::_('COM_SH404SEF_GET_IT')
								. '</a>]</small></div>';
						}
						?>
					</td>
				</tr>
				<tr>
					<td colspan="2" class="wbl-sh404sef-update-notes">
						<?php
						if (empty($this->updates->noteHtml))
						{
							if (!empty($this->updates->note))
							{
								echo $this->updates->note;
							}
						}
						else
						{
							echo $this->updates->noteHtml;
						}
						?>
					</td>
				</tr>
				<?php
			endif;
		endif;
		?>
	</table>

	<!-- end updates panel markup --></div>

