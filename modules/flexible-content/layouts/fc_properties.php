<?php
/**
 * FC Properties
 */

if(!defined( 'ABSPATH')) exit; // Exit if accessed directly

$properties = get_sub_field('properties');
if(empty($properties)) return;
$tab_count = count($properties);
?>
<div class="fc-properties">
	<div class="fc-properties-tabs" style="<?php echo $tab_count == 1 ? 'display: none;' : ''; ?>">
		<ul>
			<?php
				$tab_counter = 0;
				foreach($properties as $property):
					$property['pagination'] = 'false';
					$json_data = json_encode($property);
					$active = ($tab_counter == 0) ? ' class="active"' : '';
			?>
				<li>
					<a href="#" data-json="<?php echo htmlspecialchars($json_data); ?>"<?php echo $active; ?>>
						<?php echo $property['tab_name']; ?>
					</a>
				</li>
			<?php $tab_counter++; endforeach; ?>
		</ul>
	</div>

	<div class="apf__properties fc-properties-items"></div>
</div>
