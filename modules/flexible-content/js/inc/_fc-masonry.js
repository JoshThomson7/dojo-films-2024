/**
 * FC Masonry
 */

var Masonry = (function($) {

	function init() {
		var $grid = $('.masonry').imagesLoaded( function() {
			// init Isotope after all images have loaded
			$grid.isotope({
				itemSelector: '.masonry__item'
			});
		});
	}

	return {
		init: init
	}

})(jQuery);
