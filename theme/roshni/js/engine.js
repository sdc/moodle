jQuery(document).ready(function() {

	var _minSlides, _minSlides2,
	    _width = $(window).width();

	if (_width <= 550) {
	    _minSlides = 1;
	    _minSlides2 = 1;
	} else if (_width > 550 && _width <= 760) {
	    _minSlides = 1;
	    _minSlides2 = 2;
	} else if (_width > 760 && _width <= 1200) {
	    _minSlides = 2;
	    _minSlides2 = 2;
	} else {	
	    _minSlides = 3;
	    _minSlides2 = 2;
	}

	$('.top-slider').bxSlider({
		slideWidth: 5000,
		minSlides: 1,
		maxSlides: 1,
		slideMargin: 0,
		moveSlides: 1,
		controls: false
		
	});  

	$('.av-courses-slider').bxSlider({
		slideWidth: 5000,
		minSlides: _minSlides,
		maxSlides: 4,
		slideMargin: 0,
		moveSlides: 1,
		infiniteLoop: false,
  		hideControlOnEnd: true,
		pager: false,
		adaptiveWidth: true
	});

	$('.feedback-slider').bxSlider({
		slideWidth: 5000,
		minSlides: _minSlides2,
		maxSlides: 2,
		slideMargin: 26,
		moveSlides: 2,
		infiniteLoop: false,
	});
	$('.feedback-slider-upcomingcourse').bxSlider({
		slideWidth: 5000,
		minSlides: 1,
		maxSlides: 1,
		slideMargin: 26,
		moveSlides: 1,
		infiniteLoop: false,
	});



	$('.tabs-nav a').click(function(){
		var _index = $(this).data('label'),
			_item = $('.tabs-container').find('[data-target="' + _index + '"]');
		$('.tabs-nav a').removeClass('active');
		$(this).addClass('active');
		$('.tabs-content-item').fadeOut(0);
		_item.removeClass('hidden');
		_item.fadeIn();
	});

	
	// BEGIN script for upload image text
	$('.upload-img-text input[type="file"]').change(function(){

		var _nameImg = $(this).val();
		if (_nameImg.lastIndexOf('\\')){
			var i = _nameImg.lastIndexOf('\\')+1;
		}
		else{
			var i = _nameImg.lastIndexOf('/')+1;
		}
		var filename = _nameImg.slice(i);      
		console.log(filename);
		$(this).parents('.upload-img-text').find('input[type="text"]').val(filename);
	});
  // END script for upload image text
	
	

	
	
});