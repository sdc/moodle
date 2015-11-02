<?php

global $DB, $CFG;
$ismenubar = $DB->get_record_sql('select config.value from {config_plugins} config where config.plugin="theme_roshni" and config.name="menus"');
if($ismenubar) {
  $ifmegamenu = json_decode($ismenubar->value);
}
else {
  $ifmegamenu = '';
}
?>


<link rel="stylesheet" href="<?php echo $CFG->wwwroot ?>/theme/<?php echo $CFG->theme;?>/style/megamenu.css">
<!-- <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAAjU0EJWnWPMv7oQ-jjS7dYxSPW5CJgpdgO_s4yyMovOaVh_KvvhSfpvagV18eOyDWu7VytS6Bi1CWxw"></script> -->
<script src="<?php echo $CFG->wwwroot;?>/theme/<?php echo $CFG->theme;?>/settings/mega/FitVids.js-master/jquery.fitvids.js"></script>
<script>
	var wwwroot = '<?php echo $CFG->wwwroot; ?>';
	var cfgtheme = '<?php echo $CFG->theme; ?>';
</script>

<script>
	jQuery(document).ready(function($) {
	$(".contactbtn").click(function(){
	var from = $("#from").val();
	var emailmessage = $("#emailmessage").val();
	var dataString = 'from='+ from + '&emailmessage='+ emailmessage;
	if(from==''||emailmessage=='')
	{
	alert("Please Fill All Fields");
	}
	else
	{
	  // AJAX Code To Submit Form.
	  $.ajax({
	    type: "POST",
	    url: wwwroot+"/theme/"+cfgtheme+"/layout/home/mail.php",
	    data: dataString,
	    cache: false,
	    success: function(result){
	      if(result == 1){
	      $('.showmailsent').append('sent');
	      setTimeout( "$('.showmailsent').hide();",3000 );
	      $('#from').val('');
	      $('#emailmessage').val('');
	      } else {
	      $('.showmailsent').append('not sent');
	      setTimeout( "$('.showmailsent').hide();",3000 );
	      }
	    }
	  });
	}
	return false;
	});
	  $(window).load(function () {
	  (function(a){(jQuery.browser=jQuery.browser||{}).mobile=/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))})(navigator.userAgent||navigator.vendor||window.opera);
	    var isiDevice = /ipad|iphone|ipod/i.test(navigator.userAgent.toLowerCase());	
	    var isAndroid = /android/i.test(navigator.userAgent.toLowerCase());
	    var isBlackBerry = /blackberry/i.test(navigator.userAgent.toLowerCase());
	    var isWindowsPhone = /windows phone/i.test(navigator.userAgent.toLowerCase());
	    if(jQuery.browser.mobile || isiDevice || isAndroid || isBlackBerry || isWindowsPhone) {
				$("li.sliding-wardrobes").each(function() {
					alert('hello');
					$(this).children('a').click(function (e) {
						if(!$(this).attr('href')) {
							e.preventDefault();
						}
						var wardrobes = $(this).parent();
						if($('.active-dropdowns').length > 0) {
							if($('.active-dropdowns').get(0) != wardrobes.find(".dropdowns").get(0)) {
								$(".active-dropdowns").slideUp("slow", function() { 
									$(this).removeClass('active-dropdowns');
									$(this).parent().removeClass('active-parent');
									wardrobes.addClass('active-parent');
									wardrobes.find(".dropdowns").slideDown("slow", function() {
										$(this).addClass('active-dropdowns');
									});
								});
							} else {
								$(".active-dropdowns").slideUp("slow", function() { 
									$(this).removeClass('active-dropdowns');
									$(this).parent().removeClass('active-parent');
								});
							}
						} else {
							wardrobes.addClass('active-parent');
							wardrobes.find(".dropdowns").slideDown("slow", function() {
								$(this).addClass('active-dropdowns');
							});
						}
					});
				});
	    } else {		
	    	
			$('#main-nav li > .dropdowns').parent().click(function(e){

				if(!$(this).next('a').attr('href')) {
				   e.preventDefault();
				}
				$(this).toggleClass("active-parent");
				$(this).children('.dropdowns').slideToggle(1000).toggleClass("active-dropdowns");
			});
		}
	  }); 
	  var map = {};
	  var geocoder = {};
	  function initialize(map_canvas, index) {
	    if (GBrowserIsCompatible()) {
	      map[index] = new GMap2($('.map_canvas').get(index));
	      map[index].setCenter(new GLatLng(37.4419, -122.1419), 1);
	      map[index].setUIToDefault();
	      geocoder[index] = new GClientGeocoder();
	    }
	  }
	  function showAddress(address, index) {
	    if (geocoder[index]) {
	      geocoder[index].getLatLng(
	        address,
	        function(point) {
	          if (!point) {
	            alert(address + " not found");
	          } else {
	            map[index].setCenter(point, 15);
	            var marker = new GMarker(point, {draggable: true});
	            map[index].addOverlay(marker);
	            GEvent.addListener(marker, "dragend", function() {
	              //marker.openInfoWindowHtml(document.createTextNode(address));
	            });
	            GEvent.addListener(marker, "click", function() {
	              marker.openInfoWindowHtml(document.createTextNode(address));
	            });
	            GEvent.trigger(marker, "click");
	          }
	        }
	      );
	    }
	  }
	  $(window).load(function () {
	    $('.map_canvas').each(function(index) {
	        initialize($(this), index); 
	        givenaddress = $(".map-id:eq(" + index + ")").val(); 
	        //console.log(givenaddress);
	        showAddress(givenaddress, index); 
	    }); 
	  });
	  $(window).load(function () {
	      $(".show-video").fitVids();
	  });
	});



