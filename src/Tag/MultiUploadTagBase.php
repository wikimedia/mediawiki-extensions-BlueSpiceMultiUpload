<?php

namespace BlueSpice\MultiUpload\Tag;

use BlueSpice\Services;

abstract class MultiUploadTagBase {

	/* Config parameters for 'BS.form.UploadDetailsFieldSet' ans 'BS.panel.Upload' */
	const ATTR_DEFAULT_FILENAMEPREFIX = 'defaultfilenameprefix';
	const ATTR_DEFAULT_CATEGORIES = 'defaultcategories';
	const ATTR_DEFAULT_DESCRIPTION = 'defaultdescription';
	const ATTR_DEFAULT_LICENCE = 'defaultlicence';
	const ATTR_IMPLICIT_FILENAMEPREFIX = 'implicitfilenameprefix';
	const ATTR_IMPLICIT_CATEGORIES = 'implicitcategories';
	const ATTR_IMPLICIT_DESCRIPTION = 'implicitdescription';

	/* Element specific */
	const ATTR_LABEL = 'label';
	const ATTR_RELOADPAGE = 'reloadpage';
	const ATTR_CLASS = 'class';

	/**
	 *
	 * @var string
	 */
	protected $input = '';

	/**
	 *
	 * @var array
	 */
	protected $args = [];

	/**
	 *
	 * @var \Parser
	 */
	protected $parser = null;

	/**
	 *
	 * @var \PPFrame
	 */
	protected $frame = null;

	/**
	 *
	 * @var TagError[]
	 */
	protected $errors = [];

	/**
	 *
	 * @var array
	 */
	protected $processedArgs = [];

	/*
	 * Services
	 */
	protected $services = null;

	/**
	 *
	 * @var \ContextSource
	 */
	protected $context = null;

	/**
	 *
	 * @param string $input
	 * @param array $args
	 * @param \Parser $parser
	 * @param PPFrame $frame
	 * @return mixed
	 */
	public static function callback( $input, array $args, \Parser $parser, \PPFrame $frame ) {
		$taghandler = new static( $input, $args, $parser, $frame );

		return $taghandler->handle();
	}

	/**
	 *
	 * @param string $input
	 * @param array $args
	 * @param \Parser $parser
	 * @param \PPFrame $frame
	 */
	public function __construct( $input, array $args, \Parser $parser, \PPFrame $frame ) {
		$this->input = $input;
		$this->args = $args;
		$this->parser = $parser;
		$this->frame = $frame;

		$this->services = Services::getInstance();
		$this->context = \RequestContext::getMain();
	}

	public function handle() {
		$this->processInputAndArguments();
		if ( $this->hasErrors() ) {
			return $this->outputErrors();
		} else {
			return $this->doHandle();
		}
	}

	protected function processInputAndArguments() {
		$this->args += $this->getDefaultArgs();

		foreach ( $this->args as $attrName => $attrValue ) {
			$this->args[$attrName] = $this->parser->recursiveTagParse( $attrValue, $this->frame );
		}

		$labelMessage = $this->getDefaultLabelMessage();
		$this->processedArgs[static::ATTR_LABEL] = $labelMessage->inContentLanguage()->plain();
		if ( !empty( $this->args[static::ATTR_LABEL] ) ) {
			$this->processedArgs[static::ATTR_LABEL] = $this->args[static::ATTR_LABEL];
		}
		$this->processedArgs[static::ATTR_RELOADPAGE] = (bool)\FormatJson::decode( $this->args[static::ATTR_RELOADPAGE] );
		$this->processedArgs[static::ATTR_CLASS] = explode( ' ', $this->args[static::ATTR_CLASS] );

		$this->setPrefixArg( static::ATTR_DEFAULT_FILENAMEPREFIX );
		$this->setCategoriesArg( static::ATTR_DEFAULT_CATEGORIES );
		$this->setPrefixArg( static::ATTR_IMPLICIT_FILENAMEPREFIX );
		$this->setCategoriesArg( static::ATTR_IMPLICIT_CATEGORIES );

		$this->processedArgs[static::ATTR_DEFAULT_DESCRIPTION] =
			$this->args[static::ATTR_DEFAULT_DESCRIPTION];
		$this->processedArgs[static::ATTR_IMPLICIT_DESCRIPTION] =
			$this->args[static::ATTR_IMPLICIT_DESCRIPTION];
		$this->processedArgs[static::ATTR_DEFAULT_LICENCE] =
			$this->args[static::ATTR_DEFAULT_LICENCE];
	}

