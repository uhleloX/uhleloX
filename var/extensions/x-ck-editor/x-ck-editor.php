<?php
/**
 * X CKEditor Extension.
 *
 * This Extension enables CKEditor on uhleloX Textareas.
 *
 * @see (add doc)
 * @package uhleloX\var\extensions\x-ck-editor
 */

$hooks = new X_Hooks();
$functions = new X_Functions();
$post = new X_Post();
$get = new X_Get();
$current_screen = new X_Current_View();

if ( $current_screen->is_request( 'edit' ) || $current_screen->is_request( 'add' ) ) {
	$functions->add_script( 'ck-editor', $get->get_item_by( 'settings', 'slug', 'x_site_url' )->value . 'var/extensions/x-ck-editor/ckeditor/ckeditor.js', array(), '', 'footer', 11 );
	$functions->add_script( 'x-ck-editor', $get->get_item_by( 'settings', 'slug', 'x_site_url' )->value . 'var/extensions/x-ck-editor/ck-editor.js', array(), '', 'footer', 13 );
}
