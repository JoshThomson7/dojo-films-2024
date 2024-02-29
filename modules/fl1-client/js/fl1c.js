(function ($, root, undefined) {

	/**
     * On load
     */
    $(window).on('load', function () {

		$('.development--gallery').lightSlider({
			item: 1,
			loop: false,
			easing: 'cubic-bezier(0.25, 0, 0.25, 1)',
			enableDrag: false,
			gallery: true,
			galleryMargin: 8,
        	thumbMargin: 8,
			thumbItem: 6,
			slideMargin: 0,
			onSliderLoad: function(el) {
				el.lightGallery({
					selector: '.development--gallery .development--gallery-slide',
					download: false,
					hash: false,
					exThumbImage: 'data-thumb'
				});
			},
			onBeforeSlide: function(el) {
				var slide = el.find('.development--gallery-slide').eq(el.getCurrentSlideCount() - 1);
				var img = slide.find('img');
				var src = img.attr('src');
				if(src !== '') { return; }
				var data_src = slide.data('src');
				img.attr('src', data_src);
				img.css('display', 'initial');
			}
		});

		if($('#development_map').length > 0) { 
			var bLazy = new Blazy({
				selector: '#development_map',
				success: function(element){
					apf_single_map('#development_map');
				}
			});
		}

	});

})(jQuery, this);