<?php
    if($properties->posts):

        foreach($properties->posts as $post_id) {
        	require 'loop-item.php';
		}
		
		if($properties->search_params['pagination']):
			APF_Helpers::pagination($properties->max_num_pages, 4, $properties->search_params['apf_page']);
		endif;

    else:
?>
        <div class="apf__no__results">
            <i class="fa-light fa-house-circle-xmark"></i>
            <h2>Oh no! We couldn't find anything.</h2><p>We weren't able to find any properties matching your criteria.<br/>Please try a different set of options or contact us.</p>
        </div><!-- apf__no__results -->

    <?php endif; ?>
