/**
 * Implements Edit/Add scren layouts
 *
 * @package uhleloX\admin\js
 */

(function( $ ) {
	'use strict';
	/**
	 * Implement the drag and drop/resize features of add/edit screens
	 *
	 * @since 1.0.0
	 */
		$( window ).on(
			'DOMContentLoaded',
			function() {
				// Local storage for the Columns Layout.
				var x_grid = JSON.parse( localStorage.x_grid || '{}' );
				var x_draggable_class_name = '.draggable';

				$(
					function() {

						// All elements with class .resizable are resizable, unless the last child (last column).
						var x_resizable_el = $( '.resizable' ).not( ':last-child' );
						// All elements with class .draggable are draggable/sortable.
						var x_draggable_el = $( x_draggable_class_name );
						// Bootstrap has 12 columns.
						var x_columns = 12;
						// Full (css) width of row.
						var x_full_width = x_resizable_el.parent().width();
						// Calculated width of single column.
						var x_column_width = x_full_width / x_columns;
						// Total number of columns actually available - filled in 'start' event.
						var x_total_cols;

						// Update each column class if available in x_grid local storage.
						$.each(
							x_grid,
							function ( id, pos ) {
								var x_col = $( '#' + id );
								x_update_col_class( x_col, pos );
							}
						);

						// Implement jQuery resize.
						x_resizable_el.resizable(
							{

								handles: 'e',
								start: function( event, ui ) {

									  // The Target Element.
									  var x_target_el = ui.element;
									  // The Adjacent Element.
									  var x_next_el = x_target_el.next();
									  // The Target Column.
									  var x_target_col = Math.round( x_target_el.width() / x_column_width );
									  // The Next (other) Columns.
									  var x_next_col = Math.round( x_next_el.width() / x_column_width );
									  // Total Columns available.
									  x_total_cols = x_target_col + x_next_col;

									  // Set Target element min width.
									  x_target_el.resizable( 'option', 'minWidth', x_column_width );
									  // Set Target element max widht.
									  x_target_el.resizable( 'option', 'maxWidth', ((x_total_cols - 1) * x_column_width ) );

								},
								resize: function( event, ui ) {

									  // The Target Element.
									  var x_target_el = ui.element;
									  // The Adjacent Element.
									  var x_next_el = x_target_el.next();
									  // Column count of adjacent columns.
									  var x_next_col_count = Math.round( x_next_el.width() / x_column_width );
									  // Target column set (1 to 12).
									  var x_target_set = x_total_cols - x_next_col_count;

									  // If we where to have more than 2 columns we would needd to apply x_update_col_class() for a "next_set" (all adjacent columns...).
									  x_update_col_class( x_target_el, x_target_set );
									  // Set an array of IDs and Column sets.
									  x_grid[ this.id ] = x_target_set;
									  // Store array in local storage.
									  localStorage.x_grid = JSON.stringify( x_grid );
								},
							}
						);

						x_draggable_el.sortable(
							{

								connectWith: x_draggable_class_name,
								helper: 'clone',
								appendTo: '#x_form_container',
								handle:'.x_drag_handle',

								update: function() {
									$( x_draggable_class_name ).children().each(
										function() {
											x_save_position( $( this ).attr( 'id' ) );
										}
									);
								}

							}
						);

					}
				);

				function x_update_col_class( el, col ) {

					// remove width, our class already has it.
					el.css( 'width', '' );
					// remove class from column and add new class.
					el.removeClass(
						function( index, classname ) {
							return ( classname.match( /(^|\s)col-\S+/g ) || [] ).join( ' ' );
						}
					).addClass( 'col-sm-' + col );

				}

				function x_save_position( id ) {

					// Dragged element ID.
					var el = $( '#' + id );
					// Parent element of dragged element.
					var container = el.parent().attr( 'id' );
					// Index position of dragged element.
					var index = el.index();

					// Store the ID of dragged element, parent container and index position in local storage.
					localStorage.setItem( id, JSON.stringify( { container:container, index:index } ) );

				}

				function x_load_position( id ){

					// Element ID to position.
					var el = $( '#' + id );
					// Position to applly from local storage.
					var position = JSON.parse( localStorage.getItem( id ) || '{ "container":"0", "index":"0" }' );
					// Parent container of element to position.
					var container = '#' + position.container;
					// Index position of element.
					var index = position.index;

					/**
					 * Position the elements basede on their Index
					 *
					 * If index 0: prepend
					 * If element at index-1 does not exist, append
					 * else, after
					 */
					if ( index === 0 ) {
						$( container ).prepend( el );
					} else if ( $( container ).children().eq( index - 1 ).length === 0 ) {
						$( container ).append( el );
					} else {
						$( container ).children().eq( index - 1 ).after( el );
					}

				}

				// For each draggable, try to position according to local storage.
				$( x_draggable_class_name ).children().each(
					function() {

						x_load_position( $( this ).attr( 'id' ) );

					}
				);
			}
		);
})( jQuery );
