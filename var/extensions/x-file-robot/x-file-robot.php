<?php
/**
 * X File Robot Extension
 *
 * This extension enables FileRobot on Media Edit screens.
 *
 * @see (add doc)
 * @package uhleloX\var\extensions\x-file-robot
 */

$hooks = new X_Hooks();
$functions = new X_Functions();
$post = new X_Post();
$get = new X_Get();
$current_screen = new X_Current_View();
if ( ( $current_screen->is_request( 'edit' ) || $current_screen->is_request( 'add' ) ) && $current_screen->is_request( 'media' ) ) {
	$functions->add_script( 'file-robot', $get->get_item_by( 'settings', 'slug', 'x_site_url' )->value . 'var/extensions/x-file-robot/filerobot.min.js', array(), '', 'footer', 11 );
	$functions->add_script( 'x-filerobot', $get->get_item_by( 'settings', 'slug', 'x_site_url' )->value . 'var/extensions/x-file-robot/filerobot.js', array(), '', 'footer', 11 );
}
