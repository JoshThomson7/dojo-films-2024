/**
 * Accordion
 */

var Accordion = (function($) {

	function init()	{

		// get url hash
		var hash = window.location.hash;
		if(hash && hash.indexOf('#fc-accordion') > -1) {
			var accordionEl = $('#' + hash.replace('#', ''));
			accordionEl.addClass('active');
			accordionEl.find('h3.toggle span').toggleClass( 'fa-chevron-down fa-chevron-up' );
		}

		$('h3.toggle').click(function() {

			$('.accordion__wrap').removeClass('inactive');

			var parent = $(this).parent();

			if(parent.hasClass('active')) {
				// reset current
				$(this).removeClass('active');
			} else {
				// reset all
				$('.accordion__wrap').removeClass('active');
			}

			parent.toggleClass('active');
			$(this).find('span').toggleClass( 'fa-chevron-down fa-chevron-up' );

			if($('.accordion__wrap.active').length > 0) {
				$('.accordion__wrap:not(.active)').addClass('inactive');
			}

		});

	}

	return {
		init: init
	}

})(jQuery);
