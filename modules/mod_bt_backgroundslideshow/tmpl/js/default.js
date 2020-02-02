var activeContainer = 1;
var currentContainer = 1;
var interval = 0;
var caption = '';
var animating = false;
var seekReady = false;
var player = new Array();
$B =jQuery.noConflict();
$B(document).ready(function() {
	var tag = document.createElement('script');
	tag.src = "//www.youtube.com/iframe_api";
	var firstScriptTag = document.getElementsByTagName('script')[0];	
	firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);						
	$B(bsData.wrapperElement).each(function() {
		$B(this).find('script').remove();
		$B(this).wrapInner('<div class="wrapp-inner"></div>');
		$B(this).append($B('#html-control').html());
		$B('#html-control').remove();
		$B('#cp-bg-navigation').show();
		return false;
	});
	
	if(bsData.slideshowSize=="window"){	
		$B('#cp-bg-slide').css('position','fixed');		
	}
	else{
		$B('#cp-bg-slide').css('position','absolute');
		$B(window).bind('load', function() {
			resizeBackground(activeContainer);
		});
	}
	if(bsData.slideshowSize =="window" && bsData.wrapperElement =="body"){	
		$B('#cp-bg-bar').css('position','fixed');
	}
	else{
		$B('#cp-bg-bar').css('position',bsData.navPosition);
	}
	
	$B('#cp-caption-inner').css('position',bsData.navPosition);
	if(bsData.slideshowSize=="wrapper"){
		$B(bsData.wrapperElement).css('position', 'relative');
	}
	
	
	caption = $B('#cp-caption').html();
	$B('#cp-caption').html('');			
	$B("#cp-bg-slide").append('<div class="loading"></div>');
	resizeBackground();
	// load thumbnail image
	if(bsData.navType=='nav-thumb'){
		$B.each(bsData.photos,function(){
			var img = new Image(); 
			img.src =this.image.replace('/original/','/thumbnail/').replace('/slideshow/','/thumbnail/');
		})
	}else if(bsData.navType=='nav-btn'){
		var img = new Image();
		var pathway = bsData.url + '/modules/mod_bt_backgroundslideshow/tmpl/images/';
		img.src = pathway + 'play.png';
		img.src = pathway + 'pause.png';
		img.src = pathway + 'next.png';
		img.src = pathway + 'back.png';
	}
			
			// load main image
			var total = bsData.photos.length;
			var u = 0;
			$B.each(bsData.photos,function(){
				var img = new Image();
				$B(img).load(function (){
					bsData.photos[u].height = img.height;
					bsData.photos[u].width = img.width;
					++u;
					if (u == total) {
						$B("#cp-bg-slide").find('.loading').fadeOut('fast',function(){$B(this).remove()});
						initSlideShow();
					}
				}).error(function () {
					//$B(bsData.wrapperElement).find('.loading').html('Error: loading');
				}).attr('src',this.image);
			})	
			
			
});
var stopAnimation;
var navigate;
function initSlideShow(){
			$B(window).bind('resize', function() {
				resizeBackground(activeContainer);
			});
			$B(bsData.wrapperElement).css('background', 'none');			
			// Backwards navigation
			$B("#cp-back").click(function() {
				stopAnimation($B('#cp-pause').is(':visible'));
				navigate("back");				
			var showhidevideo =parseInt($B('#cp-bg-bar .progress-button').attr('class').replace('progress-button',''));			
				if(showhidevideo == 0){						
						$B(".fr-video").unbind('mouseenter').unbind('mouseleave');
						if ($B('#cp-video'+ currentContainer).is(':hidden') ) {
							pauseVideo(currentContainer);
						}
				}
				else{							
						if ($B('#cp-video'+ activeContainer).is(':hidden') ) {
							var	autoplay = $B("#play-pause-btn"+activeContainer).attr('rel');								
							if(autoplay == 1){							
								stopAnimation(false);
								$B('#cp-play').show();
								$B('#cp-pause').hide();
								setTimeout('replayVideo(activeContainer)',1200);
							}else{
								if(bsData.autoPlay ==1){
									$B("#progress-background").show();
									stopAnimation(true);
									$B('#cp-play').hide();
									$B('#cp-pause').show();
								}
							}
							if ($B('#cp-video'+ currentContainer).is(':hidden') ) {								
								pauseVideo(currentContainer);
								$B('#play-pause-btn'+currentContainer).removeClass('btn-playing');	
								$B('#play-pause-btn'+currentContainer).removeClass('btn-buffering');
								$B('#play-pause-btn'+currentContainer).addClass('btn-paused');								
							}
								
						}
						else{					
							$B(".fr-video").unbind('mouseenter').unbind('mouseleave');		
							 
							 if ($B('#cp-video'+ currentContainer).is(':hidden') ) {
								pauseVideo(currentContainer);
								$B('#play-pause-btn'+currentContainer).removeClass('btn-playing');	
								$B('#play-pause-btn'+currentContainer).removeClass('btn-buffering');
								$B('#play-pause-btn'+currentContainer).addClass('btn-paused');	
								}
								if(bsData.autoPlay ==1){
								$B("#progress-background").show();
								stopAnimation(true);
								$B('#cp-play').hide();
								$B('#cp-pause').show();
							}
							else{
							$B("#progress-background").show();
							}
							
							 
						}
					}				
			
			});
			// Forward navigation
			$B("#cp-next").click(function() {				
				stopAnimation($B('#cp-pause').is(':visible'));
				navigate("next");
				var showhidevideo =parseInt($B('#cp-bg-bar .progress-button').attr('class').replace('progress-button',''));			
				if(showhidevideo ==0){	
					$B(".fr-video").unbind('mouseenter').unbind('mouseleave');
						if ($B('#cp-video'+ currentContainer).is(':hidden') ) {
						pauseVideo(currentContainer);
					}
					}
				else{						
						if ($B('#cp-video'+ activeContainer).is(':hidden') ) {
							var	autoplay = $B("#play-pause-btn"+activeContainer).attr('rel');							
							if(autoplay == 1){	
								stopAnimation(false);
								$B('#cp-play').show();
								$B('#cp-pause').hide();														
								setTimeout('replayVideo(activeContainer)',1200);
							}
							else{
								if(bsData.autoPlay ==1){
									$B("#progress-background").show();
									stopAnimation(true);
									$B('#cp-play').hide();
									$B('#cp-pause').show();
								}
							}
							if ($B('#cp-video'+ currentContainer).is(':hidden') ) {
							pauseVideo(currentContainer);
							$B('#play-pause-btn'+currentContainer).removeClass('btn-playing');	
							$B('#play-pause-btn'+currentContainer).removeClass('btn-buffering');
							$B('#play-pause-btn'+currentContainer).addClass('btn-paused');	
							}
							
						}
						else{
							$B(".fr-video").unbind('mouseenter').unbind('mouseleave');			
							
							if ($B('#cp-video'+ currentContainer).is(':hidden') ) {
								pauseVideo(currentContainer);
								$B('#play-pause-btn'+currentContainer).removeClass('btn-playing');	
								$B('#play-pause-btn'+currentContainer).removeClass('btn-buffering');
								$B('#play-pause-btn'+currentContainer).addClass('btn-paused');	
							}
							if(bsData.autoPlay ==1){
								$B("#progress-background").show();
								stopAnimation(true);
								$B('#cp-play').hide();
								$B('#cp-pause').show();
							}else{
							$B("#progress-background").show();
							}
						}
						
					}
				
			});
			$B("#cp-pause").click(function() {
				stopAnimation(false);
				$B('#cp-pause').hide();
				$B('#cp-play').show();
			});
			$B("#cp-play").click(function() {
				stopAnimation(true);
				$B("#progress-background").show();	
				$B('#cp-play').hide();
				$B('#cp-pause').show();
			});					
			var first = true;
			navigate = function(direction) {			
				currentContainer = activeContainer;				
				if (!first) {
				if (direction == "next") {			
					if (activeContainer == bsData.photos.length) {
						activeContainer = 1;
					} else if (activeContainer <= bsData.photos.length) {
						activeContainer += 1;
					}
				} else {
					if (activeContainer == 1) {
						activeContainer = bsData.photos.length;
					} else if (activeContainer <= bsData.photos.length) {
						activeContainer -= 1;
					}
				}				
				}				
				showImage(currentContainer, activeContainer);				
				$B('#cp-video'+activeContainer).click(function(){					
						showvideo(activeContainer);												
							setTimeout('replayVideo(activeContainer)',1200);
						
					});	
					if ($B('#cp-video'+ activeContainer).is(':hidden') ) {
						var	autoplay = $B("#play-pause-btn"+activeContainer).attr('rel');							
						if(autoplay == 1){
							stopAnimation(false);
							$B('#cp-play').show();
							$B('#cp-pause').hide();	
							$B("#progress-background").hide();								
							 setTimeout('replayVideo(activeContainer)',bsData.effectTime);
						}else{
								if(bsData.autoPlay ==1){
									$B("#progress-background").show();
									stopAnimation(true);
								};
							}
							
						if ($B('#cp-video'+ currentContainer).is(':hidden') ) {
							pauseVideo(currentContainer);
						}
					}
				else{						
						if ($B('#cp-video'+ currentContainer).is(':hidden') ) {
							pauseVideo(currentContainer);
						}
						if(bsData.autoPlay ==1){
								$B("#progress-background").show();
								stopAnimation(true);
								$B('#cp-play').hide();
								$B('#cp-pause').show();
							}else{
							$B("#progress-background").show();
							}
					}
				
			};			

			var showImage = function(currentContainer, activeContainer) {				
				var	autoplay = $B("#play-pause-btn"+activeContainer).attr('rel');
				var controlvideo =$B('.fr-video').attr('rel');
				var	autohidebar = $B("#ytplayer").attr('rel');
				if (!first) {
					var shvideo = $B("#slideimgs").attr('rel');				
					var effecslide = $B("#slideimg" + currentContainer).attr('rel');
					var ssSize = getSlideShowSize();
					var slidewidth = ssSize.width;
					var slideheight= ssSize.height;
					if(effecslide!=''){					
					switch(effecslide){
						case'top':
							if(activeContainer > currentContainer){
							$B("#slideimg" + activeContainer).css({top: '-'+slideheight+'px'});
							$B("#slideimg" + currentContainer).stop(true).animate( {opacity: 1,top:slidewidth+'px'},1000);
							$B("#slideimg" + activeContainer).show().stop(true).animate( {opacity: 1,top: '0px'}, 1000,function(){												
								if(shvideo == 0){																			
										hidevideo(currentContainer);							
								}else{
									if ($B('#cp-video'+ activeContainer).is(':hidden') ) {									
										showyoubarvideo(activeContainer);
										if(controlvideo ==0){
											if(autohidebar !=2){
												$B('#cp-bg-navigation').addClass('spaceimage');							
												$B('.progress-button').addClass('spacebarimage');
											}
										}										
									}
								}
								$B('.slideimg[id!="slideimg' + activeContainer+'"]').hide();
							});	
							}
							else{							
							$B("#slideimg" + activeContainer).css({top: slideheight+'px'});
							$B("#slideimg" + currentContainer).stop(true).animate( {opacity: 1,top:'-'+slidewidth+'px'}, 1000);
							$B("#slideimg" + activeContainer).show().stop(true).animate( {opacity: 1,top: '0px'}, 1000,function(){												
								if(shvideo ==0){																			
										hidevideo(currentContainer);							
								}else{
									if ($B('#cp-video'+ activeContainer).is(':hidden') ) {									
										showyoubarvideo(activeContainer);
										if(controlvideo ==0){
											if(autohidebar !=2){
											$B('#cp-bg-navigation').addClass('spaceimage');							
											$B('.progress-button').addClass('spacebarimage');	
											}
										}
									}
								}
								$B('.slideimg[id!="slideimg' + activeContainer+'"]').hide();
							});							
							}
																	
							break;
						case'bottom':
							if(activeContainer > currentContainer){
							$B("#slideimg" + activeContainer).css({top: slideheight+'px'});
							$B("#slideimg" + currentContainer).stop(true).animate( {opacity: 1,top:'-'+slidewidth+'px'}, 1000);
							$B("#slideimg" + activeContainer).show().stop(true).animate( {opacity: 1,top: '0px'}, 1000,function(){												
								if(shvideo ==0){																			
										hidevideo(currentContainer);							
								}else{
									if ($B('#cp-video'+ activeContainer).is(':hidden') ) {									
										showyoubarvideo(activeContainer);
										if(controlvideo ==0){
											if(autohidebar !=2){
												$B('#cp-bg-navigation').addClass('spaceimage');							
												$B('.progress-button').addClass('spacebarimage');	
											}
										}
									}
								}
								$B('.slideimg[id!="slideimg' + activeContainer+'"]').hide();
							});
							}
							else{
								$B("#slideimg" + activeContainer).css({top: '-'+slideheight+'px'});
								$B("#slideimg" + currentContainer).stop(true).animate( {opacity: 1,top:slidewidth+'px'},1000);
								$B("#slideimg" + activeContainer).show().stop(true).animate( {opacity: 1,top: '0px'}, 1000,function(){												
									if(shvideo ==0){																			
											hidevideo(currentContainer);							
									}else{
										if ($B('#cp-video'+ activeContainer).is(':hidden') ) {									
											showyoubarvideo(activeContainer);
												if(controlvideo ==0){
													if(autohidebar !=2){
														$B('#cp-bg-navigation').addClass('spaceimage');							
														$B('.progress-button').addClass('spacebarimage');
												}
											}											
										}
								}
									$B('.slideimg[id!="slideimg' + activeContainer+'"]').hide();
								});	
							}
							break;
						case'left':	
							if(activeContainer > currentContainer){							
							$B("#slideimg" + activeContainer).css({left:'-'+slidewidth+'px'});
							$B("#slideimg" + currentContainer).stop(true).animate( {opacity: 1,left:slidewidth+'px'}, 1000);
							$B("#slideimg" + activeContainer).show().stop(true).animate( {opacity: 1,left: '0px'}, 1000,function(){												
								if(shvideo ==0){																			
										hidevideo(currentContainer);							
								}else{
									if ($B('#cp-video'+ activeContainer).is(':hidden') ) {									
										showyoubarvideo(activeContainer);
									if(controlvideo ==0){
										if(autohidebar !=2){
											$B('#cp-bg-navigation').addClass('spaceimage');							
											$B('.progress-button').addClass('spacebarimage');
										}
									}
									}
								}
								$B('.slideimg[id!="slideimg' + activeContainer+'"]').hide();
							});	
							}
							else{							
							$B("#slideimg" + activeContainer).css({left: slidewidth + 'px'});
							$B("#slideimg" + currentContainer).stop(true).animate( {opacity: 1,left:'-'+slidewidth+'px'}, 1000);
							$B("#slideimg" + activeContainer).show().stop(true).animate( {opacity: 1,left: '0px'}, 1000,function(){												
								if(shvideo ==0){																			
										hidevideo(currentContainer);							
								}else{
									if ($B('#cp-video'+ activeContainer).is(':hidden') ) {									
										showyoubarvideo(activeContainer);
										if(controlvideo ==0){
											if(autohidebar !=2){
											$B('#cp-bg-navigation').addClass('spaceimage');							
											$B('.progress-button').addClass('spacebarimage');	
											}
										}
									}
								}
								$B('.slideimg[id!="slideimg' + activeContainer+'"]').hide();
							});							
							}
							
							break;
						case'right':
							if(activeContainer > currentContainer){
							$B("#slideimg" + activeContainer).css({left: slidewidth + 'px'});
							$B("#slideimg" + currentContainer).stop(true).animate( {opacity: 1,left:'-'+slidewidth+'px'}, 1000);
							$B("#slideimg" + activeContainer).show().stop(true).animate( {opacity: 1,left: '0px'}, 1000,function(){												
								if(shvideo ==0){																			
										hidevideo(currentContainer);							
								}else{
									if ($B('#cp-video'+ activeContainer).is(':hidden') ) {									
										showyoubarvideo(activeContainer);
										if(controlvideo ==0){
										if(autohidebar !=2){
											$B('#cp-bg-navigation').addClass('spaceimage');							
											$B('.progress-button').addClass('spacebarimage');
											}
										}
									}
								}
								$B('.slideimg[id!="slideimg' + activeContainer+'"]').hide();
							});
							}
							else{
							$B("#slideimg" + activeContainer).css({left:'-'+slidewidth+'px'});
							$B("#slideimg" + currentContainer).stop(true).animate( {opacity: 1,left:slidewidth+'px'}, 1000);
							$B("#slideimg" + activeContainer).show().stop(true).animate( {opacity: 1,left: '0px'}, 1000,function(){												
								if(shvideo ==0){																			
										hidevideo(currentContainer);							
								}else{
									if ($B('#cp-video'+ activeContainer).is(':hidden') ) {									
										showyoubarvideo(activeContainer);
										if(controlvideo ==0){
											if(autohidebar !=2){
											$B('#cp-bg-navigation').addClass('spaceimage');							
											$B('.progress-button').addClass('spacebarimage');	
										}										
									}
									}
								}
								$B('.slideimg[id!="slideimg' + activeContainer+'"]').hide();
							});	
							}
							break;
						default:
							break;
					}
					
					}
					else{						
						$B("#slideimg" + currentContainer).fadeOut(bsData.effectTime);
						$B("#slideimg" + activeContainer).fadeIn(bsData.effectTime,function(){												
								if(shvideo ==0){																			
										hidevideo(currentContainer);							
								}else{
									if ($B('#cp-video'+ activeContainer).is(':hidden') ) {									
										showyoubarvideo(activeContainer);
										if(controlvideo ==0){
											if(autohidebar !=2){
												$B('#cp-bg-navigation').addClass('spaceimage');							
												$B('.progress-button').addClass('spacebarimage');	
											}
										}
									}
								}
								
							});						
					}						
				} else {					
					activeContainer = currentContainer;
					$B("#slideimg" + activeContainer).show();
					first = false;
				}
				$B("#ytplayer").hide();
				$B(".play-rel").hide();
				$B(".seekbarscroll").hide();
				$B(".mute-btn").hide();
				$B('.progress-button').removeClass('spacebarimage');
				$B('#cp-bg-navigation').removeClass('spaceimage');
				var shvideo = $B("#slideimgs").attr('rel');
				var itemhover = bsData.wrapperElement;;
				if(shvideo == 1){							
				if ($B('#cp-video'+ activeContainer).is(':hidden') ) {
					if(autoplay ==1){
							stopAnimation(false);							
							$B('#cp-play').show();
							$B('#cp-pause').hide();
							$B("#progress-background").hide();													
							bindhover();
						}
						
					}
					else{					
					var youid = $B("#player"+activeContainer).attr("rel");
					if(youid){
						//stopAnimation(false);	
						$B('#cp-video'+ activeContainer).hide();
						if(activeContainer == 1){
						if(controlvideo ==0){
							if(autohidebar !=2){
								$B('#cp-bg-navigation').addClass('spaceimage');
								$B('.progress-button').addClass('spacebarimage');	
							}
						}
						showyoubarvideo(activeContainer);	
						}
						clickvideo(activeContainer);
						bindhover();
					}
					else{					
						$B(".fr-video").unbind('mouseenter').unbind('mouseleave');						
						$B(''+itemhover+'').unbind('mouseenter').unbind('mouseleave');
						$B('body').unbind('mouseenter').unbind('mouseleave');											
						
					}
					}
				}else{
					$B(''+itemhover+'').unbind('mouseenter').unbind('mouseleave');
					$B('body').unbind('mouseenter').unbind('mouseleave');
					$B('#cp-bg-navigation').removeClass('spaceimage');	
					$B('.progress-button').removeClass('spacebarimage');
					$B('#cp-video'+ activeContainer).show();
					if(bsData.autoPlay ==1){
							$B("#progress-background").show();
							stopAnimation(true);
					};
				}
				resizeBackground(activeContainer); 
				changeCaption(activeContainer-1);
			};			

				stopAnimation = function(reset) {			
				progressBarReset();
				clearInterval(interval);
				if (reset) {
					progressBar();
					interval = setInterval(function() {
						navigate("next");
						progressBar();
					}, bsData.slideshowSpeed);
				}
			};
			// We should statically set the first image
			
			navigate("next");

			if (bsData.photos.length >1) {
				if (bsData.autoPlay) {
					if ($B('#cp-video'+ activeContainer).is(':hidden') ) {
						$B("#slideimg"+activeContainer+" img").hide();
						var	autoplayvideo = $B("#play-pause-btn"+activeContainer).attr('rel');							
						if(autoplayvideo == 1){
							stopAnimation(false);
						}
						else{
							stopAnimation(true);
						}
					}
					$B('#cp-pause').show();
					$B('#cp-play').hide();
				} else {
					if ($B('#cp-video'+ activeContainer).is(':hidden') ) {
						$B("#slideimg"+activeContainer+" img").hide();
					}
					$B('#cp-pause').hide();
					$B('#cp-play').show();
				}
				$B('#cp-bg-bar').show();
			}else{
				if ($B('#cp-video'+ activeContainer).is(':hidden') ) {
					$B("#slideimg"+activeContainer+" img").hide();
				}
				$B('.progress-button').hide();
				stopAnimation(false);
				$B('#cp-bg-bar').show();
			}			
			
			// control navigation
			if(bsData.navType=='nav-thumb'){
				var widthThumb = $B('#thumbimgs .thumbimg').outerWidth(true);
				var heightThumb = $B('#thumbimgs .thumbimg').outerHeight(true);
				var length = $B('#thumbimgs .thumbimg').length;
				$B('#thumbimgs').width(widthThumb*bsData.thumbNumber);
				$B('#thumbimgs-inner').width(widthThumb*length);
				$B('#thumbimgs').height(heightThumb);
				$B('.nav-btn').css('marginTop',(heightThumb-$B('.nav-btn').height())/2);
				$B("#nav-back").click(function() {				
						if(animating) return;
						animating= true;
						if(parseInt($B('#thumbimgs-inner').css('left')) < 0 ){
							$B('#thumbimgs-inner').stop();
							if(parseInt($B('#thumbimgs-inner').css('left'))+widthThumb <=0){
								$B('#thumbimgs-inner').animate({left: '+='+widthThumb},function(){animating= false;});
							}else{
								$B('#thumbimgs-inner').animate({left: 0},function(){animating= false;});
							}
						}
						else{
							$B('#thumbimgs-inner').animate({left: -1* widthThumb*(length-bsData.thumbNumber)},function(){animating= false;});
						}
						
				});
				$B("#nav-next").click(function() {					
						if(animating) return;
						animating= true;
						if(Math.abs(parseInt($B('#thumbimgs-inner').css('left'))) < widthThumb*(length-bsData.thumbNumber) ){
							$B('#thumbimgs-inner').stop();
							if(Math.abs(parseInt($B('#thumbimgs-inner').css('left'))) < widthThumb*(length-bsData.thumbNumber)){
								$B('#thumbimgs-inner').animate({left: '-='+widthThumb},function(){animating= false;});
							}else{
								$B('#thumbimgs-inner').animate({left: -1* widthThumb*(length-bsData.thumbNumber)},function(){animating= false;});
							}
						}else{
							$B('#thumbimgs-inner').animate({left: 0},function(){animating= false;});
						}
						
				});
				$B('#thumbimgs .thumbimg').click(function(){				
					if(!$B(this).hasClass('active')){						
							stopAnimation(!bsData.stopAuto);							
							currentContainer = activeContainer;
							activeContainer = parseInt($B(this).attr('id').replace('thumbimg',''));							
							showImage(currentContainer,activeContainer );						
					}	
					var	autoplay = $B("#play-pause-btn"+activeContainer).attr('rel');
					var showhidevideo = $B("#slideimgs").attr('rel');							
					if(showhidevideo == 0){						
						if (!$B('#cp-video'+ activeContainer).is(':hidden') ) {							
							$B("#progress-background").show();							
							if ($B('#cp-video'+ currentContainer).is(':hidden')) {					
								pauseVideo(currentContainer);
							}							
						}						
						$B(".fr-video").unbind('mouseenter').unbind('mouseleave');						
					}
					else{				
						if ($B('#cp-video'+ activeContainer).is(':hidden') ) {								
							bindhover();							
							var	autoplay = $B("#play-pause-btn"+activeContainer).attr('rel');
							if(autoplay == 1){
								stopAnimation(false);	
								$B("#progress-background").hide();
								setTimeout('replayVideo(activeContainer)',1200);
							}else{
								 if(bsData.autoPlay ==1){
								if(currentContainer !=activeContainer){
									stopAnimation(true);
									$B("#progress-background").show();
								 }
								}
							}
							if ($B('#cp-video'+ currentContainer).is(':hidden') && currentContainer!=activeContainer ) {																
								pauseVideo(currentContainer);
								$B('#play-pause-btn'+currentContainer).removeClass('btn-playing');	
								$B('#play-pause-btn'+currentContainer).removeClass('btn-buffering');
								$B('#play-pause-btn'+currentContainer).addClass('btn-paused');						
							}
													
						}
						else{							
							if ($B('#cp-video'+ currentContainer).is(':hidden') && currentContainer!=activeContainer ) {						
								pauseVideo(currentContainer);
								$B('#play-pause-btn'+currentContainer).removeClass('btn-playing');	
								$B('#play-pause-btn'+currentContainer).removeClass('btn-buffering');
								$B('#play-pause-btn'+currentContainer).addClass('btn-paused');		
							}
							$B("#cp-bg-bar").animate({opacity: "1",bottom :'0px'}, 20);
							$B(".fr-video").unbind('mouseenter').unbind('mouseleave');						
							$B('#cp-bg-navigation').removeClass('spaceimage');
							if(bsData.autoPlay == 1){
								if(currentContainer !=activeContainer){
									$B("#progress-background").show();
									stopAnimation(true);
								}
							}else{
							$B("#progress-background").show();
							}
														
						}
					}						
					
					$B('#cp-video'+activeContainer).click(function(){						
						showvideo(activeContainer);								
							setTimeout('replayVideo(activeContainer)',1200);						
					});						
				});				
			}			
		var showvideo =(function(activeContainer){					
					$B("#slideimg" + activeContainer + ' .fr-video').show();
					showyoubarvideo(activeContainer);
					$B("#progress-background").hide();							
					$B('#cp-video'+ activeContainer).hide();
					$B('#cp-play').show();
					$B('#cp-pause').hide();
					var controlvideo =$B('.fr-video').attr('rel');
					var	autohidebar = $B("#ytplayer").attr('rel');
					if(controlvideo ==0){
						if(autohidebar !=2){
							$B('#cp-bg-navigation').addClass('spaceimage');	
							$B('.progress-button').addClass('spacebarimage');
						}
					}
					stopAnimation(false);					
					clickvideo(activeContainer);					
					   //onYouTubePlayerAPIReady();
										
			});
		var hidevideo = (function(activeContainer){				
				$B("#slideimg" + activeContainer + ' .fr-video').hide();				
			});
		controlPosition();
		
}

