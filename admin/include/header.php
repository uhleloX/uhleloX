<?php
/**
 * Renders the header HTML
 *
 * @since 1.0.0
 * @package uhleloX\admin\include
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
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
		<title>
			<?php
			echo X_Sanitize::out_html( X_NAME );
			echo ' | ';
			echo X_Sanitize::out_html( $this->results['title'] );
			?>
		</title>
		<?php
		$x_hooks = new X_Hooks();
		$x_hooks->do_action( 'x_head' );
		?>
	</head>
	<body class="x-body x-body_nav-fixed">
		<nav class="x-body__top-nav navbar navbar-expand navbar-dark bg-dark fixed-top" aria-label="Admin Top Menu">
			<!-- Navbar Brand-->
			<a class="navbar-brand ps-3" href="/"><?php echo X_Sanitize::out_html( X_NAME ); ?> Admin</a>
			<!-- Sidebar Toggle-->
			<button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="x_sidebar_toggle" href="#!"><span class="bi bi-chevron-left"></span></button>
			<ul class="navbar-nav ms-auto  me-3 me-lg-4">
				<li class="nav-item dropdown">
					<a class="nav-link text-secondary dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><span class="bi bi-person-circle"></span></a>
					<ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
						<?php
						$x_get = new X_Get();
						$x_user = $x_get->get_item_by( 'users', 'uuid', $_SESSION['x_user_uuid'] );
						?>
						<li><a class="dropdown-item" href="admin.php?x_action=edit&x_type=users&id=<?php echo intval( $x_user->id ); ?>">Edit User</a></li>
						<li><a class="dropdown-item" href="#!">Activity Log (@todo)</a></li>
						<li><hr class="dropdown-divider" /></li>
						<li><a class="dropdown-item" href="admin.php?x_action=logout">Log out</a></li>
					</ul>
				</li>
			</ul>
		</nav>
		<div class="x-admin">
			<div class="x-admin__sidebar-nav-wrap">
				<nav class="x-admin__sidebar-nav accordion" id="x_sidenav_accordion" aria-label="Admin Sidebar Menu">
					<div class="x-admin__sidebar-nav-menu">
						<div class="x-admin__sidebar-nav-items">
							<div class="x-admin__sidebar-nav-heading">Core</div>
							<a class="nav-link text-secondary" href="admin.php">
								<div class="x-admin__sidebar-nav-icon"><span class="bi bi-speedometer2"></span></div>
								Dashboard
							</a>
							<a class="nav-link text-secondary collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#x_sidenav_collapse_settings" aria-expanded="false" aria-controls="x_sidenav_collapse_settings">
								<div class="x-admin__sidebar-nav-icon"><span class="bi bi-sliders2-vertical"></span></div>
								Settings
								<div class="x-admin__sidebar-nav-collapse-arrow"><span class="bi bi-chevron-down"></span></div>
							</a>
							<div class="collapse" id="x_sidenav_collapse_settings" data-bs-parent="#x_sidenav_accordion">
								<nav class="x-admin-sidebar__nav-nested nav" aria-label="Settings">
									<a class="nav-link text-secondary" href="/admin.php?x_action=list&x_type=settings">All Settings</a>
									<a class="nav-link text-secondary" href="/admin.php?x_action=add&x_type=settings">Add Setting</a>
								</nav>
							</div>
							<a class="nav-link text-secondary collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#x_sidenav_collapse_extensions" aria-expanded="false" aria-controls="x_sidenav_collapse_extensions">
								<div class="x-admin__sidebar-nav-icon"><span class="bi bi-sliders2-vertical"></span></div>
								Extensions
								<div class="x-admin__sidebar-nav-collapse-arrow"><span class="bi bi-chevron-down"></span></div>
							</a>
							<div class="collapse" id="x_sidenav_collapse_extensions" data-bs-parent="#x_sidenav_accordion">
								<nav class="x-admin-sidebar__nav-nested nav" aria-label="Extensions">
									<a class="nav-link text-secondary" href="/admin.php?x_action=list&x_type=extensions">All Extensions</a>
									<a class="nav-link text-secondary" href="/admin.php?x_action=add&x_type=extensions">Add Extension</a>
								</nav>
							</div>
							<a class="nav-link text-secondary collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#x_sidenav_collapse_templates" aria-expanded="false" aria-controls="x_sidenav_collapse_templates">
								<div class="x-admin__sidebar-nav-icon"><span class="bi bi-sliders2-vertical"></span></div>
								Templates
								<div class="x-admin__sidebar-nav-collapse-arrow"><span class="bi bi-chevron-down"></span></div>
							</a>
							<div class="collapse" id="x_sidenav_collapse_templates" data-bs-parent="#x_sidenav_accordion">
								<nav class="x-admin-sidebar__nav-nested nav" aria-label="Templates">
									<a class="nav-link text-secondary" href="/admin.php?x_action=list&x_type=templates">All Templates</a>
									<a class="nav-link text-secondary" href="/admin.php?x_action=add&x_type=templates">Add Template</a>
								</nav>
							</div>

							<div class="x-admin__sidebar-nav-heading">Media Management</div>
							<a class="nav-link text-secondary collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#x_sidenav_collapse_media" aria-expanded="false" aria-controls="x_sidenav_collapse_media">
								<div class="x-admin__sidebar-nav-icon"><span class="bi bi-disc-fill"></span></div>
								Media
								<div class="x-admin__sidebar-nav-collapse-arrow"><span class="bi bi-chevron-down"></span></div>
							</a>
							<div class="collapse" id="x_sidenav_collapse_media" data-bs-parent="#x_sidenav_accordion">
								<nav class="x-admin-sidebar__nav-nested nav" aria-label="Media">
									<a class="nav-link text-secondary" href="/admin.php?x_action=list&x_type=media">All Media</a>
									<a class="nav-link text-secondary" href="/admin.php?x_action=add&x_type=media">Add Media</a>
								</nav>
							</div>

							<div class="x-admin__sidebar-nav-heading">Content Management</div>
							<a class="nav-link text-secondary collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#x_sidenav_collapse_pages" aria-expanded="false" aria-controls="x_sidenav_collapse_pages">
								<div class="x-admin__sidebar-nav-icon"><span class="bi bi-file-post"></span></div>
								Pages
								<div class="x-admin__sidebar-nav-collapse-arrow"><span class="bi bi-chevron-down"></span></div>
							</a>
							<div class="collapse" id="x_sidenav_collapse_pages" data-bs-parent="#x_sidenav_accordion">
								<nav class="x-admin-sidebar__nav-nested nav" aria-label="Pages">
									<a class="nav-link text-secondary" href="/admin.php?x_action=list&x_type=pages">All Pages</a>
									<a class="nav-link text-secondary" href="/admin.php?x_action=add&x_type=pages">Add Page</a>
								</nav>
							</div>
							<?php $x_hooks->do_action( 'after_pages_menu' ); ?>
							<a class="nav-link text-secondary collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#x_sidenav_collapse_relationships" aria-expanded="false" aria-controls="x_sidenav_collapse_relationships">
								<div class="x-admin__sidebar-nav-icon"><span class="bi bi-file-post"></span></div>
								Relationships
								<div class="x-admin__sidebar-nav-collapse-arrow"><span class="bi bi-chevron-down"></span></div>
							</a>
							<div class="collapse" id="x_sidenav_collapse_relationships" data-bs-parent="#x_sidenav_accordion">
								<nav class="x-admin-sidebar__nav-nested nav" aria-label="Relationships">
									<a class="nav-link text-secondary" href="/admin.php?x_action=list&x_type=relationships">All Relationships</a>
									<a class="nav-link text-secondary" href="/admin.php?x_action=add&x_type=relationships">Add Relationship</a>
								</nav>
							</div>
							<a class="nav-link text-secondary collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#x_sidenav_collapse_languages" aria-expanded="false" aria-controls="x_sidenav_collapse_languages">
								<div class="x-admin__sidebar-nav-icon"><span class="bi bi-file-post"></span></div>
								Languages
								<div class="x-admin__sidebar-nav-collapse-arrow"><span class="bi bi-chevron-down"></span></div>
							</a>
							<div class="collapse" id="x_sidenav_collapse_languages" data-bs-parent="#x_sidenav_accordion">
								<nav class="x-admin-sidebar__nav-nested nav" aria-label="Languages">
									<a class="nav-link text-secondary" href="/admin.php?x_action=list&x_type=languages">All Languages</a>
									<a class="nav-link text-secondary" href="/admin.php?x_action=add&x_type=languages">Add Language</a>
								</nav>
							</div>
							<div class="x-admin__sidebar-nav-heading">User Management</div>
							<a class="nav-link text-secondary collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#x_sidenav_collapse_users" aria-expanded="false" aria-controls="x_sidenav_collapse_users">
								<div class="x-admin__sidebar-nav-icon"><span class="bi bi-file-post"></span></div>
								Users
								<div class="x-admin__sidebar-nav-collapse-arrow"><span class="bi bi-chevron-down"></span></div>
							</a>
							<div class="collapse" id="x_sidenav_collapse_users" data-bs-parent="#x_sidenav_accordion">
								<nav class="x-admin-sidebar__nav-nested nav" aria-label="Users">
									<a class="nav-link text-secondary" href="/admin.php?x_action=list&x_type=users">All Users</a>
									<a class="nav-link text-secondary" href="/admin.php?x_action=add&x_type=users">Add User</a>
								</nav>
							</div>
							<a class="nav-link text-secondary collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#x_sidenav_collapse_roles" aria-expanded="false" aria-controls="x_sidenav_collapse_roles">
								<div class="x-admin__sidebar-nav-icon"><span class="bi bi-file-post"></span></div>
								Roles
								<div class="x-admin__sidebar-nav-collapse-arrow"><span class="bi bi-chevron-down"></span></div>
							</a>
							<div class="collapse" id="x_sidenav_collapse_roles" data-bs-parent="#x_sidenav_accordion">
								<nav class="x-admin-sidebar__nav-nested nav" aria-label="Roles">
									<a class="nav-link text-secondary" href="/admin.php?x_action=list&x_type=roles">All Roles</a>
									<a class="nav-link text-secondary" href="/admin.php?x_action=add&x_type=roles">Add Role</a>
								</nav>
							</div>
						</div>
					</div>
				</nav>
			</div>
