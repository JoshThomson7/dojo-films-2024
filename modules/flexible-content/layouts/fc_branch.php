<?php
/**
 * FC Properties
 */

if(!defined( 'ABSPATH')) exit; // Exit if accessed directly

$branch_id = get_sub_field('branch_id');

if(!$branch_id) return;
if(!class_exists('APF_Property')) return;

include APF_PATH.'templates/branches/branch-item.php';
?>

