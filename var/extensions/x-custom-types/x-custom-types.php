<?php
/**
 * Register some new CPTs
 */

$custom_types  = array(
	'posts' => array(
		'id' => 'BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
		'owner' => 'BIGINT UNSIGNED NOT NULL',
		'uuid' => 'VARCHAR(255) NOT NULL',
		'title' => 'TEXT NOT NULL',
		'summary' => 'MEDIUMTEXT',
		'content' => 'LONGTEXT',
		'publicationdate' => 'DATETIME NOT NULL',
		'editdate' => 'DATETIME NOT NULL',
	),
);

function register_cpts( $custom_types ) {

	$db = new X_Post();
	foreach ( $custom_types as $table_name => $table_columns ) {

		$tables = '';

		foreach ( $table_columns as $key => $type ) {
			$tables .= $key . ' ' . $type . ', ';
		}
		$tables = rtrim( $tables, ', ' );

		$db->add_table( $table_name, $tables );

	}
	$db = null;

}

register_cpts( $custom_types );

$x_hooks = new X_Hooks();
$x_hooks->add_filter( 'x_public_tables', 'whitelist' );
function whitelist( $array ) {
	$array[]='posts';
	return $array;
}
$x_hooks->add_action( 'after_pages_menu', 'add_menu' );
function add_menu() {
	?>
	<a class="nav-link text-secondary collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#x_sidenav_collapse_posts" aria-expanded="false" aria-controls="x_sidenav_collapse_posts">
								<div class="x-admin__sidebar-nav-icon"><span class="bi bi-file-post"></span></div>
								Posts
								<div class="x-admin__sidebar-nav-collapse-arrow"><span class="bi bi-chevron-down"></span></div>
							</a>
							<div class="collapse" id="x_sidenav_collapse_posts" data-bs-parent="#x_sidenav_accordion">
								<nav class="x-admin-sidebar__nav-nested nav" aria-label="Posts">
									<a class="nav-link text-secondary" href="/admin.php?x_action=list&x_type=posts">All Posts</a>
									<a class="nav-link text-secondary" href="/admin.php?x_action=add&x_type=posts">Add Posts</a>
								</nav>
							</div>
	<?php
}

