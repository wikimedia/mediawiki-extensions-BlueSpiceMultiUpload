(function( mw, $, bs, d, undefined ) {
	function _showMobileDialog( upldr, files ) {
		upldr.disableBrowse(true);

		Ext.require('BS.dialog.MultiUpload', function(){
			var mud = new BS.dialog.MultiUpload({
				uploader: upldr,
				files: files
			});
			mud.show();
		});
	};

	mw.loader.using( [ 'ext.bluespice.upload', 'ext.bluespice.extjs.upload' ], function() {
		var dfd = $.Deferred();

		var uploader = bs.upload.makeUploader( { browse_button: $('a[data-event-name=bsmultiupload]')[0] } );
		uploader.bind( 'FilesAdded', _showMobileDialog );
		dfd.resolve( uploader );
	});
})( mediaWiki, jQuery, blueSpice, document );