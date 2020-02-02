<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.10.7
 * @author	acyba.com
 * @copyright	(C) 2009-2019 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?><div id="acy_content">
	<div id="iframedoc"></div>
	<?php if(!acymailing_isAdmin()){ ?>
		<fieldset>
			<div class="acyheader" style="float: left;padding-left: 0px;"><?php echo acymailing_translation('CLICK_STATISTICS'); ?></div>
			<div class="toolbar" id="acytoolbar" style="float: right;">
				<table>
					<tr>
						<td id="acybutton_stats_exportglobal"><a onclick="location.href='<?php echo acymailing_completeLink('frontstatsurl&task=detaillisting&filter_mail=' . $this->selectedMail . '&filter_url=' . $this->selectedUrl); ?>';return false;" href="#" ><span class="icon-32-acyexport" title="<?php echo acymailing_translation('VIEW_DETAILS'); ?>"></span><?php echo acymailing_translation('VIEW_DETAILS'); ?></a></td>
						<td id="acybutton_stats_exportglobal"><a onclick="acymailing.submitbutton('exportglobal'); return false;" href="#" ><span class="icon-32-acyexport" title="<?php echo acymailing_translation('ACY_EXPORT'); ?>"></span><?php echo acymailing_translation('ACY_EXPORT'); ?></a></td>
						<td id="acybutton_stats_exportglobal"><a onclick="location.href='<?php echo acymailing_completeLink('frontstats'); ?>';return false;" href="#" ><span class="icon-32-cancel" title="<?php echo acymailing_translation('GLOBAL_STATISTICS'); ?>"></span><?php echo acymailing_translation('GLOBAL_STATISTICS'); ?></a></td>
					</tr>
				</table>
			</div>
		</fieldset>
	<?php } ?>
	<form action="<?php echo acymailing_completeLink((acymailing_isAdmin() ? '': 'front').'statsurl'); ?>" method="post" name="adminForm" id="adminForm">
		<table class="acymailing_table_options">
			<tr>
				<td>
					<?php acymailing_listingsearch($this->pageInfo->search); ?>
				</td>
				<td class="tablegroup_options">
					<?php echo $this->filters->mail; ?>
					<?php echo $this->filters->url; ?>
				</td>
			</tr>
		</table>

		<table class="acymailing_table" cellpadding="1">
			<thead>
				<tr>
					<th class="title titlenum">
						<?php echo acymailing_translation('ACY_NUM'); ?>
					</th>
					<th class="title">
						<?php echo acymailing_gridSort(acymailing_translation('ACY_NAME'), 'c.name', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value); ?>
					</th>
					<th class="title">
						<?php echo acymailing_gridSort(acymailing_translation('URL'), 'c.url', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value); ?>
					</th>
					<th class="title">
						<?php echo acymailing_gridSort(acymailing_translation('JOOMEXT_SUBJECT'), 'b.subject', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value); ?>
					</th>
					<?php if(acymailing_level(3)){ ?>
					<th class="title">
						<?php echo acymailing_translation('ACY_CLICKMAP'); ?>
					</th>
					<?php } ?>
					<th class="title titletoggle">
						<?php echo acymailing_gridSort(acymailing_translation('UNIQUE_HITS'), 'uniqueclick', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value); ?>
					</th>
					<th class="title titletoggle">
						<?php echo acymailing_gridSort(acymailing_translation('TOTAL_HITS'), 'totalclick', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="11">
						<?php echo $this->pagination->getListFooter();
						echo $this->pagination->getResultsCounter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
			<?php
			$k = 0;

			for($i = 0, $a = count($this->rows); $i < $a; $i++){
				$row =& $this->rows[$i];
				$row->subject = acyEmoji::Decode($row->subject);
				$id = 'urlclick'.$i;
				$myurlname = strip_tags($row->name);
				if(strlen($myurlname) > 55){
					$urlname = substr($myurlname, 0, 20).'...'.substr($myurlname, -30);
				}else{
					$urlname = $row->name;
				}

				$row->url = strip_tags($row->url);
				$url = (strlen($row->url) > 55) ? substr($row->url, 0, 20).'...'.substr($row->url, -30) : $row->url;
				?>
				<tr class="<?php echo "row$k"; ?>" id="<?php echo $id; ?>">
					<td align="center" style="text-align:center">
						<?php echo $this->pagination->getRowOffset($i); ?>
					</td>
					<td>
						<?php echo acymailing_popup(acymailing_completeLink(acymailing_getVar('cmd', 'ctrl').'&task=edit&urlid='.$row->urlid, true), '<i class="acyicon-edit"></i>', '', 500, 200); ?>
						<a target="_blank" href="<?php echo $row->url; ?>" id="urlink_<?php echo $row->urlid.'_'.$row->mailid; ?>"><?php echo $urlname; ?></a>
					</td>
					<td>
						<a target="_blank" href="<?php echo $row->url; ?>"><?php echo $url; ?></a>
					</td>
					<td align="center" style="text-align:center">
						<?php echo $row->subject; ?>
					</td>
					<?php if(acymailing_level(3)){ ?>
					<td align="center" style="text-align:center">
						<a href="<?php echo acymailing_completeLink(acymailing_getVar('cmd', 'ctrl').'&task=globalOverview&filter_mail='.$row->mailid) ?>"><i class="acyicon-statistic"></i></a>
					</td>
					<?php } ?>
					<td align="center" style="text-align:center">
						<?php
						if(acymailing_level(2) && $this->config->get('anonymous_tracking', 0) == 0) {
							echo acymailing_popup(acymailing_completeLink(acymailing_getVar('cmd', 'ctrl') . '&task=detaillisting&filter_mail=' . $row->mailid . '&filter_url=' . $row->urlid, true), $row->uniqueclick);
						}else{
							echo $row->uniqueclick;
						}
						?>
					</td>
					<td align="center" style="text-align:center">
						<?php
						if(acymailing_level(2) && $this->config->get('anonymous_tracking', 0) == 0) {
							echo acymailing_popup(acymailing_completeLink(acymailing_getVar('cmd', 'ctrl').'&task=detaillisting&filter_mail='.$row->mailid.'&filter_url='.$row->urlid, true), $row->totalclick);
						}else{
							echo $row->totalclick;
						}
						?>
					</td>
				</tr>
				<?php
				$k = 1 - $k;
			}
			?>
			</tbody>
		</table>

		<?php acymailing_formOptions($this->pageInfo->filter->order); ?>
	</form>
</div>
