<?php
namespace TFC\Image\Command;
use Google\ApiCore\ApiException as AE;
use Google\Cloud\Vision\V1\AnnotateImageResponse as Res;
use Google\Cloud\Vision\V1\BoundingPoly;
use Google\Cloud\Vision\V1\EntityAnnotation as EA;
use Google\Cloud\Vision\V1\ImageAnnotatorClient as Annotator;
use Google\Cloud\Vision\V1\LocalizedObjectAnnotation as O;
use Google\Cloud\Vision\V1\NormalizedVertex as NV;
use Google\Cloud\Vision\V1\Vertex as V;
use Google\Protobuf\Internal\RepeatedField;
use RecursiveDirectoryIterator as RDI;
use RecursiveIteratorIterator as RII;
use Symfony\Component\Console\Input\InputArgument as Arg;
use Symfony\Component\Console\Input\InputOption as Opt;
# 2020-11-09 "Images with a grey text are incorrectly cropped": https://github.com/tradefurniturecompany/image/issues/2
final class C2 extends \Df\Framework\Console\Command {
	/**
	 * 2020-10-25
	 * @override
	 * @see \Symfony\Component\Console\Command\Command::configure()
	 */
	protected function configure() {
		$this->setName('tfc:image:2')->setDescription('Processes product images');
		$this->setDefinition([new Opt(self::$P_CATEGORY, null, Arg::OPTIONAL)]);
	}

	private static $P_CATEGORY = 'category';

	/**
	 * 2020-10-25
	 * @override
	 * @see \Df\Framework\Console\Command::p()
	 * @used-by \Df\Framework\Console\Command::execute()
	 * @return void
	 */
	protected function p() {
		#$opts = $this->input()->getOptions();
		#$this->output()->writeln($this->input()->getOption(self::$P_CATEGORY));
		#return;
		df_google_init_service_account();
		$path = dirname(BP) . '/test/1.jpg';
		$ta = $this->annotationsText($path); /** @var RepeatedField $ta */
		if ($ta->count()) {
			$oo = $this->annotationsObject($path); /** @var RepeatedField $oo */
			if (1 === $oo->count()) {
				# 2020-10-26
				# https://cloud.google.com/vision/docs/reference/rest/v1/AnnotateImageResponse#LocalizedObjectAnnotation
				$o = $oo[0]; /** @var O $o */
				$t = $ta[0]; /** @var EA $t */
				# 2020-10-26
				# https://cloud.google.com/vision/docs/reference/rest/v1/projects.locations.products.referenceImages#BoundingPoly
				$bpT = $t->getBoundingPoly(); /** @var BoundingPoly $bpT */
				$vvT = $bpT->getVertices(); /** @var RepeatedField $vvT */
				$bpO = $o->getBoundingPoly(); /** @var BoundingPoly $bpO */
				$vvO = $bpO->getNormalizedVertices(); /** @var RepeatedField $vvO */
				list($w, $h) = getimagesize($path); /** @var int $w */ /** @var int $h */
				$f = function($a, $b) {return round($a * $b);};
				$x = function($a) use($f, $w) {return $f($w, $a);};
				$y = function($a) use($f, $h) {return $f($h, $a);};
				$n2v = function(NV $v) use($x, $y) {
					$r = new V;
					$r->setX($x($v->getX()));
					$r->setY($y($v->getY()));
					return $r;
				};
				$v0O = $n2v($vvO[0]); /** @var V $v0O */
				$v2O = $n2v($vvO[2]); /** @var V $v2O */
				$pad = function(V $v0, V $v2) use($w, $h) {
					$d = 10;
					$v0->setX(max(0, $v0->getX() - $d));
					$v0->setY(max(0, $v0->getY() - $d));
					$v2->setX(min($w, $v2->getX() + $d));
					$v2->setY(min($h, $v2->getY() + $d));
					return [];
				};
				$v0T = $vvT[0]; /** @var V $v0T */
				$v2T = $vvT[2]; /** @var V $v2T */
				$pad($v0T, $v2T);
				$union = function(V $a0, V $a2, V $b0, V $b2) {
					$r0 = new V; /** @var V $r0 */
					$r2 = new V; /** @var V $r2 */
					$r0->setX(min($a0->getX(), $b0->getX()));
					$r0->setY(min($a0->getY(), $b0->getY()));
					$r2->setX(max($a2->getX(), $b2->getX()));
					$r2->setY(max($a2->getY(), $b2->getY()));
					return [$r0, $r2];
				};
				list($v0, $v2) = $union($v0O, $v2O, $v0T, $v2T);
				$im = imagecreatefromjpeg($path);
				try {
					$im2 = imagecrop($im, [
						'height' => $v2->getY() - $v0->getY()
						,'width' => $v2->getX() - $v0->getX()
						,'x' => $v0->getX(), 'y' => $v0->getY()
					]);
					$path = df_cc_path(dirname(BP), 'result', basename($path));
					if (!is_dir($dir = dirname($path))) {
						mkdir($dir, 0777, true);
					}
					imagejpeg($im2, $path);
					imagedestroy($im2);
				}
				finally {imagedestroy($im);}
			}
		}
	}

	/**
	 * 2020-11-10
	 * @used-by p()
	 * @param string $path
	 * @return RepeatedField
	 * @throws AE
	 */
	private function annotationsObject($path) {
		$a = new Annotator; /** @var Annotator $a */
		try {$resO = $a->objectLocalization($f); /** @var Res $resO */}
		finally {$a->close();}
		return $resO->getLocalizedObjectAnnotations();
	}

	/**
	 * 2020-11-10
	 * @used-by p()
	 * @param string $path
	 * @return RepeatedField
	 * @throws AE
	 */
	private function annotationsText($path) {
		$a = new Annotator; /** @var Annotator $a */
		$f = file_get_contents($path); /** @var string $f */
		try {$resT = $a->textDetection($f); /** @var Res $resT */}
		finally {$a->close();}
		return $resT->getTextAnnotations();
	}
}