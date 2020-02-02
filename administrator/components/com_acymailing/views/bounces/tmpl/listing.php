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
	<form action="<?php echo acymailing_completeLink('bounces'); ?>" autocomplete="off" method="post" name="adminForm" id="adminForm">
		<div class="acyblockoptions" style="float:none; display: block;">
			<span class="acyblocktitle"><?php echo acymailing_translation('ACY_CONFIGURATION'); ?></span>
			<table style="width:100%;">
				<tr>
					<td>
						<table class="acymailing_table" cellspacing="1">
							<tr>
								<td class="acykey">
									<?php echo acymailing_translation('BOUNCE_ADDRESS'); ?>
								</td>
								<td>
									<?php echo $this->config->get('bounce_email'); ?>
								</td>
							</tr>
							<tr>
								<td class="acykey">
									<?php echo acymailing_translation('SMTP_SERVER'); ?>
								</td>
								<td>
									<input type="text" style="width:200px" name="config[bounce_server]" value="<?php echo $this->escape(trim($this->config->get('bounce_server', ''))); ?>"/>
								</td>
							</tr>
							<tr>
								<td class="acykey">
									<?php echo acymailing_translation('SMTP_PORT'); ?>
								</td>
								<td>
									<input type="text" style="width:50px" name="config[bounce_port]" value="<?php echo intval($this->config->get('bounce_port', '')); ?>"/>
								</td>
							</tr>
							<tr>
								<td class="acykey">
									<?php echo acymailing_translation('BOUNCE_CONNECTION'); ?>
								</td>
								<td>
									<?php echo $this->elements->bounce_connection; ?>
								</td>
							</tr>
							<tr>
								<td class="acykey">
									<?php echo acymailing_translation('SMTP_SECURE'); ?>
								</td>
								<td>
									<?php echo $this->elements->bounce_secured; ?>
								</td>
							</tr>
							<tr>
								<td class="acykey">
									<?php echo acymailing_translation('BOUNCE_CERTIF'); ?>
								</td>
								<td>
									<?php echo $this->elements->bounce_certif; ?>
								</td>
							</tr>
							<tr>
								<td class="acykey">
									<?php echo acymailing_translation('ACY_USERNAME'); ?>
								</td>
								<td>
									<?php
									$username = $this->config->get('bounce_username', '');
									if(!empty($username)) $username = acymailing_punycode($username, 'emailToUTF8');
									?>
									<input type="text" autocomplete="off" style="width:160px" name="config[bounce_username]" value="<?php echo $this->escape(trim($username)); ?>"/>
								</td>
							</tr>
							<tr>
								<td class="acykey">
									<?php echo acymailing_translation('SMTP_PASSWORD'); ?>
								</td>
								<td>
									<input type="text" autocomplete="off" style="width:160px" name="config[bounce_password]" value="<?php echo str_repeat('*', strlen($this->config->get('bounce_password'))); ?>"/>
								</td>
							</tr>
							<tr>
								<td class="acykey">
									<?php echo acymailing_translation('BOUNCE_TIMEOUT'); ?>
								</td>
								<td>
									<input type="text" style="width:50px" name="config[bounce_timeout]" value="<?php echo intval($this->config->get('bounce_timeout', '10')); ?>"/>
								</td>
							</tr>
							<tr>
								<td class="acykey">
									<?php echo acymailing_translation('BOUNCE_MAX_EMAIL'); ?>
								</td>
								<td>
									<input type="text" style="width:50px" name="config[bounce_max]" value="<?php echo intval($this->config->get('bounce_max', 100)); ?>"/>
								</td>
							</tr>
							<tr>
								<td class="acykey">
									<?php echo acymailing_translation('BOUNCE_FEATURE'); ?>
								</td>
								<td>
									<?php echo acymailing_boolean("config[auto_bounce]", 'onclick="displayBounceFrequency(this.value);"', $this->config->get('auto_bounce', 0)); ?>
								</td>
							</tr>
						</table>
					</td>
					<td valign="bottom" style="padding-left: 50px;">
						<table id="bouncefrequency" class="acymailing_table" cellspacing="1">
							<tr>
								<td class="acykey"><?php echo acymailing_translation('FREQUENCY'); ?></td>
								<td><?php $freqBounce = acymailing_get('type.delay');
									echo $freqBounce->display('config[auto_bounce_frequency]', $this->config->get('auto_bounce_frequency', 21600), 1); ?></td>
							</tr>
							<tr>
								<td class="acykey"><?php echo acymailing_translation('LAST_RUN'); ?></td>
								<td><?php echo acymailing_getDate($this->config->get('auto_bounce_last')); ?></td>
							</tr>
							<tr>
								<td class="acykey"><?php echo acymailing_translation('NEXT_RUN'); ?></td>
								<td><?php echo acymailing_getDate($this->config->get('auto_bounce_next')); ?></td>
							</tr>
							<tr>
								<td class="acykey"><?php echo acymailing_translation('REPORT'); ?></td>
								<td><?php echo $this->config->get('auto_bounce_report'); ?></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</div>
		<?php
		$acyToolbar = acymailing_get('helper.toolbar');
		$acyToolbar->htmlclass = 'bounce_menu';
		$acyToolbar->add();
		$acyToolbar->edit();
		$acyToolbar->delete();
		$acyToolbar->topfixed = false;
		$acyToolbar->display();
		?>
		<div class="acyblockoptions" style="display: block; float: none;">
			<span class="acyblocktitle"><?php echo acymailing_translation('BOUNCE_RULES'); ?></span>
			<table class="acymailing_table" cellspacing="1" id="bounceListing">
				<thead>
				<tr>
					<th class="title titlenum">
						<?php echo acymailing_translation('ACY_NUM'); ?>
					</th>
					<th class="title titleorder">
						<?php echo '<i class="icon-menu-2"></i>'; ?>
					</th>
					<th class="title titlebox">
						<input type="checkbox" name="toggle" value="" onclick="acymailing.checkAll(this);"/>
					</th>
					<th class="title">
						<?php echo acymailing_translation('ACY_NAME'); ?>
					</th>
					<th>
						<?php echo acymailing_translation('BOUNCE_ACTION'); ?>
					</th>
					<th>
						<?php echo acymailing_translation('EMAIL_ACTION'); ?>
					</th>
					<th class="title titletoggle">
						<?php echo acymailing_translation('ENABLED'); ?>
					</th>
					<th class="title titleid">
						<?php echo acymailing_translation('ACY_ID'); ?>
					</th>
				</tr>
				</thead>
				<tbody id="acymailing_sortable_listing">
				<?php
				$k = 0;
				$ordering = '';

				for($i = 0, $a = count($this->rows); $i < $a; $i++){
					$row =& $this->rows[$i];
					$ordering .= ',"order['.$i.']='.$row->ordering.'"';

					$publishedid = 'published_'.$row->ruleid;
					?>
					<tr class="<?php echo "row$k"; ?>" acyorderid="<?php echo $row->ruleid; ?>">
						<td align="center" style="text-align:center">
							<?php echo $i + 1; ?>
						</td>
						<td class="acyicon-draghandle"><img alt="" src="<?php echo ACYMAILING_IMAGES; ?>icons/drag.png" /></td>
						<td align="center" style="text-align:center">
							<?php echo acymailing_gridID($i, $row->ruleid); ?>
						</td>
						<td>
							<?php
							echo '<a href="'.acymailing_completeLink('bounces&task=edit&ruleid='.$row->ruleid).'" >'.acymailing_translation($row->name).'</a>'; ?>
						</td>
						<td>
							<?php if(isset($row->action_user['removesub'])) echo acymailing_translation('REMOVE_SUB').'<br />';
							if(isset($row->action_user['unsub'])) echo acymailing_translation('UNSUB_USER').'<br />';
							if(isset($row->action_user['sub'])) echo acymailing_translation('SUBSCRIBE_USER').' ( '.$this->lists[$row->action_user['subscribeto']]->name.' )<br />';
							if(isset($row->action_user['block'])) echo acymailing_translation('BLOCK_USER').'<br />';
							if(isset($row->action_user['delete'])) echo acymailing_translation('DELETE_USER');
							?>
						</td>
						<td>
							<?php if(isset($row->action_message['save'])) echo acymailing_translation('BOUNCE_SAVE_MESSAGE').'<br />';
							if(isset($row->action_message['delete'])) echo acymailing_translation('DELETE_EMAIL').'<br />';
							if(!empty($row->action_message['forwardto'])) echo acymailing_translation('FORWARD_EMAIL').' '.$row->action_message['forwardto'];

							?>
						</td>
						<td align="center" style="text-align:center">
							<span id="<?php echo $publishedid ?>" class="loading"><?php echo $this->toggleClass->toggle($publishedid, (int)$row->published, 'rules') ?></span>
						</td>
						<td align="center" style="text-align:center">
							<?php echo $row->ruleid; ?>
						</td>

					</tr>
					<?php
					$k = 1 - $k;
				}
				?>
				</tbody>
			</table>
		</div>

		<input type="hidden" name="boxchecked" value="0"/>
		<?php acymailing_formOptions(); ?>
	</form>
</div>

<?php acymailing_sortablelist('bounces', ltrim($ordering, ',')); ?>
