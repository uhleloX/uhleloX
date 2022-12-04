<?php
/**
 * Generates the Update View
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
<?php require ADMIN_PATH . '/include/header.php'; ?>

			<div class="x-admin-content">
				<main>
					<div class="container-fluid px-4">
						<h1 class="mt-4">Update</h1>
						<ol class="breadcrumb mb-4">
							<li class="breadcrumb-item active">Update uhleloX</li>
						</ol>
						<?php
							/**
							 * Add hooks prior to main Dashboard
							 */

						?>
						<div class="row row-cols-1 row-cols-md-3 g-3">
							<div class="col">
								<div class="card h-100 alert alert-success">
									<div class="card-body">
										<h5 class="card-title">Currently Installed Version</h5>
										<p class="card-text"><?php echo $x_update->get_current_version()['version']; ?></p>
									</div>
								</div>
							</div>
							<div class="col">
								<div class="card h-100 alert alert-warning">
									<div class="card-body">
										<h5 class="card-title">Update Available</h5>
										<p class="card-text">
											<?php

											if ( false !== $x_update->get_version_update() ) {
												?>
												 
												<p class="card-text">Newer version found : <?php echo $x_update->get_version_update()['version']; ?></p>
												<p class="card-text">Release Notes:<br>
													<?php echo $x_update->get_version_update()['releasenotes']; ?>
												</p>
												<button type="button" id="x_download_update" class="btn btn-warning" data-token="<?php echo X_Functions::set_token( 'x_add', 'add' ); ?>">
													<span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
												    Download Update
												</button>
												<button id="x_install_update" class="btn btn-warning" disabled>INstall Update</button>
												<?php
											} else {
												?>
												<p class="card-text">
													Current version is most up to date.
												</p>
												<?php
											}
											?>
										</p>
									</div>
								</div>
							</div>
							<div class="col">
								<div class="card h-100 alert alert-info">
									<div class="card-body">
										<h5 class="card-title">Roadmap</h5>
										<p class="card-text">
											<?php
											if ( false !== $x_update->get_version_update() ) {
												?>

												<p class="card-text">
													<?php echo $x_update->get_version_update()['roadmap']; ?>
												</p>

												<?php
											} else {
												?>
												<p class="card-text">
													No roadmap
												</p>
												<?php
											}
											?>
										</p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</main>

<?php require ADMIN_PATH . '/include/footer.php'; ?>
