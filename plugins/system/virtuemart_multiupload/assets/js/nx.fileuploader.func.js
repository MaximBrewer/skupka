jQuery(document).ready(function() {
	var upload_form_tmpl = '<div id="file-uploader"><p></p></div><ul id="separate-list"></ul>';
	jQuery('.selectimage').first().append(upload_form_tmpl);
	createUploader();
});
