(function ($) {
	$(document)
		.on( 'submit', '.csframework-ajax-form', function ( e ) {
			e.preventDefault();
			var $form = $( this );
			$form.ajaxSubmit( {
				url: $form.data( 'action' ) ? $form.data( 'action' ) : $form.action,
				dataType: 'json',
				beforeSubmit: function( formData, jqForm, options ) {
					jqForm.addClass( 'loading' );
					$( '.form-response', jqForm ).slideUp( 300, function () {
						$( this ).removeClass( 'error success bg-danger text-danger bg-success text-success' );
					} );
				},
				success : function( responseText, statusText, xhr, jqForm ) {
					jqForm.removeClass( 'loading' );
					if ( responseText.error ) {
						$( '.form-response', jqForm ).addClass( 'error' ).html(responseText.error).slideDown(300);
					} else {
						$( '.form-response', jqForm ).addClass( 'success' ).html(responseText.message).slideDown(300);
					}
				}
			});
		});
})(jQuery)