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
			<form enctype="multipart/form-data" id="x_form_container" action="admin.php?action=<?php echo X_Sanitize::out_html( $this->results['action'] ); ?>&type=<?php echo X_Sanitize::out_html( $this->type ); ?>" method="post" name="x_form">
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
							$type = $get->get_item_by( 'settings', 'slug', 'x_field_type_' . $field );

							if ( empty( $type ) || false === $type || 'img' !== $type->value ) {

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
							$type = $get->get_item_by( 'settings', 'slug', 'x_field_type_' . $field );

							if ( ! empty( $type ) && false !== $type && 'img' === $type->value ) {
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

						if( ! empty( $entity_candidates ) 
							&& isset( $entity_candidates) 
						){
							// we have some potential related items of the other type.
							foreach ( $entity_candidates as $relationship => $entity_objects ) {

								?>
								<div class="draggable" id="<?php echo X_Sanitize::out_html( $relationship ); ?>_container"><div class="mb-3 input-group" id=<?php echo X_Sanitize::out_html( $relationship ); ?>_group>
									<label for="<?php echo $relationship;?>" class="input-group-text x_drag_handle"><?php echo $related_entity;?></label>
									<select multiple name="<?php echo $relationship;?>[]" id="<?php echo $relationship;?>"   class="form-select" data-placeholder="Choose a <?php echo $related_entity;?>">
										<option></option>
										<?php
										$selected_things = array();
										$s = rtrim($related_entity,'s');
										foreach ( $related_things[ $relationship ] as $related_object ) {
											$selected_things[] = $related_object->$s;
										}
										foreach ( $entity_objects as $entity_object ) {
											
											$selected = in_array( $entity_object->id, $selected_things ) ? 'selected="selected"' : '';
											?><option <?php echo $selected; ?>value="<?php echo $entity_object->id ?>"><?php echo $entity_object->id ?></option><?php

										}
										?>
									</select>

								</div></div>
								
								<?php
							}
						}
						?>
						<div class="d-flex justify-content-between">
							<input type="hidden" name="token" value="<?php echo X_Functions::set_token( '_x_add', 'add' ); ?>">
							<input type="submit" class="btn btn-success" name="save" value="Save"/>
							<input type="submit" class="btn btn-warning" formnovalidate name="cancel" value="Cancel" />
							<?php
							if ( isset( $this->item )
								&& property_exists( $this->item, 'id' )
								&& ! is_null( $this->item->id )
							) {
								?>
								<a href="admin.php?action=delete&type=<?php echo X_Sanitize::out_html( $this->type ); ?>&id=<?php echo intval( $this->item->id ); ?>" class="btn btn-danger" onclick="return confirm('This will irrevocably delete the item. Proceed?')">Delete</a>
								<a href="<?php echo $this->link;?>" class="btn btn-success">View <?php echo ucfirst( rtrim( X_Sanitize::out_html( $this->type ), 's' ) );?></a>
								<?php
							}
							?>
						</div>
					</div>
				</div>
			</form>

		</div>
	</main>
<?php include ADMIN_PATH . '/include/footer.php'; ?>
