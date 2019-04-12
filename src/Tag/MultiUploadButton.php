<?php

namespace BlueSpice\MultiUpload\Tag;

class MultiUploadButton extends MultiUploadTagBase {
	const TAG_NAME = 'multiuploadbutton';

	protected function makeHTML() {
		return \Html::rawElement(
			'button',
			$this->makeAttribs(),
			\Html::element( 'span', [ 'class' => 'bs-icon-upload icon' ] )
			. $this->processedArgs[static::ATTR_LABEL]
		);
	}

	protected function getCssClasses() {
		return array_merge(
			parent::getCssClasses(),
			[
				'bs-multiupload-button',
				'oo-ui-inputWidget-input',
				'oo-ui-buttonElement-button'
			]
		);
	}

	protected function getDefaultLabelMessage() {
		return wfMessage( 'bs-multiupload-tag-button-label-default' );
	}

}
