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

   <h2><?php echo JText::sprintf('COM_SH404SEF_ANALYTICS_TOP5_REFERRERS', $this->options['max-top-referrers']); ?></h2>

  <table class="table table-striped">
    <thead>
      <tr>
        <th class="title" width="3%">
          <?php echo '#'; ?>
        </th>

        <th rel="tooltip" <?php echo Sh404sefHelperHtml::wrapBootstrapTipTitle(JText::_('COM_SH404SEF_ANALYTICS_TOP5_REF_SOURCE'), JText::_('COM_SH404SEF_ANALYTICS_TT_SOURCE_SITE_DESC'));?>>
        <?php echo JText::_( 'COM_SH404SEF_ANALYTICS_TOP5_REF_SOURCE' ); ?>
        </th>

        <th rel="tooltip" <?php echo Sh404sefHelperHtml::wrapBootstrapTipTitle(JText::_('COM_SH404SEF_ANALYTICS_TOP5_REF_PATH'), JText::_('COM_SH404SEF_ANALYTICS_TT_SOURCE_PATH_DESC'));?>>
        <?php echo JText::_( 'COM_SH404SEF_ANALYTICS_TOP5_REF_PATH' ); ?>
        </th>

        <th rel="tooltip" <?php echo Sh404sefHelperHtml::wrapBootstrapTipTitle(JText::_('COM_SH404SEF_ANALYTICS_TOP5_PAGEVIEWS'), JText::_('COM_SH404SEF_ANALYTICS_TT_PAGE_VIEWS_DESC'));?>>
        <?php echo JText::_( 'COM_SH404SEF_ANALYTICS_TOP5_PAGEVIEWS' ); ?>
        </th>

        <th rel="tooltip" <?php echo Sh404sefHelperHtml::wrapBootstrapTipTitle(JText::_('COM_SH404SEF_ANALYTICS_TOP5_PAGEVIEWS_PERCENT'), JText::_('COM_SH404SEF_ANALYTICS_TT_REFERRER_PER_CENT_DESC'));?>>
        <?php echo JText::_( 'COM_SH404SEF_ANALYTICS_TOP5_PAGEVIEWS_PERCENT' ); ?>
        </th>

        <th rel="tooltip" <?php echo Sh404sefHelperHtml::wrapBootstrapTipTitle(JText::_('COM_SH404SEF_ANALYTICS_TOP5_AVG_TIME_ON_SITE'), JText::_('COM_SH404SEF_ANALYTICS_TT_AVG_TIME_ON_SITE_DESC'));?>>
        <?php echo JText::_( 'COM_SH404SEF_ANALYTICS_TOP5_AVG_TIME_ON_SITE' ); ?>
        </th>
      </tr>
    </thead>


   <tbody>
        <?php
          $k = 0;
          $i = 1;
          foreach($this->analytics->analyticsData->top5referrers as $entry) :
        ?>

        <tr class="<?php echo "row$k"; ?>">

          <td class="shl-centered" width="3%">
            <?php echo $i; ?>
          </td>

          <td width="22%">
            <?php echo $this->escape( $entry->dimension['source']); ?>
          </td>

          <td width="40%">
            <?php echo $this->escape( $entry->dimension['referralPath']); ?>
          </td>

          <td class="shl-centered" width="15%">
            <?php echo $this->escape( $entry->views); ?>
          </td>

          <td class="shl-centered" width="10%">
            <?php
              echo $this->escape( sprintf( '%0.1f', $entry->viewsPerCent*100));
            ?>
          </td>

          <td class="shl-centered" width="10%">
            <?php
              echo $this->escape( sprintf( '%0.1f', $entry->avgTimeOnSite));
            ?>
          </td>

        </tr>
        <?php
        $k = 1 - $k;
        $i++;
      endforeach;

      ?>

    </tbody>
  </table>
