/**
 * Feature
 */

var Feature = (function($) {

	function init()	{

		$(document).on('click', '.feature__action .has-dropdown', function(e) {
			e.preventDefault();
			$('.feature__action-dropdown').toggleClass('is-active');
		});

	}

	return {
		init: init
	}

})(jQuery);
