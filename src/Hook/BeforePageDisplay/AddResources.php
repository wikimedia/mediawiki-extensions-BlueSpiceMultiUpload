<?php

namespace BlueSpice\MultiUpload\Hook\BeforePageDisplay;

class AddResources extends \BlueSpice\Hook\BeforePageDisplay {

	protected function doProcess() {
		$this->out->addModules( 'ext.bluespice.uploader.bootstrap' );
		$this->out->addModules( 'ext.bluespice.multiUpload' );
		$titles = [];
		$upload = $this->getServices()->getSpecialPageFactory()->getPage( 'Upload' );
		if ( $upload ) {
			$title = $this->getServices()->getSpecialPageFactory()->getLocalNameFor( 'Upload' );
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
		$this->out->addJsConfigVars( 'bsgMultiUploadTitles', $titles );

		return true;
	}

}
