(function( mw, $, bs, d, undefined ){
	function _showDialog( upldr, files ) {
		upldr.disableBrowse(true);
		mw.loader.using( 'ext.bluespice.extjs.upload', function() {
			Ext.require('BS.dialog.MultiUpload', function(){
				var mud = new BS.dialog.MultiUpload( {
						uploader: upldr,
						files: files,
						uploadPanelCfg: upldr.settings.uploadPanelCfg
				});
				mud.show();
				mud.on( 'uploadcomplete', upldr.settings.onUploadDialogUploadComplete );
				mud.on( 'cancel', function() { upldr.disableBrowse(false); } );
			});
		});
	};

	function _showError( uploader, error ) {
		bs.util.alert(
			'bs-multiupload-plupload-error',
			{
				titleMsg: 'bs-extjs-title-warning',
				text: error.message
			}
		);
	};

	bs.uploader = bs.uploader || {};
	bs.uploader.bindTo = function( elem, cfg ) {
		var dfd = $.Deferred();
		cfg = cfg || {};
		cfg = $.extend( {
			onUploadDialogUploadComplete: $.noop,
			uploadPanelCfg: {}
		}, cfg );

		mw.loader.using( [ 'ext.bluespice.upload' ], function() {
			var uploader = bs.upload.makeUploader( $.extend(
				{
					drop_element: elem,
					browse_button: elem,
					dragdrop: true
				},
				cfg
			) );
			uploader.bind( 'FilesAdded', _showDialog );
			uploader.bind( 'Error', _showError );

			dfd.resolve( uploader );
		});

		return dfd.promise();
	};
})( mediaWiki, jQuery, blueSpice, document );
