<?php
/**
 * The Create User form
 *
 * @since 1.0.0
 * @package uhleloX\public\partials
 */

?>

<?php include PUBLIC_PATH . '/include/header.php'; ?>

<form action="/index.php?x_action=create_account" method="post">
	<?php if ( isset( $this->results['error_message'] ) && ! empty( $this->results['error_message'] ) ) { ?>
		<div class="alert alert-warning" role="alert">
			<?php echo X_Sanitize::out_html( $this->results['error_message'] ); ?>
		</div>
	<?php } ?>

	<div class="input-group mb-3">
		<span class="input-group-text" id="usernameHint">Username</span>
		<input type="text" class="form-control" name="username" id="username" placeholder="username" aria-label="Username" aria-describedby="usernameHint" required autofocus>
	</div>

	<div class="input-group mb-3">
		<span class="input-group-text" id="passwordHint">Password</span>
		<input type="password" class="form-control" name="password" id="password" placeholder="password" aria-label="Password" aria-describedby="passwordHint" required>
	</div>

	<div class="input-group mb-3">
		<span class="input-group-text" id="firstnameHint">First Name</span>
		<input type="text" class="form-control" name="firstname" id="firstname" placeholder="Jon/Jane Doe" aria-label="First Name" aria-describedby="firstnameHint" required>
	</div>

	<div class="input-group mb-3">
		<span class="input-group-text" id="lastnameHint">Last Name</span>
		<input type="lastname" class="form-control" name="lastname" id="lastname" placeholder="Last Name" aria-label="Last Name" aria-describedby="lastnameHint" required>
	</div>

	<div class="input-group mb-3">
		<span class="input-group-text" id="emailHint">Email</span>
		<input type="email" class="form-control" name="email" id="email" placeholder="Email" aria-label="Email" aria-describedby="emailHint" required>
	</div>

	<div class="input-group mb-3">
		<span class="input-group-text" id="keyPhrase">Key Phrase</span>
		<input type="text" class="form-control" name="key_phrase" id="key_phrase" placeholder="A lazy fox got eaten by a sturdy wolf" aria-label="Key Phrase" aria-describedby="keyPhrase" required>
	</div>

	<input type="hidden" name="x_token" value="<?php echo X_Functions::set_token( '_x_newuser', 'newuser' ); ?>">

	<div class="d-flex">
		<input type="submit" class="btn btn-dark w-100" name="setup" value="Create User"/>
	</div>

</form>

<?php include PUBLIC_PATH . '/include/footer.php'; ?>
