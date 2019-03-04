jQuery(document).ready(function($){
	$('#pi-claim').on('click', function(e){
        e.preventDefault();
        
        var me = $(this);
        if ( me.data('requestRunning') ) {
            return;
        }
        me.data('requestRunning', true);

        var userID = me.data('user');
        var postID = me.data('id');
        // var nonce = $('#_wpnonce').val();
		var data = {
			action: 'claim_listing',
			userID: userID,
			postID: postID,
			nonce:  pi_claim_ajax.nonce
		};
		$.post(
			pi_claim_ajax.ajaxURL,
			data
		).complete( function () {
			//Spinner so user knows to wait
			$( '#spinner' ).fadeOut();
		} ).success( function ( response ) {
			//let user know that the claim process has begun and what is next
			$( response.data ).insertAfter( "#pi-claim" );
			$('#pi-claim').remove();
			console.log(response.data);
		} ).fail( function ( xhr ) {
			//Let user know it failed and why. Are there other options?
			console.log( pi_claim_ajax.failMessage + xhr.status );
		} ); 
    });
});