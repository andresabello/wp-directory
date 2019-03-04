jQuery(document).ready(function($){
	//Hover over stars for listing rating
	$('#commentform .pi-star').on({
	    mouseover: function(){
		    var me = $(this);
			var rating = me.data('number'); 
			$(this).closest('.pi-stars').find( ".pi-star" ).each(function() {
				if( $(this).data('number') <= rating ){
					$(this).find('.glyphicon').removeClass( "grey" ).addClass( "gold" );
				}else{
					$(this).find('.glyphicon').removeClass( "gold" ).addClass( "grey" );
				}
			});
	    },
	    mouseleave: function(){
		    var me = $(this);
			var rating = me.data('number');
			$('#star-rating').val(rating);
    	}
	}); 
});