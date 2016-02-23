( function ($) {
	$( document )
		.on( 'ready', function (e) {
			$( '.csframework-sortable' ).sortable( {
				handle: '.csframework-sortable-handler'
			} );
		} )
} )( jQuery )