( function ( $ ) {
	$( '.csframework-map-field' ).each( function () {
		google_api_map_init( this );
	} );
	function google_api_map_init( _this ){
		var map,
			title = $( '.title', _this ).val(),
			lat = parseFloat( $( '.lat', _this ).val() ),
			lng = parseFloat( $( '.lng', _this ).val() ),
			markCoord1 = new google.maps.LatLng( lat ? lat : 0, lng ? lng : 0 ),
			marker,
			mapOptions = {
				zoom: 11,
				center: markCoord1
			};

		map = new google.maps.Map( $( ".csframework-field-map-canvas", _this )[0], mapOptions );
		if ( lat && lng ) {

			marker = new google.maps.Marker( {
				map: map,
				position: markCoord1,
			} );

			google.maps.event.addDomListener( window, 'resize', function() {
				map.setCenter( markCoord1 );
			} );
		}

		google.maps.event.addListener( map, 'click', function( e ) {
			$( '.lat', _this ).val( e.latLng.lat() );
			$( '.lng', _this ).val( e.latLng.lng() );
			if ( !marker ) {
				marker = new google.maps.Marker( {
					map: map,
					position: e.latLng,
				} );
			}
			markCoord1 = e.latLng;
			marker.setPosition( e.latLng );
		} );
	}
} )( jQuery );