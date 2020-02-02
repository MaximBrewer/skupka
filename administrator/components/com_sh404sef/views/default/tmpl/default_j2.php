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

?>

<div id="cpanel">

  <div id="left" class="cp-block">

    <?php

      // prepare Analytics output
      $sefConfig = & Sh404sefFactory::getConfig();
      try
      {
	      $haveAccessToken = Sh404sefHelperAnalytics_auth::getAccessToken();
	      $analyticsAvailable = Sh404sefHelperAcl::userCan('sh404sef.view.analytics') && $sefConfig->analyticsReportsEnabled && !empty($haveAccessToken);
      } catch (Exception $e)
      {
	      $analyticsAvailable = false;
      }

      $analyticsOutput =
        JHtml::_('tabs.panel', JText::_('COM_SH404SEF_ANALYTICS'), 'analytics')
      . ShlMvcLayout_Helper::render('com_sh404sef.analytics.' . $this->joomlaVersionPrefix . '_controlpanel_' . Sh404sefConfigurationEdition::$id);

      // start pane
      echo JHtml::_('tabs.start', 'left-pane', array('allowAllClose' => true, 'useCookie' => true));

      // if enabled, we display analytics panel, in first place
      if ($analyticsAvailable ) {
        echo $analyticsOutput;
      }

      // management icons
      echo JHtml::_('tabs.panel', JText::_('COM_SH404SEF_MANAGEMENT'), 'management');

    ?>

        <fieldset class="adminform" id="sh404sef-management-area">
        <div class="iconothers">
          <?php echo Sh404sefHelperHtml::getCPImage( 'urlmanager'); ?>
        </div>
        <div class="iconothers">
          <?php echo Sh404sefHelperHtml::getCPImage( 'aliasesmanager'); ?>
        </div>
        <div class="iconothers">
          <?php echo Sh404sefHelperHtml::getCPImage( 'pageidmanager'); ?>
        </div>
        <div class="iconothers">
          <?php echo Sh404sefHelperHtml::getCPImage( '404manager'); ?>
        </div>
        <div class="iconothers">
          <?php echo Sh404sefHelperHtml::getCPImage( 'metamanager'); ?>
        </div>
        <div class="iconothers">
          <?php echo Sh404sefHelperHtml::getCPImage( 'analytics'); ?>
        </div>
        <div class="iconothers">
          <?php echo Sh404sefHelperHtml::getCPImage( 'doc'); ?>
        </div>
        </fieldset>

    <?php
    // if analytics data is not available, or not properly setup
    // the tab is still created, but at last position
    // if reports are set to not being displayed however
    // we don't display at all
    if (Sh404sefHelperAcl::userCan('sh404sef.view.analytics') && !$analyticsAvailable && $sefConfig->analyticsReportsEnabled) {
      echo $analyticsOutput;
    }

    echo JHtml::_('tabs.end');
    ?>

  </div>

  <div id="right" class="cp-block">

    <div id="right-bottom" class="cp-inner-block">

    <?php

      echo JHtml::_('tabs.start', 'content-pane', array('allowAllClose' => true));
      echo JHtml::_('tabs.panel', JText::_('COM_SH404SEF_QUICK_START'), 'qcontrol');

    ?>
      <div id="qcontrolcontent" class="qcontrol">
        <div class="sh-ajax-loading">&nbsp;</div>
      </div>

    <?php

      // security stats
      echo JHtml::_('tabs.panel', JText::_('COM_SH404SEF_SEC_STATS_TITLE'), 'security');

     ?>

    <div id="secstatscontent" class="secstats">

    </div>

    <?php

      $tabTitle = $this->updates->shouldUpdate ? '<b><font color="red">(!) ' . JText::_('COM_SH404SEF_VERSION_INFO') . '</font></b>' : JText::_('COM_SH404SEF_VERSION_INFO');

      echo JHtml::_('tabs.panel', $tabTitle, 'infos');

    ?>
    <table class="adminlist">
      <thead>
        <tr>
          <td width="130"><?php echo JText::_('COM_SH404SEF_INSTALLED_VERS') ;?></td>
          <td><?php if (!empty($this->sefConfig)) echo $this->sefConfig->version;
          else echo 'Please review and save configuration first'; ?></td>
        </tr>
      </thead>
      <tr>
        <td><?php echo JText::_('COM_SH404SEF_COPYRIGHT') ;?></td>
        <td><a href="https://weeblr.com/joomla-seo-analytics-security/sh404sef">&copy; 2006-<?php echo date('Y');?>
        Yannick Gaultier - Weeblr llc</a></td>
      </tr>
      <tr>
        <td><?php echo JText::_('COM_SH404SEF_LICENSE') ;?></td>
        <td><a
          href="https://weeblr.com/legal/licensing"
          target="_blank"><?php echo JText::_('COM_SH404SEF_SEE_LICENSE_AND_TERMS') ;?></a></td>
      </tr>
    </table>

    <div id="updatescontent" class="updates">

    </div>

      <?php

      // configuration and global stats
      $output = '';
      foreach ($this->sefConfig->fileAccessStatus as $file => $access) {
        if ($access == JText::_('COM_SH404SEF_UNWRITEABLE')) {
          $output .= '<tr><td>'.$file.'</td><td colspan="2">'.JText::_('COM_SH404SEF_UNWRITEABLE').'</td></tr>';
        }
      }
      if (!empty($output)) {
        $output =  '<th class="cpanel" colspan="3" >'.JText::_('COM_SH404SEF_NOACCESS').'</th>' . $output;
      }

      // ad red on tab title if something special
      if (!empty($output) || $this->sefConfig->debugToLogFile) {
        $tabTitle = '<b><font color="red">(!) ' . JText::_('COM_SH404SEF_ACCESS_URLS_STATS') . '</font></b>';
      } else {
        $tabTitle = JText::_('COM_SH404SEF_ACCESS_URLS_STATS');
      }

      echo JHtml::_('tabs.panel', $tabTitle, 'stats');
    ?>
    <table class="adminform" width="100%">
      <tr>
      <?php
        if (!empty($output)) {
          echo $output;
        }
        if ($this->sefConfig->debugToLogFile) {
          echo '<tr><th class="cpanel" colspan="3" >DEBUG to log file : ACTIVATED <small>at '
          .date('Y-m-d H:i:s', $this->sefConfig->debugStartedAt).'</small></th></tr>';
        }
      ?>
      </tr>
    </table>

    <table class="adminform" width="100%">
      <tr>
        <th class="cpanel" colspan="8"> <?php echo JText::_('COM_SH404SEF_URLS_STATS'); ?></th>
      </tr>
      <tr>
        <td width="8%"><?php echo JText::_('COM_SH404SEF_REDIR_TOTAL').':'; ?></td>
        <td align="left" width="12%" style="font-weight: bold"><?php echo $this->sefCount+$this->Count404+$this->customCount; ?>
        </td>
        <td width="8%"><?php echo JText::_('COM_SH404SEF_REDIR_SEF').':'; ?></td>
        <td align="left" width="12%" style="font-weight: bold"><?php echo $this->sefCount; ?>
        </td>
        <td width="8%"><?php echo JText::_('COM_SH404SEF_REDIR_404').':'; ?></td>
        <td align="left" width="12%" style="font-weight: bold"><?php echo $this->Count404; ?>
        </td>
        <td width="8%"><?php echo JText::_('COM_SH404SEF_REDIR_CUSTOM').':'; ?></td>
        <td align="left" width="12%" style="font-weight: bold"><?php echo $this->customCount; ?>
        </td>
      </tr>
    </table>
    <?php

    echo JHtml::_('tabs.end');

    ?>

     </div>
  </div>
</div>
<div class="clr"></div>
  <div class="sh404sef-footer-container">
	<?php echo $this->footerText; ?>
  </div>
