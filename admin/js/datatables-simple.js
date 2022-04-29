/**
 * Implements Simple DataTables
 *
 * @package uhleloX\admin\js
 */

(function( $ ) {
	'use strict';
	/**
	 * Initialise the Simple DataTables
	 *
	 * @since 1.0.0
	 * @see https://github.com/fiduswriter/Simple-DataTables/wiki
	 */
	$( window ).on(
		'load',
		function() {
			const datatables_simple = document.getElementById( 'datatablesSimple' );
			if ( datatables_simple ) {
				const data_tables_simple = new simpleDatatables.DataTable( datatables_simple );
			}
		}
	);
})( jQuery );
