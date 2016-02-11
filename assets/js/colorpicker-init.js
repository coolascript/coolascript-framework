( function ( $ ) {
	var irisLoaded = false;
	$( document )
		.on( 'ready', function () {
			$( '.csframework-color-field' ).iris( {
				change: function ( e, ui ) {
					$( this ).trigger( 'colorchange', [ui.color.toString()] );
				}
			} );
			irisLoaded = true;
		} )
		.on( 'click', function ( e ) {
			if ( irisLoaded && !$( e.target ).is( '.csframework-color-field, .iris-picker *' )) {
				$( '.csframework-color-field' ).iris( 'hide' );
			}
		} )
		.on( 'click', '.csframework-color-field', function ( e ) {
			if ( irisLoaded ) {
				$( '.csframework-color-field' ).iris( 'hide' );
				$( this ).iris( 'show' );
			}
		} );
} )( jQuery )