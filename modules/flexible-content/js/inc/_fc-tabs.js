/**
 * FC Tabs
 */

var Tabs = (function($) {

    function init() {
        $('ul.tabbed:not(.no-content) li, .tlc-pathways-bar li, .toggle-mobile').click(function() {
            var parent = $(this).closest('.tabbed-wrapper');
            parent.find('ul.tabbed li, .tlc-pathways-bar li, .toggle-mobile').removeClass('active');
            $(this).addClass('active');
            parent.find('.tab__content').hide();
            var activeTab = $(this).find('a').attr('data-id');
            parent.find('.' + activeTab).show();

            if (window.innerWidth <= 900) {
                var destination = $('.' + activeTab).offset().top;

                $("html:not(:animated),body:not(:animated)").animate({
                    scrollTop: destination - 170
                }, 800);
            }

            return false;
        });

        var fc_tabs = $('.fc_tabs, .fc_feature_tabs, .fc_pathways_tabs');

        fc_tabs.each(function(index, fc_tab) {
            $(fc_tab).find('.tab__content').hide();
            $(fc_tab).find('.tab__content:first').show();
            $(fc_tab).find('ul.tabbed:not(.no-content) li:first, .tlc-pathways-bar a:first, .toggle-mobile:first').addClass('active');
        });
    }

    return {
        init: init
    }

})(jQuery);
