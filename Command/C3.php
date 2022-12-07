<?php
namespace TFC\Image\Command;
use Magento\Catalog\Model\Product as P;
use Magento\Catalog\Model\ResourceModel\Product\Collection as PC;
use Magento\Framework\DataObject as _DO;
use TFC\Image\I;
# 2020-11-22 "Images with a grey text are incorrectly cropped": https://github.com/tradefurniturecompany/image/issues/2
final class C3 extends \Df\Framework\Console\Command {
	/**
	 * 2020-11-22
	 * @override
	 * @see \Symfony\Component\Console\Command\Command::configure()
	 * @used-by \Symfony\Component\Console\Command\Command::__construct()
	 */
	protected function configure():void {$this->setName('tfc:image:3')->setDescription('Processes product images');}

	/**
	 * 2020-11-22
	 * @override
	 * @see \Df\Framework\Console\Command::p()
	 * @used-by \Df\Framework\Console\Command::execute()
	 */
	protected function p():void {
		$conn = '2020_07_04';
		df_google_init_service_account();
		$base = df_product_images_path(); /** @var string $base */
		$baseL = df_cc_path(dfa(df_conn($conn)->getConfig(), 'path'), df_product_images_path_rel()); /** @var string $baseL */
		foreach ($this->pcL() as $pL) {/** @var P $pL */
			if ($i = $this->findLabeled($pL, $baseL)) {/** @var I $i */
				/** @var P $p */
				if (($p = $this->pc()->getItemById($pL->getId())) && !$this->findLabeled($p, $base)) {
					$this->output()->writeln("Labeled: {$i->basename()}");
				}
			}
		}
	}

	/**
	 * 2020-11-22
	 * @used-by self::p()
	 * @return I|null
	 */
	private function findLabeled(P $p, string $base) {return df_find(function(_DO $io) use($base) {
		$i = new I($base . $io['file']); /** @var I $i */
		return $i->annotationsText()->count() ? $i : null;
	}, $p->getMediaGalleryImages());}

	/**
	 * 2020-11-22
	 * @used-by self::p()
	 */
	private function pc():PC {return dfc($this, function() {return $this->pcPrepare(df_pc());});}

	/**
	 * 2020-11-22
	 * @used-by self::p()
	 */
	private function pcL():PC {return dfc($this, function() {return df_with_conn(self::$CONN_L, function() {return
		$this->pcPrepare(df_pc()->setPageSize(1)->addFieldToFilter('entity_id', 116))
	;});});}

	/**
	 * 2020-11-22 https://magento.stackexchange.com/a/228181
	 * @used-by self::pc()
	 * @used-by self::pcL()
	 */
	private function pcPrepare(PC $c):PC {return $c->addMediaGalleryData();}

	/**
	 * 2020-11-22
	 * @var string
	 */
	private static $CONN_L = '2020_07_04';
}