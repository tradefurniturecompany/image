<?php
namespace TFC\Image;
use Google\Cloud\Vision\V1\AnnotateImageResponse as Res;
use Google\Cloud\Vision\V1\BoundingPoly;
use Google\Cloud\Vision\V1\ImageAnnotatorClient as Annotator;
use Google\Cloud\Vision\V1\LocalizedObjectAnnotation as O;
use Google\Cloud\Vision\V1\NormalizedVertex as V;
use Google\Protobuf\Internal\RepeatedField;
use RecursiveDirectoryIterator as RDI;
use RecursiveIteratorIterator as RII;
# 2020-10-25
final class Command extends \Df\Framework\Console\Command {
	/**
	 * 2020-10-25
	 * @override
	 * @see \Symfony\Component\Console\Command\Command::configure()
	 */
	protected function configure() {$this->setName('tfc:image')->setDescription('Processes product images');}

	/**
	 * 2020-10-25
	 * @override
	 * @see \Df\Framework\Console\Command::p()
	 * @used-by \Df\Framework\Console\Command::execute()
	 * @return void
	 */
	protected function p() {
		df_google_init_service_account();
		$ii = $this->images(); /** @var string[] $ii */
		$count = count($ii); /** @var int $count */
		$c = 0;
		foreach ($ii as $i) {/** @var string $i */
			$c++;
			$this->output()->writeln(sprintf("%d of %d (%d%%): %s", $c, $count, $c * 100 / $count, df_product_image_path2rel($i)));
			$this->image($i);
		}
	}

	/**
	 * 2020-10-26
	 * @used-by p()
	 * @param string $path
	 * @throws \Google\ApiCore\ApiException
	 */
	private function image($path) {
		$a = new Annotator; /** @var Annotator $a */
		$f = file_get_contents($path); /** @var string $f */
		try {$res = $a->objectLocalization($f); /** @var Res $res */}
		finally {$a->close();}
		$oo = $res->getLocalizedObjectAnnotations(); /** @var RepeatedField $oo */
		if (1 === $oo->count()) {
			# 2020-10-26 https://cloud.google.com/vision/docs/reference/rest/v1/AnnotateImageResponse#LocalizedObjectAnnotation
			$o = $oo[0]; /** @var O $o */
			# 2020-10-26
			# https://cloud.google.com/vision/docs/reference/rest/v1/projects.locations.products.referenceImages#BoundingPoly
			$bp = $o->getBoundingPoly(); /** @var BoundingPoly $bp */
			$vv = $bp->getNormalizedVertices(); /** @var RepeatedField $vv */
			list($w, $h) = getimagesize($path); /** @var int $w */ /** @var int $h */
			$f = function($a, $b) {return round($a * $b);};
			$x = function($a) use($f, $w) {return $f($w, $a);};
			$y = function($a) use($f, $h) {return $f($h, $a);};
			$v0 = $vv[0]; /** @var V $v0 */
			$v2 = $vv[2]; /** @var V $v2 */
			# 2020-10-26
			# https://cloud.google.com/vision/docs/reference/rest/v1/projects.locations.products.referenceImages#NormalizedVertex
			$im = imagecreatefromjpeg($path);
			try {
				$im2 = imagecrop($im, [
					'height' => $y($v2->getY() - $v0->getY())
					,'width' => $x($v2->getX() - $v0->getX())
					,'x' => $x($v0->getX()), 'y' => $y($v0->getY())
				]);
				$path = df_cc_path(dirname(BP), 'result', df_product_image_path2rel($path));
				if (!is_dir($dir = dirname($path))) {
					mkdir($dir, 777, true);
				}
				imagejpeg($im2, $path);
				imagedestroy($im2);
			}
			finally {imagedestroy($im);}
		}
	}

	/**
	 * 2020-10-26
	 * @used-by p()
	 * @return string[]
	 */
	private function images() {return dfc($this, function() {return $this->scan(
		df_product_images_path(), ['cache', 'placeholder']
	);});}

	/**
	 * 2020-10-26
	 * @used-by images()
	 * @used-by scan()
	 * @param string $base
	 * @param string[] $skip [optional]
	 * @return string[]
	 */
	private function scan($base, $skip = []) {
		$r = []; /** @var string[] $r */
		foreach (array_diff(scandir($base = "$base/"), array_merge($skip, ['.', '..'])) as $f) {
			if (is_dir($f = $base . $f)) {
				$r = array_merge($r, $this->scan($f));
			}
			else if (in_array(strtolower(df_file_ext($f)), ['jpg', 'jpeg'])) {
				$r[]= $f;
			}
		}
		return $r;
	}
}