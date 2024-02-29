<?php 
/**
 * APF Search Form
 *
 * @author  Various
 * @package Advanced Property Framework
 *
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$apf_settings = new APF_Settings();
$markets = $apf_settings->markets();
$departments = $apf_settings->departments();
?>
<div class="apf__search">

    <h4 class="apf__search--refine-heding">Refine your search <a href="#" class="apf__search--close"><i class="fal fa-times"></i></a></h4>

    <form action="<?php echo esc_url(home_url()); ?>/property-search/" id="apf_search">

        <div class="apf__search__main<?php if(APF_Helpers::is_apf()) { echo ' apf-search-hide-on-mobile'; } ?>">

			<div class="apf-field-group expand">
				<div class="apf-field-group">
					<?php if(count($markets) > 1): ?>
						<div class="apf__search__switch column apf-search-dept">
							<?php
								$dept_count = 0;
								foreach($markets as $market):
							?>
								<input type="radio" id="apf_<?php echo $market['value']; ?>" name="apf_market" value="<?php echo $market['value']; ?>" <?php echo $dept_count == 0 ? 'checked' : ''; ?> />
								<label for="apf_<?php echo $market['value']; ?>"><?php echo $market['label']; ?></label>
							<?php $dept_count++; endforeach; ?>
						</div><!-- search-buyrent -->
					<?php else: ?>
						<input type="hidden" name="apf_market" value="<?php echo $markets[0]['value']; ?>"/>
					<?php endif; ?>
					
					<?php if(count($departments) > 1): ?>
						<div class="apf__search__switch apf-search-type">
							<?php
								$department_count = 0;
								foreach($departments as $department):
							?>
								<input type="radio" id="apf_<?php echo $department['value']; ?>" name="apf_dept" value="<?php echo $department['value']; ?>" <?php echo $department_count == 0 ? 'checked' : ''; ?> />
								<label for="apf_<?php echo $department['value']; ?>"><?php echo $department['label']; ?></label>
							<?php $department_count++; endforeach; ?>
						</div><!-- search-buyrent -->
					<?php else: ?>
						<input type="hidden" name="apf_dept" value="<?php echo $departments[0]['value']; ?>"/>
					<?php endif; ?>
				</div>

				<div class="apf__location">
					<input type="text" name="apf_location" placeholder="Area, postcode, town or street" class="apf__area__search" value="<?php echo $apf_settings->area_default(); ?>" autocomplete="off" />
				</div>
			</div>
			
			<div class="apf-field-group">
				<div class="apf-field-group">
					<article class="apf-display apf-radius-display">
						<strong>Radius</strong>
						<span>
							<i class="separator fa-light fa-location-arrow"></i>
							<span class="to">This area only</span>	
						</span>

						<div class="apf-field-group apf-selects-pop apf-pop-radius">
							<div class="apf__select__wrap column">
								<label>Mile radius</label>
								<select name="apf_radius" id="apf_radius" class="apf__select apf__radius">
									<option value="<?php echo $apf_settings->default_radius(); ?>" selected>This area only</option>
									<option value="0.1">0 miles</option>
									<option value="0.25">¼ mile</option>
									<option value="0.5">½ mile</option>
									<option value="1">1 mile</option>
									<option value="2">2 miles</option>
									<option value="3">3 miles</option>
									<option value="5">5 miles</option>
									<option value="10">15 mile</option>
									<option value="20">20 mile</option>
									<option value="30">40 mile</option>
								</select>
							</div>
						</div>
					</article>
					
					<?php if(APF_Helpers::is_property_search()): ?>
						<article class="apf-display apf-price-display">
							<strong>Price</strong>
							<span>
								<i class="separator fa-light fa-tag"></i>
								<span class="from">No min</span>-
								<span class="to">No max</span>	
							</span>

							<div class="apf-field-group apf-selects-pop apf-pop-price">
								<div class="apf__select__wrap column">
									<label>Min price</label>
									<select name="apf_minprice" id="apf_minprice" class="apf__select apf__minprice"></select>
								</div>

								<div class="apf__select__wrap column">
									<label>Max price</label>
									<select name="apf_maxprice" id="apf_maxprice" class="apf__select apf__maxprice"></select>
								</div>
							</div>
						</article>

						<article class="apf-display apf-beds-display">
							<strong>Bedrooms</strong>
							<span>
								<i class="separator fa-light fa-bed-empty"></i>
								<span class="from">No min</span>-
								<span class="to">No max</span>
							</span>

							<?php if($apf_settings->search_beds_dropdown()): ?>
								<div class="apf-field-group apf-selects-pop apf-pop-beds">
									<div class="apf__select__wrap column">
										<label>Min beds</label>
										<select id="apf_minbeds" name="apf_minbeds" class="apf__select apf__minbeds">
											<option value="">Min beds</option>
											<option value="0">Studio</option>
											<option value="1">1</option>
											<option value="2">2</option>
											<option value="3">3</option>
											<option value="4">4</option>
											<option value="5">5</option>
											<option value="100">6+</option>
										</select>
									</div>

									<div class="apf__select__wrap column">
										<label>Max beds</label>
										<select id="apf_maxbeds" name="apf_maxbeds" class="apf__select apf__maxbeds">
											<option value="6+">Max beds</option>
											<option value="0">Studio</option>
											<option value="1">1</option>
											<option value="2">2</option>
											<option value="3">3</option>
											<option value="4">4</option>
											<option value="5">5</option>
											<option value="100">6+</option>
										</select>
									</div>
								</div>
							<?php endif; ?>
						</article>
					<?php else: ?>
						<input type="hidden" name="apf_minprice" value="" />
						<input type="hidden" name="apf_maxprice" value="" />
						<input type="hidden" name="apf_minbeds" value="0" />
						<input type="hidden" name="apf_maxbeds" value="100" />
					<?php endif; ?>
				</div>
				
				<div class="apf-field-group">
					<button type="submit" class="apf__search__button button primary large apf-fetch<?php if(!APF_Helpers::is_property_search()) { echo ' apf-json'; } ?>">
						<i class="fa-light fa-search"></i>
					</button>
				</div>
			</div>
        </div>

        <?php if(APF_Helpers::is_property_search()): ?>
            <?php require_once 'filter-form.php'; ?>
        <?php else: ?>
            <input type="hidden" name="apf_view" value="grid">
            <input type="hidden" name="apf_status" value="">
            <input type="hidden" name="apf_branch" value="">
            <input type="hidden" name="apf_order" value="price_desc">
        <?php endif; ?>
    </form>
</div><!-- apf__search -->
