( function ( $ ) {
	$( function () {
		if ( typeof tinymce != 'undefined' ) {
			console.log('text');
			tinymce.init( {
				selector: ".csframework-wysiwyg-field",
				menubar : false,
				plugins: "image",
			} );
		}
	} )
} )( jQuery )