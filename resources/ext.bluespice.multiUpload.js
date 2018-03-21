$(function(){
	$(document).on( 'BSInsertFileInsertBaseDialogAfterInit', function(e, sender, items ){
		sender.btnUpload.clearListeners();
		sender.btnUpload.on('afterrender', function( button ) {
			bs.uploader.bindTo( button.getEl().dom ).done( function( uploader ) {
				uploader.bind('UploadComplete', function( uploader, files) {
					sender.stImageGrid.load({
						sorters: [{
							"property":"file_timestamp",
							"direction":"DESC"
						}]
					});
				});
			});
		});
	} );

	$(document).on( 'BS.grid.FileRepo.initComponent', function(e, sender, items ){
		sender.btnUpload.clearListeners();
		sender.btnUpload.on('afterrender', function( button ) {
			bs.uploader.bindTo( button.getEl().dom ).done( function( uploader ) {
				uploader.bind('UploadComplete', function( uploader, files) {
					sender.getStore().load();
				});
			});
		});
	} );

	//Overwrite default behavior of toolbox link to Special:Upload
	var $specialUploadLI = $('#t-upload');
	if( $specialUploadLI.length !== 0 ) {
		$specialUploadLI.find('a').attr( 'href', '' );
		bs.uploader.bindTo( $specialUploadLI[0] );
	}

	//Add same behavior to links in BlueSpiceSkin/LeftNavigation
	//that link to Special:Upload. Those links may be user provided
	//so unforntunately we need to analyze the actual URL instead
	//of using IDs or classes to identify the elements
	mw.loader.using( 'mediawiki.Title' ).done(function(){
		$( '.bs-nav-tab a' ).each( function(){
			var sTitle = $(this).data( 'bs-title' ); //Provided by BSF + MW Linker
			if( !sTitle ) {
				return;
			}
			var oTitle = mw.Title.newFromText( sTitle );
			if( oTitle.getNamespaceId() === bs.ns.NS_SPECIAL
				&& $.inArray( oTitle.getNameText(), [ 'Hochladen', 'Upload' ]) !== -1 ) {
				//Having 'de' and 'en' keywords hardcoded is bad
				$(this).attr( 'href', '' );
				bs.uploader.bindTo( this );
			}
		});
	});
});
