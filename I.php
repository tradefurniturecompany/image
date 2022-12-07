<?php
namespace TFC\Image;
use Google\ApiCore\ApiException as AE;
use Google\Cloud\Vision\V1\AnnotateImageResponse as Res;
use Google\Cloud\Vision\V1\ImageAnnotatorClient as Annotator;
use Google\Protobuf\Internal\RepeatedField;
# 2020-11-10
final class I {
	/**
	 * 2020-11-10
	 * @used-by \TFC\Image\Command\C2::p()
	 */
	function __construct(string $p) {$this->_path = $p;}

	/**
	 * 2020-11-10
	 * @used-by \TFC\Image\Command\C2::p()
	 * @return RepeatedField
	 * @throws AE
	 */
	function annotationsObject() {return dfc($this, function() {
		$a = new Annotator; /** @var Annotator $a */
		try {$resO = $a->objectLocalization($this->f()); /** @var Res $resO */}
		finally {$a->close();}
		return $resO->getLocalizedObjectAnnotations();
	});}

	/**
	 * 2020-11-10 https://cloud.google.com/vision/docs/ocr
	 * @used-by \TFC\Image\Command\C2::p()
	 * @return RepeatedField
	 * @throws AE
	 */
	function annotationsText() {return dfc($this, function() {
		$a = new Annotator; /** @var Annotator $a */
		try {$resT = $a->textDetection($this->f()); /** @var Res $resT */}
		finally {$a->close();}
		return $resT->getTextAnnotations();
	});}

	/**
	 * 2020-11-10
	 * @used-by \TFC\Image\Command\C2::p()
	 * @return string
	 */
	function basename() {return basename($this->_path);}

	/**
	 * 2020-11-10
	 * @used-by \TFC\Image\Command\C2::p()
	 * @return resource
	 */
	function createResource() {return imagecreatefromjpeg($this->_path);}

	/**
	 * 2020-11-10
	 * @used-by \TFC\Image\Command\C2::p()
	 * @return int
	 */
	function h() {return $this->size()[1];}

	/**
	 * 2020-11-10
	 * @used-by \TFC\Image\Command\C2::p()
	 * @return int
	 */
	function w() {return $this->size()[0];}

	/**
	 * 2020-11-10
	 * @used-by annotationsText()
	 * @used-by annotationsObject()
	 * @return string
	 */
	private function f() {return dfc($this, function() {return file_get_contents($this->_path);});}

	/**
	 * 2020-11-10
	 * @used-by h()
	 * @used-by w()
	 * @return int[]
	 */
	private function size() {return dfc($this, function() {return getimagesize($this->_path);});}

	/**
	 * 2020-11-10
	 * @used-by __construct()
	 * @used-by basename()
	 * @used-by createResource()
	 * @used-by f()
	 * @used-by size()
	 * @var string
	 */
	private $_path;
}