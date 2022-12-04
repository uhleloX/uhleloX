<?php
/**
 * Simple error template.
 *
 * @since 1.0.0
 * @package uhleloX\admin\partials
 */

/**
 * Security: Do not access directly.
 */
if ( count( get_included_files() ) === 1 ) {
	echo 'Direct access not allowed';
	exit();
}
?>
<div class="row">
	<div class="alert alert-warning">
		<p class="m-0"><?php echo X_Sanitize::out_html( $this->results['error_message'] ); ?></p>
	</div>
</div>
