<?php
class BlueSpiceMultiUploadHooks {

	/**
	 * JavaScript and CSS
	 * @param OutputPage $out
	 * @param Skin $skin
	 * @return boolean true
	 */
	public static function onBeforePageDisplay( &$out, &$skin ) {
		$out->addModules( 'ext.bluespice.uploader.bootstrap' );
		$out->addModules( 'ext.bluespice.upload' );
		$out->addModules( 'ext.bluespice.extjs.upload' );
		$out->addModules( 'ext.bluespice.multiUpload' );

		return true;
	}
}
