jQuery(document).ready(function($) {


    if($('#autoplay-image').length > 0) {

      var countpart = $('.countpart').val();

      $("#autoplay-image").flexisel({

          visibleItems: countpart,

          animationSpeed: 3000,

          autoPlay: true,

          autoPlaySpeed: 3000,            

          pauseOnHover: true,

          enableResponsiveBreakpoints: false,

          responsiveBreakpoints: { 

              portrait: { 

                  changePoint:480,

                  visibleItems: 1

              }, 

              landscape: { 

                  changePoint:640,

                  visibleItems: 2

              },

              tablet: { 

                  changePoint:768,

                  visibleItems: 3
              }
          }
      });
    }


  

  

  

});

