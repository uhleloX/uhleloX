<?php
/**
 * The main uhleloX Template Header.
 *
 * This can be as plain HTML or with dynamic PHP Data as you like.
 *
 * @since 1.0.0
 * @package uhleloX\var\templates
 */

/**
 * Security: Do not access directly.
 */
if ( count( get_included_files() ) === 1 ) {
	echo 'Direct access not allowed';
	exit();
}
?>

<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="keywords" content="">
	<meta name="description" content="">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
	<link rel="canonical" href="uhlelox.com/list.php">
	<meta name="robots" content="index, follow">
	<link rel="shortcut icon" type="image/png" href="favicon.png">
	
	<link rel="stylesheet" type="text/css" href="/var/templates/uhlelox-template/css/bootstrap.min.css?3002">
	<link rel="stylesheet" type="text/css" href="/var/templates/uhlelox-template/style.css?6956">
	
	<title>Single-1</title>



<!-- Analytics -->
 
<!-- Analytics END -->

</head>
<body>

<!-- Preloader -->
<div id="page-loading-blocs-notifaction" class="page-preloader"></div>
<!-- Preloader END -->


<!-- Main container -->
<div class="page-container">

<!-- bloc-0 -->
<div class="bloc l-bloc bloc-bg-texture texture-paper" id="bloc-0">
	<div class="container bloc-sm">
		<div class="row">
			<div class="col">
				<nav class="navbar navbar-light row navbar-expand-md" role="navigation">
					<div class="container-fluid ">
						<a class="navbar-brand" href="/">
						<?php
						$logo  = $this->get->get_item_by( 'settings', 'uuid', 'x_logo_id' );
						$media = $this->get->get_item_by( 'media', 'id', $logo->value );
						if ( false !== $media ) {
							?>
							<img src="<?php echo $this->functions->get_site_url() . '/var/uploads/' . $media->uuid;?>">
							<?php
						}
						?>
					</a>
						<button id="nav-toggle" type="button" class="ui-navbar-toggler navbar-toggler border-0 p-0 ms-auto" aria-expanded="false" aria-label="Toggle navigation" data-bs-toggle="collapse" data-bs-target=".navbar-27890">
							<span class="navbar-toggler-icon"></span>
						</button>
						<div class="collapse navbar-collapse navbar-27890">
							<ul class="site-navigation nav navbar-nav ms-auto">
								<li class="nav-item">
									<a href="https://uhlelo.mamp/" class="nav-link">Home</a>
								</li>
								<li class="nav-item">
									<a href="https://uhlelo.mamp/pages/1/" class="a-btn nav-link">Home-1</a>
								</li>
								<li class="nav-item">
									<a href="https://uhlelo.mamp/pages/" class="a-btn nav-link">Single-1</a>
								</li>
							</ul>
						</div>
					</div>
				</nav>
			</div>
		</div>
	</div>
</div>
<!-- bloc-0 END -->
