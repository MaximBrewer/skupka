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
	<form action="<?php echo acymailing_route('index.php'); ?>" method="post" name="adminForm" autocomplete="off" id="adminForm">
		<div>
			<?php if(!empty($this->lists)){ ?>
				<div class="onelineblockoptions">
					<span class="acyblocktitle"><?php echo acymailing_translation('NEWSLETTER_SENT_TO'); ?></span>
					<table class="adminlist table table-striped" cellspacing="1" align="center">
						<tbody>
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
						} ?>
						</tbody>
					</table>
					<?php
					if(!empty($this->mail->filter)){
						$filterClass = acymailing_get('class.filter');
						$resultFilters = $filterClass->displayFilters($this->mail->filter);
						if(!empty($resultFilters)){
							echo '<br />'.acymailing_translation('RECEIVER_LISTS').'<br />'.acymailing_translation('FILTER_ONLY_IF');
							echo '<ul><li>'.implode('</li><li>', $resultFilters).'</li></ul>';
						}
					} ?>
				</div>
				<div class="onelineblockoptions">
					<table class="acymailing_table">
						<tr>
							<td class="acykey">
								<?php echo acymailing_translation('SEND_DATE'); ?>
							</td>
							<td>
								<?php echo acymailing_calendar(acymailing_getDate(time(), '%Y-%m-%d'), 'senddate', 'senddate', '%Y-%m-%d', array('style' => 'width:200px'));
								echo '@ '.$this->hours.' : '.$this->min;
								?>
							</td>
						</tr>
						<tr>
							<td>
							</td>
							<td>
								<button class="acymailing_button" onclick="document.adminForm.task.value='schedule';" type="submit"><?php echo acymailing_translation('SCHEDULE'); ?></button>
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

							$ajaxURL = acymailing_prepareAjaxURL((acymailing_isAdmin() ? '' : 'front').'stats').'&task=opendays&tmpl=component';
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
							reloadChart();
						</script>
					</div>
					<?php
				}
			}else{
				echo acymailing_display(acymailing_translation('EMAIL_AFFECT'), 'warning');
			} ?>

		</div>
		<div class="clr"></div>
		<input type="hidden" name="mailid" value="<?php echo $this->mail->mailid; ?>"/>
		<input type="hidden" name="listid" value="<?php echo acymailing_getVar('int', 'listid'); ?>"/>
		<?php acymailing_formOptions(); ?>
	</form>
</div>
