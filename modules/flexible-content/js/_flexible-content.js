/**
 * Flexible Content
 * 
 * @author FL1 Digital
 * @version 2.0.0
 */

// @codekit-prepend "inc/_fc-accordion.js";
// @codekit-prepend "inc/_fc-carousel.js";
// @codekit-prepend "inc/_fc-feature.js";
// @codekit-prepend "inc/_fc-gallery.js";
// @codekit-prepend "inc/_fc-masonry.js";
// @codekit-prepend "inc/_fc-tabs.js";
// @codekit-prepend "inc/_fc-team.js";

jQuery(document).ready(function($) {

	// Accordion
	Accordion.init();

	// Carousels
    Carousels.fcLayout();
	Carousels.footerLogos();
	Carousels.gridBoxes();
	Carousels.images();
	Carousels.testimonials();

	// Gallery
    Gallery.init();

	// Feature
    Feature.init();

	// Masonry
    Masonry.init();

	// Tabs
    Tabs.init();

	// Team
    Team.init();
    Team.filters();
	
});