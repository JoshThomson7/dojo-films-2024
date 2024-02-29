<?php
/**
 * Reources
 */

$resource_ids = get_sub_field('resources');

$args = array(
	'post_type' => 'product',
	'post__in' => $resource_ids,
	'fields' => 'ids',
);

$resources = new WP_Query($args);
$resources = array(
	'posts' => $resources->posts,
	'max_num_pages' => $resources->max_num_pages,
);

$pagination = false;
?>

<div class="resources-wrap resources-wrap--carousel grid-boxes-carousel">
	<?php include TLC_PATH .'templates/resources/resources-loop.php'; ?>
</div>