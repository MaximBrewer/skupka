<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     sh404SEF
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     4.7.3.3292
 * @date		2016-03-15
 */

// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC')) die('Direct Access to this location is not allowed.');

function j3displaySecLine($title, $ItemName, $shSecStats) {
  ?>
<tr>
  <td><?php echo $title; ?></td>
  <td class="shl-centered"><?php echo $shSecStats[$ItemName]; ?></td>
  <td class="shl-right"><?php
  echo sprintf('%1.1f',$shSecStats[$ItemName.'Pct']). ' %  |  '.sprintf("%05.1f",$shSecStats[$ItemName.'Hrs']).' '.JText::_('COM_SH404SEF_TOTAL_PER_HOUR').'&nbsp;';
  ?></td>
</tr>
  <?php
}

?>

<div class="sh404sef-secstats" id="sh404sef-secstats">

<!-- start security stats panel markup -->
<?php
echo ShlMvcLayout_Helper::render('com_sh404sef.general.message_block', $this);
?>
    <table class="table table-striped">

      <tr>
        <th class="cpanel" colspan="3"><?php echo JText::_('COM_SH404SEF_SEC_STATS_TITLE').': ';
        if ($this->sefConfig->shSecEnableSecurity) {
          echo $this->shSecStats['curMonth'];
          echo ' <a href="javascript: void(0);" onclick="javascript: shSetupSecStats(\'updatesecstats\');" >'
 				.ShlHtmlBs_Helper::button(JText::_('COM_SH404SEF_SEC_STATS_UPDATE'), $type= '', $size='small')
          .'</a>';
          echo '<small> ('.$this->shSecStats['lastUpdated'].')</small>';
        } else {
          echo JText::_('COM_SH404SEF_SEC_DEACTIVATED');
        }
        ?></th>
      </tr>
      <tr>
        <td ><b><?php echo JText::_('COM_SH404SEF_TOTAL_ATTACKS'); ?></b></td>
        <td class="shl-centered"><b><?php echo $this->shSecStats['totalAttacks']; ?></b>
        </td>
        <td class="shl-right"><?php echo sprintf('%5.1f',$this->shSecStats['totalAttacksHrs']).' '.JText::_('COM_SH404SEF_TOTAL_PER_HOUR').'&nbsp;'?>
        </td>
      </tr>
      <?php
        j3displaySecLine(JText::_('COM_SH404SEF_TOTAL_CONFIG_VARS'),'totalConfigVars', $this->shSecStats);
        j3displaySecLine(JText::_('COM_SH404SEF_TOTAL_BASE64'),'totalBase64', $this->shSecStats);
        j3displaySecLine(JText::_('COM_SH404SEF_TOTAL_SCRIPTS'),'totalScripts', $this->shSecStats);
        j3displaySecLine(JText::_('COM_SH404SEF_TOTAL_STANDARD_VARS'),'totalStandardVars', $this->shSecStats);
        j3displaySecLine(JText::_('COM_SH404SEF_TOTAL_IMG_TXT_CMD'),'totalImgTxtCmd', $this->shSecStats);
        j3displaySecLine(JText::_('COM_SH404SEF_TOTAL_IP_DENIED'),'totalIPDenied', $this->shSecStats);
        j3displaySecLine(JText::_('COM_SH404SEF_TOTAL_USER_AGENT_DENIED'),'totalUserAgentDenied', $this->shSecStats);
        j3displaySecLine(JText::_('COM_SH404SEF_TOTAL_FLOODING'),'totalFlooding', $this->shSecStats);
        j3displaySecLine(JText::_('COM_SH404SEF_TOTAL_PHP'),'totalPHP', $this->shSecStats);
        j3displaySecLine(JText::_('COM_SH404SEF_TOTAL_PHP_USER_CLICKED'),'totalPHPUserClicked', $this->shSecStats);
      ?>
  </table>

<!-- end security stats panel markup -->

</div>

