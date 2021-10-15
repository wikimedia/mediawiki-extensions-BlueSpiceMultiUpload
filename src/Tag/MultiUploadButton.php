<?php

namespace BlueSpice\MultiUpload\Tag;

use Message;

class MultiUploadButton extends MultiUploadTagBase {
	public const TAG_NAME = 'multiuploadbutton';

	/**
	 *
	 * @return string
	 */
	protected function makeHTML() {
		return \Html::rawElement(
			'button',
			$this->makeAttribs(),
			\Html::element( 'span', [ 'class' => 'bs-icon-upload icon' ] )
			. $this->processedArgs[static::ATTR_LABEL]
		);
	}

	/**
	 *
	 * @return string[]
	 */
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

	/**
	 *
	 * @return Message
	 */
	protected function getDefaultLabelMessage() {
		return wfMessage( 'bs-multiupload-tag-button-label-default' );
	}

}
