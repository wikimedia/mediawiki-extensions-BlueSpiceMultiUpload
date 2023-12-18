<?php

namespace BlueSpice\MultiUpload;

use MediaWiki\MediaWikiServices;

class ClientConfig {

	/**
	 * @return array
	 */
	public static function makeConfigJson(): array {
		$services = MediaWikiServices::getInstance();
		$config = $services->getConfigFactory()->makeConfig( 'bsg' );
		$upload = $services->getSpecialPageFactory()->getPage( 'Upload' );

		$titles = [];
		if ( $upload && $config->get( 'MultiUploadIntegrateWithUploadLink' ) ) {
			$title = $services->getSpecialPageFactory()->getLocalNameFor( 'Upload' );
			$titles[] = [
				'ns' => NS_SPECIAL,
				'title' => $title,
			];
			// always also add the non-alias version due to some parts of the interface
			// use the linker and/or local links and others dont
			$titles[] = [
				'na' => NS_SPECIAL,
				'title' => 'Upload',
			];
		}

		return [
			'multiUploadTitles' => $titles
		];
	}
}
