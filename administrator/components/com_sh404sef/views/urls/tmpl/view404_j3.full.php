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

jimport('joomla.html.html.bootstrap');
JHtml::_('formbehavior.chosen', 'select');

$sticky = Sh404sefHelperHtml::setFixedTemplate();

if($sticky) :?>
<div class="shl-fixed-top-hidden<?php echo Sh404sefHelperHtml::getFixedHeaderClass(); ?>">&nbsp;</div>
<?php endif; ?>

<div class="shl-main-content wbl-theme-default">

<form method="post" name="adminForm" id="adminForm" class="shl-no-margin">

<div class="row-fluid">

<?php if($sticky) : ?>
<div class="shl-fixed span12 shl-main-searchbar-wrapper">
	<?php echo ShlMvcLayout_Helper::render('com_sh404sef.filters.bar_search_limit_pag_sticky', $this); ?>
</div>
<?php endif; ?>

<div id="shl-sidebar-container" class="<?php echo $sticky ? 'shl-fixed' : ''; ?> span2 shl-no-margin">
<?php echo $this->sidebar; ?>
</div>

<?php if(!$sticky): ?>
<div class="span10">
<?php endif; ?>

<?php if(!$sticky): ?>
<div class="span12 shl-main-searchbar-wrapper">
	<?php echo ShlMvcLayout_Helper::render('com_sh404sef.filters.bar_search_limit', $this); ?>
</div>
<?php endif; ?>

