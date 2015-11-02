
(function($) {
$(document).ready(function() {
	resizePanels();
});

$(window).resize(function() {
	resizePanels();
});

function resizePanels() {
	var first_panel_height = $(window).height() ;
	$('.slider-in-laptop').css({'height': first_panel_height + 'px'});
	$('.slider-in-laptop img').css({'height': first_panel_height + 'px'});
	
	var windowWidth = $(window).width();
	$('.slider-in-laptop').css({'width': windowWidth, 'height': first_panel_height});
	
	firstImageSize = getHomeImageSize(windowWidth, first_panel_height);
	
	var contentWidth = $('.slider-in-laptop .content-wrap').width();
	var contentHeight = $('.slider-in-laptop .content-wrap').height();
	
	$('.slider-in-laptop .content-wrap').css({'marginLeft': -(contentWidth / 2), 'marginTop': -(contentHeight / 2)});
}

function getHomeImageSize(windowWidth, windowHeight) {
	var imageWidth = 1200;
	var imageHeight = 702;
	var widthMultipler = windowWidth / imageWidth;

	if((imageHeight * widthMultipler) > windowHeight){
		//Width is the small one!
		yOffset = ((imageHeight * widthMultipler) - windowHeight) / 2;
		return new Array(windowWidth, (imageHeight * widthMultipler), 0, yOffset);
	} else {
		//Height is the small one!
		var heightMultipler = windowHeight / imageHeight;
		xOffset = ((imageWidth * heightMultipler) - windowWidth) / 2;
		return new Array((imageWidth * heightMultipler), windowHeight, xOffset , 0);
	}
}

})(jQuery); // Fully reference jQuery after this point.

(function( w ){
	// Enable strict mode
	"use strict";

	w.picturefill = function() {
		$(w).trigger("picturefill_complete");
	};

	// Run on resize and domready (w.load as a fallback)
	if( w.addEventListener ){
		w.addEventListener( "resize", w.picturefill, false );
		w.addEventListener( "DOMContentLoaded", function(){
			w.picturefill();
			// Run once only
			w.removeEventListener( "load", w.picturefill, false );
		}, false );
		w.addEventListener( "load", w.picturefill, false );
	}
	else if( w.attachEvent ){
		w.attachEvent( "onload", w.picturefill );
	}

}( this ));

jQuery(window).on("picturefill_complete", function($){
	jQuery('.slider-in-laptop').royalSlider({
		arrowsNav: false,
		loop: true,
		keyboardNavEnabled: true,
		controlsInside: false,
		imageScaleMode: 'fill',
		arrowsNavAutoHide: false,
		autoScaleSlider: false,
		controlNavigation: 'none',
		thumbsFitInViewport: false,
		autoHeight: false,
		navigateByClick: true,
		startSlideId: 0,
		addActiveClass: true,
		transitionSpeed: 1000,
		slidesSpacing: 0,
		autoPlay: {
				enabled: true,
				delay: 4000,
				pauseOnHover: false
		},
		transitionType:'move',
		globalCaption: false,
		deeplinking: {
			enabled: true,
			change: false
		}
	});
});

