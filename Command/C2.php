<?php
namespace TFC\Image\Command;
use Google\ApiCore\ApiException as AE;
use Google\Cloud\Vision\V1\BoundingPoly;
use Google\Cloud\Vision\V1\EntityAnnotation as EA;
use Google\Cloud\Vision\V1\LocalizedObjectAnnotation as O;
use Google\Cloud\Vision\V1\NormalizedVertex as NV;
use Google\Cloud\Vision\V1\Vertex as V;
use Google\Protobuf\Internal\RepeatedField;
use Symfony\Component\Console\Input\InputArgument as Arg;
use Symfony\Component\Console\Input\InputOption as Opt;
use TFC\Image\I;
# 2020-11-09 "Images with a grey text are incorrectly cropped": https://github.com/tradefurniturecompany/image/issues/2
final class C2 extends \Df\Framework\Console\Command {
	/**
	 * 2020-10-25
	 * @override
	 * @see \Symfony\Component\Console\Command\Command::configure()
	 * @used-by \Symfony\Component\Console\Command\Command::__construct()
	 */
	protected function configure() {
		$this->setName('tfc:image:2')->setDescription('Processes product images');
		$this->setDefinition([new Opt(self::$P_CATEGORY, null, Arg::OPTIONAL)]);
	}

	/**
	 * 2020-10-25
	 * @override
	 * @see \Df\Framework\Console\Command::p()
	 * @used-by \Df\Framework\Console\Command::execute()
	 * @throws AE
	 */
	protected function p() {
		#$opts = $this->input()->getOptions();
		#$this->output()->writeln($this->input()->getOption(self::$P_CATEGORY));
		#return;
		df_google_init_service_account();
		$i = new I(dirname(BP) . '/test/1.jpg');
		$ta = $i->annotationsText(); /** @var RepeatedField $ta */
		if ($ta->count()) {
			$oo = $i->annotationsObject(); /** @var RepeatedField $oo */
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
				/** @var int $w */ $w = $i->w();
				/** @var int $h */ $h = $i->h();
				$f = function($a, $b) {return round($a * $b);};
				$x = function($a) use($f, $w) {return $f($w, $a);};
				$y = function($a) use($f, $h) {return $f($h, $a);};
				$n2v = function(NV $v) use($x, $y) {
					$r = new V;
					$r->setX($x($v->getX()));
					$r->setY($y($v->getY()));
					return $r;
				};
				/** @var V $v0O */ $v0O = $n2v($vvO[0]);
				/** @var V $v2O */ $v2O = $n2v($vvO[2]);
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
				$im = $i->createResource();
				try {
					$im2 = imagecrop($im, [
						'height' => $v2->getY() - $v0->getY()
						,'width' => $v2->getX() - $v0->getX()
						,'x' => $v0->getX(), 'y' => $v0->getY()
					]);
					$path = df_cc_path(dirname(BP), 'result', $i->basename());
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
	 * 2020-10-11
	 * @used-by configure()
	 * @var string
	 */
	private static $P_CATEGORY = 'category';
}