(function( $, mw, bs, d ){
	bs.upload = {
		makeUploader: function( cfg ) {
			if( !cfg.browse_button ) {
				throw new Error( 'Parameter "browse_button" is required!' );
			}

			var maxUploadSize = mw.config.get('bsgMaxUploadSize');
			var lowerMaxUploadSize = maxUploadSize.php > maxUploadSize.mediawiki
				? maxUploadSize.mediawiki
				: maxUploadSize.php;

			var defaultCfg = {
				runtimes : 'html5,silverlight,flash,html4',
				url : mw.util.wikiScript('api'),
				send_file_name: false, //Avoid param "name" which is unknown to MW API - https://github.com/moxiecode/plupload/issues/943
				multipart_params: {
					//Gerneral API
					action: 'upload',
					format: 'json',

					//Upload API
					comment: '',
					text: '',
					ignorewarnings: 1,
					errorformat: 'html',
					watchlist: 'preferences'
				},
				unique_names: true,
				flash_swf_url: bs.em.paths.get( 'BlueSpiceMultiUpload' ) + '/resources/lib/plupload/Moxie.swf',
				silverlight_xap_url: bs.em.paths.get( 'BlueSpiceMultiUpload' ) + '/resources/lib/plupload/Moxie.xap',

				filters : {
					max_file_size: lowerMaxUploadSize,
					mime_types: [
						{
							title : mw.message('bs-uploader-mime-images-label').plain(),
							extensions : mw.config.get( 'bsgImageExtensions' ).join(',')
						},
						{
							title : mw.message('bs-uploader-mime-files-label').plain(),
							extensions : mw.config.get( 'bsgFileExtensions' ).join(',')
						}
					]
				},

				init: {
					BeforeUpload: function( upldr, file ) {
						/**
						 * Unfortunately plupload does not provide a setting
						 * for the filename field name. Only to the file
						 * content fieldname. So it just uses "name". but MW API
						 * needs "filename". Therefore we change the uploaders
						 * general settings with each file to satisfy MW API.
						 */
						var mpp = upldr.getOption('multipart_params');
						mpp.filename = file.name;
						upldr.setOption('multipart_params', mpp);
					},

					UploadComplete: function( upldr, files ) {
						upldr.disableBrowse(false);
						var mpp = upldr.getOption('multipart_params');
						delete(mpp.filename);
						upldr.setOption('multipart_params', mpp);
					}
				}
			};

			cfg = $.extend( defaultCfg, cfg );

			var uploader = new plupload.Uploader( cfg );
			uploader.init();

			return uploader;
		}
	};

})( jQuery, mediaWiki, blueSpice, document );
