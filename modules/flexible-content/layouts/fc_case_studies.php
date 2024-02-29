<?php
/**
 * Case Studies
 */

$case_studies = get_sub_field('case_studies');

switch (count($case_studies)) {
	case 1:
		$width = 'full';
		break;

	case 2:
		$width = 'half';
		break;
	
	default:
		$width = 'third';
		break;
}

if(!empty($case_studies)):
?>
<div class="fc-case-studies">
	<?php
		foreach($case_studies as $case_study_id):
		
			$case_study = new FL1_Blog($case_study_id);
			$case_study_image = $case_study->image(900, 500, true);
			$banner_image = '';
			if(!empty($case_study_image)) {
				$banner_image = ' style="background-image: url('.$case_study_image['url'].')"';
			} else {
				$banner_image = ' style="background-image: url('.get_stylesheet_directory_uri().'/img/sq-blog-placeholder.jpg)"';
			}

			// Main category
			$case_study_cat_id = $case_study->main_category('ids');
			$case_study_cat = $case_study->main_category('id=>name');
	?>

		<article class="case-study <?php echo $width; ?>">
			<div class="case-study--padder">
				<a class="case-study--img" href="<?php echo $case_study->url(); ?>" <?php echo $banner_image; ?>></a>
				
				<div class="case-study--content">
					<h2><a href="<?php echo $case_study->url(); ?>" title="<?php echo $case_study->title(); ?>"><?php echo $case_study->title(); ?></a></h2>
					
					<p><?php echo $case_study->excerpt(15); ?></p>

					<div class="case-study--action">
						<a href="<?php echo $case_study->url(); ?>" class="button primary <?php echo $featured ? 'large' : ''; ?>">
							<span>Read full Case Study</span>
						</a>
					</div>
				</div>
			</div>
		</article>
	<?php endforeach; ?>
</div>
<?php endif; ?>