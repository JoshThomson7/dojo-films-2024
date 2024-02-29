/**
 * GF JS
 */

(function ($, root, undefined) {

	// window load
	$(window).on('load', function() {
		
		$(document).on('click', 'a[data-gf-ajax-trigger]', function(e) {
			e.preventDefault();
			gf_form_ajax($(this));
		});
	
		$(document).on('click', '.gf__modal__form .close', function(e) {
			e.preventDefault();
	
			// hide pop-up
			$('.gf__modal__form__overlay').removeClass('open');
			$(this).closest('.flexible__content').removeAttr('style');
			$('body').removeClass('no__scroll');
	
			setTimeout(function() {
				$('[id^="gf_ajax_form_"]').html('');
			}, 500);
	
		});

	});

	function gf_form_ajax(el) {
	
		// set variables
		var gf_id = el.attr('data-gf-id');
	
		// show pop-up
		$('.gf__modal__form .wp__loading').addClass('is__loading');
		$('.gf__modal__form__overlay').addClass('open');
		$('body').addClass('no__scroll');
	
		// AJAX results
		$.ajax({
			url: fl1_ajax_object.ajaxUrl,
			dataType: 'html',
			type: 'GET',
			data: ({
				'action' : 'gf_ajax_form',
				'security' : fl1_ajax_object.ajaxNonce,
				'gf_id' : gf_id,
			}),
	
			success: function(data) {
				$('.wp__loading').removeClass('is__loading');
				$('#gf_ajax_form_'+gf_id).html(data);
			},
	
			error: function(xhr, ajaxOptions, thrownError){
				alert(xhr.status);
			}
		});
	}

}(jQuery));