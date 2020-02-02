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
   <h2><?php echo JText::sprintf('COM_SH404SEF_ANALYTICS_TOP5_PAGES', $this->options['max-top-urls']); ?></h2>

 	<table class="table table-striped" >
    <thead>
      <tr>
        <th class="title" >
          <?php echo '#'; ?>
        </th>

        <th rel="tooltip" <?php echo Sh404sefHelperHtml::wrapBootstrapTipTitle(JText::_('COM_SH404SEF_ANALYTICS_TOP5_URL'), JText::_('COM_SH404SEF_ANALYTICS_TT_URL_DESC'));?>>
        <?php echo JText::_( 'COM_SH404SEF_ANALYTICS_TOP5_URL' ); ?>
        </th>

        <th rel="tooltip" <?php echo Sh404sefHelperHtml::wrapBootstrapTipTitle(JText::_('COM_SH404SEF_ANALYTICS_TOP5_PAGEVIEWS'), JText::_('COM_SH404SEF_ANALYTICS_TT_PAGE_VIEWS_DESC'));?>>
        <?php echo JText::_( 'COM_SH404SEF_ANALYTICS_TOP5_PAGEVIEWS' ); ?>
        </th>

        <th rel="tooltip" <?php echo Sh404sefHelperHtml::wrapBootstrapTipTitle(JText::_('COM_SH404SEF_ANALYTICS_TOP5_PAGEVIEWS_PERCENT'), JText::_('COM_SH404SEF_ANALYTICS_TT_URL_PER_CENT_DESC'));?>>
        <?php echo JText::_( 'COM_SH404SEF_ANALYTICS_TOP5_PAGEVIEWS_PERCENT' ); ?>
        </th>

        <th rel="tooltip" <?php echo Sh404sefHelperHtml::wrapBootstrapTipTitle(JText::_('COM_SH404SEF_ANALYTICS_TOP5_AVG_TIME_ON_PAGE'), JText::_('COM_SH404SEF_ANALYTICS_TT_AVG_TIME_ON_PAGE_DESC'));?>>
        <?php echo JText::_( 'COM_SH404SEF_ANALYTICS_TOP5_AVG_TIME_ON_PAGE' ); ?>
        </th>
      </tr>
    </thead>


 	 <tbody>
        <?php
          $k = 0;
          $i = 1;
          foreach($this->analytics->analyticsData->top5urls as $entry) :
        ?>

        <tr class="<?php echo "row$k"; ?>">

          <td class="shl-centered" width="3%">
            <?php echo $i; ?>
          </td>

          <td width="62%">
            <?php echo $this->escape( $entry->dimension['pagePath']); ?>
          </td>

          <td class="shl-centered" width="15%">
            <?php echo $this->escape( $entry->pageviews); ?>
          </td>

          <td class="shl-centered" width="10%">
            <?php
              echo $this->escape( sprintf( '%0.1f', $entry->pageviewsPerCent*100));
            ?>
          </td>

          <td class="shl-centered" width="10%">
            <?php
              echo $this->escape( sprintf( '%0.1f', $entry->avgTimeOnPage));
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

