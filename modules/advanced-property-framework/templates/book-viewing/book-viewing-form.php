<?php
if(is_singular('property')):

global $post;
?>
    <div class="apf__book__viewing__form view">
        <div class="max__width">
            <div class="apf__book__viewing__form__wrapper">
                <a href="#" class="apf__book__viewing close"><span class="fal fa-times"></span></a>

                <h2>Arrange a viewing</h2>
                <h3>You're arranging a viewing for:<strong><?php echo $property->get_name(); ?> - <?php echo $property->get_price_html(); ?></strong></h3>
                <?php echo do_shortcode('[gravityform id="25" title="false" description="false" ajax="true"]'); ?>
            </div><!-- apf__book__viewing__wrapper -->
        </div><!-- max__width -->
    </div><!-- apf__book__viewing -->

<?php endif; ?>
