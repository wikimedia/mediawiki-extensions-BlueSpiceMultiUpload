<?php
class BlueSpiceMultiUploadHooks {

	/**
	 * Add resources for MobileFrontend
	 * @param OutputPage &$out
	 * @param Skin &$skin
	 * @return bool
	 */
	public static function onBeforePageDisplayMobile( &$out, &$skin ) {
		$out->addModules( 'ext.bluespice.multiUpload.MobileFrontend' );
		$out->addModuleStyles( 'ext.bluespice.multiUpload.MobileFrontend.styles' );

		return true;
	}

	/**
	 * Add menu item to MobileFrontend
	 * @param string $section
	 * @param MediaWiki\Minerva\Menu\Group &$group
	 * @return bool
	 */
	public static function onMobileMenu( $section, &$group ) {
		if ( $section !== 'discovery' ) {
			return true;
		}

		$group->insert( 'bsmultiupload' )
			->addComponent(
					wfMessage( 'bs-upload-multiupload-link-label' )->escaped(),
					'#',
					MobileUI::iconClass( 'bs-multiupload', 'before' ),
					[
						'id' => 'bsMultiUpload',
						'data-event-name' => 'bsmultiupload',
					]
			);

		return true;
	}
}
