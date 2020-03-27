Ext.define('BS.action.APIUpload', {
	extend: 'BS.action.Base',

	//Custom config
	uploadApiMeta: null,
	file: false,
	uploader: null,
	dfd: null,

	constructor: function( cfg ) {
		if( !cfg.uploader ) {
			throw new Error( 'Parameter "uploader" is required!' );
		}
		if( !cfg.file ) {
			throw new Error( 'Parameter "file" is required!' );
		}

		this.uploadApiMeta = $.extend( {
			filename: '',
			text: '',
			comment: '',
			ignorewarnings: 1,
			watchlist: 'preferences'
		}, cfg.uploadApiMeta );

		this.callParent( arguments );

		this.uploader.bind( 'FileUploaded' ,this.onFileUploaded, this );
		this.uploader.bind( 'BeforeUpload' ,this.onBeforeUpload, this );
		this.uploader.bind( 'UploadFile' ,this.onUploadFile, this );
		this.uploader.bind( 'Error' ,this.onError, this );

		this.dfd = $.Deferred();
	},

	execute: function() {
		var me = this;

		var api = new mw.Api();
		api.getToken( 'csrf' ).then( function ( token ) {
			me.uploadApiMeta.token = token;
			//If the process is not started yet, start it. Should only appear once
			if( me.uploader.state === plupload.STOPPED ) {
				me.uploader.start();
			}
		});

		/*
		 * As the whole upload is handled by "this.uploader"
		 * (pluploader.Upload) we don't need to do anything here. The Promise
		 * is going to be resolved/rejected within the appropriate event
		 * handlers.
		 */
		return this.dfd.promise();
	},

	onFileUploaded: function( upldr, file, xhr ) {
		if( file.id === this.file.id ) {
			var response = Ext.decode( xhr.response ); //MW API response object
			if( response.error ) {
				// this block is for backward compatability
				this.actionStatus = BS.action.Base.STATUS_ERROR;
				this.dfd.reject( this, response.error );
			} else if ( response.errors ) {
				this.actionStatus = BS.action.Base.STATUS_ERROR;
				this.actionErrors = response.errors;
				this.dfd.reject( this, response.errors );
			} else {
				this.actionStatus = BS.action.Base.STATUS_DONE;
				this.dfd.resolve( this, response.upload.result );
			}
		}
	},

	onBeforeUpload: function( upldr, file ) {
		if( file.id === this.file.id ) {
			var mpp = upldr.getOption('multipart_params');
			mpp = Ext.apply( mpp, this.uploadApiMeta );

			this.fireEvent( 'beforeuploadfile', this, mpp );

			upldr.setOption('multipart_params', mpp);
		}
	},

	onUploadFile: function( upldr, file ) {
		this.actionStatus = BS.action.Base.STATUS_RUNNING;
	},

	onError: function( upldr, error ) {
		if( error.file && error.file.id === this.file.id ) {
			this.actionStatus = BS.action.Base.STATUS_ERROR;
			this.dfd.reject( this, arguments );
		}
	},

	getDescription: function() {
		return mw.message('bs-deferred-action-apiupload-description', this.file.name, this.uploadApiMeta.filename).parse();
	}
});
