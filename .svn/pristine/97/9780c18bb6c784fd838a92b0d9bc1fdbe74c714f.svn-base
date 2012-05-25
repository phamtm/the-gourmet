$(document).ready(function() {
	$.preload([ 'events_nav_left_btn_over', 'events_nav_mid_btn_over', 'events_nav_right_btn_over' ], {
	    base:'assets/images/',
	    ext:'.png'
	});
	$('#featuredSlideshow').cycle({
		fx: 'scrollHorz',
		timeout: 5000,
		cleartypeNoBg: true,
		next: '#featured .nextArrow',
		prev: '#featured .prevArrow',
		activePagerClass: 'active',
		pager: '#slideshowProgress',
		pause: true,
		pagerAnchorBuilder: function(idx, slide) {
			return '<li><a href="javascript:void(0);">' + (idx+1) + '</a></li>';
		}
	});
	$('#sideSlideshow ul').cycle({
		fx: 'scrollHorz',
		timeout: 0,
		next: '#sideSlideshow .nextArrow',
		prev: '#sideSlideshow .prevArrow',
		cleartypeNoBg: true
	});
	$('.contentTopPhoto .slideshow').cycle({
		fx: 'fade',
		timeout: 3000,
		cleartypeNoBg: true
	});
	$('#brandSlideshow ul').cycle({
		fx: 'scrollHorz',
		timeout: 0,
		next: '#brandSlideshow .nextArrow',
		prev: '#brandSlideshow .prevArrow',
		cleartypeNoBg: true
	});
	$("#staffList .staffHeader").hover(
		function() {
			$(this).find('h3').addClass("hover");
		},
		function() {
			$(this).find('h3').removeClass("hover");
		}
	);
	$('#staffList .staffHeader').click(function() {
		if($(this).hasClass('active')) {
			$(this).removeClass("active");
			$(this).find('h3').removeClass("active");
			$(this).next(".staffBio").slideToggle("slow");
		} else {
			$(this).addClass("active");
			$(this).find('h3').addClass("active");
			$(this).next(".staffBio").slideToggle("slow");
			return;
		}
		});
	$('.aboutHeader').click(function() {
	if($(this).hasClass('active')) {
		$(this).removeClass("active");
		$(this).find('h3').removeClass("active");
		$(this).next(".aboutContent").slideToggle("slow");
	} else {
		$(this).addClass("active");
		$(this).find('h3').addClass("active");
		$(this).next(".aboutContent").slideToggle("slow");
		return;
	}
	});
	$("#tipsList .tipsHeader").hover(
		function() {
			$(this).find('h3').addClass("hover");
		},
		function() {
			$(this).find('h3').removeClass("hover");
		}
	);
	$('#tipsList .tipsHeader').click(function() {
		if ($(this).hasClass('active')) {
			$(this).removeClass("active");
			$(this).find('h3').removeClass("active");
			$(this).next(".tipsContent").slideToggle("slow");
		} else {
			$(this).addClass("active");
			$(this).find('h3').addClass("active");
			$(this).next(".tipsContent").slideToggle("slow");
			return;
		}
		});
		$("a.gallery-thumb").fancybox({
		'transitionIn'	:	'elastic',
		'transitionOut'	:	'elastic',
		'speedIn'		:	600, 
		'speedOut'		:	200, 
		'overlayShow'	:	true
	});
	$(".scroll-pane").jScrollPane({showArrows: true});
	$('#searchField input').one("focus", function() {
		$(this).val("");
	});
	$("#searchSubmit").hover(
		function() {
			$(this).attr('src', '/assets/images/side_search_bt_over.gif');
		},
		function() {
			$(this).attr('src', '/assets/images/side_search_bt.gif');
		}
	);
	$("#signupSubmit").hover(
		function() {
			$(this).attr('src', '/assets/images/footer_signup_bt_over.gif');
		},
		function() {
			$(this).attr('src', '/assets/images/footer_signup_bt.gif');
		}
	);
	$("#commentSubmit").hover(
		function() {
			$(this).attr('src', '/assets/images/submit_bt_over.gif');
		},
		function() {
			$(this).attr('src', '/assets/images/submit_bt.gif');
		}
	);
});
