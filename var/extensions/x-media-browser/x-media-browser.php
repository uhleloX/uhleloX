<?php
/**
 * X Media Browser Extension.
 *
 * Extends the X CK Editors with a Browser to quickly select already uploaded media.
 * Like CKFinder, but much lighter and free.
 *
 * @see https://ckeditor.com/docs/ckeditor5/latest/api/adapter-ckfinder.html CKFinder Adapter for Media Browser.
 * @package uhleloX\var\extensions\mediabrowser
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

if ( $current_screen->is_request( 'edit' ) || $current_screen->is_request( 'add' ) ) {
	$functions->add_script( 'x-media-browser', $get->get_item_by( 'settings', 'uuid', 'x_site_url' )->value . '/var/extensions/x-media-browser/x-media-browser.js', array(), '', 'footer', 12 );
	$hooks->add_action( 'x_footer', 'add_media_browser' );
}

/**
 * Callback to add the media browser View.
 *
 * @since 1.0.0
 * @return void
 */
function add_media_browser() {
	?>
	<div class="modal fade" id="x_media_browser_modal" tabindex="-1" aria-labelledby="x_media_browser_modal_label" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
			<div class="modal-content">
				<div class="modal-header">
					<div>
						<h5 class="modal-title" id="x_media_browser_modal_label">Media Browser</h5>
						<small><em>Click on the image to insert.</em></small>
					</div>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="p-2" id="x_loading">  
					<div class="progress">
						<div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
					</div>
				</div>
				<div class="modal-body row row-cols-1 row-cols-sm-3 row-cols-lg-6 g-4" id="x_media_browser">
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="x_media_view_modal" tabindex="-1" aria-labelledby="x_media_view_modal_label" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body" id="x_media_view">
				</div>
			</div>
		</div>
	</div>
	<?php
}
