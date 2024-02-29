(function ($, root, undefined) {

    /**
     * Converst a serialised array
     * into a JSON Object
     */
    $.fn.APFserializeJSON = function () {

        var unindexed_array = this.serializeArray();
        var indexed_array = {};

        $.map(unindexed_array, function(n, i){
            indexed_array[n['name']] = n['value'];
        });

        return indexed_array;
    }

   /**
	 * Utility to handle storage based on the 'persist' variable
	 */
	$.fn.APFjsStorage = function (item) {

		var persist = apf_ajax_object.apf_persist_delete_unload;
		var storage = persist ? sessionStorage : localStorage;

		if(persist) { 
			localStorage.removeItem(item)
		} else {
			sessionStorage.removeItem(item)
		}

		return {

			exists: function exists() { 
				return storage.getItem(item) ? true : false;
			},

			set: function set(data) { 
				storage.setItem(item, JSON.stringify(data));
			},

			get: function get(parse = false) {
				return parse ? JSON.parse(storage.getItem(item)) : storage.getItem(item);
			},

			getProp: function getProp(prop) {
				return this.get(true)[prop];
			},

			remove: function remove() { 
				storage.removeItem(item);
			}

		};
	};


    /**
     * Outputs skeleton markup
     * as content preloader
     * 
     * @param {string} markup
     * @param {int} rows
     */
    $.fn.skeleton = function(markup, rows) {

        var skeleton = '';
        rows = rows ? rows : 7;

        for (i = 1; i < rows; i++) {
            skeleton += markup;
        }

        return skeleton;

    }

    /**
     * Converts URL parameters to object
     * 
     * @param {string} query 
     */
    $.fn.APFurlParamsAsObject = function(query) {

        query = query.substring(query.indexOf('?') + 1);
    
        var re = /([^&=]+)=?([^&]*)/g;
        var decodeRE = /\+/g;
    
        var decode = function (str) {
            return decodeURIComponent(str.replace(decodeRE, " "));
        };
    
        var params = {}, e;
        while (e = re.exec(query)) {
            var k = decode(e[1]), v = decode(e[2]);
            if (k.substring(k.length - 2) === '[]') {
                k = k.substring(0, k.length - 2);
                (params[k] || (params[k] = [])).push(v);
            }
            else params[k] = v;
        }
    
        var assign = function (obj, keyPath, value) {
            var lastKeyIndex = keyPath.length - 1;
            for (var i = 0; i < lastKeyIndex; ++i) {
                var key = keyPath[i];
                if (!(key in obj))
                    obj[key] = {}
                obj = obj[key];
            }
            obj[keyPath[lastKeyIndex]] = value;
        }
    
        for (var prop in params) {
            var structure = prop.split('[');
            if (structure.length > 1) {
                var levels = [];
                structure.forEach(function (item, i) {
                    var key = item.replace(/[?[\]\\ ]/g, '');
                    levels.push(key);
                });
                assign(params, levels, params[prop]);
                delete(params[prop]);
            }
        }
        return params;
    }

	/**
     * Returns options for sales price dropdown
     */
    $.fn.priceDropdownSales = function() {

        return '<option value="50000">&pound;50,000</option>'+
        '<option value="75000">&pound;75,000</option>'+
        '<option value="100000">&pound;100,000</option>'+
        '<option value="125000">&pound;125,000</option>'+
        '<option value="150000">&pound;150,000</option>'+
        '<option value="175000">&pound;175,000</option>'+
        '<option value="200000">&pound;200,000</option>'+
        '<option value="300000">&pound;300,000</option>'+
        '<option value="400000">&pound;400,000</option>'+
        '<option value="500000">&pound;500,000</option>'+
        '<option value="750000">&pound;750,000</option>'+
        '<option value="1000000">&pound;1,000,000</option>'+
        '<option value="1500000">&pound;1,500,000</option>'+
        '<option value="2000000">&pound;2,000,000</option>'+
        '<option value="2500000">&pound;2,500,000</option>'+
        '<option value="3000000">&pound;3,000,000</option>'+
        '<option value="3500000">&pound;3,500,000</option>'+
        '<option value="4000000">&pound;4,000,000</option>'+
        '<option value="4500000">&pound;4,500,000</option>'+
        '<option value="5000000">&pound;5,000,000</option>';

    }

	/**
     * Returns options for lettings price dropdown
     */
    $.fn.priceDropdownLettings = function() {

        return '<option value="400">&pound;400 pcm</option>'+
        '<option value="500">&pound;500 pcm</option>'+
        '<option value="600">&pound;600 pcm</option>'+
        '<option value="700">&pound;700 pcm</option>'+
        '<option value="800">&pound;800 pcm</option>'+
        '<option value="900">&pound;900 pcm</option>'+
        '<option value="1000">&pound;1,000 pcm</option>'+
        '<option value="1250">&pound;1,250 pcm</option>'+
        '<option value="1500">&pound;1,500 pcm</option>'+
        '<option value="1750">&pound;1,750 pcm</option>'+
        '<option value="2000">&pound;2,000 pcm</option>'+
        '<option value="2250">&pound;2,250 pcm</option>'+
        '<option value="2500">&pound;2,500 pcm</option>'+
        '<option value="2750">&pound;2,750 pcm</option>'+
        '<option value="3000">&pound;3,000 pcm</option>';

    }

	/**
     * Returns options for lettings price dropdown
     */
    $.fn.priceDropdownStudent = function() {

        return '<option value="50">&pound;50 pw</option>'+
		'<option value="80">&pound;80 pw</option>'+
		'<option value="100">&pound;100 pw</option>'+
		'<option value="125">&pound;125 pw</option>'+
		'<option value="150">&pound;150 pw</option>'+
		'<option value="175">&pound;175 pw</option>'+
		'<option value="200">&pound;200 pw</option>'+
		'<option value="250">&pound;250 pw</option>'+
		'<option value="300">&pound;300 pw</option>';
    }

	$.fn.GMapmapStyles = function () {

		return [
			{
				"featureType": "administrative",
				"elementType": "labels.text.fill",
				"stylers": [
					{
						"color": "#6195a0"
					}
				]
			},
			{
				"featureType": "administrative.province",
				"elementType": "geometry.stroke",
				"stylers": [
					{
						"visibility": "off"
					}
				]
			},
			{
				"featureType": "landscape",
				"elementType": "geometry",
				"stylers": [
					{
						"lightness": "0"
					},
					{
						"saturation": "0"
					},
					{
						"color": "#f5f5f2"
					},
					{
						"gamma": "1"
					}
				]
			},
			{
				"featureType": "landscape.man_made",
				"elementType": "all",
				"stylers": [
					{
						"lightness": "-3"
					},
					{
						"gamma": "1.00"
					}
				]
			},
			{
				"featureType": "landscape.natural.terrain",
				"elementType": "all",
				"stylers": [
					{
						"visibility": "off"
					}
				]
			},
			{
				"featureType": "poi",
				"elementType": "all",
				"stylers": [
					{
						"visibility": "off"
					}
				]
			},
			{
				"featureType": "poi.attraction",
				"elementType": "all",
				"stylers": [
					{
						"visibility": "on"
					},
					{
						"hue": "#f400ff"
					},
					{
						"saturation": "31"
					},
					{
						"lightness": "45"
					}
				]
			},
			{
				"featureType": "poi.attraction",
				"elementType": "labels.text",
				"stylers": [
					{
						"visibility": "off"
					}
				]
			},
			{
				"featureType": "poi.business",
				"elementType": "all",
				"stylers": [
					{
						"visibility": "on"
					},
					{
						"hue": "#ffa300"
					},
					{
						"lightness": "27"
					},
					{
						"saturation": "100"
					},
					{
						"gamma": "1"
					}
				]
			},
			{
				"featureType": "poi.business",
				"elementType": "labels",
				"stylers": [
					{
						"visibility": "on"
					}
				]
			},
			{
				"featureType": "poi.business",
				"elementType": "labels.text",
				"stylers": [
					{
						"visibility": "off"
					}
				]
			},
			{
				"featureType": "poi.park",
				"elementType": "geometry.fill",
				"stylers": [
					{
						"color": "#bae5ce"
					},
					{
						"visibility": "on"
					}
				]
			},
			{
				"featureType": "poi.sports_complex",
				"elementType": "all",
				"stylers": [
					{
						"visibility": "on"
					}
				]
			},
			{
				"featureType": "road",
				"elementType": "all",
				"stylers": [
					{
						"saturation": -100
					},
					{
						"lightness": "59"
					},
					{
						"visibility": "simplified"
					},
					{
						"weight": "2.64"
					}
				]
			},
			{
				"featureType": "road.highway",
				"elementType": "all",
				"stylers": [
					{
						"visibility": "off"
					}
				]
			},
			{
				"featureType": "road.highway",
				"elementType": "geometry.fill",
				"stylers": [
					{
						"color": "#fcd883"
					},
					{
						"visibility": "simplified"
					},
					{
						"saturation": "15"
					}
				]
			},
			{
				"featureType": "road.highway",
				"elementType": "labels.text",
				"stylers": [
					{
						"color": "#4e4e4e"
					}
				]
			},
			{
				"featureType": "road.arterial",
				"elementType": "labels.text.fill",
				"stylers": [
					{
						"color": "#c1c1c1"
					}
				]
			},
			{
				"featureType": "road.arterial",
				"elementType": "labels.icon",
				"stylers": [
					{
						"visibility": "off"
					}
				]
			},
			{
				"featureType": "transit",
				"elementType": "all",
				"stylers": [
					{
						"visibility": "simplified"
					}
				]
			},
			{
				"featureType": "transit.station.airport",
				"elementType": "labels.icon",
				"stylers": [
					{
						"hue": "#0a00ff"
					},
					{
						"saturation": "-77"
					},
					{
						"gamma": "0.57"
					},
					{
						"lightness": "0"
					}
				]
			},
			{
				"featureType": "transit.station.bus",
				"elementType": "all",
				"stylers": [
					{
						"visibility": "simplified"
					},
					{
						"hue": "#ff0000"
					}
				]
			},
			{
				"featureType": "transit.station.rail",
				"elementType": "all",
				"stylers": [
					{
						"visibility": "on"
					}
				]
			},
			{
				"featureType": "transit.station.rail",
				"elementType": "labels.text.fill",
				"stylers": [
					{
						"saturation": "96"
					},
					{
						"weight": "1"
					},
					{
						"visibility": "on"
					}
				]
			},
			{
				"featureType": "transit.station.rail",
				"elementType": "labels.icon",
				"stylers": [
					{
						"weight": "1"
					}
				]
			},
			{
				"featureType": "water",
				"elementType": "all",
				"stylers": [
					{
						"visibility": "on"
					},
					{
						"color": "#21dfff"
					}
				]
			},
			{
				"featureType": "water",
				"elementType": "geometry.fill",
				"stylers": [
					{
						"color": "#85e2ff"
					}
				]
			},
			{
				"featureType": "water",
				"elementType": "labels.text.fill",
				"stylers": [
					{
						"lightness": "-49"
					},
					{
						"saturation": "-53"
					},
					{
						"gamma": "0.79"
					}
				]
			}
		];
		
	}

}(jQuery));