</script>
<?php
	$checkenable = $DB->get_record_sql('select config.value from {config_plugins} config where config.plugin="theme_roshni" and config.name="formsection1"');
	$positionclass = '';
	if(!empty($checkenable)) {
	  $positionclass = ' megamenuposition';
	}
?>
<div class="main-menu">
	<div class="container">
		<!-- <a href="<?php echo $CFG->wwwroot;?>" class="logo">Home</a> -->
		<div class="navbar">
			<div class="navbar-inner">
				<div class="">
					<button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<div class="nav-collapse collapse">
						<ul class="nav megamenu" id = "main-nav">
							<?php
      						if(!empty($ifmegamenu)) {
        					foreach($ifmegamenu as $key => $val) { ?>
								<li class="sliding-wardrobes">
									<?php
									$addmenuclass = '';
									$textmenuclass = '';
									if(!empty($val->image)) {
										if($val->image_pos == 'left') {
											$addmenuclass = ' align-left';
											$textmenuclass = ' text-align-right';
										}
										if($val->image_pos == 'right') {
											$addmenuclass = ' align-right';
											$textmenuclass = ' text-align-left';
										}
										if($val->image_pos == 'bottom') {
											$addmenuclass = ' align-bottom';
											$textmenuclass = ' text-align-top';
										}
										if($val->image_pos == 'top') {
											$addmenuclass = ' align-top';
											$textmenuclass = ' text-align-bottom';
										}
										if($val->image_pos == 'center') {
											$addmenuclass = ' align-center';
											$textmenuclass = ' text-align-center';
										}
									}
									if(!empty($val->image) && ($val->image_pos == 'top')) {?>
										<a href="<?php if(empty($val->sections)) { echo $val->link; } else { echo '#'; } ?>">
											<?php if(!empty($val->image)) { ?>
													<div class="show-menu-icon<?php echo $addmenuclass; ?>">
														<img src="<?php echo $val->image; ?>" width="<?php echo (isset($val->width) && !empty($val->width) ? $val->width : 30); ?>px" height="<?php echo (isset($val->height) && !empty($val->height) ? $val->height : 30); ?>px">
													</div>
											<?php } ?>
											<?php if(!empty($val->title)) { ?>
											<span class="<?php echo $textmenuclass; ?>"><?php echo $val->title; ?></span><span class="arrow-nav"></span>											<?php } ?> 
										</a>
									<?php } else {?>
										<a href="<?php echo $val->link; ?>">
											<?php if(!empty($val->title)) { ?>
												<span class="<?php echo $textmenuclass; ?>"><?php echo $val->title; ?></span><span class="arrow-nav"></span>
											<?php } ?>
											<?php if(!empty($val->image)) { ?>
												<div class="show-menu-icon<?php echo $addmenuclass; ?>">
													<img src="<?php echo $val->image; ?>" width="<?php echo (isset($val->width) && !empty($val->width) ? $val->width : 30); ?>px" height="<?php echo (isset($val->height) && !empty($val->height) ? $val->height : 30); ?>px">
												</div>
											<?php } ?>
										</a>
									<?php } ?>
									<?php if(!empty($val->sections)) {
										$section = $val->sections;
									} else {
										$section = '';
									}
									if(!empty($section)) {?>
										<div class="dropdowns " style="display: none;">  
											<?php foreach($section as $sectionkey => $sectionval) {?>
												<ul class="ul-nav row-fluid">
													<li class="sliding-sections span12">
														<?php
															$addsecclass = '';
															$textsecclass = '';
															if(!empty($sectionval->image)) {
																if($sectionval->image_pos == 'left') {
																	$addsecclass = ' align-sec-img-left';
																	$textsecclass = ' align-sec-text-right';
																}
																if($sectionval->image_pos == 'right') {
																	$addsecclass = ' align-sec-img-right';
																	$textsecclass = ' align-sec-text-left';
																}
																if($sectionval->image_pos == 'bottom') {
																	$addsecclass = ' align-sec-img-bottom';
																	$textsecclass = ' align-sec-text-top';
																}
																if($sectionval->image_pos == 'top') {
																	$addsecclass = ' align-sec-img-top';
																	$textsecclass = ' align-sec-text-bottom';
																}
																if($sectionval->image_pos == 'center') {
																	$addsecclass = ' align-sec-img-center';
																	$textsecclass = ' align-sec-text-center';
																}
															}
															if(!empty($sectionval->image)) {
																if(!empty($sectionval->image) && ($sectionval->image_pos == 'top')){?>
																	<div class="section-image<?php echo $addsecclass; ?>">
																		<img src="<?php echo $sectionval->image; ?>" width="<?php echo (isset($sectionval->width) && !empty($sectionval->width) ? $sectionval->width : 100); ?>px" height="<?php echo (isset($sectionval->height) && !empty($sectionval->height) ? $sectionval->height : 100); ?>px">
																	</div>
																	<?php if(!empty($sectionval->title)) {?>
																		<h5 class="<?php echo $textsecclass; ?>"><a href="<?php echo $sectionval->link; ?>"><?php echo $sectionval->title; ?></a></h5>
																		<?php } 
																} else {
																	if(!empty($sectionval->title)) { ?>
																		<h5 class="<?php echo $textsecclass; ?>"><a href="<?php echo $sectionval->link; ?>"><?php echo $sectionval->title; ?></a></h5>
																	<?php } 
																	if(!empty($sectionval->image)) {?>
																		<div class="section-image<?php echo $addsecclass; ?>">
																			<img src="<?php echo $sectionval->image; ?>" width="<?php echo (isset($sectionval->width) && !empty($sectionval->width) ? $sectionval->width : 100); ?>px" height="<?php echo (isset($sectionval->height) && !empty($sectionval->height) ? $sectionval->height : 100); ?>px">
																		</div>
																	<?php }
																}
															} else {
																if(!empty($sectionval->video)) {
																	if($sectionval->video_pos == 'left') {
																		$addsecclass = ' align-sec-img-left';
																		$textsecclass = ' align-sec-text-right';
																	}
																	if($sectionval->video_pos == 'right') {
																		$addsecclass = ' align-sec-img-right';
																		$textsecclass = ' align-sec-text-left';
																	}
																	if($sectionval->video_pos == 'bottom') {
																		$addsecclass = ' align-sec-img-bottom';
																		$textsecclass = ' align-sec-text-top';
																	}
																	if($sectionval->video_pos == 'top') {
																		$addsecclass = ' align-sec-img-top';
																		$textsecclass = ' align-sec-text-bottom';
																	}
																	if($sectionval->video_pos == 'center') {
																		$addsecclass = ' align-sec-img-center';
																		$textsecclass = ' align-sec-text-center';
																	}
																}
																if(!empty($sectionval->video) && ($sectionval->video_pos == 'top')) {
																	if(!empty($sectionval->video)) {?>
																		<div class="section-video show-video<?php echo $addsecclass; ?>"><?php echo $sectionval->video; ?></div>
																	<?php } 
																	if(!empty($sectionval->title)) {?>
																		<h5 class="<?php echo $textsecclass; ?>"><a href="<?php echo $sectionval->link; ?>"><?php echo $sectionval->title; ?></a></h5>
																	<?php } 
																} else {
																	if(!empty($sectionval->title)) {?>
																		<h5 class="<?php echo $textsecclass; ?>"><a href="<?php echo $sectionval->link; ?>"><?php echo $sectionval->title; ?></a></h5>
																	<?php }
																	if(!empty($sectionval->video)) {?>
																		<div class="section-video show-video<?php echo $addsecclass; ?>"><?php echo $sectionval->video; ?></div>
																	<?php } 
																}
															}
															if(!empty($sectionval->columns)) {
																$columns = $sectionval->columns;
															} else {
																$columns = '';
															}
															if(!empty($columns)) {?>
																<div class="columns row-fluid">  
																	<?php foreach($columns as $columnskey => $columnsval) {
																		if (count($columns) == 1) {
																			$spanclassforcolumn = 'span12';
																		} else if (count($columns) == 2) {
																			$spanclassforcolumn = 'span6';
																		} else if (count($columns) == 3) {
																			$spanclassforcolumn = 'span4';
																		} else {
																			$spanclassforcolumn = 'span3';
																		} ?>
																	
																		<div class="<?php echo $spanclassforcolumn;?> slide-col">
																			<?php
																			$addcolclass = '';
																			$textcolclass = '';
																			if(!empty($columnsval->image)) {
																				if($columnsval->image_pos == 'left') {
																					$addcolclass = ' align-col-img-left';
																					$textcolclass = ' align-col-text-right';
																				}
																				if($columnsval->image_pos == 'right') {
																					$addcolclass = ' align-col-img-right';
																					$textcolclass = ' align-col-text-left';
																				}
																				if($columnsval->image_pos == 'bottom') {
																					$addcolclass = ' align-col-img-bottom';
																					$textcolclass = ' align-col-text-top';
																				}
																				if($columnsval->image_pos == 'top') {
																					$addcolclass = ' align-col-img-top';
																					$textcolclass = ' align-col-text-bottom';
																				}
																				if($columnsval->image_pos == 'center') {
																					$addcolclass = ' align-col-img-center';
																					$textcolclass = ' align-col-text-center';
																				}
																			}
																			if(!empty($columnsval->image)) {
																				if(!empty($columnsval->image) && ($columnsval->image_pos == 'top')) {
																					if(!empty($columnsval->image)) {?>
																					<div class="column-image<?php echo $addcolclass; ?>">
																						<img src="<?php echo $columnsval->image; ?>" width="<?php echo (isset($columnsval->width) && !empty($columnsval->width) ? $columnsval->width : 100); ?>px" height="<?php echo (isset($columnsval->height) && !empty($columnsval->height) ? $columnsval->height : 100); ?>px">
																					</div>
																					<?php } 
																					if(!empty($columnsval->title)) {?>
																						<a class="column-title<?php echo $textcolclass; ?>" href="<?php echo $columnsval->link; ?>"><?php echo $columnsval->title; ?></a>
																					<?php }
																				} else {
																					if(!empty($columnsval->title)) {?>
																						<a class="column-title<?php echo $textcolclass; ?>" href="<?php echo $columnsval->link; ?>"><?php echo $columnsval->title; ?></a>
																					<?php }
																					if(!empty($columnsval->image)) {?>
																						<div class="column-image<?php echo $addcolclass; ?>">
																							<img src="<?php echo $columnsval->image; ?>" width="<?php echo (isset($columnsval->width) && !empty($columnsval->width) ? $columnsval->width : 100); ?>px" height="<?php echo (isset($columnsval->height) && !empty($columnsval->height) ? $columnsval->height : 100); ?>px">
																						</div>	
																					<?php }
																				} 
																			} else {
																				if(!empty($columnsval->video)) {
																					if($columnsval->video_pos == 'left') {
																						$addcolclass = ' align-col-img-left';
																						$textcolclass = ' align-col-text-right';
																					}
																					if($columnsval->video_pos == 'right') {
																						$addcolclass = ' align-col-img-right';
																						$textcolclass = ' align-col-text-left';
																					}
																					if($columnsval->video_pos == 'bottom') {
																						$addcolclass = ' align-col-img-bottom';
																						$textcolclass = ' align-col-text-top';
																					}
																					if($columnsval->video_pos == 'top') {
																						$addcolclass = ' align-col-img-top';
																						$textcolclass = ' align-col-text-bottom';
																					}
																					if($columnsval->video_pos == 'center') {
																						$addcolclass = ' align-col-img-center';
																						$textcolclass = ' align-col-text-center';
																					}
																				}
																				if(!empty($columnsval->video) && ($columnsval->video_pos == 'top')) {?>
																					<div class="column-video show-video<?php echo $addcolclass;?>"><?php echo $columnsval->video; ?></div>
																					<?php if(!empty($columnsval->title)) {?>
																						<a class="column-title<?php echo $textcolclass; ?>" href="<?php echo $columnsval->link; ?>"><?php echo $columnsval->title; ?></a>
																					<?php }
																				} else {
																					if(!empty($columnsval->title)) {?>
																						<a class="column-title<?php echo $textcolclass; ?>" href="<?php echo $columnsval->link; ?>"><?php echo $columnsval->title; ?></a>
																					<?php } ?>
																					<div class="column-video show-video<?php echo $addcolclass; ?>"><?php echo $columnsval->video; ?></div>
																				<?php
																				}
																			}
																			if(!empty($columnsval->elements)) {
																				$elements = $columnsval->elements;
																			} else {
																				$elements = '';
																			}
																			if(!empty($elements)) {?>
																				<div class="col-elements">
																					<?php foreach($elements as $elementskey => $elementsval) {?>
																						<div class="choose-element">
																							<?php if($elementsval->type == "form"){
																									if($elementsval->forms == "login_form"){
																										if (!isloggedin() or isguestuser()) {?>
																											<div class="login-form">
																												<h4>Login form</h4> 
																												<form class="navbar-form pull-left menu-form" method="post" action="<?php echo $CFG->wwwroot ?>/login/index.php?authldap_skipntlmsso=1">																									<label style="width: 62px; display: inline-block;">Username: </label><input  type="text" name="username" placeholder="<?php echo get_string('username');?>">
																													<label style="width: 62px; display: inline-block;">Password: </label><input  type="password" name="password" placeholder="<?php echo get_string('password');?>">
																													<button class="btn menubtn" type="submit"><?php echo get_string('login'); ?></button>
																												</form>
																											</div>
																											<?php
																										} else {?>
																											<div>
																												<span class="txt">HI, <?php echo $USER->firstname; ?></span>
																												<span class="txt">You are already logged in.</span>
																											</div>
																										<?php }
																									}
																									if($elementsval->forms == "contactus_form"){?>
																										<div class="contactus-form">
																											<h4>Contactus form</h4> 
																											<label style="width: 65px; display: inline-block;">From: </label><input class="message" type="text" id="from" name="frommail" placeholder="from">
																											<label style="width: 65px; display: inline-block;">Message: </label><input class="message" type="text" id="emailmessage" name="message" placeholder="message">
																											<button class="btn contactbtn" type="submit">send</button>

																											<div class="showmailsent"></div>
																										</div>
																									<?php
																									}
																								}
																							if($elementsval->type == "text"){?>
																								<div class="col-element-text">
																									<?php if(!empty($elementsval->image)) {
																										if($elementsval->image_pos == 'left') {
																											$addclass = ' align-left';
																											$textclass = ' text-align-right';
																										}
																										if($elementsval->image_pos == 'right') {
																											$addclass = ' align-right';
																											$textclass = ' text-align-left';
																										}
																										if($elementsval->image_pos == 'bottom') {
																											$addclass = ' align-bottom';
																											$textclass = ' text-align-top';
																										}
																										if($elementsval->image_pos == 'top') {
																											$addclass = ' align-top';
																											$textclass = ' text-align-bottom';
																										}
																										if($elementsval->image_pos == 'center') {
																											$addclass = ' align-center';
																											$textclass = ' text-align-center';
																										}
																									}
																									if(!empty($elementsval->image) && ($elementsval->image_pos == 'top')) {
																										if(!empty($elementsval->image)) {?>
																											<div class="element-image<?php echo $addclass; ?>">
																												<img src="<?php echo $elementsval->image; ?>" width="<?php echo (isset($elementsval->width) && !empty($elementsval->width) ? $elementsval->width : 100); ?>px" height="<?php echo (isset($elementsval->height) && !empty($elementsval->height) ? $elementsval->height : 100); ?>px">
																											</div>
																										<?php }
																										if(!empty($elementsval->title)) {?>
																											<p class="<?php echo $textclass; ?>"><?php echo $elementsval->title; ?></p>
																										<?php } 
																									} else {
																										if(!empty($elementsval->title)) {?>
																											<p class="<?php echo $textclass; ?>"><?php echo $elementsval->title; ?></p>
																										<?php }
																										if(!empty($elementsval->image)) {?>
																											<div class="element-image<?php echo $addclass; ?>">
																												<img src="<?php echo $elementsval->image; ?>" width="<?php echo (isset($elementsval->width) && !empty($elementsval->width) ? $elementsval->width : 100); ?>px" height="<?php echo (isset($elementsval->height) && !empty($elementsval->height) ? $elementsval->height : 100); ?>px">
																											</div>
																										<?php } 
																									} ?>
																								</div>
																							<?php }
																							if($elementsval->type == "map"){
																								if(!empty($elementsval->address)) {?>
																									<div class="map_canvas span12" style="max-width:100%; height: 205px;"></div>
																									<input type="hidden" name="show-address" value="<?php echo $elementsval->address; ?>" class="map-id">
																								<?php }
																							}
																							if($elementsval->type == "link"){
																								if(!empty($elementsval->link)){?>
																									<div class="show-link">
																										<a href="<?php echo $elementsval->link; ?>"><?php echo $elementsval->title; ?></a>
																									</div>
																								<?php } 
																							} ?>
																						</div>
																					<?php } ?>
																				</div>
																			<?php } ?>
																		</div>
																	<?php } ?>
																</div>
															<?php } 
														?>
													</li>
												</ul>
											<?php } //end for ?>
										</div> <!-- end dropdown --> 
									<?php } // end if ?>
								</li>
						<?php } // end if 
						} // end for
						?>					
						</ul>
					</div><!--/.nav-collapse -->
				</div>
			</div><!-- END of .navbar-inner -->
		</div><!-- END of .navbar -->
	</div><!-- END of .container -->
</div><!-- END of main-menu -->
