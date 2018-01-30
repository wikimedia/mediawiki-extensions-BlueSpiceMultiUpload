<?php

namespace BlueSpice\MultiUpload\Hook\ParserFirstCallInit;

use BlueSpice\MultiUpload\Tag\MultiUploadButton;
use BlueSpice\MultiUpload\Tag\MultiUploadDropZone;

class AddTags {

	/**
	 *
	 * @var \Parser
	 */
	protected $parser = null;

	/**
	 *
	 * @param \Parser $parser
	 * @return boolean
	 */
	public static function callback( &$parser ) {
		$handler = new self( $parser );

		return $handler->process();
	}

	/**
	 *
	 * @param \Parser $parser
	 */
	public function __construct( &$parser ) {
		$this->parser =& $parser;
	}

	/**
	 *
	 * @return boolean
	 */
	public function process() {
		return $this->doProcess();
	}

	protected function doProcess() {
		$this->parser->setHook(
			MultiUploadButton::TAG_NAME,
			[ MultiUploadButton::class, 'callback' ]
		);
		$this->parser->setHook(
			MultiUploadDropZone::TAG_NAME,
			[ MultiUploadDropZone::class, 'callback' ]
		);

		return true;
	}
}