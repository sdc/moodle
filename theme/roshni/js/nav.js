jQuery(document).ready(function() {
	$(".btn-navbar").on("click",function() {
		$('.nav-collapse').toggleClass('in').removeAttr('style');
	});
	$( window ).load(function() {
  		var outerHeight = $('#page-content').outerHeight();
		$('#block-region-side-post').css('min-height', outerHeight+'px');
		$('#block-region-side-pre').css('min-height', outerHeight+'px');
	});
});