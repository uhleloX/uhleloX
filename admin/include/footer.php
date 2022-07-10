<?php
/**
 * Renders the footer HTML
 *
 * @since 1.0.0
 * @package uhleloX\admin\include
 */

?>
				<footer class="py-4 bg-light fixed-bottom">
					<div class="container-fluid px-4 d-flex align-items-center justify-content-between">
						<div class="d-flex align-items-center justify-content-between">
							<div class="small">Logged in as: <?php echo X_Sanitize::out_html( $_SESSION['x_username'] ); ?></div>
						</div>
						<div class="d-flex align-items-center justify-content-end">
							<div class="text-muted">Copyright &copy; <?php echo X_Sanitize::out_html( X_NAME );?> <?php echo date("Y");?></div>
						</div>
					</div>
				</footer>
			</div>
		</div>
		<?php
		$x_hooks = new X_Hooks();
		$x_hooks->do_action( 'x_footer' );
		?>
	</body>
</html>
