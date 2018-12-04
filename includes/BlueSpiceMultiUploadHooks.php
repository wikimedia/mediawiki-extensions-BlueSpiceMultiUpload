<?php
class BlueSpiceMultiUploadHooks {

	/**
	 * Add resources for MobileFrontend
	 * @param OutputPage $out
	 * @param Skin $skin
	 * @return boolean
	 */
	public static function onBeforePageDisplayMobile( &$out, &$skin ) {
		$out->addModules( 'ext.bluespice.multiUpload.MobileFrontend' );
		$out->addModuleStyles( 'ext.bluespice.multiUpload.MobileFrontend.styles' );

		return true;
	}

	/**
	 * Add menu item to MobileFrontend
	 * @param string $section
	 * @param \MobileFrontend\MenuBuilder $menu
	 * @return boolean
	 */
	public static function onMobileMenu( $section, &$menu ) {
		if( $section !== 'discovery' ) {
			return true;
		}

		$menu->insert( 'bsmultiupload' )
			->addComponent(
					wfMessage( 'bs-upload-multiupload-link-label' )->escaped(),
					'#',
					MobileUI::iconClass( 'bs-multiupload', 'before' ),
					array(
						'id' => 'bsMultiUpload',
						'data-event-name' => 'bsmultiupload',
					)
			);

		return true;
	}
}
