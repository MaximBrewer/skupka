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

  <h2><?php echo JText::_('COM_SH404SEF_ANALYTICS_PERF_DATA'); ?></h2>

    <div>
          	<div class="span4" rel="tooltip" <?php echo Sh404sefHelperHtml::wrapBootstrapTipTitle(JText::_('COM_SH404SEF_ANALYTICS_AVG_CREATION_TIME'), JText::_('COM_SH404SEF_ANALYTICS_TT_AVG_CREATION_TIME'));?>>
                <div class="span12 shl-center">
                <?php echo JText::_( 'COM_SH404SEF_ANALYTICS_AVG_CREATION_TIME' ); ?>&nbsp;
                </div>
                <div class="span12 shl-center">
                  <?php echo ShlHtmlBs_Helper::badge($this->escape(sprintf( '%0.2f', $this->analytics->analyticsData->perf->avgPageCreationTime)) . ' s.', 'info'); ?>
                </div>
			</div>

            <div class="span4" rel="tooltip" <?php echo Sh404sefHelperHtml::wrapBootstrapTipTitle(JText::_('COM_SH404SEF_ANALYTICS_AVG_MEMORY_USED'), JText::_('COM_SH404SEF_ANALYTICS_TT_AVG_MEMORY_USED'));?>>
            	<div class="span12 shl-center">
                <?php echo JText::_( 'COM_SH404SEF_ANALYTICS_AVG_MEMORY_USED' ); ?>&nbsp;
                </div>
                <div class="span12 shl-center">
                  <?php echo ShlHtmlBs_Helper::badge($this->escape(sprintf( '%0.1f', $this->analytics->analyticsData->perf->avgMemoryUsed)) . ' Mb', 'info'); ?>
                </div>
            </div>

          <div class="span4" rel="tooltip" <?php echo Sh404sefHelperHtml::wrapBootstrapTipTitle(JText::_('COM_SH404SEF_ANALYTICS_USER_STATUS'), JText::_('COM_SH404SEF_ANALYTICS_TT_USER_STATUS'));?>>
                <div class="span12 shl-center">
                <?php echo JText::_( 'COM_SH404SEF_ANALYTICS_USER_STATUS' ); ?>&nbsp;
                </div>
                <div class="span12 shl-center">
                  <?php echo ShlHtmlBs_Helper::badge($this->escape(sprintf( '%0.1f', $this->analytics->analyticsData->perf->loggedInUserRate * 100)) . ' %', 'info'); ?>
                </div>
			</div>
	</div>
