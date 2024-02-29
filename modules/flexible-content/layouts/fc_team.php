<?php
/**
 * Team
 */
$team_type = get_sub_field('type');
$no_bios = get_sub_field('no_bios');

if($team_type === 'full'):

	$team_cats = get_terms(array(
		'taxonomy' => 'team_department',
		'hide_empty' => false,
	));

	$branches = new WP_Query(array(
		'post_type' => 'branch',
		'post_status' => 'publish',
		'posts_per_page' => -1,
		'fields' => 'ids',
	));
?>
	<div class="team__filters">
		<form id="fc_team_filters">
			<article>
				<label>Search</label>
				<input type="text" name="team_keywords" placeholder="Search by name" />
			</article>

			<?php if(!empty($team_cats)): ?>
				<article>
					<label>Department</label>
					<select name="team_categories[]">
						<option value="">All Categories</option>
						<?php foreach($team_cats as $team_cat): ?>
							<option value="<?php echo $team_cat->term_id; ?>"><?php echo $team_cat->name; ?></option>
						<?php endforeach; ?>
					</select>
				</article>
			<?php endif; ?>
			
			<?php if(!empty($branches)): ?>
				<article>
					<label>Office</label>
					<select name="team_offices[]">
						<option value="">All Offices</option>
						<?php
							foreach($branches->posts as $branch_id):
								$branch = new APF_Branch($branch_id);
						?>
							<option value="<?php echo $branch_id; ?>"><?php echo $branch->get_name(); ?></option>
						<?php endforeach; ?>
					</select>
				</article>
			<?php endif; ?>
		</form>
	</div>

	<div id="fc_team_response" class="team__wrap"></div>
<?php
	else:

	$args = array(
		'post_type'         => 'team',
		'post_status'       => 'publish',
		'orderby'           => 'menu_order',
		'order'             => 'asc',
		'posts_per_page'    => -1,
		'fields'			=> 'ids',
	);
	
	if($team = get_sub_field('team_custom')) {
		$args['post__in'] = $team;
	}
	
	$team_query = new WP_Query($args);
	$team = $team_query->posts;
	$team_total = $team_query->post_count;
	
	include 'fc_team-loop.php';

endif;
?>