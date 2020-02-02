/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     sh404SEF
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     4.7.3.3292
 * @date    2016-03-15
 */
var __sh404sefJs = (function($) {
  var tmp = {

    // function to call on document ready
    onReady : function() {
      jQuery(window).resize(__sh404sefJs.stickyPagination);
      __sh404sefJs.stickyPagination();
    },

    // display the pagination bar at the top, fixed, or let it
    // at bottom, scrolling
    stickyPagination : function() {
      var needed = $("#shl-bottom-pagination-container ul.pagination-list").width();
      var avail = $("#shl-main-searchbar-right-block").width();
      var used = 0;
      $("#shl-main-searchbar-right-block div.btn-group").each(function() {
        var w = jQuery(this).width();
        used += w;
      });
      if ((avail - used - 20) > needed) {
        var c = jQuery("#shl-bottom-pagination-container").html();
        if(c) {
          $("#shl-bottom-pagination-container").html("");
          $("#shl-top-pagination-container").html(c);
          $(".shl-main-list-wrapper table tfoot").hide();
        }
      } else {
          var t = jQuery("#shl-top-pagination-container").html();
          if(t) {
            $("#shl-bottom-pagination-container").html(t);
            $("#shl-top-pagination-container").html("");
          }
          
      }
    }
  };

  // init
  jQuery(document).ready(tmp.onReady);
  return tmp;
})(jQuery);
