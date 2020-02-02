;
window.NfireEvent = function(element, event) {
    if (document.createEventObject) {
        if (njQuery)
            return njQuery(element).trigger(event);
        // dispatch for IE
        var evt = document.createEventObject();
        return element.fireEvent('on' + event, evt);
    } else {
        // dispatch for firefox + others
        var evt = document.createEvent("HTMLEvents");
        evt.initEvent(event, true, true); // event type,bubbling,cancelable
        return !element.dispatchEvent(evt);
    }
};
(function(dojo) {
    dojo.declare("NextendElement", null, {
        constructor: function(args) {

        },
        fireEvent: window.NfireEvent
    });
})(ndojo);