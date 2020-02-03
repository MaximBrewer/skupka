;;(function($) {

    $.fn.chosenImage = function(options) {

        return this.each(function() {

            var $select = $(this),
                imgMap  = {};

            // 1. Retrieve img-src from data attribute and build object of image sources for each list item
            $select.find('option').filter(function(){
                return $(this).text();
            }).each(function(i) {
                    var imgSrc   = $(this).attr('data-img-src');
                    imgMap[i]    = imgSrc;
            });


            // 2. Execute chosen plugin
            $select.chosen(options);

            // 2.1 update (or create) div.chzn-container id
            var chzn_id = $select.attr('id').length ? $select.attr('id').replace(/[^\w]/g, '_') : this.generate_field_id();
            chzn_id += "_chzn";

            var  chzn      = '#' + chzn_id,            
                $chzn      = $(chzn).addClass('chznImage-container');


            // 3. Style lis with image sources
			$select.on('keydown.chosen chosen:showing_dropdown liszt:showing_dropdown liszt:ready liszt:open', function () {
				setTimeout(function() {					
					$chzn.find('.chzn-results li').each(function(i) {
						$(this).css(cssObj(imgMap[i]));
					});
				}, 200);
			});
			$chzn.find('input').first().on('keydown.chosen', function () {
				setTimeout(function() {					
					$chzn.find('.chzn-results li').each(function(i) {
						$(this).css(cssObj(imgMap[i]));
					});
				}, 200);
			});


            // 4. Change image on chosen selected element when form changes
            $select.on('change update', function() {
                var imgSrc = ($select.find('option:selected').attr('data-img-src')) 
                                ? $select.find('option:selected').attr('data-img-src') : '';
                $chzn.find('.chzn-single span').css(cssObj(imgSrc)).text($select.val());
            });

            $select.trigger('update');


            // Utilties
            function cssObj(imgSrc) {
                if(imgSrc) {
                    return {
                        'background-image': 'url(' + imgSrc + ')',
                        'background-repeat': 'no-repeat'
                    }
                } else {
                    return {
                        'background-image': 'none'
                    }
                }
            }
        });
    }

})(window.XDjQuery || window.jQuery);
