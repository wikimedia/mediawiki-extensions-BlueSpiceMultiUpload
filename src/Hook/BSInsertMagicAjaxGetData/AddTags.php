<?php

namespace BlueSpice\MultiUpload\Hook\BSInsertMagicAjaxGetData;

use BlueSpice\MultiUpload\Tag\MultiUploadButton;
use BlueSpice\MultiUpload\Tag\MultiUploadDropZone;

class AddTags {

	/**
	 *
	 * @var \stdClass
	 */
	protected $response = null;

	/**
	 *
	 * @var string
	 */
	protected $type = '';

	/**
	 *
	 * @param \stdClass $response
	 * @param string $type
	 * @return boolean
	 */
	public static function callback( &$response, $type ) {
		$handler = new self( $response, $type );

		return $handler->process();
	}

	/**
	 *
	 * @param \stdClass $response
	 * @param string $type
	 */
	public function __construct( &$response, $type ) {
		$this->response =& $response;
		$this->type = $type;
	}

	/**
	 *
	 * @return boolean
	 */
	public function process() {
		return $this->doProcess();
	}

	protected function doProcess() {
		if( $this->type !== 'tags' ) {
			return true;
		}

		//Button
		$descriptor = new \stdClass();
		$descriptor->id = '<' . MultiUploadButton::TAG_NAME . '>';
		$descriptor->type = 'tag';
		$descriptor->name = MultiUploadButton::TAG_NAME;
		$descriptor->desc = wfMessage( 'bs-multiupload-tag-button-desc' )->text();
		$descriptor->code = <<<HERE
<multiuploadbutton
  defaultCategories="Uploaded with Multiupload"
  defaultFileNamePrefix="Help_"
  defaultDescription="Uploaded with Multiupload"
  label="Upload file"
  reloadpage=true
/>
HERE;
		$descriptor->previewable = false;

		$this->response->result[] = $descriptor;

		//Dropzone
		$descriptor = new \stdClass();
		$descriptor->id = '<' . MultiUploadDropZone::TAG_NAME . '>';
		$descriptor->type = 'tag';
		$descriptor->name = MultiUploadDropZone::TAG_NAME;
		$descriptor->desc = wfMessage( 'bs-multiupload-tag-dropzone-desc' )->text();
		$descriptor->code = <<<HERE
<multiuploaddropzone
  defaultCategories="Uploaded with Multiupload"
  defaultFileNamePrefix="Help_"
  defaultDescription="Uploaded with Multiupload"
  label="Upload file"
  reloadpage=true
/>
HERE;
		$descriptor->previewable = false;

		$this->response->result[] = $descriptor;
		return true;
	}
}
