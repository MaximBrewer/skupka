<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author       Yannick Gaultier
 * @copyright    (c) Yannick Gaultier - Weeblr llc - 2016
 * @package      sh404SEF
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version      4.7.3.3292
 * @date        2016-03-15
 */

// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC'))
{
	die('Direct Access to this location is not allowed.');
}

$infoTabTitle = $this->updates->shouldUpdate ? ShlHtmlBs_Helper::label(JText::_('COM_SH404SEF_VERSION_INFO'), 'important')
	: JText::_('COM_SH404SEF_VERSION_INFO');

?>
	<ul class="nav nav-tabs" id="content-pane">
		<li class="active"><a data-toggle="tab"
		                      href="#qcontrol"><?php echo JText::_('COM_SH404SEF_QUICK_START'); ?></a></li>
		<li><a data-toggle="tab" href="#security"><?php echo JText::_('COM_SH404SEF_SEC_STATS_TITLE'); ?></a></li>
		<li><a data-toggle="tab" href="#infos"><?php echo $infoTabTitle; ?></a></li>
	</ul>

<?php
echo JHtml::_('bootstrap.startPane', 'content-pane', array('active' => 'qcontrol'));
echo JHtml::_('bootstrap.addPanel', 'content-pane', 'qcontrol');
?>
	<div id="qcontrolcontent" class="qcontrol">
	</div>

<?php

echo JHtml::_('bootstrap.endPanel');
// security stats
echo JHtml::_('bootstrap.addPanel', 'content-pane', 'security');

?>

	<div id="secstatscontent" class="secstats">

	</div>

<?php

echo JHtml::_('bootstrap.endPanel');
echo JHtml::_('bootstrap.addPanel', 'content-pane', 'infos');

?>
	<table class="table wbl-sh404sef-installed-version">
		<thead>
		<tr>
			<td class="center">
				<small><?php echo JText::_('COM_SH404SEF_INSTALLED_VERS'); ?></small>
				<?php if (!empty($this->sefConfig))
				{
					echo ShlHtmlBs_Helper::label($this->sefConfig->version, 'info');
				}
				else
				{
					echo 'Please review and save configuration first';
				}
				?>
			</td>
		</tr>
		</thead>
	</table>

	<div id="updatescontent" class="updates">

	</div>

<?php

echo JHtml::_('bootstrap.endPanel');
