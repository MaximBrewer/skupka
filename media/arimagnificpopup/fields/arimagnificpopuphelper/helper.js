;(function($, undefined) {
    ARIMagnificPopupFieldsHelper = function(options) {
        this.options = $.extend({}, this.options, options);
    };

    ARIMagnificPopupFieldsHelper.prototype = {
        options: {
            'hidePro': false,

            'textTransform': '.ctrl-textTransform',

            'textTransformAdvanced': '.ctrl-textTransformAdvanced'
        },

        init: function() {
            var options = this.options,
                textTransformCtrl = $(options.textTransform),
                textTransformAdvancedCtrl = $(options.textTransformAdvanced),
                textTransformAdvancedContainer = this.getControlContainer(textTransformAdvancedCtrl);

            if (textTransformCtrl.val() != '_advanced')
                textTransformAdvancedContainer.hide();

            textTransformCtrl.on('change', function() {
                var textTransform = $(this).val();

                if (textTransform == '_advanced')
                    textTransformAdvancedContainer.show(500);
                else
                    textTransformAdvancedContainer.hide(500);
            });

            if (options.hidePro)
                this.hidePro();
        },

        getControlContainer: function(ctrl) {
            return ctrl.closest('.control-group');
        },

        hidePro: function() {
            $('.warning-pro').each(function() {
                var $this = $(this);

                $this.prev('.control-group').hide();
                $this.hide();
            });
        }
    };
})(jQuery);