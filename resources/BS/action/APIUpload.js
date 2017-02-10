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

		this.uploadApiMeta = {
			filename: '',
			text: '',
			comment: '',
			ignorewarnings: 1,
			warch: 1
		};

		this.addEvents( 'beforeuploadfile' );

		this.callParent( arguments );

		this.uploader.bind( 'FileUploaded' ,this.onFileUploaded, this );
		this.uploader.bind( 'BeforeUpload' ,this.onBeforeUpload, this );
		this.uploader.bind( 'UploadFile' ,this.onUploadFile, this );
		this.uploader.bind( 'Error' ,this.onError, this );

		this.dfd = $.Deferred();
	},

	execute: function() {
		//If the process is not started yet, start it. Should only appear once
		if( this.uploader.state === plupload.STOPPED ) {
			this.uploader.start();
		}

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
			var response = Ext.decode(xhr.response); //MW API response object
			if( response.error ) {
				this.actionStatus = BS.action.Base.STATUS_ERROR;
				this.dfd.reject( this, response.error );
				//console.log(response.error);
			}
			else {
				this.actionStatus = BS.action.Base.STATUS_DONE;
				this.dfd.resolve( this, response.upload.result );
				//console.log(response.upload.result);
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