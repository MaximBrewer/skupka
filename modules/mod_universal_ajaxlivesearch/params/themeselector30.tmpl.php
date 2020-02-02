<?php
/*------------------------------------------------------------------------
# mod_universal_ajaxlivesearch - Universal AJAX Live Search
# ------------------------------------------------------------------------
# author    Janos Biro
# copyright Copyright (C) 2011 Offlajn.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.offlajn.com
-------------------------------------------------------------------------*/
?><?php
defined('_JEXEC') or die('Restricted access');

?>
<div class="panel">
<!--  <h3 id="basic-options" class="title pane-toggler-down"><a href="javascript:void(0);"><span>Theme Parameters</span></a></h3>-->
<div class="offlajn-title"><span>Theme Parameters</span></div>
  <div class="pane-slider content pane-down" style="padding-top: 0px; border-top: medium none; padding-bottom: 0px; border-bottom: medium none; overflow: hidden; height: auto;">
    <fieldset class="panelform">
        <div class="control-group theme">
          <div class="control-label"> <label title="" class="hasTip" for="jform_ordering" id="jform_ordering-lbl">Theme</label></div>
          <div class="controls"><?php echo $themeField; ?></div>
        </div>
        <?php echo @$render; ?>
      <div style="clear: left;" id="<?php echo $control; ?>theme-details">
      </div>

    </fieldset>
    <div class="clr"></div>
  </div>
</div>