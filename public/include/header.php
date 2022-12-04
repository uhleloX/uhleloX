<?php
/**
 * Renders the public header HTML
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
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
		<title><?php echo X_Sanitize::out_html( $this->results['title'] ); ?></title>
		<!-- Favicon-->

		<?php
			$x_hooks = new X_Hooks();
			$x_hooks->do_action( 'x_head' );
		?>
	</head>
	<body class="vh-100 p-5">
		<section class="h-100">
			<div class="container d-flex align-items-center flex-column justify-content-center h-100">
				<img class="h-25" src="/public/img/logo.svg" alt="uhleloX Logo" />
				<h1 class="mb-5">uhleloX</h1>
