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


$title = JText::_('COM_SH404SEF_ANALYTICS_REPORT_SOURCES') . '::' . JText::_('COM_SH404SEF_ANALYTICS_DATA_SOURCES_DESC_RAW');

?>

  <div class="hasAnalyticsTip width-100" title="<?php echo $title; ?>">
       
       <fieldset class="adminform">
					<legend>
						<?php echo JText::_('COM_SH404SEF_ANALYTICS_REPORT_SOURCES') . JText::_( 'COM_SH404SEF_ANALYTICS_REPORT_BY_LABEL') . Sh404sefHelperAnalytics::getDataTypeTitle(); ?>
					</legend>
        
          <ul class="adminformlist">
            <li>
              <div class="analytics-report-image"><img src="<?php echo $this->analytics->analyticsData->images['sources']; ?>" /></div>
            </li>
          </ul>
        </fieldset>
	
	</div>
