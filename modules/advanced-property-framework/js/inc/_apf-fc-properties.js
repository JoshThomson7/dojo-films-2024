/**
 * APF FC properties
 *
 */

(function ($, root, undefined) {

    var skeleton = 
        '<article class="skeleton">'+
            '<div class="apf__property__border">'+
                '<a class="apf__property__img"></a>'+
                '<div class="apf__property__details__wrap">'+
                    '<div class="apf__property__details">'+
                        '<h3></h3>'+
                        '<h5></h5>'+
                        '<p></p>'+
                    '</div>'+
                    '<div class="apf__property__meta">'+
                        '<div class="apf__property__meta__data">'+
                            '<span></span>'+
                            '<span></span>'+
                            '<span></span>'+
                        '</div>'+
                        '<a></a>'+
                    '</div>'+
                '</div>'+
            '</div>'+
        '</article>';

    /**
     * On load
     */
    $(window).on('load', function () {
		
		fetchOnLoad();

        /**
		 * Fire search on click
		 * 
		 * @param {object} evt
		 */
        $(document).on('click', '.fc-properties-tabs li a', function (e) {

			e.preventDefault();

            $('.fc-properties-tabs li a').removeClass('active');
			$(this).addClass('active');

			var slick = $('.fc-properties-items.slick-initialized');
			if(slick.length > 0) {
				slick.slick('unslick');
			}

			var formData = $(this).data('json');
			fetchProperties(formData);
            
        });

    });

	/**
     * Fetches properties via AJAX on page load
     * 
     * @param obj formData 
     */
    function fetchOnLoad() {

		if($('.fc-properties-tabs').length) {
			var formData = $('.fc-properties-tabs li a.active').data('json');
			fetchProperties(formData);
		}

	}

	function carouselInit(parent) {

		var formData = $('.fc-properties-tabs li a.active').data('json');
		var count = parent.find('article');

		if(count.length > 3 && !formData.is_grid) {

			parent.slick({
				dots: true,
				infinite: false,
				speed: 300,
				slidesToShow: 3,
				slidesToScroll: 1,
				variableWidth: false,
				autoplay: false,
				arrows: false,
				cssEase: 'cubic-bezier(0.645, 0.045, 0.355, 1)',
				responsive: [
					{
						breakpoint: 1100,
						settings: {
							slidesToShow: 2,
							slidesToScroll: 2
						}
					},
					{
						breakpoint: 560,
						settings: {
							slidesToShow: 1,
							slidesToScroll: 1
						}
					}
				]
			});

		}
	
	}

	/**
     * Fetches properties via AJAX
     * 
     * @param obj formData 
     */
    function fetchProperties(formData) {

		var responseEl = $('.fc-properties-items');
        responseEl.html($().skeleton(skeleton, 4));

        $.ajax({
            url: apf_ajax_object.ajax_url,
            dataType: 'html',
            type: 'POST',
            data: ({
                'action': 'fc_properties',
                'apf_security': apf_ajax_object.ajax_nonce,
                'search_data': formData
            }),

            success: function (data) {

                responseEl.html(data);                    

                var bLazy = new Blazy({
                    selector: '.blazy'
                });

				//parent.slick('unslick');
				carouselInit(responseEl);

            }

        });

    }

		
})(jQuery, this);
