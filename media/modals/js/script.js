/**
 * Main JavaScript file
 *
 * @package         Modals
 * @version         4.2.0
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2012 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

(function($)
{
	$(document).ready(function()
	{
		$.each($('.' + modal_class), function(i, el)
		{
			p = $.extend({}, modal_defaults);

			// Get data from tag
			$.each(el.attributes, function(index, attr)
			{
				if (attr.name.indexOf("data-modal-") === 0) {
					k = $.camelCase(attr.name.substring(11));
					p[k] = attr.value;
				}
			});

			// remove width/height if inner is already set
			if(p['innerWidth'] != undefined) {
				delete p['width'];
			}
			if(p['innerHeight'] != undefined) {
				delete p['height'];
			}

			// set true/false values to booleans
			for (k in p)
			{
				if (p[k] == 'true') {
					p[k] = true;
				} else if (p[k] == 'false') {
					p[k] = false;
				} else if (!isNaN(p[k])) {
					p[k] = parseFloat(p[k]);
				}
			}

			// Bind the modal script to the element
			$(el).colorbox(p);
		});
	});
})(jQuery);
