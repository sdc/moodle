<script src="<?php echo $CFG->themewww .'/'. current_theme() ?>/js/jquery-latest.pack.js" type="text/javascript"></script>

<script type="text/javascript" charset="utf-8">
/* <![CDATA[ */
    var script = {
        corrections: function () {
            if (top.user) {
                top.document.getElementsByTagName('frameset')[0].rows = "117,30%,0,200";
            }
            
            // correct some Safari 2 (webkit 419.3) rtl rendering issues 
            if($.browser.version == '419.3') {
                if ($('body.dir-rtl').length) {
                    $('div.bb div,div.bt div').css('left','13px');
                }
            }
            
            // check for layouttabel and add haslayouttable class to body
            // remove nocoursepage class from body
            var layoutTable = $('#layout-table');
            
            if (layoutTable.length) {
                $('body').addClass('haslayouttable');
                $('body').removeClass('nocoursepage');
            }
        },
        
        info: function() {
            window.setTimeout(function(){$('#infowrapper').click();}, 4000);
            $('#infowrapper').toggle(function() {
                $('#infooverlay').animate({height: 'toggle'}, "fast");
                $(this).animate({opacity: 0.3}, "fast");
            }, function() {
                $('#infooverlay').animate({height: 'toggle'}, "fast");
                $(this).animate({opacity: 0.9}, "fast");
            });
        },
        
        init: function() {
            script.corrections();
            // script.info();
        }
    };
/* ]]> */
</script>