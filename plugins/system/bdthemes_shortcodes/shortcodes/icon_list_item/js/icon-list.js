jQuery(document).ready(function($) {
    // Tabs
    $('body').on('click', '.su-icon-list .icon_list_item', function (e) {
        var $icon_list = $(this),
            data = $icon_list.data();

        // Open specified url
        if (data.url !== '') {
            if (data.target === 'self') window.location = data.url;
            else if (data.target === 'blank') window.open(data.url);
        }
        e.preventDefault();
    });
});