<?php
/**
 * Renders the public footer HTML
 *
 * @since 1.0.0
 * @package uhleloX\public\include
 */

/**
 * Security: Do not access directly.
 */
if ( count( get_included_files() ) === 1 ) {
	echo 'Direct access not allowed';
	exit();
}
?>
			<div class="d-flex mb-0 m-auto">
				<a href="https://www.uhlelox.com" class="me-1">uhleloX</a>&copy; 2011. All rights reserved.
			</div>
		</div>
		<?php
		$x_hooks = new X_Hooks();
		$x_hooks->do_action( 'x_footer' );
		?>
	</body>
</html>
