<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.10.7
 * @author	acyba.com
 * @copyright	(C) 2009-2019 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?><div id="page-security">
	<?php if(acymailing_level(1)){
		?>
		<div class="onelineblockoptions">
			<span class="acyblocktitle"><?php echo acymailing_translation('CAPTCHA'); ?></span>
			<table class="acymailing_table" cellspacing="1">
				<tr>
					<td class="acykey">
						<?php echo acymailing_translation('ENABLE_CATCHA'); ?>
					</td>
					<td>
						<?php
						$captchaClass = acymailing_get('class.acycaptcha');
						$captchaPlugins = $captchaClass->getCaptchaPlugins(false);
						$captchaEnabled = ($this->config->get('captcha_plugin') == 'no') ? 0 : 1;
						echo acymailing_select($captchaPlugins, "config[captcha_plugin]", 'onChange="updateCaptcha(this.value)"', 'value', 'text', $this->config->get('captcha_plugin'));
						echo '<input type="hidden" id="captcha_enabled" name="config[captcha_enabled]" value="'.$captchaEnabled.'">';

						$js = "function updateCaptcha(newvalue){
								if(newvalue == 'no'){
									window.document.getElementById('captcha_enabled').value = 0;
								}else{
									window.document.getElementById('captcha_enabled').value = 1;
								}
								if(newvalue != 'acycaptcha') {
									window.document.getElementById('captchafield').style.display = 'none';
								}else{
									window.document.getElementById('captchafield').style.display = '';
								}
								if(newvalue != 'acyrecaptcha') {
									window.document.getElementById('recaptchafield').style.display = 'none';
								}else{
									window.document.getElementById('recaptchafield').style.display = '';
								}
							}";
						$js .= 'document.addEventListener("DOMContentLoaded", function(){updateCaptcha("'.$this->config->get('captcha_plugin').'")});';

						acymailing_addScript(true, $js);
						?>
					</td>
				</tr>
				<tr>
					<td class="acykey">
						<?php $secKey = $this->config->get('security_key');
						if(empty($secKey)){
							$secKey = acymailing_generateKey(30);
						}
						echo acymailing_tooltip(acymailing_translation_sprintf('SECURITY_KEY_DESC', '&seckey='.$secKey), acymailing_translation('SECURITY_KEY'), '', acymailing_translation('SECURITY_KEY')); ?>
					</td>
					<td>
						<input class="inputbox" type="text" name="config[security_key]" style="width:300px" value="<?php echo $this->escape($secKey); ?>"/>
					</td>
				</tr>
			</table>
			<div id="recaptchafield" width="100%" class="acymailing_deploy">
				<table width="100%">
					<tr>
						<td colspan="2">
							<table class="acymailing_table" cellspacing="1">
								<tr>
									<td class="acykey">
										<a target="_blank" href="https://www.google.com/recaptcha/admin"><?php echo acymailing_translation('ACY_SITE_KEY'); ?></a>
									</td>
									<td>
										<input class="inputbox" type="text" name="config[recaptcha_sitekey]" style="width:350px" value="<?php echo $this->escape($this->config->get('recaptcha_sitekey', '')); ?>"/>
									</td>
								</tr>
								<tr>
									<td class="acykey">
										<a target="_blank" href="https://www.google.com/recaptcha/admin"><?php echo acymailing_translation('ACY_SECRET_KEY'); ?></a>
									</td>
									<td>
										<input class="inputbox" type="text" name="config[recaptcha_secretkey]" style="width:350px" value="<?php echo $this->escape($this->config->get('recaptcha_secretkey', '')); ?>"/>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</div>
			<div id="captchafield" width="100%" class="acymailing_deploy">
				<table width="100%">
					<tr>
						<td colspan="2">
							<table class="acymailing_table" cellspacing="1">
								<tr>
									<td class="acykey">
										<?php echo acymailing_tooltip(acymailing_translation('CAPTCHA_CHARS_DESC'), acymailing_translation('CAPTCHA_CHARS'), '', acymailing_translation('CAPTCHA_CHARS')); ?>
									</td>
									<td>
										<input class="inputbox" type="text" name="config[captcha_chars]" style="width:450px" value="<?php echo $this->escape($this->config->get('captcha_chars', 'abcdefghijkmnpqrstwxyz23456798ABCDEFGHJKLMNPRSTUVWXYZ')); ?>"/>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td width="50%">
							<div class="acyblockoptions">
								<span class="acyblocktitle"><?php echo acymailing_translation('MODULE_VIEW'); ?></span>
								<table class="acymailing_table" cellspacing="1">
									<tr>
										<td class="acykey">
											<?php echo acymailing_translation('CAPTCHA_NBCHAR'); ?>
										</td>
										<td>
											<input class="inputbox" type="text" name="config[captcha_nbchar_module]" style="width:50px" value="<?php echo intval($this->config->get('captcha_nbchar_module', 3)); ?>"/>
										</td>
									</tr>
									<tr>
										<td class="acykey">
											<?php echo acymailing_translation('CAPTCHA_HEIGHT'); ?>
										</td>
										<td>
											<input class="inputbox" type="text" name="config[captcha_height_module]" style="width:100px" value="<?php echo intval($this->config->get('captcha_height_module', 25)); ?>"/>
										</td>
									</tr>
									<tr>
										<td class="acykey">
											<?php echo acymailing_translation('CAPTCHA_WIDTH'); ?>
										</td>
										<td>
											<input class="inputbox" type="text" name="config[captcha_width_module]" style="width:100px" value="<?php echo intval($this->config->get('captcha_width_module', 60)); ?>"/>
										</td>
									</tr>
									<tr>
										<td class="acykey">
											<?php echo acymailing_translation('CAPTCHA_BACKGROUND'); ?>
										</td>
										<td>
											<?php echo $this->elements->colortype->displayAll('captcha_background_module', 'config[captcha_background_module]', $this->config->get('captcha_background_module', '#ffffff')); ?>
										</td>
									</tr>
									<tr>
										<td class="acykey">
											<?php echo acymailing_translation('CAPTCHA_COLOR'); ?>
										</td>
										<td>
											<?php echo $this->elements->colortype->displayAll('captcha_color_module', 'config[captcha_color_module]', $this->config->get('captcha_color_module', '#bbbbbb')); ?>
										</td>
									</tr>
								</table>
							</div>
						</td>
						<td>
							<div class="acyblockoptions">
								<span class="acyblocktitle"><?php echo acymailing_translation('COMPONENT_VIEW'); ?></span>
								<table class="acymailing_table" cellspacing="1">
									<tr>
										<td class="acykey">
											<?php echo acymailing_translation('CAPTCHA_NBCHAR'); ?>
										</td>
										<td>
											<input class="inputbox" type="text" name="config[captcha_nbchar_component]" style="width:50px" value="<?php echo intval($this->config->get('captcha_nbchar_component', 6)); ?>"/>
										</td>
									</tr>
									<tr>
										<td class="acykey">
											<?php echo acymailing_translation('CAPTCHA_HEIGHT'); ?>
										</td>
										<td>
											<input class="inputbox" type="text" name="config[captcha_height_component]" style="width:100px" value="<?php echo intval($this->config->get('captcha_height_component', 25)); ?>"/>
										</td>
									</tr>
									<tr>
										<td class="acykey">
											<?php echo acymailing_translation('CAPTCHA_WIDTH'); ?>
										</td>
										<td>
											<input class="inputbox" type="text" name="config[captcha_width_component]" style="width:100px" value="<?php echo intval($this->config->get('captcha_width_component', 120)); ?>"/>
										</td>
									</tr>
									<tr>
										<td class="acykey">
											<?php echo acymailing_translation('CAPTCHA_BACKGROUND'); ?>
										</td>
										<td>
											<?php echo $this->elements->colortype->displayAll('captcha_background_component', 'config[captcha_background_component]', $this->config->get('captcha_background_component', '#ffffff')); ?>
										</td>
									</tr>
									<tr>
										<td class="acykey">
											<?php echo acymailing_translation('CAPTCHA_COLOR'); ?>
										</td>
										<td>
											<?php echo $this->elements->colortype->displayAll('captcha_color_component', 'config[captcha_color_component]', $this->config->get('captcha_color_component', '#bbbbbb')); ?>
										</td>
									</tr>
								</table>
							</div>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<?php
	}else{ ?>
		<div class="onelineblockoptions">
			<span class="acyblocktitle"><?php echo acymailing_translation('CAPTCHA'); ?></span>
			<table class="acymailing_table" cellspacing="1">
				<tr>
					<td class="acykey">
						<?php echo acymailing_translation('ENABLE_CATCHA'); ?>
					</td>
					<td>
						<?php echo acymailing_getUpgradeLink('essential'); ?>
					</td>
				</tr>
			</table>
		</div>
	<?php } ?>

	<div class="onelineblockoptions">
		<span class="acyblocktitle"><?php echo acymailing_translation('ADVANCED_EMAIL_VERIFICATION'); ?></span>
		<table class="acymailing_table" cellspacing="1">
			<tr>
				<td class="acykey">
					<?php echo acymailing_translation('CHECK_DOMAIN_EXISTS'); ?>
				</td>
				<td>
					<?php
					if(function_exists('getmxrr')){
						echo acymailing_boolean("config[email_checkdomain]", '', $this->config->get('email_checkdomain', 0));
					}else{
						echo 'Function getmxrr not enabled';
					}
					?>
				</td>
			</tr>
			<tr>
				<td class="acykey">
					<?php echo acymailing_translation_sprintf('X_INTEGRATION', 'BotScout'); ?>
				</td>
				<td>
					<?php echo acymailing_boolean("config[email_botscout]", '', $this->config->get('email_botscout', 0)); ?>
					<br/>API Key: <input class="inputbox" type="text" name="config[email_botscout_key]" style="width:100px;float:none;" value="<?php echo $this->escape($this->config->get('email_botscout_key')) ?>"/>
				</td>
			</tr>
			<tr>
				<td class="acykey">
					<?php echo acymailing_translation_sprintf('X_INTEGRATION', 'StopForumSpam'); ?>
				</td>
				<td>
					<?php echo acymailing_boolean("config[email_stopforumspam]", '', $this->config->get('email_stopforumspam', 0)); ?>
				</td>
			</tr>
			<tr>
				<td class="acykey">
					<?php echo acymailing_tooltip(acymailing_translation('IPTIMECHECK_DESC'), acymailing_translation('IPTIMECHECK'), '', acymailing_translation('IPTIMECHECK')); ?>
				</td>
				<td>
					<?php echo acymailing_boolean("config[email_iptimecheck]", '', $this->config->get('email_iptimecheck', 0)); ?>
				</td>
			</tr>
		</table>
	</div>

	<div class="onelineblockoptions">
		<span class="acyblocktitle"><?php echo acymailing_translation('ACY_FILES'); ?></span>
		<table class="acymailing_table" cellspacing="1">
			<tr>
				<td class="acykey">
					<?php echo acymailing_tooltip(acymailing_translation('ALLOWED_FILES_DESC'), acymailing_translation('ALLOWED_FILES'), '', acymailing_translation('ALLOWED_FILES')); ?>
				</td>
				<td>
					<input class="inputbox" type="text" name="config[allowedfiles]" style="width:250px" value="<?php echo $this->escape(strtolower(str_replace(' ', '', $this->config->get('allowedfiles')))); ?>"/>
				</td>
			</tr>
			<tr>
				<td class="acykey">
					<?php echo acymailing_tooltip(acymailing_translation('UPLOAD_FOLDER_DESC'), acymailing_translation('UPLOAD_FOLDER'), '', acymailing_translation('UPLOAD_FOLDER')); ?>
				</td>
				<td>
					<?php $uploadfolder = $this->config->get('uploadfolder');
					if(empty($uploadfolder)) $uploadfolder = ACYMAILING_MEDIA_FOLDER.'/upload'; ?>
					<input class="inputbox" type="text" name="config[uploadfolder]" style="width:250px" value="<?php echo $this->escape($uploadfolder); ?>"/>
				</td>
			</tr>
			<tr>
				<td class="acykey">
					<?php echo acymailing_tooltip(acymailing_translation('MEDIA_FOLDER_DESC'), acymailing_translation('MEDIA_FOLDER'), '', acymailing_translation('MEDIA_FOLDER')); ?>
				</td>
				<td>
					<?php $mediafolder = $this->config->get('mediafolder', ACYMAILING_MEDIA_FOLDER.'/upload');
					if(empty($mediafolder)) $mediafolder = ACYMAILING_MEDIA_FOLDER.'/upload'; ?>
					<input class="inputbox" type="text" name="config[mediafolder]" style="width:250px" value="<?php echo $this->escape($mediafolder); ?>"/>
				</td>
			</tr>
		</table>
	</div>
	<div class="onelineblockoptions">
		<span class="acyblocktitle"><?php echo acymailing_translation('DATABASE_MAINTENANCE'); ?></span>
		<table class="acymailing_table" cellspacing="1">
			<?php if(acymailing_level(1)){ ?>
				<tr>
					<td class="acykey">
						<?php echo acymailing_tooltip(acymailing_translation('DATABASE_MAINTENANCE_DESC').'<br />'.acymailing_translation('DATABASE_MAINTENANCE_DESC2'), acymailing_translation('DELETE_DETAILED_STATS'), '', acymailing_translation('DELETE_DETAILED_STATS')); ?>
					</td>
					<td>
						<?php echo $this->elements->delete_stats; ?>
					</td>
				</tr>
				<tr>
					<td class="acykey">
						<?php echo acymailing_tooltip(acymailing_translation('DATABASE_MAINTENANCE_DESC').'<br />'.acymailing_translation('DATABASE_MAINTENANCE_DESC2'), acymailing_translation('DELETE_HISTORY'), '', acymailing_translation('DELETE_HISTORY')); ?>
					</td>
					<td>
						<?php echo $this->elements->delete_history; ?>
					</td>
				</tr>
			<?php } ?>
			<?php if(acymailing_level(3)){ ?>
				<tr>
					<td class="acykey">
						<?php echo acymailing_tooltip(acymailing_translation('ACY_DELETE_CHARTS_DESC'), acymailing_translation('ACY_DELETE_CHARTS'), '', acymailing_translation('ACY_DELETE_CHARTS')); ?>
					</td>
					<td>
						<?php echo $this->elements->delete_charts; ?>
					</td>
				</tr>
			<?php } ?>
			<tr>
				<td class="acykey">
					<?php echo acymailing_translation('DATABASE_INTEGRITY'); ?>
				</td>
				<td>
					<?php echo $this->elements->checkDB; ?>
				</td>
			</tr>
		</table>
	</div>
</div>
