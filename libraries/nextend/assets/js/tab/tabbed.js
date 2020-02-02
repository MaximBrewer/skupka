(function ($, scope, undefined) {
    scope.nextendTabTabbed = NClass.extend({
        init: function(container, active){
            var $this = this;
            this.container = njQuery('#'+container);
            this.options = this.container.find('.smartslider-toolbar-options');
           
            this.panes = this.container.find('.nextend-tab-tabbed-panes');
            this.pane = this.container.find('.nextend-tab-tabbed-pane');
            
            this.options.each(function(i){
                $(this).on('click', function(){
                    $this.changePane(i);
                });
            });
        },
        
        changePane: function(i){
            var $this = this;
            this.options.eq(i).addClass('active');
            this.options.not(this.options.eq(i)).removeClass('active');
            
            this.panes.css((window.nextendDir == 'rtl' ? 'marginRight' : 'marginLeft'), (window.nextendDir == 'rtl' ? (-(Math.abs(i-(this.options.length-1)))*100)+'%' : (-i*100)+'%'));
            
            this.pane.eq(i).css('visibility', 'visible');
            
            var hidden = this.pane.not(this.pane.eq(i));
            setTimeout(function(){
                hidden.css('visibility', 'hidden');
            }, 400);
        }
    });
    
})(njQuery, window);