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
  <h3 id="<?php echo $id; ?>-title" class="jpane-toggler title jpane-toggler-down"><span style="background-image: none;"><?php echo $title; ?></span></h3>
  <div class="jpane-slider content">
    <?php echo $text; ?>
    <div style="clear: left;" id="<?php echo $id; ?>-details">
    </div>
  </div>
</div>