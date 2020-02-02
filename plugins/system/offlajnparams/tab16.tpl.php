<?php
/*-------------------------------------------------------------------------
# plg_offlajnparams - Offlajn Params
# -------------------------------------------------------------------------
# @ author    Balint Polgarfi
# @ copyright Copyright (C) 2016 Offlajn.com  All Rights Reserved.
# @ license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @ website   http://www.offlajn.com
-------------------------------------------------------------------------*/
?><?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<div class="panel <?php echo $class; ?>">
  <h3 id="<?php echo $id; ?>-title" class="title pane-toggler-down"><a href="" onclick="return false;"><span><?php echo $title; ?></span></a></h3>
  <div class="pane-slider content pane-down" style="padding-top: 0px; border-top: medium none; padding-bottom: 0px; border-bottom: medium none; height: auto;">
    <fieldset class="panelform" id="<?php echo $id; ?>-fieldset" >
      <?php echo $text; ?>
      <div style="clear: left;" id="<?php echo $id; ?>-details">
      </div>

    </fieldset>
    <div class="clr"></div>
  </div>
</div>