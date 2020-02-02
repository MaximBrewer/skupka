/**
 *---------------------------------------------------------------------------------------
 * @package      VP One Page Checkout - Joomla! System Plugin
 * @subpackage   For VirtueMart 3+
 *---------------------------------------------------------------------------------------
 * @copyright    Copyright (C) 2012-2017 VirtuePlanet Services LLP. All rights reserved.
 * @license      GNU General Public License version 2 or later; see LICENSE.txt
 * @authors      Abhishek Das
 * @email        info@virtueplanet.com
 * @link         https://www.virtueplanet.com
 *---------------------------------------------------------------------------------------
 * $Revision: 3 $
 * $LastChangedDate: 2017-09-20 20:00:08 +0530 (Wed, 20 Sep 2017) $
 * $Id: admin.js 3 2017-09-20 14:30:08Z Abhshek Das $
 * --------------------------------------------------------------------------------------
*/
(function($) {
	$(function() {
    var $title = $('.vp-extension-description .extension-title [data-text]');
    $title.text($title.data('text'));
    
    $('#vp-inline-stylesheet').remove();
    
    if (!$('.vpdk-dummy-linebreak').length) {
        $('<div class="vpdk-dummy-linebreak clearfix"></div>').insertBefore('.vpdk-info-box');
    }
    
		$('#jform_params_show_social_login[disabled], #jform_params_social_btn_size[disabled]').closest('li').wrapAll('<div id="only-for-vpau" />');
		$('#only-for-vpau').append('<div id="only-for-vpau-overlay" />');
	});
})(jQuery);