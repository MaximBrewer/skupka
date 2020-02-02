/*global XDjQuery,jQ,jQuery,window*/
(function ($) {
	'use strict';
	window.initAutoComplete = function () {
		$(".xdsoft_search_input").autocomplete({
			filter: false,
			dropdownWidth: 'auto',
			appendMethod: 'replace',
			valid: function () {
				return true;
			},
			getTitle: function (item) {
				return item.GeoObject.metaDataProperty.GeocoderMetaData.text;
			},
			getValue: function (item) {
				return item.GeoObject.metaDataProperty.GeocoderMetaData.text;
			},
			source: [
				function (q, add) {
					$.getJSON("//geocode-maps.yandex.ru/1.x/?callback=?&format=json&sco=latlong&geocode=" + encodeURIComponent(q), function (data) {
						var suggestions = [];
						if (data.response) {
							$.each(data.response.GeoObjectCollection.featureMember, function (i, val) {
								suggestions.push(val);
							});
							add(suggestions);
						}
					});
				}
			]
		});
	};
	$(window.initAutoComplete);
	if (document.readyState !== 'complete') {
		window.initAutoComplete();
	}
}(window.XDjQuery || window.jQ || window.jQuery));