<?php
/**
 * The single template
 *
 * @package uhleloX\var\templates
 */

/**
 * Include header
 */
require TEMPLATE_PATH . '/' . $this->template->value . '/header.php';
?>

<!-- bloc-2 -->
<div class="bloc l-bloc bloc-bg-texture texture-paper ck-content" id="bloc-2">
	<div class="container bloc-lg bloc-xxl-lg">
		<div class="row">
			<div class="col-sm-4 col">
				<div><div data-tilt="" class="img-hover-tilt-container"   data-tilt-glare="true" data-tilt-maxglare=".5"><img class="hover-tilt-img-item img-fluid lazyload" src="img/lazyload-ph.png" data-src="/var/templates/uhlelox-template/img/placeholder-image.png" alt="placeholder image"><h3 class="hover-tilt-label"><?php echo $this->x_item->title;?></h3></div>
				</div>
			</div>
			<div class="col-sm-8 col">
					<h1 class="mg-md">
						<?php echo $this->x_item->title;?>
					</h1>
					<p>
						<?php echo $this->x_item->content;?>
					</p>
				</div>
		</div>
	</div>
</div>
<!-- bloc-2 END -->

<!-- ScrollToTop Button -->
<a class="bloc-button btn btn-d scrollToTop" onclick="scrollToTarget('1',this)"><svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 32 32"><path class="scroll-to-top-btn-icon" d="M30,22.656l-14-13-14,13"/></svg></a>
<!-- ScrollToTop Button END-->

<?php include TEMPLATE_PATH . '/' . $this->template->value . '/footer.php';?>
