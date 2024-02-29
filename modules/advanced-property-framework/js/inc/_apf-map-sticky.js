/*
*   APF Sticky Map
*
*   @package Advanced Property Framework
*   @version 1.0
*/



jQuery(document).ready(function($) {

    // Check if div#map exists on page so it doesn't throw errors
    if($('.apf__results__map__wrap').length > 0) {

        function sticky_map() {

            var windowWidth = $(window).width();

            if (windowWidth > 960) {
                /* Sticky Map */
                var y = $(window).scrollTop() + 120; // the "+ value" should equal the margin-top value for nav.stick
                var sticky_top = $('.apf__stick__top');
                var sticky_btm = $('.apf__stick__btm');
                var item_height = $('.apf__results__map__wrap').outerHeight();

                // reize map
                google.maps.event.trigger(map, "resize");

                if (sticky_top.length) {
                    var top = sticky_top.offset().top;
                    var btm = sticky_btm.offset().top - item_height;

                    if (y >= top && y <= btm ) {
                        $('.apf__results__map').addClass('stick').removeClass('bottom').removeAttr('style');
                    } else {
                        if (y <= top) {
                            $('.apf__results__map').removeClass('stick');
                        }
                        if (y >= btm) {
                            $('.apf__results__map').removeClass('stick').addClass('bottom').removeAttr('style');;
                        }
                    }
                }

            } else {

                $('.apf__results__map__wrap').removeClass('stick').removeAttr('style');

            }

        }

        // sticky_map();
        // $(window).bind('resize', sticky_map);
        // $(window).bind('scroll', sticky_map);

    }

});
