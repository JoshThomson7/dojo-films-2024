/**
 * FC Team
 */

var Team = (function($) {

	function init() {

		$('.team__modal').click(function() {
			var team_name = $(this).attr('href');
			//console.log(team_name);

			$('body').addClass('no__scroll');
			$('.team__popup__holder').addClass('on');
			$(team_name).addClass('is__active');

			return false;
		});

		$('a.team__switch').click(function() {
			if($(this).hasClass('team__next')) {
				var next = $(this).closest('.team__popup.is__active').next('.team__popup').attr('id');
			} else {
				var next = $(this).closest('.team__popup.is__active').prev('.team__popup').attr('id');
			}

			var current = $(this).closest('.team__popup.is__active').attr('id');

			$('#'+current).addClass('rotate__bye');
			$('#'+next).addClass('is__active rotate__hello');
			$('#'+current).removeClass('is__active');

			return false;
		});

		//close
		$('a.team__close').click(function() {
			$('.team__popup').removeClass('is__active rotate__hello rotate__bye');
			$('.team__popup__holder').removeClass('on');
			$('body').removeClass('no__scroll');

			return false;
		});

		// $(document).mouseup(function(e) {
		//     var container = $('.team__popup');

		//     // if the target of the click isn't the container nor a descendant of the container
		//     if (!container.is(e.target) && container.has(e.target).length === 0) {
		//         $('.team__popup').removeClass('is__active rotate__hello rotate__bye');
		//         $('.team__popup__holder').removeClass('on');
		//         $('body').removeClass('no__scroll');
		//     }
		// });

	}

	function filters() {

		/**
		 * Filterify
		 */
		var teamFilters = $('#fc_team_filters').filterify({
			ajaxObject: 'fl1_ajax_object',
			ajaxAction: 'fc_team_filters',
			responseEl: '#fc_team_response',
			skeleton: {
				count: 9,
				markup: 
				'<article class="skeleton">'+
					'<div class="padder">'+
						'<a class="team__modal"></a>'+
						'<h5></h5>'+
						'<h6></h6>'+
					'</div>'+
				'</article>'
			},
		});


	}

	return {
		init: init,
		filters: filters
	}

})(jQuery);
