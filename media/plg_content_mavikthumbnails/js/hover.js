jQuery(document).ready(function(){
    
    jQuery('.thumbnail img, img.thumbnail').each(function(index, img){
        
        var image = jQuery(img);
        
        var process = function(image) {    
            if (image.hasClass('no-hover')) { return; }
            
            var wrapper = image.hasClass('thumbnail') ? image : image.parents('.thumbnail').first();
            var width = image.width();
            var height = image.height();
            var wrapperWidth = wrapper.outerWidth();
            var wrapperHeight = wrapper.outerHeight();
            var widthSpace = wrapperWidth - width;
            var heightSpace = wrapperHeight - height;
            var marginTop = parseInt (wrapper.css('margin-top'));
            var marginLeft = parseInt(wrapper.css('margin-left'));
            var zIndex = wrapper.css('z-index');

            var realSize = {};
            var img = new Image();            
            jQuery(img).load(function(){
                realSize.width = img.width;
                realSize.height = img.height;
                if (realSize.width > width && realSize.height > height) {
                    image.data('hover', true);
                } else {
                    image.data('hover', false);
                }
                delete img;
            });
            img.src = image.attr('src');

            var cap = jQuery(document.createElement('div')).addClass('thumbnail-dummy');
            cap.css({
                'display': 'block',
                'position': wrapper.css('position') == 'absolute' ? 'absolute' : 'relative',
                'float': wrapper.css('float'),
                'margin-top': wrapper.css('margin-top'),
                'margin-bottom': wrapper.css('margin-bottom'),
                'margin-left': wrapper.css('margin-left'),
                'margin-right': wrapper.css('margin-right'),
                'width': wrapper.outerWidth(),
                'height': wrapper.outerHeight()
            });
            wrapper.replaceWith(cap);

            wrapper.css({
                'position': 'absolute',
                'top': 0,
                'left': 0
            });

            image.css({
                'width': width,
                'height': height,
                'max-width': 'none',
                'max-height': 'none'
            });

            cap.append(wrapper);

            wrapper.css({
                '-webkit-transition': 'all .2s ease',
                '-moz-transition': 'all .2s ease',
                '-ms-transition': 'all .2s ease',
                '-o-transition': 'all .2s ease',
                'transition': 'all .2s ease'            
            });         

            image.css({
                '-webkit-transition': 'all .2s ease',
                '-moz-transition': 'all .2s ease',
                '-ms-transition': 'all .2s ease',
                '-o-transition': 'all .2s ease',
                'transition': 'all .2s ease'            
            });

            wrapper.mouseenter(function(){
                if (!image.data('hover')) { return; }
                var jWindow = jQuery(window);
                var left = (width-realSize.width)/2 + marginLeft;
                var top = (height-realSize.height)/2 + marginTop;
                var offset = wrapper.offset();
                var scrollTop = jWindow.scrollTop();
                var scrollLeft = jWindow.scrollLeft();
                var leftPositionOut = offset.left - scrollLeft;
                var topPositionOut = offset.top - scrollTop;
                var rightPositionIn = leftPositionOut + realSize.width + widthSpace + left;
                var bottomPositionIn = topPositionOut + realSize.height + heightSpace + top;
                var windowWidth = jWindow.width();
                var windowHeigth = jWindow.height();
                if (rightPositionIn > windowWidth) {
                    left = left - (rightPositionIn - windowWidth);
                }
                if (bottomPositionIn > windowHeigth) {
                    top = top - (bottomPositionIn - windowHeigth);
                }                
                if (leftPositionOut < -left) {
                    left = -leftPositionOut;
                }
                if (topPositionOut < -top) {
                    top = -topPositionOut;
                }
                wrapper.css({
                    'z-index': 9999
                });             
                wrapper.css({
                    'left': left,
                    'top': top
                }); 
                image.css({
                    'width': realSize.width,
                    'height': realSize.height
                });                         
            });

            wrapper.mouseleave(function(){
                if (!image.data('hover')) { return; }
                image.css({
                    'width': width,
                    'height': height                  
                });
                wrapper.css({
                    'left': 0,
                    'top': 0
                });        
                wrapper.delay(300).css({
                    'z-index': zIndex
                });
            });
            
            wrapper.click(function(){wrapper.mouseleave();});
        };
        
        if (img.complete) {
            process(image);
        } else {
            image.load(function(){process(image);});
        }
    });
});