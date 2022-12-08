<?php
/**
 * Generates the Dashboard View
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
<?php require_once ADMIN_PATH . '/include/header.php'; ?>

			<div class="x-admin-content">
				<main>
					<div class="container-fluid px-4">
						<h1 class="mt-4">Dashboard</h1>
						<ol class="breadcrumb mb-4">
							<li class="breadcrumb-item active">Dashboard</li>
						</ol>
						<?php
							/**
							 * Add hooks prior to main Dashboard
							 */
							$this->hooks->do_action( 'x_dashboard_errors' );
							$this->hooks->do_action( 'x_pre_dashboard' );
						?>
						<div class="row row-cols-1 row-cols-md-3 g-3">
							<?php $this->hooks->do_action( 'x_start_dashboard' ); ?>
							<div class="col">
								<div class="card h-100 alert alert-success">
									<div class="card-body">
										<h5 class="card-title"><a class="stretched-link" href="https://www.uhlelox.com/documentation" target="_blank" rel="noopener">Documentation</a></h5>
										<p class="card-text">Access all uhleloX Documentation in one place.</p>
									</div>
								</div>
							</div>
							<div class="col">
								<div class="card h-100 alert alert-info">
									<div class="card-body">
										<h5 class="card-title">Quick Links</h5>
										<p class="card-text">
											<ul>
												<li><a href="/admin.php?x_action=add&x_type=pages">New Page</a></li>
												<li><a href="/admin.php?x_action=add&x_type=users">New User</a></li>
												<li><a href="/admin.php?x_action=add&x_type=settings">New Setting</a></li>
												<li><a href="/admin.php?x_action=update">Check for Updates</a></li>
											</ul>
										</p>
									</div>
								</div>
							</div>
							<div class="col">
								<div class="card h-100">
									<div class="card-body">
										<h5 class="card-title">System Information</h5>
										<p class="card-text">
											<ul>
												<?php
												/**
												 * We should get Details more dynamically.
												 *
												 * @todo Get this from the database/API instead
												 */
												$setup   = new X_Setup();
												$props   = (array) $setup;
												$garbage = array_shift( $props );
												foreach ( $props as $key => $value ) {
													echo '<li>' . X_Sanitize::out_html( $key ) . ' ' . X_Sanitize::out_html( $value ) . '</li>';
												}
												?>
											</ul>
										</p>
									</div>
								</div>
							</div>
							<?php $this->hooks->do_action( 'x_end_dashboard' ); ?>
						</div>
						<?php $this->hooks->do_action( 'x_after_dashboard' ); ?>
					</div>
				</main>

<?php require_once ADMIN_PATH . '/include/footer.php'; ?>
