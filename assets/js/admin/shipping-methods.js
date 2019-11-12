jQuery( function( $ ) {
	$( 'input[id$="_show_delivery_time"], #datadev-jadlog_display_date' ).on( 'change', function() {
		var field = $( 'input[id$="_additional_time"]' ).closest( 'tr' );

		if ( $( this ).is( ':checked' ) ) {
			field.show();
		} else {
			field.hide();
		}
	}).change();
});
