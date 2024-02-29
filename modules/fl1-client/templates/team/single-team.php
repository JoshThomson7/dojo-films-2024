<?php
/**
 * Team single template
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

global $post;

get_header();

$team_id = $post->ID;
$team = new FL1C_Team_Member($team_id);
$picture = $team->image();
$job_title = $team->job_title();
$phone = $team->phone();
$email = $team->email();
?>

<section>
	Content
</section>

<?php get_footer(); ?>