<?php

namespace BlueSpice\MultiUpload\Hook\SkinTemplateOutputPageBeforeExec;

use BlueSpice\Hook\SkinTemplateOutputPageBeforeExec;
use BlueSpice\SkinData;

class AddDropZone extends SkinTemplateOutputPageBeforeExec {
	protected function doProcess() {
		$this->mergeSkinDataArray(
			SkinData::PAGE_TOOLS_PANEL,
			[
				'DropZone' => [
					'position' => 50,
					'label' => 'DropZone',
					'type' => 'html',
					'content' => '<div id="bs-sitetool-dropzone"></div>'
				]
			]
		);
		return true;
	}
}