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
			<ol class="breadcrumb mb-3">
				<li class="breadcrumb-item">Dashboard</li>
				<li class="breadcrumb-item active"><?php echo ucfirst( X_Sanitize::out_html( $this->type ) ); ?></li>
				<li class="breadcrumb-item active"><?php echo ucfirst( X_Sanitize::out_html( $this->action ) ); ?> <?php echo ucfirst( rtrim( X_Sanitize::out_html( $this->type ), 's' ) ); ?></li>
			</ol>
			<?php 
				/**
				 * Add Hooks prior to main form
				 */
				$this->hooks->do_action( 'x_edit_screen_errors' );
				$this->hooks->do_action( 'x_pre_edit_screen' );
			?>
			<form enctype="multipart/form-data" id="x_form_container" action="admin.php?x_action=<?php echo X_Sanitize::out_html( $this->results['action'] ); ?>&x_type=<?php echo X_Sanitize::out_html( $this->type ); ?>" method="post" name="x_form">
				<div class="row">
					<div class="col-sm-9 resizable" id="resizable_0">
						<?php
						if ( isset( $this->item ) && property_exists( $this->item, 'id' ) ) {
							?>
							<input type="hidden" name="id" value="<?php echo intval( $this->item->id ); ?>"/>
							<?php
						}

						if ( isset( $_SESSION['error_message'] )
							&& ! empty( $_SESSION['error_message'] )
						) {
							?>
							<div class="alert alert-warning" role="alert">
								<?php echo X_Sanitize::out_html( stripslashes( $_SESSION['error_message'] ) ); ?>
							</div>
							<?php
							unset( $_SESSION['error_message'] );
						}

						foreach ( $this->columns as $key => $column ) {

							$required = 'NO' === $column->Null ? 'required' : '';
							$field = X_Sanitize::out_html( $column->Field );
							$type = $this->get->get_item_by( 'settings', 'slug', 'x_field_type_' . $field );

							if ( empty( $type )
								|| false === $type
								|| ( 'img' !== $type->value
									&& 'owner' !== $type->value
								)
							) {

								switch ( $column->Type ) {

									case 'bigint(20) unsigned':
										if ( 'auto_increment' !== $column->Extra ) {
											?>
											<div class="draggable" id="<?php echo X_Sanitize::out_html( $column->Field ); ?>_container">
												<div class="input-group mb-3" id="<?php echo X_Sanitize::out_html( $field ); ?>_group">
													<span class="input-group-text x_drag_handle" id="<?php echo X_Sanitize::out_html( $field ); ?>Hint"><?php echo strtoupper( X_Sanitize::out_html( $field ) ); ?></span>
													<input type="number" class="form-control" name="<?php echo X_Sanitize::out_html( $field ); ?>" id="<?php echo X_Sanitize::out_html( $field ); ?>" placeholder="<?php echo strtoupper( X_Sanitize::out_html( $field ) ); ?>" aria-label="<?php echo strtoupper( X_Sanitize::out_html( $field ) ); ?>" aria-describedby="<?php echo X_Sanitize::out_html( $field ); ?>Hint" value="<?php echo X_Sanitize::out_html( $this->item->{$field} ); ?>" <?php echo X_Sanitize::out_html( $required ); ?>>
												</div>
											</div>
											<?php
										}
										break;
									case 'text':
										?>
										<div class="draggable" id="<?php echo X_Sanitize::out_html( $column->Field ); ?>_container">
											<div class="input-group mb-3" id="<?php echo X_Sanitize::out_html( $field ); ?>_group">
												<span class="input-group-text x_drag_handle" id="<?php echo X_Sanitize::out_html( $field ); ?>Hint"><?php echo strtoupper( X_Sanitize::out_html( $field ) ); ?></span>
												<input type="text" class="form-control" name="<?php echo X_Sanitize::out_html( $field ); ?>" id="<?php echo X_Sanitize::out_html( $field ); ?>" placeholder="<?php echo strtoupper( X_Sanitize::out_html( $field ) ); ?>" aria-label="<?php echo strtoupper( X_Sanitize::out_html( $field ) ); ?>" aria-describedby="<?php echo X_Sanitize::out_html( $field ); ?>Hint" value="<?php echo X_Sanitize::out_html( $this->item->{$field} ); ?>" <?php echo X_Sanitize::out_html( $required ); ?>>
											</div>
										</div>
										<?php
										break;
									case 'mediumtext':
									case 'longtext':
										?>
										<div class="draggable" id="<?php echo X_Sanitize::out_html( $field ); ?>_container">
											<div class="mb-3 position-relative" id="<?php echo X_Sanitize::out_html( $field ); ?>_group">
												<span class="position-absolute top-0 left-0 m-1 x_drag_handle x_drag_handle-editor" id="<?php echo X_Sanitize::out_html( $field ); ?>Hint"></span>
												<textarea name="<?php echo X_Sanitize::out_html( $field ); ?>" id="<?php echo X_Sanitize::out_html( $field ); ?>" placeholder="<?php echo strtoupper( X_Sanitize::out_html( $field ) ); ?>" class="form-control"><?php echo X_Sanitize::out_html( $this->item->{$field} ); ?></textarea>
											</div>
										</div>
										<?php
										break;
									default:
										// fallback simple input.
										break;
								}
							}
						}
						if ( 'media' === $this->type ) {
							?>
							<div class="draggable" id="media_item_container">
								<div class="mb-3 position-relative" id="media_item_container_group">
									<span class="position-absolute top-0 left-0 m-1 x_drag_handle x_drag_handle-editor" id="media_item_containerHint"></span>
									<div id="media_item_containerEditor" style="height: 666px;position:relative;">
									</div>
								</div>
							</div>
							<?php
						}
						?>
					
					</div>
					<div class="col resizable" id="resizable_1">
						<?php
						foreach ( $this->columns as $key => $column ) {

							$required = 'NO' === $column->Null ? 'required' : '';
							$field = X_Sanitize::out_html( $column->Field );
							$type = $this->get->get_item_by( 'settings', 'slug', 'x_field_type_' . $field );

							if ( ! empty( $type )
								&& false !== $type
							) {
								if ( 'img' === $type->value ) {
									?>
									<div class="draggable" id="<?php echo X_Sanitize::out_html( $field ); ?>_container">
										<?php
										$imgt = null;
										if ( $this->item->{$field} ) {
											$imgt = 'var/uploads/' . $this->item->{$field};
										}
										?>
										<img src="<?php echo X_Sanitize::out_html( $imgt ); ?>" class="rounded mx-auto d-block w-100 mb-3 x_drag_handle" alt="..." id="<?php echo X_Sanitize::out_html( $field ); ?>_group">
										<input type='file' id="<?php echo X_Sanitize::out_html( $field ); ?>_input" accept="image/*" class="d-none" name="<?php echo X_Sanitize::out_html( $field ); ?>"/>
									</div>
									<?php
								} elseif ( 'owner' === $type->value ) {

									$users = $this->get->get_items( 'users' );
									?>
									<div class="draggable" id="<?php echo X_Sanitize::out_html( $field ); ?>_container"><div class="mb-3 input-group" id=<?php echo X_Sanitize::out_html( $field ); ?>_group>
										<label for="<?php echo $field; ?>" class="input-group-text x_drag_handle"><?php echo $field; ?></label>
										<select required name="<?php echo $field ?>" id="<?php echo $field; ?>"   class="form-select x_select2" data-placeholder="Choose a <?php echo $field; ?>">
											<option></option>
											<?php
											foreach ( $users as $user_object ) {

												$selected = $user_object->id === $this->item->owner ? 'selected="selected"' : '';
												?>
												<option <?php echo $selected; ?> value="<?php echo $user_object->id; ?>"><?php echo $user_object->username; ?></option>
												<?php

											}
											?>
										</select>

									</div></div>
									
									<?php
								}
							} else {
								switch ( $column->Type ) {
									case 'date':
										?>
										<div class="draggable" id="<?php echo X_Sanitize::out_html( $field ); ?>_container"><div class="input-group mb-3 " id="<?php echo X_Sanitize::out_html( $field ); ?>_group">
											<span class="input-group-text x_drag_handle" id="<?php echo X_Sanitize::out_html( $field ); ?>Hint"><?php echo strtoupper( X_Sanitize::out_html( $field ) ); ?></span>
											<input type="date" class="form-control" name="<?php echo X_Sanitize::out_html( $field ); ?>" id="<?php echo X_Sanitize::out_html( $field ); ?>" placeholder="YYYY-MM-DD" <?php echo X_Sanitize::out_html( $required ); ?> aria-label="<?php echo strtoupper( X_Sanitize::out_html( $field ) ); ?>" aria-describedby="<?php echo X_Sanitize::out_html( $field ); ?>Hint" value="<?php echo X_Sanitize::out_html( $this->item->{$field} ); ?>">
										</div></div>
										<?php
										break;
									case 'datetime':
										?>
										<div class="draggable" id="<?php echo X_Sanitize::out_html( $field ); ?>_container">
											<div class="input-group mb-3" id="<?php echo X_Sanitize::out_html( $field ); ?>_group">
											<span class="input-group-text x_drag_handle" id="<?php echo X_Sanitize::out_html( $field ); ?>Hint"><?php echo strtoupper( X_Sanitize::out_html( $field ) ); ?></span>
											<input type="datetime-local" step="1" class="form-control" name="<?php echo X_Sanitize::out_html( $field ); ?>" id="<?php echo X_Sanitize::out_html( $field ); ?>" placeholder="YYYY-MM-DD" <?php echo X_Sanitize::out_html( $required ); ?> aria-label="<?php echo strtoupper( X_Sanitize::out_html( $field ) ); ?>" aria-describedby="<?php echo X_Sanitize::out_html( $field ); ?>Hint" value="<?php echo str_replace( ' ', 'T', X_Sanitize::out_html( $this->item->{$field} ) ); ?>">
											</div>
										</div>
										<?php
										break;
									case 'varchar(20)':
									case 'varchar(60)':
									case 'varchar(255)':
									case 'varchar(100)':
									case 'tinytext':
										?>
										<div class="draggable" id="<?php echo X_Sanitize::out_html( $field ); ?>_container"><div class="input-group mb-3 " id="<?php echo X_Sanitize::out_html( $field ); ?>_group">
											<span class="input-group-text x_drag_handle" id="<?php echo X_Sanitize::out_html( $field ); ?>Hint"><?php echo strtoupper( X_Sanitize::out_html( $field ) ); ?></span>
											<input type="text" class="form-control" name="<?php echo X_Sanitize::out_html( $field ); ?>" id="<?php echo X_Sanitize::out_html( $field ); ?>" placeholder="<?php echo strtoupper( X_Sanitize::out_html( $field ) ); ?>" aria-label="<?php echo strtoupper( X_Sanitize::out_html( $field ) ); ?>" aria-describedby="<?php echo X_Sanitize::out_html( $field ); ?>Hint" value="<?php echo X_Sanitize::out_html( $this->item->{$field} ); ?>" maxlength="255" <?php echo X_Sanitize::out_html( $required ); ?>>
										</div></div>
										<?php
									default:
										// fallback simple input.
										break;
								}
							}
						}

						if ( false !== $this->relationships ) {
							// we have some relationships with this item type.
							foreach ( $this->relationships as $relationship_object ) {

								?>
								<div class="draggable" id="<?php echo X_Sanitize::out_html( $relationship_object->slug ); ?>_container"><div class="mb-3 input-group" id=<?php echo X_Sanitize::out_html( $relationship_object->slug ); ?>_group>

									<?php if ( 'edit' === $this->results['action'] ) { ?>
									<label for="<?php echo $relationship_object->slug; ?>" class="input-group-text x_drag_handle"><?php echo $this->related_entities[ $relationship_object->slug ]; ?></label>
									<select multiple name="<?php echo $relationship_object->slug; ?>[]" id="<?php echo $relationship_object->slug; ?>"   class="x_select2 form-select" data-placeholder="Choose a <?php echo $this->related_entities[ $relationship_object->slug ]; ?>">
										<option></option>
										<?php
										$selected_partners = array();
										foreach ( $this->partners[ $relationship_object->slug ] as $related_partner_object ) {
											/**
											 * Returns objects of each "couple" in the specific table
											 */
											$selected_partners[] = $related_partner_object->{rtrim( $this->related_entities[ $relationship_object->slug ], 's' )};
										}
										foreach ( $this->parter_candidates[ $relationship_object->slug ] as $partner_candidate_object ) {

											$selected = in_array( $partner_candidate_object->id, $selected_partners ) ? 'selected="selected"' : '';
											?>
											<option <?php echo $selected; ?>value="<?php echo $partner_candidate_object->id; ?>"><?php echo $partner_candidate_object->id; ?></option>
											<?php

										}
										?>
									</select>
									<?php } else { ?>
										<div class="alert alert-warning w-100">
											<p class="m-0">To connect <?php echo ucfirst( $this->related_entities[ $relationship_object->slug ] ); ?>, save the <?php echo rtrim( ucfirst( $this->type ), 's' ); ?> first.</p>
										</div>
									<?php } ?>
								</div></div>
								
								<?php
							}
						}
						?>
						<div class="d-flex justify-content-between">
							<input type="hidden" name="x_token" value="<?php echo X_Functions::set_token( 'x_add', 'add' ); ?>">
							<?php
							if ( isset( $this->item )
								&& property_exists( $this->item, 'id' )
								&& ! is_null( $this->item->id )
							) {
								?>
								<a href="admin.php?x_action=delete&x_type=<?php echo X_Sanitize::out_html( $this->type ); ?>&id=<?php echo intval( $this->item->id ); ?>" class="x_confirm btn btn-danger" data-x_confirm="This will irrevocably delete the Item.">Delete</a>
								<?php
							}
							?>
							<a href="admin.php" class="btn btn-warning">Cancel</a>
							<input type="submit" class="btn btn-success" name="save" value="Save"/>
						</div>
						<?php
						if ( isset( $this->item )
							&& property_exists( $this->item, 'id' )
							&& ! is_null( $this->item->id )
						) {
							?>
							<div class="d-flex justify-content-end align-items-center">
								<a href="<?php echo $this->link; ?>" target="_blank" noopener class="text-decoration-none text-success pt-3">View <?php echo ucfirst( rtrim( X_Sanitize::out_html( $this->type ), 's' ) ); ?> <span class="bi bi-box-arrow-up-right"></span></a>
							</div>
							<?php
						}
						?>
					</div>
				</div>
			</form>

		</div>
	</main>
<?php include ADMIN_PATH . '/include/footer.php'; ?>
