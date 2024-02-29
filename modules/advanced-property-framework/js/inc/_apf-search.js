/**
 * APF Search
 * 
 * @package APF
 * @version 2.0
 */

(function ($, root, undefined) {

	var dataStore = $().APFjsStorage('apf_search');

    var sales_prices = $().priceDropdownSales();
	var lettings_prices = $().priceDropdownLettings();
	var student_prices = $().priceDropdownStudent();

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

        var timeout;
        //geolocate();
        handlePriceDropdowns();
        handleCommercialMarket();
        fetchPropertiesOnLoad();
        handleSoldLetText();
		handleView();
		handleMarketSelection();

        /**
		 * Fire search on click
		 * 
		 * @param {object} evt
		 */
        $(document).on('click', '.apf-fetch', function (evt) {

            if(evt.target.tagName === 'BUTTON' || $(evt.target).parent()[0].tagName === 'BUTTON') {
                evt.preventDefault();
            }

            var formData = handleFormData($(this));

            if($(this).hasClass('apf-json')) {
                window.location.replace(apf_ajax_object.apf_page);
            } else {
                clearTimeout(timeout);

                timeout = setTimeout(() => {
                    fetchProperties(formData);
                }, 100);
            }

            var isPop = $(this).closest('.apf__search.pop');
            if(isPop.length > 0 && $(this).hasClass('apf__search__button')) {
                $('.apf__search--close').trigger('click');
            }
            
        });

		/**
		 * Fires search when changing page
		 * 
		 * @param {object} evt
		 */
        $(document).on('click', '.apf-paginate', function (evt) {
        
            var formData = handleFormData($(this));
            fetchProperties(formData);

        });

		/**
		 * Fires search on changing order
		 * 
		 * @param {object} evt
		 */
        $('.apf__filter__order').change(function () {

            var formData = handleFormData($(this));
            fetchProperties(formData);

        });

        /**
		 * Fetches on change for any element
		 * with css class .apf-fetch-on-change
		 * 
		 * @param {object} evt
		 */
        $(document).on('change', '.apf-fetch-on-change', function (evt) {

            clearTimeout(timeout);

            timeout = setTimeout(() => {
                var formData = handleFormData($(this));
                fetchProperties(formData)
            }, 100);
            
        });

        /**
		 * Fetches on change for any element
		 * with css class .apf-view-on-change
		 * 
		 * @param {object} evt
		 */
        $(document).on('click', '.apf-view-on-change', function (evt) {

            clearTimeout(timeout);
            timeout = setTimeout(() => {
                handleView();
            }, 100);
            
        });

		/**
		 * Toggles map view
		 * 
		 * @param {object} evt
		 */
        $(document).on('click', '#apf_view_map', function (evt) {

			$('.apf__results').toggleClass('apf__map__hidden');
            
        });

        /**
		 * Toggles price dropdowns and sold/let text
		 * 
		 * @param {object} evt
		 */
        $(document).on('change', 'input[name="apf_market"]', function (evt) {
        
            var market = $(this).val();
			handleMarketSelection(market);

        });

        /**
		 * Toggles price dropdowns and sold/let text
		 * 
		 * @param {object} evt
		 */
        $(document).on('click', '.apf-search-type label', function (evt) {
        
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                handlePriceDropdowns();
                handleSoldLetText();
            }, 100);

        });

        /**
		 * Toggles mobile search
		 */
        $(document).on('click', '.apf-mobile-search', function (evt) {
            $(this).toggleClass('active');
            $('.apf__search__main').toggleClass('open');
        });

		 /**
		 * Closes mobile search
		 */
		$('.apf__search--close').click(function () {
            $('.apf__search').removeClass('pop');
            $('body').removeClass('no__scroll');
        });

        /**
		 * Toggles mobile map
		 */
        $(document).on('click', '.apf__switch__view__mobile', function(e) {
            e.preventDefault();
    
            $('.apf__results__map__wrap').removeAttr('style');
            $('.apf__results__map').toggleClass('apf__results__map__active');
    
        });

         /**
		  * Toggles mobile search
		  */
        $('.apf__filter__refine').click(function () {
            $('.apf__search').addClass('pop');
            $('body').addClass('no__scroll');
        });

        /**
		 * Geolocates
		 */
        // $('.apf-do-geocode').on('click', function(e) {
        //     $().APFjsStorage('apf_do_geocode').set(true)
        //     geolocate();
        // });

        /**
		 * Removes geocode flag from localStorage
		 */
        $('input[name="apf_location"]').on('keyup', function(e) {

            clearTimeout(timeout);
            timeout = setTimeout(function () {
                $().APFjsStorage('apf_do_geocode').set(false)
            }, 500);
            
        });

		/**
		 * Radius display change
		 */
		$(document).on('change', '#apf_radius', function (evt) {
			handleRadiusChangeDisplay($(this));
		});

		/**
		 * Price display change
		 */
		$(document).on('change', '#apf_minprice, #apf_maxprice', function (evt) {
			handlePriceChangeDisplay($(this));
		});

		/**
		 * Radius display change
		 */
		$(document).on('change', '#apf_minbeds, #apf_maxbeds', function (evt) {
			handleBedsChangeDisplay($(this));
		});

		/**
		 * Price display change
		 */
		$(document).on('click', '.apf-display', function (evt) {

			$('.apf-display').not(this).removeClass('active');
			$(this).toggleClass('active');
			
			var height = $(this).outerHeight();

			var selector = '.apf-pop-price';
			if($(this).hasClass('apf-beds-display')) {
				selector = '.apf-pop-beds';
			}

			if($(this).hasClass('apf-radius-display')) {
				selector = '.apf-pop-radius';
			}
			
			$(selector).toggleClass('popped').css({
				top: height + 16,
				right: 0,
			});

			$('.apf-selects-pop:not('+selector+')').removeClass('popped');
		});

		// Add this click event handler to prevent propagation
		$(document).on('click', '.apf-selects-pop', function (evt) {
			evt.stopPropagation();
		});

		$().closeOnClickOutside('.apf-display', '.apf-selects-pop');

    });

    /**
     * Check if we should fetch on load
     */
    function fetchPropertiesOnLoad() {

        if($('.apf__results').length > 0) {
            var formData = handleFormData();
            fetchProperties(formData);
        }

    }

	function handleRadiusChangeDisplay(el) {

		var targetEl = $('.apf-radius-display');	
		var value = el.find('option:selected').text();

		targetEl.find('.to').text(value);

	}

	function handleMarketSelection(market = null) {

		var maybeMarket = $('input[name="apf_market"]:checked').val();
		market = !market ? maybeMarket : market;

		if(market === 'student') {
			$('input[name="apf_dept"][value="to-let"]').prop('checked', true);
			$('.apf-search-type').hide();
		} else {
			$('.apf-search-type').show();
		}

	}

	function handlePriceChangeDisplay(el) {

		var targetEl = $('.apf-price-display');	
		var value = el.find('option:selected').val();
		var output = '£'+humanReadablePrice(value);

		switch (el.attr('id')) {
			case 'apf_minprice':
				targetEl = targetEl.find('.from');
				if(value === '0' || value === '') {
					output = 'No min';
				}
				break;

			case 'apf_maxprice':
				targetEl = targetEl.find('.to');
				if(value === '0' || value === '') {
					output = 'No max';
				}
				break;
		
			default:
				break;
		}

		targetEl.text(output);

	}

	function handleBedsChangeDisplay(el) {

		var targetEl = $('.apf-beds-display');	
		var value = el.find('option:selected').text();

		switch (el.attr('id')) {
			case 'apf_minbeds':
				targetEl = targetEl.find('.from');
				break;

			case 'apf_maxbeds':
				targetEl = targetEl.find('.to');
				break;
		
			default:
				break;
		}

		targetEl.text(value);

	}

	function humanReadablePrice(value) {
		const suffixes = ['', 'K', 'M', 'B', 'T'];
	  
		if (value === 0) {
		  return '0';
		}
	  
		const absValue = Math.abs(value);
		const sign = value < 0 ? '-' : '';
	  
		const index = Math.floor(Math.log10(absValue) / 3);
		const scaledValue = absValue / Math.pow(1000, index);
	  
		const formattedValue =
		  scaledValue % 1 === 0 ? scaledValue : scaledValue.toFixed(1);
	  
		return sign + formattedValue + suffixes[index];
	}

    /**
     * Handles price dropdowns
     */
    function handlePriceDropdowns() {

		var market = $('input[name="apf_market"]:checked').val();
        var type = $('.apf-search-type').find('input:checked').val();

		if(market === 'student') {

			$('#apf_minprice').html('<option value="">Min price</option>'+student_prices);
			$('#apf_maxprice').html('<option value="">Max price</option>'+student_prices);

		} else {

			switch (type) {
				case 'to-let':
					$('#apf_minprice').html('<option value="">Min price</option>'+lettings_prices);
					$('#apf_maxprice').html('<option value="">Max price</option>'+lettings_prices);
					break;
			
				default:
					$('#apf_minprice').html('<option value="">Min price</option>'+sales_prices);
					$('#apf_maxprice').html('<option value="">Max price</option>'+sales_prices);
					break;
			}

		}

    }

    /**
     * Handles price dropdowns
     */
     function handleCommercialMarket() {

        if($('.apf__results').length > 0) {
            
            var market = $('input[name="apf_market"]:checked').val();
            var typeSelect = $('select[name="apf_property_type"]');
            var types = []
            var markup = '<option value="">Any property type</option>';

            var bedsEl = $('.apf-beds');

            switch (market) {
                case 'commercial':
                    types = typeSelect.data('commercial')
                    bedsEl.hide();
                    break;
            
                default:
                    types = typeSelect.data('residential') 
                    bedsEl.show();
                    break;
            }

            types = types instanceof Array ? types : []

            if(types.length > 0) {
                $.each(types, function(idx, type) {
                    markup += '<option value="'+type+'">'+type+'</option>';
                });
            }

            typeSelect.html(markup);

        }

    }

    /**
     * Handles text
     */
     function handleSoldLetText() {

        var type = $('.apf-search-type').find('input:checked').val();

        var text = '';
        var targetEl = $('label.apf__status');

        switch (type) {
            case 'to-let':
                text = 'Show Let properties'
                break;
        
            default:
                var text = 'Show Sold properties';
                break;
        }

        targetEl.text(text)

    }

    /**
     * Handles view change dropdowns
     */
    function handleView() {

        var view = $('input[name="apf_view"]:checked').val();
		var jsonData = dataStore.get(true);

        switch (view) {
            case 'list':
                $('.apf__properties').addClass('list');
				$.extend(jsonData, {'apf_view': view});
                break;
        
            default: // grid
                $('.apf__properties').removeClass('list');
				$.extend(jsonData, {'apf_view': view});
                break;
        }
		
		dataStore.set(jsonData);

		if(!$('.apf__results').length) return;
		updateURL(jsonData)

    }

    /**
     * Returns serialised form data
     */
    function getFormData(formEl, format) {
        
        switch (format) {
            case 'object':
                return formEl.APFserializeJSON();
                break;

            case 'array':
                return formEl.serializeArray();
                break;
        
            default:
                return formEl.serialize();
                break;
        }

    }

    /**
     * Converts JSON object to URL
     * params string
     * 
     * @param obj data 
     */
    function paramify(data) {
        return new URLSearchParams(data).toString();
    }

    /**
     * Handles form data and localStorage
     * 
     * @param obj clickedEl 
     */
    function handleFormData(clickedEl) {

        var formEl = $('#apf_search');

        // JSON Store
        //var dataStore = $().APFjsStorage('apf_search');

        // Make form data ready
        var formData = getFormData(formEl, 'object');

        // On load?
        if(!clickedEl) {

            var params = window.location.search;

            if(params) {
                params = params.substr(1);
                params = $().APFurlParamsAsObject(params);
                dataStore.set(params)
            }

        } else {

            if(clickedEl.hasClass('apf-paginate')) {
                
                var page = clickedEl.data('apf-page');
                var jsonData = dataStore.get(true);
                $.extend(jsonData, {'apf_page': page});
                dataStore.set(jsonData);

            } else {
                dataStore.set(formData);
            }

        }

        // Last safety check: if none of the above, set store to formData
        if(!dataStore.get()) {
            dataStore.set(formData)
        }

        // Define our variables
		var persistSearch = apf_ajax_object.apf_persist_search
        var jsonData = dataStore.get(true);
		if(!persistSearch) {
			jsonData = formData;
			dataStore.remove();
		}
        var stringData = paramify(jsonData);

        // Update URL
        if(!clickedEl || !clickedEl.hasClass('apf-json')) {
            updateURL(jsonData)
        }

        // Highlight form elements
        highlightEl(jsonData);

        return {
            dataStore: dataStore,
            jsonData: jsonData,
            stringData: stringData
        }

    }

    /**
     * Highlights/selects/checks form
     * elements programatically
     * 
     * @param obj jsonData 
     */
    function highlightEl(jsonData) {

        $.each(jsonData, function (name, value) {
            
            var el = $('[name="'+name+'"]');

            if(el.length > 0) {
                
                var type = $(el)[0].tagName;

                switch (type) {
                    case 'INPUT':
                        
                        if( $(el).is(':radio') && $(el).val() != value ) {
                            $(el).trigger('click');
                            if($(el).parent().hasClass('apf-search-type')) {
                                handlePriceDropdowns();
                            }

                        } else if( $(el).is(':checkbox') && $(el).val() === value ) {
                            $(el).attr('checked', 'checked');
                            
                        } else if($(el).is(':text')) {
                            $(el).val(value);
                        }

                        break;

                    case 'SELECT':
                        $(el).val(value);
                        break;
                
                    default:
                        break;
                }

            }

        });

    }

	/**
     * Updates URL
     * 
     * @param obj formData 
     */
    function updateURL(jsonData) {

		var stringData = paramify(jsonData);
		history.replaceState({ id: 'apf_url' }, '', '?' + stringData);

	}

    /**
     * Fetches properties via AJAX
     * 
     * @param obj formData 
     */
    function fetchProperties(formData) {

        var responseEl = $('.apf__properties');
        
        responseEl.html($().skeleton(skeleton));

        $.ajax({
            url: apf_ajax_object.ajax_url,
            dataType: 'html',
            type: 'POST',
            data: ({
                'action': 'apf_property_search',
                'apf_security': apf_ajax_object.ajax_nonce,
                'search_data': formData.jsonData
            }),

            success: function (data) {

                responseEl.html(data);                    

                var bLazy = new Blazy({
                    selector: '.blazy'
                });

                // //scroll to results
                // if (clickedEl.hasClass("apf__paginate")) {
                //     var destination = $('.apf__properties').offset().top;
                //     $("html:not(:animated), body:not(:animated)").animate({ scrollTop: destination - 200 }, 700);
                // } else {
                //     $('.apf__properties article').addClass('in-view');
                // }
            }

        });

        mapFilter(formData.jsonData);

    }

	/**
     * Geolocate
     */
	function geolocate() {

        var geo = $().APFjsStorage('apf_do_geocode').get();
        
        if(!geo || geo === 'undefined' || geo === undefined) {
            $().APFjsStorage('apf_do_geocode').remove()
        }

        var locationEl = $('input[name="apf_location"]');
        locationEl.val('')

        if ('permissions' in navigator && locationEl.length > 0) {
            
            navigator.permissions.query({name:'geolocation'}).then(function(result) {

                if (result.state === 'denied') {
                    locationEl.attr('placeholder', 'Area, postcode, town or street')
                }

                result.onchange = function () {
                    if (result.state === 'denied') {
                        locationEl.attr('placeholder', 'Area, postcode, town or street')
                    }
                }

            })

        }

        if ('geolocation' in navigator && locationEl.length > 0) {
            
            var icon = $('.apf-do-geocode i');
            icon.toggleClass('fa-location fa-spinner-third fa-spin')
            locationEl.attr('placeholder', 'Retreiving your location...');
        
            navigator.geolocation.getCurrentPosition(function(position) {
                var lat = position.coords.latitude
                var lng = position.coords.longitude
                geocode(lat, lng)
            }, function() {
                locationEl.attr('placeholder', 'Area, postcode, town or street')
            });

        }

    }

    /**
     * Geocode via Google API
     * 
     * @param {int} latitude 
     * @param {int} longitude 
     */
    function geocode(latitude, longitude) {

        var locationEl = $('input[name="apf_location"]');
        var icon = $('.apf-do-geocode i');

        $.ajax(
            'https://maps.googleapis.com/maps/api/geocode/json?latlng=' +
                latitude +
                ',' +
                longitude +
                '&key=' +
                apf_ajax_object.apf_google_api_key_geocoding
        ).then(
            function success(response) {
                locationEl.attr('placeholder', 'Area, postcode, town or street')
                icon.toggleClass('fa-spinner-third fa-spin fa-location')
				console.log(response);
                var address = response.results[0].formatted_address
                $('input[name="apf_location"]').val(address)
            },
            function fail(status) {
                locationEl.attr('placeholder', 'Area, postcode, town or street')
                console.log('Request failed. Returned status of', status)
            }
        )
    }

    /**
     * Filters map after posts filter
     * 
     * @param object formData
     */
    function mapFilter(formData) {

        $('#apf_map').html('').addClass('skeleton');

        $.ajax({
            url: apf_ajax_object.ajax_url,
            dataType: 'JSON',
            type: 'POST',
            data: ({
                'action': 'apf_map',
                'ajax_security': apf_ajax_object.ajax_nonce,
                'search_data': formData
            }),
            success: function (response) {
                var postIDs = response.toString();
                apfMap(postIDs);
                $('#apf_map').removeClass('skeleton');
            },
            error: function (err) {
                console.error(err);
            }
        });


    }

    /**
     * Initialises Google Map
     * 
     * @param object postIDs 
     */
    function apfMap(postIDs) {
    
        var centre = new google.maps.LatLng(53.48831593066507, -2.2435612630077726);

        var map = new google.maps.Map(document.getElementById("apf_map"), {
            center: centre,
            zoom: 11,
            mapTypeId: 'roadmap',

            mapTypeControl: true,
            mapTypeControlOptions: {
                style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
                position: google.maps.ControlPosition.CENTER_BOTTOM
            },

            zoomControl: true,
            zoomControlOptions: {
                style: google.maps.ZoomControlStyle.SMALL,
                position: google.maps.ControlPosition.TOP_LEFT
            },

            streetViewControl: true,
            streetViewControlOptions: {
                position: google.maps.ControlPosition.TOP_LEFT
            },
            styles: $().GMapmapStyles()
        });

        var i;
        var gmarkers = [];
        var infowindow = new google.maps.InfoWindow();
        var bounds = new google.maps.LatLngBounds();
        
        var marker_icon = new google.maps.MarkerImage(apf_ajax_object.apf_path+"/img/marker-property.png", null, null, null, new google.maps.Size(50,38));
        var hover_icon = new google.maps.MarkerImage(apf_ajax_object.apf_path+"/img/marker-property-hover.png", null, null, null, new google.maps.Size(60,46));
        
        // Read the data
        downloadUrl(apf_ajax_object.apf_properties_map_url+'?posts='+postIDs, function(doc) {
            var xml = xmlParse(doc);
            var markers = xml.documentElement.getElementsByTagName("marker");

            for (var i = 0; i < markers.length; i++) {
                // obtain the attribues of each marker
                var lat = parseFloat(markers[i].getAttribute("lat"));
                var lng = parseFloat(markers[i].getAttribute("lng"));
                var point = new google.maps.LatLng(lat,lng);
                var permalink = markers[i].getAttribute("permalink");
                var name = markers[i].getAttribute("name");
                var price = markers[i].getAttribute("price");
                var type = markers[i].getAttribute("type");
                var status = markers[i].getAttribute("status");
                var seo = markers[i].getAttribute("seo");
                var image = markers[i].getAttribute("image");
                var html =
                    '<div class="infowindow">'+
                        '<div class="infowindow__img">'+
                            '<a href="#" title="Close" class="infowindow-close"><i class="fal fa-times"></i></a>'+
                            '<a href="'+permalink+'" title="Click for full details on '+name+'">'+
                                '<img src="'+image+'" alt="'+name+'">'+
                            '</a>'+
                        '</div>'+
                        '<h3>'+
                            '<a href="'+permalink+'" title="Click for full details on '+name+'">&pound;'+price+(type == 'to-let' ? ' <small>PW</small>' : '' )+'</a>'+
                            (status !== '' ? '<span>'+status+'</span>' : '' )+
                        '</h3>'+
                        '<div class="infowindow__content">'+
                            '<h5>'+seo+'</h5>'+
                            '<p>'+name.replace(/,/g, '<br>')+'</p>'+
                            '<p class="infowindow__content__name-mobile">'+name+'</p>'+
                            '<a href="'+permalink+'" title="Click for full details on '+name+'" class="button small icon-right">Details<i class="fa-light fa-chevron-right"></i></a>'+
                        '</div>'+
                    '</div>';
                // create the marker
                var marker = createMarker(point,name,html);

                bounds.extend(point);
            }

            map.fitBounds(bounds);

            if(map.getZoom() > 18) {
                map.setZoom(16)
            }
        });

        // A function to create the marker and set up the event window
        function createMarker(latlng,name,html,icon) {
            var contentString = html;
            var marker = new google.maps.Marker({
                position: latlng,
                icon : marker_icon,
                map: map,
                title: name,
                //zIndex: Math.round(latlng.lat()*-100000)<<5
            });

            marker.myname = name;
            marker.myicon = icon;
            gmarkers.push(marker);

            google.maps.event.addListener(marker, 'click', function() {

                for (var i = 0; i < gmarkers.length; i++) {
                    gmarkers[i].setIcon(marker_icon);
                }

                marker.setIcon(hover_icon);

                $('#infowindow').addClass('open');
                $('#infowindow').removeClass('closed empty');
                $('#infowindow').empty();
                $('#infowindow').append(html);
            });
        }

        google.maps.event.addDomListener(window, "resize", function() {
            var center = map.getCenter();
            google.maps.event.trigger(map, "resize");
            map.setCenter(center);
        });

        google.maps.event.addListener(map, 'click', function() {
            for (var i = 0; i < gmarkers.length; i++) {
                gmarkers[i].setIcon(marker_icon);
            }

            $('#infowindow').empty();
            $('#infowindow').removeClass('open');
            $('#infowindow').addClass('closed empty');
        });

        $('#infowindow').on('click', "a.infowindow-close", function(e) {
            e.preventDefault();

            for (var i = 0; i < gmarkers.length; i++) {
                gmarkers[i].setIcon(marker_icon);
            }

            $('#infowindow').empty();
            $('#infowindow').removeClass('open');
            $('#infowindow').addClass('closed empty');
        });

        // transit layer
        var transitLayer = new google.maps.TransitLayer();
        transitLayer.setMap(map);

        // Load stuff when map has finished loading
        google.maps.event.addListenerOnce(map, 'idle', function(){
			$('.apf__properties article').hover(
				// mouse in
				function () {
					var property_index = $('.apf__properties article').index(this);
					gmarkers[property_index].setIcon(hover_icon);
				},

				// mouse out
				function () {
					var property_index = $('.apf__properties article').index(this);
					gmarkers[property_index].setIcon(marker_icon);
				}
			);
        });

        // responsive
        var windowWidth = $(window).width();

        if(windowWidth < 900) {
            map.setOptions({
                mapTypeControl: false,

                zoomControl: true,
                zoomControlOptions: {
                    style: google.maps.ZoomControlStyle.SMALL,
                    position: google.maps.ControlPosition.RIGHT_TOP
                },

                streetViewControl: true,
                streetViewControlOptions: {
                    position: google.maps.ControlPosition.RIGHT_TOP
                },
            });
        }

        $('.apf__map__hide').on('click', function(e) {
            $(this).toggleClass('active');
            $('.apf__results').toggleClass('apf__map__hidden');
        });
    
    }
    

})(jQuery, this);
