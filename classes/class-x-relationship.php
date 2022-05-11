<?php
/**
 * X_Relationship trait
 *
 * @since 1.0.0
 * @package uhleloX\classes\presenters
 */

/**
 * Trait to connect, disconnect, gather partners in a relationship and add (new) relationship tables.
 *
 * @since 1.0.0
 */

trait X_Relationship {

	/**
	 * Make sure X_Post is instantiated.
	 *
	 * @throws Exception $e If $post not set.
	 */
	private function x_post() {

		try {

			if ( ! isset( $this->post ) ) {

				throw new Exception( '$post must be defined in ' . __CLASS__ );

			}
		} catch ( Exception $e ) {

			echo $e->getMessage();
			error_log( $e->getMessage() . print_r( $e, true ), 0 );
			exit();

		}

		return $this->post;

	}

	/**
	 * Make sure X_Get is instantiated.
	 *
	 * @throws Exception $e If $get not set.
	 */
	private function x_get() {

		try {

			if ( ! isset( $this->get ) ) {

				throw new Exception( '$get must be defined in ' . __CLASS__ );

			}
		} catch ( Exception $e ) {

			echo $e->getMessage();
			error_log( $e->getMessage() . print_r( $e, true ), 0 );
			exit();

		}

		return $this->get;

	}

	/**
	 * Make sure X_Delete is instantiated.
	 *
	 * @throws Exception $e If $get not set.
	 */
	private function x_delete() {

		try {

			if ( ! isset( $this->delete ) ) {

				throw new Exception( '$get must be defined in ' . __CLASS__ );

			}
		} catch ( Exception $e ) {

			echo $e->getMessage();
			error_log( $e->getMessage() . print_r( $e, true ), 0 );
			exit();

		}

		return $this->delete;

	}

	/**
	 * Make sure $relationships is instantiated.
	 *
	 * @throws Exception $e If $relationships not set.
	 */
	private function x_relationships() {

		try {

			if ( ! isset( $this->relationships ) ) {

				throw new Exception( '$relationships must be defined in ' . __CLASS__ );

			}
		} catch ( Exception $e ) {

			echo $e->getMessage();
			error_log( $e->getMessage() . print_r( $e, true ), 0 );
			exit();

		}

		return $this->relationships;

	}

	/**
	 * Make sure $type is instantiated.
	 *
	 * @throws Exception $e If $relationships not set.
	 */
	private function x_type() {

		try {

			if ( ! isset( $this->type ) ) {

				throw new Exception( '$relationships must be defined in ' . __CLASS__ );

			}
		} catch ( Exception $e ) {

			echo $e->getMessage();
			error_log( $e->getMessage() . print_r( $e, true ), 0 );
			exit();

		}

		return $this->type;

	}

	/**
	 * Disconnect partners in a relationship, if any.
	 */
	private function maybe_disconnect_partners() {

		if ( false !== $this->x_relationships()
			&& 'POST' === $_SERVER['REQUEST_METHOD']
		) {

			foreach ( $this->x_relationships() as $relationship ) {

				/**
				 * Map the partners to correct entity
				 */
				$is_entity_a = $relationship->entity_a === $this->x_type() ? true : false;
				$is_entity_b = $relationship->entity_b === $this->x_type() ? true : false;

				/**
				 * Already connected partners
				 */
				$partners = $this->x_get()->get_items_in( $relationship->slug, array( rtrim( $this->x_type(), 's' ) ), $this->x_post()->id );
				if ( false !== $partners ) {

					$persisting_partners = array();
					if ( isset( $_POST[ $relationship->slug ] ) ) {

						$persisting_partners = $_POST[ $relationship->slug ];

					}

					$type = $is_entity_a ? $relationship->entity_b : $relationship->entity_a;
					$connected_partners = array_column( $partners, rtrim( $type, 's' ) );
					$divorced_partners = array_diff( $connected_partners, $persisting_partners );
					foreach ( $divorced_partners as $divorced_partner ) {

						/**
						 * Disconnect the old partners.
						 *
						 * $couple_id is the ID of the row that connects these 2 partners (current and other),
						 * which shall be disconnencted. We simply delete that row, that's it.
						 */
						$couple = array_column( $partners, null, rtrim( $type, 's' ) )[ $divorced_partner ] ?? false;
						$this->x_delete()->delete_by_id( $relationship->slug, $couple->id );

					}
					/**
					 * Unset the deleted partners from the $_POST so they do not get connected again.
					 *
					 * If $_POST is empty (all partners deleted or none set), skip.
					 */
					if ( isset( $_POST[ $relationship->slug ] ) ) {

						$_POST[ $relationship->slug ] = array_diff( $_POST[ $relationship->slug ], $divorced_partners );

					}
				}
			}
		}
	}

