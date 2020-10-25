<?php
namespace TFC\Image;
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
		$this->output()->writeln(df_dump(scandir(df_product_images_path())));
	}
}