function onYouTubePlayerAPIReady() {
	$B('.fr-video').each(function(){
		$player = $B('.player', this);
		var ytid = $player.attr('rel');	
		var id = $player.attr('id').replace('player','');
		var controlvideo =$B('.fr-video').attr('rel');
		var	autohidebar = $B("#ytplayer").attr('rel');
		if (autohidebar == 2) controlvideo = 0;
		player[id] = new YT.Player('player'+ id, {		
			height: '100%',
			width: '100%',
			videoId: ytid,
			playerVars: {
				'controls': controlvideo,
				'autoplay': 0,
				'hd': 1,
				'autohide':autohidebar,
				'wmode': 'opaque',
				'modestbranding':1,
				'showinfo':0
			},
			events: {
				'onReady': onPlayerReady,
				'onStateChange': onPlayerStateChange		
			}		
		});			
	});	
}

var clickvideo = (function(activeContainer){
			$B('#play-pause-btn'+activeContainer).click(function() {			
						playPause(activeContainer);
						$B("#progress-background").hide();
						stopAnimation(false);
					});
					$B('#seekbar'+activeContainer).click(function(e) {					
						if(!seekReady) return;
						else {
							var navposition = bsData.navPosition;
							if(navposition =="absolute"){
								if(bsData.slideshowSize =="specific"){
									wSize = bsData.slideshowWidth;
									}else{
									var itemhover = bsData.wrapperElement;					
										wSize = $B(''+itemhover+'').width();
								}
							}
							else{
							wSize = $B(window).width();
							}
							var localX = (e.pageX - $B(this).offset().left);							
							if(localX >  (wSize-81)) localX =  (wSize-81);						
							var percent = localX /  (wSize-81);
							seekToPercent(percent);
						}
					});
					$B('#mute-btn'+activeContainer).click(function() {					
						 toggleSound();
					});	
					var	autohidebar = $B("#ytplayer").attr('rel');
					var heightthumb = (bsData.thumbHeight +40);					
					if(autohidebar ==1){					
						$B("#cp-bg-bar").stop(true).animate({opacity: "1",bottom :'0px'}, 800);
						//setTimeout('$B("#cp-bg-bar").stop(true).animate({opacity: "1",bottom :"-'+heightthumb+'px"}, 800);', 5000);
					}
					else{
						$B("#cp-bg-bar").stop(true).animate({opacity: "1",bottom :'0px'}, 800);
					}
					bindhover();
});
var i = null;
var bindhover = (function(){	
	$B('.fr-video').bind({
		mouseenter: function() {		
			clearTimeout(i);
			$B("#cp-bg-bar").stop(true).animate({opacity: "1",bottom :'0px'}, 500);
			// i = setTimeout('$B("#cp-bg-bar").stop(true).animate({opacity: "0.4",bottom :"-100px"}, 1400);', 3500);
		},		
		mouseleave:function(){
			var heightthumb = (bsData.thumbHeight +40);	
				clearTimeout(i);
				i = setTimeout('$B("#cp-bg-bar").stop(true).animate({opacity: "1",bottom :"0px"}, 1000);', 500);
	   }
	  });	
	  $B("#cp-bg-bar").bind({
	  mouseenter :function() {
				clearTimeout(i);
			$B("#cp-bg-bar").stop(true).animate({opacity: "1",bottom :'0px'}, 800);
		}
		});

});
var showyoubarvideo =(function(activeContainer){
	var controlvideo =$B('.fr-video').attr('rel');
	var	autohidebar = $B("#ytplayer").attr('rel');
	if(controlvideo ==0){
		if(autohidebar !=2){
			$B("#play-pause-btn"+activeContainer).fadeIn('slow');	
			$B("#seekbar"+activeContainer).fadeIn('slow');	
			$B("#mute-btn"+activeContainer).fadeIn('slow');
			$B("#ytplayer").fadeIn('slow');
		}
	}
	//$B("#ytplayer").css({'margin-bottom':'40px'});
	var navposition ;
	if(bsData.slideshowSize =="window" && bsData.wrapperElement =="body"){	
		navposition='fixed';
	}else{	
	navposition = bsData.navPosition;
	}
	var itemhover = bsData.wrapperElement;
	var heightthumb = (bsData.thumbHeight +40);
	if(itemhover!='body'){	
	$B(''+itemhover+'').css({'overflow':'hidden'});
	}
	if(autohidebar == 1){
	if(navposition == 'absolute'){	
	
		if(itemhover!='body' && itemhover!= ""){			
				$B(''+itemhover+'').bind({
				mouseenter: function() {		
					clearTimeout(i);					
					$B("#cp-bg-bar").stop(true).animate({opacity: "1",bottom :'0px'}, 500);
					// i = setTimeout('$B("#cp-bg-bar").stop(true).animate({opacity: "0.4",bottom :"-100px"}, 1400);', 3500);
				},
				mousemove:function(p){
					clearTimeout(i);
					$B("#cp-bg-bar").stop(true).animate({opacity: "1",bottom :'0px'}, 500);					 
				},
				mouseleave:function(){		
					clearTimeout(i);
					i = setTimeout('$B("#cp-bg-bar").stop(true).animate({opacity: "1",bottom :"-'+heightthumb+'px"}, 1000);', 500);
			   }
				
			});
			
			}
			else{			
			$B('body').bind({
			
				mouseenter: function() {		
					clearTimeout(i);
					$B("#cp-bg-bar").stop(true).animate({opacity: "1",bottom :'0px'}, 500);
					// i = setTimeout('$B("#cp-bg-bar").stop(true).animate({opacity: "0.4",bottom :"-100px"}, 1400);', 3500);
				},
				mousemove:function(p){
					clearTimeout(i);
					$B("#cp-bg-bar").stop(true).animate({opacity: "1",bottom :'0px'}, 500);
					 
				},
				mouseleave:function(){
					var heightthumb = (bsData.thumbHeight +40);
						clearTimeout(i);						
					 $B("#cp-bg-bar").stop(true).animate({opacity: "1",bottom :'-'+heightthumb+'px'}, 1000);
			   }
				
			});
			}
		}
		else{		
			$B('body').bind({
				mouseenter: function() {		
					clearTimeout(i);
					$B("#cp-bg-bar").stop(true).animate({opacity: "1",bottom :'0px'}, 500);
					// i = setTimeout('$B("#cp-bg-bar").stop(true).animate({opacity: "0.4",bottom :"-100px"}, 1400);', 3500);
				},
				mousemove:function(p){
					clearTimeout(i);
					$B("#cp-bg-bar").stop(true).animate({opacity: "1",bottom :'0px'}, 500);
					 
				},
				mouseleave:function(){
					var heightthumb = (bsData.thumbHeight +40);
						clearTimeout(i);						
					 $B("#cp-bg-bar").stop(true).animate({opacity: "1",bottom :'-'+heightthumb+'px'}, 1000);
			   }
				
			});
		}
	}
});
function onPlayerReady(event) {
	var autoplay = 0;	
	var	volume = $B("#mute-btn"+activeContainer).attr('rel');
	var	quality = $B("#seekbar"+activeContainer).attr('rel');		
        event.target.setVolume(volume);
		event.target.setPlaybackQuality(quality);
	if(autoplay == 0){
		event.target.stopVideo();
	}
	else{
		event.target.playVideo();
	}
	if ($B('#cp-video'+ activeContainer).is(':hidden') ) {				 
		 var autoplay = $B("#play-pause-btn"+activeContainer).attr('rel');							
			if(autoplay == 1){					
					replayVideo(activeContainer);
			}
	}
 } 
 var done = false;
 var i =0;
