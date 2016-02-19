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
			styleArray = [{"featureType":"landscape","stylers":[{"saturation":-100},{"lightness":65},{"visibility":"on"}]},{"featureType":"poi","stylers":[{"saturation":-100},{"lightness":51},{"visibility":"simplified"}]},{"featureType":"road.highway","stylers":[{"saturation":-100},{"visibility":"simplified"}]},{"featureType":"road.arterial","stylers":[{"saturation":-100},{"lightness":30},{"visibility":"on"}]},{"featureType":"road.local","stylers":[{"saturation":-100},{"lightness":40},{"visibility":"on"}]},{"featureType":"transit","stylers":[{"saturation":-100},{"visibility":"simplified"}]},{"featureType":"administrative.province","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"labels","stylers":[{"visibility":"on"},{"lightness":-25},{"saturation":-100}]},{"featureType":"water","elementType":"geometry","stylers":[{"hue":"#ffff00"},{"lightness":-25},{"saturation":-97}]}],
			mapOptions = {
				zoom: 11,
				center: markCoord1
				//scrollwheel: false,
				//styles: styleArray
			},
			contentString = "<div></div>",
			infowindow = new google.maps.InfoWindow( {
				content: contentString,
				maxWidth: 200
			} );

		map = new google.maps.Map( $( ".csframework-field-map-canvas", _this )[0], mapOptions );
		if ( lat && lng ) {

			marker = new google.maps.Marker( {
				map: map,
				position: markCoord1,
			} );

			var contentString = '<div id="content">'+
			'<div id="siteNotice">'+
			'</div>'+
			'<div id="bodyContent">'+
			'<p>'+title+'</p>'+
			'</div>'+
			'</div>';

			var infowindow = new google.maps.InfoWindow( {
				content: contentString
			} );

			google.maps.event.addListener( marker, 'click', function() {
				infowindow.open( map,marker );
				$( '.gm-style-iw' ).parent().parent().addClass( "gm-wrapper" );
			} );

			google.maps.event.addListener( map, 'click', function( e ) {
				$( '.lat', _this ).val( e.latLng.G );
				$( '.lng', _this ).val( e.latLng.K );
				marker.setPosition( e.latLng );
			} );

			google.maps.event.addDomListener( window, 'resize', function() {
				map.setCenter( markCoord1 );
			} );
		}
	}
} )( jQuery );