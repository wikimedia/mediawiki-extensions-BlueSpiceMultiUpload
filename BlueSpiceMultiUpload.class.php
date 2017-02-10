<?php
class BlueSpiceMultiUpload extends BsExtensionMW {

	public function __construct() {
		$this->mExtensionPath = __FILE__;
		$this->mExtensionType = EXTTYPE::OTHER;
		$this->mInfo = array(
			EXTINFO::NAME        => 'BlueSpiceMultiUpload',
			EXTINFO::DESCRIPTION => 'bs-multiupload-desc',
			EXTINFO::AUTHOR      => 'Robert Vogel',
			EXTINFO::VERSION     => 'default',
			EXTINFO::STATUS      => 'stable',
			EXTINFO::URL         => 'http://www.hallowelt.biz',
			EXTINFO::DEPS        => array(
				'bluespice' => '2.27.0'
			)
		);
		$this->mExtensionKey = 'MW::BlueSpiceMultiUpload';
	}
}
