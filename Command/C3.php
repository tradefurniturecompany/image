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
final class C3 extends \Df\Framework\Console\Command {
	/**
	 * 2020-10-25
	 * @override
	 * @see \Symfony\Component\Console\Command\Command::configure()
	 */
	protected function configure() {
		$this->setName('tfc:image:3')->setDescription('Processes product images');
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
		$this->output()->writeln(df_product_c()->count());
	}

	/**
	 * 2020-10-11
	 * @used-by configure()
	 * @var string
	 */
	private static $P_CATEGORY = 'category';
}