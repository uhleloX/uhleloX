<?php
/**
 * X Codemirror Extension.
 *
 * This Extension enables Codemirror 4 on uhleloX Textareas.
 *
 * @package uhleloX\var\extensions\xcodemirror
 */

/**
 * Security: Do not access directly.
 */
if ( count( get_included_files() ) === 1 ) {
	echo 'Direct access not allowed';
	exit();
}

function codemirror_editor_init() {
	$hooks          = new X_Hooks();
	$functions      = new X_Functions();
	$post           = new X_Post();
	$get            = new X_Get();
	$current_screen = new X_Current_View();
	if ( $current_screen->is_request( 'edit' ) || $current_screen->is_request( 'add' ) ) {
		$functions->add_link( 'x-csslint', $get->get_item_by( 'settings', 'uuid', 'x_site_url' )->value . '/var/extensions/x-codemirror/css/x-codemirror.css', array(), '', 'stylesheet', 'head' );
		$functions->add_link( 'x-codemirror-highlight', $get->get_item_by( 'settings', 'uuid', 'x_site_url' )->value . '/var/extensions/x-codemirror/css/x-codemirror-highlight.css', array(), '', 'stylesheet', 'head' );
		$functions->add_script( 'x-csslint', $get->get_item_by( 'settings', 'uuid', 'x_site_url' )->value . '/var/extensions/x-codemirror/js/x-csslint.js', array(), '', 'footer', 11 );
		$functions->add_script( 'x-htmlhint', $get->get_item_by( 'settings', 'uuid', 'x_site_url' )->value . '/var/extensions/x-codemirror/js/x-htmlhint.js', array(), '', 'footer', 11 );
		$functions->add_script( 'x-jshint', $get->get_item_by( 'settings', 'uuid', 'x_site_url' )->value . '/var/extensions/x-codemirror/js/x-jshint.js', array(), '', 'footer', 11 );
		$functions->add_script( 'x-codemirror', $get->get_item_by( 'settings', 'uuid', 'x_site_url' )->value . '/var/extensions/x-codemirror/js/x-codemirror.js', array(), '', 'footer', 11 );
		$functions->add_script( 'x-codemirror-highlighting', $get->get_item_by( 'settings', 'uuid', 'x_site_url' )->value . '/var/extensions/x-codemirror/js/x-codemirror-highlighting.js', array(), '', 'footer', 11 );
	}
}
codemirror_editor_init();
