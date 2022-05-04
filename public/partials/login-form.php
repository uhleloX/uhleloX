<?php
/**
 * The Login form
 *
 * @since 1.0.0
 * @package uhleloX\public\partials
 */

?>

<?php include PUBLIC_PATH . '/include/header.php'; ?>

<form action="admin.php?x_action=login" method="post">

	<input type="hidden" name="login" value="true" />

	<?php
	$this->hooks->do_action('x_login_form_errors');
	$this->hooks->do_action('x_pre_login_form');
	?>

	<div class="input-group mb-3">
		<span class="input-group-text" id="usernameHint">Username</span>
		<input type="text" class="form-control" name="username" id="username" placeholder="username" aria-label="Username" aria-describedby="usernameHint" required autofocus>
	</div>

	<div class="input-group mb-3">
		<span class="input-group-text" id="passwordHint">Password</span>
		<input type="password" class="form-control" name="password" id="password" placeholder="password" aria-label="Password" aria-describedby="passwordHint" required>
	</div>

	<input type="hidden" name="x_token" value="<?php echo X_Functions::set_token( 'x_login', 'login' ); ?>">

	<div class="d-flex">
		<input type="submit" class="btn btn-dark w-100" name="login" value="Login"/>
	</div>

</form>

<?php include PUBLIC_PATH . '/include/footer.php'; ?>
