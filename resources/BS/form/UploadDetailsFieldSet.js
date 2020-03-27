Ext.define( 'BS.form.UploadDetailsFieldSet', {
	extend: 'Ext.form.FieldSet',
	requires: [ 'BS.form.CategoryBoxSelect', 'BS.store.BSApi' ],
	collapsed: true,
	collapsible: true,
	anchor: '98%',
	defaults: {
		anchor: '100%',
		labelWidth: 90,
		labelAlign: 'right'
	},
	title: mw.message('bs-upload-details').plain(),

	/* Component specific */
	defaultFileNamePrefix: '',
	defaultCategories: [],
	defaultDescription: '',
	defaultLicence: null,
	implicitFileNamePrefix: '',
	implicitCategories: [],
	implicitDescription: '',

	initComponent: function() {

		this.taDescription = new Ext.form.field.TextArea({
			fieldLabel: mw.message('bs-upload-descfilelabel').plain(),
			id: this.getId()+'-text',
			value: this.defaultDescription,
			name: 'text'
		});

		this.storeLicenses = new BS.store.BSApi({
			apiAction: 'bs-upload-license-store',
			fields: ['text', 'value', 'indent'],
			submitValue: false
		});

		this.cbLicences = new Ext.form.ComboBox( {
			fieldLabel: mw.message('bs-upload-license').plain(),
			mode: 'local',
			store: this.storeLicenses,
			valueField: 'value',
			displayField: 'text',
			tpl: new Ext.XTemplate(
				'<ul class="x-list-plain">',
				  '<tpl for=".">',
				    '<tpl if="this.hasValue(value) == false">',
				      '<li role="option" class="x-boundlist-item no-value">{text}</li>',
				    '</tpl>',
				    '<tpl if="this.hasValue(value)">',
				      '<li role="option" class="x-boundlist-item indent-{indent}">{text}</li>',
				    '</tpl>',
				  '</tpl>',
				'</ul>',
				{
					compiled: true,
					disableFormats: true,
					// member functions:
					hasValue: function(value) {
						return value !== '';
					}
				}
			)
		});
		this.cbLicences.setValue( this.defaultLicence );

		this.cbxWatch = new Ext.form.field.Checkbox({
			boxLabel: mw.message('bs-upload-uploadwatchthislabel').plain(),
			id: this.getId() + 'watch_page',
			name: 'watchlist',
			checked: mw.user.options.get( 'watchuploads' )
		});

		this.cbxWarnings = new Ext.form.field.Checkbox({
			boxLabel: mw.message('bs-upload-uploadignorewarningslabel').plain(),
			id: this.getId()+'ignorewarnings',
			name: 'ignorewarnings',
			checked: true
		});

		this.bsCategories = new BS.form.CategoryBoxSelect({
			id: this.getId() + 'categories',
			name: 'categories',
			fieldLabel: mw.message('bs-upload-categories').plain()
		});
		this.bsCategories .setValue( this.defaultCategories );

		this.items = this.makeItems();
		this.callParent( arguments );
	},

	makeItems: function() {
		return [
			this.bsCategories,
			this.taDescription,
			this.cbLicences,
			this.cbxWarnings,
			this.cbxWatch
		];
	},

	getUploadAPIData: function() {
		var desc = this.taDescription.getValue();
		var licence = this.cbLicences.getValue();
		if( licence ) {
			desc += licence + "\n";
		}

		var categories = this.bsCategories.getValue();
		categories = categories.concat( this.implicitCategories );
		var formattedNamespaces = mw.config.get('wgFormattedNamespaces');
		for( var i = 0; i < categories.length; i++ ) {
			var category = categories[i];
			var categoryLink = new bs.wikiText.Link({
				title: category.charAt( 0 ).toUpperCase() + category.slice( 1 ), //$.ucFirst is no longer available in MW REL1_27
				nsText: formattedNamespaces[bs.ns.NS_CATEGORY],
				link: false //TODO: fix this in "bs.wikiText.Link"
			});
			desc += "\n" + categoryLink.toString();
		}
		desc += this.implicitDescription;

		return {
			fileNamePrefix: this.implicitFileNamePrefix + this.defaultFileNamePrefix,
			text: desc,
			watchlist: this.cbxWatch.getValue() ? 'watch' : 'nochange',
			ignorewarnings: this.cbxWarnings.getValue() ? 1 : 0
		};
	}
});
