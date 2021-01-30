<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @since              1.0.0
 * @package           RoleDisplay
 */

// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}
 

$meta_type  = 'user';
$object_id    = 0; 
$meta_key   = 'roledisplay_notice_dismissed';
$meta_value = ''; 
$delete_all = true;

//delete the 'roledisplay_notice_dismissed' meta key from the database
delete_metadata( $meta_type, $object_id, $meta_key, $meta_value, $delete_all );