	/**
	 * Connect partners in a relationship, if any.
	 */
	private function maybe_connect_partners() {

		if ( false !== $this->x_relationships() 
            && 'POST' === $_SERVER['REQUEST_METHOD']
        ) {

			foreach ( $this->x_relationships() as $relationship ) {

				/**
				 * Map the partners to correct entity
				 */
				$is_entity_a = $relationship->entity_a === $this->x_type() ? true : false;
				$is_entity_b = $relationship->entity_b === $this->x_type() ? true : false;

				if ( isset( $_POST[ $relationship->slug ] ) ) {

					/**
					 * Already connected partners
					 */
					$partners = $this->x_get()->get_items_in( $relationship->slug, array( rtrim( $this->x_type(), 's' ) ), $this->x_post()->id );

					foreach ( $_POST[ $relationship->slug ] as $partner_id ) {

						/**
						 * Skip if partner is already connected.
						 */
						$type = $is_entity_a ? $relationship->entity_b : $relationship->entity_a;
						$connected_partners = array_column( $partners, rtrim( $type, 's' ) );
						if ( in_array( $partner_id, $connected_partners ) ) {
							continue;
						}

						/**
						 * Connect the new partners.
						 */
						$left = $is_entity_a ? $this->x_post()->id : $partner_id;
						$right = $is_entity_b ? $this->x_post()->id : $partner_id;
						$this->x_post()->connect( $relationship->slug, (int) $left, (int) $right );

					}

					/**
					 * We do not need the posted relationship anymore in the POST data.
					 *
					 * It is crucial to remove any non-item data after setting up post data.
					 *
					 * @see $this->X_Post::setup_data()
					 */
					unset( $this->x_post()->data[ $relationship->slug ] );
				}
			}
		}
	}

	/**
	 * Partnership Candidates of the other type in a given relationship.
	 *
	 * Ex: if editing an item type 'page' which is in relation with an item type 'user',
	 * provide a list of all users (the other type).
	 *
	 * @todo bring $this->related_entities out into a seprate methhod, sicne we might use it
	 * more granularly.
	 */
	private function gather_parter_candidates() {

		if ( false !== $this->x_relationships() 
            && 'GET' === $_SERVER['REQUEST_METHOD']
        ) {
			foreach ( $this->x_relationships() as $relationship ) {
				/**
				 * Get type in relationship which is not currently edited.
				 *
				 * Ex: if editing an item type 'page' which is in relation with an item type 'user',
				 * $this->related_entity is 'users'
				 */
				$this->related_entities[ $relationship->slug ] = $relationship->entity_a === $this->x_type() ? $relationship->entity_b : $relationship->entity_a;

				/**
				 * Get all candidate partners (not yet connected items) of that type
				 *
				 * Ex: if editing an item type 'page' which is in relation with an item type 'user',
				 * $this->parter_candidates[ $relationship->slug ] is a list of users not connected yet.
				 */
				$this->parter_candidates[ $relationship->slug ] = $this->x_get()->get_items( $this->related_entities[ $relationship->slug ] );

				/**
				 * Get all partners (already connected items) of that type.
				 *
				 * Since the current item might be new, and we did not yet POST the data,
				 * we need to build the current item ID first.
				 *
				 * Ex: if editing an item type 'page' which is in relation with an item type 'user',
				 * $this->partners[ $relationship->slug ] is a list of already connected users.
				 */
				$current_item = isset( $_GET['id'] ) ? (int) $_GET['id'] : 0;
				$this->partners[ $relationship->slug ] = $this->x_get()->get_items_in( $relationship->slug, array( rtrim( $this->x_type(), 's' ) ), $current_item );
			}
		}

	}

	/**
	 * Create new relationship table, if adding new relationship.
	 */
	private function maybe_add_relationship_table() {

		if ( 'relationships' === $this->type ) {

			$entity_a = rtrim( X_Validate::str( stripslashes( $_POST['entity_a'] ) ), 's' );
			$entity_b = rtrim( X_Validate::str( stripslashes( $_POST['entity_b'] ) ), 's' );
			$this->post->add_table( X_Validate::str( stripslashes( $_POST['slug'] ) ), $entity_a, $entity_b  );

		}
	}
}
