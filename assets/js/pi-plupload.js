jQuery(document).ready(function($)
{
	// Hide "Uploaded files" title if there are no files uploaded after deleting files
	$( '.pi-images' ).on( 'click', '.pi-delete-file', function()
	{
		// Check if we need to show drop target
		var $dragndrop = $( this ).parents( '.pi-images' ).siblings( '.pi-drag-drop' );

		// After delete files, show the Drag & Drop section
		$dragndrop.removeClass('hidden');
	} );

	$( '.pi-drag-drop' ).each(function()
	{
		// Declare vars
		var $dropArea = $( this ),
			$imageList = $dropArea.siblings( '.pi-uploaded' ),
			uploaderData = $dropArea.data( 'js_options' ),
			uploader = {};

		// Extend uploaderData
		uploaderData.multipart_params = $.extend(
			{
				_ajax_nonce	:  $dropArea.data( 'upload_nonce' ),
				post_id 	: $( '#post_ID' ).val()
			},
			uploaderData.multipart_params
		);

		// Create uploader
		uploader = new plupload.Uploader( uploaderData );
		uploader.init();

		// Add files
		uploader.bind( 'FilesAdded', function( up, files )
		{
			var maxFileUploads = $imageList.data( 'max_file_uploads' ),
				uploaded = $imageList.children().length,
				msg = maxFileUploads > 1 ? piFile.maxFileUploadsPlural : piFile.maxFileUploadsSingle;

			msg = msg.replace( '%d', maxFileUploads );

			// Remove files from queue if exceed max file uploads
			if ( maxFileUploads > 0  && ( uploaded + files.length ) > maxFileUploads )
			{
				if ( uploaded < maxFileUploads )
				{
					var diff = maxFileUploads - uploaded;
					up.splice( diff - 1, files.length - diff );
					files = up.files;
				}
				alert( msg );
			}

			// Hide drag & drop section if reach max file uploads
			if ( maxFileUploads > 0 && uploaded + files.length >= maxFileUploads )
				$dropArea.addClass( 'hidden' );

			max = parseInt( up.settings.max_file_size, 10 );

			// Upload files
			plupload.each( files, function( file )
			{
				addLoading( up, file, $imageList );
				addThrobber( file );
				if ( file.size >= max )
					removeError( file );
			} );
			up.refresh();
			up.start();

		} );

		uploader.bind( 'Error', function( up, e )
		{
			addLoading( up, e.file, $imageList );
			removeError( e.file );
			up.removeFile( e.file );
		} );

		uploader.bind( 'FileUploaded', function( up, file, r )
		{
			r = $.parseJSON( r.response );
			r.success ? $( 'li#' + file.id ).replaceWith( r.data ) : removeError( file );
		} );
	});

	// Delete file via Ajax
	$( '.pi-images' ).on( 'click', '.pi-delete-file', function()
	{
		var $this = $( this ),
			$parent = $this.parents( 'li' ),
			$container = $this.closest( '.pi-uploaded' ),
			data = {
				action: 'pi_delete_file',
				_ajax_nonce: $container.data( 'delete_nonce' ),
				post_id: $( '#post_ID' ).val(),
				field_id: $container.data( 'field_id' ),
				attachment_id: $this.data( 'attachment_id' ),
				force_delete: $container.data( 'force_delete' )
			};

		$.post( piajax.ajaxurl, data, function( r )
		{
			if ( !r.success )
			{
				alert( r.data );
				return;
			}

			$parent.addClass( 'removed' );

			// If transition events not supported
			if (
				!( 'ontransitionend' in window )
				&& ( 'onwebkittransitionend' in window )
				&& !( 'onotransitionend' in myDiv || navigator.appName == 'Opera' )
			)
			{
				$parent.remove();
				$container.trigger( 'update.piFile' );
			}

			$( '.pi-images' ).on( 'transitionend webkitTransitionEnd otransitionend', 'li.removed', function()
			{
				$( this ).remove();
				$container.trigger( 'update.piFile' );
			} );
		}, 'json' );

		return false;
	});

	//Remove deleted file
	$( '.pi-images' ).on( 'transitionend webkitTransitionEnd otransitionend', 'li.removed', function() {
		$( this ).remove();
	});

	$( 'body' ).on( 'update.piFile', '.pi-images', function()
	{
		var $fileList = $( this ),
			maxFileUploads = $fileList.data( 'max_file_uploads' ),
			$uploader = $fileList.siblings( '.new-files' ),
			numFiles = $fileList.children().length;

		numFiles > 0 ? $fileList.removeClass( 'hidden' ) : $fileList.addClass( 'hidden' );

		// Return if maxFileUpload = 0
		if ( maxFileUploads === 0 )
			return false;

		// Hide files button if reach max file uploads
		numFiles >= maxFileUploads ? $uploader.addClass( 'hidden' ) : $uploader.removeClass( 'hidden' );

		return false;
	});

	$( document ).ajaxSend( function( e, xhr, s )
	{
		if ( typeof s.data != 'undefined' &&  -1 != s.data.indexOf( 'action=autosave' ) )
		{
			$( '.ot-metabox-wrapper').each( function()
			{
				var $meta_box = $( this );
				if ( $meta_box.data( 'autosave' ) == true )
				{
					s.data += '&' + $meta_box.find( ':input' ).serialize();
				}
			} );
		}
	});

	/**
	 * Removes li element if there is an error with the file
	 *
	 * @return void
	 */
	function removeError( file )
	{
		$( 'li#' + file.id )
			.addClass( 'pi-image-error' )
			.delay( 1600 )
			.fadeOut( 'slow', function()
			{
				$( this ).remove();
			}
		);
	}

	/**
	 * Adds loading li element
	 *
	 * @return void
	 */
	function addLoading( up, file, $ul )
	{
		$ul.removeClass('hidden').append( "<li id='" + file.id + "'><div class='pi-image-uploading-bar'></div><div id='" + file.id + "-throbber' class='pi-image-uploading-status'></div></li>" );
	}

	/**
	 * Adds loading throbber while waiting for a response
	 *
	 * @return void
	 */
	function addThrobber( file )
	{
		$( '#' + file.id + '-throbber' ).html( "<img class='pi-loader' height='64' width='64' src='" + piajax.imgs + "/icons/loader.gif'/>" );
	}
	

});
