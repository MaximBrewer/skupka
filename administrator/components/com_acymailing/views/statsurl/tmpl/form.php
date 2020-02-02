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
	<form action="<?php echo acymailing_completeLink((acymailing_isAdmin() ? '': 'front').'statsurl', true); ?>" method="post" name="adminForm" id="adminForm" autocomplete="off" enctype="multipart/form-data">
		<table class="adminform" cellspacing="1" width="100%">
			<tr>
				<td>
					<label for="name">
						<?php echo acymailing_translation('URL_NAME'); ?>
					</label>
				</td>
				<td>
					<input type="text" name="data[url][name]" id="name" class="inputbox" style="width:200px" value="<?php echo $this->escape(@$this->url->name); ?>"/>
				</td>
			</tr>
			<tr>
				<td>
					<label for="url">
						<?php echo acymailing_translation('URL'); ?>
					</label>
				</td>
				<td>
					<input type="text" name="data[url][url]" id="name" class="inputbox" style="width:200px" value="<?php echo $this->escape(@$this->url->url); ?>"/>
				</td>
			</tr>
		</table>
		<input type="hidden" name="cid[]" value="<?php echo $this->url->urlid; ?>"/>
		<?php acymailing_formOptions(); ?>
	</form>
</div>
