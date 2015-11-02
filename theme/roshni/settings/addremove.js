$(document).ready(function() {
	$('.b-s-class').hide();
	layoutprop();
	  $('input[name=playout]').click(function() {
	    layoutprop();    
	  });
  
	  function layoutprop() {
	    $('.plimage1').hide();
	    $('.plcolor1').hide();
	    $('.b-s-class').hide();
	    if($('input[name=playout]:checked').attr("class") == 'pagelayoutcolor') $('.plcolor1').show();
	    if($('input[name=playout]:checked').attr("class") == 'pagelayoutimg') {
	    	$('.plimage1').show();
	    	$('.b-s-class').show();
	    }
	  } 
  
	  avcstripchange();
	  $('input[name=cstrip]').click(function() {
	    avcstripchange();    
	  });
  
	  function avcstripchange() {
	    $('.plimage').hide();
	    $('.plcolor').hide();
	    if($('input[name=cstrip]:checked').attr("class") == 'avcstripcolor') $('.plcolor').show();
	    if($('input[name=cstrip]:checked').attr("class") == 'avcstripimg') $('.plimage').show();
	  } 
	$(".slider_remove_field").hide();
	$(".slider_remove_field_show").show();
	var wrapper9         = $(".slider_wrap"); 
	var add_button9      = $(".slider_add_field_button");
	
	if($('.jeet_abc').attr('data-slide-label')) {
		var count = $('.jeet_abc').attr('data-slide-label');
		for(tinymcecount = 1;tinymcecount <= count; tinymcecount = tinymcecount + 1){
			tinymce.init({
						selector:'#textarea'+tinymcecount,
						plugins: [
	                                "advlist autolink lists link image charmap print preview anchor",
	                                "searchreplace visualblocks code fullscreen",
	                                "insertdatetime media table contextmenu paste"
		                         ],
		                toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
                    });
		}
	} else {
		var count = 1;
		tinymce.init({
			selector:'#textarea'+count,
			plugins: [
                        "advlist autolink lists link image charmap print preview anchor",
                        "searchreplace visualblocks code fullscreen",
                        "insertdatetime media table contextmenu paste"
                     ],
            toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
		});
	}
	$(add_button9).on( "click",function(e) { 
		count++;
		$(".active").removeClass("active");
		$(".slider_tab").clone().insertBefore(add_button9).removeClass( "slider_tab").addClass('active').attr("data-slide-label", count).attr("id", "fisrt_tab_"+count).text("Slide "+count);
		
		
		$(".slider_body").clone().appendTo('.slider_body_content').removeClass("slider_body fisrt_tab_1").addClass("fisrt_tab_"+count).attr("data-slide-number", count).find('input:text').val('');
		$(".fisrt_tab_"+count+" textarea").attr('id','textarea'+count);
		$(".fisrt_tab_"+count+" .html-editor div").remove();
		$(".slider-settings-item").css('display','none');
		$(".fisrt_tab_"+count).css('display','block');
		$('#textarea'+count).css('display','block');
		tinymce.init({
			selector:'#textarea'+count,
			plugins: [
                        "advlist autolink lists link image charmap print preview anchor",
                        "searchreplace visualblocks code fullscreen",
                        "insertdatetime media table contextmenu paste"
                     ],
            toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
		});			
	});
	
	$(wrapper9).on("click",".slider_remove_field", function(e) {
		//count = count-1;
		e.preventDefault();
		if($(this).parent().is('[class*=fisrt_tab_]')) {
			$(this).parent().remove();
			$("#fisrt_tab_"+$(this).parent().attr("data-slide-number")).remove();
			//$(".fisrt_tab_"+count).show();
			//$("#fisrt_body_"+count).addClass('active');
			$(".fisrt_tab_1").show();
			$("#fisrt_tab_1").addClass('active');
		}
	});
	
	//$(".slider-settings-body").hide();
	//$(".slider_body_content").show();
	
	$(".slider-settings-item").hide();
	$(".slider_body").show();
	
	$(wrapper9).on("click",".tab_toggle", function(e) {
			e.preventDefault();
			$('.tab_toggle').removeClass('active');
			$("#"+$(this).attr("id")).addClass('active');
			$('.slider-settings-item').hide();
			$(".fisrt_tab_"+$(this).attr("data-slide-label")).show();
			//$(".slider-settings-body").hide();
			//$(".samsung_"+$(this).attr("data-slide-label")).show();
	});	
	
	
	/* header menu Settings add / remove */
	$('.hm_remove_field').css("display","none");
	$('.hm_remove_field_show').css("display","block");
	var wrapper10         = $(".hm_wrap"); 
	var add_button10      = $(".hm_add_field_button");
	
	$(add_button10).click(function(e) { 
		e.preventDefault();
		$(".hm_content").clone().appendTo(wrapper10).removeClass( "hm_content").addClass('remove_hm_details').find('input:text').val('');
		$('.remove_hm_details').find('a.hm_remove_field').css("display","block");
	});
	
	$(wrapper10).on("click",".hm_remove_field", function(e) { 
		e.preventDefault();
		if($(this).parent().parent().hasClass('remove_hm_details')) {
			$(this).parent().parent().remove();
		}
	});
		
	
	
	/* available courses Settings add / remove */
	$('.ac_remove_field').css("display","none");
	$('.ac_remove_field_show').css("display","block");
	var wrapper8         = $(".ac_wrap"); 
	var add_button8      = $(".ac_add_field_button");
	
	$(add_button8).click(function(e) { 
		e.preventDefault();
		$(".ac_content").clone().appendTo(wrapper8).removeClass( "ac_content").addClass('remove_ac_details').find('input:text').val('');
		$('.remove_ac_details').find('a.ac_remove_field').css("display","block");
	});
	
	$(wrapper8).on("click",".ac_remove_field", function(e) { 
		e.preventDefault();
		if($(this).parent().parent().hasClass('remove_ac_details')) {
			$(this).parent().parent().remove();
		}
	});
	
	
	
	/* about site Settings add / remove */
	$('.as_remove_field').css("display","none");
	$('.as_remove_field_show').css("display","block");
	var wrapper7         = $(".as_wrap"); 
	var add_button7      = $(".as_add_field_button");
	$('.iconpicker i').click(function(e) {
    		e.preventDefault();
    
		    var iconWithPrefix = $(this).attr('class');
		    var fontName = $(this).attr('data-name');
    
		    if($(this).hasClass('active')) {
		      $(this).parent().find('.active').removeClass('active');
		      $(this).parent().parent().find('input').attr('value', '');
		      //$(this).parent().parent().find('input').val('');
		    } else {
		      $(this).parent().find('.active').removeClass('active');
		      $(this).addClass('active');
		      
		      $(this).parent().parent().find('input').attr('value', fontName);
		    }
  	});
	$(add_button7).click(function(e) { 
		e.preventDefault();
		$(".as_content").clone().appendTo(wrapper7).removeClass( "as_content").addClass('remove_as_details').find('input:text').val('');
		$('.remove_as_details').find('a.as_remove_field').css("display","block");
		/* activate iconpicker*/
		$('.iconpicker i').click(function(e) {
    		e.preventDefault();
    
		    var iconWithPrefix = $(this).attr('class');
		    var fontName = $(this).attr('data-name');
    
		    if($(this).hasClass('active')) {
		      $(this).parent().find('.active').removeClass('active');
		      $(this).parent().parent().find('input').attr('value', '');
		      //$(this).parent().parent().find('input').val('');
		    } else {
		      $(this).parent().find('.active').removeClass('active');
		      $(this).addClass('active');
		      
		      $(this).parent().parent().find('input').attr('value', fontName);
		    }
  		}); 
	});
	
	$(wrapper7).on("click",".as_remove_field", function(e) { 
		e.preventDefault();
		if($(this).parent().parent().hasClass('remove_as_details')) {
			$(this).parent().parent().remove();
		}
	});
	
	
	/* site details add / remove */
	if($('.sdcla').attr('sd-data-element')) {
		var count = $('.sdcla').attr('sd-data-element');
		
		for(tinymcecount = 1;tinymcecount <= count; tinymcecount = tinymcecount + 1){
			
			tinymce.init({
						selector:'#sdtextarea'+tinymcecount,
						plugins: [
	                                "advlist autolink lists link image charmap print preview anchor",
	                                "searchreplace visualblocks code fullscreen",
	                                "insertdatetime media table contextmenu paste"
		                         ],
		                toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
                    });
		}
	} else {
		var count = 1;
		tinymce.init({
			selector:'#sdtextarea'+count,
			plugins: [
                        "advlist autolink lists link image charmap print preview anchor",
                        "searchreplace visualblocks code fullscreen",
                        "insertdatetime media table contextmenu paste"
                     ],
            toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
		});
	}
	$('.site_details_remove_field').css("display","none");
	$('.site_details_remove_field_show').css("display","block");
	var wrapper         = $(".site_details_wrap"); 
	var add_button      = $(".site_details_add_field_button");
	
	$(add_button).click(function(e) { 
		count++;
		e.preventDefault();
		$(".site_details_content").clone().appendTo(wrapper).removeClass( "site_details_content sdcla sd_class_1").addClass('remove_site_details sdcla sd_class_'+count).find('input:text').val('');
		$('.remove_site_details').find('a.site_details_remove_field').css("display","block");
		$(".sd_class_"+count+" textarea").attr('id','sdtextarea'+count);
		$(".sd_class_"+count+" .html-editor div").remove();
		$(".sd_class_"+count).css('display','block');
		$('#sdtextarea'+count).css('display','block');
		tinymce.init({
			selector:'#sdtextarea'+count,
			plugins: [
                        "advlist autolink lists link image charmap print preview anchor",
                        "searchreplace visualblocks code fullscreen",
                        "insertdatetime media table contextmenu paste"
                     ],
            toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
		});
	});
	
	$(wrapper).on("click",".site_details_remove_field", function(e) { 
		e.preventDefault();
		if($(this).parent().parent().hasClass('remove_site_details')) {
			$(this).parent().parent().remove();
		}
	});
	
	/* Testimonials Settings add / remove */
	/*if($('.tdcla').attr('td-data-element')) {
		var tdcount = $('.tdcla').attr('td-data-element');
		for(tinymcecount = 1;tinymcecount <= tdcount; tinymcecount = tinymcecount + 1){
			tinymce.init({
						selector:'#tdtextarea'+tinymcecount,
						plugins: [
	                                "advlist autolink lists link image charmap print preview anchor",
	                                "searchreplace visualblocks code fullscreen",
	                                "insertdatetime media table contextmenu paste"
		                         ],
		                toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
                    });
		}
	} else {
		var tdcount = 1;
		tinymce.init({
			selector:'#tdtextarea'+tdcount,
			plugins: [
                        "advlist autolink lists link image charmap print preview anchor",
                        "searchreplace visualblocks code fullscreen",
                        "insertdatetime media table contextmenu paste"
                     ],
            toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
		});
	}
	$('.testimonial_remove_field').css("display","none");
	$('.testimonial_remove_field_show').css("display","block");
	var wrapper1         = $(".testimonial_wrap"); 
	var add_button1      = $(".testimonial_add_field_button");
	
	$(add_button1).click(function(e) { 
		//var count = $('.tdcla').attr('td-data-element');
		tdcount++;
		e.preventDefault();
		$(".testimonial_content").clone().appendTo(wrapper1).removeClass( "testimonial_content td_class_1").addClass('remove_testimonial_details td_class_'+tdcount).find('input:text').val('');
		$('.remove_testimonial_details').find('a.testimonial_remove_field').css("display","block");
		$(".td_class_"+tdcount+" textarea").attr('id','tdtextarea'+tdcount);
		$(".td_class_"+tdcount+" .html-editor div").remove();
		$(".td_class_"+tdcount).css('display','block');
		$('#tdtextarea'+tdcount).css('display','block');
		tinymce.init({
			selector:'#tdtextarea'+tdcount,
			plugins: [
                        "advlist autolink lists link image charmap print preview anchor",
                        "searchreplace visualblocks code fullscreen",
                        "insertdatetime media table contextmenu paste"
                     ],
            toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
		});
	});
	
	$(wrapper1).on("click",".testimonial_remove_field", function(e) { 
		e.preventDefault();
		if($(this).parent().parent().hasClass('remove_testimonial_details')) {
			$(this).parent().parent().remove();
		}
	});*/
	/* Testimonials Settings add / remove */
	if($('.tdcla').attr('td-data-element')) {
		var tdcount = $('.tdcla').attr('td-data-element');
		for(tinymcecount = 1;tinymcecount <= tdcount; tinymcecount = tinymcecount + 1){
			//alert(tinymcecount);
			tinymce.init({
						selector:'#tdtextarea'+tinymcecount,
						plugins: [
	                                "advlist autolink lists link image charmap print preview anchor",
	                                "searchreplace visualblocks code fullscreen",
	                                "insertdatetime media table contextmenu paste"
		                         ],
		                toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
                    });
		}
	} else {
		var tdcount = 1;
		tinymce.init({
			selector:'#tdtextarea'+tdcount,
			plugins: [
                        "advlist autolink lists link image charmap print preview anchor",
                        "searchreplace visualblocks code fullscreen",
                        "insertdatetime media table contextmenu paste"
                     ],
            toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
		});
	}
	$('.testimonial_remove_field').css("display","none");
	$('.testimonial_remove_field_show').css("display","block");
	var wrapper1         = $(".testimonial_wrap"); 
	var add_button1      = $(".testimonial_add_field_button");
	
	$(add_button1).click(function(e) { 
		//var count = $('.tdcla').attr('td-data-element');
		tdcount++;
		e.preventDefault();
		$(".testimonial_content").clone().appendTo(wrapper1).removeClass( "testimonial_content td_class_1").addClass('remove_testimonial_details td_class_'+tdcount).find('input:text').val('');
		$('.remove_testimonial_details').find('a.testimonial_remove_field').css("display","block");
		$(".td_class_"+tdcount+" textarea").attr('id','tdtextarea'+tdcount);
		$(".td_class_"+tdcount+" .html-editor div").remove();
		$(".td_class_"+tdcount).css('display','block');
		$('#tdtextarea'+tdcount).css('display','block');
		tinymce.init({
			selector:'#tdtextarea'+tdcount,
			plugins: [
                        "advlist autolink lists link image charmap print preview anchor",
                        "searchreplace visualblocks code fullscreen",
                        "insertdatetime media table contextmenu paste"
                     ],
            toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
		});
	});
	
	$(wrapper1).on("click",".testimonial_remove_field", function(e) { 
		e.preventDefault();
		if($(this).parent().parent().hasClass('remove_testimonial_details')) {
			$(this).parent().parent().remove();
		}
	});
	
	/* Course Categories Settings add / remove */
	$('.cc_remove_field').css("display","none");
	$('.cc_remove_field_show').css("display","block");
	var wrapper2         = $(".cc_wrap"); 
	var add_button2      = $(".cc_add_field_button");
	
	$(add_button2).click(function(e) { 
		e.preventDefault();
		$(".cc_content").clone().appendTo(wrapper2).removeClass( "cc_content").addClass('remove_cc_details').find('input:text').val('');
		$('.remove_cc_details').find('a.cc_remove_field').css("display","block");
	});
	
	$(wrapper2).on("click",".cc_remove_field", function(e) { 
		e.preventDefault();
		if($(this).parent().parent().hasClass('remove_cc_details')) {
			$(this).parent().parent().remove();
		}
	});
	
	/* Up Coming Course Settings add / remove */
	if($('.uccla').attr('uc-data-element')) {
		var count = $('.uccla').attr('uc-data-element');
		
		for(tinymcecount = 1;tinymcecount <= count; tinymcecount = tinymcecount + 1){
			
			tinymce.init({
						selector:'#uctextarea'+tinymcecount,
						plugins: [
	                                "advlist autolink lists link image charmap print preview anchor",
	                                "searchreplace visualblocks code fullscreen",
	                                "insertdatetime media table contextmenu paste"
		                         ],
		                toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
                    });
		}
	} else {
		var count = 1;
		tinymce.init({
			selector:'#uctextarea'+count,
			plugins: [
                        "advlist autolink lists link image charmap print preview anchor",
                        "searchreplace visualblocks code fullscreen",
                        "insertdatetime media table contextmenu paste"
                     ],
            toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
		});
	}
	$('.uc_remove_field').css("display","none");
	$('.uc_remove_field_show').css("display","block");
	var wrapper3         = $(".uc_wrap"); 
	var add_button3      = $(".uc_add_field_button");
	
	$(add_button3).click(function(e) { 
		count++;
		e.preventDefault();
		$(".uc_content").clone().appendTo(wrapper3).removeClass( "uc_content uccla uc_class_1").addClass('remove_uc_details uccla uc_class_'+count).find('input:text').val('');
		$('.remove_uc_details').find('a.uc_remove_field').css("display","block");
		$(".uc_class_"+count+" textarea").attr('id','uctextarea'+count);
		$(".uc_class_"+count+" .html-editor div").remove();
		$(".uc_class_"+count).css('display','block');
		$('#uctextarea'+count).css('display','block');
		tinymce.init({
			selector:'#uctextarea'+count,
			plugins: [
                        "advlist autolink lists link image charmap print preview anchor",
                        "searchreplace visualblocks code fullscreen",
                        "insertdatetime media table contextmenu paste"
                     ],
            toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
		});
	});
		
	$(wrapper3).on("click",".uc_remove_field", function(e) { 
		e.preventDefault();
		if($(this).parent().parent().hasClass('remove_uc_details')) {
			$(this).parent().parent().remove();
		}
	});
	
	/* Patner Settings add / remove */
	$('.partner_remove_field').css("display","none");
	$('.partner_remove_field_show').css("display","block");
	var wrapper4         = $(".partner_wrap"); 
	var add_button4      = $(".partner_add_field_button");
	
	$(add_button4).click(function(e) { 
		e.preventDefault();
		$(".partner_content").clone().appendTo(wrapper4).removeClass( "partner_content").addClass('remove_partner_details').find('input:text').val('');
		$('.remove_partner_details').find('a.partner_remove_field').css("display","block");
	});
	
	$(wrapper4).on("click",".partner_remove_field", function(e) { 
		e.preventDefault();
		if($(this).parent().parent().hasClass('remove_partner_details')) {
			$(this).parent().parent().remove();
		}
	});
	
	/* Contact Settings add / remove */
	$('.contact_remove_field').css("display","none");
	$('.contact_remove_field_show').css("display","block");
	var wrapper5         = $(".contact_wrap"); 
	var add_button5      = $(".contact_add_field_button");
	
	$(add_button5).click(function(e) { 
		e.preventDefault();
		$(".contact_content").clone().appendTo(wrapper5).removeClass( "contact_content").addClass('remove_contact_details').find('input:text').val('');
		$('.remove_contact_details').find('a.contact_remove_field').css("display","block");
		/* activate iconpicker*/
		$('.iconpicker i').click(function(e) {
    		e.preventDefault();
    
		    var iconWithPrefix = $(this).attr('class');
		    var fontName = $(this).attr('data-name');
    
		    if($(this).hasClass('active')) {
		      $(this).parent().find('.active').removeClass('active');
		      $(this).parent().parent().find('input').attr('value', '');
		      //$(this).parent().parent().find('input').val('');
		    } else {
		      $(this).parent().find('.active').removeClass('active');
		      $(this).addClass('active');
		      
		      $(this).parent().parent().find('input').attr('value', fontName);
		    }
  		});
	});
	
	$(wrapper5).on("click",".contact_remove_field", function(e) { 
		e.preventDefault();
		if($(this).parent().parent().hasClass('remove_contact_details')) {
			$(this).parent().parent().remove();
		}
	});
	if($('.dynamiccla').attr('data-element')) {
		var count = $('.dynamiccla').attr('data-element');
		for(tinymcecount = 1;tinymcecount <= count; tinymcecount = tinymcecount + 1){
			tinymce.init({
						selector:'#dtextarea'+tinymcecount,
						plugins: [
	                                "advlist autolink lists link image charmap print preview anchor",
	                                "searchreplace visualblocks code fullscreen",
	                                "insertdatetime media table contextmenu paste"
		                         ],
		                toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
                    });
		}
	} else {
		var count = 1;
		tinymce.init({
			selector:'#dtextarea'+count,
			plugins: [
                        "advlist autolink lists link image charmap print preview anchor",
                        "searchreplace visualblocks code fullscreen",
                        "insertdatetime media table contextmenu paste"
                     ],
            toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
		});
	}
	
	/* header menu page Settings add / remove */
	$('.header_menu_remove_field').css("display","none");
	$('.header_menu_remove_field_show').css("display","block");
	var wrapper6         = $(".header_menu_wrap"); 
	var add_button6      = $(".header_menu_add_field_button");
	$(add_button6).click(function(e) { 
		count++;
		
		e.preventDefault();
		$(".header_menu_content").clone().appendTo(wrapper6).removeClass( "header_menu_content dynamiccla dynamic_class_1").addClass('remove_header_menu_details dynamiccla dynamic_class_'+count).find('input:text').val('');
		$('.remove_header_menu_details').find('a.header_menu_remove_field').css("display","block");
		$(".dynamic_class_"+count+" textarea").attr('id','dtextarea'+count);
		$(".dynamic_class_"+count+" .html-editor div").remove();
		$(".dynamic_class_"+count).css('display','block');
		$('#dtextarea'+count).css('display','block');
		tinymce.init({
			selector:'#dtextarea'+count,
			plugins: [
                        "advlist autolink lists link image charmap print preview anchor",
                        "searchreplace visualblocks code fullscreen",
                        "insertdatetime media table contextmenu paste"
                     ],
            toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
		});
	});

	$(wrapper6).on("click",".header_menu_remove_field", function(e) { 
		e.preventDefault();
		if($(this).parent().parent().hasClass('remove_header_menu_details')) {
			$(this).parent().parent().remove();
		}
	});
	
	$('#adminsettings .settingsform textarea').css('height', '20px');
	$('#adminsettings .settingsform textarea').css('width', '128px');
	
	$('#adminsettings .settingsform textarea').focus(function() { $(this).css('height', '28px'); });
	$('#adminsettings .settingsform textarea').focus(function() { $(this).css('width', '128px'); });
  $('#adminsettings .settingsform textarea').focusout(function() { $(this).css('height', '20px'); }); 
  $('#adminsettings .settingsform textarea').focus(function() { $(this).css('width', '128px'); });
	
	
  
	
  
  
  
  /******************** For front page sections dropdown ***********************/
  var preval;
  $("select.unique-value").on('focus', function () {
   preval = $(this).val();
  }).change(function() {
   var nextval = $(this).val();
   if(nextval != 'none') {
     $("select.unique-value option[value='"+nextval+"']").css('display', 'none');
   }
   $("select.unique-value option[value='"+preval+"']").css('display', 'block');
  });

  
  megamenuProp();
  $('input[name=navmenubar]').click(function() {
    megamenuProp();    
  });
  
  function megamenuProp() {
    $('#show-link').hide();
    if($('input[name=navmenubar]:checked').val() == 'megamenu') $('#show-link').show();
  } 
  
	
});

/*
9ta repeat hobe - done
r jodi paari then jquery tab korbo nahole ota uriye div repeat kore debo - done
*/
