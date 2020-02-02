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
<div class="panel dashboard" id="offlajn-dashboard">
  <div id="dashboard-icon" class="opened"></div>
  <h3 class="title" style="background-image:url('<?php echo $logoUrl?>');">Universal AJAX Live Search<span> DASHBOARD</span></h3>
  <div class="pane-slider content" style="padding-top: 0px; border-top: medium none; padding-bottom: 0px; border-bottom: medium none; overflow: hidden;">
    <div>
    	<div class="column left">
    	 <div class="dashboard-box">
        <div class="box-title">
         General <b>Information</b>
        </div>
        <div class="box-content">
         <?php
          echo $this->generalInfo;
         ?>
        </div>
        </div>
      </div>
    	<div class="column mid">
    	 <div class="dashboard-box">
        <div class="box-title">
         Related <b>News</b>
        </div>
        <div class="box-content">
         <?php
          echo $this->relatedNews;
         ?>
        </div>
      </div>
      </div>
    	<div class="column right">
    	 <div class="dashboard-box">
        <div class="box-title">
         Product <b>Support</b>
        </div>
        <div class="box-content">
          <div class="content-inner">
             If you have any problem with <?php echo @$this->label?> just write us and we will help ASAP!
             <div style="background-image:url('<?php echo $supportTicketUrl?>');" class="support-ticket-button"><a href="http://offlajn.com/contact-us.html#department=6&product=34" target="_blank"></a></div>
             <div class="clr"></div>
          </div>
        </div>
        </div>
    	 <div class="dashboard-box">
        <div class="box-title">
         Rate <b>Us</b>
        </div>
        <div class="box-content">
          <div class="content-inner">
            If you use <?php echo @$this->label?>, please post a rating and a review at the Joomla! Extensions Directory. With this small gesture you will help the community a lot. Thank you very much!
             <div style="background-image:url('<?php echo $supportUsUrl?>');" class="support-us-button"><a href="http://extensions.joomla.org/index.php?option=com_mtree&task=viewlink&link_id=16893" target="_blank"></a></div>
             <div class="clr"></div>
          </div>
        </div>
        </div>
      </div>
      <div class="clr"></div>
    </div>
  </div>
</div>

<script type="text/javascript">
dojo.addOnLoad(function(){
        var dash = dojo.byId('offlajn-dashboard');
        dojo.place(dash,dash.parentNode.parentNode.parentNode, 'first');
      });
</script>
