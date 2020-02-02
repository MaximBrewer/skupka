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
	<?php $saveOrder = $this->pageInfo->filter->order->value == 'a.ordering' && strtolower($this->pageInfo->filter->order->dir) == 'asc'; ?>
	<form action="<?php echo acymailing_completeLink('action'); ?>" method="post" name="adminForm" id="adminForm">
		<table class="acymailing_table_options">
			<tr>
				<td width="100%">
					<?php acymailing_listingsearch($this->pageInfo->search); ?>
				</td>
				<td nowrap="nowrap">
					<?php echo $this->filters->creator; ?>
				</td>
			</tr>
		</table>

		<table class="acymailing_table" cellpadding="1" id="actionListing">
			<thead>
			<tr>
				<th class="title titlenum">
					<?php echo acymailing_translation('ACY_NUM'); ?>
				</th>
				<th class="title titleorder" style="width:32px !important; padding-left:1px; padding-right:1px;">
					<?php echo acymailing_gridSort('<i class="icon-menu-2"></i>', 'a.ordering', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
				</th>
				<th class="title titlebox">
					<input type="checkbox" name="toggle" value="" onclick="acymailing.checkAll(this);"/>
				</th>
				<th class="title">
					<?php echo acymailing_gridSort(acymailing_translation('ACY_NAME'), 'a.name', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value); ?>
				</th>
				<th class="title">
					<?php echo acymailing_gridSort(acymailing_translation('EMAIL_ADDRESS'), 'a.username', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value); ?>
				</th>
				<th class="title titlesender">
					<?php echo acymailing_gridSort(acymailing_translation('CREATOR'), 'd.'.$this->cmsUserVars->name, $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value); ?>
				</th>
				<th class="title titletoggle">
					<?php echo acymailing_gridSort(acymailing_translation('ENABLED'), 'a.published', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value); ?>
				</th>
				<th class="title titleid">
					<?php echo acymailing_gridSort(acymailing_translation('ACY_ID'), 'a.action_id', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value); ?>
				</th>
			</tr>
			</thead>
			<tfoot>
			<tr>
				<td colspan="8">
					<?php echo $this->pagination->getListFooter();
					echo $this->pagination->getResultsCounter(); ?>
				</td>
			</tr>
			</tfoot>
			<tbody id="acymailing_sortable_listing">
			<?php
			$k = 0;
			$ordering = '';

			for($i = 0, $a = count($this->rows); $i < $a; $i++){
				$row =& $this->rows[$i];
				$ordering .= ',"order['.$i.']='.$row->ordering.'"';

				$publishedid = 'published_'.$row->action_id;
				?>
				<tr class="<?php echo "row$k"; ?>" acyorderid="<?php echo $row->action_id; ?>">
					<td align="center" style="text-align:center">
						<?php echo $this->pagination->getRowOffset($i); ?>
					</td>
					<?php $iconClass = 'acyicon-draghandle';
					if(!$saveOrder) $iconClass .= ' acyinactive-handler" title="Sort the listing by ordering first'; ?>
					<td class="<?php echo $iconClass; ?>"><img alt="" src="<?php echo ACYMAILING_IMAGES; ?>icons/drag.png" /></td>
					<td align="center" style="text-align:center">
						<?php echo acymailing_gridID($i, $row->action_id); ?>
					</td>
					<td>
						<?php echo acymailing_tooltip($row->description, $row->name, 'tooltip.png', $row->name, acymailing_completeLink('action&task=edit&action_id='.$row->action_id)); ?>
					</td>
					<td>
						<?php
						if(!empty($row->username)) {
							echo acymailing_punycode($row->username, 'emailToUTF8');
						}else{
							echo $row->username;
						}
						?>
					</td>
					<td align="center" style="text-align:center">
						<?php
						if(!empty($row->userid)){
							$text = '<strong>'.acymailing_translation('JOOMEXT_NAME').' : </strong>'.$row->creatorname;
							$text .= '<br /><strong>'.acymailing_translation('ACY_USERNAME').' : </strong>'.$row->creatorusername;
							$text .= '<br /><strong>'.acymailing_translation('JOOMEXT_EMAIL').' : </strong>'.$row->email;
							$text .= '<br /><strong>'.acymailing_translation('ACY_ID').' : </strong>'.$row->userid;
							echo acymailing_tooltip($text, $row->creatorname, 'tooltip.png', $row->creatorname, acymailing_userEditLink().$row->userid);
						}
						?>
					</td>
					<td align="center" style="text-align:center">
						<span id="<?php echo $publishedid ?>" class="spanloading"><?php echo $this->toggleClass->toggle($publishedid, $row->published, 'action') ?></span>
					</td>
					<td align="center" style="text-align:center">
						<?php echo $row->action_id; ?>
					</td>
				</tr>
				<?php
				$k = 1 - $k;
			}
			?>
			</tbody>
		</table>


		<?php if(!empty($this->Itemid)) echo '<input type="hidden" name="Itemid" value="'.$this->Itemid.'" />';
		acymailing_formOptions($this->pageInfo->filter->order); ?>
	</form>
</div>

<?php if($saveOrder) acymailing_sortablelist('action', ltrim($ordering, ',')); ?>
