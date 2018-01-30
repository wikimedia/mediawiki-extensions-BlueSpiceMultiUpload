(function( mw, $, bs, d, undefined ){
	$( '.bs-multiupload-tag' ).each( function() {
		var $elem = $(this);

		var data = $(this).data();
		/**
		 * HTML attributes are case insensitive and MediaWiki normalized them
		 * to lower case. To be compatible with BlueSpiceFounation interfaces
		 * we need to convert cases
		 */
		var uploadPanelCfg = {
			defaultFileNamePrefix: data.defaultfilenameprefix,
			defaultCategories: data.defaultcategories,
			defaultDescription: data.defaultdescription,
			implicitFileNamePrefix: data.implicitfilenameprefix,
			implicitCategories: data.implicitcategories,
			implicitDescription: data.implicitdescription,
		};

		bs.uploader.bindTo( $elem[0], {
			uploadPanelCfg: uploadPanelCfg,
			onUploadDialogUploadComplete: function() {
				if( data.reloadpage ) {
					bs.util.alert(
						'bs-multiupload-alert-reload',
						{
							textMsg: 'bs-multiupload-tag-hint-reloadpage'
						},
						{
							'ok': function() {
								window.location.reload();
							}
						}
					);
				}
			}
		} );
	} )
})( mediaWiki, jQuery, blueSpice, document );