Ext.define( 'BS.dialog.MultiUpload', {
	extend: 'BS.Window',
	requires: [ 'BS.form.UploadDetailsFieldSet', 'BS.action.APIUpload', 'BS.dialog.BatchActions' ],

	title: mw.message('bs-upload-multiuploaddialogtitle').plain(),

	uploader: null, //pluploader.Upload
	files: [],

	makeItems: function() {
		this.fsUploadDetails = new BS.form.UploadDetailsFieldSet({
			collapsed: false
		});

		var items = [
			this.fsUploadDetails
		];

		$(document).trigger('BSMultiUploadDialogMakeItems', [this, items]);
		return items;
	},

	onBtnOKClick: function() {
		var allFilesParams = this.fsUploadDetails.getUploadAPIData();

		var actions = [],
			file = null,
			meta = {},
			cfg = {};

		for( var i = 0; i < this.files.length; i++) {
			file = this.files[i];
			var value = file.name;

			//Copy & Paste from "BS.InsertFile.UploadPanel": Remove path info
			value = value.replace(/^.*?([^\\\/:]*?\.[a-z0-9]+)$/img, "$1");
			value = value.replace(/\s/g, "_");
			if( mw.config.get('bsIsWindows') ) {
				value = value.replace(/[^\u0000-\u007F]/gmi, ''); //Replace Non-ASCII
			}
			meta = Ext.Object.merge(
				{}, //make a copy!
				{
					filename: value
				},
				allFilesParams
			);

			cfg = {
				uploader: this.uploader,
				file: file,
				uploadApiMeta: meta
			};

			$(document).trigger('BSMultiUploadDialogMakeAction', [this, cfg]);

			actions.push(new BS.action.APIUpload( cfg ));
		}

		var diag =  new BS.dialog.BatchActions();
		diag.setData( actions );
		diag.show();
		diag.startProcessing();

		this.callParent(arguments);
	}
});