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
	<form action="<?php echo acymailing_completeLink((acymailing_isAdmin() ? '' : 'front').'statsurl', acymailing_isNoTemplate()); ?>" method="post" name="adminForm" id="adminForm">
		<?php
		if(!acymailing_isAdmin()){
			?>
			<fieldset>
				<div class="acyheader icon-48-stats" style="float: left;"><?php echo acymailing_translation('CLICK_STATISTICS'); ?></div>
				<div class="toolbar" id="toolbar" style="float: right;">
					<table>
						<tr>
							<?php if(acymailing_isAllowed($this->config->get('acl_subscriber_export', 'all'))){ ?>
								<td><a onclick="acymailing.submitbutton('export'); return false;" href="#"><span class="icon-32-acyexport" title="<?php echo acymailing_translation('ACY_EXPORT', true); ?>"></span><?php echo acymailing_translation('ACY_EXPORT'); ?></a></td>
							<?php }
							$mailid = acymailing_getVar('int', 'mailid');
							if(empty($mailid)) $mailid = acymailing_getVar('int', 'filter_mail');

							if(acymailing_isNoTemplate()){
								$link = 'frontdiagram&task=mailing&mailid='.$mailid;
							}else {
								$link = 'frontstatsurl&filter_mail=' . $mailid . '&Itemid=' . acymailing_getVar('int', 'Itemid');
							}
							?>
							<td><a href="<?php echo acymailing_completeLink($link, acymailing_isNoTemplate()) ?>"><span class="icon-32-cancel" title="<?php echo acymailing_translation('ACY_CANCEL', true); ?>"></span><?php echo acymailing_translation('ACY_CANCEL'); ?></a></td>
						</tr>
					</table>
				</div>
			</fieldset>
		<?php } ?>
		<table width="100%" class="acymailing_table_options">
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
				<th class="title titledate">
					<?php echo acymailing_gridSort(acymailing_translation('CLICK_DATE'), 'a.date', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value); ?>
				</th>
				<?php $selectedMail = acymailing_getVar('int', 'filter_mail');
				if(empty($selectedMail)){ ?>
					<th class="title">
						<?php echo acymailing_gridSort(acymailing_translation('JOOMEXT_SUBJECT'), 'b.subject', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value); ?>
					</th>
				<?php } ?>
				<th class="title">
					<?php echo acymailing_gridSort(acymailing_translation('URL'), 'c.name', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value); ?>
				</th>
				<th class="title">
					<?php echo acymailing_gridSort(acymailing_translation('ACY_USER'), 'd.email', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value); ?>
				</th>
				<th class="title titletoggle">
					<?php echo acymailing_gridSort(acymailing_translation('TOTAL_HITS'), 'a.click', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value); ?>
				</th>
			</tr>
			</thead>
			<tfoot>
			<tr>
				<td colspan="10">
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
				$id = 'urlclick'.$i;
				?>
				<tr class="<?php echo "row$k"; ?>" id="<?php echo $id; ?>">
					<td align="center" style="text-align:center">
						<?php echo $this->pagination->getRowOffset($i); ?>
					</td>
					<td align="center" style="text-align:center">
						<?php echo acymailing_getDate($row->date); ?>
					</td>
					<?php if(empty($selectedMail)){ ?>
						<td align="center" style="text-align:center">
							<?php
							$text = '<b>'.acymailing_translation('ACY_ID', true).' : </b>'.$row->mailid;
							echo acymailing_tooltip($text, $row->subject, '', $row->subject);
							?>
						</td>
					<?php } ?>
					<td align="center" style="text-align:center">
						<?php if(strlen(strip_tags($row->urlname)) > 50) $row->urlname = substr($row->urlname, 0, 20).'...'.substr($row->urlname, -20); ?>
						<a target="_blank" href="<?php echo acymailing_absoluteURL(strip_tags($row->url)); ?>"><?php echo $row->urlname; ?></a>
					</td>
					<td align="center" style="text-align:center">
						<?php
						$text = '<b>'.acymailing_translation('JOOMEXT_NAME', true).' : </b>'.$row->name;
						$text .= '<br /><b>'.acymailing_translation('JOOMEXT_EMAIL', true).' : </b>'.$row->email;
						$text .= '<br /><b>'.acymailing_translation('ACY_ID', true).' : </b>'.$row->subid;
						echo acymailing_tooltip($text, $row->email, '', $row->email);
						?>
					</td>
					<td align="center" style="text-align:center">
						<?php echo $row->click; ?>
					</td>
				</tr>
				<?php
				$k = 1 - $k;
			}
			?>
			</tbody>
		</table>

		<input type="hidden" name="defaulttask" value="detaillisting"/>
		<?php if(acymailing_getVar('int', 'listid')){ ?> <input type="hidden" name="listid" value="<?php echo acymailing_getVar('int', 'listid') ?>" /><?php } ?>
		<?php acymailing_formOptions($this->pageInfo->filter->order); ?>
	</form>
</div>

