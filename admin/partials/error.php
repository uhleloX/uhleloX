<?php
/**
 * Simple error template.
 *
 * @since 1.0.0
 * @package uhleloX\admin\partials
 */

?>
<div class="row">
	<div class="alert alert-warning">
		<p class="m-0"><?php echo X_Sanitize::out_html( $this->results['error_message'] ); ?></p>
	</div>
</div>
