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
	<form action="<?php echo acymailing_completeLink('campaign'); ?>" method="post" name="adminForm" id="adminForm">
		<table class="acymailing_table_options">
			<tr>
				<td width="100%">
					<?php acymailing_listingsearch($this->pageInfo->search); ?>
				</td>
				<td nowrap="nowrap">
				</td>
			</tr>
		</table>

		<table class="acymailing_table acymailing_campaigns_listing" cellpadding="1">
			<thead>
			<tr>
				<th class="title titlenum">
					<?php echo acymailing_translation('ACY_NUM'); ?>
				</th>
				<th class="title titlebox">
					<input type="checkbox" name="toggle" value="" onclick="acymailing.checkAll(this);"/>
				</th>
				<th class="title">
					<?php echo acymailing_gridSort(acymailing_translation('ACY_TITLE'), 'a.name', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value); ?>
				</th>
				<th class="title">
					<?php echo acymailing_gridSort(acymailing_translation('ACY_DESCRIPTION'), 'a.description', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value); ?>
				</th>
				<th class="title">
					<?php echo acymailing_translation('FOLLOWUP'); ?>
				</th>
				<th class="title titletoggle">
					<?php echo acymailing_gridSort(acymailing_translation('ENABLED'), 'a.published', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value); ?>
				</th>
				<th class="title titleid">
					<?php echo acymailing_gridSort(acymailing_translation('ACY_ID'), 'a.listid', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value); ?>
				</th>
			</tr>
			</thead>
			<tfoot>
			<tr>
				<td colspan="7">
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
				$publishedid = 'published_'.$row->listid;
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td align="center" style="text-align:center" valign="top">
						<?php echo $this->pagination->getRowOffset($i); ?>
					</td>
					<td align="center" style="text-align:center" valign="top">
						<?php echo acymailing_gridID($i, $row->listid); ?>
					</td>
					<td valign="top">
						<a href="<?php echo acymailing_completeLink('campaign&task=edit&listid='.$row->listid); ?>">
							<?php echo acymailing_dispSearch($row->name, $this->pageInfo->search); ?>
						</a>
					</td>
					<td valign="top">
						<?php echo $row->description; ?>
					</td>
					<td style="text-align:center">
						<a href="<?php echo acymailing_completeLink('followup&task=add&campaign='.$row->listid) ?>" class="hasTooltip" data-original-title="<?php echo acymailing_translation('FOLLOWUP_ADD', true) ?>"><i class="acyicon-new" alt="<?php echo acymailing_translation('FOLLOWUP_ADD', true); ?>"></i></a>
						<?php echo acymailing_translation_sprintf('NUM_FOLLOWUP_CAMPAIGN', '<span id="followupCount_'.$row->listid.'">'.count($row->followup).'</span>');
						$refreshCount = 'document.getElementById(\'followupCount_'.$row->listid.'\').innerHTML=document.getElementById(\'followupCount_'.$row->listid.'\').innerHTML-1;';
						if(!empty($row->followup)){
							echo '<div class="acyblockoptions"><table width="100%" style="padding-left:50px">';
							foreach($row->followup as $oneFollow){
								$oneFollow->subject = acyEmoji::Decode($oneFollow->subject);
								$publishedidfollow = 'published_'.$oneFollow->mailid.'_followup';
								$iddelete = 'followup_'.$oneFollow->mailid;
								$copyButton = '<a href="'.acymailing_completeLink('followup&task=copy&followupid='.$oneFollow->mailid.'&'.acymailing_getFormToken()).'"><i class="hasTooltip acyicon-copy" data-original-title="'.acymailing_translation('ACY_COPY').'" alt="'.acymailing_translation('ACY_COPY').'" title="'.acymailing_translation('ACY_COPY').'"></i></a>';
								$statButton = '<span class="acystatsbutton" style="margin-right:12px;">&nbsp;</span>';
								if(acymailing_isAllowed($this->config->get('acl_statistics_manage', 'all'))){
									$urlStat = acymailing_completeLink('diagram&task=mailing&mailid='.$oneFollow->mailid, true);
									$statButton = '<span class="acystatsbutton">'.acymailing_popup($urlStat, '<i class="hasTooltip acyicon-statistic" data-original-title="'.acymailing_translation('STATISTICS', true).'" alt="'.acymailing_translation('STATISTICS', true).'" ></i>').'</span>';
								}
								echo '<tr id="'.$iddelete.'"><td width="100px" align="right">'.$this->delay->display($oneFollow->senddate).'</td><td width="50%" align="left"><span id="'.$publishedidfollow.'" class="spanloading" style="padding:2px 20px;width:65px;white-space: nowrap">'.$this->toggleClass->display('published', (int)$oneFollow->published).'</span><a title="'.acymailing_translation('ACY_EDIT', true).'" href="'.acymailing_completeLink('followup&task=edit&campaign='.$row->listid.'&mailid=').$oneFollow->mailid.'">'.$oneFollow->subject.'</a></td><td class="titletoggle" align="center">'.$statButton.' '.$copyButton.' '.$this->toggleClass->delete($iddelete, $row->listid.'_'.$oneFollow->mailid, 'followup', true, '', $refreshCount).'</td></tr>';
							}
							echo '</table></div>';
						} ?>
					</td>
					<td align="center" style="text-align:center" valign="top">
						<span id="<?php echo $publishedid ?>" class="loading"><?php echo $this->toggleClass->toggle($publishedid, $row->published, 'list') ?></span>
					</td>
					<td align="center" style="text-align:center" valign="top">
						<?php echo $row->listid; ?>
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

