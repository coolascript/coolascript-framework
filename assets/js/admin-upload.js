( function ( $ ) {
	var frame;
	$( document )
		.on( 'click', '.csframework-file-field .add-file', function( event ){

			event.preventDefault();

			var addFileLink = $( this ),
				fieldBlock = addFileLink.parent().parent( ".csframework-file-field" ),
				delFileLink = fieldBlock.find( '.delete-file' ),
				fileContainer = fieldBlock.find( '.csframework-file-container' ),
				fileIdInput = fieldBlock.find( '.file-id' ),
				type = fieldBlock.data( 'type' );

			// If the media frame already exists, close it.
			if ( frame ) {
				frame.close();
			}

			// Create a new media frame
			frame = wp.media( {
				multiple: false  // Set to true to allow multiple files to be selected
			} );


			// When an image is selected in the media frame...
			frame.on( 'select', function() {

				// Get media attachment details from the frame state
				var attachment = frame.state().get( 'selection' ).first().toJSON();
				if ( type == 'file' || attachment.type == type ) {
					$( '.error-filetype', fieldBlock ).slideUp( 300 );

					switch ( type ) {
						case 'image':
							// Send the attachment URL to our custom image input field.
							fileContainer.html( '<img src="'+attachment.url+'" alt="" />' );
							break;

						default:
							$.ajax( {
								dataType: 'html',
								method: 'post',
								url: csframework.url,
								data: {
									action: 'file',
									type: type,
									id: attachment.id
								},
								success: function ( data ) {
									fileContainer.html( data );
									$( '.wp-audio-shortcode, .wp-video-shortcode', fieldBlock ).mediaelementplayer();
								}
							} );
							break;
					}

					// Send the attachment id to our hidden input
					fileIdInput.val( attachment.id );

					// Hide the add image link
					addFileLink.addClass( 'hidden' );

					// Unhide the remove image link
					delFileLink.removeClass( 'hidden' );
				} else {
					$( '.error-filetype', fieldBlock ).slideDown( 300 );
				}

			});

			// Finally, open the modal on click
			frame.open();
		})
		.on( 'click', '.csframework-file-field .delete-file', function( event ){

			event.preventDefault();

			var delFileLink = $(this),
				fieldBlock = delFileLink.parent().parent( ".csframework-file-field" ),
				addFileLink = fieldBlock.find( '.add-file'),
				fileContainer = fieldBlock.find( '.csframework-file-container'),
				fileIdInput = fieldBlock.find( '.file-id' );

			// Clear out the preview image
			fileContainer.html( '' );

			// Un-hide the add image link
			addFileLink.removeClass( 'hidden' );

			// Hide the delete image link
			delFileLink.addClass( 'hidden' );

			// Delete the image id from the hidden input
			fileIdInput.val( '' );

		} );
} )( jQuery )