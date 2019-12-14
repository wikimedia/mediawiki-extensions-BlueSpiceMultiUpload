<?php

namespace BlueSpice\MultiUpload\Tag;

use Message;

class MultiUploadDropZone extends MultiUploadTagBase {
	const TAG_NAME = 'multiuploaddropzone';

	/* Element specific */
	const ATTR_WIDTH = 'width';
	const ATTR_HEIGHT = 'height';

	/**
	 *
	 * @return array
	 */
	protected function getDefaultArgs() {
		$args = parent::getDefaultArgs();
		$args[static::ATTR_HEIGHT] = '100px';
		$args[static::ATTR_WIDTH] = '100%';

		return $args;
	}

	protected function processInputAndArguments() {
		parent::processInputAndArguments();
		$this->processedArgs[static::ATTR_HEIGHT]
			= \Sanitizer::checkCss( $this->args[static::ATTR_HEIGHT] );
		$this->processedArgs[static::ATTR_WIDTH]
			= \Sanitizer::checkCss( $this->args[static::ATTR_WIDTH] );
	}

	/**
	 *
	 * @return string
	 */
	protected function makeHTML() {
		$innerHtml = \Html::rawElement(
			'div',
			[ 'class' => 'inner' ],
			\Html::element( 'div', [ 'class' => 'icon' ] )
			. $this->processedArgs[static::ATTR_LABEL]
		);
		return \Html::rawElement(
			'div',
			$this->makeAttribs(),
			$innerHtml
		);
	}

	/**
	 *
	 * @return array
	 */
	protected function makeAttribs() {
		$attribs = parent::makeAttribs();

		$height = $this->processedArgs[static::ATTR_HEIGHT];
		$width = $this->processedArgs[static::ATTR_WIDTH];
		$attribs['style'] = "width:$width; height:$height";

		return $attribs;
	}

	/**
	 *
	 * @return string[]
	 */
	protected function getCssClasses() {
		return array_merge(
			parent::getCssClasses(),
			[
				'bs-multiupload-dropzone'
			]
		);
	}

	/**
	 *
	 * @return Message
	 */
	protected function getDefaultLabelMessage() {
		return wfMessage( 'bs-multiupload-tag-dropzone-label-default' );
	}
}
