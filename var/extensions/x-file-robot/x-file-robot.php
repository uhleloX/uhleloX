<?php
/**
 * X File Robot Extension
 *
 * This extension enables FileRobot on Media Edit screens.
 *
 * @see https://github.com/scaleflex/filerobot-image-editor GitHub Repository of FileRobot.
 * @package uhleloX\var\extensions\filerobot
 */

/**
 * Security: Do not access directly.
 */
if ( count( get_included_files() ) === 1 ) {
	echo 'Direct access not allowed';
	exit();
}

$hooks = new X_Hooks();
$functions = new X_Functions();
$post = new X_Post();
$get = new X_Get();
$current_screen = new X_Current_View();

if ( $current_screen->is_request( 'edit' ) && $current_screen->is_request( 'media' ) ) {

	$functions->add_link( 'file-robot', $get->get_item_by( 'settings', 'uuid', 'x_site_url' )->value . '/var/extensions/x-file-robot/filerobot.css', array(), '', 'stylesheet', 'head' );
	$functions->add_script( 'file-robot', $get->get_item_by( 'settings', 'uuid', 'x_site_url' )->value . '/var/extensions/x-file-robot/filerobot.min.js', array(), '', 'footer', 11 );
	$functions->add_script( 'x-filerobot', $get->get_item_by( 'settings', 'uuid', 'x_site_url' )->value . '/var/extensions/x-file-robot/filerobot.js', array(), '', 'footer', 11 );

	$hooks->add_action( 'x_media_screen_media_editor_area', 'add_filerobot_media_editor' );
	$hooks->add_action( 'x_footer', 'add_filerobot_modal' );

}

/**
 * Add Media File Editor Button and image.
 *
 * @param obj $media_object The Current edited media object.
 */
function add_filerobot_media_editor( $media_object ) {

	$functions = new X_Functions();

	?>
	<div class="draggable" id="media_item_container">
		<div class="mb-3 position-relative" id="media_item_container_group">
			<span class="position-absolute top-0 left-0 m-1 x_drag_handle x_drag_handle-editor" id="media_item_containerHint"></span>
			<div  class="trigger_hover">
			  <img src="<?php echo $functions->get_site_url() . '/var/uploads/' . $media_object->uuid; ?>" alt="<?php echo $media_object->title; ?>" id="x_media_item_still" class="img-fluid">
			  <span class="btn btn-lg btn-success position-absolute top-50 start-50 translate-middle show_on_hover" data-bs-toggle="modal" data-bs-target="#x_filerobot_modal">Edit</span>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Add Filerobot Modal
 */
function add_filerobot_modal() {
	?>
	<div class="modal fade" id="x_filerobot_modal" tabindex="-1" aria-labelledby="x_filerobot_modalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
	  <div class="modal-dialog modal-dialog-centered modal-xl">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		  </div>
		  <div class="modal-body">
			<div id="media_item_containerEditor">
			</div>
		  </div>
		</div>
	  </div>
	</div>
	<?php
}
