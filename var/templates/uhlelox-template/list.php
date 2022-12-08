<?php
/**
 * The lists template
 *
 * @package uhleloX\var\templates
 */

/**
 * Security: Do not access directly.
 */
if ( count( get_included_files() ) === 1 ) {
	echo 'Direct access not allowed';
	exit();
}
/**
 * Include header
 */
require_once TEMPLATE_PATH . '/' . $this->template->value . '/header.php';
?>
<!-- bloc-3 -->
<div class="bloc l-bloc bloc-bg-texture texture-paper" id="bloc-3">
	<div class="container bloc-lg bloc-xxl-lg ">
		<div class="row">
				<?php
					foreach ( $this->x_list as $item ) {
						$link = $functions->get_url( $this->request['archive'], $item->id );
						?>
						<div class="mt-3 col-lg-3 col-md-6 mt-md-0">
							<div class="card">
								<img src="https://uhlelo.mamp/var/templates/uhlelox-template/img/lazyload-ph.png" data-src="https://uhlelo.mamp/var/templates/uhlelox-template/img/placeholder-image.png" class="img-fluid mx-auto d-block lazyload" alt="placeholder image" />
								<div class="card-body">
									<p>
										<?php echo $item->title;?>
									</p><a href="<?php echo $link;?>" class="btn btn-d w-100">More Info</a>
								</div>
							</div>
						</div>
						<?php
					}
				?>
		</div>
	</div>
</div>
<!-- bloc-3 END -->
<?php require_once TEMPLATE_PATH . '/' . $this->template->value . '/footer.php';?>
