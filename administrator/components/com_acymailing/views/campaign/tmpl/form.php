<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.10.7
 * @author	acyba.com
 * @copyright	(C) 2009-2019 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?><style type="text/css">
	.campaignarea{
		float: left;
		max-width: 700px;
		width: 100%;
		display: inline-table;
	}
</style>

<div id="acy_content">
	<div id="iframedoc"></div>
	<form action="<?php echo acymailing_completeLink('campaign'); ?>" method="post" name="adminForm" autocomplete="off" id="adminForm">
		<div style="display:block; float:left; max-width: 800px;">
			<div class="acyblockoptions campaignarea">
				<span class="acyblocktitle"><?php echo acymailing_translation('ACY_CAMPAIGN_INFORMATIONS'); ?></span>
				<table cellspacing="1" width="100%">
					<tr>
						<td class="acykey" style="width:200px;">
							<label for="name">
								<?php echo acymailing_translation('ACY_TITLE'); ?>
							</label>
						</td>
						<td>
							<input type="text" name="data[list][name]" id="name" class="inputbox" style="width:200px" value="<?php echo $this->escape(@$this->list->name); ?>"/>
						</td>
					</tr>
					<tr>
						<td class="acykey">
							<label for="activated">
								<?php echo acymailing_translation('ENABLED'); ?>
							</label>
						</td>
						<td>
							<?php echo acymailing_boolean("data[list][published]", '', $this->list->published); ?>
						</td>
					</tr>
					<tr>
						<td class="acykey">
							<label>
								<?php echo acymailing_translation('ACY_START_CAMPAIGN'); ?>
							</label>
						</td>
						<td>
							<?php echo acymailing_select($this->startoptions, "data[list][startrule]", 'size="1" style="width:auto;" ', 'value', 'text', (string)$this->list->startrule); ?>
						</td>
					</tr>
				</table>
			</div>

			<div class="acyblockoptions campaignarea">
				<span class="acyblocktitle"><?php echo acymailing_translation('ACY_DESCRIPTION'); ?></span>
				<?php echo $this->editor->display(); ?>
			</div>
		</div>
		<div class="acyblockoptions campaignarea">
			<span class="acyblocktitle"><?php echo acymailing_translation('LISTS'); ?></span>

			<span><?php echo acymailing_translation('CAMPAIGN_START') ?></span>
			<?php
			$currentPage = 'campaign';
			include_once(ACYMAILING_BACK.'views'.DS.'newsletter'.DS.'tmpl'.DS.'filter.lists.php');
			?>
		</div>

		<div style="clear:both;"></div>

		<input type="hidden" name="cid[]" value="<?php echo @$this->list->listid; ?>"/>
		<input type="hidden" name="data[list][type]" value="campaign"/>
		<?php acymailing_formOptions(); ?>
	</form>
</div>
