/**
 * @author Sakis Terzis
 * @license GNU/GPL v.2
 * @copyright Copyright (C) 2013 breakDesigns.net. All rights reserved
 */

window.addEvent('domready', function() {
	var displayTypesDropDown = $$('.cfDisplayTypes');

	displayTypesDropDown.addEvent('change', function() {
		var selected_val = this.getElement(':selected').value;
		var dropdown_id = this.getProperty('id');
		var filterid = dropdown_id.substring(7);	
		var advSettingLink = document.id('show_popup' + filterid);

		// display advanced settings link
		if (display_types_advanced.indexOf(selected_val) >= 0) {
			advSettingLink.removeClass('cfhide');
			displayPopup(filterid);
		}else {
			advSettingLink.addClass('cfhide');
		}
	});

	if (document.id('cfOptimizerForm') != null) {
		document.id('cfOptimizerForm').addEvent('submit', function(e) {
			e.stop();
			var target=document.id('optimizer_results');
			var req = new Request.JSON({
				method : 'post',
				onRequest : function() {
					target.addClass('cf_spinner');

				},
				onSuccess : function(response) {
					setInterval(function(){displayResults(response,target);},2000);
					
				}
			});
			req.post(this);
		});
	}
	
	/**
	 * Display the results of the optimizer
	 * @since 1.9.5
	 * @author Sakis Terz
	 */
	function displayResults(response,target){
		var found=response.found.length;
		var notFound=response.Notfound.length;
		var added=response.added.length;
		var success=response.success;
		var html='<div class="cf_log_wrapper">';
		if (success!=-1){
			html+='<span class="msg_neutral">Indexes Found: '+found+'</span><br/>';
			html+='<span class="msg_division">'+notFound+'/'+added+' Indexes Added.</span> <br/><span class="msg_precentage">Success'+parseInt(success)*100+'%</span>';
		}else html+='<span class="msg_neutral">No missing indexes found. No action required</span>';
		html+='</div>';
		target.removeClass('cf_spinner');
		target.innerHTML=html;
	}
})