<?php
/**
 * FC Global
 */

$fc_id = get_sub_field('global_content');
FC_Helpers::flexible_content($fc_id);
$fc_id = null;