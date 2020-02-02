
;(function($) {
    var retina = window.devicePixelRatio > 1;
    $.fn.nextendunveil = function(mode) {
        if(mode == 'phone') mode = 'mobile';
        var images = this,
            deferred = $.Deferred(),
            loadedimages = [];
            
        function loaded(img){
            loadedimages.push(img);
            if(loadedimages.length == images.length){
                deferred.resolve(images)
            }
        };
        
        function getSrc(im, mode){
            var src;
            switch(mode){
                case 'mobile':
                    if(retina){
                        src = im.data('mobileretina');
                        if(src) return src;
                    }
                    src = im.data('mobile');
                    if(src) return src;
                case 'tablet':
                    if(retina){
                        src = im.data('tabletretina');
                        if(src) return src;
                    }
                    src = im.data('tablet');
                    if(src) return src;
                default:
                    if(retina){
                        src = im.data('desktopretina');
                        if(src) return src;
                    }
                    return im.data('desktop');
            }
        }
    
        this.each(function() {
            var targetimg = $(this),
                source = getSrc(targetimg, mode);
            
            if (!this.getAttribute("old-src") && source) {
            
                var oldsrc = this.getAttribute("src"),
                    img = $('<img/>');
                    
                this.setAttribute("old-src", oldsrc);
                
                img[0].setAttribute("src", source);
                
                img.one('load', function() {
                    targetimg[0].setAttribute("src", img[0].getAttribute("src"));
                    targetimg.trigger('lazyloaded');
                    loaded(targetimg);
                }).one('error', function() {
                    targetimg[0].setAttribute("src", oldsrc);
                    loaded(targetimg);
                });
            }else{
                loaded(targetimg);
            }
        });
        
        if(!images.length){
            deferred.resolve(images)
        }
        
        return deferred.promise();
    };
})(njQuery);
