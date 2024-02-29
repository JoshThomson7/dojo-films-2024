<?php
/**
 * Feature tabs
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$image_id = get_sub_field('image');
$heading = get_sub_field('heading') ?? '';
$tabs = get_sub_field('tabs');

if($tabs):
?>

<div class="feature-tabs<?php if(!$image_id) { echo ' no-image';} ?>">
    <?php
        if($image_id):
            $image = vt_resize($image_id, '', 600, 600, true);
    ?>
        <figure>
            <img src="<?php echo $image['url']; ?>" alt="<?php echo $heading ? $heading : ''; ?>">
        </figure>
    <?php endif; ?>

    <div class="tabbed-wrapper horizontal left">
        <?php if($heading): ?><h3><?php echo $heading; ?></h3><?php endif; ?>

        <ul class="tabbed">
            <?php
                foreach($tabs as $tab):
                    $tabbed_id = strtolower(preg_replace("#[^A-Za-z0-9]#", "", $tab['tab_heading']));
            ?>
                <li><a href="#" data-id="<?php echo $tabbed_id; ?>_tabbed" title="<?php echo $tab['tab_heading']; ?>"><?php echo $tab['tab_heading']; ?></a></li>
            <?php endforeach; ?>
        </ul>

        <?php
            foreach($tabs as $tab):
                $tabbed_id = strtolower(preg_replace("#[^A-Za-z0-9]#", "", $tab['tab_heading']));
        ?>
			<h4 class="toggle-mobile">
				<a href="#" data-id="<?php echo $tabbed_id; ?>_tabbed" title="<?php echo $tab['tab_heading']; ?>">
					<?php echo $tab['tab_heading']; ?>
					<i class="fa-light fa-chevron-down"></i>
				</a>
			</h4>
            <div class="tab__content <?php echo $tabbed_id; ?>_tabbed">
                <?php echo $tab['tab_content']; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>