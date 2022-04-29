<?php
/**
 * Builds the View for all lists
 *
 * @since 1.0.0
 * @package uhleloX\admin\partials
 */

?>
<?php include ADMIN_PATH . '/include/header.php'; ?>
			<div class="x-admin-content">
				<main>
					<div class="container-fluid px-4">
						<h1 class="mt-4"><?php echo X_Sanitize::out_html( $this->results['title'] ); ?></h1>
						<ol class="breadcrumb mb-4">
							<li class="breadcrumb-item">Dashboard</li>
							<li class="breadcrumb-item active">Articles</li>
						</ol>
						<div id="filerobot-widget"></div>
						<div class="card mb-4">
							<div class="card-header">
								<span id="<?php echo X_Sanitize::out_html( $this->type ); ?>_table_desc">Manage and Search <?php echo X_Sanitize::out_html( $this->results['title'] ); ?></span>
							</div>
							<div class="card-body">
								<table id="datatablesSimple" aria-describedby="<?php echo X_Sanitize::out_html( $this->type ); ?>_table_desc">
									<thead>
										<tr>
											<?php
											foreach ( $this->columns as $key => $column ) {
												echo '<th scope="col">' . strtoupper( X_Sanitize::out_html( $column->Field ) ) . '</th>';
											}
											?>
											<th scope="col">Action</th>
										</tr>
									</thead>
									<tfoot>
										<tr>
											<?php
											foreach ( $this->columns as $key => $column ) {
												echo '<th scope="col">' . strtoupper( X_Sanitize::out_html( $column->Field ) ) . '</th>';
											}
											?>
											<th scope="col">Action</th>
										</tr>
									</tfoot>
									<tbody>
										<?php foreach ( $this->items as $item ) { ?>
											<tr>
												<?php
												foreach ( $this->columns as $key => $column ) {
													echo '<td class="x_table__td"><div class="x_table__td-content">' . X_Sanitize::out_html( $item->{$column->Field} ) . '</td>';
												}
												?>
												<td class="x_table x_table__td">
													<div class="x_table__td-actions d-flex justify-content-between align-items-center">
														<a href="/admin.php?action=edit&id=<?php echo intval( $item->id ); ?>&type=<?php echo X_Sanitize::out_html( $this->type ); ?>"><span class="bi bi-pencil-square"></span></a>
														<a href="/admin.php?action=delete&id=<?php echo intval( $item->id ); ?>&type=<?php echo X_Sanitize::out_html( $this->type ); ?>"><span class="bi bi-trash"></span></a>
													</div>
												</td>
											</tr>
										<?php } ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</main>

<?php include ADMIN_PATH . '/include/footer.php'; ?>
