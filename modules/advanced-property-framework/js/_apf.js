/*
*   Advanced Property Framework scripts
*
*   Scripts and functions for APF
*
*   @package Advanced Property Framework
*   @version 1.0
*/

// @codekit-prepend "inc/_apf-helpers.js";
// @codekit-prepend "inc/_apf-downloadxml.js";
// @codekit-prepend "inc/_apf-map-sticky.js";
// @codekit-prepend "inc/_apf-filter-form.js";
// @codekit-prepend "inc/_apf-search.js";
// @codekit-prepend "inc/_apf-book-viewing-form.js";
// @codekit-prepend "inc/_apf-single-map.js";
// @codekit-prepend "inc/_apf-fc-properties.js";

jQuery(document).ready(function($) {

    // lazyload
    var bLazy = new Blazy({
        selector: '.blazy'
    });

	$(".apf-property--gallery").lightGallery({
        selector: '.apf-property--gallery-image:not(.no-img)',
        hash: false,
        download: false
    });

	$(".apf-property--floorplans").lightGallery({
        selector: '.apf-property--floorplans figure',
        hash: false,
        download: false
    });

    $('.apf__featured').lightSlider({
        item: 3,
	    loop: false,
	    easing: 'cubic-bezier(0.25, 0, 0.25, 1)',
        enableDrag: false
	});

});