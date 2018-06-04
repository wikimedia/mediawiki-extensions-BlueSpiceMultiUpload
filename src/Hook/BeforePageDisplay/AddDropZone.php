<?php

namespace BlueSpice\MultiUpload\Hook\BeforePageDisplay;

use BlueSpice\Hook\BeforePageDisplay;

class AddDropZone extends BeforePageDisplay {

	protected function skipProcessing() {
		return $this->skin instanceof \BlueSpice\Calumma\Skin === false;
	}

	protected function doProcess() {
		$this->out->addModules( "ext.bluespice.multiUpload.dropzone" );
		return true;
	}
}