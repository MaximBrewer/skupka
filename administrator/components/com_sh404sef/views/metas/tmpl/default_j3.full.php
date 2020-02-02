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

<form method="post" action="index.php?option=com_sh404sef&c=metas&layout=default&view=metas" name="adminForm" id="adminForm" class="shl-no-margin">

<div class="row-fluid">

<?php if($sticky) : ?>
<div class="shl-fixed span12 shl-main-searchbar-wrapper">
	<?php echo ShlMvcLayout_Helper::render('com_sh404sef.filters.bar_search_limit_pag_sticky', $this); ?>
</div>
<?php endif; ?>

<div id="shl-sidebar-container" class="<?php echo $sticky ? 'shl-fixed' : ''; ?> span2 shl-no-margin">
<?php
echo $this->sidebar;
?>
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
	<?php
		endif;
		echo ShlMvcLayout_Helper::render('shlib.msg.collapsed_message', array('content' => $this->helpMessage, 'close' => true, 'collapse' => true), SHLIB_LAYOUTS_PATH);
	?>

	<div id="sh-message-box"></div>

    <table class="table table-striped table-bordered shl-main-list-wrapper">
      <thead>
        <tr>
          <th class="shl-list-id">&nbsp;
          </th>

          <th class="shl-list-icon">
            <?php echo JText::_( 'COM_SH404SEF_IS_CUSTOM'); ?>
          </th>

          <th class="shl-list-sef" >
            <?php echo JHTML::_('grid.sort', JText::_( 'COM_SH404SEF_URL'), 'oldurl', $this->options->filter_order_Dir, $this->options->filter_order); ?>
          </th>

          <th class="shl-list-metatitle">
            <?php echo JText::_( 'COM_SH404SEF_META_TITLE'); ?>
          </th>

          <th class="shl-list-metadesc">
            <?php echo JText::_( 'COM_SH404SEF_META_DESC'); ?>
          </th>

        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="5">
            <?php echo '<div id="shl-bottom-pagination-container">' . $this->pagination->getListFooter() . '</div>'; ?>
          </td>
        </tr>
      </tfoot>
      <tbody>
        <?php
          $k = 0;
          if( $this->itemCount > 0 ) {
            for ($i=0; $i < $this->itemCount; $i++) {

              $url = &$this->items[$i];
              $nonSefUrl = empty($url->newurl) ? ( empty($url->nonsefurl) ? '' : $url->nonsefurl) : $url->newurl;
              $custom = !empty($url->newurl) && $url->dateadd != '0000-00-00' ? ShlHtmlBs_Helper::iconglyph('', 'wrench', JText::_('COM_SH404SEF_CUSTOM_URL_LINK_TITLE')) : '&nbsp;';
        ?>

        <tr>

          <td class="shl-list-id">
            <?php echo $this->pagination->getRowOffset( $i ); ?>
          </td>

          <td class="shl-list-icon">
            <?php echo $custom;?>
          </td>


          <td class="shl-list-sef">
            <?php
              echo '<input type="hidden" name="metaid['.$url->id.']" value="'.(empty($url->metaid) ? 0 : $url->metaid).'" />';
              echo '<input type="hidden" name="newurls['.$url->id.']" value="'.(empty($nonSefUrl) ? '' : $this->escape( $nonSefUrl)).'" />';
              // link to full meta edit
              $anchor = empty($url->oldurl) ? '(-)' : $this->escape( $url->oldurl);
              $anchor .= '<br/><i>(' . $this->escape( $nonSefUrl) . ')</i>';

              $params = array();
              $linkData = array( 'c' => 'editurl', 'task' => 'edit', 'view' => 'editurl', 'startOffset' => '1','cid[]' => $url->id, 'tmpl' => 'component');
              $targetUrl = Sh404sefHelperUrl::buildUrl($linkData);
              $params['linkTitle'] = Sh404sefHelperHtml::abridge(JText::_('COM_SH404SEF_MODIFY_LINK_TITLE'). ' ' . $this->escape($url->oldurl), 'editurl');
              $modalTitle = '';
              $name = '-editurl-' . $url->id;
              $params['linkClass'] = 'shl-list-sef';
              $params['linkType'] = 'a';
              echo ShlHtmlModal_helper::modalLink($name, $anchor, $targetUrl, Sh404sefFactory::getPConfig()->windowSizes['editurl']['x'], Sh404sefFactory::getPConfig()->windowSizes['editurl']['y'], $top = 0, $left = 0, $onClose = '', $modalTitle, $params);

              // small preview icon
              $sefConfig = & Sh404sefFactory::getConfig();
              $link = JURI::root() . ltrim( $sefConfig->shRewriteStrings[$sefConfig->shRewriteMode], '/') . (empty($url->oldurl) ? $nonSefUrl : $url->oldurl);
              echo '&nbsp;<a href="' . $this->escape($link) . '" target="_blank" title="' . JText::_('COM_SH404SEF_PREVIEW') . ' ' . $this->escape($link) . '">';
              echo '<img src=\'components/com_sh404sef/assets/images/external-black.png\' border=\'0\' alt=\''.JText::_('COM_SH404SEF_PREVIEW').'\' />';
              echo '</a>';

              // attach an input counter to the title input boxes
			  echo ShlHtmlBs_Helper::renderInputCounter( 'metatitle' . $url->id, Sh404sefFactory::getPConfig()->metaDataSpecs['metatitle']);
			  echo ShlHtmlBs_Helper::renderInputCounter( 'metadesc' . $url->id, Sh404sefFactory::getPConfig()->metaDataSpecs['metadesc']);

            ?>
          </td>

          <td class="center">
            	<textarea class="text_area" id="metatitle<?php echo $url->id; ?>" name="metatitle[<?php echo $url->id; ?>]" cols="40" rows="5"><?php echo $this->escape( $url->metatitle); ?></textarea>
          </td>

          <td class="center">
            	<textarea class="text_area" id="metadesc<?php echo $url->id; ?>" name="metadesc[<?php echo $url->id; ?>]" cols="40" rows="5"><?php echo $this->escape( $url->metadesc); ?></textarea>
          </td>

        </tr>
        <?php
        $k = 1 - $k;
      }
    } else {
      ?>
        <tr>
          <td class="center shl-middle" colspan="5">
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

    <input type="hidden" name="c" value="metas" />
    <input type="hidden" name="view" value="metas" />
    <input type="hidden" name="option" value="com_sh404sef" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="hidemainmenu" value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $this->options->filter_order; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $this->options->filter_order_Dir; ?>" />
    <input type="hidden" name="contentcs" value="<?php echo $this->contentcs; ?>" />
    <input type="hidden" name="format" value="html" />
    <input type="hidden" name="shajax" value="0" />
    <?php echo JHTML::_( 'form.token' ); ?>
 </div>
</form>
</div>

<div class="sh404sef-footer-container">
	<?php echo $this->footerText; ?>
</div>
