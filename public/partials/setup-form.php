<?php
/**
 * The Setup Data form
 *
 * @since 1.0.0
 * @package uhleloX\public\partials
 */

/**
 * Security: Do not access directly.
 */
if ( count( get_included_files() ) === 1 ) {
	echo 'Direct access not allowed';
	exit();
}
?>

<?php
/**
 * At this point config file does not exist yet.
 * Thus, constants are undefined (and install folders should not be modified).
 */
require_once dirname( __DIR__, 1 ) . '/include/header.php';
?>

<form action="/index.php?x_action=setup" method="post">
	<?php if ( isset( $this->results['error_message'] ) && ! empty( $this->results['error_message'] ) ) { ?>
		<div class="alert alert-warning" role="alert">
			<?php echo X_Sanitize::out_html( $this->results['error_message'] ); ?>
		</div>
	<?php } ?>
	<div class="mb-3 input-group">
		<label for="timezone" class="input-group-text">Timezone</label>
		<select name="timezone" id="timezone"  required class="form-select" data-placeholder="Choose a Timezone">
			<option></option>
			<?php
			foreach ( X_Functions::timezones_select() as $region => $list ) {

				echo '<optgroup label="' . $region . '">' . "\n";

				foreach ( $list as $timezone => $name ) {
					echo '<option value="' . $timezone . '">' . $name . '</option>' . "\n";
				}

				echo '<optgroup>' . "\n";

			}
			?>
		</select>

	</div>

	<div class="input-group mb-3">
		<span class="input-group-text" id="hostHint">Host</span>
		<input type="text" class="form-control" name="host" id="host" placeholder="localhost" aria-label="Host" aria-describedby="hostHint" required>
	</div>

	<div class="input-group mb-3">
		<span class="input-group-text" id="databaseHint">Database Name</span>
		<input type="text" class="form-control" name="db" id="db" placeholder="database_name" aria-label="Database" aria-describedby="databaseHint" required>
	</div>

	<div class="input-group mb-3">
		<span class="input-group-text" id="databaseuserHint">Database User</span>
		<input type="text" class="form-control" name="db_usr" id="db_usr" placeholder="database_user" aria-label="Database User" aria-describedby="databaseuserHint" required>
	</div>

	<div class="input-group mb-3">
		<span class="input-group-text" id="databasepwdHint">Database Password</span>
		<input type="password" class="form-control" name="db_pwd" id="db_pwd" placeholder="Database Password" aria-label="Database Password" aria-describedby="databasepwdHint" required>
	</div>

	<div class="input-group mb-3">
		<span class="input-group-text" id="databasechrstHint">Database Charset</span>
		<input type="text" class="form-control" name="db_charset" id="db_charset" placeholder="utf8mb4" default="utf8mb4" aria-label="Database Charset" aria-describedby="databasechrstHint" required>
	</div>

	<div class="input-group mb-3">
		<span class="input-group-text" id="databaseportHint">Database Port</span>
		<input type="text" class="form-control" name="db_port" id="db_port" placeholder="3306" aria-label="Database Port" aria-describedby="databaseportHint" required>
	</div>

	<div class="input-group mb-3">
		<span class="input-group-text" id="keyPhrase">Key Phrase</span>
		<input type="text" class="form-control" name="key_phrase" id="key_phrase" placeholder="A lazy fox got eaten by a sturdy wolf" aria-label="Key Phrase" aria-describedby="keyPhrase" required>
	</div>

	<input type="hidden" name="x_token" value="<?php echo X_Functions::set_token( '_x_setup', 'setup' ); ?>">

	<div class="d-flex">
		<input type="submit" class="btn btn-dark w-100" name="setup" value="Setup"/>
	</div>

</form>

<?php
/**
 * At this point config file does not exist yet.
 * Thus, constants are undefined (and install folders should not be modified).
 */
require_once dirname( __DIR__, 1 ) . '/include/footer.php';
