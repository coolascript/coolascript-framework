( function ($) {
	$(document)
		.on( 'ready', function (e) {
			$( '.sortable' ).sortable({
				handle: '.sortable-handler'
			} );
		} )
		.on( 'click', '.csframework-add-repeatable-row', function ( e ) {
			e.preventDefault();
			var $target = $( '#' + $( this ).data( 'target' ) ),
				indx = $target.data( 'rows' ),
				indexes = [];
			$target.parents( '.repeatable-field-row' ).each( function () {
				indexes[indexes.length] = $( this ).index();
			} );
			$.ajax( {
				beforeSend: function () {
					// body...
				},
				complete: function () {
					// body...
				},
				dataType: 'html',
				method: 'post',
				url: $( this ).attr( 'href' ),
				data: {
					indx: indx,
					indexes: indexes
				},
				success: function ( data ) {
					$row = $( data );
					$row.hide().appendTo( $target ).slideDown( 300, function () {
						$( document ).trigger( 'onNewRepeatableRow' );
						$target.data( 'rows', indx + 1 );
						var wysiwygs = $row.find( '.wysiwyg-field' );
						if ( wysiwygs.size() ) {
							wysiwygs.each( function () {
								var id = $( this ).attr( 'id' );
								tinymce.init({
									selector: "#" + id,
									menubar : false,
									plugins: "image"
								});
							} );
						}

					} );
				}
			} );
		} )
		.on( 'click', '.remove-repeatable-row', function ( e ) {
			e.preventDefault();
			var $target = $( this ).parent().parent();
			$target.slideUp( 300, function () {
				$( this ).remove();
			} );
		} );
} )( jQuery )