<div class="shl-main-list-wrapper span12  <?php if($sticky) echo ' shl-main-list-wrapper-padding'; ?>">

	<?php if($sticky):?>
	<div class="span2 shl-hidden-low-width"></div>
	<div class="span10 <?php echo $sticky ? 'shl-no-margin-left' : ''; ?>">
	<?php endif; ?>

	<div id="sh-message-box"></div>
    <table class="table table-striped table-bordered shl-main-list-wrapper">
      <thead>
        <tr>
          <th class="center shl-list-id">&nbsp;
          </th>
          <th class="center shl-list-check">
            <input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
          </th>
          <th class="shl-list-hits">
            <?php echo JHTML::_('grid.sort', JText::_( 'COM_SH404SEF_HITS'), 'cpt', $this->options->filter_order_Dir, $this->options->filter_order); ?>
          </th>
	      <th class="shl-list-hits"><?php echo JText::_('COM_SH404SEF_HIT_TYPE_INTERNAL'); ?>
	      </th>
          <th class="shl-list-sef">
            <?php echo JHTML::_('grid.sort', JText::_( 'COM_SH404SEF_SEF_URL'), 'oldurl', $this->options->filter_order_Dir, $this->options->filter_order); ?>
          </th>
	      <th class="shl-list-hits"><?php echo JText::_('COM_SH404SEF_HIT_DETAILS'); ?>
	      </th>
          <th class="shl-list-large-buttons">
            &nbsp;
          </th>
          <th class="shl-list-large-buttons">
            &nbsp;
          </th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="8">
            <?php echo '<div id="shl-bottom-pagination-container">' . $this->pagination->getListFooter() . '</div>'; ?>
          </td>
        </tr>
      </tfoot>
      <tbody>
        <?php
        $k = 0;
        if( $this->itemCount > 0 ) {
		  $sizes = Sh404sefFactory::getPConfig()->windowSizes;
          for ($i=0; $i < $this->itemCount; $i++) {

            $url = &$this->items[$i];
            $checked = JHtml::_( 'grid.id', $i, $url->id);
            $custom = '&nbsp;'; ?>

        <tr>
          <td class="shl-list-id">
            <?php echo $this->pagination->getRowOffset( $i ); ?>
          </td>
          <td class="shl-list-check">
            <?php echo $checked; ?>
          </td>
          <td class="shl-list-hits">
            <?php echo empty($url->cpt) ? '&nbsp;' : ShlSystem_Strings::formatIntForTitle($url->cpt); ?>
          </td>
	        <td class="shl-list-hit-type">
		        <?php
		        $txt = $url->referrer_type == Sh404sefHelperUrl::IS_INTERNAL ? ShlHtmlBs_Helper::iconglyph('', 'thumbs-down') : '';
		        echo empty($txt) ? '&nbsp;' : ShlHtmlBs_Helper::badge($txt, 'warning', JText::_('COM_SH404SEF_HIT_TYPE_INTERNAL_404_TITLE'));
		        ?>
	        </td>

          <td class="shl-list-sef">
            <?php
              $params = array();
              $linkData = array( 'c' => 'editurl', 'task' => 'edit', 'cid[]' => $url->id, 'tmpl' => 'component', 'view' => 'editurl');
              $targetUrl = Sh404sefHelperUrl::buildUrl($linkData);
              $params['linkTitle'] = Sh404sefHelperHtml::abridge(JText::_('COM_SH404SEF_MODIFY_LINK_TITLE') . ' ' . $this->escape($url->oldurl), 'editurl');
              $modalTitle = '';
              $params['linkClass'] = 'shl-list-sef';
              $params['linkType'] = 'a';
              $name = '-editurl-' . $url->id;
              echo ShlHtmlModal_helper::modalLink($name, $this->escape($url->oldurl), $targetUrl, $sizes['editurl']['x'], $sizes['editurl']['y'], $top = 0, $left = 0, $onClose = '', $modalTitle, $params);
              ?>
          </td>

	        <td class="center">
		        <?php
		        $params = array();
		        $linkData = array( 'c' => 'hitdetails', 'url_id' => $url->id, 'tmpl' => 'component','request_type' => '404s');
		        $targetUrl = Sh404sefHelperUrl::buildUrl($linkData);
		        $modalTitle = '';
		        $params['linkTitle'] = JText::_('COM_SH404SEF_HIT_VIEW_DETAILS_TITLE');
		        $params['linkClass'] = 'btn';
		        $name = '-viewhitdetails-' . $url->id;
		        echo ShlHtmlModal_helper::modalLink($name, '+', $targetUrl, $sizes['selectredirect']['x'], $sizes['selectredirect']['y'], $top = 0, $left = 0, $onClose = '', $modalTitle, $params);
		        ?>
	        </td>

          <td class="center">
          <?php
              $params = array();
              $linkData = array( 'c' => 'notfound', 'notfound_url_id' => $url->id, 'tmpl' => 'component');
              $targetUrl = Sh404sefHelperUrl::buildUrl($linkData);
              $modalTitle = '';
              $params['linkTitle'] = JText::_('COM_SH404SEF_NOT_FOUND_SHOW_URLS_TITLE'). ' ' . $this->escape($url->oldurl);
              $params['linkClass'] = 'btn';
              $name = '-editsefredirect-' . $url->id;
              echo ShlHtmlModal_helper::modalLink($name, JText::_('COM_SH404SEF_NOT_FOUND_SHOW_URLS'), $targetUrl, $sizes['selectredirect']['x'], $sizes['selectredirect']['y'], $top = 0, $left = 0, $onClose = '', $modalTitle, $params);
            ?>
          </td>
          <td class="center">
            <?php
	            $params = array();
	            $linkData = array( 'c' => 'editnotfound', 'notfound_url_id' => $url->id, 'task' => 'newredirect', 'tmpl' => 'component');
	            $targetUrl = Sh404sefHelperUrl::buildUrl($linkData);
	            //$modalTitle = JText::_('COM_SH404SEF_NOT_FOUND_ENTER_REDIRECT');
	            $modalTitle = '';
	            $params['linkTitle'] = JText::_('COM_SH404SEF_NOT_FOUND_ENTER_REDIRECT_TITLE'). ' ' . $this->escape($url->oldurl);
		            $name = '-enterredirect-' . $url->id;
	            echo ShlHtmlModal_helper::modalLink($name, JText::_('COM_SH404SEF_NOT_FOUND_ENTER_REDIRECT'), $targetUrl, $sizes['enterredirect']['x'], $sizes['enterredirect']['y'], $top = 0, $left = 0, $onClose = '', $modalTitle, $params);
            ?>
          </td>
        </tr>
        <?php
        $k = 1 - $k;
      }
    } else {
      ?>
        <tr>
          <td class="center shl-middle" colspan="8">
            <?php echo JText::_( 'COM_SH404SEF_NO_URL' ); ?>
          </td>
        </tr>
        <?php
      }
      ?>
      </tbody>
    </table>
    <?php if($sticky):?>
    </div>
    <?php endif;?>
</div>

<?php if(!$sticky): ?>
</div>
<?php endif; ?>

    <input type="hidden" name="c" value="urls" />
    <input type="hidden" name="view" value="urls" />
    <input type="hidden" name="layout" value="view404" />
    <input type="hidden" name="option" value="com_sh404sef" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $this->options->filter_order; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $this->options->filter_order_Dir; ?>" />
    <?php echo JHTML::_( 'form.token' ); ?>
 </div>
</form>
</div>

<div class="sh404sef-footer-container">
	<?php echo $this->footerText; ?>
</div>