function onPlayerStateChange(event) { 
     switch(event.data){
	   case -1: // unstarted
			return;
		case 0: // ended			
			resetPlayer();
			if(bsData.autoPlay == 1){
				if (bsData.photos.length >1) {
					$B("#progress-background").show();
					clearTimeout(i);
					i = setTimeout(function(){navigate("next");}, 500);					
					stopAnimation(true);
					
				}
			}
							
			return;
		case 1: // playing	
			$B("#progress-background").hide();
			stopAnimation(false);
			$B('#cp-play').show();
			$B('#cp-pause').hide();
			seekReady = true;
			playheadInterval = setInterval(updatePlayhead, 10);	
			$B('#play-pause-btn'+activeContainer).removeClass('btn-paused');		
			$B('#play-pause-btn'+activeContainer).removeClass('btn-buffering');		
			$B('#play-pause-btn'+activeContainer).addClass('btn-playing');			
			return;
		case 2: // paused
			$B('#play-pause-btn'+activeContainer).removeClass('btn-playing');	
			$B('#play-pause-btn'+activeContainer).removeClass('btn-buffering');
			$B('#play-pause-btn'+activeContainer).addClass('btn-paused');
			//clearInterval(playheadInterval);
			return;
		case 3: // buffering
			$B('#play-pause-btn'+activeContainer).removeClass('btn-playing');	
			$B('#play-pause-btn'+activeContainer).removeClass('btn-paused');
			$B('#play-pause-btn'+activeContainer).addClass('btn-buffering');
			return;
		case 5: // video cued
			resetPlayer();
			ready = true;
			return;
	}
 }
 
