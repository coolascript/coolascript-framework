( function ( $ ) {
	$( function () {
		if ( typeof tinymce != 'undefined' ) {
			tinymce.init( {
				selector: ".csframework-wysiwyg-field",
				menubar : false,
				plugins: "image",
			} );
		}
	} )
} )( jQuery )