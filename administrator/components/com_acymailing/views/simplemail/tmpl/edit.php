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
	<form id="adminForm" name="adminForm" method="post" action="<?php echo acymailing_completeLink('simplemail'); ?>">
		<div class="mail-information">
			<div class="acyblockoptions mail-part">
				<span><?php echo acymailing_translation('SIMPLE_SENDING_INTRO') ?></span>
			</div>
			<div class="acyblockoptions mail-part">
				<span class="acyblocktitle"><?php echo acymailing_translation('SIMPLE_SENDING_RECEIVERS') ?></span>
				<?php echo $this->testreceiverType->display($this->infos->test_selection, $this->infos->test_group, $this->infos->test_emails); ?>
			</div>
			<div class="acyblockoptions mail-part acyblock_newsletter" id="htmlfieldset">
				<span class="acyblocktitle"><?php echo acymailing_translation('SIMPLE_SENDING_CONTENT') ?></span>
				<div>
					<input onclick="zoneToTag='subject';" type="text" name="subject" id="subject" placeholder="<?php echo acymailing_translation('JOOMEXT_SUBJECT') ?>" value="<?php echo $this->subject ?>">
				</div>
				<?php echo $this->editor->display(); ?>
			</div>
		</div>
		<input type="hidden" id="tempid" name="tempid" value="<?php echo $this->tempid ?>"/>
		<?php acymailing_formOptions(); ?>
	</form>
</div>
