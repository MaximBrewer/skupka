<?php
/*------------------------------------
* System Compare Products for Virtuemart
* Author    CMSMart Team
* Copyright Copyright (C) 2012 http://cmsmart.net. All Rights Reserved.
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* Websites: http://cmsmart.net
* Email: team@cmsmart.net
* Technical Support:  Forum - http://bloorum.com/forums
* Version 1.0.0
-----------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
if(!$this->limit){
    if($this->success_compare){//success

?>
    <p class='success-compare'>Product was added to your comparison</p>
    <script type="text/javascript">
        jQuery(document).ready(function(){
           var fly = jQuery('.fly');
           var cmscp = jQuery('.nb_compare');
           cmscp.top = cmscp.offset().top;
 		   cmscp.left = cmscp.offset().left;
           var imgtodrag = jQuery('<?php echo $this->img_class ?> img:first');
           if (!imgtodrag.length) {
            var imgtodrag = fly.closest('<?php echo $this->spacer ?>').find('img');
           }
           if (imgtodrag.length) {
               var imgclone = imgtodrag.clone()
                           .offset({ top: imgtodrag.offset().top, left: imgtodrag.offset().left })
                           .css({'opacity': '0.7', 'position': 'absolute', 'z-index': '10000','width':'100px'})
                           .appendTo(jQuery('body'))
                           .animate({
                           'top': cmscp.offset().top + 10,
                           'left': cmscp.offset().left + 10
                           },500, 'swing');
               imgclone.animate({
                   'width': 0,
                   'height': 0
               }, 500, function() {
                            imgclone.remove();
                       });
           }
           fly.removeClass('fly');
        });
    
    </script>
<?php        
    }else{
?>    
    <p class='error-compare'>Sorry,Product was exist in your comparison.</p>    
<?php
    }  
}else{
?>
<p class='error-compare'>You can only compare a maximum of <?php echo $this->max ?> products.</p>
<?Php
}
?>

<span class="cboxClose">Close</span>
<script type="text/javascript">
    jQuery(document).ready(function(){
       jQuery('.cboxClose').click(function(){
            jQuery('#cboxClose').click();
       }); 
    });
</script>