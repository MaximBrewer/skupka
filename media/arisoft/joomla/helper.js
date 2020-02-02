;(function($) {
    if (window["ARIJoomlaHelper"] !== undefined)
        return ;

    function JoomlaHelper() {
        this.init();
    };

    JoomlaHelper.prototype = {
        submitHandlers: [],

        constructor: JoomlaHelper,

        init: function() {
            var self = this;

            var oldSubmitHandler = Joomla.submitform;
            Joomla.submitform = function() {
                self.executeSubmitHandlers();

                oldSubmitHandler.apply(this, arguments);
            }
        },

        registerOnSubmitHandler: function(handler) {
            this.submitHandlers.push(handler);
        },

        executeSubmitHandlers: function() {
            for (var i = 0; i < this.submitHandlers.length; i++) {
                var handler = this.submitHandlers[i];

                handler();
            }
        }
    };

    ARIJoomlaHelper = new JoomlaHelper();
})(jQuery, undefined);