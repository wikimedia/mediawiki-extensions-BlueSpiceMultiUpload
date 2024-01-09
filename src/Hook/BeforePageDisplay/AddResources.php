<?php

namespace BlueSpice\MultiUpload\Hook\BeforePageDisplay;

class AddResources extends \BlueSpice\Hook\BeforePageDisplay {

	protected function doProcess() {
		$this->out->addModules( 'ext.bluespice.uploader.bootstrap' );
		$this->out->addModules( 'ext.bluespice.multiUpload' );

		return true;
	}
}
