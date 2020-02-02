/*!
 * jquery.instagramFeed
 * @version 1.0
 * @author Javier Sanahuja Liebana <bannss1@gmail.com>
 * https://github.com/BanNsS1/jquery.instagramFeed
 * Modified by @bdthemes
 *
 */
(function($){
	var defaults = {
		'username'        	: '',
		'container'       	: '',
		'layout'		  	: 'grid',
		'show_profile'    	: false,
		'show_follow_me'   	: false,
		'follow_me_text'   	: '',
		'show_biography'  	: false,
		'show_lightbox'   	: false,
		'display_gallery' 	: true,
		'get_raw_json'    	: false,
		'callback'        	: null,
		'items'           	: 8,
		'columns'         	: 4,
		'columns_tablet'  	: 3,
		'columns_mobile'  	: 2,
		'column_gap'      	: 'small',
		'like_count'      	: false,
		'comment_count'   	: false,
		'loading_animation'	: false
	};
	$.instagramFeed = function(options){
		options = $.fn.extend({}, defaults, options);
		if(options.username == "" && options.tag == ""){
			console.log("Instagram: Error, no username or tag found.");
			return;
		}	
		if(!options.get_raw_json && options.container == ""){
			console.log("Instagram: Error, no container found.");
			return;
		}
		if(options.get_raw_json && options.callback == null){
			console.log("Instagram: Error, no callback defined to get the raw json");
			return;
		}

		var url = "https://www.instagram.com/"+ options.username;
		$.get(url, function(data){
			data = data.split("window._sharedData = ");
			data = data[1].split("<\/script>");
			data = data[0];
			data = data.substr(0, data.length - 1);
			data = JSON.parse(data);
			data = data.entry_data.ProfilePage[0].graphql.user;
			
			//console.log(data);			
			if(options.get_raw_json){
				options.callback(JSON.stringify({
					id: data.id,
					username: data.username,
					full_name: data.full_name,
					is_private: data.is_private,
					is_verified: data.is_verified,
					biography: data.biography,
					followed_by: data.edge_followed_by.count,
					following: data.edge_follow.count,
					'images': data.edge_owner_to_timeline_media.edges,
				}));
				return;
			}
			
			var html = "";
			if(options.show_profile){
				html += "<div class='bdt-instagram-profile'>";
				html += "<img class='bdt-instagram-profile-image' src='"+ data.profile_pic_url +"' alt='"+ options.username +" profile pic' />";
				html += "<p class='bdt-instagram-username'>@"+ data.full_name +" (<a href='https://www.instagram.com/"+ options.username +"'>@"+options.username+"</a>)</p>";
			}
			if(options.show_follow_me){
				html += "<div class='bdt-instagram-follow-me bdt-position-z-index bdt-position-center'><a href='https://www.instagram.com/"+ options.username +"'>" + options.follow_me_text + " @"+options.username+"</a></div>";
			}
			
			if(options.show_biography){
				html += "	<p class='bdt-instagram-biography'>"+ data.biography +"</p>";
			}
			
			if(options.show_profile){
				html += "</div>";
			}
			
			if(options.display_gallery){
				if(data.is_private){
					html += "<p class='bdt-instagram-private'><strong>This profile is private</strong></p>";
				} else {
					var imgs = data.edge_owner_to_timeline_media.edges;
						max = (imgs.length > options.items) ? options.items : imgs.length;
						lightbox = (options.show_lightbox) ? ' bdt-lightbox' : '';
						column_gap = (options.column_gap) ? ' su-row '+options.column_gap : '';
						scrollspy = (options.loading_animation) ? " bdt-scrollspy='cls: bdt-animation-fade; target: > .bdt-instagram-item-wrapper > .bdt-instagram-item; delay: 350;'" : '';
					
					
					html += "<div class='su-row'>";
					
						
					for(var i = 0; i < max; i++) {
						
						//console.log(imgs[i].node.edge_media_preview_like.count); // like count
						//console.log(imgs[i].node.edge_media_to_comment.count); // comment count
						//console.log(imgs[i].node.is_video); // is it video?
						//console.log(imgs[i].node.edge_media_to_caption.edges[0].node.text); // is it video?
						//"+imgs[i].node.edge_media_to_caption.edges[0].node['text']+"

						if (options.show_lightbox) {
							var url = imgs[i].node.display_url; // get full image
						} else {
							var url = "https://www.instagram.com/p/"+ imgs[i].node.shortcode;
						}

						var like_comment = "";						
							like_comment += "<div class='bdt-instagram-like-comment bdt-flex-center bdt-child-width-auto bdt-grid'>";
							if (options.like_count) {
								like_comment += "<span><span class='fa fa-heart'></span> <b>"+imgs[i].node.edge_media_preview_like.count+"</b></span>";
							}
							if (options.comment_count) {
								like_comment += "<span><span class='fa fa-comment'></span> <b>"+imgs[i].node.edge_media_to_comment.count+"</b></span>";
							}							
							like_comment += "</div>";

						if (options.like_count || options.comment_count || options.show_lightbox) {
							var animation = "";
						} else {
							var animation = "bdt-transition-scale-up";
						}
						

						html += "<div class='bdt-instagram-item-wrapper su-column su-column-size-1-"+ options.columns_mobile +" su-column-size-small-1-"+ options.columns_tablet +" su-column-size-medium-1-"+ options.columns +"'>";
						html += "<div class='bdt-instagram-item bdt-transition-toggle bdt-position-relative bdt-inline-clip'>";
						html += "<a href='"+url+"' target='_blank' class='' data-elementor-open-lightbox='no'>";
						html += "<img class='"+animation+"' src='"+ imgs[i].node.thumbnail_src +"' alt='"+ options.username +" instagram image "+ i +"' bdt-img />";

						if (options.show_lightbox) {
							html += "<div class='bdt-transition-fade bdt-inline-clip bdt-position-cover bdt-position-small bdt-overlay bdt-overlay-default '><span class='bdt-position-center' bdt-overlay-icon></span>"+like_comment+"</div>";
						} else {
							if (options.like_count || options.comment_count ) {
								html += "<div class='bdt-transition-fade bdt-inline-clip bdt-position-cover bdt-position-small bdt-overlay bdt-overlay-default bdt-flex bdt-flex-center bdt-flex-middle'>"+like_comment+"</div>";
							}
						}						

						html += "</a>";
						html += "</div>";
						html += "</div>";
					}

					html += "</div>";

					if (options.layout === 'carousel') {
						html += "<a class='bdt-position-center-left bdt-position-small bdt-hidden-hover bdt-visible@m' href='#' bdt-slidenav-previous bdt-slider-item='previous'></a>";
						html += "<a class='bdt-position-center-right bdt-position-small bdt-hidden-hover bdt-visible@m' href='#' bdt-slidenav-next bdt-slider-item='next'></a>";
						html += "</div>";
					} 
				}
			}
			$(options.container).html(html);
		});
	};
	
})(jQuery);