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
	<form action="<?php echo acymailing_completeLink('autonews'); ?>" method="post" name="adminForm" id="adminForm">
		<table class="acymailing_table_options">
			<tr>
				<td width="100%">
					<?php acymailing_listingsearch($this->pageInfo->search); ?>
				</td>
				<td nowrap="nowrap">
					<?php echo $this->filters->list; ?>
					<?php echo $this->filters->creator; ?>
					<?php echo $this->filters->tags; ?>
				</td>
			</tr>
		</table>

		<table class="acymailing_table" cellpadding="1">
			<thead>
			<tr>
				<th class="title titlenum">
					<?php echo acymailing_translation('ACY_NUM'); ?>
				</th>
				<th class="title titlebox">
					<input type="checkbox" name="toggle" value="" onclick="acymailing.checkAll(this);"/>
				</th>
				<th class="title">
					<?php echo acymailing_gridSort(acymailing_translation('JOOMEXT_SUBJECT'), 'a.subject', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value); ?>
				</th>
				<th class="title titledate">
					<?php echo acymailing_gridSort(acymailing_translation('NEXT_GENERATE'), 'a.senddate', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value); ?>
				</th>
				<th class="title titledate">
					<?php echo acymailing_gridSort(acymailing_translation('FREQUENCY'), 'a.frequency', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value); ?>
				</th>
				<th class="title titlesender">
					<?php echo acymailing_gridSort(acymailing_translation('SENDER_INFORMATIONS'), 'a.fromname', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value); ?>
				</th>
				<th class="title titlesender">
					<?php echo acymailing_gridSort(acymailing_translation('CREATOR'), 'b.name', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value); ?>
				</th>
				<th class="title titletoggle">
					<?php echo acymailing_gridSort(acymailing_translation('ACY_PUBLISHED'), 'a.published', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value); ?>
				</th>
				<th class="title titleid">
					<?php echo acymailing_gridSort(acymailing_translation('ACY_ID'), 'a.mailid', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value); ?>
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
			$i = 0;
			foreach($this->rows as &$row){
				$row->subject = acyEmoji::Decode($row->subject);

				$publishedid = 'published_'.$row->mailid;
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td align="center" style="text-align:center">
						<?php echo $this->pagination->getRowOffset($i); ?>
					</td>
					<td align="center" style="text-align:center">
						<?php echo acymailing_gridID($i, $row->mailid); ?>
					</td>
					<td>
						<a href="<?php echo acymailing_completeLink('autonews&task=edit&mailid='.$row->mailid); ?>">
							<?php echo acymailing_dispSearch($row->subject, $this->pageInfo->search); ?>
						</a>
					</td>
					<td align="center" style="text-align:center">
						<?php echo acymailing_getDate($row->senddate); ?>
					</td>
					<td align="center" style="text-align:center">
						<?php echo $this->frequencyType->display($row->frequency); ?>
					</td>
					<td align="center" style="text-align:center">
						<?php
						if(empty($row->fromname)) $row->fromname = $this->config->get('from_name');
						if(empty($row->fromemail)) $row->fromemail = $this->config->get('from_email');
						if(empty($row->replyname)) $row->replyname = $this->config->get('reply_name');
						if(empty($row->replyemail)) $row->replyemail = $this->config->get('reply_email');
						if(!empty($row->fromname)){
							$text = '<b>'.acymailing_translation('FROM_NAME').' : </b>'.$row->fromname;
							$text .= '<br /><b>'.acymailing_translation('FROM_ADDRESS').' : </b>'.$row->fromemail;
							$text .= '<br /><br /><b>'.acymailing_translation('REPLYTO_NAME').' : </b>'.$row->replyname;
							$text .= '<br /><b>'.acymailing_translation('REPLYTO_ADDRESS').' : </b>'.$row->replyemail;
							echo acymailing_tooltip($text, '', '', $row->fromname);
						}
						?>
					</td>
					<td align="center" style="text-align:center">
						<?php
						if(!empty($row->name)){
							$text = '<b>'.acymailing_translation('JOOMEXT_NAME').' : </b>'.$row->name;
							$text .= '<br /><b>'.acymailing_translation('ACY_USERNAME').' : </b>'.$row->username;
							$text .= '<br /><b>'.acymailing_translation('JOOMEXT_EMAIL').' : </b>'.$row->email;
							$text .= '<br /><b>'.acymailing_translation('USER_ID').' : </b>'.$row->userid;
							echo acymailing_tooltip($text, $row->name, '', $row->name, acymailing_userEditLink().$row->userid);
						}
						?>
					</td>
					<td align="center" style="text-align:center">
						<span id="<?php echo $publishedid ?>" class="spanloading"><?php echo $this->toggleClass->toggle($publishedid, (int) $row->published, 'mail') ?></span>
					</td>
					<td width="1%" align="center">
						<?php echo $row->mailid; ?>
					</td>
				</tr>
				<?php
				$k = 1 - $k;
				$i++;
			}
			?>
			</tbody>
		</table>

		<?php acymailing_formOptions($this->pageInfo->filter->order); ?>
	</form>
</div>
