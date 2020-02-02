<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.10.7
 * @author	acyba.com
 * @copyright	(C) 2009-2019 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?><div id="acy_content" xmlns="http://www.w3.org/1999/html">
	<div id="iframedoc"></div>
	<form action="<?php echo acymailing_completeLink('send', true); ?>" method="post" name="adminForm" id="adminForm" autocomplete="off">
		<div>
			<?php if(!empty($this->lists)){ ?>
				<div class="onelineblockoptions">
					<span class="acyblocktitle"><?php echo acymailing_translation('NEWSLETTER_SENT_TO'); ?></span>
					<table class="acymailing_table">
						<?php
						$k = 0;
						foreach($this->lists as $row){
							?>
							<tr class="<?php echo "row$k"; ?>">
								<td>
									<?php
									echo acymailing_tooltip($row->description, $row->name, 'tooltip.png', $row->name);
									echo ' ( '.$row->nbsub.' )';
									?>
								</td>
							</tr>
							<?php
							$k = 1 - $k;
						}

						if(!empty($this->mail->filter)){
							$filterClass = acymailing_get('class.filter');
							$resultFilters = $filterClass->displayFilters($this->mail->filter);
							if(!empty($resultFilters)){
								echo '<br />'.acymailing_translation('RECEIVER_LISTS').'<br />'.acymailing_translation('FILTER_ONLY_IF');
								echo '<ul><li>'.implode('</li><li>', $resultFilters).'</li></ul>';
							}
						} ?>
					</table>
				</div>
				<?php
				if(!empty($this->alreadySent)){
					acymailing_display(acymailing_translation_sprintf('ALREADY_SENT', $this->alreadySent).'<br />'.acymailing_translation('REMOVE_ALREADY_SENT').'<br />'.acymailing_boolean("onlynew", '', 1, acymailing_translation('JOOMEXT_YES'), acymailing_translation('SEND_TO_ALL')), 'warning');
				}
				?>
				<div class="onelineblockoptions">
					<table class="acymailing_table">
						<tr>
							<td class="acykey">
								<?php echo acymailing_translation('SEND_DATE'); ?>
							</td>
							<td>
								<?php echo acymailing_calendar(acymailing_getDate(time(), '%Y-%m-%d'), 'senddate', 'senddate', '%Y-%m-%d', array('style' => 'width:100px; margin-right: 5px'));
								echo '&nbsp; @ '.$this->hours.' : '.$this->minutes; ?>
							</td>
						</tr>
						<tr>
							<td>
							</td>
							<td>
								<button onclick="document.adminForm.task.value='schedule';" class="acymailing_button" type="submit"><?php echo acymailing_translation('SCHEDULE'); ?></button>
							</td>
						</tr>
					</table>
				</div>
				<?php
				if(acymailing_level(3)){ ?>
					<div class="onelineblockoptions" style="text-align: center;">
						<span class="acyblocktitle" style="text-align: left;"><?php echo acymailing_translation('ACY_OPEN_DAY'); ?></span>
						<div style="text-align: left;">
							<?php
							$tagfieldtype = acymailing_get('type.tagfield');
							$tagfieldtype->onclick = 'reloadChart();';
							echo acymailing_translation('TAGS').': '.$tagfieldtype->display('tagselect', 'tags');

							$ajaxURL = acymailing_prepareAjaxURL((acymailing_isAdmin() ? '' : 'front').'stats').'&task=opendays';
							?>
						</div>
						<div id="chartbox" style="max-width:700px;margin:auto;margin-top:15px;"></div>
						<script language="JavaScript" type="text/javascript">
							google.load('visualization', '1', {packages: ['bar']});

							function reloadChart(){
								var tags = "";
								var tagselect = document.getElementById('tagselect');

								for (var i=0 ; i<tagselect.length ; i++) {
									if (tagselect[i].selected && tagselect[i].value.length > 0) {
										tags += tagselect[i].value+",";
									}
								}

								tags = tags.replace(/,+$/,'');
								
								var chart = document.getElementById('chartbox');
								chart.style.opacity = "0.5";

								var xhr = new XMLHttpRequest();
								xhr.open('GET', "<?php echo $ajaxURL; ?>&tags="+tags);
								xhr.onload = function(){
									chart.innerHTML = xhr.responseText;
									var arr = chart.getElementsByTagName('script')
									for (var n = 0; n < arr.length; n++) {
										eval(arr[n].innerHTML);
									}
									chart.style.opacity = "1";
								};
								xhr.send();
							}
							document.addEventListener("DOMContentLoaded", function(){
								reloadChart();
							});
						</script>
					</div>
				<?php
				}
			}else{
				echo acymailing_display(acymailing_translation('EMAIL_AFFECT'), 'warning');
			}
			?>
		</div>
		<div class="clr"></div>
		<input type="hidden" name="cid[]" value="<?php echo $this->mail->mailid; ?>"/>
		<?php acymailing_formOptions(); ?>
	</form>
</div>