var updatePlayhead = function(){
	if(	player[activeContainer]){
		if(typeof(player[activeContainer].getCurrentTime) == 'undefined') {	
			clearInterval(playheadInterval);
			return;
		}	
		var navposition = bsData.navPosition;
		if(navposition =="absolute"){
			if(bsData.slideshowSize =="specific"){
				wSize = bsData.slideshowWidth;
				}else{
				var itemhover = bsData.wrapperElement;					
					wSize = $B(''+itemhover+'').width();
			}
		}else{
		wSize = $B(window).width();
		}
		var percentage = player[activeContainer ].getCurrentTime() / player[activeContainer].getDuration();
		$B('#seekbar'+activeContainer).css('background-position', Math.round(percentage * (wSize-81)) + 'px 0px');
	}
}

  var resetPlayer = function() {		
	$B('#play-pause-btn'+activeContainer).removeClass('btn-playing')		
	$B('#play-pause-btn'+activeContainer).removeClass('btn-buffering')		
	$B('#play-pause-btn'+activeContainer).addClass('btn-paused');			
	$B('#seekbar'+activeContainer).css('background-position', '0px 0px');	
}

var seekToPercent = function(percent) {
	var time = percent * player[activeContainer].getDuration();
	player[activeContainer].seekTo(time, true);
}

