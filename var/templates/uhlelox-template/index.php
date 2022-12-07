<?php
/**
 * The main template
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
require TEMPLATE_PATH . '/' . $this->template->value . '/header.php';
?>

<!-- bloc-1 -->
<div class="bloc bg-br-edge bloc-bg-texture texture-paper l-bloc" id="bloc-1">
	<div class="container bloc-xl">
		<div class="row">
			<?php echo $this->get->get_item_by( 'pages', 'id', $this->x_home_id )->content; ?>
		</div>
	</div>
</div>
<!-- bloc-1 END -->

<?php include TEMPLATE_PATH . '/' . $this->template->value . '/footer.php';?>
