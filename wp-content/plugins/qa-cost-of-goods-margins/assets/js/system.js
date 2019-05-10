( function( $ ) {
	'use strict';
	$( function() {
		$( '#the-list' ).on( 'click', '.editinline', function() {
			var cp = $( this )
				.closest( 'tr' )
				.find( '.cost_price.column-cost_price' )
				.text()
				.replace(/\D/g,'');

			$( 'input[name="_qa_cog_cost"]', '.inline-edit-row' ).val( cp );
		} );
	} );

} )( jQuery );

// var post_id = $( this ).closest( 'tr' ).attr( 'id' );
// post_id = post_id.replace( 'post-', '' );