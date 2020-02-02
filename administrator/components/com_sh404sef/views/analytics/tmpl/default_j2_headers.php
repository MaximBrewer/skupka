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

    <table class="adminlist">

      <thead>
        <tr>
          <td width="18%">
            <?php
               $allFilters = $this->options['showFilters'] == 'yes';
               echo '<a href="javascript: void(0);" onclick="javascript: shSetupAnalytics({forced:1' . ($allFilters ? '' : ',showFilters:\'no\'') . '});" > ['
               . JText::_('COM_SH404SEF_CHECK_ANALYTICS').']</a>';
               echo '&nbsp;';
               echo empty($this->analytics->status) ? JText::_('COM_SH404SEF_ERROR_CHECKING_ANALYTICS') : $this->escape( $this->analytics->statusMessage);
            ?>
          </td>
        </tr>
      </thead>
      <?php
      if(!empty($this->analytics->status)) :

        echo $this->loadTemplate( $this->joomlaVersionPrefix . '_filters');

      endif;
      ?>

    </table>



