( function ( $ ) {
	$( document )
		.on( 'ready onNewRepeatableRow', function () {
			$( '.csframework-depend-field' ).each( function () {
				var $this = $( this ),
					depend = $this.data( 'depend' );
					dependcies = {};
				depend = depend.split( ';' );
				for (var i = 0; i < depend.length; i++) {
					dependcies[depend[i].split( ':' )[0]] = depend[i].split( ':' )[1].split( ',' );
				}
				for(var field in dependcies) { 
					var $field = $( '#' + field );
					if ( $field.size() && dependcies[field].indexOf( $field.val() ) >= 0 ) {
						$this.show( 0 );
					}
					$field.on( 'change', function () {
						var depend = $this.data( 'depend' );
							dependcies = {};
						depend = depend.split( ';' );
						for (var i = 0; i < depend.length; i++) {
							dependcies[depend[i].split( ':' )[0]] = depend[i].split( ':' )[1].split( ',' );
						}
						if ( dependcies[field].indexOf( $( this ).val() ) >= 0 ) {
							$this.slideDown( 300 );
						} else {
							$this.slideUp( 300 );
						}
					} )
				}
			} )
		} );
} )( jQuery );