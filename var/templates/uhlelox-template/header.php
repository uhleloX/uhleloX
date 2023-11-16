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
	<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
	<title><?php echo $this->x_item->title; ?></title>
	<!-- Favicons -->
    <!-- <link rel="icon" type="image/png" sizes="32x32" href="/path/to/favicon-32x32.png"> -->
    <!-- <link rel="icon" type="image/png" sizes="16x16" href="/path/to/favicon-16x16.png"> -->
	<link rel="stylesheet" type="text/css" href="/var/templates/uhlelox-template/css/bootstrap.min.css?3002">
	<link rel="stylesheet" type="text/css" href="/var/templates/uhlelox-template/style.css?6956">

	<meta name="keywords" content="">
	<meta name="description" content="">
	<link rel="canonical" href="uhlelox.com/list.php">
	<meta name="robots" content="index, follow">



<!-- Analytics -->
 
<!-- Analytics END -->

</head>
<body>
	<!-- Navigation -->
    <!-- <nav>
        <ul>
            <li><a href="#">Home</a></li>
            <li><a href="#">About</a></li>
            <li><a href="#">Services</a></li>
            <li><a href="#">Contact</a></li>
        </ul>
    </nav> -->
	<!-- Header -->
    <!-- <header>
        <h1>Welcome to My Website</h1>
        <p>This is a modern HTML5 website.</p>
    </header> -->
	<!-- Main content -->
    <main>
        <!-- <section>
            <h2>Section Title</h2>
            <p>Some content here.</p>
        </section> -->
    

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
							<img src="<?php echo $this->functions->get_site_url() . '/var/uploads/' . $media->uuid;?>" alt="Alt Value">
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
