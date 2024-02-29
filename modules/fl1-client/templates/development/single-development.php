<?php
/**
 * Template name: Development
 *
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post;
get_header();
AVB::avb_banners();

$development = new X1_Development($post->ID);
$gallery = $development->gallery();
$location = $development->location();
$latitude = isset($location['lat']) ? $location['lat']: '';
$longitude = isset($location['lng']) ? $location['lng']: '';
$team = $development->team_member();
$team_bio = $development->team_member_bio();
?>

<main class="development">
	<div class="max__width">
		<div class="development--wrapper">
			<div class="development--main">
				<div class="development--gallery">
					<?php
						$gallery_counter = 0;
						foreach($gallery as $gallery_url):
					?>
						<div data-thumb="<?php echo $gallery_url; ?>" data-src="<?php echo $gallery_url; ?>" class="development--gallery-slide">
							<img src="<?php echo $gallery_counter == 0 ? $gallery_url : ''; ?>">
						</div>
					<?php $gallery_counter++; endforeach; ?>
				</div>
				
				<div id="development_map" data-src="<?php echo esc_url(APF_URL.'img/apf-blank.png'); ?>" data-lat="<?php echo $latitude; ?>" data-lng="<?php echo $longitude; ?>"></div>

				<?php FC_Helpers::flexible_content(); ?>
			</div>

			<aside>
				<article class="actions">
					<h3>Get in touch</h3>
					<p>Like what you see? Get in touch with our team to for more information.</p>
					<a href="#" class="button primary">Get in touch</a>
				</article>
				
				<?php
					if(is_a($team, 'FL1C_Team_Member')):
						$team_img = $team->image(300, 300);
						$team_img_url = isset($team_img['url']) ? $team_img['url'] : '';
				?>
					<article class="team">
						<h3>Speak to <?php echo $team->name('first'); ?></h3>
						<?php if($team_img_url): ?>
							<figure>
								<img src="<?php echo $team_img_url; ?>" alt="<?php echo $team->name(); ?>">
							</figure>
						<?php endif; ?>

						<?php if($team_bio): ?>
							<?php echo $team_bio; ?>
						<?php endif; ?>
						
						<?php echo $team->email() ? FL1_Helpers::hide_email($team->email(), 'Get in touch with Eve', 'button primary') : ''; ?>
					</article>
				<?php endif; ?>
			</aside>
		</div>
	</div>
</main>

<?php get_footer(); ?>