var toggleSound = function() {
	if(player[activeContainer].isMuted()) {
		player[activeContainer].unMute();
		$B('#mute-btn'+activeContainer).css('background-position', '0px 0px');
	} else {
		player[activeContainer].mute();
		$B('#mute-btn'+activeContainer).css('background-position', '0px -40px');
	}
}

function pauseVideo(activeContainer) {
  if(typeof (player[activeContainer])!= "undefined"){
	if(typeof(player[activeContainer].pauseVideo())!= 'function'){
		player[activeContainer].pauseVideo();
	}
 }
} 

function replayVideo(activeContainer) {
 if(typeof (player[activeContainer])!= "undefined"){
	//if(typeof(player[activeContainer].playVideo()) != 'function'){
        player[activeContainer].playVideo();
	//}		
  }		
 }
 
function stopVideo() {
        player[activeContainer].stopVideo();
  }	
  
var playPause = function(activeContainer) {
	var index = activeContainer  ;
	if(typeof(player[index].getPlayerState()) != 'function'){
		player[index].playVideo();
	}
	switch(player[index].getPlayerState() ) {
			case -1: // unstarted
			case 0: // ended
			case 2: // paused
			case 5: // cued
				player[index].playVideo();
				return;
			case 1: // playing
				player[index].pauseVideo();
				return;
			case 3: //buffering pause;
				player[index].pauseVideo();
				return;
			default:
				return;
		}
}

