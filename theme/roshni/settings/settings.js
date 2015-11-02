$(document).ready(function($) {
    
   
    
  $('.incept_settings table tr td textarea').focus(function() { $(this).css('height', '40px'); });
  $('.incept_settings table tr td textarea').focusout(function() { $(this).css('height', '20px'); });  
  
  
  tinymce.init({
     selector: ".textarea1",
     plugins: [
         "advlist autolink lists link image charmap print preview anchor",
         "searchreplace visualblocks code fullscreen",
         "insertdatetime media table contextmenu paste"
     ],
     toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | source code"
  });
  
  
  
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

 