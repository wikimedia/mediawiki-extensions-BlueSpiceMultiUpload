(function( mw, $, bs, d, undefined ){
	function _showDialog( upldr, files ) {
		upldr.disableBrowse(true);

		Ext.require('BS.dialog.MultiUpload', function(){
			var mud = new BS.dialog.MultiUpload({
				uploader: upldr,
				files: files
			});
			mud.show();
		});
	};

	bs.uploader = bs.uploader || {};
	bs.uploader.bindTo = function( elem ) {
		var dfd = $.Deferred();

		mw.loader.using( [ 'ext.bluespice.upload', 'ext.bluespice.extjs.upload' ], function() {
			var uploader = bs.upload.makeUploader( { browse_button: elem } );
			uploader.bind( 'FilesAdded', _showDialog );
			dfd.resolve( uploader );
		});

		return dfd.promise();
	};
})( mediaWiki, jQuery, blueSpice, document );