function changeCaption(index) {
	// change caption
	var photoObj = bsData.photos[index];
	$B('#cp-caption').fadeOut(function() {
		
		if(!photoObj.desc && !photoObj.title){
			$B("#cp-caption").html('');
			return;
		}
		$B("#cp-caption").html(caption);
		if ($B("#cp-caption .cp-link").length && photoObj.link != '') {
			$B("#cp-caption .cp-link").html(photoObj.title);
			$B("#cp-caption .cp-link").attr('href', photoObj.link);
			$B("#cp-caption .cp-link").attr('target', photoObj.target);
		} else {
			$B("#cp-caption .cp-title").html(photoObj.title);
		}
		if(photoObj.desc){
			$B("#cp-caption .cp-desc").html(photoObj.desc);
		}else{
			$B("#cp-caption .cp-desc").remove();
		}
		
		$B("#cp-caption").fadeIn(bsData.effectTime);
	});
	
	// change navigation
	if(bsData.navType=='nav-thumb'){
		index++;
		//var oldIndex = $B('#thumbimgs .active').attr('id') + '';
		//oldIndex = parseInt(oldIndex.replace('thumbimg',''));
		$B('#thumbimgs .thumbimg').removeClass('active');
		$B('#thumbimg'+index).addClass('active');
		var width= $B('#thumbimgs .active').outerWidth(true);
		var left = $B('#thumbimgs .active').position().left +  $B('#thumbimgs .active').outerWidth(true);
		var step = Math.round(($B('#thumbimgs .active').position().left+$B('#thumbimgs-inner').position().left)/width);
		if(step>=bsData.thumbNumber){
			move = width * (step- bsData.thumbNumber+1);
			$B('#thumbimgs-inner').animate({left: '-='+move});
		}
		if(step<0){
			move = -1 * width * step ;
			$B('#thumbimgs-inner').animate({left: '+='+move});
		}
	}
}

