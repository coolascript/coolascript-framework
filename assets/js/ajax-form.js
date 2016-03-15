(function ($) {
	$(document)
		.on( 'submit', '.csframework-ajax-form', function ( e ) {
			e.preventDefault();
			var $form = $( this );
			$form.ajaxSubmit( {
				url: $form.data( 'action' ) ? $form.data( 'action' ) : $form.action,
				dataType: 'json',
				beforeSerialize: function( jqForm, options ) {
					jqForm.trigger( "ajax_form_befoer_serialize", {
						form: jqForm,
						options: options
					} );
				},
				beforeSubmit: function( formData, jqForm, options ) {
					jqForm.addClass( 'loading' );
					$( '.form-response', jqForm ).slideUp( 300, function () {
						$( this ).removeClass( 'error success' );
					} );
					jqForm.trigger( "ajax_form_befoer_send", {
						form: jqForm,
						formData: formData,
						options: options
					} );
				},
				complete: function(  xhr, status ) {
					$form.removeClass( 'loading' );
					$form.trigger( "ajax_form_complete", {
						form: $form,
						status: status,
						xhr: xhr
					} );
				},
				error: function(  xhr, status, error ) {
					$form.trigger( "ajax_form_complete", {
						form: $form,
						status: status,
						error: error,
						xhr: xhr
					} );
				},
				success : function( responseText, statusText, xhr, jqForm ) {
					if ( responseText.error ) {
						$( '.form-response', jqForm ).addClass( 'error' ).html(responseText.error).slideDown( 300 );
					} else {
						$( '.form-response', jqForm ).addClass( 'success' ).html(responseText.message).slideDown( 300 );
					}
					jqForm.trigger( "ajax_form_sent", {
						form: jqForm,
						response: responseText,
						status: statusText,
						xhr: xhr
					} );
				}
			});
		});
})(jQuery)