	protected function hasErrors() {
		return !empty( $this->errors );
	}

	protected function outputErrors() {
		$output = [];
		foreach ( $this->errors as $errorId => $errorMessage ) {
			$output[] = \Html::element(
				'div',
				[ 'class' => 'box-error' ],
				$errorId . ": " . $errorMessage->parse()
			);
		}

		return implode( "\n", $output );
	}

	protected function doHandle() {
		$this->parser->getOutput()->addModuleStyles( 'ext.bluespice.multiUpload.tags.styles' );
		$this->parser->getOutput()->addModules( 'ext.bluespice.multiUpload.tags' );

		return $this->makeHTML();
	}

	protected function setPrefixArg( $key ) {
		$this->processedArgs[$key] = '';
		if ( empty( $this->args[$key] ) ) {
			return;
		}

		$prefix = $this->args[$key];
		$dummyTitle = $prefix . '_dummy.png';
		$dummyFile = \RepoGroup::singleton()->getLocalRepo()->newFile( $dummyTitle );

		if ( $dummyFile === null ) {
			$this->errors[$key] = wfMessage( 'bs-multiupload-tag-error-invalid prefix', $prefix );
			return;
		}

		$this->processedArgs[$key] = $prefix;
	}

	protected function setCategoriesArg( $key ) {
		$this->processedArgs[$key] = [];
		if ( empty( $this->args[$key] ) ) {
			return;
		}
		$categories = explode( '|', $this->args[$key] );
		$invalidCategories = [];
		$categoryRecords = [];
		foreach ( $categories as $category ) {
			$category = trim( $category );
			$title = \Title::newFromText( $category, NS_CATEGORY );
			if ( $title === null ) {
				$invalidCategories[] = $category;
				continue;
			}
			$categoryRecords[] = $title->getText();
		}
		if ( !empty( $invalidCategories ) ) {
			$count = count( $invalidCategories );
			$list = implode( ', ', $invalidCategories );
			$errorMessage = wfMessage(
				'bs-multiupload-tag-error-invalid-categories',
				$count,
				$list
			);
			$this->errors[$key] = $errorMessage;
			return;
		}

		$this->processedArgs[$key] = $categoryRecords;
	}

	protected function makeAttribs() {
		$classes = array_merge(
			$this->getCssClasses(),
			$this->processedArgs[ static::ATTR_CLASS ]
		);

		$attribs = [
			'class' => implode( ' ', $classes )
		];

		foreach ( $this->getDataAttributeNames() as $attrName ) {
			$dataAttrName = "data-$attrName";
			$formattedValue = $this->processedArgs[$attrName];
			if ( !is_string( $formattedValue ) ) {
				$formattedValue = \FormatJson::encode( $formattedValue );
			}
			$attribs[$dataAttrName] = $formattedValue;
		}

		return $attribs;
	}

	protected function getDataAttributeNames() {
		return [
			static::ATTR_DEFAULT_FILENAMEPREFIX,
			static::ATTR_DEFAULT_CATEGORIES,
			static::ATTR_DEFAULT_DESCRIPTION,
			static::ATTR_DEFAULT_LICENCE,
			static::ATTR_IMPLICIT_FILENAMEPREFIX,
			static::ATTR_IMPLICIT_CATEGORIES,
			static::ATTR_IMPLICIT_DESCRIPTION,
			static::ATTR_RELOADPAGE
		];
	}

	protected function getCssClasses() {
		return [
			'bs-multiupload-tag'
		];
	}

	/**
	 * @return \Message
	 */
	abstract protected function  getDefaultLabelMessage();

	abstract protected function makeHTML();

	protected function getDefaultArgs() {
		return [
			static::ATTR_LABEL => '',
			static::ATTR_CLASS => '',
			static::ATTR_RELOADPAGE => 'false',

			static::ATTR_DEFAULT_FILENAMEPREFIX => '',
			static::ATTR_DEFAULT_CATEGORIES => '',
			static::ATTR_DEFAULT_DESCRIPTION => '',
			static::ATTR_DEFAULT_LICENCE => '',
			static::ATTR_IMPLICIT_FILENAMEPREFIX => '',
			static::ATTR_IMPLICIT_CATEGORIES => '',
			static::ATTR_IMPLICIT_DESCRIPTION => '',

		];
	}
}