/** * CHANGE IMAGE SIZE ** */
function resizeBackground(index) {
	var el = "#slideimg" + index + ' img';
	$B('#cp-bg-slide').css({ 'width' : 'auto', 'height' : 'auto' });
	var ssSize = getSlideShowSize();
	wSize = ssSize.width + 'px';
	wHeight = ssSize.height + 'px';
	var hAlign = false;
	var vAlign = true;
	var img = bsData.photos[index-1];
	if($B(el).length){
		switch(bsData.resizeImage){
			case 'fitwidth':
				$B("#slideimgs img").css('cssText','width:'+wSize+'!important;height:auto');
				break;
			case 'fitheight':
				$B("#slideimgs img").css('cssText','height:'+wHeight+'!important;width:auto');
				hAlign = true;
				vAlign = false;
				break;
			case 'stretch':
				$B("#slideimgs img").css('cssText','width:'+wSize+'!important;height:'+wHeight+'!important');
				vAlign = false;
				break;
			case 'auto':
				if (ssSize.width * parseInt(img.height)	/ parseInt(img.width) < ssSize.height) {
					$B(el).css('cssText','height:'+wHeight+'!important;width:auto');
					hAlign = true;
					vAlign = false;
				}else{
					$B(el).css('cssText','width:'+wSize+'!important;height:auto');
				}
				break;
			default:
				hAlign=true;
		}
	}

	if(bsData.slideshowSize!="window" && bsData.navPosition =='absolute'){
	var shvideo = $B("#slideimgs").attr('rel');
		if(shvideo == 1){		
			$B('#cp-bg-bar').css({'bottom':'0px'});
		}else{
			$B('#cp-bg-bar').css({'top':ssSize.height - $B('#cp-bg-bar').height()});			
		}
	} 
	
	$B('#cp-bg-slide').css({ 'width' : wSize, 'height' : wHeight });

	$B(el).css('position','absolute');
	if(hAlign){
		switch(bsData.hAlign){
			case 'l':
				$B(el).css('left',0);
				break;
			case 'r':
				$B(el).css({'left':'auto','right':0});
				break;
			case 'c':
				$B(el).css('left',((ssSize.width-$B(el).width())/2));
				break;
		}
	}else{
		$B(el).css('left',0);
	}
	if(vAlign){
		switch(bsData.vAlign){
			case 't':
				$B(el).css('top',0);
				break;
			case 'b':
				$B(el).css({'top':'auto','bottom':0});
				break;
			case 'm':
				$B(el).css('top',((ssSize.height-$B(el).height())/2));
				break;
		}
	}else{
		$B("#slideimg" + index).css('top',0);
	}	
	controlPosition();
}
function progressBar() {
	var ssSize = bsData.navPosition =='fixed'? {width:$B(window).width(),height:$B(window).height()}: getSlideShowSize();
	$B('#progress-bar,progress-background').width(ssSize.width+'px');
	$B('#progress-bar').stop()
			.animate({ left : -ssSize.width }, 0)
			.animate({ left : 0 }, bsData.slideshowSpeed);
}
function progressBarReset() {
	var ssSize = bsData.navPosition =='fixed'? {width:$B(window).width(),height:$B(window).height()}: getSlideShowSize();
	$B('#progress-bar').stop().css('left', -ssSize.width);
}
function preloadImage(photos) {
	//var img = new Image();
	//img.src = bsData.url + '/modules/mod_bt_backgroundslideshow/tmpl/images/loading.gif';
}
function controlPosition(){
	// control button 
	var ssSize = bsData.navPosition =='fixed'? {width:$B(window).width(),height:$B(window).height()}: getSlideShowSize();
	var el = '';
	if(bsData.navType=='nav-btn'){
		el = '.progress-button';
	}
	else if(bsData.navType=='nav-thumb'){
		el = '#cp-bg-navigation';
	}else{
		return;
	}
	if(bsData.navAlign == 'left'){
		$B(el).css({'float':bsData.navAlign});
	}else if(bsData.navAlign == 'right'){
		$B(el).css({'float':bsData.navAlign,'right':0});
	}else if(bsData.navAlign == 'center'){
		$B(el).css('left',(ssSize.width - $B(el).width())/2);
	}else if(bsData.navAlign == 'justify'){
		$B('.progress-button').addClass('button-justify');
		$B('#cp-bg-bar').height(0);
		$B('#cp-back,#cp-next,#cp-pause,#cp-play').css({'top':-1*(ssSize.height+$B('#cp-back').height())/2});
	}
}
function getSlideShowSize(){
	switch(bsData.slideshowSize){
		case 'document':
			return {width:$B(document).width(),height:$B(document).height()};
		case 'wrapper':
			return {width:$B(bsData.wrapperElement).innerWidth(),height:$B(bsData.wrapperElement).innerHeight()};
		case 'specific':
			return {width:(bsData.slideshowWidth?bsData.slideshowWidth:$B(bsData.wrapperElement).innerWidth()),height:(bsData.slideshowHeight?bsData.slideshowHeight:$B(bsData.wrapperElement).innerHeight())};
		default:
			return {width:$B(window).width(),height:$B(window).height()};
	}
};