$(document).ready(function() {

$('#banner .homeNav li.panel1').hover(
	function() {
		$('#banner .mouseover1').fadeIn();
		$('#banner .homeNav li.panel1 .button a').addClass('hover');
	},
	function() {
		$('#banner .mouseover1').fadeOut();
		$('#banner .homeNav li.panel1 .button a').removeClass('hover');
	}
);
$('#banner .homeNav li.panel2').hover(
	function() {
		$('#banner .mouseover2').fadeIn();
		$('#banner .homeNav li.panel2 .button a').addClass('hover');
	},
	function() {
		$('#banner .mouseover2').fadeOut();
		$('#banner .homeNav li.panel2 .button a').removeClass('hover');
	}
);
$('#banner .homeNav li.panel3').hover(
	function() {
		$('#banner .mouseover3').fadeIn();
		$('#banner .homeNav li.panel3 .button a').addClass('hover');
	},
	function() {
		$('#banner .mouseover3').fadeOut();
		$('#banner .homeNav li.panel3 .button a').removeClass('hover');
	}
);
});