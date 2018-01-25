$( function () {
	mw.loader.using( [ 'ext.bluespice.upload' ] ).done( function () {

		function _showDialog( upldr, files ) {
			upldr.disableBrowse( true );

			Ext.require( 'BS.dialog.MultiUpload', function () {
				var mud = new BS.dialog.MultiUpload( {
					uploader: upldr,
					files: files
				} );
				mud.show();
			} );
		}
		;

		var uploader = bs.upload.makeUploader( {
			drop_element: 'bs-sitetool-dropzone',
			browse_button: 'bs-sitetool-dropzone',
			dragdrop: true
		} );
		uploader.bind( 'FilesAdded', _showDialog );

		$( '#bs-sitetool-dropzone' ).html( mw.message( 'bs-uploader-drop-or-click' ).text() );
	} );

} );